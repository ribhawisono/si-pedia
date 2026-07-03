<?php

namespace App\Policies;

use App\Models\User;

class UserPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->role === 'admin';
    }

    public function view(User $user, User $model): bool
    {
        return $user->role === 'admin' || $user->id === $model->id;
    }

    public function update(User $user, User $model): bool
    {
        return $user->id === $model->id || $user->role === 'admin';
    }

    public function updateRole(User $user, User $model): bool
    {
        return $user->role === 'admin';
    }
}
