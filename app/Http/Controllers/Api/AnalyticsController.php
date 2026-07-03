<?php
namespace App\Http\Controllers\Api;
use App\Http\Controllers\Controller;
use App\Http\Resources\ArticleResource;
use App\Models\{Article, Category, User};
use Illuminate\Support\Facades\Cache;

class AnalyticsController extends Controller
{
    public function popular()
    {
        $mostViewedIds = Cache::remember('api_analytics_popular_ids', 300, fn () =>
            Article::where('status','active')->orderByDesc('views')->limit(10)->pluck('id')
        );
        $data = [
            'most_viewed'     => Article::with('category:id,name')->whereIn('id', $mostViewedIds)->orderByDesc('views')->get(),
            'category_stats'  => Category::withCount(['articles' => fn ($q) => $q->where('status','active')])->get(),
            'total_articles'  => Article::where('status','active')->count(),
            'total_users'     => User::count(),
            'total_views'     => Article::where('status','active')->sum('views'),
        ];

        return response()->json([
            'most_viewed'    => ArticleResource::collection($data['most_viewed']),
            'category_stats' => $data['category_stats'],
            'stats' => [
                'total_articles' => $data['total_articles'],
                'total_users'    => $data['total_users'],
                'total_views'    => $data['total_views'],
            ],
        ]);
    }

    public function monthly()
    {
        $monthly = Cache::remember('api_monthly_' . now()->year, 300, fn () =>
            Article::selectRaw('MONTH(created_at) as month, COUNT(*) as count, SUM(views) as total_views')
                ->where('status', 'active')
                ->whereYear('created_at', now()->year)
                ->groupByRaw('MONTH(created_at)')
                ->orderBy('month')
                ->get()
                ->toArray()
        );
        return response()->json(['year' => now()->year, 'data' => $monthly]);
    }
}
