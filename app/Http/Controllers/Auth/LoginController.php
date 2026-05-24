<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use App\Services\AuditService;

class LoginController extends Controller
{
    protected $auditService;

    public function __construct(AuditService $auditService)
    {
        $this->auditService = $auditService;
    }

    /**
     * Afficher le formulaire de connexion
     */
    public function showLoginForm()
    {
        return view('auth.login');
    }

    /**
     * Gérer la tentative de connexion
     */
    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required|string',
        ]);

        // Vérifier si l'utilisateur existe et est actif
        $user = User::where('email', $credentials['email'])->first();

        if (!$user) {
            return back()->withErrors([
                'email' => 'Ces identifiants ne correspondent pas à nos enregistrements.',
            ])->onlyInput('email');
        }

        // Vérifier si le compte est actif
        if (!$user->is_active) {
            return back()->withErrors([
                'email' => 'Votre compte a été désactivé. Contactez l\'administrateur.',
            ])->onlyInput('email');
        }

        // Vérifier le mot de passe
        if (!Hash::check($credentials['password'], $user->password)) {
            // Log tentative échouée
            $this->auditService->log('login_failed', 'User', $user->id, [
                'email' => $credentials['email'],
                'ip' => $request->ip(),
            ]);

            return back()->withErrors([
                'email' => 'Ces identifiants ne correspondent pas à nos enregistrements.',
            ])->onlyInput('email');
        }

        // Connexion réussie
        auth()->login($user);

        // Mettre à jour les informations de dernière connexion
        $user->update([
            'last_login_at' => now(),
            'last_login_ip' => $request->ip(),
        ]);

        // Log connexion réussie
        $this->auditService->log('login_success', 'User', $user->id, [
            'ip' => $request->ip(),
        ]);

        // Régénérer le token de session pour prévenir CSRF
        $request->session()->regenerate();

        // Rediriger vers le dashboard
        return redirect()->intended(route('dashboard'));
    }

    /**
     * Déconnexion de l'utilisateur
     */
    public function logout(Request $request)
    {
        $user = auth()->user();
        
        if ($user) {
            $this->auditService->log('logout', 'User', $user->id);
        }

        auth()->logout();
        
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login');
    }
}
