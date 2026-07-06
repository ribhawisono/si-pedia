<?php
namespace App\Http\Controllers\Api;
use App\Http\Controllers\Controller;
use App\Http\Resources\{ArticleResource, CategoryResource, LecturerResource, TagResource};
use App\Models\{Article, Category, Lecturer, Tag};
use Illuminate\Http\Request;

class SearchController extends Controller
{
    public function __invoke(Request $request)
    {
        $q = trim($request->get('q', ''));
        if (mb_strlen($q) < 2) {
            return response()->json(['message' => 'Query minimal 2 karakter.', 'data' => []], 422);
        }
        $like = "%{$q}%";

        return response()->json([
            'query'      => $q,
            'articles'   => ArticleResource::collection(
                Article::with(['category:id,name', 'tags:id,name,slug'])
                    ->where('status', 'active')
                    ->where(fn ($qb) => $qb->where('title', 'like', $like)->orWhere('content', 'like', $like))
                    ->orderByDesc('views')->limit(10)->get()
            ),
            'lecturers'  => LecturerResource::collection(
                Lecturer::with('user:id,name,email')->where('status', 'active')
                    ->whereHas('user', fn ($u) => $u->where('name', 'like', $like))->limit(5)->get()
            ),
            'categories' => CategoryResource::collection(
                Category::withCount(['articles' => fn ($q) => $q->where('status', 'active')])
                    ->where('name', 'like', $like)->limit(5)->get()
            ),
            'tags'       => TagResource::collection(
                Tag::withCount(['articles' => fn ($q) => $q->where('status', 'active')])->where('name', 'like', $like)->limit(8)->get()
            ),
        ]);
    }
}
