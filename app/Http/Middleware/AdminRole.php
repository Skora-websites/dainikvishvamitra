<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Role gate for admin routes: `admin.role:admin` etc.
 * The authenticated payload is an array (legacy row shape), so read `role` directly.
 */
class AdminRole
{
    public function handle(Request $request, Closure $next, string ...$roles): Response
    {
        $user = $request->session()->get('admin');

        if (! $user) {
            return redirect()->route('admin.login');
        }

        $role = is_array($user) ? ($user['role'] ?? '') : '';

        if (! empty($roles) && ! in_array($role, $roles, true)) {
            abort(403, 'आपको इस पृष्ठ तक पहुँच की अनुमति नहीं है।');
        }

        return $next($request);
    }
}
