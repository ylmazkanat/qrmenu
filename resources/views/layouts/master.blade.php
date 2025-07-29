<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    
    <title>@yield('title', 'QR Menu Sistemi')</title>
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
    <!-- Chart.js (Analytics için) -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    
    <!-- Ortak CSS Stilleri -->
    <style>
        :root {
            --sidebar-width: 280px;
            --header-height: 70px;
            
            /* Admin Panel Renkleri */
            --admin-primary: #dc3545;
            --admin-secondary: #6c757d;
            --admin-gradient: linear-gradient(135deg, #dc3545 0%, #fd7e14 100%);
            
            /* Business Panel Renkleri */
            --business-primary: #28a745;
            --business-secondary: #20c997;
            --business-gradient: linear-gradient(135deg, #28a745 0%, #20c997 100%);
            
            /* Restaurant Panel Renkleri */
            --restaurant-primary: #007bff;
            --restaurant-secondary: #6f42c1;
            --restaurant-gradient: linear-gradient(135deg, #007bff 0%, #6f42c1 100%);

            /* Bootstrap kart boşluk değişkenleri */
            --bs-card-spacer-y: 1.25rem; /* 20px */
            --bs-card-spacer-x: 1.25rem;
        }
        
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #f8f9fa;
            margin: 0;
            padding: 0;
        }
        
        /* Sidebar */
        .sidebar {
            position: fixed;
            top: 0;
            left: 0;
            height: 100vh;
            width: var(--sidebar-width);
            background: @yield('sidebar-gradient', 'linear-gradient(135deg, #007bff 0%, #6f42c1 100%)');
            box-shadow: 2px 0 10px rgba(0,0,0,0.1);
            z-index: 1000;
            overflow-y: auto;
            transition: all 0.3s ease;
        }
        
        .sidebar-header {
            padding: 1.5rem;
            text-align: center;
            border-bottom: 1px solid rgba(255,255,255,0.1);
        }
        
        .sidebar-header h4 {
            color: white;
            margin: 0;
            font-weight: 600;
        }
        
        .sidebar-header .role-badge {
            background: rgba(255,255,255,0.2);
            color: white;
            padding: 0.25rem 0.75rem;
            border-radius: 15px;
            font-size: 0.8rem;
            margin-top: 0.5rem;
            display: inline-block;
        }
        
        .nav-menu {
            padding: 1rem 0;
        }
        
        .nav-menu .nav-item {
            margin: 0.25rem 1rem;
        }
        
        .nav-menu .nav-link {
            color: rgba(255,255,255,0.8);
            padding: 0.75rem 1rem;
            border-radius: 10px;
            text-decoration: none;
            display: flex;
            align-items: center;
            transition: all 0.3s ease;
            font-weight: 500;
        }
        
        .nav-menu .nav-link:hover,
        .nav-menu .nav-link.active {
            background: rgba(255,255,255,0.1);
            color: white;
            transform: translateX(5px);
        }
        
        .nav-menu .nav-link i {
            margin-right: 0.75rem;
            width: 20px;
            text-align: center;
        }
        
        .nav-menu .notification-badge {
            background: #ff4757;
            color: white;
            border-radius: 10px;
            padding: 0.2rem 0.5rem;
            font-size: 0.7rem;
            margin-left: auto;
        }
        
        /* Main Content */
        .main-content {
            margin-left: var(--sidebar-width);
            min-height: 100vh;
            transition: all 0.3s ease;
        }
        
        /* Header */
        .top-header {
            background: white;
            height: var(--header-height);
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 0 2rem;
            position: sticky;
            top: 0;
            z-index: 999;
        }
        
        .page-title {
            margin: 0;
            color: #333;
            font-weight: 600;
        }
        
        .header-actions {
            display: flex;
            align-items: center;
            gap: 1rem;
        }
        
        .user-menu .dropdown-toggle {
            background: none;
            border: none;
            display: flex;
            align-items: center;
            gap: 0.5rem;
            padding: 0.5rem;
            border-radius: 8px;
            transition: all 0.3s ease;
        }
        
        .user-menu .dropdown-toggle:hover {
            background: #f8f9fa;
        }
        
        .user-avatar {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background: @yield('panel-color', '#007bff');
            color: white;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 600;
        }
        
        /* Content Area */
        .content-area {
            padding: 2rem;
        }
        
        .content-card {
            margin-bottom: 2rem;
            background: white;
            border-radius: 15px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.08);
            border: 1px solid #e9ecef;
            transition: all 0.3s ease;
        }

        /* Ek kenar boşlukları */
        .row {
            margin-left: 0;
            margin-right: 0;
        }

        .card-body {
            padding: 1.5rem;
        }
        
        .content-card:hover {
            box-shadow: 0 8px 25px rgba(0,0,0,0.12);
            transform: translateY(-2px);
        }
        
        .card-header {
            border-bottom: 1px solid #e9ecef;
            padding: 1.5rem;
            background: #f8f9fa;
            border-radius: 15px 15px 0 0;
        }
        
        .card-title {
            margin: 0;
            color: #333;
            font-weight: 600;
        }
        
        /* Stats Cards */
        .stats-card {
            background: white;
            border-radius: 15px;
            padding: 1.5rem;
            box-shadow: 0 5px 15px rgba(0,0,0,0.08);
            border: 1px solid #e9ecef;
            transition: all 0.3s ease;
            position: relative;
            overflow: hidden;
        }
        
        .stats-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 25px rgba(0,0,0,0.15);
        }
        
        .stats-icon {
            width: 60px;
            height: 60px;
            border-radius: 15px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.5rem;
            margin-bottom: 1rem;
        }
        
        .stats-value {
            font-size: 2rem;
            font-weight: 700;
            color: #333;
            margin-bottom: 0.5rem;
        }
        
        .stats-label {
            color: #6c757d;
            font-size: 0.9rem;
            margin-bottom: 0.5rem;
        }
        
        .stats-change {
            font-size: 0.8rem;
            display: flex;
            align-items: center;
            gap: 0.25rem;
        }
        
        .stats-change.positive { color: #28a745; }
        .stats-change.negative { color: #dc3545; }
        .stats-change.neutral { color: #6c757d; }
        
        /* Gradients */
        .gradient-primary { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); }
        .gradient-success { background: linear-gradient(135deg, #28a745 0%, #20c997 100%); }
        .gradient-info { background: linear-gradient(135deg, #17a2b8 0%, #6f42c1 100%); }
        .gradient-warning { background: linear-gradient(135deg, #ffc107 0%, #fd7e14 100%); }
        .gradient-danger { background: linear-gradient(135deg, #dc3545 0%, #e83e8c 100%); }
        
        /* Tables */
        .table-modern {
            border: none;
        }
        
        .table-modern thead th {
            border: none;
            background: #f8f9fa;
            color: #333;
            font-weight: 600;
            padding: 1rem;
        }
        
        .table-modern tbody td {
            border: none;
            padding: 1rem;
            vertical-align: middle;
            border-bottom: 1px solid #e9ecef;
        }
        
        .table-modern tbody tr:hover {
            background: #f8f9fa;
        }
        
        /* Badges */
        .badge-modern {
            padding: 0.5rem 0.75rem;
            border-radius: 8px;
            font-weight: 500;
            font-size: 0.8rem;
        }
        
        /* Buttons */
        .btn-admin-modern {
            background: var(--admin-gradient);
            border: none;
            border-radius: 10px;
            padding: 0.75rem 1.5rem;
            color: white;
            font-weight: 600;
            transition: all 0.3s ease;
        }
        
        .btn-business-modern {
            background: var(--business-gradient);
            border: none;
            border-radius: 10px;
            padding: 0.75rem 1.5rem;
            color: white;
            font-weight: 600;
            transition: all 0.3s ease;
        }
        
        .btn-restaurant-modern {
            background: var(--restaurant-gradient);
            border: none;
            border-radius: 10px;
            padding: 0.75rem 1.5rem;
            color: white;
            font-weight: 600;
            transition: all 0.3s ease;
        }
        
        .btn-admin-modern:hover,
        .btn-business-modern:hover,
        .btn-restaurant-modern:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(0,0,0,0.2);
            color: white;
        }
        
        /* Responsive */
        @media (max-width: 768px) {
            .sidebar {
                transform: translateX(-100%);
            }
            
            .sidebar.show {
                transform: translateX(0);
            }
            
            .main-content {
                margin-left: 0;
            }
            
            .top-header {
                padding: 0 1rem;
            }
            
            .content-area {
                padding: 1rem;
            }
        }
        
        /* Custom scrollbar */
        .sidebar::-webkit-scrollbar {
            width: 6px;
        }
        
        .sidebar::-webkit-scrollbar-track {
            background: rgba(255,255,255,0.1);
        }
        
        .sidebar::-webkit-scrollbar-thumb {
            background: rgba(255,255,255,0.3);
            border-radius: 3px;
        }
        
        .sidebar::-webkit-scrollbar-thumb:hover {
            background: rgba(255,255,255,0.5);
        }
        /* Collapsed sidebar */
        body.sidebar-collapsed .sidebar {
            width: 80px;
        }
        body.sidebar-collapsed .main-content {
            margin-left: 80px;
        }
        body.sidebar-collapsed .sidebar-header h4,
        body.sidebar-collapsed .sidebar-header .role-badge {
            display: none;
        }
        body.sidebar-collapsed .nav-menu .nav-link {
            justify-content: center;
            padding: 0.75rem 0.5rem;
            font-size: 0;
        }
        body.sidebar-collapsed .nav-menu .nav-link i {
            margin-right: 0;
            font-size: 1.3rem;
        }
    </style>
    
    @yield('additional-css')
</head>
<body>
    <!-- Sidebar -->
    <div class="sidebar">
        <div class="sidebar-header">
            <h4>
                <i class="@yield('panel-icon', 'bi bi-qr-code')"></i>
                QR Menu
            </h4>
            <div class="role-badge">
                @yield('role-name', 'Kullanıcı')
            </div>
        </div>
        
        <nav class="nav-menu">
            @yield('sidebar-menu')
        </nav>
    </div>
    
    <!-- Main Content -->
    <div class="main-content">
        <!-- Header -->
        <header class="top-header">
            <div class="d-flex align-items-center">
                <button class="btn btn-link d-md-none me-2" id="sidebar-toggle">
                    <i class="bi bi-list fs-4"></i>
                </button>
                <button class="btn btn-link d-none d-md-inline me-2" id="sidebar-shrink">
                    <i class="bi bi-chevron-double-left fs-4"></i>
                </button>
                <h1 class="page-title">@yield('page-title', 'Dashboard')</h1>
            </div>
            
            <div class="header-actions">
                <!-- Notifications -->
                @yield('header-notifications')
                
                <!-- User Menu -->
                <div class="dropdown user-menu">
                    <button class="dropdown-toggle" type="button" data-bs-toggle="dropdown">
                        <div class="user-avatar">
                            {{ substr(auth()->user()->name, 0, 2) }}
                        </div>
                        <div class="d-none d-md-block">
                            <div class="fw-medium">{{ auth()->user()->name }}</div>
                            <small class="text-muted">{{ auth()->user()->email }}</small>
                        </div>
                    </button>
                    <ul class="dropdown-menu dropdown-menu-end">
                        <li><h6 class="dropdown-header">{{ auth()->user()->name }}</h6></li>
                        <li><hr class="dropdown-divider"></li>
                        <li>
                            <a class="dropdown-item" href="#">
                                <i class="bi bi-person me-2"></i>Profil
                            </a>
                        </li>
                        <li>
                            <a class="dropdown-item" href="#">
                                <i class="bi bi-gear me-2"></i>Ayarlar
                            </a>
                        </li>
                        <li><hr class="dropdown-divider"></li>
                        <li>
                            <form method="POST" action="{{ route('logout') }}" class="d-inline">
                                @csrf
                                <button type="submit" class="dropdown-item text-danger">
                                    <i class="bi bi-box-arrow-right me-2"></i>Çıkış Yap
                                </button>
                            </form>
                        </li>
                    </ul>
                </div>
            </div>
        </header>
        
        <!-- Content Area -->
        <main class="content-area">
            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <i class="bi bi-check-circle me-2"></i>
                    {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif
            
            @if(session('error'))
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <i class="bi bi-exclamation-triangle me-2"></i>
                    {{ session('error') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif
            
            @yield('content')
        </main>
    </div>
    
    <!-- Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    <script>
        // Sidebar toggle for mobile
        document.getElementById('sidebar-toggle')?.addEventListener('click', function() {
            document.querySelector('.sidebar').classList.toggle('show');
        });
        
        // Close sidebar when clicking outside on mobile
        document.addEventListener('click', function(event) {
            const sidebar = document.querySelector('.sidebar');
            const toggle = document.getElementById('sidebar-toggle');
            
            if (window.innerWidth <= 768 && 
                !sidebar.contains(event.target) && 
                !toggle.contains(event.target)) {
                sidebar.classList.remove('show');
            }
        });
        
        // Active menu item
        document.addEventListener('DOMContentLoaded', function() {
            const currentPath = window.location.pathname;
            const menuLinks = document.querySelectorAll('.nav-menu .nav-link');
            
            menuLinks.forEach(link => {
                if (link.getAttribute('href') === currentPath) {
                    link.classList.add('active');
                }
            });
        });

        // Sidebar shrink for desktop
        document.getElementById('sidebar-shrink')?.addEventListener('click', function() {
            document.body.classList.toggle('sidebar-collapsed');
            const icon = this.querySelector('i');
            icon.classList.toggle('bi-chevron-double-left');
            icon.classList.toggle('bi-chevron-double-right');
        });
    </script>
    
    @yield('scripts')
</body>
</html> 