<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title', 'Dashboard') - AEPS Maint Pro</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />
    
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    
    <style>
        :root {
            --sidebar-width: 260px;
            --primary-color: #0d6efd;
            --secondary-bg: #f8f9fa;
        }
        body {
            font-family: 'Figtree', sans-serif;
            background-color: var(--secondary-bg);
            overflow-x: hidden;
        }
        /* Sidebar Styles */
        #sidebar-wrapper {
            min-height: 100vh;
            width: var(--sidebar-width);
            margin-left: 0;
            transition: margin .25s ease-out;
            background-color: #212529;
            color: white;
            position: fixed;
            z-index: 1000;
        }
        #sidebar-wrapper .sidebar-heading {
            padding: 1.5rem 1.25rem;
            font-size: 1.2rem;
            font-weight: bold;
            border-bottom: 1px solid #495057;
        }
        #sidebar-wrapper .list-group {
            width: var(--sidebar-width);
        }
        #sidebar-wrapper .list-group-item {
            background-color: transparent;
            color: #cfd2d6;
            border: none;
            padding: 1rem 1.25rem;
        }
        #sidebar-wrapper .list-group-item:hover {
            background-color: #343a40;
            color: #fff;
        }
        #sidebar-wrapper .list-group-item.active {
            background-color: var(--primary-color);
            color: #fff;
            font-weight: 600;
        }
        #sidebar-wrapper .list-group-item i {
            margin-right: 10px;
            width: 20px;
            text-align: center;
        }

        /* Page Content */
        #page-content-wrapper {
            min-width: 100vw;
            margin-left: var(--sidebar-width);
            transition: margin .25s ease-out;
        }

        /* Toggled State */
        body.sb-sidenav-toggled #sidebar-wrapper {
            margin-left: calc(-1 * var(--sidebar-width));
        }
        body.sb-sidenav-toggled #page-content-wrapper {
            margin-left: 0;
        }

        @media (min-width: 768px) {
            #sidebar-wrapper {
                margin-left: 0;
            }
            #page-content-wrapper {
                min-width: 0;
                width: 100%;
                margin-left: var(--sidebar-width);
            }
            body.sb-sidenav-toggled #sidebar-wrapper {
                margin-left: calc(-1 * var(--sidebar-width));
            }
            body.sb-sidenav-toggled #page-content-wrapper {
                margin-left: 0;
            }
        }
        
        /* Custom Utilities */
        .card-shadow {
            box-shadow: 0 0.15rem 1.75rem 0 rgba(58, 59, 69, 0.15);
            border: none;
        }
        .navbar-top {
            background-color: #fff;
            box-shadow: 0 .15rem 1.75rem 0 rgba(58,59,69,.15);
        }
    </style>
    
    @stack('styles')
</head>
<body>

    <div class="d-flex" id="wrapper">
        <!-- Sidebar -->
        @include('partials.sidebar')

        <!-- Page Content -->
        <div id="page-content-wrapper">
            <!-- Top Navbar -->
            @include('partials.navbar')

            <!-- Main Content -->
            <div class="container-fluid px-4 py-4">
                @include('partials.flash')
                
                @yield('content')
            </div>

            <!-- Footer -->
            @include('partials.footer')
        </div>
    </div>

    <!-- Bootstrap 5 JS Bundle -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <!-- Chart.js (Optionnel mais utile pour le dashboard) -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    
    <script>
        // Toggle Sidebar
        window.addEventListener('DOMContentLoaded', event => {
            const sidebarToggle = document.body.querySelector('#sidebarToggle');
            if (sidebarToggle) {
                sidebarToggle.addEventListener('click', event => {
                    event.preventDefault();
                    document.body.classList.toggle('sb-sidenav-toggled');
                });
            }
        });

        // Auto-hide alerts
        document.addEventListener('DOMContentLoaded', function () {
            setTimeout(function() {
                let alerts = document.querySelectorAll('.alert');
                alerts.forEach(function(alert) {
                    let bsAlert = new bootstrap.Alert(alert);
                    bsAlert.close();
                });
            }, 5000);
        });
    </script>
    
    @stack('scripts')
</body>
</html>
