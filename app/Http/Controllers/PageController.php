<?php

namespace App\Http\Controllers;

use App\Models\ActivityLog;
use App\Models\Article;
use App\Models\Category;
use App\Models\Lecturer;
use App\Models\Page;
use App\Models\ReadingHistory;
use App\Models\Review;
use App\Models\Tag;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class PageController extends Controller
{
    public function home()
    {
        $articleIds = Cache::remember('homepage_article_ids_v2', 300, fn () =>
            Article::where('status', 'active')->latest()->take(6)->pluck('id')->all()
        );
        $articles = Article::with(['category:id,name', 'tags:id,name,slug'])
            ->whereIn('id', (array) $articleIds)->latest()->get();

        $page = Cache::remember('homepage_page', 600, fn () =>
            Page::where('name', 'home')->where('status', 'publish')->first()
        );

        return view('pages.homepage', compact('articles', 'page'));
    }

    public function about()
    {
        $lecturers = Lecturer::with('user:id,name,email')->where('status', 'active')->get();
        return view('pages.about', compact('lecturers'));
    }

    public function catalog(Request $request)
    {
        $query    = Article::with(['category:id,name', 'user:id,name', 'tags:id,name,slug'])
            ->where('status', 'active')
            ->where(fn ($q) => $q->whereNull('scheduled_at')->orWhere('scheduled_at', '<=', now()));

        // Filter: keyword
        if ($q = $request->get('q')) {
            $query->where(fn ($qb) => $qb
                ->where('title', 'like', "%{$q}%")
                ->orWhere('content', 'like', "%{$q}%")
                ->orWhere('writer', 'like', "%{$q}%")
            );
        }

        // Filter: category
        if ($cat = $request->get('category')) {
            $query->where('category_id', $cat);
        }

        // Filter: tag
        if ($tag = $request->get('tag')) {
            $query->whereHas('tags', fn ($t) => $t->where('slug', $tag));
        }

        // Sort
        $sort = $request->get('sort', 'newest');
        match ($sort) {
            'oldest'      => $query->oldest(),
            'most_viewed' => $query->orderByDesc('views'),
            'alpha'       => $query->orderBy('title'),
            'trending'    => $query->where('created_at', '>=', now()->subDays(30))->orderByDesc('views'),
            default       => $query->latest(),
        };

        $articles      = $query->paginate(12)->withQueryString();
        $categoryIds   = Cache::remember('categories_all_ids_v2', 300, fn () => Category::withCount([
            'articles' => fn ($q) => $q->where('status', 'active'),
        ])->pluck('id')->all());
        $categories    = Category::withCount(['articles' => fn ($q) => $q->where('status', 'active')])
            ->whereIn('id', (array) $categoryIds)->get();
        $tagIds        = Cache::remember('tags_popular_ids_v2', 300, fn () => Tag::withCount('articles')->orderByDesc('articles_count')->limit(20)->pluck('id')->all());
        $tags          = Tag::withCount('articles')->whereIn('id', (array) $tagIds)->orderByDesc('articles_count')->get();

        return view('pages.catalog', compact('articles', 'categories', 'tags', 'sort', 'q'));
    }

    public function showArticle(Article $article)
    {
        if ($article->status !== 'active') abort(404);

        $article->increment('views');
        $article->load([
            'category:id,name',
            'tags:id,name,slug',
            'user:id,name,role,avatar',
            'comments' => fn ($q) => $q->where('status', 'approved')->with('user:id,name,avatar')->latest(),
        ]);

        // Record reading history
        if (auth()->check()) {
            ReadingHistory::updateOrCreate(
                ['user_id' => auth()->id(), 'article_id' => $article->id],
                ['read_at' => now()]
            );
        }

        // Related articles (same category, exclude current)
        $related = Article::with(['category:id,name', 'tags:id,name,slug'])
            ->where('status', 'active')
            ->where('category_id', $article->category_id)
            ->where('id', '!=', $article->id)
            ->orderByDesc('views')
            ->limit(3)
            ->get();

        // Bookmark status
        $isBookmarked = auth()->check()
            ? $article->bookmarks()->where('user_id', auth()->id())->exists()
            : false;

        return view('pages.article_detail', compact('article', 'related', 'isBookmarked'));
    }

    public function faq()
    {
        return view('pages.faq');
    }

    public function contact()
    {
        return view('pages.contact');
    }

    public function terms()
    {
        return view('pages.terms');
    }

    public function privacy()
    {
        return view('pages.privacy');
    }

    public function adminPanel()
    {
        $stats = Cache::remember('admin_stats', 60, fn () => [
            'articles'  => Article::count(),
            'lecturers' => Lecturer::count(),
            'reviews'   => Review::count(),
            'users'     => User::count(),
            'pending'   => Article::whereIn('status', ['pending', 'pending_delete'])->count(),
        ]);

        $monthlyArticles = Cache::remember('admin_monthly_v2_' . now()->year, 300, fn () =>
            Article::selectRaw('MONTH(created_at) as month, COUNT(*) as count')
                ->whereYear('created_at', now()->year)
                ->groupByRaw('MONTH(created_at)')
                ->orderBy('month')
                ->get()
                ->toArray()
        );

        $articles       = Article::with('category:id,name')->latest()->take(4)->get();
        $topArticleIds  = Cache::remember('admin_top_articles_ids_v2', 120, fn () =>
            Article::where('status','active')->orderByDesc('views')->limit(5)->pluck('id')->all()
        );
        $topArticles    = Article::with('category:id,name')->whereIn('id', (array) $topArticleIds)->orderByDesc('views')->get();
        $topUserIds     = Cache::remember('admin_top_user_ids_v2', 120, fn () =>
            User::withCount('articles')->orderByDesc('articles_count')->limit(5)->pluck('id')->all()
        );
        $topUsers       = User::withCount('articles')->whereIn('id', (array) $topUserIds)->orderByDesc('articles_count')->get();
        $recentActivities = ActivityLog::with('user:id,name')->latest()->take(10)->get();

        return view('pages.admin_panel', compact('stats', 'articles', 'monthlyArticles', 'recentActivities', 'topArticles', 'topUsers'));
    }

    public function reportPosts()
    {
        $stats = [
            'total'     => Article::count(),
            'active'    => Article::where('status', 'active')->count(),
            'draft'     => Article::where('status', 'draft')->count(),
            'pending'   => Article::where('status', 'pending')->count(),
            'deleted'   => Article::onlyTrashed()->count(),
            'scheduled' => Article::whereNotNull('scheduled_at')->where('scheduled_at', '>', now())->count(),
        ];
        $articles        = Article::with(['category:id,name', 'user:id,name'])->latest()->take(6)->get();
        $recentActivities = ActivityLog::with('user:id,name')->latest()->take(10)->get();

        return view('pages.report_posts', compact('stats', 'articles', 'recentActivities'));
    }
}
