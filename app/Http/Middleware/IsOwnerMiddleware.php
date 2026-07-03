<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class IsOwnerMiddleware
{
    public function handle(Request $request, Closure $next): Response
    {
        $userId = $request->route('user')?->id ?? $request->route('id');

        if ($userId && $request->user()->id !== (int)$userId && ! $request->user()->isAdmin()) {
            abort(403, 'Unauthorized: Can only edit your own profile.');
        }

        return $next($request);
    }
}
