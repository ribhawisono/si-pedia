<?php

namespace App\Policies;

use App\Models\Article;
use App\Models\User;

class ArticlePolicy
{
    public function viewAny(?User $user): bool   { return true; }
    public function view(?User $user, Article $article): bool { return $article->status === 'active' || ($user && ($user->isAdmin() || $article->user_id === $user->id)); }
    public function create(User $user): bool      { return true; } // any authenticated user
    public function update(User $user, Article $article): bool { return $user->isAdmin() || ($article->user_id === $user->id && !in_array($article->status, ['active', 'pending_delete'])); }
    public function delete(User $user, Article $article): bool { return $user->isAdmin(); }
    public function requestDelete(User $user, Article $article): bool { return $article->user_id === $user->id && !in_array($article->status, ['pending_delete']); }
    public function approve(User $user): bool     { return $user->isAdmin(); }
    public function forceDelete(User $user): bool { return $user->isAdmin(); }
}
