<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Auth;

class CheckUserAccess
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     * @param  string  $menu
     * @param  string|null  $action
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function handle(Request $request, Closure $next, $menu, $action = null): Response
    {
        if (!Auth::check()) {
            return redirect()->route('login');
        }

        $user = Auth::user();

        // Jika user adalah admin, berikan akses
        if ($user->is_admin) {
            return $next($request);
        }

        // Jika action adalah 'index', cek juga apakah user memiliki akses 'monitoring'
        if ($action === 'index') {
            if ($user->hasAccess($menu, 'index') || $user->hasAccess($menu, 'monitoring')) {
                return $next($request);
            }
        } else {
            // Cek apakah user memiliki akses
            if ($user->hasAccess($menu, $action)) {
                return $next($request);
            }
        }

        abort(403, 'Akses ditolak.');
    }
}