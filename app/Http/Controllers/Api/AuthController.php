<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\UserResource;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    /** POST /api/v1/auth/login */
    public function login(Request $request): JsonResponse
    {
        $data = $request->validate([
            'email'    => 'required|email',
            'password' => 'required|string',
        ]);

        $user = User::where('email', $data['email'])->first();

        if (!$user || !Hash::check($data['password'], $user->password)) {
            throw ValidationException::withMessages([
                'email' => ['Email atau password salah.'],
            ]);
        }

        // Revoke previous tokens with same name
        DB::table('personal_access_tokens')
            ->where('tokenable_type', User::class)
            ->where('tokenable_id', $user->id)
            ->where('name', 'api')
            ->delete();

        $plainToken = $this->createToken($user, 'api', now()->addDays(30));

        return response()->json([
            'message' => 'Login berhasil.',
            'token'   => $plainToken,
            'user'    => new UserResource($user),
        ]);
    }

    /** POST /api/v1/auth/register */
    public function register(Request $request): JsonResponse
    {
        $data = $request->validate([
            'name'     => 'required|string|max:255',
            'email'    => 'required|email|unique:users,email',
            'password' => 'required|string|min:6|confirmed',
        ]);

        $user  = User::create([
            'name'     => $data['name'],
            'email'    => $data['email'],
            'password' => Hash::make($data['password']),
            'role'     => 'user',
        ]);

        $plainToken = $this->createToken($user, 'api', now()->addDays(30));

        return response()->json([
            'message' => 'Registrasi berhasil.',
            'token'   => $plainToken,
            'user'    => new UserResource($user),
        ], 201);
    }

    /** POST /api/v1/auth/logout */
    public function logout(Request $request): JsonResponse
    {
        $plainToken = $request->bearerToken();
        if ($plainToken) {
            DB::table('personal_access_tokens')
                ->where('token', hash('sha256', $plainToken))
                ->delete();
        }
        return response()->json(['message' => 'Logout berhasil.']);
    }

    /** GET /api/v1/auth/me */
    public function me(Request $request): JsonResponse
    {
        return response()->json(new UserResource($request->user()));
    }

    /**
     * Create a token, store hashed version, return plain token.
     */
    private function createToken(User $user, string $name, \Carbon\Carbon $expiresAt): string
    {
        $plainToken = Str::random(64);

        DB::table('personal_access_tokens')->insert([
            'tokenable_type' => User::class,
            'tokenable_id'   => $user->id,
            'name'           => $name,
            'token'          => hash('sha256', $plainToken),
            'abilities'      => json_encode(['*']),
            'expires_at'     => $expiresAt,
            'created_at'     => now(),
            'updated_at'     => now(),
        ]);

        return $plainToken;
    }
}
