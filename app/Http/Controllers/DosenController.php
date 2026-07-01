<?php

namespace App\Http\Controllers;

use App\Models\Lecturer;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class DosenController extends Controller
{
    public function index(Request $request)
    {
        $lecturers = Lecturer::with('user')
            ->when($request->q, fn ($query, $q) =>
                $query->where('nidn', 'like', "%{$q}%")
                      ->orWhere('address', 'like', "%{$q}%")
                      ->orWhereHas('user', fn ($u) =>
                          $u->where('name', 'like', "%{$q}%")
                            ->orWhere('email', 'like', "%{$q}%")
                      ))
            ->paginate(5);

        return view('pages.dosen_index', compact('lecturers'));
    }

    public function create()
    {
        return view('pages.dosen_create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name'     => 'required|string|max:255',
            'email'    => 'required|email|unique:users,email',
            'password' => 'required|string|min:6|confirmed',
            'nidn'     => 'required|string|max:50',
            'address'  => 'required|string|max:255',
            'photo'    => 'nullable|image|max:10240',
        ]);

        DB::transaction(function () use ($request) {
            // 1. Buat akun user dengan role dosen
            $user = User::create([
                'name'     => $request->name,
                'email'    => $request->email,
                'password' => Hash::make($request->password),
                'role'     => 'dosen',
            ]);

            // 2. Buat data lecturer yang terhubung ke user tersebut
            $photoPath = null;
            if ($request->hasFile('photo')) {
                $photoPath = $request->file('photo')->store('lecturers', 'public');
            }

            $lecturer = Lecturer::create([
                'user_id' => $user->id,
                'nidn'    => $request->nidn,
                'address' => $request->address,
                'photo'   => $photoPath,
                'status'  => 'waiting',
            ]);

            $this->logActivity('create', "Created lecturer: {$user->name}", $lecturer);
        });

        return redirect()->route('admin.dosen.index')->with('success', 'Dosen berhasil ditambahkan.');
    }

    public function edit(Lecturer $lecturer)
    {
        $lecturer->load('user');
        return view('pages.dosen_create', compact('lecturer'));
    }

    public function update(Request $request, Lecturer $lecturer)
    {
        $request->validate([
            'name'     => 'required|string|max:255',
            'email'    => 'required|email|unique:users,email,' . $lecturer->user_id,
            'nidn'     => 'required|string|max:50',
            'address'  => 'required|string|max:255',
            'photo'    => 'nullable|image|max:10240',
        ]);

        DB::transaction(function () use ($request, $lecturer) {
            // Update data user
            $lecturer->user->update([
                'name'  => $request->name,
                'email' => $request->email,
            ]);

            // Update data lecturer
            $data = [
                'nidn'    => $request->nidn,
                'address' => $request->address,
            ];

            if ($request->hasFile('photo')) {
                $data['photo'] = $request->file('photo')->store('lecturers', 'public');
            }

            $lecturer->update($data);

            $this->logActivity('update', "Updated lecturer: {$lecturer->user->name}", $lecturer);
        });

        return redirect()->route('admin.dosen.index')->with('success', 'Data dosen berhasil diperbarui.');
    }

    public function approve(Lecturer $lecturer)
    {
        $lecturer->update(['status' => 'active']);
        $this->logActivity('approve', "Approved lecturer: {$lecturer->user->name}", $lecturer);
        return back()->with('success', 'Dosen berhasil diaktifkan.');
    }

    public function acc(Lecturer $lecturer)
    {
        $lecturer->load('user');
        return view('pages.dosen_acc', compact('lecturer'));
    }

    public function destroy(Lecturer $lecturer)
    {
        $name = $lecturer->user->name ?? 'Unknown';
        $this->logActivity('delete', "Deleted lecturer: {$name}", $lecturer);

        DB::transaction(function () use ($lecturer) {
            $user = $lecturer->user;
            $lecturer->delete();
            // Hapus user juga sekalian supaya tidak ada akun dosen yang orphan
            if ($user) {
                $user->delete();
            }
        });

        return back()->with('status', 'Data dosen berhasil dihapus.');
    }
}
