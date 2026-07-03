<?php

namespace App\Services;

use App\Http\Requests\StoreArticleRequest;
use App\Http\Requests\UpdateArticleRequest;
use App\Models\Article;
use App\Models\ArticleRevision;
use App\Models\Tag;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;

class ArticleService
{
    /** Build and save an article from a validated request */
    public function store(StoreArticleRequest $request, bool $isAdmin): Article
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

    /** Update an existing article from a validated request */
    public function update(UpdateArticleRequest $request, Article $article, bool $isAdmin): Article
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
        Cache::forget('categories_all');
        Cache::forget('tags_popular');
        Cache::forget('admin_top_users');
        Cache::forget('admin_monthly_v2_' . now()->year);
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

    private function prepareData(StoreArticleRequest|UpdateArticleRequest $request, bool $isAdmin): array
    {
        $data = $request->validated();
        $data['writer'] = $isAdmin ? ($request->writer ?? auth()->user()->name) : auth()->user()->name;

        return $data;
    }

    private function resolveStatus(StoreArticleRequest $request, bool $isAdmin): string
    {
        if ($isAdmin) return $request->status ?? 'draft';
        return $request->has('submit') ? 'pending' : 'draft';
    }
}
