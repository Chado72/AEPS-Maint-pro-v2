<nav class="navbar navbar-expand-lg navbar-top border-bottom">
    <div class="container-fluid">
        <!-- Toggle Button -->
        <button class="btn btn-link btn-sm order-1 order-lg-0 me-3" id="sidebarToggle" href="#">
            <i class="fas fa-bars"></i>
        </button>

        <!-- Brand Mobile -->
        <a class="navbar-brand d-lg-none" href="{{ route('dashboard') }}">
            <i class="fas fa-tint text-primary"></i> AEPS Maint Pro
        </a>

        <!-- Navbar Content -->
        <div class="collapse navbar-collapse" id="navbarSupportedContent">
            <!-- Left Side Of Navbar (Search or Notifications could go here) -->
            <ul class="navbar-nav me-auto">
                <li class="nav-item">
                    <span class="nav-text text-muted small">
                        Province du Yadéga - Région du Nord
                    </span>
                </li>
            </ul>

            <!-- Right Side Of Navbar -->
            <ul class="navbar-nav ms-auto align-items-center">
                <!-- User Dropdown -->
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle d-flex align-items-center" href="#" id="navbarDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                        <div class="avatar-circle bg-primary text-white rounded-circle d-flex align-items-center justify-content-center me-2" 
                             style="width: 35px; height: 35px; font-weight: bold;">
                            {{ substr(Auth::user()->name, 0, 1) }}
                        </div>
                        <div>
                            <span class="d-none d-lg-inline fw-bold small">{{ Auth::user()->name }}</span>
                            <div class="d-none d-lg-block" style="font-size: 0.75rem; color: #6c757d;">
                                {{ Auth::user()->role->name ?? 'Utilisateur' }}
                            </div>
                        </div>
                    </a>
                    <ul class="dropdown-menu dropdown-menu-end shadow border-0" aria-labelledby="navbarDropdown">
                        <li>
                            <a class="dropdown-item" href="{{ route('profile.edit') }}">
                                <i class="fas fa-user me-2"></i> Mon Profil
                            </a>
                        </li>
                        <li><hr class="dropdown-divider"></li>
                        <li>
                            <form action="{{ route('logout') }}" method="POST">
                                @csrf
                                <button type="submit" class="dropdown-item text-danger">
                                    <i class="fas fa-sign-out-alt me-2"></i> Déconnexion
                                </button>
                            </form>
                        </li>
                    </ul>
                </li>
            </ul>
        </div>
    </div>
</nav>
