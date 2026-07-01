<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\ArticleResource;
use App\Models\Article;
use Illuminate\Http\Request;

class ArticleController extends Controller
{
    /** GET /api/v1/articles */
    public function index(Request $request)
    {
        $articles = Article::with(['category:id,name', 'user:id,name,role', 'tags:id,name,slug'])
            ->withCount(['comments', 'bookmarks'])
            ->where('status', 'active')
            ->when($request->q, fn ($q, $s) => $q->where(fn ($qb) => $qb
                ->where('title', 'like', "%{$s}%")
                ->orWhere('content', 'like', "%{$s}%")
            ))
            ->when($request->category, fn ($q, $c) => $q->where('category_id', $c))
            ->when($request->tag, fn ($q, $t) => $q->whereHas('tags', fn ($tq) => $tq->where('slug', $t)))
            ->when($request->sort, fn ($q, $s) => match ($s) {
                'views'    => $q->orderByDesc('views'),
                'oldest'   => $q->oldest(),
                'alpha'    => $q->orderBy('title'),
                default    => $q->latest(),
            }, fn ($q) => $q->latest())
            ->paginate($request->integer('per_page', 15, 1, 50))
            ->withQueryString();

        return ArticleResource::collection($articles);
    }

    /** GET /api/v1/articles/{slug} */
    public function show(Article $article)
    {
        if ($article->status !== 'active') {
            return response()->json(['message' => 'Artikel tidak ditemukan.'], 404);
        }

        $article->load(['category:id,name', 'user:id,name,role,avatar', 'tags:id,name,slug']);
        $article->loadCount(['comments', 'bookmarks']);

        $related = Article::with(['category:id,name'])
            ->where('status', 'active')
            ->where('category_id', $article->category_id)
            ->where('id', '!=', $article->id)
            ->orderByDesc('views')
            ->limit(3)
            ->get();

        return response()->json([
            'data'    => new ArticleResource($article),
            'related' => ArticleResource::collection($related),
        ]);
    }
}
