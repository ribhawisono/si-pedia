<?php

namespace App\Http\Controllers;

use App\Models\Lecturer;
use Illuminate\Support\Facades\Cache;

class DosenPublicController extends Controller
{
    public function index()
    {
        $lecturers = Cache::remember('public_lecturers', 300, fn () =>
            Lecturer::with('user:id,name,email')
                ->where('status', 'active')
                ->get()
        );
        return view('pages.dosen_public_index', compact('lecturers'));
    }

    public function show(Lecturer $lecturer)
    {
        if ($lecturer->status !== 'active') abort(404);
        $lecturer->load('user:id,name,email');

        $articles = \App\Models\Article::with('category:id,name')
            ->where('user_id', $lecturer->user_id)
            ->where('status', 'active')
            ->latest()
            ->take(6)
            ->get();

        return view('pages.dosen_public_show', compact('lecturer', 'articles'));
    }
}
