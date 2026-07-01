<?php

namespace App\Http\Controllers;

use App\Models\Article;
use App\Models\ArticleRevision;
use App\Models\Category;
use App\Models\Tag;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class ArticleController extends Controller
{
    // ─── Admin: list ──────────────────────────────────────────────────────────
    public function index(Request $request)
    {
        $articles = Article::with(['category:id,name', 'user:id,name', 'tags:id,name'])
            ->when($request->q, fn ($q, $s) => $q->where('title', 'like', "%{$s}%"))
            ->latest()->paginate(10);

        $pendingCount       = Article::where('status', 'pending')->count();
        $pendingDeleteCount = Article::where('status', 'pending_delete')->count();

        return view('pages.article_index', compact('articles', 'pendingCount', 'pendingDeleteCount'));
    }

    // ─── Admin: pending ────────────────────────────────────────────────────────
    public function pendingIndex()
    {
        $pending       = Article::with(['category:id,name', 'user:id,name'])->where('status', 'pending')->latest()->paginate(10);
        $pendingDelete = Article::with(['category:id,name', 'user:id,name'])->where('status', 'pending_delete')->latest()->paginate(10);
        return view('pages.article_pending', compact('pending', 'pendingDelete'));
    }

    // ─── Admin actions ─────────────────────────────────────────────────────────
    public function approve(Article $article)
    {
        $article->update(['status' => 'active']);
        $this->logActivity('approve', "Approved article: {$article->title}", $article);
        return back()->with('success', "Artikel \"{$article->title}\" dipublikasikan.");
    }

    public function reject(Article $article)
    {
        $article->update(['status' => 'draft']);
        $this->logActivity('reject', "Rejected article: {$article->title}", $article);
        return back()->with('success', "Artikel \"{$article->title}\" dikembalikan ke draft.");
    }

    public function approveDelete(Article $article)
    {
        $title = $article->title;
        $article->delete();
        $this->logActivity('delete', "Approved delete: {$title}");
        return back()->with('success', "Artikel \"{$title}\" dihapus.");
    }

    public function rejectDelete(Article $article)
    {
        $article->update(['status' => 'active']);
        $this->logActivity('reject_delete', "Rejected delete: {$article->title}", $article);
        return back()->with('success', "Permintaan hapus \"{$article->title}\" ditolak.");
    }

    // ─── Create ────────────────────────────────────────────────────────────────
    public function create()
    {
        $categories = Category::all();
        $allTags    = Tag::orderBy('name')->get();
        $isAdmin    = auth()->user()->role === 'admin';
        return view('pages.edit_article', [
            'article'    => new Article(),
            'categories' => $categories,
            'allTags'    => $allTags,
            'mode'       => 'create',
            'isAdmin'    => $isAdmin,
        ]);
    }

    public function store(Request $request)
    {
        $isAdmin = auth()->user()->role === 'admin';
        $rules   = [
            'title'       => 'required|string|max:255',
            'category_id' => 'required|exists:categories,id',
            'content'     => 'required|string',
            'image'       => 'nullable|image|max:10240',
            'tags'             => 'nullable|string',
            'meta_title'       => 'nullable|string|max:255',
            'meta_description' => 'nullable|string|max:300',
            'meta_keywords'    => 'nullable|string|max:300',
        ];
        if ($isAdmin) {
            $rules['writer']     = 'required|string';
            $rules['status']     = 'required|in:active,draft,archived';
            $rules['created_at'] = 'required|date';
        }

        $data = $request->validate($rules);

        $data['slug']    = $this->uniqueSlug($data['title']);
        $data['user_id'] = auth()->id();
        $data['writer']  = $isAdmin ? $request->writer : auth()->user()->name;
        $data['views']   = 0;
        $data['status']  = $isAdmin
            ? ($request->status ?? 'draft')
            : ($request->has('submit') ? 'pending' : 'draft');

        if (!$isAdmin) $data['created_at'] = now();

        if ($request->hasFile('image')) {
            $data['image'] = $request->file('image')->store('articles', 'public');
        }

        $article = Article::create($data);
        $this->syncTags($article, $request->input('tags', ''));
        // Save initial revision
        ArticleRevision::create([
            'article_id'    => $article->id,
            'user_id'       => auth()->id(),
            'title'         => $article->title,
            'content'       => $article->content,
            'status'        => $article->status,
            'revision_note' => 'Versi awal',
        ]);
        $this->logActivity('create', "Created article: {$article->title}", $article);
        \Illuminate\Support\Facades\Cache::forget('homepage_articles');
        \Illuminate\Support\Facades\Cache::forget('admin_stats');

        if ($isAdmin) return redirect()->route('admin.articles.index')->with('success', 'Artikel ditambahkan.');

        $msg = $data['status'] === 'pending'
            ? 'Artikel disubmit dan menunggu persetujuan admin.'
            : 'Draft disimpan.';
        return redirect()->route('articles.my')->with('success', $msg);
    }

    // ─── Edit ─────────────────────────────────────────────────────────────────
    public function edit(Article $article)
    {
        $user    = auth()->user();
        $isAdmin = $user->role === 'admin';

        if (!$isAdmin && $article->user_id !== $user->id) abort(403);
        if (!$isAdmin && in_array($article->status, ['active', 'pending_delete'])) {
            return back()->with('error', 'Artikel aktif tidak dapat diedit.');
        }

        $article->load('tags:id,name');
        $categories = Category::all();
        $allTags    = Tag::orderBy('name')->get();

        return view('pages.edit_article', compact('article', 'categories', 'allTags', 'isAdmin') + ['mode' => 'edit']);
    }

    public function update(Request $request, Article $article)
    {
        $user    = auth()->user();
        $isAdmin = $user->role === 'admin';

        if (!$isAdmin && $article->user_id !== $user->id) abort(403);

        $rules = [
            'title'       => 'required|string|max:255',
            'category_id' => 'required|exists:categories,id',
            'content'     => 'required|string',
            'image'       => 'nullable|image|max:10240',
            'tags'        => 'nullable|string',
        ];
        if ($isAdmin) {
            $rules['writer']     = 'required|string';
            $rules['status']     = 'required|in:active,draft,pending,pending_delete';
            $rules['created_at'] = 'required|date';
        }

        $data = $request->validate($rules);

        if (!$isAdmin) {
            $data['status'] = $request->has('submit') ? 'pending' : 'draft';
            unset($data['created_at']);
        }

        if ($data['title'] !== $article->title) {
            $data['slug'] = $this->uniqueSlug($data['title'], $article->id);
        }

        if ($request->hasFile('image')) {
            $data['image'] = $request->file('image')->store('articles', 'public');
        }

        $article->update($data);
        $this->syncTags($article, $request->input('tags', ''));
        // Save revision on update
        ArticleRevision::create([
            'article_id'    => $article->id,
            'user_id'       => auth()->id(),
            'title'         => $article->title,
            'content'       => $article->content,
            'status'        => $article->status,
            'revision_note' => $request->input('revision_note', 'Pembaruan'),
        ]);
        $this->logActivity('update', "Updated article: {$article->title}", $article);
        \Illuminate\Support\Facades\Cache::forget('homepage_articles');
        \Illuminate\Support\Facades\Cache::forget('admin_stats');

        if ($isAdmin) return redirect()->route('admin.articles.index')->with('success', 'Artikel diperbarui.');

        $msg = ($data['status'] ?? '') === 'pending'
            ? 'Artikel disubmit ke admin.'
            : 'Draft disimpan.';
        return redirect()->route('articles.my')->with('success', $msg);
    }

    // ─── Destroy ──────────────────────────────────────────────────────────────
    public function destroy(Article $article)
    {
        $this->logActivity('delete', "Deleted article: {$article->title}", $article);
        $article->delete();
        \Illuminate\Support\Facades\Cache::forget('homepage_articles');
        \Illuminate\Support\Facades\Cache::forget('admin_stats');
        return redirect()->route('admin.articles.index')->with('success', 'Artikel dihapus.');
    }

    public function requestDelete(Article $article)
    {
        if ($article->user_id !== auth()->id()) abort(403);
        $article->update(['status' => 'pending_delete']);
        $this->logActivity('request_delete', "Requested delete: {$article->title}", $article);
        return redirect()->route('articles.my')->with('success', 'Permintaan hapus dikirim ke admin.');
    }

    // ─── User: my articles ────────────────────────────────────────────────────
    public function myArticles()
    {
        $articles = Article::with(['category:id,name', 'tags:id,name,slug'])
            ->where('user_id', auth()->id())
            ->latest()
            ->paginate(10);
        return view('pages.my_articles', compact('articles'));
    }

    // ─── Bulk ────────────────────────────────────────────────────────────────
    public function bulkAction(Request $request)
    {
        $request->validate([
            'ids'    => 'required|array',
            'ids.*'  => 'exists:articles,id',
            'action' => 'required|in:publish,draft,delete',
        ]);

        $q = Article::whereIn('id', $request->ids);

        match ($request->action) {
            'publish' => $q->update(['status' => 'active']),
            'draft'   => $q->update(['status' => 'draft']),
            'delete'  => Article::whereIn('id', $request->ids)->delete(),
        };

        return redirect()->route('admin.articles.index')->with('success', 'Bulk action selesai.');
    }

    // ─── Preview ────────────────────────────────────────────────────────────────
    public function preview(Article $article)
    {
        $user = auth()->user();
        if ($user->role !== 'admin' && $article->user_id !== $user->id) abort(403);
        $article->load(['category:id,name', 'tags:id,name,slug', 'user:id,name,role']);
        return view('pages.article_preview', compact('article'));
    }

    // ─── Revisions ───────────────────────────────────────────────────────────────
    public function revisions(Article $article)
    {
        $user = auth()->user();
        if ($user->role !== 'admin' && $article->user_id !== $user->id) abort(403);
        $revisions = $article->revisions()->with('user:id,name')->get();
        return view('pages.article_revisions', compact('article', 'revisions'));
    }

    // ─── Private helpers ──────────────────────────────────────────────────────
    private function uniqueSlug(string $title, ?int $excludeId = null): string
    {
        $slug = Str::slug($title);
        $orig = $slug;
        $i    = 1;
        while (Article::where('slug', $slug)->when($excludeId, fn ($q) => $q->where('id', '!=', $excludeId))->exists()) {
            $slug = $orig . '-' . $i++;
        }
        return $slug;
    }

    private function syncTags(Article $article, string $tagsInput): void
    {
        if (empty(trim($tagsInput))) {
            $article->tags()->detach();
            return;
        }

        $names  = array_unique(array_filter(array_map('trim', explode(',', $tagsInput))));
        $tagIds = [];

        foreach ($names as $name) {
            $slug  = Str::slug($name);
            if (!$slug) continue;
            $tag   = Tag::firstOrCreate(['slug' => $slug], ['name' => $name]);
            $tagIds[] = $tag->id;
        }

        $article->tags()->sync($tagIds);
    }
}
