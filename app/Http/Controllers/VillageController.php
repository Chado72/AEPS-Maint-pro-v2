<?php

namespace App\Http\Controllers;

use App\Models\Village;
use App\Models\Commune;
use Illuminate\Http\Request;
use App\Services\AuditService;

class VillageController extends Controller
{
    protected $auditService;

    public function __construct(AuditService $auditService)
    {
        $this->auditService = $auditService;
    }

    public function index()
    {
        $villages = Village::with(['commune', 'sites'])->orderBy('nom')->paginate(15);
        return view('villages.index', compact('villages'));
    }

    public function create()
    {
        $communes = Commune::orderBy('nom')->get();
        return view('villages.create', compact('communes'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nom' => 'required|string|max:100',
            'commune_id' => 'required|exists:communes,id',
        ]);

        $village = Village::create($validated);
        
        $this->auditService->log('create', 'Village', $village->id, ['nom' => $village->nom]);
        
        return redirect()->route('villages.index')
            ->with('success', 'Village créé avec succès.');
    }

    public function show(Village $village)
    {
        $village->load(['commune', 'sites.forages']);
        return view('villages.show', compact('village'));
    }

    public function edit(Village $village)
    {
        $communes = Commune::orderBy('nom')->get();
        return view('villages.edit', compact('village', 'communes'));
    }

    public function update(Request $request, Village $village)
    {
        $validated = $request->validate([
            'nom' => 'required|string|max:100',
            'commune_id' => 'required|exists:communes,id',
        ]);

        $village->update($validated);
        
        $this->auditService->log('update', 'Village', $village->id, ['nom' => $village->nom]);
        
        return redirect()->route('villages.index')
            ->with('success', 'Village mis à jour avec succès.');
    }

    public function destroy(Village $village)
    {
        if ($village->sites()->count() > 0) {
            return redirect()->route('villages.index')
                ->with('error', 'Impossible de supprimer ce village car il contient des sites.');
        }
        
        $village->delete();
        
        $this->auditService->log('delete', 'Village', $village->id, ['nom' => $village->nom]);
        
        return redirect()->route('villages.index')
            ->with('success', 'Village supprimé avec succès.');
    }
}
