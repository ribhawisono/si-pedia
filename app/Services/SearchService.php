<?php

namespace App\Services;

use App\Models\Article;
use App\Models\Category;
use App\Models\Lecturer;
use App\Models\Tag;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;

class SearchService
{
    /** Full search — returns grouped results */
    public function search(string $q, int $articleLimit = 12): array
    {
        if (mb_strlen(trim($q)) < 2) {
            return $this->emptyResult($q);
        }

        $like = "%" . trim($q) . "%";

        $articles = Article::with(['category:id,name', 'user:id,name', 'tags:id,name,slug'])
            ->where('status', 'active')
            ->where(fn ($qb) => $qb
                ->where('title', 'like', $like)
                ->orWhere('content', 'like', $like)
                ->orWhere('writer', 'like', $like)
                ->orWhereHas('category', fn ($c) => $c->where('name', 'like', $like))
            )
            ->orderByDesc('views')
            ->limit($articleLimit)
            ->get();

        $lecturers = Lecturer::with('user:id,name,email')
            ->where('status', 'active')
            ->where(fn ($qb) => $qb
                ->where('nidn', 'like', $like)
                ->orWhereHas('user', fn ($u) => $u->where('name', 'like', $like))
            )
            ->limit(6)
            ->get();

        $categories = Category::withCount(['articles' => fn ($q) => $q->where('status', 'active')])
            ->where('name', 'like', $like)
            ->limit(6)
            ->get();

        $tags = Tag::withCount(['articles' => fn ($q) => $q->where('status', 'active')])
            ->where('name', 'like', $like)
            ->limit(10)
            ->get();

        return [
            'q'            => trim($q),
            'articles'     => $articles,
            'lecturers'    => $lecturers,
            'categories'   => $categories,
            'tags'         => $tags,
            'totalResults' => $articles->count() + $lecturers->count() + $categories->count() + $tags->count(),
        ];
    }

    /** Fast suggestions for navbar live search */
    public function suggest(string $q): Collection
    {
        if (mb_strlen(trim($q)) < 2) return collect();

        $cacheKey = 'search_suggest_' . md5($q);

        return Cache::remember($cacheKey, 30, function () use ($q) {
            $like = "%" . trim($q) . "%";

            $articles = Article::select('id', 'title', 'slug')
                ->where('status', 'active')
                ->where('title', 'like', $like)
                ->orderByDesc('views')
                ->limit(5)
                ->get()
                ->map(fn ($a) => ['type' => 'article', 'label' => $a->title, 'url' => route('articles.show', $a->slug), 'icon' => '📄']);

            $lecturers = Lecturer::with('user:id,name')
                ->whereHas('user', fn ($u) => $u->where('name', 'like', $like))
                ->limit(3)
                ->get()
                ->map(fn ($l) => ['type' => 'lecturer', 'label' => $l->user->name ?? '-', 'url' => route('search', ['q' => $l->user->name ?? '']), 'icon' => '👤']);

            $categories = Category::select('id', 'name')
                ->where('name', 'like', $like)
                ->limit(3)
                ->get()
                ->map(fn ($c) => ['type' => 'category', 'label' => $c->name, 'url' => route('catalog', ['category' => $c->id]), 'icon' => '📂']);

            return $articles->concat($lecturers)->concat($categories)->values();
        });
    }

    private function emptyResult(string $q): array
    {
        return ['q' => $q, 'articles' => collect(), 'lecturers' => collect(), 'categories' => collect(), 'tags' => collect(), 'totalResults' => 0];
    }
}
