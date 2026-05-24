<?php

namespace App\Http\Controllers;

use App\Models\Forage;
use App\Models\Site;
use App\Models\EnergySource;
use Illuminate\Http\Request;
use App\Services\AuditService;

class ForageController extends Controller
{
    protected $auditService;

    public function __construct(AuditService $auditService)
    {
        $this->auditService = $auditService;
    }

    public function index()
    {
        $forages = Forage::with(['site.commune', 'energySources'])->orderBy('nom')->paginate(15);
        return view('forages.index', compact('forages'));
    }

    public function create()
    {
        $sites = Site::orderBy('nom')->get();
        return view('forages.create', compact('sites'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nom' => 'required|string|max:100',
            'site_id' => 'required|exists:sites,id',
            'profondeur' => 'nullable|numeric|min:0',
            'debit_theorique' => 'nullable|numeric|min:0',
            'date_installation' => 'nullable|date',
            'statut' => 'required|in:actif,en_panne,en_maintenance,abandonne',
        ]);

        $forage = Forage::create($validated);
        
        $this->auditService->log('create', 'Forage', $forage->id, ['nom' => $forage->nom]);
        
        return redirect()->route('forages.index')
            ->with('success', 'Forage créé avec succès.');
    }

    public function show(Forage $forage)
    {
        $forage->load(['site.commune', 'site.village', 'energySources', 'interventions.user']);
        return view('forages.show', compact('forage'));
    }

    public function edit(Forage $forage)
    {
        $sites = Site::orderBy('nom')->get();
        return view('forages.edit', compact('forage', 'sites'));
    }

    public function update(Request $request, Forage $forage)
    {
        $validated = $request->validate([
            'nom' => 'required|string|max:100',
            'site_id' => 'required|exists:sites,id',
            'profondeur' => 'nullable|numeric|min:0',
            'debit_theorique' => 'nullable|numeric|min:0',
            'date_installation' => 'nullable|date',
            'statut' => 'required|in:actif,en_panne,en_maintenance,abandonne',
        ]);

        $forage->update($validated);
        
        $this->auditService->log('update', 'Forage', $forage->id, ['nom' => $forage->nom]);
        
        return redirect()->route('forages.show', $forage)
            ->with('success', 'Forage mis à jour avec succès.');
    }

    public function destroy(Forage $forage)
    {
        if ($forage->interventions()->count() > 0) {
            return redirect()->route('forages.index')
                ->with('error', 'Impossible de supprimer ce forage car il contient des interventions.');
        }
        
        $forage->delete();
        
        $this->auditService->log('delete', 'Forage', $forage->id, ['nom' => $forage->nom]);
        
        return redirect()->route('forages.index')
            ->with('success', 'Forage supprimé avec succès.');
    }
}
