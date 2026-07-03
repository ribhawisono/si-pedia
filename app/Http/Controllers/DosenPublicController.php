<?php

namespace App\Http\Controllers;

use App\Models\Lecturer;
use Illuminate\Support\Facades\Cache;

class DosenPublicController extends Controller
{
    public function index()
    {
        // Cache only primitive IDs, then rehydrate via a clean Eloquent query.
        // Caching raw Collections/Models can crash unserialize() on this route
        // ("incomplete object ... Collection") if the cache entry was written
        // before the Eloquent classes were fully autoloaded in a given request.
        $ids = Cache::remember('public_lecturer_ids', 300, fn () =>
            Lecturer::where('status', 'active')->pluck('id')
        );

        $lecturers = Lecturer::with('user:id,name,email')
            ->whereIn('id', $ids)
            ->get();

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
