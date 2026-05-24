<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Configuration DomPDF
    |--------------------------------------------------------------------------
    |
    | Configuration pour la génération de rapports PDF avec DomPDF.
    | Optimisé pour le support UTF-8 et les caractères français.
    |
    */

    // Mode d'affichage : 'inline' ou 'download'
    'display' => 'inline',

    // Encodage du document
    'encoding' => 'UTF-8',

    // Police par défaut (dejavusans supporte mieux UTF-8)
    'font' => env('PDF_DEFAULT_FONT', 'dejavusans'),

    // Activer/désactiver le sous-ensemble des polices
    'enable_font_subsetting' => env('PDF_ENABLE_FONTSUBSETTING', true),

    // Dossier temporaire pour DomPDF
    'temp_dir' => storage_path('app/temp'),

    // Chemin vers les polices personnalisées
    'font_dir' => storage_path('fonts/'),

    // Chemin vers le dossier des fonts de DomPDF
    'font_cache' => storage_path('fonts/'),

    // Chemin vers les logs
    'log_channel' => env('LOG_CHANNEL', 'stack'),

    // Options papier par défaut
    'paper' => 'a4',
    'orientation' => 'portrait',

    // Marges par défaut (en points: 1pt = 1/72 pouce)
    'margin_top' => 20,
    'margin_bottom' => 20,
    'margin_left' => 15,
    'margin_right' => 15,

    // Activer le mode debug (affiche les erreurs dans le PDF)
    'debug' => env('APP_DEBUG', false),

    // DPI (dots per inch) pour le rendu
    'dpi' => 96,

    // Support HTTP/HTTPS pour charger les images externes
    'allow_remote' => true,

    // Contexte de flux pour les requêtes HTTP (headers, etc.)
    'http_context' => [],

    // Activer le support CSS3 expérimental
    'enable_css_float' => true,

    // Délai d'exécution maximal (en secondes)
    'dompdf_options' => [
        'isHtml5ParserEnabled' => true,
        'isRemoteEnabled' => true,
        'defaultFont' => env('PDF_DEFAULT_FONT', 'dejavusans'),
    ],

];
