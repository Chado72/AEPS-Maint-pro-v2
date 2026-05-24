<?php

namespace App\Services;

use App\Models\Site;
use App\Models\Forage;
use App\Models\Intervention;
use App\Models\Commune;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class ReportService
{
    /**
     * Statistiques globales pour le dashboard
     */
    public function getDashboardStats()
    {
        return [
            'total_sites' => Site::count(),
            'total_forages' => Forage::count(),
            'sites_actifs' => Site::where('statut', 'actif')->count(),
            'sites_en_panne' => Site::where('statut', 'en_panne')->count(),
            'interventions_ce_mois' => Intervention::whereMonth('date_intervention', Carbon::now()->month)
                ->whereYear('date_intervention', Carbon::now()->year)
                ->count(),
            'interventions_en_cours' => Intervention::where('statut', 'en_cours')->count(),
            'communes_count' => Commune::count(),
        ];
    }

    /**
     * Rapport par site avec tous les détails
     */
    public function getSiteReport(int $siteId)
    {
        $site = Site::with([
            'commune',
            'village',
            'forages.energySources',
            'interventions.user',
            'documents'
        ])->findOrFail($siteId);

        return [
            'site' => $site,
            'generated_at' => Carbon::now(),
        ];
    }

    /**
     * Rapport par forage
     */
    public function getForageReport(int $forageId)
    {
        $forage = Forage::with([
            'site.commune',
            'site.village',
            'energySources',
            'interventions.user'
        ])->findOrFail($forageId);

        return [
            'forage' => $forage,
            'generated_at' => Carbon::now(),
        ];
    }

    /**
     * Rapport des interventions sur une période
     */
    public function getInterventionsReport(?Carbon $startDate = null, ?Carbon $endDate = null)
    {
        $query = Intervention::with(['site', 'user', 'pieces']);

        if ($startDate) {
            $query->whereDate('date_intervention', '>=', $startDate);
        }
        if ($endDate) {
            $query->whereDate('date_intervention', '<=', $endDate);
        }

        $interventions = $query->orderByDesc('date_intervention')->get();

        return [
            'interventions' => $interventions,
            'start_date' => $startDate ?? Carbon::now()->subMonth(),
            'end_date' => $endDate ?? Carbon::now(),
            'generated_at' => Carbon::now(),
        ];
    }

    /**
     * Rapport mensuel synthétique
     */
    public function getMonthlyReport(int $month, int $year)
    {
        $startDate = Carbon::createFromDate($year, $month, 1);
        $endDate = $startDate->copy()->endOfMonth();

        $interventions = Intervention::with(['site', 'user'])
            ->whereBetween('date_intervention', [$startDate, $endDate])
            ->get();

        $stats = [
            'total_interventions' => $interventions->count(),
            'interventions_terminees' => $interventions->where('statut', 'terminee')->count(),
            'interventions_en_cours' => $interventions->where('statut', 'en_cours')->count(),
            'cout_total' => $interventions->sum('cout'),
            'sites_touches' => $interventions->unique('site_id')->count(),
        ];

        return [
            'month' => $month,
            'year' => $year,
            'interventions' => $interventions,
            'stats' => $stats,
            'generated_at' => Carbon::now(),
        ];
    }

    /**
     * Rapport par commune
     */
    public function getCommuneReport(int $communeId)
    {
        $commune = Commune::with([
            'villages.sites.forages.energySources',
            'villages.sites.interventions'
        ])->findOrFail($communeId);

        $totalSites = $commune->villages->sum(function($village) {
            return $village->sites->count();
        });

        $totalInterventions = $commune->villages->sum(function($village) {
            return $village->sites->sum(function($site) {
                return $site->interventions->count();
            });
        });

        return [
            'commune' => $commune,
            'total_sites' => $totalSites,
            'total_interventions' => $totalInterventions,
            'generated_at' => Carbon::now(),
        ];
    }

    /**
     * Rapport global complet
     */
    public function getGlobalReport()
    {
        $stats = $this->getDashboardStats();
        
        $recentInterventions = Intervention::with(['site', 'user'])
            ->orderByDesc('date_intervention')
            ->limit(50)
            ->get();

        $sitesEnPanne = Site::with(['commune', 'village'])
            ->where('statut', 'en_panne')
            ->get();

        return [
            'stats' => $stats,
            'recent_interventions' => $recentInterventions,
            'sites_en_panne' => $sitesEnPanne,
            'generated_at' => Carbon::now(),
        ];
    }
}
