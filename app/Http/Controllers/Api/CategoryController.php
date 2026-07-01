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
        $cats = Cache::remember('api_categories', 300, fn () =>
            Category::withCount(['articles' => fn ($q) => $q->where('status', 'active')])->get()
        );
        return CategoryResource::collection($cats);
    }
}
