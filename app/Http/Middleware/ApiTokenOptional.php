<?php

namespace App\Http\Middleware;

use App\Models\User;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpFoundation\Response;

/**
 * Tries to authenticate via Bearer token but doesn't reject if missing.
 * Used for endpoints that are public but have extra features when authenticated.
 */
class ApiTokenOptional
{
    public function handle(Request $request, Closure $next): Response
    {
        $plainToken = $request->bearerToken();

        if ($plainToken) {
            $hashed = hash('sha256', $plainToken);
            $tokenRecord = DB::table('personal_access_tokens')
                ->where('token', $hashed)
                ->first();

            if ($tokenRecord && !($tokenRecord->expires_at && now()->isAfter($tokenRecord->expires_at))) {
                DB::table('personal_access_tokens')
                    ->where('id', $tokenRecord->id)
                    ->update(['last_used_at' => now()]);

                $user = User::find($tokenRecord->tokenable_id);
                if ($user) {
                    auth()->setUser($user);
                    $request->setUserResolver(fn () => $user);
                }
            }
        }

        return $next($request);
    }
}
