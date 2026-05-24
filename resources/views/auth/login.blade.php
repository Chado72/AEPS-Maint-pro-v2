@extends('layouts.auth')

@section('title', 'Connexion')

@section('content')
<div class="auth-card">
    <div class="auth-header">
        <div class="auth-logo">
            <i class="fas fa-tint"></i>
        </div>
        <h3 class="fw-bold mb-1">AEPS Maint Pro</h3>
        <p class="text-muted small">Province du Yadéga - ONEA</p>
    </div>
    
    <div class="auth-body">
        <form method="POST" action="{{ route('login') }}">
            @csrf
            
            <div class="mb-3">
                <label for="email" class="form-label">Adresse Email</label>
                <div class="input-group">
                    <span class="input-group-text bg-light">
                        <i class="fas fa-envelope text-muted"></i>
                    </span>
                    <input type="email" 
                           class="form-control @error('email') is-invalid @enderror" 
                           id="email" 
                           name="email" 
                           value="{{ old('email') }}" 
                           placeholder="exemple@onea.bf"
                           required 
                           autofocus>
                </div>
                @error('email')
                    <div class="invalid-feedback d-block">{{ $message }}</div>
                @enderror
            </div>

            <div class="mb-4">
                <label for="password" class="form-label">Mot de passe</label>
                <div class="input-group">
                    <span class="input-group-text bg-light">
                        <i class="fas fa-lock text-muted"></i>
                    </span>
                    <input type="password" 
                           class="form-control @error('password') is-invalid @enderror" 
                           id="password" 
                           name="password" 
                           placeholder="••••••••"
                           required>
                </div>
                @error('password')
                    <div class="invalid-feedback d-block">{{ $message }}</div>
                @enderror
            </div>

            <div class="d-grid gap-2">
                <button type="submit" class="btn btn-primary btn-lg">
                    <i class="fas fa-sign-in-alt me-2"></i> Se connecter
                </button>
            </div>

            <div class="text-center mt-3">
                <small class="text-muted">
                    Besoin d'aide ? Contactez l'administrateur système.
                </small>
            </div>
        </form>
    </div>
</div>
@endsection
