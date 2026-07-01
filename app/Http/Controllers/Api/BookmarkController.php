<?php
namespace App\Http\Controllers\Api;
use App\Http\Controllers\Controller;
use App\Http\Resources\BookmarkResource;
use App\Models\{Article, Bookmark};
use Illuminate\Http\Request;

class BookmarkController extends Controller
{
    public function index(Request $request)
    {
        $bookmarks = $request->user()
            ->bookmarks()
            ->with(['article:id,title,slug,image,status,created_at,category_id'])
            ->latest()
            ->paginate(15);
        return BookmarkResource::collection($bookmarks);
    }

    public function toggle(Request $request, Article $article)
    {
        $existing = Bookmark::where('user_id', $request->user()->id)
            ->where('article_id', $article->id)
            ->first();

        if ($existing) {
            $existing->delete();
            return response()->json(['bookmarked' => false, 'message' => 'Bookmark dihapus.']);
        }

        Bookmark::create(['user_id' => $request->user()->id, 'article_id' => $article->id]);
        return response()->json(['bookmarked' => true, 'message' => 'Artikel disimpan ke bookmark.'], 201);
    }
}
