@extends('layouts.app')

@section('title', 'Villages')

@section('content')
<div class="row mb-4">
    <div class="col-12 d-flex justify-content-between align-items-center">
        <div>
            <h2 class="fw-bold text-dark">
                <i class="fas fa-home me-2 text-primary"></i> Villages
            </h2>
            <p class="text-muted mb-0">Gestion des villages de la province</p>
        </div>
        <a href="{{ route('villages.create') }}" class="btn btn-primary">
            <i class="fas fa-plus me-2"></i> Nouveau village
        </a>
    </div>
</div>

<!-- Filtres -->
<div class="card card-shadow mb-4">
    <div class="card-body">
        <form method="GET" action="{{ route('villages.index') }}" class="row g-3">
            <div class="col-md-4">
                <input type="text" name="search" class="form-control" 
                       placeholder="Rechercher un village..." 
                       value="{{ request('search') }}">
            </div>
            <div class="col-md-3">
                <select name="commune_id" class="form-select">
                    <option value="">Toutes les communes</option>
                    @foreach($communes as $commune)
                        <option value="{{ $commune->id }}" {{ request('commune_id') == $commune->id ? 'selected' : '' }}>
                            {{ $commune->nom }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-3">
                <button type="submit" class="btn btn-outline-primary w-100">
                    <i class="fas fa-filter me-2"></i> Filtrer
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Tableau -->
<div class="card card-shadow">
    <div class="card-header bg-white py-3">
        <h5 class="mb-0 fw-bold text-dark">Liste des villages</h5>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead class="bg-light">
                    <tr>
                        <th>Nom du village</th>
                        <th>Commune</th>
                        <th class="text-center">Sites AEPS/PEA</th>
                        <th class="text-center">Population</th>
                        <th class="text-center">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($villages as $village)
                        <tr>
                            <td><strong>{{ $village->nom }}</strong></td>
                            <td>
                                <span class="badge bg-info">{{ $village->commune->nom ?? 'N/A' }}</span>
                            </td>
                            <td class="text-center">
                                <span class="badge bg-success">{{ $village->sites_count ?? 0 }}</span>
                            </td>
                            <td class="text-center">
                                {{ number_format($village->population ?? 0, 0, ',', ' ') }}
                            </td>
                            <td class="text-center">
                                <div class="btn-group btn-group-sm">
                                    <a href="{{ route('villages.edit', $village->id) }}" 
                                       class="btn btn-outline-primary" title="Modifier">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <form action="{{ route('villages.destroy', $village->id) }}" 
                                          method="POST" class="d-inline"
                                          onsubmit="return confirm('Supprimer ce village ?')">
                                        @csrf @method('DELETE')
                                        <button type="submit" class="btn btn-outline-danger" title="Supprimer">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="text-center py-5 text-muted">
                                <i class="fas fa-inbox fa-3x mb-3"></i>
                                <p>Aucun village trouvé</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    @if($villages->hasPages())
        <div class="card-footer bg-white py-3">
            {{ $villages->links() }}
        </div>
    @endif
</div>
@endsection
