<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Backs the `admin` guard: keeps the legacy session-based admin session and
 * blocks non-authenticated users. Mirrors legacy admin/includes/guard.php.
 */
class AdminAuth
{
    public function handle(Request $request, Closure $next): Response
    {
        if (! $request->session()->get('admin')) {
            return redirect()->route('admin.login');
        }

        return $next($request);
    }
}
