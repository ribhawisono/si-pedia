<?php

namespace App\Http\Controllers;

use App\Models\Tag;

class TagController extends Controller
{
    public function show(Tag $tag)
    {
        $articles = $tag->articles()
            ->with(['category:id,name', 'tags:id,name,slug', 'user:id,name'])
            ->where('status', 'active')
            ->orderByDesc('created_at')
            ->paginate(12);

        return view('pages.tag_articles', compact('tag', 'articles'));
    }
}
