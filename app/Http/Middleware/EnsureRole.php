<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureRole
{
    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next, string ...$roles): Response
    {
        $user = $request->user();
        $userRole = $user?->role;

        if (! $user) {
            if (! $request->expectsJson()) {
                return redirect()->route('login');
            }

            return response()->json([
                'message' => 'Unauthenticated.',
            ], 401);
        }

        if (! in_array($userRole, $roles, true)) {
            if (! $request->expectsJson()) {
                abort(403);
            }

            return response()->json([
                'message' => 'Forbidden: insufficient role.',
            ], 403);
        }

        return $next($request);
    }
}
