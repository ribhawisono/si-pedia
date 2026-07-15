<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreArticleRequest;
use App\Http\Requests\UpdateArticleRequest;
use App\Models\Article;
use App\Models\Category;
use App\Models\Tag;
use App\Services\ArticleService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ArticleController extends Controller
{
    public function __construct(private ArticleService $articleService) {}

    // ─── Admin: list ─────────────────────
    public function index(Request $request)
    {
        $adminId  = auth()->id();
        $articles = Article::with(['category:id,name', 'user:id,name', 'tags:id,name'])
            ->where(fn ($q) => $q->where('status', '!=', 'draft')->orWhere('user_id', $adminId))
            ->when($request->q, fn ($q, $s) => $q->where('title', 'like', "%{$s}%"))
            ->latest()->paginate(10);

        $pendingCount       = Article::where('status', 'pending')->count();
        $pendingDeleteCount = Article::where('status', 'pending_delete')->count();

        return view('pages.article_index', compact('articles', 'pendingCount', 'pendingDeleteCount'));
    }

    public function pendingIndex()
    {
        $pending       = Article::with(['category:id,name', 'user:id,name'])->where('status', 'pending')->latest()->paginate(10);
        $pendingDelete = Article::with(['category:id,name', 'user:id,name'])->where('status', 'pending_delete')->latest()->paginate(10);
        return view('pages.article_pending', compact('pending', 'pendingDelete'));
    }

    public function trash(Request $request)
    {
        $articles = Article::onlyTrashed()
            ->with(['category:id,name', 'user:id,name'])
            ->when($request->q, fn ($q, $s) => $q->where('title', 'like', "%{$s}%"))
            ->latest('deleted_at')->paginate(10);

        return view('pages.article_trash', compact('articles'));
    }

    public function restore($id)
    {
        $article = Article::onlyTrashed()->findOrFail($id);
        $article->restore();
        // rejection_note dibersihkan juga: kalau tidak, catatan takedown/reject
        // lama masih nyangkut dan tampil basi ke penulis setelah direstore.
        $article->update(['status' => 'draft', 'trashed_reason' => null, 'rejection_note' => null]);
        $this->logActivity('restore', "Restored: {$article->title}", $article);
        $this->articleService->clearCache();
        return back()->with('success', "\"{$article->title}\" dipulihkan sebagai Draft.");
    }

    public function forceDelete($id)
    {
        $article = Article::onlyTrashed()->findOrFail($id);
        $title = $article->title;
        if ($article->image) Storage::disk('public')->delete($article->image);
        $article->forceDelete();
        $this->logActivity('force_delete', "Permanently deleted: {$title}");
        $this->articleService->clearCache();
        return back()->with('success', "\"{$title}\" dihapus permanen.");
    }

    public function approve(Article $article)
    {
        $article->update(['status' => 'active', 'rejection_note' => null]);
        $this->logActivity('approve', "Approved: {$article->title}", $article);
        $this->articleService->clearCache();
        return back()->with('success', "\"{$article->title}\" dipublikasikan.");
    }

    public function reject(Request $request, Article $article)
    {
        $data = $request->validate([
            'rejection_note' => 'nullable|string|max:1000',
        ]);

        $article->update([
            'status'         => 'draft',
            'rejection_note' => $data['rejection_note'] ?? null,
        ]);
        $this->logActivity('reject', "Rejected: {$article->title}", $article);
        return back()->with('success', "\"{$article->title}\" dikembalikan ke draft. Penulis akan melihat catatan perbaikan yang kamu berikan.");
    }

    // Takedown: pull a LIVE article down (distinct from Edit/Hapus). Soft-
    // deletes it (lands in Trash like any delete) but tags trashed_reason as
    // 'takedown' so it still surfaces in the writer's "Artikel Saya" for
    // editing — unlike a normal Hapus, which stays hidden from the writer.
    public function takedownForm(Article $article)
    {
        return view('pages.admin_takedown_form', compact('article'));
    }

    public function takedown(Request $request, Article $article)
    {
        $data = $request->validate([
            'rejection_note' => 'required|string|max:1000',
        ]);

        $article->update([
            'rejection_note' => $data['rejection_note'],
            'trashed_reason' => 'takedown',
        ]);
        $article->delete();
        $this->logActivity('takedown', "Takedown: {$article->title}", $article);
        $this->articleService->clearCache();

        return redirect()->route('admin.articles.index')->with('success',
            "\"{$article->title}\" ditakedown. Penulis dapat memperbaiki dan mengedit ulang dari \"Artikel Saya\"."
        );
    }

    public function approveDelete(Article $article)
    {
        $title = $article->title;
        $article->update(['trashed_reason' => 'deleted']);
        $article->delete();
        $this->logActivity('delete', "Deleted: {$title}");
        $this->articleService->clearCache();
        return back()->with('success', "\"{$title}\" dipindahkan ke Trash.");
    }

    public function rejectDelete(Article $article)
    {
        $article->update(['status' => 'active']);
        $this->logActivity('reject_delete', "Rejected delete: {$article->title}", $article);
        return back()->with('success', "Permintaan hapus \"{$article->title}\" ditolak.");
    }

    public function create()
    {
        return view('pages.edit_article', [
            'article'    => new Article(),
            'categories' => Category::all(),
            'allTags'    => Tag::orderBy('name')->get(),
            'mode'       => 'create',
            'isAdmin'    => auth()->user()->role === 'admin',
        ]);
    }

    public function store(StoreArticleRequest $request)
    {
        $isAdmin = auth()->user()->role === 'admin';
        $article = $this->articleService->store($request, $isAdmin);
        $this->logActivity('create', "Created: {$article->title}", $article);

        if ($isAdmin) return redirect()->route('admin.articles.index')->with('success', 'Artikel ditambahkan.');

        return redirect()->route('articles.my')->with('success',
            $article->status === 'pending' ? 'Artikel disubmit ke admin.' : 'Draft disimpan.'
        );
    }

    public function edit(Article $article)
    {
        $user    = auth()->user();
        $isAdmin = $user->role === 'admin';

        if ($article->user_id !== $user->id) abort(403);

        // A takedown'd article is soft-deleted but intentionally editable by
        // its writer (see takedown() above). A normally-deleted article
        // (trashed_reason=deleted) must NOT be editable at all.
        if ($article->trashed() && $article->trashed_reason !== 'takedown') abort(403);

        // Fix: sebelumnya cek status 'active' ini tetap menghalangi artikel
        // yang baru di-takedown (statusnya masih 'active', hanya trashed_reason
        // yang berubah), sehingga penulis dapat pesan "Artikel aktif tidak
        // dapat diedit" meski trashed_reason sudah 'takedown'. Skip cek status
        // untuk kasus takedown karena memang sengaja dibuat bisa diedit.
        if (!$isAdmin && $article->trashed_reason !== 'takedown' && in_array($article->status, ['active', 'pending_delete'])) {
            return back()->with('error', 'Artikel aktif tidak dapat diedit.');
        }

        $article->load('tags:id,name');
        return view('pages.edit_article', [
            'article'    => $article,
            'categories' => Category::all(),
            'allTags'    => Tag::orderBy('name')->get(),
            'mode'       => 'edit',
            'isAdmin'    => $isAdmin,
        ]);
    }

    public function update(UpdateArticleRequest $request, Article $article)
    {
        $user    = auth()->user();
        $isAdmin = $user->role === 'admin';
        if ($article->user_id !== $user->id) abort(403);
        if ($article->trashed() && $article->trashed_reason !== 'takedown') abort(403);

        $wasTakendown = $article->trashed_reason === 'takedown';

        $article = $this->articleService->update($request, $article, $isAdmin);

        // Saving a takedown'd article resubmits it: restore from Trash, clear
        // the takedown flag and note, back to normal draft/pending flow.
        if ($wasTakendown) {
            $article->restore();
            $article->update(['trashed_reason' => null]);
        }

        $this->logActivity('update', "Updated: {$article->title}", $article);

        if ($isAdmin) return redirect()->route('admin.articles.index')->with('success', 'Artikel diperbarui.');

        return redirect()->route('articles.my')->with('success',
            $article->status === 'pending' ? 'Artikel disubmit ke admin.' : 'Draft disimpan.'
        );
    }

    public function destroy(Article $article)
    {
        $this->logActivity('delete', "Deleted: {$article->title}", $article);
        $article->update(['trashed_reason' => 'deleted']);
        $article->delete();
        $this->articleService->clearCache();
        return redirect()->route('admin.articles.index')->with('success', 'Artikel dipindahkan ke Trash.');
    }

    public function requestDelete(Article $article)
    {
        if ($article->user_id !== auth()->id()) abort(403);
        $article->update(['status' => 'pending_delete']);
        $this->logActivity('request_delete', "Requested delete: {$article->title}", $article);
        return redirect()->route('articles.my')->with('success', 'Permintaan hapus dikirim ke admin.');
    }

    public function myArticles()
    {
        $articles = Article::withTrashed()
            ->with(['category:id,name', 'tags:id,name,slug'])
            ->where('user_id', auth()->id())
            ->latest()
            ->paginate(10);
        return view('pages.my_articles', compact('articles'));
    }

    public function bulkAction(Request $request)
    {
        $request->validate(['ids' => 'required|array', 'ids.*' => 'exists:articles,id', 'action' => 'required|in:publish,draft,delete']);
        $q = Article::whereIn('id', $request->ids);
        match ($request->action) {
            'publish' => $q->update(['status' => 'active']),
            'draft'   => $q->update(['status' => 'draft']),
            'delete'  => (function () use ($request) {
                Article::whereIn('id', $request->ids)->update(['trashed_reason' => 'deleted']);
                Article::whereIn('id', $request->ids)->delete();
            })(),
        };
        $this->articleService->clearCache();
        return redirect()->route('admin.articles.index')->with('success', 'Bulk action selesai.');
    }

    public function preview(Article $article)
    {
        $user = auth()->user();
        if ($user->role !== 'admin' && $article->user_id !== $user->id) abort(403);
        $article->load(['category:id,name', 'tags:id,name,slug', 'user:id,name,role']);
        return view('pages.article_preview', compact('article'));
    }

    public function revisions(Article $article)
    {
        $user    = auth()->user();
        $isAdmin = $user->role === 'admin';
        if (!$isAdmin && $article->user_id !== $user->id) abort(403);
        $revisions = $article->revisions()->with('user:id,name')->get();
        // isAdmin dikirim ke view supaya bisa pilih layout: admin panel untuk
        // admin, layout publik biasa untuk penulis (user) -> sebelumnya view
        // ini SELALU pakai <x-layouts.admin>, jadi sidebar admin bocor ke user.
        return view('pages.article_revisions', compact('article', 'revisions', 'isAdmin'));
    }
}
