<?php

namespace App\Policies;

use App\Models\Lecturer;
use App\Models\User;

class DosenPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->role === 'admin';
    }

    public function view(User $user, Lecturer $lecturer): bool
    {
        return $user->role === 'admin';
    }

    public function create(User $user): bool
    {
        return $user->role === 'admin';
    }

    public function update(User $user, Lecturer $lecturer): bool
    {
        return $user->role === 'admin';
    }

    public function delete(User $user, Lecturer $lecturer): bool
    {
        return $user->role === 'admin';
    }

    public function approve(User $user, Lecturer $lecturer): bool
    {
        return $user->role === 'admin';
    }
}
