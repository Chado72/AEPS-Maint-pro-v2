<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Setting;
use App\Services\AuditService;

class SettingController extends Controller
{
    protected $auditService;

    public function __construct(AuditService $auditService)
    {
        $this->auditService = $auditService;
        $this->middleware('auth');
        $this->middleware('role:admin')->only(['update']);
    }

    public function index()
    {
        $settings = [
            'ai_provider' => config('ai.default_provider', 'mistral'),
            'ai_mistral_key' => config('ai.mistral.api_key', ''),
            'ai_groq_key' => config('ai.groq.api_key', ''),
        ];
        
        return view('settings.index', compact('settings'));
    }

    public function update(Request $request)
    {
        // Vérification supplémentaire que seul l'admin peut modifier
        if (!auth()->user()->isAdmin()) {
            abort(403, 'Seul un administrateur peut modifier les paramètres.');
        }

        $validated = $request->validate([
            'ai_provider' => 'required|in:mistral,groq',
            'ai_mistral_key' => 'nullable|string|max:255',
            'ai_groq_key' => 'nullable|string|max:255',
        ]);

        // Mettre à jour la configuration en mémoire (sans modifier .env directement)
        // Les clés API doivent être gérées via variables d'environnement ou base de données chiffrée
        config([
            'ai.default_provider' => $validated['ai_provider'],
            'ai.mistral.api_key' => $validated['ai_mistral_key'],
            'ai.groq.api_key' => $validated['ai_groq_key'],
        ]);

        // Log la modification
        $this->auditService->log('update', 'Settings', null, [
            'ai_provider' => $validated['ai_provider'],
            'user_id' => auth()->id(),
        ]);

        return redirect()->route('settings.index')
            ->with('success', 'Paramètres mis à jour avec succès pour cette session. Pour une persistance permanente, configurez les variables d\'environnement.');
    }
}
