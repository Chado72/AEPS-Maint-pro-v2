<?php

namespace App\Http\Controllers;

use App\Models\Site;
use App\Models\Commune;
use App\Models\Village;
use App\Http\Requests\StoreSiteRequest;
use App\Http\Requests\UpdateSiteRequest;
use App\Services\AuditService;
use App\Services\ReportService;
use App\Services\PdfService;

class SiteController extends Controller
{
    protected $auditService;
    protected $reportService;
    protected $pdfService;

    public function __construct(AuditService $auditService, ReportService $reportService, PdfService $pdfService)
    {
        $this->auditService = $auditService;
        $this->reportService = $reportService;
        $this->pdfService = $pdfService;
    }

    public function index()
    {
        $sites = Site::with(['commune', 'village', 'forages'])
            ->orderBy('nom')
            ->paginate(15);
        
        $statuts = request('statut') ? ['statut' => request('statut')] : [];
        if (request('statut')) {
            $sites = Site::with(['commune', 'village', 'forages'])
                ->where('statut', request('statut'))
                ->orderBy('nom')
                ->paginate(15);
        }
        
        return view('sites.index', compact('sites'));
    }

    public function create()
    {
        $communes = Commune::orderBy('nom')->get();
        $villages = Village::orderBy('nom')->get();
        return view('sites.create', compact('communes', 'villages'));
    }

    public function store(StoreSiteRequest $request)
    {
        $site = Site::create($request->validated());
        
        $this->auditService->log('create', 'Site', $site->id, ['nom' => $site->nom, 'type' => $site->type]);
        
        return redirect()->route('sites.index')
            ->with('success', 'Site créé avec succès.');
    }

    public function show(Site $site)
    {
        $site->load(['commune', 'village', 'forages.energySources', 'interventions.user', 'documents']);
        return view('sites.show', compact('site'));
    }

    public function edit(Site $site)
    {
        $communes = Commune::orderBy('nom')->get();
        $villages = Village::orderBy('nom')->get();
        return view('sites.edit', compact('site', 'communes', 'villages'));
    }

    public function update(UpdateSiteRequest $request, Site $site)
    {
        $site->update($request->validated());
        
        $this->auditService->log('update', 'Site', $site->id, ['nom' => $site->nom]);
        
        return redirect()->route('sites.show', $site)
            ->with('success', 'Site mis à jour avec succès.');
    }

    public function destroy(Site $site)
    {
        if ($site->forages()->count() > 0 || $site->interventions()->count() > 0) {
            return redirect()->route('sites.index')
                ->with('error', 'Impossible de supprimer ce site car il contient des forages ou des interventions.');
        }
        
        $site->delete();
        
        $this->auditService->log('delete', 'Site', $site->id, ['nom' => $site->nom]);
        
        return redirect()->route('sites.index')
            ->with('success', 'Site supprimé avec succès.');
    }

    public function exportPdf(Site $site)
    {
        $data = $this->reportService->getSiteReport($site->id);
        return $this->pdfService->generate('pdf.site', $data, 'site_' . $site->nom);
    }
}
