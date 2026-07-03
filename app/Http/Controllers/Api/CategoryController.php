<?php
namespace App\Http\Controllers\Api;
use App\Http\Controllers\Controller;
use App\Http\Resources\CategoryResource;
use App\Models\Category;
use Illuminate\Support\Facades\Cache;

class CategoryController extends Controller
{
    public function index()
    {
        $ids = Cache::remember('api_category_ids', 300, fn () =>
            Category::withCount(['articles' => fn ($q) => $q->where('status', 'active')])->pluck('id')
        );
        $cats = Category::withCount(['articles' => fn ($q) => $q->where('status', 'active')])->whereIn('id', $ids)->get();
        return CategoryResource::collection($cats);
    }
}
