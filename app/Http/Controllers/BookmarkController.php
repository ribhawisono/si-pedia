<?php

namespace App\Http\Controllers;

use App\Models\Article;
use App\Models\Bookmark;
use Illuminate\Http\Request;

class BookmarkController extends Controller
{
    /** Toggle bookmark — returns JSON for JS or redirects */
    public function toggle(Request $request, Article $article)
    {
        $user = auth()->user();

        $existing = Bookmark::where('user_id', $user->id)
            ->where('article_id', $article->id)
            ->first();

        if ($existing) {
            $existing->delete();
            $bookmarked = false;
            $message    = 'Bookmark dihapus.';
        } else {
            Bookmark::create(['user_id' => $user->id, 'article_id' => $article->id]);
            $bookmarked = true;
            $message    = 'Artikel ditambahkan ke bookmark.';
        }

        if ($request->wantsJson()) {
            return response()->json([
                'bookmarked' => $bookmarked,
                'count'      => $article->bookmarks()->count(),
                'message'    => $message,
            ]);
        }

        return back()->with('success', $message);
    }

    /** Daftar bookmark milik user */
    public function index()
    {
        $bookmarks = auth()->user()
            ->bookmarkedArticles()
            ->with(['category:id,name', 'tags:id,name,slug'])
            ->where('status', 'active')
            ->paginate(12);

        return view('pages.bookmarks', compact('bookmarks'));
    }
}
