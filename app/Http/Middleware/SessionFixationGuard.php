<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Marks sessions (and anything else derived from them) as read-only after
 * logout, and stops session fixation: `session_regenerate_id()` on every
 * authenticated admin request when the session id is stale.
 */
class SessionFixationGuard
{
    public function handle(Request $request, Closure $next): Response
    {
        if ($request->session()->get('admin') && ! $request->session()->get('_regen')) {
            $request->session()->regenerate();
            $request->session()->put('_regen', true);
        }

        return $next($request);
    }
}
