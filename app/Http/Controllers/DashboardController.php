<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index()
    {
        $stats = [
            'total_sites' => \App\Models\Site::count(),
            'total_forages' => \App\Models\Forage::count(),
            'sites_actifs' => \App\Models\Site::where('statut', 'actif')->count(),
            'sites_en_panne' => \App\Models\Site::where('statut', 'en_panne')->count(),
            'interventions_ce_mois' => \App\Models\Intervention::whereMonth('date_intervention', now()->month)
                ->whereYear('date_intervention', now()->year)
                ->count(),
            'interventions_en_cours' => \App\Models\Intervention::where('statut', 'en_cours')->count(),
        ];

        $recentInterventions = \App\Models\Intervention::with(['site', 'user'])
            ->orderByDesc('date_intervention')
            ->limit(5)
            ->get();

        $sitesEnPanne = \App\Models\Site::with(['commune', 'village'])
            ->where('statut', 'en_panne')
            ->limit(5)
            ->get();

        return view('dashboard.index', compact('stats', 'recentInterventions', 'sitesEnPanne'));
    }
}
