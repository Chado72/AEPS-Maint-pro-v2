<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class OwnsAiSession
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (!auth()->check()) {
            return redirect()->route('login');
        }

        $session = $request->route('session');
        
        if ($session && $session instanceof \App\Models\AiSession) {
            // Admin peut accéder à toutes les sessions
            if (auth()->user()->isAdmin()) {
                return $next($request);
            }
            
            // Vérifier que l'utilisateur possède la session
            if ($session->user_id !== auth()->id()) {
                abort(403, 'Accès non autorisé à cette session.');
            }
        }

        return $next($request);
    }
}
