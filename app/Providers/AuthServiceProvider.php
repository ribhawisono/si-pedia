<?php

namespace App\Providers;

use App\Models\Article;
use App\Models\Bookmark;
use App\Models\Category;
use App\Models\Comment;
use App\Models\Lecturer;
use App\Models\Review;
use App\Models\User;
use App\Policies\ArticlePolicy;
use App\Policies\BookmarkPolicy;
use App\Policies\CategoryPolicy;
use App\Policies\CommentPolicy;
use App\Policies\DosenPolicy;
use App\Policies\ReviewPolicy;
use App\Policies\UserPolicy;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Gate;

class AuthServiceProvider extends ServiceProvider
{
    protected $policies = [
        Article::class  => ArticlePolicy::class,
        Bookmark::class => BookmarkPolicy::class,
        Category::class => CategoryPolicy::class,
        Comment::class  => CommentPolicy::class,
        Lecturer::class => DosenPolicy::class,
        Review::class   => ReviewPolicy::class,
        User::class     => UserPolicy::class,
    ];

    public function boot(): void
    {
        Gate::before(fn ($user) => $user->isAdmin() ? true : null);
        Gate::define('manage-articles',    fn ($user) => $user->isAdmin());
        Gate::define('moderate-comments',  fn ($user) => $user->isAdmin());
        Gate::define('manage-users',       fn ($user) => $user->isAdmin());
    }
}
