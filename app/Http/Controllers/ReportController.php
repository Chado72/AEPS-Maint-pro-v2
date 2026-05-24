<?php

namespace App\Http\Controllers;

use App\Services\ReportService;
use App\Services\PdfService;
use App\Models\Commune;
use Carbon\Carbon;
use Illuminate\Http\Request;

class ReportController extends Controller
{
    protected $reportService;
    protected $pdfService;

    public function __construct(ReportService $reportService, PdfService $pdfService)
    {
        $this->reportService = $reportService;
        $this->pdfService = $pdfService;
    }

    public function index()
    {
        $stats = $this->reportService->getDashboardStats();
        $communes = Commune::orderBy('nom')->get();
        
        return view('reports.index', compact('stats', 'communes'));
    }

    public function sitePdf(int $siteId)
    {
        $data = $this->reportService->getSiteReport($siteId);
        return $this->pdfService->generate('pdf.site', $data, 'rapport_site_' . $data['site']->nom);
    }

    public function foragePdf(int $forageId)
    {
        $data = $this->reportService->getForageReport($forageId);
        return $this->pdfService->generate('pdf.forage', $data, 'rapport_forage_' . $data['forage']->nom);
    }

    public function interventionPdf(int $interventionId)
    {
        $intervention = \App\Models\Intervention::with(['site', 'forage', 'user', 'pieces'])->findOrFail($interventionId);
        $data = [
            'intervention' => $intervention,
            'generated_at' => Carbon::now(),
        ];
        return $this->pdfService->generate('pdf.intervention', $data, 'intervention_' . $interventionId);
    }

    public function monthlyPdf(Request $request)
    {
        $validated = $request->validate([
            'month' => 'required|integer|between:1,12',
            'year' => 'required|integer|between:2020,2030',
        ]);

        $data = $this->reportService->getMonthlyReport($validated['month'], $validated['year']);
        $monthName = Carbon::create()->month($validated['month'])->translatedFormat('F');
        
        return $this->pdfService->generate('pdf.monthly', $data, 'rapport_mensuel_' . $monthName . '_' . $validated['year']);
    }

    public function communePdf(int $communeId)
    {
        $data = $this->reportService->getCommuneReport($communeId);
        return $this->pdfService->generate('pdf.commune', $data, 'rapport_commune_' . $data['commune']->nom);
    }

    public function globalPdf()
    {
        $data = $this->reportService->getGlobalReport();
        return $this->pdfService->generate('pdf.global', $data, 'rapport_global_aeps');
    }

    public function interventionsPeriod(Request $request)
    {
        $validated = $request->validate([
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
        ]);

        $startDate = Carbon::parse($validated['start_date']);
        $endDate = Carbon::parse($validated['end_date']);
        
        $data = $this->reportService->getInterventionsReport($startDate, $endDate);
        
        return $this->pdfService->generate('pdf.intervention', $data, 'interventions_periode');
    }
}
