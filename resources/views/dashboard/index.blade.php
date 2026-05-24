@extends('layouts.app')

@section('title', 'Tableau de bord')

@section('content')
<div class="row mb-4">
    <div class="col-12">
        <h2 class="fw-bold text-dark">
            <i class="fas fa-tachometer-alt me-2 text-primary"></i> Tableau de bord
        </h2>
        <p class="text-muted">Province du Yadéga - Région du Nord</p>
    </div>
</div>

<!-- Statistiques Cards -->
<div class="row g-4 mb-4">
    <!-- Sites Total -->
    <div class="col-md-3">
        <div class="card card-shadow h-100 border-start border-4 border-primary">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="text-muted text-uppercase small fw-bold mb-2">Sites AEPS/PEA</h6>
                        <h3 class="mb-0 fw-bold text-dark">{{ $stats['total_sites'] ?? 0 }}</h3>
                    </div>
                    <div class="bg-primary bg-opacity-10 rounded-circle p-3">
                        <i class="fas fa-map-marker-alt fa-2x text-primary"></i>
                    </div>
                </div>
            </div>
            <div class="card-footer bg-white border-0 pt-0">
                <a href="{{ route('sites.index') }}" class="text-decoration-none small">
                    Voir tous les sites <i class="fas fa-arrow-right ms-1"></i>
                </a>
            </div>
        </div>
    </div>

    <!-- Forages Total -->
    <div class="col-md-3">
        <div class="card card-shadow h-100 border-start border-4 border-success">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="text-muted text-uppercase small fw-bold mb-2">Forages</h6>
                        <h3 class="mb-0 fw-bold text-dark">{{ $stats['total_forages'] ?? 0 }}</h3>
                    </div>
                    <div class="bg-success bg-opacity-10 rounded-circle p-3">
                        <i class="fas fa-bullseye fa-2x text-success"></i>
                    </div>
                </div>
            </div>
            <div class="card-footer bg-white border-0 pt-0">
                <a href="{{ route('forages.index') }}" class="text-decoration-none small">
                    Voir tous les forages <i class="fas fa-arrow-right ms-1"></i>
                </a>
            </div>
        </div>
    </div>

    <!-- Interventions en cours -->
    <div class="col-md-3">
        <div class="card card-shadow h-100 border-start border-4 border-warning">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="text-muted text-uppercase small fw-bold mb-2">Interventions en cours</h6>
                        <h3 class="mb-0 fw-bold text-dark">{{ $stats['interventions_en_cours'] ?? 0 }}</h3>
                    </div>
                    <div class="bg-warning bg-opacity-10 rounded-circle p-3">
                        <i class="fas fa-tools fa-2x text-warning"></i>
                    </div>
                </div>
            </div>
            <div class="card-footer bg-white border-0 pt-0">
                <a href="{{ route('interventions.index', ['status' => 'en_cours']) }}" class="text-decoration-none small">
                    Voir les interventions <i class="fas fa-arrow-right ms-1"></i>
                </a>
            </div>
        </div>
    </div>

    <!-- Pièces en stock faible -->
    <div class="col-md-3">
        <div class="card card-shadow h-100 border-start border-4 border-danger">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="text-muted text-uppercase small fw-bold mb-2">Stock faible</h6>
                        <h3 class="mb-0 fw-bold text-dark">{{ $stats['pieces_stock_faible'] ?? 0 }}</h3>
                    </div>
                    <div class="bg-danger bg-opacity-10 rounded-circle p-3">
                        <i class="fas fa-exclamation-triangle fa-2x text-danger"></i>
                    </div>
                </div>
            </div>
            <div class="card-footer bg-white border-0 pt-0">
                <a href="{{ route('spare-parts.index', ['stock' => 'low']) }}" class="text-decoration-none small">
                    Gérer le stock <i class="fas fa-arrow-right ms-1"></i>
                </a>
            </div>
        </div>
    </div>
</div>

<!-- Graphiques et Activités Récentes -->
<div class="row g-4">
    <!-- Graphique: Interventions par mois -->
    <div class="col-lg-8">
        <div class="card card-shadow h-100">
            <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
                <h5 class="mb-0 fw-bold text-dark">
                    <i class="fas fa-chart-line me-2 text-primary"></i> Interventions par mois (2024)
                </h5>
            </div>
            <div class="card-body">
                <canvas id="interventionsChart" height="120"></canvas>
            </div>
        </div>
    </div>

    <!-- Dernières interventions -->
    <div class="col-lg-4">
        <div class="card card-shadow h-100">
            <div class="card-header bg-white py-3">
                <h5 class="mb-0 fw-bold text-dark">
                    <i class="fas fa-history me-2 text-primary"></i> Dernières interventions
                </h5>
            </div>
            <div class="card-body p-0">
                <div class="list-group list-group-flush">
                    @forelse($recentInterventions ?? [] as $intervention)
                        <a href="{{ route('interventions.show', $intervention->id) }}" 
                           class="list-group-item list-group-item-action py-3">
                            <div class="d-flex w-100 justify-content-between align-items-start">
                                <div>
                                    <h6 class="mb-1 fw-bold text-dark">{{ $intervention->site->nom ?? 'Site inconnu' }}</h6>
                                    <small class="text-muted">{{ $intervention->type_intervention }}</small>
                                    <br>
                                    <span class="badge bg-{{ $intervention->statut === 'terminee' ? 'success' : ($intervention->statut === 'en_cours' ? 'warning' : 'secondary') }}">
                                        {{ ucfirst(str_replace('_', ' ', $intervention->statut)) }}
                                    </span>
                                </div>
                                <small class="text-muted">{{ $intervention->date_intervention->format('d/m/Y') }}</small>
                            </div>
                        </a>
                    @empty
                        <div class="p-4 text-center text-muted">
                            <i class="fas fa-inbox fa-2x mb-2"></i>
                            <p class="mb-0">Aucune intervention récente</p>
                        </div>
                    @endforelse
                </div>
            </div>
            <div class="card-footer bg-white text-center">
                <a href="{{ route('interventions.index') }}" class="text-decoration-none small">
                    Voir toutes les interventions <i class="fas fa-arrow-right ms-1"></i>
                </a>
            </div>
        </div>
    </div>
</div>

<!-- Communes et Villages Stats -->
<div class="row g-4 mt-4">
    <div class="col-md-6">
        <div class="card card-shadow">
            <div class="card-header bg-white py-3">
                <h5 class="mb-0 fw-bold text-dark">
                    <i class="fas fa-city me-2 text-primary"></i> Répartition par commune
                </h5>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead>
                            <tr>
                                <th>Commune</th>
                                <th class="text-center">Villages</th>
                                <th class="text-center">Sites</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($communesStats ?? [] as $commune)
                                <tr>
                                    <td>{{ $commune->nom }}</td>
                                    <td class="text-center">{{ $commune->villages_count ?? 0 }}</td>
                                    <td class="text-center">
                                        <span class="badge bg-primary rounded-pill">{{ $commune->sites_count ?? 0 }}</span>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="3" class="text-center text-muted py-4">
                                        Aucune donnée disponible
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <div class="col-md-6">
        <div class="card card-shadow">
            <div class="card-header bg-white py-3">
                <h5 class="mb-0 fw-bold text-dark">
                    <i class="fas fa-solar-panel me-2 text-primary"></i> Sources d'énergie
                </h5>
            </div>
            <div class="card-body">
                <div class="row text-center">
                    <div class="col-4">
                        <div class="p-3">
                            <i class="fas fa-sun fa-2x text-warning mb-2"></i>
                            <h4 class="fw-bold">{{ $stats['solaire'] ?? 0 }}</h4>
                            <small class="text-muted">Solaire</small>
                        </div>
                    </div>
                    <div class="col-4">
                        <div class="p-3">
                            <i class="fas fa-plug fa-2x text-success mb-2"></i>
                            <h4 class="fw-bold">{{ $stats['reseau'] ?? 0 }}</h4>
                            <small class="text-muted">Réseau</small>
                        </div>
                    </div>
                    <div class="col-4">
                        <div class="p-3">
                            <i class="fas fa-gas-pump fa-2x text-danger mb-2"></i>
                            <h4 class="fw-bold">{{ $stats['groupe'] ?? 0 }}</h4>
                            <small class="text-muted">Groupe</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    // Graphique des interventions par mois
    const ctx = document.getElementById('interventionsChart').getContext('2d');
    
    // Données provenant du contrôleur
    const monthlyData = @json($monthlyInterventions ?? []);
    
    new Chart(ctx, {
        type: 'line',
        data: {
            labels: ['Jan', 'Fév', 'Mar', 'Avr', 'Mai', 'Juin', 'Juil', 'Août', 'Sep', 'Oct', 'Nov', 'Déc'],
            datasets: [{
                label: 'Nombre d\'interventions',
                data: monthlyData,
                borderColor: '#0d6efd',
                backgroundColor: 'rgba(13, 110, 253, 0.1)',
                tension: 0.4,
                fill: true,
                pointBackgroundColor: '#0d6efd',
                pointBorderColor: '#fff',
                pointBorderWidth: 2,
                pointRadius: 5,
                pointHoverRadius: 7
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: true,
            plugins: {
                legend: {
                    display: false
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        stepSize: 1
                    }
                }
            }
        }
    });
</script>
@endpush
