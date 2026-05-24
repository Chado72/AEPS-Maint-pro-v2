<?php

namespace App\Http\Controllers;

use App\Models\Commune;
use App\Http\Requests\StoreCommuneRequest;
use App\Http\Requests\UpdateCommuneRequest;
use App\Services\AuditService;

class CommuneController extends Controller
{
    protected $auditService;

    public function __construct(AuditService $auditService)
    {
        $this->auditService = $auditService;
    }

    public function index()
    {
        $communes = Commune::withCount('villages')->orderBy('nom')->paginate(15);
        return view('communes.index', compact('communes'));
    }

    public function create()
    {
        return view('communes.create');
    }

    public function store(StoreCommuneRequest $request)
    {
        $commune = Commune::create($request->validated());
        
        $this->auditService->log('create', 'Commune', $commune->id, ['nom' => $commune->nom]);
        
        return redirect()->route('communes.index')
            ->with('success', 'Commune créée avec succès.');
    }

    public function show(Commune $commune)
    {
        $commune->load(['villages.sites']);
        return view('communes.show', compact('commune'));
    }

    public function edit(Commune $commune)
    {
        return view('communes.edit', compact('commune'));
    }

    public function update(UpdateCommuneRequest $request, Commune $commune)
    {
        $commune->update($request->validated());
        
        $this->auditService->log('update', 'Commune', $commune->id, ['nom' => $commune->nom]);
        
        return redirect()->route('communes.index')
            ->with('success', 'Commune mise à jour avec succès.');
    }

    public function destroy(Commune $commune)
    {
        if ($commune->villages()->count() > 0) {
            return redirect()->route('communes.index')
                ->with('error', 'Impossible de supprimer cette commune car elle contient des villages.');
        }
        
        $commune->delete();
        
        $this->auditService->log('delete', 'Commune', $commune->id, ['nom' => $commune->nom]);
        
        return redirect()->route('communes.index')
            ->with('success', 'Commune supprimée avec succès.');
    }
}
