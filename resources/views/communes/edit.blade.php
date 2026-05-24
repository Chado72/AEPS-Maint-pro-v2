@extends('layouts.app')

@section('title', 'Modifier la commune')

@section('content')
<div class="row mb-4">
    <div class="col-12">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                <li class="breadcrumb-item"><a href="{{ route('communes.index') }}">Communes</a></li>
                <li class="breadcrumb-item active">Modifier</li>
            </ol>
        </nav>
        <h2 class="fw-bold text-dark">
            <i class="fas fa-edit me-2 text-primary"></i> Modifier la commune
        </h2>
    </div>
</div>

<div class="row">
    <div class="col-lg-8">
        <div class="card card-shadow">
            <div class="card-header bg-white py-3">
                <h5 class="mb-0 fw-bold text-dark">Informations principales</h5>
            </div>
            <div class="card-body">
                <form action="{{ route('communes.update', $commune->id) }}" method="POST">
                    @csrf
                    @method('PUT')
                    
                    <div class="mb-3">
                        <label for="nom" class="form-label required">Nom de la commune <span class="text-danger">*</span></label>
                        <input type="text" 
                               class="form-control @error('nom') is-invalid @enderror" 
                               id="nom" 
                               name="nom" 
                               value="{{ old('nom', $commune->nom) }}"
                               placeholder="Ex: Ouahigouya"
                               required>
                        @error('nom')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        <div class="form-text">Nom officiel de la commune</div>
                    </div>

                    <div class="mb-3">
                        <label for="code" class="form-label">Code INSEE / Code officiel</label>
                        <input type="text" 
                               class="form-control @error('code') is-invalid @enderror" 
                               id="code" 
                               name="code" 
                               value="{{ old('code', $commune->code) }}"
                               placeholder="Ex: OUH">
                        @error('code')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        <div class="form-text">Code d'identification officiel (optionnel)</div>
                    </div>

                    <div class="mb-3">
                        <label for="superficie" class="form-label">Superficie (km²)</label>
                        <input type="number" 
                               class="form-control @error('superficie') is-invalid @enderror" 
                               id="superficie" 
                               name="superficie" 
                               value="{{ old('superficie', $commune->superficie) }}"
                               step="0.01"
                               min="0"
                               placeholder="Ex: 150.5">
                        @error('superficie')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="population" class="form-label">Population estimée</label>
                        <input type="number" 
                               class="form-control @error('population') is-invalid @enderror" 
                               id="population" 
                               name="population" 
                               value="{{ old('population', $commune->population) }}"
                               min="0"
                               placeholder="Ex: 25000">
                        @error('population')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="d-flex gap-2 mt-4">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save me-2"></i> Mettre à jour
                        </button>
                        <a href="{{ route('communes.index') }}" class="btn btn-outline-secondary">
                            <i class="fas fa-times me-2"></i> Annuler
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="col-lg-4">
        <div class="card card-shadow">
            <div class="card-header bg-white py-3">
                <h5 class="mb-0 fw-bold text-dark">
                    <i class="fas fa-chart-bar me-2 text-primary"></i> Statistiques
                </h5>
            </div>
            <div class="card-body">
                <div class="mb-3">
                    <div class="d-flex justify-content-between mb-1">
                        <span class="text-muted small">Villages</span>
                        <strong>{{ $commune->villages_count ?? 0 }}</strong>
                    </div>
                    <div class="progress" style="height: 6px;">
                        <div class="progress-bar bg-info" role="progressbar" style="width: {{ min(100, ($commune->villages_count ?? 0) * 10) }}%"></div>
                    </div>
                </div>
                
                <div class="mb-3">
                    <div class="d-flex justify-content-between mb-1">
                        <span class="text-muted small">Sites AEPS/PEA</span>
                        <strong>{{ $commune->sites_count ?? 0 }}</strong>
                    </div>
                    <div class="progress" style="height: 6px;">
                        <div class="progress-bar bg-success" role="progressbar" style="width: {{ min(100, ($commune->sites_count ?? 0) * 5) }}%"></div>
                    </div>
                </div>

                <hr>
                
                <div class="small text-muted">
                    <p><strong>Créée le :</strong> {{ $commune->created_at->format('d/m/Y H:i') }}</p>
                    <p><strong>Dernière modification :</strong> {{ $commune->updated_at->diffForHumans() }}</p>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
    .required::after {
        content: " *";
        color: red;
    }
</style>
@endpush
