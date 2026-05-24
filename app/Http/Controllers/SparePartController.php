<?php

namespace App\Http\Controllers;

use App\Models\SparePart;
use Illuminate\Http\Request;
use App\Services\AuditService;
use Illuminate\Support\Facades\DB;

class SparePartController extends Controller
{
    protected $auditService;

    public function __construct(AuditService $auditService)
    {
        $this->auditService = $auditService;
        $this->middleware('auth');
        $this->middleware('permission:manage_stock')->only(['store', 'update', 'destroy']);
    }

    public function index()
    {
        $spareParts = SparePart::orderBy('nom')->paginate(15);
        return view('spare-parts.index', compact('spareParts'));
    }

    public function create()
    {
        return view('spare-parts.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nom' => 'required|string|max:150',
            'reference' => 'nullable|string|max:50|unique:spare_parts,reference',
            'description' => 'nullable|string',
            'categorie' => 'nullable|string|max:50',
            'stock_actuel' => 'required|integer|min:0',
            'stock_minimum' => 'required|integer|min:0',
            'prix_unitaire' => 'nullable|numeric|min:0',
            'fournisseur' => 'nullable|string|max:150',
        ]);

        $sparePart = SparePart::create($validated);
        
        $this->auditService->log('create', 'SparePart', $sparePart->id, ['nom' => $sparePart->nom]);
        
        return redirect()->route('spare-parts.index')
            ->with('success', 'Pièce détachée créée avec succès.');
    }

    public function show(SparePart $sparePart)
    {
        $sparePart->load(['interventions']);
        return view('spare-parts.show', compact('sparePart'));
    }

    public function edit(SparePart $sparePart)
    {
        return view('spare-parts.edit', compact('sparePart'));
    }

    public function update(Request $request, SparePart $sparePart)
    {
        $validated = $request->validate([
            'nom' => 'required|string|max:150',
            'reference' => 'nullable|string|max:50|unique:spare_parts,reference,' . $sparePart->id,
            'description' => 'nullable|string',
            'categorie' => 'nullable|string|max:50',
            'stock_actuel' => 'required|integer|min:0',
            'stock_minimum' => 'required|integer|min:0',
            'prix_unitaire' => 'nullable|numeric|min:0',
            'fournisseur' => 'nullable|string|max:150',
        ]);

        $sparePart->update($validated);
        
        $this->auditService->log('update', 'SparePart', $sparePart->id, ['nom' => $sparePart->nom]);
        
        return redirect()->route('spare-parts.index')
            ->with('success', 'Pièce détachée mise à jour avec succès.');
    }

    public function destroy(SparePart $sparePart)
    {
        if ($sparePart->interventions()->count() > 0) {
            return redirect()->route('spare-parts.index')
                ->with('error', 'Impossible de supprimer cette pièce car elle est liée à des interventions.');
        }
        
        $sparePart->delete();
        
        $this->auditService->log('delete', 'SparePart', $sparePart->id, ['nom' => $sparePart->nom]);
        
        return redirect()->route('spare-parts.index')
            ->with('success', 'Pièce détachée supprimée avec succès.');
    }
}
