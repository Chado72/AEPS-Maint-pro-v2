<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Exception;

class AiService
{
    protected $provider;
    protected $apiKey;
    protected $baseUrl;
    protected $model;

    public function __construct()
    {
        // Récupérer le provider depuis la config (mistral ou groq)
        $this->provider = config('ai.default_provider', 'mistral');
        $this->loadCredentials();
    }

    /**
     * Charge les credentials selon le provider
     */
    protected function loadCredentials()
    {
        if ($this->provider === 'mistral') {
            $this->apiKey = config('ai.mistral.api_key');
            $this->baseUrl = config('ai.mistral.base_url', 'https://api.mistral.ai/v1');
            $this->model = config('ai.mistral.model', 'mistral-small');
        } elseif ($this->provider === 'groq') {
            $this->apiKey = config('ai.groq.api_key');
            $this->baseUrl = config('ai.groq.base_url', 'https://api.groq.com/openai/v1');
            $this->model = config('ai.groq.model', 'llama-3.1-70b-versatile');
        } else {
            throw new Exception("Provider IA non configuré: {$this->provider}");
        }
    }

    /**
     * Vérifie si le service IA est activé et configuré
     */
    public function isEnabled(): bool
    {
        return !empty($this->apiKey);
    }

    /**
     * Envoie une conversation à l'IA et retourne la réponse
     *
     * @param array $messages Tableau de messages [{role, content}]
     * @return string Réponse de l'IA
     */
    public function chat(array $messages): string
    {
        if (!$this->isEnabled()) {
            return "Le service IA n'est pas configuré. Veuillez ajouter votre clé API dans les paramètres.";
        }

        try {
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $this->apiKey,
                'Content-Type' => 'application/json',
            ])->post($this->baseUrl . '/chat/completions', [
                'model' => $this->model,
                'messages' => $messages,
                'temperature' => 0.7,
                'max_tokens' => 1024,
            ]);

            if ($response->successful()) {
                $data = $response->json();
                return $data['choices'][0]['message']['content'] ?? 'Aucune réponse générée.';
            }

            Log::error('Erreur API IA', [
                'provider' => $this->provider,
                'status' => $response->status(),
                'body' => $response->body(),
            ]);

            return "Erreur lors de l'appel à l'IA: " . $response->status();

        } catch (Exception $e) {
            Log::error('Exception IA', [
                'provider' => $this->provider,
                'message' => $e->getMessage(),
            ]);

            return "Une erreur est survenue lors de la communication avec l'IA.";
        }
    }

    /**
     * Change dynamiquement le provider
     */
    public function setProvider(string $provider): void
    {
        $this->provider = $provider;
        $this->loadCredentials();
    }

    /**
     * Obtient le provider actuel
     */
    public function getProvider(): string
    {
        return $this->provider;
    }
}
