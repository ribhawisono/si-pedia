<?php

namespace App\Http\Controllers;

use App\Models\ActivityLog;
use App\Models\Article;
use App\Models\Lecturer;
use App\Models\Page;
use App\Models\Review;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class PageController extends Controller
{
    public function home()
    {
        $articles = Article::with('category')->where('status', 'active')->latest()->take(6)->get();
        $page = Page::where('name', 'home')->where('status', 'publish')->first();
        return view('pages.homepage', compact('articles', 'page'));
    }

    public function about()
    {
        return view('pages.about');
    }

    public function catalog()
    {
        $articles = Article::with('category')
            ->where('status', 'active')
            ->where(function ($query) {
                $query->whereNull('scheduled_at')
                      ->orWhere('scheduled_at', '<=', now());
            })
            ->latest()
            ->paginate(12);

        return view('pages.catalog', compact('articles'));
    }

    public function showArticle(Article $article)
    {
        if ($article->status !== 'active') {
            abort(404);
        }
        $article->load(['comments' => fn ($q) => $q->where('status', 'approved')->with('user')]);
        return view('pages.article_detail', compact('article'));
    }

    public function adminPanel()
    {
        $stats = [
            'articles'  => Article::count(),
            'lecturers' => Lecturer::count(),
            'reviews'   => Review::count(),
            'users'     => User::count(),
        ];
        $articles = Article::latest()->take(4)->get();

        // Monthly article count for chart
        $monthlyArticles = Article::selectRaw("MONTH(created_at) as month, COUNT(*) as count")
            ->whereYear('created_at', now()->year)
            ->groupByRaw("MONTH(created_at)")
            ->orderBy('month')
            ->get();

        // Recent activity log
        $recentActivities = ActivityLog::with('user')
            ->latest()
            ->take(10)
            ->get();

        return view('pages.admin_panel', compact('stats', 'articles', 'monthlyArticles', 'recentActivities'));
    }

    public function reportPosts()
    {
        $stats = [
            'total'     => Article::count(),
            'active'    => Article::where('status', 'active')->count(),
            'draft'     => Article::where('status', 'draft')->count(),
            'deleted'   => Article::onlyTrashed()->count(),
            'scheduled' => Article::whereNotNull('scheduled_at')->where('scheduled_at', '>', now())->count(),
        ];
        $articles = Article::latest()->take(4)->get();

        // Recent activity log
        $recentActivities = ActivityLog::with('user')
            ->latest()
            ->take(10)
            ->get();

        return view('pages.report_posts', compact('stats', 'articles', 'recentActivities'));
    }
}
