<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckRole
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, string $role): Response
    {
        if (!auth()->check()) {
            return redirect()->route('login');
        }

        $user = auth()->user();
        
        // Admin a accès à tout
        if ($user->isAdmin()) {
            return $next($request);
        }

        if (!$user->hasRole($role)) {
            abort(403, 'Accès non autorisé. Rôle requis : ' . $role);
        }

        return $next($request);
    }
}
