<?php

use App\Http\Controllers\AccountReportController;
use App\Http\Controllers\ArticleController;
use App\Http\Controllers\ArticleReportController;
use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\CommentController;
use App\Http\Controllers\DosenController;
use App\Http\Controllers\HomepageController;
use App\Http\Controllers\PageController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ReviewController;
use App\Http\Controllers\BookmarkController;
use App\Http\Controllers\DosenPublicController;
use App\Http\Controllers\UserPublicController;
use App\Http\Controllers\SearchController;
use App\Http\Controllers\TagController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

require __DIR__.'/lecturer_photos.php';

Route::get('/search', [SearchController::class, 'index'])->name('search');
Route::get('/search/suggest', [SearchController::class, 'suggest'])->name('search.suggest')->middleware('throttle:30,1');

Route::get('/tags/{tag:slug}', [TagController::class, 'show'])->name('tags.show');

Route::get('/favicon.ico', function () {
    $ico = base64_decode('AAABAAEAEBAAAAAAIABZAgAAFgAAAIlQTkcNChoKAAAADUlIRFIAAAAQAAAAEAgGAAAAH/P/YQAAAiBJREFUeJyNkk9Ik3Ecxj+/d53eQxMvklgzMv/Qu4zWxZGKVjKlY4SSWXkwIzrUG4KxkLJ2MrwYRDE8ZCJYKPSPLh6GCboE10LrZhq7bu8szdr27TTdnGUP/A7fP8/D8334KQA9z2gHTIQywMa/IQJflChzNR5+o/Q8ox3BvwNpWyEl0qQB5nbTivISno8M8Gr8CUcqK7ZbUaJUL7rdSOh2Q9LvYMUJ8Q+OSiKRlDRSqZS8GHsnhyo9krmr243fpItCh1se9Ptlbe2n/A3r67/EPzgq+w5UpwVS5Be4pNvbJ9GolUOYC83LXGg+px+NWtLt7ZP8AldK24xWNo6zrBXMLh/Vdc1U1zVjdvmwrJWsAHy9Jk2eWrJOmJ4JydDwuDhKarbeKo6SGhkaHpeZYEgKHW4REWm9cGPTQSwWZ2k5gqY0lFI5kScSSSzrO1+XIsRi8Y2+lrmklOLUyeMEp8bo7GjBZtOw2TQ6O1qYC77k7JkmNC2Lki0AEJgMUt/QirvKRWBihMDECO4qF/UNrQQmgynOdmUW76dmuXL5HKWl+2m7dJNjLicAH2bDNHpqcRplPHr8DIDzF02mZ0Io3W4kyPj/e4v20OO9RnFxEb2+hwDcvnWVxcVv3L0/wNJyJCsapduNWeDoVmuHneXcu3MdAG9PPx/Dn3PsA2Gl73Y2ouQ1kBv9DlBCm7YaD79F1GlgAUj9By8JhJXQ9iP+6ekfuuMod8b6I/8AAAAASUVORK5CYII=');
    return response($ico, 200)
        ->header('Content-Type', 'image/x-icon')
        ->header('Cache-Control', 'public, max-age=604800');
})->name('favicon');

Route::get('/', [PageController::class, 'home'])->name('home');
Route::get('/about', [PageController::class, 'about'])->name('about');
Route::get('/catalog', [PageController::class, 'catalog'])->name('catalog');
Route::get('/faq', [PageController::class, 'faq'])->name('faq');
Route::get('/contact', [PageController::class, 'contact'])->name('contact');
Route::get('/terms', [PageController::class, 'terms'])->name('terms');
Route::get('/privacy', [PageController::class, 'privacy'])->name('privacy');
Route::get('/review', [ReviewController::class, 'index'])->name('review.index');
Route::get('/review/create', [ReviewController::class, 'create'])->name('review.create')->middleware('auth', 'verified');
Route::post('/review', [ReviewController::class, 'store'])->name('review.store')->middleware('auth', 'verified', 'throttle:3,1');

Route::get('/dosen', [DosenPublicController::class, 'index'])->name('dosen.public.index');
Route::get('/dosen/{lecturer}', [DosenPublicController::class, 'show'])->name('dosen.public.show');

Route::get('/u/{user}', [UserPublicController::class, 'show'])->name('users.public.show');

Route::get('/articles/{article:slug}/comments', [CommentController::class, 'index'])->name('comments.index');
Route::post('/articles/{article}/comments', [CommentController::class, 'store'])
    ->middleware('auth', 'throttle:10,1')->name('comments.store');

Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login'])->middleware('throttle:5,1');
    Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
    Route::post('/register', [AuthController::class, 'register'])->middleware('throttle:3,1');
    Route::get('/forgot-password', [AuthController::class, 'showForgot'])->name('password.request');
    Route::post('/forgot-password', [AuthController::class, 'sendResetLink'])->name('password.email')->middleware('throttle:3,1');
    Route::get('/reset-password/{token}', [AuthController::class, 'showResetForm'])->name('password.reset');
    Route::post('/reset-password', [AuthController::class, 'resetPassword'])->name('password.update')->middleware('throttle:3,1');
});
Route::post('/logout', [AuthController::class, 'logout'])->middleware('auth')->name('logout');

Route::middleware('auth')->group(function () {
    Route::get('/email/verify', [AuthController::class, 'showOtp'])->name('verification.notice');
    Route::get('/email/verify/otp', [AuthController::class, 'showOtp'])->name('verification.otp');
    Route::post('/email/verify/otp', [AuthController::class, 'verifyOtp'])->name('verification.otp.verify')->middleware('throttle:5,1');
    Route::post('/email/verify/resend', [AuthController::class, 'resendOtp'])->name('verification.otp.resend')->middleware('throttle:3,1');
    Route::post('/email/verification-notification', [AuthController::class, 'resendOtp'])->name('verification.send');
});

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/profile', [ProfileController::class, 'show'])->name('profile.show');
    Route::get('/profile/edit', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::put('/profile', [ProfileController::class, 'update'])->name('profile.update');

    Route::prefix('articles')->name('articles.')->group(function () {
        Route::get('/my', [ArticleController::class, 'myArticles'])->name('my');
        Route::get('/create', [ArticleController::class, 'create'])->name('create');
        Route::post('/', [ArticleController::class, 'store'])->name('store');
        Route::get('/{article}/edit', [ArticleController::class, 'edit'])->name('edit')->withTrashed();
        Route::put('/{article}', [ArticleController::class, 'update'])->name('update')->withTrashed();
        Route::patch('/{article}/request-delete', [ArticleController::class, 'requestDelete'])->name('requestDelete');
        Route::get('/{article}/preview', [ArticleController::class, 'preview'])->name('preview')->withTrashed();
        Route::get('/{article}/revisions', [ArticleController::class, 'revisions'])->name('revisions')->withTrashed();
    });

    Route::post('/articles/{article}/bookmark', [BookmarkController::class, 'toggle'])->name('bookmarks.toggle');
    Route::get('/profile/bookmarks', [BookmarkController::class, 'index'])->name('bookmarks.index');

    Route::get('/users/{user}/report', [AccountReportController::class, 'create'])->name('users.report');
    Route::post('/users/{user}/report', [AccountReportController::class, 'store'])->name('users.report.store');

    Route::get('/articles/{article}/report', [ArticleReportController::class, 'create'])->name('articles.report');
    Route::post('/articles/{article}/report', [ArticleReportController::class, 'store'])->name('articles.report.store');
});

Route::get('/articles/{article:slug}', [PageController::class, 'showArticle'])->name('articles.show');

Route::middleware(['auth', 'admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/', [PageController::class, 'adminPanel'])->name('panel');
    Route::get('/report', [PageController::class, 'reportPosts'])->name('report');

    Route::get('/articles', [ArticleController::class, 'index'])->name('articles.index');
    Route::get('/articles/pending', [ArticleController::class, 'pendingIndex'])->name('articles.pending');
    Route::get('/articles/trash', [ArticleController::class, 'trash'])->name('articles.trash');
    Route::patch('/articles/{id}/restore', [ArticleController::class, 'restore'])->where('id', '[0-9]+')->name('articles.restore');
    Route::delete('/articles/{id}/force-delete', [ArticleController::class, 'forceDelete'])->where('id', '[0-9]+')->name('articles.forceDelete');
    Route::get('/articles/create', [ArticleController::class, 'create'])->name('articles.create');
    Route::post('/articles', [ArticleController::class, 'store'])->name('articles.store');
    Route::get('/articles/{article}/edit', [ArticleController::class, 'edit'])->name('articles.edit')->withTrashed();
    Route::put('/articles/{article}', [ArticleController::class, 'update'])->name('articles.update')->withTrashed();
    Route::delete('/articles/{article}', [ArticleController::class, 'destroy'])->name('articles.destroy');
    Route::patch('/articles/bulk', [ArticleController::class, 'bulkAction'])->name('articles.bulk');
    Route::patch('/articles/{article}/approve', [ArticleController::class, 'approve'])->name('articles.approve');
    Route::patch('/articles/{article}/reject', [ArticleController::class, 'reject'])->name('articles.reject');
    Route::delete('/articles/{article}/approve-delete', [ArticleController::class, 'approveDelete'])->name('articles.approveDelete');
    Route::patch('/articles/{article}/reject-delete', [ArticleController::class, 'rejectDelete'])->name('articles.rejectDelete');

    Route::get('/articles/{article}/takedown', [ArticleController::class, 'takedownForm'])->name('articles.takedownForm');
    Route::post('/articles/{article}/takedown', [ArticleController::class, 'takedown'])->name('articles.takedown');

    Route::patch('/reviews/{review}/accept', [ReviewController::class, 'accept'])->name('reviews.accept');
    Route::patch('/reviews/{review}/decline', [ReviewController::class, 'decline'])->name('reviews.decline');

    Route::get('/homepage/edit', [HomepageController::class, 'edit'])->name('homepage.edit');
    Route::put('/homepage', [HomepageController::class, 'update'])->name('homepage.update');
    Route::get('/pages/create', [HomepageController::class, 'createPage'])->name('pages.create');
    Route::post('/pages', [HomepageController::class, 'storePage'])->name('pages.store');

    Route::get('/categories', [CategoryController::class, 'index'])->name('categories.index');
    Route::post('/categories', [CategoryController::class, 'store'])->name('categories.store');
    Route::delete('/categories/{category}', [CategoryController::class, 'destroy'])->name('categories.destroy');

    Route::get('/users', [UserController::class, 'index'])->name('users.index');
    Route::get('/users/create', [UserController::class, 'create'])->name('users.create');
    Route::post('/users', [UserController::class, 'store'])->name('users.store');
    Route::get('/users/{user}/edit', [UserController::class, 'edit'])->name('users.edit');
    Route::put('/users/{user}', [UserController::class, 'update'])->name('users.update');
    Route::delete('/users/{user}', [UserController::class, 'destroy'])->name('users.destroy');
    Route::patch('/users/{user}/role', [UserController::class, 'updateRole'])->name('users.updateRole');

    Route::get('/dosen', [DosenController::class, 'index'])->name('dosen.index');
    Route::get('/dosen/create', [DosenController::class, 'create'])->name('dosen.create');
    Route::post('/dosen', [DosenController::class, 'store'])->name('dosen.store');
    Route::get('/dosen/{lecturer}/edit', [DosenController::class, 'edit'])->name('dosen.edit');
    Route::put('/dosen/{lecturer}', [DosenController::class, 'update'])->name('dosen.update');
    Route::get('/dosen/{lecturer}/acc', [DosenController::class, 'acc'])->name('dosen.acc');
    Route::patch('/dosen/{lecturer}/approve', [DosenController::class, 'approve'])->name('dosen.approve');
    Route::delete('/dosen/{lecturer}', [DosenController::class, 'destroy'])->name('dosen.destroy');

    Route::get('/comments', [\App\Http\Controllers\CommentController::class, 'adminIndex'])->name('comments.index');
    Route::patch('/comments/{comment}/approve', [\App\Http\Controllers\CommentController::class, 'approve'])->name('comments.approve');
    Route::patch('/comments/{comment}/reject', [\App\Http\Controllers\CommentController::class, 'reject'])->name('comments.reject');
    Route::delete('/comments/{comment}', [\App\Http\Controllers\CommentController::class, 'destroy'])->name('comments.destroy');
    Route::post('/comments/bulk', [\App\Http\Controllers\CommentController::class, 'bulk'])->name('comments.bulk');

    Route::get('/articles/{article}/preview', [\App\Http\Controllers\ArticleController::class, 'preview'])->name('articles.preview');
    Route::get('/articles/{article}/revisions', [\App\Http\Controllers\ArticleController::class, 'revisions'])->name('articles.revisions')->withTrashed();

    Route::get('/account-reports', [AccountReportController::class, 'index'])->name('account-reports.index');
    Route::patch('/account-reports/{report}', [AccountReportController::class, 'update'])->name('account-reports.update');

    Route::get('/article-reports', [ArticleReportController::class, 'index'])->name('article-reports.index');
    Route::patch('/article-reports/{report}', [ArticleReportController::class, 'update'])->name('article-reports.update');
});
