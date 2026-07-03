<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreUserRequest;
use App\Http\Requests\UpdateUserRequest;
use App\Models\Lecturer;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    public function index()
    {
        $users = User::withCount('articles')->latest()->paginate(15);
        return view('pages.manage_users', compact('users'));
    }

    public function create()
    {
        return view('pages.user_form', ['user' => new User(), 'mode' => 'create']);
    }

    public function store(StoreUserRequest $request)
    {
        $data = $request->validated();

        $user = User::create([
            'name'              => $data['name'],
            'email'             => $data['email'],
            'password'          => Hash::make($data['password']),
            'role'              => $data['role'],
            'email_verified_at' => now(), // admin-created users auto-verified
        ]);

        // Jika role dosen, buat record lecturer juga
        if ($data['role'] === 'dosen') {
            Lecturer::create([
                'user_id' => $user->id,
                'nidn'    => $request->nidn ?? null,
                'address' => $request->address ?? null,
                'status'  => 'active',
            ]);
        }

        return redirect()->route('admin.users.index')
            ->with('success', "User {$user->name} berhasil dibuat.");
    }

    public function edit(User $user)
    {
        return view('pages.user_form', compact('user') + ['mode' => 'edit']);
    }

    public function update(UpdateUserRequest $request, User $user)
    {
        $data = $request->validated();

        $update = [
            'name'  => $data['name'],
            'email' => $data['email'],
            'role'  => $data['role'],
        ];

        if (!empty($data['password'])) {
            $update['password'] = Hash::make($data['password']);
        }

        $user->update($update);

        // Jika diubah jadi dosen dan belum punya lecturer record
        if ($data['role'] === 'dosen' && !$user->lecturer) {
            Lecturer::create([
                'user_id' => $user->id,
                'nidn'    => $request->nidn ?? null,
                'address' => $request->address ?? null,
                'status'  => 'active',
            ]);
        }

        return redirect()->route('admin.users.index')
            ->with('success', "Data {$user->name} berhasil diperbarui.");
    }

    public function destroy(User $user)
    {
        if ($user->id === auth()->id()) {
            return back()->with('error', 'Tidak bisa menghapus akun sendiri.');
        }
        $name = $user->name;
        $user->delete();
        return back()->with('success', "User {$name} berhasil dihapus.");
    }

    public function updateRole(Request $request, User $user)
    {
        $request->validate(['role' => 'required|in:admin,user,dosen']);
        if ($user->id === auth()->id()) {
            return back()->with('error', 'Tidak bisa mengubah role sendiri.');
        }
        $user->update(['role' => $request->role]);
        return back()->with('success', "Role {$user->name} diubah ke {$request->role}.");
    }
}
