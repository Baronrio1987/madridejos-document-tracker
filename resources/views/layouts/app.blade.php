<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="default">
    
    <title>@yield('title', 'Dashboard') - {{ setting('system.name', config('app.name')) }}</title>
    
    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family={{ urlencode(setting('theme.font_family', 'inter:400,500,600,700')) }}&display=swap" rel="stylesheet" />
    
    <!-- Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css" rel="stylesheet">
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- Favicon -->
    @if(setting('appearance.favicon'))
    <link rel="icon" type="image/x-icon" href="{{ setting('appearance.favicon') }}">
    @else
    <link rel="icon" type="image/x-icon" href="{{ asset('favicon.ico') }}">
    @endif
    
    <link rel="apple-touch-icon" sizes="180x180" href="{{ setting('appearance.favicon', asset('favicon.ico')) }}">
    
    <!-- Dynamic Styles -->
    <link rel="stylesheet" href="{{ route('dynamic-styles.css') }}?v={{ config('app.version', '1.0') }}">
    
    <!-- Custom CSS -->
    <style>
        :root {
            --primary-color: {{ setting('theme.primary_color', '#1e40af') }};
            --secondary-color: {{ setting('theme.secondary_color', '#64748b') }};
            --success-color: {{ setting('theme.success_color', '#059669') }};
            --warning-color: {{ setting('theme.warning_color', '#d97706') }};
            --danger-color: {{ setting('theme.danger_color', '#dc2626') }};
            --sidebar-width: {{ setting('theme.sidebar_width', '250') }}px;
            --sidebar-collapsed-width: 1px;
            --font-family: {{ setting('theme.font_family', 'Inter, sans-serif') }};
            --font-size: {{ setting('theme.font_size', '14') }}px;
            --border-radius: {{ setting('theme.border_radius', '0.5') }}rem;
            --topbar-height: 60px;
            --bottom-nav-height: 60px;
        }
        
        @media (max-width: 576px) {
            :root {
                --font-size: 16px; 
                --topbar-height: 56px;
                --sidebar-width: 280px;
            }
        }
        
        body {
            font-family: var(--font-family);
            font-size: var(--font-size);
            background-color: #f8fafc;
            margin: 0;
            padding: 0;
            -webkit-font-smoothing: antialiased;
            -moz-osx-font-smoothing: grayscale;
            overscroll-behavior: none;
            @if(setting('appearance.background_image'))
                position: relative;
            @endif
        }
        
        @if(setting('appearance.background_image'))
        body::before {
            content: '';
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-image: url('{{ setting('appearance.background_image') }}');
            background-size: cover;
            background-position: center;
            background-repeat: no-repeat;
            opacity: {{ setting('appearance.background_opacity', 0.1) }};
            z-index: -1;
        }
        @endif
        
        /* Enhanced Mobile Topbar */
        .topbar {
            height: var(--topbar-height);
            background: white;
            border-bottom: 1px solid #e5e7eb;
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            z-index: 1001;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
            display: flex;
            align-items: center;
        }
        
        .topbar .container-fluid {
            padding: 0 1rem;
            height: 100%;
            display: flex;
            align-items: center;
        }
        
        .topbar .d-flex {
            height: 100%;
            align-items: center;
        }
        
        .topbar .brand-section,
        .topbar .breadcrumb,
        .topbar .dropdown,
        .topbar .btn,
        .topbar .mobile-menu-toggle {
            display: flex;
            align-items: center;
        }
        
        .topbar .breadcrumb {
            margin-bottom: 0;
        }
        
        .topbar .breadcrumb-item {
            display: flex;
            align-items: center;
        }
        
        @media (max-width: 576px) {
            .topbar .container-fluid {
                padding: 0 0.75rem;
            }
        }
        
        /* Mobile-Optimized Sidebar */
        .sidebar {
            width: var(--sidebar-width);
            height: 100vh;
            background: linear-gradient(180deg, var(--primary-color) 0%, #1e3a8a 100%);
            color: white;
            position: fixed;
            top: 0;
            left: 0;
            z-index: 1000;
            transform: translateX(-100%);
            transition: transform 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            overflow-y: auto;
            overflow-x: hidden;
            -webkit-overflow-scrolling: touch;
        }
        
        .sidebar.show {
            transform: translateX(0);
        }
        
        .sidebar-content {
            padding: 1rem;
            padding-top: calc(var(--topbar-height) + 1rem);
            height: 100vh;
            display: flex;
            flex-direction: column;
        }
        
        .sidebar-footer {
            margin-top: auto;
            padding-top: 1rem;
            border-top: 1px solid rgba(255, 255, 255, 0.1);
        }
        
        .sidebar-overlay {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.5);
            z-index: 999;
            opacity: 0;
            visibility: hidden;
            transition: all 0.3s ease;
            backdrop-filter: blur(2px);
            -webkit-backdrop-filter: blur(2px);
        }
        
        .sidebar-overlay.show {
            opacity: 1;
            visibility: visible;
        }
        
        /* Mobile-First Navigation */
        .nav-link {
            color: rgba(255, 255, 255, 0.8) !important;
            padding: 0.75rem 1rem;
            border-radius: var(--border-radius);
            margin: 0.25rem 0;
            transition: all 0.2s ease;
            display: flex;
            align-items: center;
            white-space: nowrap;
            overflow: hidden;
            min-height: 48px; /* Touch-friendly */
        }
        
        .nav-link:hover, .nav-link:focus {
            background-color: rgba(255, 255, 255, 0.1);
            color: white !important;
            outline: none;
        }
        
        .nav-link.active {
            background-color: rgba(255, 255, 255, 0.2);
            color: white !important;
        }
        
        .nav-item .nav-link i {
            width: 24px;
            height: 24px;
            text-align: center;
            margin-right: 12px;
            flex-shrink: 0;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        
        /* Brand Section Mobile Optimization */
        .brand-section {
            display: flex;
            align-items: center;
            font-weight: 700;
            font-size: 1.1rem;
            color: var(--primary-color);
            white-space: nowrap;
            overflow: hidden;
            flex: 1;
            min-width: 0;
            height: 100%;
        }
        
        .brand-section img {
            height: 32px;
            width: auto;
            margin-right: 0.5rem;
            flex-shrink: 0;
        }
        
        .brand-section span {
            overflow: hidden;
            text-overflow: ellipsis;
            line-height: 1;
        }
        
        @media (max-width: 576px) {
            .brand-section {
                font-size: 1rem;
            }
            
            .brand-section img {
                height: 28px;
                margin-right: 0.25rem;
            }
        }
        
        /* Mobile Menu Toggle Button */
        .mobile-menu-toggle {
            min-width: 40px;
            min-height: 40px;
            display: flex;
            align-items: center;
            justify-content: center;
            border: none;
            background: transparent;
            color: var(--primary-color);
            border-radius: var(--border-radius);
            transition: all 0.2s ease;
            margin-right: 0.5rem;
            flex-shrink: 0;
        }
        
        .mobile-menu-toggle:hover, .mobile-menu-toggle:focus {
            background-color: rgba(0, 0, 0, 0.05);
            outline: none;
        }
        
        .mobile-menu-toggle i {
            font-size: 1.25rem;
            line-height: 1;
        }
        
        /* Main Content Layout */
        .main-content {
            margin-left: 0;
            margin-top: var(--topbar-height);
            padding: 0;
            min-height: calc(100vh - var(--topbar-height));
            transition: margin-left 0.3s ease;
        }
        
        .main-content .container-fluid {
            padding: 1rem;
        }
        
        @media (max-width: 576px) {
            .main-content .container-fluid {
                padding: 0.75rem;
            }
        }
        
        .bottom-nav {
            display: none;
            position: fixed;
            bottom: 0;
            left: 0;
            right: 0;
            height: var(--bottom-nav-height);
            background: white;
            border-top: 1px solid #e5e7eb;
            z-index: 1000;
            box-shadow: 0 -2px 4px rgba(0, 0, 0, 0.1);
        }
        
        @media (max-width: 768px) {
            .bottom-nav {
                display: flex;
            }
            
            .main-content {
                margin-bottom: var(--bottom-nav-height);
            }
        }
        
        .bottom-nav-item {
            flex: 1;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            text-decoration: none;
            color: var(--secondary-color);
            padding: 0.5rem;
            transition: all 0.2s ease;
            min-height: 44px;
        }
        
        .bottom-nav-item:hover, .bottom-nav-item.active {
            color: var(--primary-color);
            text-decoration: none;
        }
        
        .bottom-nav-item i {
            font-size: 1.25rem;
            margin-bottom: 0.25rem;
        }
        
        .bottom-nav-item span {
            font-size: 0.75rem;
            font-weight: 500;
        }
        
        .notification-badge {
            background-color: var(--danger-color);
            color: white;
            border-radius: 50%;
            padding: 0.25rem 0.5rem;
            font-size: 0.75rem;
            min-width: 1.5rem;
            height: 1.5rem;
            display: inline-flex;
            align-items: center;
            justify-content: center;
        }
        
        /* Mobile Dropdown*/
        .dropdown-menu {
            border: none;
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.15);
            border-radius: var(--border-radius);
        }
        
        .topbar .dropdown {
            position: relative;
            display: flex;
            align-items: center;
            height: 100%;
        }
        
        .topbar .btn {
            min-width: 40px;
            min-height: 40px;
            height: 40px;
            padding: 0.5rem;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: var(--border-radius);
            line-height: 1;
            white-space: nowrap;
        }
        
        .topbar .dropdown-toggle {
            min-width: auto;
            padding-left: 0.5rem;
            padding-right: 0.5rem;
        }
        
        .topbar .dropdown-toggle::after {
            margin-left: 0.25rem;
            vertical-align: middle;
        }
        
        .topbar .btn i {
            font-size: 1.1rem;
            line-height: 1;
        }
        
        .topbar .btn .me-1,
        .topbar .btn .me-2 {
            margin-right: 0.25rem !important;
        }
        
        .topbar .btn span {
            line-height: 1;
        }
        
        /*Breadcrumb*/
        .breadcrumb {
            background: none;
            padding: 0;
            margin: 0;
            font-size: 0.9rem;
        }
        
        .breadcrumb-item {
            line-height: 1;
        }
        
        .breadcrumb-item + .breadcrumb-item::before {
            float: none;
            padding-right: 0.5rem;
            color: #6c757d;
            content: var(--bs-breadcrumb-divider, "/");
        }
        
        @media (max-width: 576px) {
            .dropdown-menu {
                width: 95vw !important;
                max-width: 300px;
                left: 50% !important;
                transform: translateX(-50%) !important;
                margin-top: 0.5rem !important;
            }
            
            .dropdown-menu[data-bs-popper] {
                left: 50% !important;
                transform: translateX(-50%) !important;
            }
            
            /* Fix notification dropdown positioning */
            .dropdown-menu[style*="width: 300px"] {
                width: 90vw !important;
                max-width: 320px !important;
                right: 0.5rem !important;
                left: auto !important;
                transform: none !important;
            }
            
            .topbar .btn {
                min-width: 36px;
                min-height: 36px;
                height: 36px;
                padding: 0.375rem;
            }
            
            .topbar .dropdown-toggle {
                padding-left: 0.375rem;
                padding-right: 0.375rem;
            }
            
            .topbar .btn i {
                font-size: 1rem;
            }
        }
        
        /* Enhanced Mobile Cards */
        .card {
            border: 0;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
            border-radius: var(--border-radius);
            margin-bottom: 1rem;
        }
        
        @media (max-width: 576px) {
            .card {
                margin-bottom: 0.75rem;
            }
            
            .card-body {
                padding: 1rem;
            }
        }
        
        /* Page Header Mobile Optimization */
        .page-header {
            background: white;
            border-radius: var(--border-radius);
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
            margin-bottom: 1.5rem;
            padding: 1.5rem;
        }
        
        @media (max-width: 576px) {
            .page-header {
                padding: 1rem;
                margin-bottom: 1rem;
            }
            
            .page-header h1 {
                font-size: 1.5rem;
            }
        }
        
        /* Mobile Button Optimizations */
        .btn {
            border-radius: var(--border-radius);
            font-weight: 500;
            padding: 0.5rem 1rem;
            min-height: 44px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
        }
        
        @media (max-width: 576px) {
            .btn {
                padding: 0.625rem 1rem;
            }
            
            .btn-sm {
                min-height: 38px;
                padding: 0.375rem 0.75rem;
            }
        }
        
        /* Mobile Table Responsiveness */
        .table-responsive {
            border-radius: var(--border-radius);
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }
        
        @media (max-width: 768px) {
            .table-responsive {
                font-size: 0.875rem;
            }
            
            .table th, .table td {
                padding: 0.5rem;
                white-space: nowrap;
            }
        }
        
        /* Mobile Form Optimizations */
        .form-control, .form-select {
            min-height: 44px;
            border-radius: var(--border-radius);
        }
        
        @media (max-width: 576px) {
            .form-control, .form-select {
                font-size: 16px; /* Prevent zoom on iOS */
            }
        }
        
        /* Status and Priority Styles */
        .status-badge {
            padding: 0.25rem 0.75rem;
            border-radius: 9999px;
            font-size: 0.75rem;
            font-weight: 600;
            white-space: nowrap;
        }
        
        .status-pending { background-color: #fef3c7; color: #92400e; }
        .status-in-progress { background-color: #dbeafe; color: #1e40af; }
        .status-completed { background-color: #d1fae5; color: #065f46; }
        .status-cancelled { background-color: #fee2e2; color: #991b1b; }
        
        .priority-low { color: var(--success-color); }
        .priority-normal { color: var(--secondary-color); }
        .priority-high { color: var(--warning-color); }
        .priority-urgent { color: var(--danger-color); }
        
        /* Desktop Sidebar Auto-hide */
        @media (min-width: 769px) {
            .sidebar {
                transform: translateX(0);
                top: var(--topbar-height);
                height: calc(100vh - var(--topbar-height));
            }
            
            .sidebar.collapsed {
                width: var(--sidebar-collapsed-width);
            }
            
            .sidebar.auto-hide:not(:hover):not(.pinned) {
                width: var(--sidebar-collapsed-width);
            }
            
            .sidebar.auto-hide:not(:hover):not(.pinned) .nav-text {
                opacity: 0;
            }
            
            .sidebar.auto-hide:not(:hover):not(.pinned) .nav-section-header {
                opacity: 0;
            }
            
            .sidebar.auto-hide:not(:hover):not(.pinned) .notification-badge {
                display: none;
            }
            
            .sidebar.auto-hide:not(:hover):not(.pinned) .nav-link {
                padding: 0.75rem;
                justify-content: center;
            }
            
            .sidebar.auto-hide:not(:hover):not(.pinned) .nav-item .nav-link i {
                margin-right: 0;
            }
            
            .main-content {
                margin-left: var(--sidebar-width);
            }
            
            .main-content.sidebar-collapsed {
                margin-left: var(--sidebar-collapsed-width);
            }
        }
        
        /* Loading States */
        .loading {
            display: none;
        }
        
        .loading.show {
            display: block;
        }
        
        /* Mobile-specific utilities */
        @media (max-width: 576px) {
            .d-mobile-none {
                display: none !important;
            }
            
            .d-mobile-block {
                display: block !important;
            }
            
            .d-mobile-flex {
                display: flex !important;
            }
            
            .min-w-0 {
                min-width: 0 !important;
            }
            
            .gap-1 {
                gap: 0.25rem !important;
            }
        }
        
        /* Smooth scrolling for better mobile experience */
        html {
            scroll-behavior: smooth;
        }
        
        /* Prevent horizontal scroll on mobile */
        body, html {
            overflow-x: hidden;
        }
        
        /* Touch-friendly spacing */
        @media (max-width: 768px) {
            .btn-group .btn {
                margin-bottom: 0.5rem;
            }
            
            .btn-toolbar .btn-group {
                margin-bottom: 0.5rem;
            }
        }
    </style>
    
    @stack('styles')
</head>
<body>
    <!-- Top Navigation Bar -->
    <nav class="topbar">
        <div class="container-fluid">
            <div class="d-flex align-items-center justify-content-between w-100">
                <!-- Left Section: Menu Toggle & Brand -->
                <div class="d-flex align-items-center flex-grow-1 min-w-0 me-3">
                    <!-- Mobile Menu Toggle -->
                    <button class="mobile-menu-toggle d-md-none" type="button" id="sidebarToggle" aria-label="Toggle navigation">
                        <i class="bi bi-list"></i>
                    </button>
                    
                    <!-- Brand/Logo -->
                    <div class="brand-section">
                        @if(setting('appearance.logo'))
                            <img src="{{ setting('appearance.logo') }}" 
                                alt="{{ setting('system.name', config('app.name')) }}">
                        @endif
                        <span class="d-none d-sm-inline">{{ setting('system.name', config('app.name')) }}</span>
                        <span class="d-sm-none">{{ Str::limit(setting('system.name', config('app.name')), 15) }}</span>
                    </div>
                    
                    <!-- Breadcrumb (Hidden on mobile) -->
                    <nav aria-label="breadcrumb" class="d-none d-lg-block ms-4 me-4">
                        <ol class="breadcrumb mb-0">
                            @yield('breadcrumbs')
                        </ol>
                    </nav>
                </div>
                
                <!-- Right Section: Notifications & User Menu -->
                <div class="d-flex align-items-center gap-1">
                    <!-- Notifications -->
                    <div class="dropdown">
                        <button class="btn btn-outline-secondary position-relative" type="button" id="notificationsDropdown" data-bs-toggle="dropdown" aria-label="Notifications">
                            <i class="bi bi-bell"></i>
                            <span class="notification-badge position-absolute top-0 start-100 translate-middle" id="notificationCount" style="display: none;">0</span>
                        </button>
                        <div class="dropdown-menu dropdown-menu-end" style="width: 300px;">
                            <h6 class="dropdown-header">Notifications</h6>
                            <div id="notificationsList">
                                <div class="text-center py-3">
                                    <div class="loading show">Loading...</div>
                                </div>
                            </div>
                            <div class="dropdown-divider"></div>
                            <a class="dropdown-item text-center" href="{{ route('notifications.index') }}">View All</a>
                        </div>
                    </div>
                    
                    <!-- User Menu -->
                    <div class="dropdown">
                        <button class="btn btn-outline-secondary dropdown-toggle d-flex align-items-center justify-content-center" type="button" id="userDropdown" data-bs-toggle="dropdown" aria-label="User menu">
                            <i class="bi bi-person-circle d-none d-sm-inline me-1"></i>
                            <span class="d-none d-md-inline">{{ Auth::user()->name }}</span>
                            <span class="d-md-none d-sm-inline">{{ Str::limit(Auth::user()->name, 8) }}</span>
                            <i class="bi bi-person-circle d-sm-none"></i>
                        </button>
                        <ul class="dropdown-menu dropdown-menu-end">
                            <li><h6 class="dropdown-header">{{ Auth::user()->email }}</h6></li>
                            <li><span class="dropdown-item-text small text-muted">{{ Auth::user()->department?->name ?? 'No Department' }}</span></li>
                            <li><hr class="dropdown-divider"></li>
                            <li><a class="dropdown-item" href="{{ route('profile.show') }}"><i class="bi bi-person me-2"></i>Profile</a></li>
                            <li><hr class="dropdown-divider"></li>
                            <li>
                                <form method="POST" action="{{ route('logout') }}">
                                    @csrf
                                    <button type="submit" class="dropdown-item">
                                        <i class="bi bi-box-arrow-right me-2"></i>Logout
                                    </button>
                                </form>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </nav>
    
    <!-- Sidebar Overlay -->
    <div class="sidebar-overlay" id="sidebarOverlay"></div>
    
    <!-- Include Navigation Sidebar -->
    @include('partials.navigation')
    
    <!-- Main Content -->
    <div class="main-content" id="mainContent">
        <!-- Page Content Container -->
        <div class="container-fluid">
            <!-- Flash Messages -->
            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <i class="bi bi-check-circle me-2"></i>
                    {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif
            
            @if(session('error'))
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <i class="bi bi-exclamation-triangle me-2"></i>
                    {{ session('error') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif
            
            @if(session('warning'))
                <div class="alert alert-warning alert-dismissible fade show" role="alert">
                    <i class="bi bi-exclamation-triangle me-2"></i>
                    {{ session('warning') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif
            
            @if(session('info'))
                <div class="alert alert-info alert-dismissible fade show" role="alert">
                    <i class="bi bi-info-circle me-2"></i>
                    {{ session('info') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif
            
            <!-- Page Header -->
            @hasSection('page-header')
                <div class="page-header">
                    @yield('page-header')
                </div>
            @endif
            
            <!-- Main Content -->
            @yield('content')
        </div>
    </div>
    
    <!-- Optional Bottom Navigation for Mobile -->
    <nav class="bottom-nav">
        <a href="{{ route('dashboard') }}" class="bottom-nav-item {{ request()->routeIs('dashboard') ? 'active' : '' }}">
            <i class="bi bi-house"></i>
            <span>Home</span>
        </a>
        <a href="{{ route('documents.index') }}" class="bottom-nav-item {{ request()->routeIs('documents.*') ? 'active' : '' }}">
            <i class="bi bi-file-earmark"></i>
            <span>Documents</span>
        </a>
        <a href="{{ route('notifications.index') }}" class="bottom-nav-item {{ request()->routeIs('notifications.*') ? 'active' : '' }}">
            <i class="bi bi-bell"></i>
            <span>Alerts</span>
        </a>
        <a href="{{ route('profile.show') }}" class="bottom-nav-item {{ request()->routeIs('profile.*') ? 'active' : '' }}">
            <i class="bi bi-person"></i>
            <span>Profile</span>
        </a>
    </nav>
    
    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    
    <!-- Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    
    <!-- Custom JavaScript -->
    <script>
        // CSRF Token Setup
        window.Laravel = {
            csrfToken: '{{ csrf_token() }}'
        };
        
        // Enhanced Mobile-First Sidebar Manager
        class SidebarManager {
            constructor() {
                this.sidebar = document.getElementById('sidebar');
                this.sidebarToggle = document.getElementById('sidebarToggle');
                this.sidebarOverlay = document.getElementById('sidebarOverlay');
                this.autoHideToggle = document.getElementById('autoHideToggle');
                this.autoHideIcon = document.getElementById('autoHideIcon');
                this.mainContent = document.getElementById('mainContent');
                
                this.isAutoHideEnabled = localStorage.getItem('sidebarAutoHide') !== 'false';
                this.isPinned = false;
                this.isMobile = window.innerWidth < 769;
                
                this.init();
            }
            
            init() {
                this.setupEventListeners();
                this.updateAutoHideState();
                this.updateSidebarClasses();
                this.handleResize();
            }
            
            setupEventListeners() {
                // Mobile toggle
                this.sidebarToggle?.addEventListener('click', (e) => {
                    e.preventDefault();
                    this.toggleMobileSidebar();
                });
                
                // Overlay click (mobile)
                this.sidebarOverlay?.addEventListener('click', () => {
                    this.hideMobileSidebar();
                });
                
                // Auto-hide toggle
                this.autoHideToggle?.addEventListener('click', () => {
                    this.toggleAutoHide();
                });
                
                // Enhanced click outside detection
                document.addEventListener('click', (e) => {
                    if (this.isMobile && 
                        this.sidebar?.classList.contains('show') &&
                        !this.sidebar.contains(e.target) && 
                        !this.sidebarToggle?.contains(e.target)) {
                        this.hideMobileSidebar();
                    }
                });
                
                // Touch events for better mobile experience
                let touchStartX = 0;
                let touchEndX = 0;
                
                document.addEventListener('touchstart', (e) => {
                    touchStartX = e.changedTouches[0].screenX;
                });
                
                document.addEventListener('touchend', (e) => {
                    touchEndX = e.changedTouches[0].screenX;
                    this.handleSwipeGesture();
                });
                
                // Desktop auto-hide behavior
                if (!this.isMobile) {
                    this.sidebar?.addEventListener('mouseenter', () => {
                        if (this.isAutoHideEnabled && !this.isPinned) {
                            this.expandSidebar();
                        }
                    });
                    
                    this.sidebar?.addEventListener('mouseleave', () => {
                        if (this.isAutoHideEnabled && !this.isPinned) {
                            this.collapseSidebar();
                        }
                    });
                }
                
                // Handle window resize
                window.addEventListener('resize', () => {
                    this.handleResize();
                });
                
                // Prevent body scroll when sidebar is open on mobile
                this.sidebar?.addEventListener('touchmove', (e) => {
                    if (this.isMobile && this.sidebar.classList.contains('show')) {
                        e.stopPropagation();
                    }
                });
            }
            
            handleSwipeGesture() {
                const swipeThreshold = 50;
                const swipeDistance = touchEndX - touchStartX;
                
                if (this.isMobile) {
                    // Swipe right to open sidebar
                    if (swipeDistance > swipeThreshold && touchStartX < 50) {
                        this.showMobileSidebar();
                    }
                    // Swipe left to close sidebar
                    else if (swipeDistance < -swipeThreshold && this.sidebar?.classList.contains('show')) {
                        this.hideMobileSidebar();
                    }
                }
            }
            
            handleResize() {
                const wasMobile = this.isMobile;
                this.isMobile = window.innerWidth < 769;
                
                if (wasMobile !== this.isMobile) {
                    // Reset sidebar state when switching between mobile and desktop
                    this.hideMobileSidebar();
                    this.updateSidebarClasses();
                }
            }
            
            toggleMobileSidebar() {
                if (this.sidebar?.classList.contains('show')) {
                    this.hideMobileSidebar();
                } else {
                    this.showMobileSidebar();
                }
            }
            
            showMobileSidebar() {
                this.sidebar?.classList.add('show');
                this.sidebarOverlay?.classList.add('show');
                document.body.style.overflow = 'hidden'; // Prevent background scroll
            }
            
            hideMobileSidebar() {
                this.sidebar?.classList.remove('show');
                this.sidebarOverlay?.classList.remove('show');
                document.body.style.overflow = ''; // Restore scroll
            }
            
            toggleAutoHide() {
                this.isAutoHideEnabled = !this.isAutoHideEnabled;
                localStorage.setItem('sidebarAutoHide', this.isAutoHideEnabled);
                this.updateAutoHideState();
                this.updateSidebarClasses();
            }
            
            updateAutoHideState() {
                if (this.autoHideIcon) {
                    this.autoHideIcon.className = this.isAutoHideEnabled ? 
                        'bi bi-pin-angle' : 'bi bi-pin-angle-fill';
                }
                
                if (this.autoHideToggle) {
                    this.autoHideToggle.title = this.isAutoHideEnabled ? 
                        'Disable Auto-hide' : 'Enable Auto-hide';
                }
            }
            
            updateSidebarClasses() {
                if (!this.isMobile) {
                    if (this.isAutoHideEnabled) {
                        this.sidebar?.classList.add('auto-hide');
                        this.collapseSidebar();
                    } else {
                        this.sidebar?.classList.remove('auto-hide');
                        this.expandSidebar();
                    }
                } else {
                    this.sidebar?.classList.remove('auto-hide');
                }
            }
            
            expandSidebar() {
                if (!this.isMobile) {
                    this.sidebar?.classList.remove('collapsed');
                    this.mainContent?.classList.remove('sidebar-collapsed');
                }
            }
            
            collapseSidebar() {
                if (!this.isMobile && this.isAutoHideEnabled && !this.isPinned) {
                    this.sidebar?.classList.add('collapsed');
                    this.mainContent?.classList.add('sidebar-collapsed');
                }
            }
        }
        
        // Initialize sidebar manager
        document.addEventListener('DOMContentLoaded', function() {
            new SidebarManager();
            
            // Add loading states to forms
            const forms = document.querySelectorAll('form');
            forms.forEach(form => {
                form.addEventListener('submit', function() {
                    const submitBtn = form.querySelector('button[type="submit"]');
                    if (submitBtn) {
                        submitBtn.disabled = true;
                        submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Loading...';
                    }
                });
            });
        });
        
        // Auto-hide alerts with better mobile timing
        setTimeout(function() {
            const alerts = document.querySelectorAll('.alert');
            alerts.forEach(function(alert) {
                if (alert.classList.contains('alert-success') || 
                    alert.classList.contains('alert-info')) {
                    const bsAlert = new bootstrap.Alert(alert);
                    bsAlert.close();
                }
            });
        }, window.innerWidth < 769 ? 7000 : 5000); // Longer duration on mobile
        
        // Enhanced notification functionality
        function loadNotifications() {
            fetch('/api/notifications/recent')
                .then(response => response.json())
                .then(data => {
                    updateNotificationUI(data.notifications);
                })
                .catch(error => {
                    console.error('Error loading notifications:', error);
                });
                
            fetch('/api/notifications/unread-count')
                .then(response => response.json())
                .then(data => {
                    updateNotificationCount(data.count);
                })
                .catch(error => {
                    console.error('Error loading notification count:', error);
                });
        }
        
        function updateNotificationCount(count) {
            const badge = document.getElementById('notificationCount');
            if (count > 0) {
                badge.textContent = count > 99 ? '99+' : count;
                badge.style.display = 'block';
            } else {
                badge.style.display = 'none';
            }
        }
        
        function updateNotificationUI(notifications) {
            const container = document.getElementById('notificationsList');
            container.innerHTML = '';
            
            if (notifications.length === 0) {
                container.innerHTML = '<div class="text-center py-3 text-muted">No notifications</div>';
                return;
            }
            
            notifications.forEach(notification => {
                const item = document.createElement('div');
                item.className = `dropdown-item ${!notification.is_read ? 'bg-light' : ''}`;
                item.style.cursor = 'pointer';
                item.innerHTML = `
                    <div class="d-flex justify-content-between align-items-start">
                        <div class="flex-grow-1">
                            <h6 class="mb-1">${notification.title}</h6>
                            <p class="mb-1 small text-muted">${notification.message}</p>
                            <small class="text-muted">${formatDate(notification.created_at)}</small>
                        </div>
                        ${!notification.is_read ? '<i class="bi bi-circle-fill text-primary ms-2" style="font-size: 0.5rem;"></i>' : ''}
                    </div>
                `;
                
                item.addEventListener('click', function() {
                    if (!notification.is_read) {
                        markNotificationAsRead(notification.id);
                    }
                    if (notification.document_id) {
                        window.location.href = `/documents/${notification.document_id}`;
                    }
                });
                
                container.appendChild(item);
            });
        }
        
        function markNotificationAsRead(notificationId) {
            fetch(`/api/notifications/${notificationId}/read`, {
                method: 'PATCH',
                headers: {
                    'X-CSRF-TOKEN': window.Laravel.csrfToken,
                    'Content-Type': 'application/json',
                },
            }).catch(error => {
                console.error('Error marking notification as read:', error);
            });
        }
        
        function formatDate(dateString) {
            const date = new Date(dateString);
            const now = new Date();
            const diff = now - date;
            const minutes = Math.floor(diff / 60000);
            const hours = Math.floor(diff / 3600000);
            const days = Math.floor(diff / 86400000);
            
            if (minutes < 1) return 'Just now';
            if (minutes < 60) return `${minutes}m ago`;
            if (hours < 24) return `${hours}h ago`;
            if (days < 7) return `${days}d ago`;
            return date.toLocaleDateString();
        }
        
        // Load notifications on page load
        document.addEventListener('DOMContentLoaded', function() {
            loadNotifications();
            
            // Refresh notifications every 60 seconds on mobile, 30 on desktop
            const refreshInterval = window.innerWidth < 769 ? 60000 : 30000;
            setInterval(loadNotifications, refreshInterval);
        });
        
        // Global AJAX setup
        if (window.fetch) {
            const originalFetch = window.fetch;
            window.fetch = function(url, options = {}) {
                options.headers = {
                    'X-CSRF-TOKEN': window.Laravel.csrfToken,
                    'X-Requested-With': 'XMLHttpRequest',
                    ...options.headers
                };
                return originalFetch(url, options);
            };
        }
        
        // Add touch feedback for mobile
        if ('ontouchstart' in window) {
            document.addEventListener('touchstart', function() {}, {passive: true});
        }
    </script>
    
    @stack('scripts')
</body>
</html>