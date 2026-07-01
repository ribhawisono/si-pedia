<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;

class UserController extends Controller
{
    public function index()
    {
        $users = User::withCount([
            'articles' => fn ($q) => $q->whereNull('deleted_at'),
        ])->latest()->paginate(15);

        return view('pages.manage_users', compact('users'));
    }

    public function updateRole(Request $request, User $user)
    {
        $request->validate([
            'role' => 'required|in:admin,user,dosen',
        ]);

        if ($user->id === auth()->id()) {
            return back()->with('error', 'Kamu tidak bisa mengubah role akunmu sendiri.');
        }

        $user->update(['role' => $request->role]);

        return back()->with('success', "Role {$user->name} berhasil diubah menjadi {$request->role}.");
    }
}
