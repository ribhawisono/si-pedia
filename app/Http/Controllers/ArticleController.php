<?php

namespace App\Http\Controllers;

use App\Models\Article;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class ArticleController extends Controller
{
    // ─── ADMIN: daftar semua artikel ──────────────────────────────────────
    public function index(Request $request)
    {
        $articles = Article::with(['category', 'user'])
            ->when($request->q, fn ($q, $search) =>
                $q->where('title', 'like', "%{$search}%"))
            ->latest()->paginate(10);

        $pendingCount      = Article::where('status', 'pending')->count();
        $pendingDeleteCount = Article::where('status', 'pending_delete')->count();

        return view('pages.article_index', compact('articles', 'pendingCount', 'pendingDeleteCount'));
    }

    // ─── ADMIN: halaman pending approval ──────────────────────────────────
    public function pendingIndex()
    {
        $pending       = Article::with(['category', 'user'])->where('status', 'pending')->latest()->paginate(10);
        $pendingDelete = Article::with(['category', 'user'])->where('status', 'pending_delete')->latest()->paginate(10);

        return view('pages.article_pending', compact('pending', 'pendingDelete'));
    }

    // ─── ADMIN: approve artikel pending ───────────────────────────────────
    public function approve(Article $article)
    {
        $article->update(['status' => 'active']);
        $this->logActivity('approve', "Approved article: {$article->title}", $article);
        return back()->with('success', "Artikel \"{$article->title}\" berhasil dipublikasikan.");
    }

    // ─── ADMIN: reject artikel pending ────────────────────────────────────
    public function reject(Article $article)
    {
        $article->update(['status' => 'draft']);
        $this->logActivity('reject', "Rejected article: {$article->title}", $article);
        return back()->with('success', "Artikel \"{$article->title}\" dikembalikan ke draft.");
    }

    // ─── ADMIN: approve request delete ────────────────────────────────────
    public function approveDelete(Article $article)
    {
        $title = $article->title;
        $article->delete();
        $this->logActivity('delete', "Approved delete request for article: {$title}");
        return back()->with('success', "Artikel \"{$title}\" berhasil dihapus.");
    }

    // ─── ADMIN: reject request delete (kembalikan ke status sebelumnya) ───
    public function rejectDelete(Article $article)
    {
        $article->update(['status' => 'active']);
        $this->logActivity('reject_delete', "Rejected delete request for article: {$article->title}", $article);
        return back()->with('success', "Permintaan hapus artikel \"{$article->title}\" ditolak.");
    }

    // ─── SEMUA USER: form buat artikel ────────────────────────────────────
    public function create()
    {
        $categories = Category::all();
        $isAdmin    = auth()->user()->role === 'admin';

        return view('pages.edit_article', [
            'article'    => new Article(),
            'categories' => $categories,
            'mode'       => 'create',
            'isAdmin'    => $isAdmin,
        ]);
    }

    // ─── SEMUA USER: simpan artikel baru ──────────────────────────────────
    public function store(Request $request)
    {
        $isAdmin = auth()->user()->role === 'admin';

        $rules = [
            'title'       => 'required|string|max:255',
            'category_id' => 'required|exists:categories,id',
            'content'     => 'required|string',
            'image'       => 'nullable|image|max:10240',
        ];

        // Admin bisa set status & tanggal; non-admin tidak
        if ($isAdmin) {
            $rules['writer']       = 'required|string';
            $rules['status']       = 'required|in:active,draft';
            $rules['created_at']   = 'required|date';
            $rules['scheduled_at'] = 'nullable|date';
        }

        $data = $request->validate($rules);

        $data['slug']    = $this->generateUniqueSlug($data['title']);
        $data['user_id'] = auth()->id();
        $data['writer']  = $isAdmin ? ($request->writer ?? auth()->user()->name) : auth()->user()->name;
        $data['views']   = 0;

        if ($isAdmin) {
            $data['status'] = $request->status ?? 'draft';
        } else {
            // Non-admin: submit → pending, save draft → draft
            $data['status']     = $request->has('submit') ? 'pending' : 'draft';
            $data['created_at'] = now();
        }

        if ($request->hasFile('image')) {
            $data['image'] = $request->file('image')->store('articles', 'public');
        }

        $article = Article::create($data);
        $this->logActivity('create', "Created article: {$article->title}", $article);

        if ($isAdmin) {
            return redirect()->route('admin.articles.index')->with('success', 'Artikel berhasil ditambahkan.');
        }

        $msg = $data['status'] === 'pending'
            ? 'Artikel berhasil disubmit dan menunggu persetujuan admin.'
            : 'Artikel berhasil disimpan sebagai draft.';

        return redirect()->route('articles.my')->with('success', $msg);
    }

    // ─── SEMUA USER: form edit artikel (hanya milik sendiri atau admin) ───
    public function edit(Article $article)
    {
        $user    = auth()->user();
        $isAdmin = $user->role === 'admin';

        if (!$isAdmin && $article->user_id !== $user->id) {
            abort(403, 'Anda tidak punya akses untuk mengedit artikel ini.');
        }

        // Non-admin tidak bisa edit artikel yang sudah active/pending_delete
        if (!$isAdmin && in_array($article->status, ['active', 'pending_delete'])) {
            return back()->with('error', 'Artikel yang sudah aktif atau dalam antrian hapus tidak dapat diedit.');
        }

        $categories = Category::all();
        return view('pages.edit_article', compact('article', 'categories', 'isAdmin'))->with('mode', 'edit');
    }

    // ─── SEMUA USER: update artikel ───────────────────────────────────────
    public function update(Request $request, Article $article)
    {
        $user    = auth()->user();
        $isAdmin = $user->role === 'admin';

        if (!$isAdmin && $article->user_id !== $user->id) {
            abort(403);
        }

        $rules = [
            'title'       => 'required|string|max:255',
            'category_id' => 'required|exists:categories,id',
            'content'     => 'required|string',
            'image'       => 'nullable|image|max:10240',
        ];

        if ($isAdmin) {
            $rules['writer']       = 'required|string';
            $rules['status']       = 'required|in:active,draft,pending,pending_delete';
            $rules['created_at']   = 'required|date';
            $rules['scheduled_at'] = 'nullable|date';
        }

        $data = $request->validate($rules);

        if (!$isAdmin) {
            $data['status'] = $request->has('submit') ? 'pending' : 'draft';
            unset($data['created_at'], $data['scheduled_at']);
        }

        if ($data['title'] !== $article->title) {
            $data['slug'] = $this->generateUniqueSlug($data['title'], $article->id);
        }

        if ($request->hasFile('image')) {
            $data['image'] = $request->file('image')->store('articles', 'public');
        }

        $article->update($data);
        $this->logActivity('update', "Updated article: {$article->title}", $article);

        if ($isAdmin) {
            return redirect()->route('admin.articles.index')->with('success', 'Artikel berhasil diupdate.');
        }

        $msg = ($data['status'] ?? '') === 'pending'
            ? 'Artikel berhasil disubmit dan menunggu persetujuan admin.'
            : 'Artikel berhasil disimpan sebagai draft.';

        return redirect()->route('articles.my')->with('success', $msg);
    }

    // ─── ADMIN ONLY: hapus artikel ────────────────────────────────────────
    public function destroy(Article $article)
    {
        $this->logActivity('delete', "Deleted article: {$article->title}", $article);
        $article->delete();
        return redirect()->route('admin.articles.index')->with('success', 'Artikel berhasil dihapus.');
    }

    // ─── NON-ADMIN: request hapus artikel ─────────────────────────────────
    public function requestDelete(Article $article)
    {
        $user = auth()->user();

        if ($article->user_id !== $user->id) {
            abort(403, 'Anda tidak bisa meminta hapus artikel milik orang lain.');
        }

        $article->update(['status' => 'pending_delete']);
        $this->logActivity('request_delete', "Requested delete for article: {$article->title}", $article);

        return redirect()->route('articles.my')->with('success', 'Permintaan hapus artikel telah dikirim ke admin.');
    }

    // ─── NON-ADMIN: daftar artikel milik sendiri ──────────────────────────
    public function myArticles()
    {
        $articles = Article::with('category')
            ->where('user_id', auth()->id())
            ->latest()->paginate(10);

        return view('pages.my_articles', compact('articles'));
    }

    // ─── ADMIN: bulk action ───────────────────────────────────────────────
    public function bulkAction(Request $request)
    {
        $request->validate([
            'ids'    => 'required|array',
            'ids.*'  => 'exists:articles,id',
            'action' => 'required|in:publish,draft,delete',
        ]);

        $articles = Article::whereIn('id', $request->ids);

        switch ($request->action) {
            case 'publish':
                $articles->update(['status' => 'active']);
                $desc = 'Bulk published ' . count($request->ids) . ' articles';
                break;
            case 'draft':
                $articles->update(['status' => 'draft']);
                $desc = 'Bulk drafted ' . count($request->ids) . ' articles';
                break;
            case 'delete':
                Article::whereIn('id', $request->ids)->delete();
                $desc = 'Bulk deleted ' . count($request->ids) . ' articles';
                break;
        }

        $this->logActivity('bulk_action', $desc ?? 'Bulk action performed');
        return redirect()->route('admin.articles.index')->with('success', 'Bulk action selesai.');
    }

    private function generateUniqueSlug(string $title, ?int $excludeId = null): string
    {
        $slug         = Str::slug($title);
        $originalSlug = $slug;
        $counter      = 1;

        while (Article::where('slug', $slug)
            ->when($excludeId, fn ($q, $id) => $q->where('id', '!=', $id))
            ->exists()
        ) {
            $slug = $originalSlug . '-' . $counter++;
        }

        return $slug;
    }
}
