@extends('layouts.app')

@section('title', 'Nouveau village')

@section('content')
<div class="row mb-4">
    <div class="col-12">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                <li class="breadcrumb-item"><a href="{{ route('villages.index') }}">Villages</a></li>
                <li class="breadcrumb-item active">Créer</li>
            </ol>
        </nav>
        <h2 class="fw-bold text-dark">
            <i class="fas fa-plus-circle me-2 text-primary"></i> Nouveau village
        </h2>
    </div>
</div>

<div class="row">
    <div class="col-lg-8">
        <div class="card card-shadow">
            <div class="card-header bg-white py-3">
                <h5 class="mb-0 fw-bold text-dark">Informations du village</h5>
            </div>
            <div class="card-body">
                <form action="{{ route('villages.store') }}" method="POST">
                    @csrf
                    
                    <div class="mb-3">
                        <label for="nom" class="form-label required">Nom du village <span class="text-danger">*</span></label>
                        <input type="text" 
                               class="form-control @error('nom') is-invalid @enderror" 
                               id="nom" 
                               name="nom" 
                               value="{{ old('nom') }}"
                               placeholder="Ex: Tampouy"
                               required>
                        @error('nom')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="commune_id" class="form-label required">Commune <span class="text-danger">*</span></label>
                        <select class="form-select @error('commune_id') is-invalid @enderror" 
                                id="commune_id" 
                                name="commune_id"
                                required>
                            <option value="">Sélectionner une commune</option>
                            @foreach($communes as $commune)
                                <option value="{{ $commune->id }}" {{ old('commune_id') == $commune->id ? 'selected' : '' }}>
                                    {{ $commune->nom }}
                                </option>
                            @endforeach
                        </select>
                        @error('commune_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="population" class="form-label">Population estimée</label>
                        <input type="number" 
                               class="form-control @error('population') is-invalid @enderror" 
                               id="population" 
                               name="population" 
                               value="{{ old('population') }}"
                               min="0"
                               placeholder="Ex: 1500">
                        @error('population')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="latitude" class="form-label">Latitude (GPS)</label>
                        <input type="text" 
                               class="form-control @error('latitude') is-invalid @enderror" 
                               id="latitude" 
                               name="latitude" 
                               value="{{ old('latitude') }}"
                               placeholder="Ex: 13.5833">
                        @error('latitude')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        <div class="form-text">Coordonnées GPS pour localisation</div>
                    </div>

                    <div class="mb-3">
                        <label for="longitude" class="form-label">Longitude (GPS)</label>
                        <input type="text" 
                               class="form-control @error('longitude') is-invalid @enderror" 
                               id="longitude" 
                               name="longitude" 
                               value="{{ old('longitude') }}"
                               placeholder="Ex: -2.4167">
                        @error('longitude')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="d-flex gap-2 mt-4">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save me-2"></i> Enregistrer
                        </button>
                        <a href="{{ route('villages.index') }}" class="btn btn-outline-secondary">
                            <i class="fas fa-times me-2"></i> Annuler
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
    .required::after { content: " *"; color: red; }
</style>
@endpush
