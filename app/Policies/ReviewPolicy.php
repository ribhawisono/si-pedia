<?php

namespace App\Policies;

use App\Models\Review;
use App\Models\User;

class ReviewPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->role === 'admin';
    }

    public function view(User $user, Review $review): bool
    {
        return $user->role === 'admin';
    }

    public function accept(User $user, Review $review): bool
    {
        return $user->role === 'admin';
    }

    public function decline(User $user, Review $review): bool
    {
        return $user->role === 'admin';
    }
}
