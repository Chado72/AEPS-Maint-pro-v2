@extends('layouts.app')

@section('title', $site->nom)

@section('content')
<div class="row mb-4">
    <div class="col-12 d-flex justify-content-between align-items-center">
        <div>
            <nav aria-label="breadcrumb" class="mb-2">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('sites.index') }}">Sites</a></li>
                    <li class="breadcrumb-item active">{{ $site->nom }}</li>
                </ol>
            </nav>
            <h2 class="fw-bold text-dark">
                <i class="fas fa-map-marker-alt me-2 text-primary"></i> {{ $site->nom }}
            </h2>
            @if($site->code)
                <p class="text-muted mb-0"><strong>Code:</strong> {{ $site->code }}</p>
            @endif
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route('sites.edit', $site->id) }}" class="btn btn-primary">
                <i class="fas fa-edit me-2"></i> Modifier
            </a>
            <a href="{{ route('reports.site', $site->id) }}" target="_blank" class="btn btn-outline-danger">
                <i class="fas fa-file-pdf me-2"></i> PDF
            </a>
        </div>
    </div>
</div>

<!-- Statut et Infos principales -->
<div class="row g-4 mb-4">
    <div class="col-md-4">
        <div class="card card-shadow h-100">
            <div class="card-body text-center">
                <h6 class="text-muted text-uppercase small">Statut actuel</h6>
                @php
                    $badgeClass = match($site->statut) {
                        'actif' => 'success',
                        'en_panne' => 'danger',
                        'abandonne' => 'secondary',
                        default => 'warning'
                    };
                @endphp
                <span class="badge bg-{{ $badgeClass }} fs-6 p-3 my-2">
                    {{ ucfirst(str_replace('_', ' ', $site->statut)) }}
                </span>
                <hr>
                <div class="text-start small">
                    <p><strong>Type:</strong> {{ $site->type }}</p>
                    <p><strong>Village:</strong> {{ $site->village->nom ?? 'N/A' }}</p>
                    <p><strong>Commune:</strong> {{ $site->village->commune->nom ?? 'N/A' }}</p>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-md-4">
        <div class="card card-shadow h-100">
            <div class="card-body">
                <h6 class="text-muted text-uppercase small mb-3">Coordonnées GPS</h6>
                @if($site->latitude && $site->longitude)
                    <div class="alert alert-info small">
                        <i class="fas fa-map-marked-alt me-2"></i>
                        <strong>Latitude:</strong> {{ $site->latitude }}<br>
                        <strong>Longitude:</strong> {{ $site->longitude }}
                    </div>
                    <a href="https://www.google.com/maps?q={{ $site->latitude }},{{ $site->longitude }}" 
                       target="_blank" class="btn btn-sm btn-outline-primary w-100">
                        <i class="fas fa-directions me-2"></i> Ouvrir dans Google Maps
                    </a>
                @else
                    <p class="text-muted small text-center">Coordonnées non renseignées</p>
                @endif
            </div>
        </div>
    </div>
    
    <div class="col-md-4">
        <div class="card card-shadow h-100">
            <div class="card-body">
                <h6 class="text-muted text-uppercase small mb-3">Dernière intervention</h6>
                @if($site->derniere_intervention)
                    <div class="bg-light rounded p-3">
                        <p class="mb-1"><strong>Date:</strong> {{ $site->derniere_intervention->date_intervention->format('d/m/Y') }}</p>
                        <p class="mb-1"><strong>Type:</strong> {{ $site->derniere_intervention->type_intervention }}</p>
                        <p class="mb-0"><strong>Statut:</strong> 
                            <span class="badge bg-{{ $site->derniere_intervention->statut === 'terminee' ? 'success' : 'warning' }}">
                                {{ ucfirst($site->derniere_intervention->statut) }}
                            </span>
                        </p>
                    </div>
                @else
                    <p class="text-muted small text-center">Aucune intervention enregistrée</p>
                @endif
            </div>
        </div>
    </div>
</div>

<!-- Onglets -->
<ul class="nav nav-tabs mb-3" id="siteTabs" role="tablist">
    <li class="nav-item">
        <button class="nav-link active" data-bs-toggle="tab" data-bs-target="#forages">
            <i class="fas fa-bullseye me-2"></i> Forages ({{ $site->forages->count() }})
        </button>
    </li>
    <li class="nav-item">
        <button class="nav-link" data-bs-toggle="tab" data-bs-target="#energie">
            <i class="fas fa-solar-panel me-2"></i> Énergie
        </button>
    </li>
    <li class="nav-item">
        <button class="nav-link" data-bs-toggle="tab" data-bs-target="#historique">
            <i class="fas fa-history me-2"></i> Historique interventions
        </button>
    </li>
    <li class="nav-item">
        <button class="nav-link" data-bs-toggle="tab" data-bs-target="#description">
            <i class="fas fa-align-left me-2"></i> Description
        </button>
    </li>
</ul>

<div class="tab-content" id="siteTabsContent">
    <!-- Forages -->
    <div class="tab-pane fade show active" id="forages">
        <div class="card card-shadow">
            <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
                <h5 class="mb-0 fw-bold">Forages du site</h5>
                <a href="{{ route('forages.create', ['site_id' => $site->id]) }}" class="btn btn-sm btn-primary">
                    <i class="fas fa-plus me-2"></i> Ajouter un forage
                </a>
            </div>
            <div class="card-body p-0">
                @if($site->forages->count() > 0)
                    <table class="table table-hover mb-0">
                        <thead class="bg-light">
                            <tr>
                                <th>Nom/Code</th>
                                <th>Profondeur</th>
                                <th>Débit</th>
                                <th>Statut</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($site->forages as $forage)
                                <tr>
                                    <td>{{ $forage->nom }}</td>
                                    <td>{{ $forage->profondeur }} m</td>
                                    <td>{{ $forage->debit }} m³/h</td>
                                    <td>
                                        <span class="badge bg-{{ $forage->statut === 'actif' ? 'success' : 'secondary' }}">
                                            {{ ucfirst($forage->statut) }}
                                        </span>
                                    </td>
                                    <td>
                                        <a href="{{ route('forages.edit', $forage->id) }}" class="btn btn-sm btn-outline-primary">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                @else
                    <div class="text-center py-5 text-muted">
                        <i class="fas fa-bullseye fa-3x mb-3"></i>
                        <p>Aucun forage associé à ce site</p>
                    </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Énergie -->
    <div class="tab-pane fade" id="energie">
        <div class="card card-shadow">
            <div class="card-header bg-white py-3">
                <h5 class="mb-0 fw-bold">Sources d'énergie</h5>
            </div>
            <div class="card-body">
                @if($site->energySources->count() > 0)
                    <div class="row g-3">
                        @foreach($site->energySources as $energy)
                            <div class="col-md-6">
                                <div class="card border">
                                    <div class="card-body">
                                        <h6 class="fw-bold">{{ ucfirst($energy->type) }}</h6>
                                        <p class="mb-1 small"><strong>Statut:</strong> {{ ucfirst($energy->statut) }}</p>
                                        @if($energy->details)
                                            <p class="mb-0 small text-muted">{{ $energy->details }}</p>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <p class="text-muted text-center">Aucune source d'énergie enregistrée</p>
                @endif
            </div>
        </div>
    </div>

    <!-- Historique -->
    <div class="tab-pane fade" id="historique">
        <div class="card card-shadow">
            <div class="card-body">
                @if($site->interventions->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Date</th>
                                    <th>Type</th>
                                    <th>Technicien</th>
                                    <th>Statut</th>
                                    <th>Coût</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($site->interventions->take(10) as $intervention)
                                    <tr>
                                        <td>{{ $intervention->date_intervention->format('d/m/Y') }}</td>
                                        <td>{{ $intervention->type_intervention }}</td>
                                        <td>{{ $intervention->technicien_nom ?? 'N/A' }}</td>
                                        <td>
                                            <span class="badge bg-{{ $intervention->statut === 'terminee' ? 'success' : 'warning' }}">
                                                {{ ucfirst($intervention->statut) }}
                                            </span>
                                        </td>
                                        <td>{{ number_format($intervention->cout_total, 0, ',', ' ') }} FCFA</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <p class="text-muted text-center">Aucune intervention dans l'historique</p>
                @endif
            </div>
        </div>
    </div>

    <!-- Description -->
    <div class="tab-pane fade" id="description">
        <div class="card card-shadow">
            <div class="card-body">
                @if($site->description)
                    <p class="mb-0">{{ nl2br(e($site->description)) }}</p>
                @else
                    <p class="text-muted mb-0">Aucune description disponible</p>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
