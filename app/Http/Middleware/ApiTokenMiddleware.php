<?php

namespace App\Http\Middleware;

use App\Models\User;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpFoundation\Response;

/**
 * Lightweight API token authentication.
 * Reads Bearer token from Authorization header,
 * validates against personal_access_tokens table (SHA-256 hashed).
 * Drop-in replacement for auth:sanctum. No external package required.
 */
class ApiTokenMiddleware
{
    public function handle(Request $request, Closure $next): Response
    {
        $plainToken = $request->bearerToken();

        if (!$plainToken) {
            return response()->json([
                'message' => 'Token tidak ditemukan. Sertakan Authorization: Bearer {token}',
            ], 401);
        }

        $hashed = hash('sha256', $plainToken);

        $tokenRecord = DB::table('personal_access_tokens')
            ->where('token', $hashed)
            ->first();

        if (!$tokenRecord) {
            return response()->json(['message' => 'Token tidak valid.'], 401);
        }

        // Check expiry
        if ($tokenRecord->expires_at && now()->isAfter($tokenRecord->expires_at)) {
            DB::table('personal_access_tokens')->where('id', $tokenRecord->id)->delete();
            return response()->json(['message' => 'Token sudah kadaluarsa. Silakan login ulang.'], 401);
        }

        // Update last_used_at
        DB::table('personal_access_tokens')
            ->where('id', $tokenRecord->id)
            ->update(['last_used_at' => now()]);

        // Bind authenticated user to request
        $user = User::find($tokenRecord->tokenable_id);

        if (!$user) {
            return response()->json(['message' => 'User tidak ditemukan.'], 401);
        }

        auth()->setUser($user);
        $request->setUserResolver(fn () => $user);

        return $next($request);
    }
}
