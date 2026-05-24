<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Setting;

class SettingController extends Controller
{
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
        $validated = $request->validate([
            'ai_provider' => 'required|in:mistral,groq',
            'ai_mistral_key' => 'nullable|string',
            'ai_groq_key' => 'nullable|string',
        ]);

        // Mettre à jour le fichier .env
        $this->updateEnvFile([
            'AI_DEFAULT_PROVIDER' => $validated['ai_provider'],
            'AI_MISTRAL_API_KEY' => $validated['ai_mistral_key'],
            'AI_GROQ_API_KEY' => $validated['ai_groq_key'],
        ]);

        // Clear config cache
        \Artisan::call('config:clear');

        return redirect()->route('settings.index')
            ->with('success', 'Paramètres mis à jour avec succès. Veuillez rafraîchir la page pour appliquer les changements.');
    }

    /**
     * Met à jour le fichier .env avec les nouvelles valeurs
     */
    protected function updateEnvFile(array $values)
    {
        $envPath = base_path('.env');
        $content = file_get_contents($envPath);

        foreach ($values as $key => $value) {
            if (strpos($content, $key . '=') !== false) {
                // La clé existe, on la met à jour
                $content = preg_replace(
                    "/^{$key}=.*/m",
                    "{$key}={$value}",
                    $content
                );
            } else {
                // La clé n'existe pas, on l'ajoute
                $content .= "\n{$key}={$value}";
            }
        }

        file_put_contents($envPath, $content);
    }
}
