<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;

class UserPublicController extends Controller
{
    public function show(User $user)
    {
        $articles = \App\Models\Article::with('category:id,name', 'tags:id,name,slug')
            ->where('user_id', $user->id)
            ->where('status', 'active')
            ->latest()
            ->paginate(9);

        return view('pages.user_public_profile', compact('user', 'articles'));
    }
}
