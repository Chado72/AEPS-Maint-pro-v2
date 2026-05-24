<!-- Sidebar -->
<div id="sidebar-wrapper">
    <div class="sidebar-heading text-center">
        <i class="fas fa-tint me-2"></i> AEPS Maint Pro
    </div>
    <div class="list-group list-group-flush">
        <!-- Dashboard -->
        <a href="{{ route('dashboard') }}" 
           class="list-group-item list-group-item-action {{ request()->routeIs('dashboard') ? 'active' : '' }}">
            <i class="fas fa-tachometer-alt"></i> Tableau de bord
        </a>

        <!-- Géographie -->
        <div class="sidebar-heading px-3 mt-3 mb-2 text-uppercase small text-muted">
            Géographie
        </div>
        <a href="{{ route('communes.index') }}" 
           class="list-group-item list-group-item-action {{ request()->routeIs('communes.*') ? 'active' : '' }}">
            <i class="fas fa-city"></i> Communes
        </a>
        <a href="{{ route('villages.index') }}" 
           class="list-group-item list-group-item-action {{ request()->routeIs('villages.*') ? 'active' : '' }}">
            <i class="fas fa-home"></i> Villages
        </a>

        <!-- Infrastructure -->
        <div class="sidebar-heading px-3 mt-3 mb-2 text-uppercase small text-muted">
            Infrastructure
        </div>
        <a href="{{ route('sites.index') }}" 
           class="list-group-item list-group-item-action {{ request()->routeIs('sites.*') ? 'active' : '' }}">
            <i class="fas fa-map-marker-alt"></i> Sites AEPS/PEA
        </a>
        <a href="{{ route('forages.index') }}" 
           class="list-group-item list-group-item-action {{ request()->routeIs('forages.*') ? 'active' : '' }}">
            <i class="fas fa-bullseye"></i> Forages
        </a>
        <a href="{{ route('energie.index') }}" 
           class="list-group-item list-group-item-action {{ request()->routeIs('energie.*') ? 'active' : '' }}">
            <i class="fas fa-solar-panel"></i> Énergie
        </a>

        <!-- Maintenance -->
        <div class="sidebar-heading px-3 mt-3 mb-2 text-uppercase small text-muted">
            Maintenance
        </div>
        <a href="{{ route('interventions.index') }}" 
           class="list-group-item list-group-item-action {{ request()->routeIs('interventions.*') ? 'active' : '' }}">
            <i class="fas fa-tools"></i> Interventions
        </a>
        <a href="{{ route('spare-parts.index') }}" 
           class="list-group-item list-group-item-action {{ request()->routeIs('spare-parts.*') ? 'active' : '' }}">
            <i class="fas fa-cogs"></i> Pièces détachées
        </a>

        <!-- Documents & Rapports -->
        <div class="sidebar-heading px-3 mt-3 mb-2 text-uppercase small text-muted">
            Documents & Rapports
        </div>
        <a href="{{ route('documents.index') }}" 
           class="list-group-item list-group-item-action {{ request()->routeIs('documents.*') ? 'active' : '' }}">
            <i class="fas fa-file-alt"></i> Documents
        </a>
        <a href="{{ route('reports.index') }}" 
           class="list-group-item list-group-item-action {{ request()->routeIs('reports.*') ? 'active' : '' }}">
            <i class="fas fa-chart-bar"></i> Rapports
        </a>

        <!-- IA & Paramètres -->
        <div class="sidebar-heading px-3 mt-3 mb-2 text-uppercase small text-muted">
            Intelligent & Config
        </div>
        <a href="{{ route('ai.chat') }}" 
           class="list-group-item list-group-item-action {{ request()->routeIs('ai.*') ? 'active' : '' }}">
            <i class="fas fa-robot"></i> Assistant IA
        </a>
        <a href="{{ route('settings.index') }}" 
           class="list-group-item list-group-item-action {{ request()->routeIs('settings.*') ? 'active' : '' }}">
            <i class="fas fa-cog"></i> Paramètres
        </a>

        <!-- Déconnexion -->
        <div class="mt-auto pt-3">
            <form action="{{ route('logout') }}" method="POST" class="d-inline">
                @csrf
                <button type="submit" class="list-group-item list-group-item-action w-100 text-danger border-0">
                    <i class="fas fa-sign-out-alt"></i> Déconnexion
                </button>
            </form>
        </div>
    </div>
</div>
