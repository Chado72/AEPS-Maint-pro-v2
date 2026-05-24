<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckPermission
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, string $permission): Response
    {
        if (!auth()->check()) {
            return redirect()->route('login');
        }

        $user = auth()->user();
        
        // Admin a accès à tout
        if ($user->isAdmin()) {
            return $next($request);
        }

        // Vérifier si l'utilisateur a la permission via son rôle
        if (!$user->role || !$user->role->hasPermission($permission)) {
            abort(403, 'Accès non autorisé. Permission requise : ' . $permission);
        }

        return $next($request);
    }
}
