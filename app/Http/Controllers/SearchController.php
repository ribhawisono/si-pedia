<?php

namespace App\Http\Controllers;

use App\Models\Article;
use App\Models\Category;
use App\Models\Lecturer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class SearchController extends Controller
{
    public function index(Request $request)
    {
        $q = trim($request->get('q', ''));

        if (mb_strlen($q) < 2) {
            return view('pages.search', [
                'q'            => $q,
                'articles'     => collect(),
                'lecturers'    => collect(),
                'categories'   => collect(),
                'totalResults' => 0,
            ]);
        }

        $like = "%{$q}%";

        $articles = Article::with(['category:id,name', 'user:id,name'])
            ->where('status', 'active')
            ->where(fn ($qb) => $qb
                ->where('title', 'like', $like)
                ->orWhere('content', 'like', $like)
                ->orWhere('writer', 'like', $like)
                ->orWhereHas('category', fn ($c) => $c->where('name', 'like', $like))
            )
            ->orderByDesc('views')
            ->limit(12)
            ->get();

        $lecturers = Lecturer::with('user:id,name,email')
            ->where(fn ($qb) => $qb
                ->where('nidn', 'like', $like)
                ->orWhere('address', 'like', $like)
                ->orWhereHas('user', fn ($u) => $u->where('name', 'like', $like))
            )
            ->limit(6)
            ->get();

        $categories = Category::withCount(['articles' => fn ($q) => $q->where('status', 'active')])
            ->where('name', 'like', $like)
            ->limit(6)
            ->get();

        $totalResults = $articles->count() + $lecturers->count() + $categories->count();

        return view('pages.search', compact('q', 'articles', 'lecturers', 'categories', 'totalResults'));
    }

    /**
     * JSON endpoint for live search suggestions (debounced from navbar)
     */
    public function suggest(Request $request)
    {
        $q = trim($request->get('q', ''));

        if (mb_strlen($q) < 2) {
            return response()->json(['results' => []]);
        }

        $cacheKey = 'search_suggest_' . md5($q);

        $results = Cache::remember($cacheKey, 30, function () use ($q) {
            $like = "%{$q}%";

            $articles = Article::select('id', 'title', 'slug')
                ->where('status', 'active')
                ->where('title', 'like', $like)
                ->orderByDesc('views')
                ->limit(5)
                ->get()
                ->map(fn ($a) => [
                    'type'  => 'article',
                    'label' => $a->title,
                    'url'   => route('articles.show', $a->slug),
                    'icon'  => '📄',
                ]);

            $lecturers = Lecturer::with('user:id,name')
                ->whereHas('user', fn ($u) => $u->where('name', 'like', $like))
                ->limit(3)
                ->get()
                ->map(fn ($l) => [
                    'type'  => 'lecturer',
                    'label' => $l->user->name ?? '-',
                    'url'   => route('search', ['q' => $l->user->name ?? '']),
                    'icon'  => '👤',
                ]);

            $categories = Category::select('id', 'name')
                ->where('name', 'like', $like)
                ->limit(3)
                ->get()
                ->map(fn ($c) => [
                    'type'  => 'category',
                    'label' => $c->name,
                    'url'   => route('catalog', ['category' => $c->id]),
                    'icon'  => '📂',
                ]);

            return $articles->concat($lecturers)->concat($categories)->values();
        });

        return response()->json(['results' => $results, 'query' => $q]);
    }
}
