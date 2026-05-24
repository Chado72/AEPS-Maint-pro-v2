<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Configuration de l'Intelligence Artificielle
    |--------------------------------------------------------------------------
    |
    | Ce fichier configure les providers IA supportés (Mistral, Groq)
    | pour l'assistant intelligent de l'application AEPS-Maint Pro.
    |
    */

    // Provider par défaut : 'mistral' ou 'groq'
    'default_provider' => env('AI_PROVIDER', 'mistral'),

    // Modèle par défaut à utiliser
    'default_model' => env('AI_MODEL', 'mistral-small-latest'),

    // Timeout des requêtes en secondes
    'timeout' => env('AI_TIMEOUT', 30),

    // URL de base pour Mistral AI
    'mistral' => [
        'base_url' => 'https://api.mistral.ai/v1',
        'api_key' => env('MISTRAL_API_KEY'),
        'models' => [
            'small' => 'mistral-small-latest',
            'medium' => 'mistral-medium-latest',
            'large' => 'mistral-large-latest',
        ],
    ],

    // URL de base pour Groq
    'groq' => [
        'base_url' => 'https://api.groq.com/openai/v1',
        'api_key' => env('GROQ_API_KEY'),
        'models' => [
            'llama2_70b' => 'llama2-70b-4096',
            'mixtral_8x7b' => 'mixtral-8x7b-32768',
            'gemma_7b' => 'gemma-7b-it',
        ],
    ],

    // Configuration du système d'IA
    'system_prompt' => "Tu es un assistant expert en maintenance des systèmes d'eau potable (AEPS/PEA) au Burkina Faso. 
Tu aides les techniciens et gestionnaires de ONEA dans la province du Yadéga. 
Tes réponses doivent être techniques, précises et adaptées au contexte local.",

    // Historique des conversations (nombre de messages à garder en contexte)
    'max_context_messages' => 10,

    // Activer/désactiver l'IA globalement
    'enabled' => true,

];
