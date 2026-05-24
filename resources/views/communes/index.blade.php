@extends('layouts.app')

@section('title', 'Communes')

@section('content')
<div class="row mb-4">
    <div class="col-12 d-flex justify-content-between align-items-center">
        <div>
            <h2 class="fw-bold text-dark">
                <i class="fas fa-city me-2 text-primary"></i> Communes
            </h2>
            <p class="text-muted mb-0">Gestion des communes de la province du Yadéga</p>
        </div>
        <a href="{{ route('communes.create') }}" class="btn btn-primary">
            <i class="fas fa-plus me-2"></i> Nouvelle commune
        </a>
    </div>
</div>

<!-- Filtres et Recherche -->
<div class="card card-shadow mb-4">
    <div class="card-body">
        <form method="GET" action="{{ route('communes.index') }}" class="row g-3">
            <div class="col-md-6">
                <input type="text" name="search" class="form-control" 
                       placeholder="Rechercher une commune..." 
                       value="{{ request('search') }}">
            </div>
            <div class="col-md-4">
                <select name="sort" class="form-select">
                    <option value="nom_asc" {{ request('sort') == 'nom_asc' ? 'selected' : '' }}>Nom (A-Z)</option>
                    <option value="nom_desc" {{ request('sort') == 'nom_desc' ? 'selected' : '' }}>Nom (Z-A)</option>
                    <option value="villages_desc" {{ request('sort') == 'villages_desc' ? 'selected' : '' }}>Plus de villages</option>
                </select>
            </div>
            <div class="col-md-2">
                <button type="submit" class="btn btn-outline-primary w-100">
                    <i class="fas fa-search"></i>
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Tableau des communes -->
<div class="card card-shadow">
    <div class="card-header bg-white py-3">
        <div class="row align-items-center">
            <div class="col">
                <h5 class="mb-0 fw-bold text-dark">Liste des communes</h5>
            </div>
            <div class="col-auto">
                <span class="badge bg-primary">{{ $communes->total() }} communes</span>
            </div>
        </div>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead class="bg-light">
                    <tr>
                        <th>Nom</th>
                        <th class="text-center">Code</th>
                        <th class="text-center">Villages</th>
                        <th class="text-center">Sites AEPS/PEA</th>
                        <th class="text-center">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($communes as $commune)
                        <tr>
                            <td>
                                <strong>{{ $commune->nom }}</strong>
                            </td>
                            <td class="text-center">
                                <span class="badge bg-secondary">{{ $commune->code ?? 'N/A' }}</span>
                            </td>
                            <td class="text-center">
                                <span class="badge bg-info">{{ $commune->villages_count ?? 0 }}</span>
                            </td>
                            <td class="text-center">
                                <span class="badge bg-success">{{ $commune->sites_count ?? 0 }}</span>
                            </td>
                            <td class="text-center">
                                <div class="btn-group btn-group-sm" role="group">
                                    <a href="{{ route('communes.show', $commune->id) }}" 
                                       class="btn btn-outline-info" 
                                       title="Voir">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    <a href="{{ route('communes.edit', $commune->id) }}" 
                                       class="btn btn-outline-primary" 
                                       title="Modifier">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <form action="{{ route('communes.destroy', $commune->id) }}" 
                                          method="POST" 
                                          class="d-inline"
                                          onsubmit="return confirm('Êtes-vous sûr de vouloir supprimer cette commune ? Cette action est irréversible.')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-outline-danger" title="Supprimer">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="text-center py-5">
                                <i class="fas fa-inbox fa-3x text-muted mb-3"></i>
                                <p class="text-muted">Aucune commune trouvée</p>
                                <a href="{{ route('communes.create') }}" class="btn btn-sm btn-primary">
                                    <i class="fas fa-plus me-2"></i>Créer la première commune
                                </a>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    @if($communes->hasPages())
        <div class="card-footer bg-white py-3">
            <div class="d-flex justify-content-center">
                {{ $communes->links() }}
            </div>
        </div>
    @endif
</div>
@endsection
