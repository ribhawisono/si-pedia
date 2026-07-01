<?php

use App\Http\Controllers\AccountReportController;
use App\Http\Controllers\ArticleController;
use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\CommentController;
use App\Http\Controllers\DosenController;
use App\Http\Controllers\HomepageController;
use App\Http\Controllers\PageController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ReviewController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

// ─── Publik ───────────────────────────────────────────────────────────────────
Route::get('/', [PageController::class, 'home'])->name('home');
Route::get('/about', [PageController::class, 'about'])->name('about');
Route::get('/catalog', [PageController::class, 'catalog'])->name('catalog');
Route::get('/faq', [PageController::class, 'faq'])->name('faq');
Route::get('/articles/{article:slug}', [PageController::class, 'showArticle'])->name('articles.show');
Route::get('/review', [ReviewController::class, 'index'])->name('review.index');

// ─── Comments ─────────────────────────────────────────────────────────────────
Route::get('/articles/{article:slug}/comments', [CommentController::class, 'index'])->name('comments.index');
Route::post('/articles/{article}/comments', [CommentController::class, 'store'])
    ->middleware('auth', 'throttle:10,1')->name('comments.store');

// ─── Auth (guest) ─────────────────────────────────────────────────────────────
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

// ─── Email Verification (OTP) ──────────────────────────────────────────────────
Route::middleware('auth')->group(function () {
    Route::get('/email/verify', [AuthController::class, 'showOtp'])->name('verification.notice');
    Route::get('/email/verify/otp', [AuthController::class, 'showOtp'])->name('verification.otp');
    Route::post('/email/verify/otp', [AuthController::class, 'verifyOtp'])->name('verification.otp.verify')->middleware('throttle:5,1');
    Route::post('/email/verify/resend', [AuthController::class, 'resendOtp'])->name('verification.otp.resend')->middleware('throttle:3,1');
    Route::post('/email/verification-notification', [AuthController::class, 'resendOtp'])->name('verification.send');
});

// ─── User terautentikasi ──────────────────────────────────────────────────────
Route::middleware(['auth', 'verified'])->group(function () {
    // Profil
    Route::get('/profile', [ProfileController::class, 'show'])->name('profile.show');
    Route::get('/profile/edit', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::put('/profile', [ProfileController::class, 'update'])->name('profile.update');

    // Artikel milik sendiri
    Route::prefix('articles')->name('articles.')->group(function () {
        Route::get('/my', [ArticleController::class, 'myArticles'])->name('my');
        Route::get('/create', [ArticleController::class, 'create'])->name('create');
        Route::post('/', [ArticleController::class, 'store'])->name('store');
        Route::get('/{article}/edit', [ArticleController::class, 'edit'])->name('edit');
        Route::put('/{article}', [ArticleController::class, 'update'])->name('update');
        Route::patch('/{article}/request-delete', [ArticleController::class, 'requestDelete'])->name('requestDelete');
    });

    // Report akun
    Route::get('/users/{user}/report', [AccountReportController::class, 'create'])->name('users.report');
    Route::post('/users/{user}/report', [AccountReportController::class, 'store'])->name('users.report.store');
});

// ─── Admin ────────────────────────────────────────────────────────────────────
Route::middleware(['auth', 'admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/', [PageController::class, 'adminPanel'])->name('panel');
    Route::get('/report', [PageController::class, 'reportPosts'])->name('report');

    // Artikel
    Route::get('/articles', [ArticleController::class, 'index'])->name('articles.index');
    Route::get('/articles/pending', [ArticleController::class, 'pendingIndex'])->name('articles.pending');
    Route::get('/articles/create', [ArticleController::class, 'create'])->name('articles.create');
    Route::post('/articles', [ArticleController::class, 'store'])->name('articles.store');
    Route::get('/articles/{article}/edit', [ArticleController::class, 'edit'])->name('articles.edit');
    Route::put('/articles/{article}', [ArticleController::class, 'update'])->name('articles.update');
    Route::delete('/articles/{article}', [ArticleController::class, 'destroy'])->name('articles.destroy');
    Route::patch('/articles/{article}/bulk', [ArticleController::class, 'bulkAction'])->name('articles.bulk');
    Route::patch('/articles/{article}/approve', [ArticleController::class, 'approve'])->name('articles.approve');
    Route::patch('/articles/{article}/reject', [ArticleController::class, 'reject'])->name('articles.reject');
    Route::delete('/articles/{article}/approve-delete', [ArticleController::class, 'approveDelete'])->name('articles.approveDelete');
    Route::patch('/articles/{article}/reject-delete', [ArticleController::class, 'rejectDelete'])->name('articles.rejectDelete');

    // Review
    Route::patch('/reviews/{review}/accept', [ReviewController::class, 'accept'])->name('reviews.accept');
    Route::patch('/reviews/{review}/decline', [ReviewController::class, 'decline'])->name('reviews.decline');

    // Homepage & Pages
    Route::get('/homepage/edit', [HomepageController::class, 'edit'])->name('homepage.edit');
    Route::put('/homepage', [HomepageController::class, 'update'])->name('homepage.update');
    Route::get('/pages/create', [HomepageController::class, 'createPage'])->name('pages.create');
    Route::post('/pages', [HomepageController::class, 'storePage'])->name('pages.store');

    // Category
    Route::get('/categories', [CategoryController::class, 'index'])->name('categories.index');
    Route::post('/categories', [CategoryController::class, 'store'])->name('categories.store');
    Route::delete('/categories/{category}', [CategoryController::class, 'destroy'])->name('categories.destroy');

    // Users — CRUD lengkap
    Route::get('/users', [UserController::class, 'index'])->name('users.index');
    Route::get('/users/create', [UserController::class, 'create'])->name('users.create');
    Route::post('/users', [UserController::class, 'store'])->name('users.store');
    Route::get('/users/{user}/edit', [UserController::class, 'edit'])->name('users.edit');
    Route::put('/users/{user}', [UserController::class, 'update'])->name('users.update');
    Route::delete('/users/{user}', [UserController::class, 'destroy'])->name('users.destroy');
    Route::patch('/users/{user}/role', [UserController::class, 'updateRole'])->name('users.updateRole');

    // Dosen
    Route::get('/dosen', [DosenController::class, 'index'])->name('dosen.index');
    Route::get('/dosen/create', [DosenController::class, 'create'])->name('dosen.create');
    Route::post('/dosen', [DosenController::class, 'store'])->name('dosen.store');
    Route::get('/dosen/{lecturer}/edit', [DosenController::class, 'edit'])->name('dosen.edit');
    Route::put('/dosen/{lecturer}', [DosenController::class, 'update'])->name('dosen.update');
    Route::get('/dosen/{lecturer}/acc', [DosenController::class, 'acc'])->name('dosen.acc');
    Route::patch('/dosen/{lecturer}/approve', [DosenController::class, 'approve'])->name('dosen.approve');
    Route::delete('/dosen/{lecturer}', [DosenController::class, 'destroy'])->name('dosen.destroy');

    // Report Akun
    Route::get('/account-reports', [AccountReportController::class, 'index'])->name('account-reports.index');
    Route::patch('/account-reports/{report}', [AccountReportController::class, 'update'])->name('account-reports.update');
});
