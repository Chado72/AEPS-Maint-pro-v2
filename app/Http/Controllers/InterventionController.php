<?php

namespace App\Http\Controllers;

use App\Models\Intervention;
use App\Models\Site;
use App\Models\Forage;
use App\Models\SparePart;
use App\Models\User;
use App\Http\Requests\StoreInterventionRequest;
use Illuminate\Http\Request;
use App\Services\AuditService;
use App\Services\ReportService;
use App\Services\PdfService;

class InterventionController extends Controller
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
        $interventions = Intervention::with(['site', 'forage', 'user', 'pieces'])
            ->orderByDesc('date_intervention')
            ->paginate(15);
        
        if (request('statut')) {
            $interventions = Intervention::with(['site', 'forage', 'user', 'pieces'])
                ->where('statut', request('statut'))
                ->orderByDesc('date_intervention')
                ->paginate(15);
        }
        
        return view('interventions.index', compact('interventions'));
    }

    public function create()
    {
        $sites = Site::orderBy('nom')->get();
        $forages = Forage::orderBy('nom')->get();
        $users = User::orderBy('name')->get();
        $spareParts = SparePart::where('stock_actuel', '>', 0)->orderBy('nom')->get();
        
        return view('interventions.create', compact('sites', 'forages', 'users', 'spareParts'));
    }

    public function store(StoreInterventionRequest $request)
    {
        $validated = $request->validated();
        
        // Création de l'intervention
        $intervention = Intervention::create([
            'site_id' => $validated['site_id'],
            'forage_id' => $validated['forage_id'] ?? null,
            'user_id' => auth()->id(),
            'type_intervention' => $validated['type_intervention'],
            'statut' => $validated['statut'],
            'date_intervention' => $validated['date_intervention'],
            'description' => $validated['description'],
            'cout' => $validated['cout'] ?? 0,
            'duree_heures' => $validated['duree_heures'] ?? null,
        ]);

        // Gestion des pièces utilisées
        if (!empty($validated['pieces_utilisees'])) {
            foreach ($validated['pieces_utilisees'] as $piece) {
                $sparePart = SparePart::find($piece['spare_part_id']);
                if ($sparePart) {
                    $sparePart->decrement('stock_actuel', $piece['quantite']);
                    
                    $intervention->pieces()->attach($piece['spare_part_id'], [
                        'quantite' => $piece['quantite'],
                    ]);
                }
            }
        }
        
        $this->auditService->log('create', 'Intervention', $intervention->id, [
            'type' => $intervention->type_intervention,
            'site_id' => $intervention->site_id
        ]);
        
        return redirect()->route('interventions.index')
            ->with('success', 'Intervention créée avec succès.');
    }

    public function show(Intervention $intervention)
    {
        $intervention->load(['site.commune', 'forage', 'user', 'pieces']);
        return view('interventions.show', compact('intervention'));
    }

    public function edit(Intervention $intervention)
    {
        $sites = Site::orderBy('nom')->get();
        $forages = Forage::orderBy('nom')->get();
        $users = User::orderBy('name')->get();
        $spareParts = SparePart::orderBy('nom')->get();
        
        return view('interventions.edit', compact('intervention', 'sites', 'forages', 'users', 'spareParts'));
    }

    public function update(Request $request, Intervention $intervention)
    {
        $validated = $request->validate([
            'site_id' => 'required|exists:sites,id',
            'forage_id' => 'nullable|exists:forages,id',
            'type_intervention' => 'required|in:preventive,corrective,urgence,installation,rehabilitation',
            'statut' => 'required|in:planifiee,en_cours,terminee,annulee',
            'date_intervention' => 'required|date',
            'description' => 'required|string',
            'cout' => 'nullable|numeric|min:0',
            'duree_heures' => 'nullable|numeric|min:0',
        ]);

        $intervention->update($validated);
        
        $this->auditService->log('update', 'Intervention', $intervention->id, ['statut' => $intervention->statut]);
        
        return redirect()->route('interventions.show', $intervention)
            ->with('success', 'Intervention mise à jour avec succès.');
    }

    public function destroy(Intervention $intervention)
    {
        // Restaurer le stock des pièces si nécessaire
        foreach ($intervention->pieces as $piece) {
            $pivot = $piece->pivot;
            if ($pivot && isset($pivot->quantite)) {
                $sparePart = SparePart::find($piece->id);
                if ($sparePart) {
                    $sparePart->increment('stock_actuel', $pivot->quantite);
                }
            }
        }
        
        $intervention->delete();
        
        $this->auditService->log('delete', 'Intervention', $intervention->id);
        
        return redirect()->route('interventions.index')
            ->with('success', 'Intervention supprimée avec succès.');
    }

    public function exportPdf(Intervention $intervention)
    {
        $data = [
            'intervention' => $intervention->load(['site', 'forage', 'user', 'pieces']),
            'generated_at' => now(),
        ];
        
        return $this->pdfService->generate('pdf.intervention', $data, 'intervention_' . $intervention->id);
    }
}
