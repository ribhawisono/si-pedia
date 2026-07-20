<?php

use App\Http\Controllers\Api\AnalyticsController;
use App\Http\Controllers\Api\ArticleController;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\BookmarkController;
use App\Http\Controllers\Api\CategoryController;
use App\Http\Controllers\Api\CommentController;
use App\Http\Controllers\Api\LecturerController;
use App\Http\Controllers\Api\SearchController;
use App\Http\Controllers\Api\TagController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|──────────────────────────────────────────────────────────────────
| SI-Pedia REST API v1
|
| Base URL : /api/v1
| Auth     : Bearer token (custom ApiTokenMiddleware)
| Format   : JSON
|──────────────────────────────────────────────────────────────────
*/

Route::prefix('v1')->name('api.')->group(function () {

    // Health check
    Route::get('/', fn () => response()->json([
        'name'    => 'SI-Pedia API',
        'version' => 'v1',
        'status'  => 'ok',
        'docs'    => url('/api/v1/docs'),
    ]))->name('health');

    // Authentication
    Route::prefix('auth')->name('auth.')->group(function () {
        Route::post('login',    [AuthController::class, 'login'])->middleware('throttle:10,1')->name('login');
        Route::post('register', [AuthController::class, 'register'])->middleware('throttle:5,1')->name('register');

        Route::middleware('auth.api')->group(function () {
            Route::post('logout', [AuthController::class, 'logout'])->name('logout');
            Route::get('me',      [AuthController::class, 'me'])->name('me');
        });
    });

    // Articles (public)
    Route::prefix('articles')->name('articles.')->group(function () {
        Route::get('/',          [ArticleController::class, 'index'])->middleware('throttle:60,1')->name('index');
        Route::get('{article:slug}', [ArticleController::class, 'show'])->name('show');

        // Comments
        Route::get('{article:slug}/comments', [CommentController::class, 'index'])->name('comments.index');
        Route::post('{article:slug}/comments', [CommentController::class, 'store'])
            ->middleware('auth.api', 'throttle:10,1')->name('comments.store');

        // Bookmark toggle
        Route::post('{article:slug}/bookmark', [BookmarkController::class, 'toggle'])
            ->middleware('auth.api')->name('bookmark');
    });

    // Categories
    Route::get('categories', [CategoryController::class, 'index'])->middleware('throttle:60,1')->name('categories.index');

    // Tags
    Route::prefix('tags')->name('tags.')->group(function () {
        Route::get('/',              [TagController::class, 'index'])->middleware('throttle:60,1')->name('index');
        Route::get('{tag:slug}/articles', [TagController::class, 'articles'])->name('articles');
    });

    // Lecturers
    Route::get('lecturers', [LecturerController::class, 'index'])->middleware('throttle:60,1')->name('lecturers.index');

    // Search
    Route::get('search', SearchController::class)->middleware('throttle:30,1')->name('search');

    // Analytics (public)
    Route::prefix('analytics')->name('analytics.')->group(function () {
        Route::get('popular', [AnalyticsController::class, 'popular'])->middleware('throttle:30,1')->name('popular');
        Route::get('monthly', [AnalyticsController::class, 'monthly'])->middleware('throttle:30,1')->name('monthly');
    });

    // Bookmarks (authenticated)
    Route::middleware('auth.api')->group(function () {
        Route::get('bookmarks', [BookmarkController::class, 'index'])->name('bookmarks.index');
    });

    // API Documentation
    Route::get('docs', fn () => response()->json([
        'endpoints' => [
            'GET  /api/v1/'                           => 'Health check',
            'POST /api/v1/auth/login'                 => 'Login → Bearer token',
            'POST /api/v1/auth/register'              => 'Register',
            'POST /api/v1/auth/logout'                => '[Auth] Logout',
            'GET  /api/v1/auth/me'                    => '[Auth] Current user',
            'GET  /api/v1/articles'                   => 'List articles (q, category, tag, sort, per_page)',
            'GET  /api/v1/articles/{slug}'            => 'Article detail + related',
            'GET  /api/v1/articles/{slug}/comments'   => 'Article comments',
            'POST /api/v1/articles/{slug}/comments'   => '[Auth] Post comment',
            'POST /api/v1/articles/{slug}/bookmark'   => '[Auth] Toggle bookmark',
            'GET  /api/v1/categories'                 => 'All categories',
            'GET  /api/v1/tags'                       => 'All tags (sorted by usage)',
            'GET  /api/v1/tags/{slug}/articles'       => 'Articles by tag',
            'GET  /api/v1/lecturers'                  => 'Active lecturers (q, per_page)',
            'GET  /api/v1/search?q='                  => 'Full-text search (q min 2 chars)',
            'GET  /api/v1/analytics/popular'          => 'Most viewed articles + category stats',
            'GET  /api/v1/analytics/monthly'          => 'Monthly article counts',
            'GET  /api/v1/bookmarks'                  => '[Auth] User bookmarks',
        ],
        'auth_header' => 'Authorization: Bearer {token}',
        'pagination'  => ['page', 'per_page (max 50, default 15)'],
    ]))->name('docs');
});
