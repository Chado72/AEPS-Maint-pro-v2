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
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;

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
        $this->middleware('auth');
    }

    public function index()
    {
        Gate::authorize('viewAny', Intervention::class);
        
        $user = auth()->user();
        
        // Si l'utilisateur n'est pas admin, il ne voit que ses propres interventions
        if (!$user->isAdmin()) {
            $interventions = Intervention::with(['site', 'forage', 'user', 'pieces'])
                ->where('user_id', $user->id)
                ->orderByDesc('date_intervention')
                ->paginate(15);
            
            if (request('statut')) {
                $interventions = Intervention::with(['site', 'forage', 'user', 'pieces'])
                    ->where('user_id', $user->id)
                    ->where('statut', request('statut'))
                    ->orderByDesc('date_intervention')
                    ->paginate(15);
            }
        } else {
            $interventions = Intervention::with(['site', 'forage', 'user', 'pieces'])
                ->orderByDesc('date_intervention')
                ->paginate(15);
            
            if (request('statut')) {
                $interventions = Intervention::with(['site', 'forage', 'user', 'pieces'])
                    ->where('statut', request('statut'))
                    ->orderByDesc('date_intervention')
                    ->paginate(15);
            }
        }
        
        return view('interventions.index', compact('interventions'));
    }

    public function create()
    {
        Gate::authorize('create', Intervention::class);
        
        $sites = Site::orderBy('nom')->get();
        $forages = Forage::orderBy('nom')->get();
        $users = User::orderBy('name')->get();
        $spareParts = SparePart::where('stock_actuel', '>', 0)->orderBy('nom')->get();
        
        return view('interventions.create', compact('sites', 'forages', 'users', 'spareParts'));
    }

    public function store(StoreInterventionRequest $request)
    {
        Gate::authorize('create', Intervention::class);
        
        $validated = $request->validated();
        
        DB::beginTransaction();
        try {
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

            // Gestion des pièces utilisées avec verrouillage pour éviter les race conditions
            if (!empty($validated['pieces_utilisees'])) {
                foreach ($validated['pieces_utilisees'] as $piece) {
                    $sparePart = SparePart::lockForUpdate()->find($piece['spare_part_id']);
                    
                    if (!$sparePart) {
                        throw new \Exception("Pièce non trouvée: {$piece['spare_part_id']}");
                    }
                    
                    if ($sparePart->stock_actuel < $piece['quantite']) {
                        throw new \Exception("Stock insuffisant pour la pièce: {$sparePart->nom}");
                    }
                    
                    $sparePart->decrement('stock_actuel', $piece['quantite']);
                    
                    $intervention->pieces()->attach($piece['spare_part_id'], [
                        'quantite' => $piece['quantite'],
                    ]);
                }
            }
            
            DB::commit();
            
            $this->auditService->log('create', 'Intervention', $intervention->id, [
                'type' => $intervention->type_intervention,
                'site_id' => $intervention->site_id
            ]);
            
            return redirect()->route('interventions.index')
                ->with('success', 'Intervention créée avec succès.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withErrors(['error' => 'Erreur lors de la création: ' . $e->getMessage()]);
        }
    }

    public function show(Intervention $intervention)
    {
        Gate::authorize('view', $intervention);
        
        $intervention->load(['site.commune', 'forage', 'user', 'pieces']);
        return view('interventions.show', compact('intervention'));
    }

    public function edit(Intervention $intervention)
    {
        Gate::authorize('update', $intervention);
        
        $sites = Site::orderBy('nom')->get();
        $forages = Forage::orderBy('nom')->get();
        $users = User::orderBy('name')->get();
        $spareParts = SparePart::orderBy('nom')->get();
        
        return view('interventions.edit', compact('intervention', 'sites', 'forages', 'users', 'spareParts'));
    }

    public function update(Request $request, Intervention $intervention)
    {
        Gate::authorize('update', $intervention);
        
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
        Gate::authorize('delete', $intervention);
        
        DB::beginTransaction();
        try {
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
            DB::commit();
            
            $this->auditService->log('delete', 'Intervention', $intervention->id);
            
            return redirect()->route('interventions.index')
                ->with('success', 'Intervention supprimée avec succès.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withErrors(['error' => 'Erreur lors de la suppression: ' . $e->getMessage()]);
        }
    }

    public function exportPdf(Intervention $intervention)
    {
        Gate::authorize('exportPdf', $intervention);
        
        $data = [
            'intervention' => $intervention->load(['site', 'forage', 'user', 'pieces']),
            'generated_at' => now(),
        ];
        
        return $this->pdfService->generate('pdf.intervention', $data, 'intervention_' . $intervention->id);
    }
}
