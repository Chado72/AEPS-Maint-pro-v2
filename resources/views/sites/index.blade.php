@extends('layouts.app')

@section('title', 'Sites AEPS/PEA')

@section('content')
<div class="row mb-4">
    <div class="col-12 d-flex justify-content-between align-items-center">
        <div>
            <h2 class="fw-bold text-dark">
                <i class="fas fa-map-marker-alt me-2 text-primary"></i> Sites AEPS/PEA
            </h2>
            <p class="text-muted mb-0">Gestion des sites d'approvisionnement en eau</p>
        </div>
        <a href="{{ route('sites.create') }}" class="btn btn-primary">
            <i class="fas fa-plus me-2"></i> Nouveau site
        </a>
    </div>
</div>

<!-- Filtres avancés -->
<div class="card card-shadow mb-4">
    <div class="card-body">
        <form method="GET" action="{{ route('sites.index') }}" class="row g-3">
            <div class="col-md-3">
                <input type="text" name="search" class="form-control" 
                       placeholder="Rechercher un site..." 
                       value="{{ request('search') }}">
            </div>
            <div class="col-md-2">
                <select name="commune_id" class="form-select">
                    <option value="">Toutes communes</option>
                    @foreach($communes as $commune)
                        <option value="{{ $commune->id }}" {{ request('commune_id') == $commune->id ? 'selected' : '' }}>
                            {{ $commune->nom }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-2">
                <select name="statut" class="form-select">
                    <option value="">Tous statuts</option>
                    <option value="actif" {{ request('statut') == 'actif' ? 'selected' : '' }}>Actif</option>
                    <option value="en_panne" {{ request('statut') == 'en_panne' ? 'selected' : '' }}>En panne</option>
                    <option value="abandonne" {{ request('statut') == 'abandonne' ? 'selected' : '' }}>Abandonné</option>
                </select>
            </div>
            <div class="col-md-2">
                <select name="type" class="form-select">
                    <option value="">Tous types</option>
                    <option value="AEPS" {{ request('type') == 'AEPS' ? 'selected' : '' }}>AEPS</option>
                    <option value="PEA" {{ request('type') == 'PEA' ? 'selected' : '' }}>PEA</option>
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

<!-- Liste des sites -->
<div class="card card-shadow">
    <div class="card-header bg-white py-3">
        <div class="row align-items-center">
            <div class="col">
                <h5 class="mb-0 fw-bold text-dark">Liste des sites</h5>
            </div>
            <div class="col-auto">
                <span class="badge bg-primary">{{ $sites->total() }} sites</span>
            </div>
        </div>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead class="bg-light">
                    <tr>
                        <th>Nom du site</th>
                        <th>Village / Commune</th>
                        <th class="text-center">Type</th>
                        <th class="text-center">Forages</th>
                        <th class="text-center">Statut</th>
                        <th class="text-center">Dernière intervention</th>
                        <th class="text-center">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($sites as $site)
                        <tr>
                            <td>
                                <strong>{{ $site->nom }}</strong>
                                @if($site->code)
                                    <br><small class="text-muted">{{ $site->code }}</small>
                                @endif
                            </td>
                            <td>
                                <div>{{ $site->village->nom ?? 'N/A' }}</div>
                                <small class="text-muted">{{ $site->village->commune->nom ?? '' }}</small>
                            </td>
                            <td class="text-center">
                                <span class="badge bg-{{ $site->type === 'AEPS' ? 'success' : 'info' }}">
                                    {{ $site->type }}
                                </span>
                            </td>
                            <td class="text-center">
                                <span class="badge bg-secondary">{{ $site->forages_count ?? 0 }}</span>
                            </td>
                            <td class="text-center">
                                @php
                                    $badgeClass = match($site->statut) {
                                        'actif' => 'success',
                                        'en_panne' => 'danger',
                                        'abandonne' => 'secondary',
                                        default => 'warning'
                                    };
                                @endphp
                                <span class="badge bg-{{ $badgeClass }}">
                                    {{ ucfirst(str_replace('_', ' ', $site->statut)) }}
                                </span>
                            </td>
                            <td class="text-center">
                                @if($site->derniere_intervention)
                                    <small>{{ $site->derniere_intervention->date_intervention->format('d/m/Y') }}</small>
                                @else
                                    <span class="text-muted">-</span>
                                @endif
                            </td>
                            <td class="text-center">
                                <div class="btn-group btn-group-sm">
                                    <a href="{{ route('sites.show', $site->id) }}" 
                                       class="btn btn-outline-info" title="Voir">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    <a href="{{ route('sites.edit', $site->id) }}" 
                                       class="btn btn-outline-primary" title="Modifier">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <form action="{{ route('sites.destroy', $site->id) }}" 
                                          method="POST" class="d-inline"
                                          onsubmit="return confirm('Supprimer ce site ?')">
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
                            <td colspan="7" class="text-center py-5">
                                <i class="fas fa-inbox fa-3x text-muted mb-3"></i>
                                <p class="text-muted">Aucun site trouvé</p>
                                <a href="{{ route('sites.create') }}" class="btn btn-sm btn-primary">
                                    <i class="fas fa-plus me-2"></i>Créer le premier site
                                </a>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    @if($sites->hasPages())
        <div class="card-footer bg-white py-3">
            {{ $sites->links() }}
        </div>
    @endif
</div>
@endsection
