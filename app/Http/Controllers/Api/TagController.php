<?php
namespace App\Http\Controllers\Api;
use App\Http\Controllers\Controller;
use App\Http\Resources\{ArticleResource, TagResource};
use App\Models\Tag;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class TagController extends Controller
{
    public function index()
    {
        $tags = Cache::remember('api_tags', 300, fn () =>
            Tag::withCount('articles')->orderByDesc('articles_count')->get()
        );
        return TagResource::collection($tags);
    }

    public function articles(Request $request, Tag $tag)
    {
        $articles = $tag->articles()
            ->with(['category:id,name', 'tags:id,name,slug'])
            ->where('status', 'active')
            ->latest()
            ->paginate($request->integer('per_page', 15, 1, 50));

        return ArticleResource::collection($articles);
    }
}
