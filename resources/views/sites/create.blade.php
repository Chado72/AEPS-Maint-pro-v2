@extends('layouts.app')

@section('title', 'Nouveau site')

@section('content')
<div class="row mb-4">
    <div class="col-12">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                <li class="breadcrumb-item"><a href="{{ route('sites.index') }}">Sites</a></li>
                <li class="breadcrumb-item active">Créer</li>
            </ol>
        </nav>
        <h2 class="fw-bold text-dark">
            <i class="fas fa-plus-circle me-2 text-primary"></i> Nouveau site AEPS/PEA
        </h2>
    </div>
</div>

<form action="{{ route('sites.store') }}" method="POST">
    @csrf
    
    <div class="row">
        <!-- Informations principales -->
        <div class="col-lg-8">
            <div class="card card-shadow mb-4">
                <div class="card-header bg-white py-3">
                    <h5 class="mb-0 fw-bold text-dark">Informations générales</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="nom" class="form-label required">Nom du site <span class="text-danger">*</span></label>
                            <input type="text" 
                                   class="form-control @error('nom') is-invalid @enderror" 
                                   id="nom" 
                                   name="nom" 
                                   value="{{ old('nom') }}"
                                   placeholder="Ex: AEPS Tampouy Centre"
                                   required>
                            @error('nom')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-6 mb-3">
                            <label for="code" class="form-label">Code site</label>
                            <input type="text" 
                                   class="form-control @error('code') is-invalid @enderror" 
                                   id="code" 
                                   name="code" 
                                   value="{{ old('code') }}"
                                   placeholder="Ex: TAM-001">
                            @error('code')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="type" class="form-label required">Type <span class="text-danger">*</span></label>
                            <select class="form-select @error('type') is-invalid @enderror" 
                                    id="type" 
                                    name="type"
                                    required>
                                <option value="">Sélectionner</option>
                                <option value="AEPS" {{ old('type') == 'AEPS' ? 'selected' : '' }}>AEPS (Alimentation en Eau Potable Simplifiée)</option>
                                <option value="PEA" {{ old('type') == 'PEA' ? 'selected' : '' }}>PEA (Poste d'Eau Autonome)</option>
                            </select>
                            @error('type')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-6 mb-3">
                            <label for="statut" class="form-label required">Statut <span class="text-danger">*</span></label>
                            <select class="form-select @error('statut') is-invalid @enderror" 
                                    id="statut" 
                                    name="statut"
                                    required>
                                <option value="">Sélectionner</option>
                                <option value="actif" {{ old('statut') == 'actif' ? 'selected' : '' }}>Actif</option>
                                <option value="en_panne" {{ old('statut') == 'en_panne' ? 'selected' : '' }}>En panne</option>
                                <option value="abandonne" {{ old('statut') == 'abandonne' ? 'selected' : '' }}>Abandonné</option>
                                <option value="en_construction" {{ old('statut') == 'en_construction' ? 'selected' : '' }}>En construction</option>
                            </select>
                            @error('statut')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="description" class="form-label">Description / Observations</label>
                        <textarea class="form-control @error('description') is-invalid @enderror" 
                                  id="description" 
                                  name="description" 
                                  rows="3"
                                  placeholder="Informations complémentaires sur le site...">{{ old('description') }}</textarea>
                        @error('description')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
            </div>

            <!-- Localisation -->
            <div class="card card-shadow mb-4">
                <div class="card-header bg-white py-3">
                    <h5 class="mb-0 fw-bold text-dark">Localisation</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="commune_id" class="form-label required">Commune <span class="text-danger">*</span></label>
                            <select class="form-select @error('commune_id') is-invalid @enderror" 
                                    id="commune_id" 
                                    name="commune_id"
                                    required
                                    onchange="loadVillages(this.value)">
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

                        <div class="col-md-6 mb-3">
                            <label for="village_id" class="form-label required">Village <span class="text-danger">*</span></label>
                            <select class="form-select @error('village_id') is-invalid @enderror" 
                                    id="village_id" 
                                    name="village_id"
                                    required>
                                <option value="">Sélectionner un village</option>
                                @if(old('commune_id'))
                                    @foreach($villages as $village)
                                        <option value="{{ $village->id }}" {{ old('village_id') == $village->id ? 'selected' : '' }}>
                                            {{ $village->nom }}
                                        </option>
                                    @endforeach
                                @endif
                            </select>
                            @error('village_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="latitude" class="form-label">Latitude</label>
                            <input type="text" 
                                   class="form-control @error('latitude') is-invalid @enderror" 
                                   id="latitude" 
                                   name="latitude" 
                                   value="{{ old('latitude') }}"
                                   placeholder="Ex: 13.5833">
                            @error('latitude')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-6 mb-3">
                            <label for="longitude" class="form-label">Longitude</label>
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
                    </div>
                </div>
            </div>
        </div>

        <!-- Sidebar actions -->
        <div class="col-lg-4">
            <div class="card card-shadow mb-4">
                <div class="card-header bg-white py-3">
                    <h5 class="mb-0 fw-bold text-dark">Actions</h5>
                </div>
                <div class="card-body">
                    <div class="d-grid gap-2">
                        <button type="submit" class="btn btn-primary btn-lg">
                            <i class="fas fa-save me-2"></i> Enregistrer le site
                        </button>
                        <a href="{{ route('sites.index') }}" class="btn btn-outline-secondary">
                            <i class="fas fa-times me-2"></i> Annuler
                        </a>
                    </div>

                    <hr>

                    <div class="alert alert-info small mb-0">
                        <strong><i class="fas fa-info-circle me-2"></i>Info :</strong>
                        Après création, vous pourrez ajouter des forages et des équipements énergétiques à ce site.
                    </div>
                </div>
            </div>
        </div>
    </div>
</form>
@endsection

@push('styles')
<style>
    .required::after { content: " *"; color: red; }
</style>
@endpush

@push('scripts')
<script>
function loadVillages(communeId) {
    if (!communeId) {
        document.getElementById('village_id').innerHTML = '<option value="">Sélectionner un village</option>';
        return;
    }

    // Ici, vous pourriez faire un appel AJAX pour charger les villages dynamiquement
    // Pour l'instant, recharger la page avec le paramètre commune_id
    window.location.href = "{{ route('sites.create') }}?commune_id=" + communeId;
}
</script>
@endpush
