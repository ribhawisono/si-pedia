<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreArticleRequest;
use App\Http\Requests\UpdateArticleRequest;
use App\Models\Article;
use App\Models\Category;
use App\Models\Tag;
use App\Services\ArticleService;
use Illuminate\Http\Request;

class ArticleController extends Controller
{
    public function __construct(private ArticleService $articleService) {}

    // ─── Admin: list ───────────────────────────────────────────────
    public function index(Request $request)
    {
        // Drafts are a user's private in-progress work and must stay hidden from
        // admin until submitted (status changes to 'pending'). Admins only see
        // their own drafts (self-authored articles they manage directly).
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

    public function approve(Article $article)
    {
        $article->update(['status' => 'active']);
        $this->logActivity('approve', "Approved: {$article->title}", $article);
        $this->articleService->clearCache();
        return back()->with('success', "\"{$article->title}\" dipublikasikan.");
    }

    public function reject(Article $article)
    {
        $article->update(['status' => 'draft']);
        $this->logActivity('reject', "Rejected: {$article->title}", $article);
        return back()->with('success', "\"{$article->title}\" dikembalikan ke draft.");
    }

    public function approveDelete(Article $article)
    {
        $title = $article->title;
        $article->delete();
        $this->logActivity('delete', "Deleted: {$title}");
        $this->articleService->clearCache();
        return back()->with('success', "\"{$title}\" dihapus.");
    }

    public function rejectDelete(Article $article)
    {
        $article->update(['status' => 'active']);
        $this->logActivity('reject_delete', "Rejected delete: {$article->title}", $article);
        return back()->with('success', "Permintaan hapus \"{$article->title}\" ditolak.");
    }

    // ─── Create ────────────────────────────────────────────
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

    // ─── Edit ────────────────────────────────────────────
    public function edit(Article $article)
    {
        $user    = auth()->user();
        $isAdmin = $user->role === 'admin';

        // Admins may only edit content they authored themselves. For
        // user-submitted articles the admin's role is limited to approve /
        // reject / delete via the index actions, not direct editing.
        if ($article->user_id !== $user->id) abort(403);
        if (!$isAdmin && in_array($article->status, ['active', 'pending_delete'])) {
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

        $article = $this->articleService->update($request, $article, $isAdmin);
        $this->logActivity('update', "Updated: {$article->title}", $article);

        if ($isAdmin) return redirect()->route('admin.articles.index')->with('success', 'Artikel diperbarui.');

        return redirect()->route('articles.my')->with('success',
            $article->status === 'pending' ? 'Artikel disubmit ke admin.' : 'Draft disimpan.'
        );
    }

    public function destroy(Article $article)
    {
        $this->logActivity('delete', "Deleted: {$article->title}", $article);
        $article->delete();
        $this->articleService->clearCache();
        return redirect()->route('admin.articles.index')->with('success', 'Artikel dihapus.');
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
        $articles = Article::with(['category:id,name', 'tags:id,name,slug'])
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
            'delete'  => Article::whereIn('id', $request->ids)->delete(),
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
        $user = auth()->user();
        if ($user->role !== 'admin' && $article->user_id !== $user->id) abort(403);
        $revisions = $article->revisions()->with('user:id,name')->get();
        return view('pages.article_revisions', compact('article', 'revisions'));
    }
}
