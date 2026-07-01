<?php

namespace App\Services;

use App\Models\Article;
use App\Models\ArticleRevision;
use App\Models\Tag;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;

class ArticleService
{
    /** Build and save an article from request data */
    public function store(Request $request, bool $isAdmin): Article
    {
        $data = $this->prepareData($request, $isAdmin);
        $data['slug']    = $this->uniqueSlug($data['title']);
        $data['user_id'] = auth()->id();
        $data['views']   = 0;
        $data['status']  = $this->resolveStatus($request, $isAdmin);
        if (!$isAdmin) $data['created_at'] = now();

        if ($request->hasFile('image')) {
            $data['image'] = $request->file('image')->store('articles', 'public');
        }

        $article = Article::create($data);
        $this->syncTags($article, $request->input('tags', ''));
        $this->saveRevision($article, 'Versi awal');
        $this->clearCache();

        return $article;
    }

    /** Update an existing article */
    public function update(Request $request, Article $article, bool $isAdmin): Article
    {
        $data = $this->prepareData($request, $isAdmin);

        if ($data['title'] !== $article->title) {
            $data['slug'] = $this->uniqueSlug($data['title'], $article->id);
        }

        if (!$isAdmin) {
            $data['status'] = $request->has('submit') ? 'pending' : 'draft';
            unset($data['created_at']);
        }

        if ($request->hasFile('image')) {
            $data['image'] = $request->file('image')->store('articles', 'public');
        }

        $article->update($data);
        $this->syncTags($article, $request->input('tags', ''));
        $this->saveRevision($article, $request->input('revision_note', 'Pembaruan'));
        $this->clearCache();

        return $article;
    }

    /** Sync comma-separated tags */
    public function syncTags(Article $article, string $tagsInput): void
    {
        if (empty(trim($tagsInput))) {
            $article->tags()->detach();
            return;
        }

        $tagIds = collect(explode(',', $tagsInput))
            ->map(fn ($n) => trim($n))
            ->filter()
            ->unique()
            ->map(function ($name) {
                $slug = Str::slug($name);
                return $slug ? Tag::firstOrCreate(['slug' => $slug], ['name' => $name])->id : null;
            })
            ->filter()
            ->toArray();

        $article->tags()->sync($tagIds);
    }

    /** Save a revision snapshot */
    public function saveRevision(Article $article, string $note = 'Pembaruan'): void
    {
        ArticleRevision::create([
            'article_id'    => $article->id,
            'user_id'       => auth()->id(),
            'title'         => $article->title,
            'content'       => $article->content,
            'status'        => $article->status,
            'revision_note' => $note,
        ]);

        // Keep only last 20 revisions per article
        $count = ArticleRevision::where('article_id', $article->id)->count();
        if ($count > 20) {
            ArticleRevision::where('article_id', $article->id)
                ->oldest()
                ->limit($count - 20)
                ->delete();
        }
    }

    /** Clear article-related caches */
    public function clearCache(): void
    {
        Cache::forget('homepage_articles');
        Cache::forget('admin_stats');
        Cache::forget('admin_top_articles');
        Cache::forget('api_categories');
        Cache::forget('api_tags');
    }

    /** Generate unique slug */
    public function uniqueSlug(string $title, ?int $excludeId = null): string
    {
        $slug = Str::slug($title);
        $orig = $slug;
        $i    = 1;

        while (Article::where('slug', $slug)
            ->when($excludeId, fn ($q) => $q->where('id', '!=', $excludeId))
            ->exists()
        ) {
            $slug = $orig . '-' . $i++;
        }

        return $slug;
    }

    private function prepareData(Request $request, bool $isAdmin): array
    {
        $rules = [
            'title'            => 'required|string|max:255',
            'category_id'      => 'required|exists:categories,id',
            'content'          => 'required|string',
            'image'            => 'nullable|image|max:10240|mimes:jpg,jpeg,png,webp',
            'tags'             => 'nullable|string|max:500',
            'meta_title'       => 'nullable|string|max:255',
            'meta_description' => 'nullable|string|max:300',
            'meta_keywords'    => 'nullable|string|max:300',
            'canonical_url'    => 'nullable|url',
        ];

        if ($isAdmin) {
            $rules['writer']     = 'required|string|max:255';
            $rules['status']     = 'required|in:active,draft,archived';
            $rules['created_at'] = 'required|date';
        }

        $data = $request->validate($rules);
        $data['writer'] = $isAdmin ? ($request->writer ?? auth()->user()->name) : auth()->user()->name;

        return $data;
    }

    private function resolveStatus(Request $request, bool $isAdmin): string
    {
        if ($isAdmin) return $request->status ?? 'draft';
        return $request->has('submit') ? 'pending' : 'draft';
    }
}
