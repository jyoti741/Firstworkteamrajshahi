<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureUserIsAdmin
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if (! $user) {
            return redirect()->route('login');
        }

        if (! $user->is_active) {
            auth()->guard('web')->logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();

            return redirect()->route('login')->withErrors(['email' => 'Your account has been deactivated. Please contact the owner.']);
        }

        if (! $user->isAdmin()) {
            if ($request->expectsJson()) {
                abort(403, 'Unauthorized. Admin access only.');
            }

            return redirect()->route('seller.quick-sell')->with('error', 'Access Denied: You do not have permission to access the Admin area.');
        }

        return $next($request);
    }
}
