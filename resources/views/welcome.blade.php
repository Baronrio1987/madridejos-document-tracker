<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no">
    <meta name="mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="default">
    <meta name="theme-color" content="{{ setting('theme.primary_color', '#1e40af') }}">
    
    <title>{{ setting('system.name', config('app.name')) }} - {{ setting('municipality.name', 'Municipality of Madridejos') }}</title>
    
    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family={{ urlencode(setting('theme.font_family', 'inter:400,500,600,700')) }}&display=swap" rel="stylesheet" />
    
    <!-- Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css" rel="stylesheet">
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Favicon & Apple Touch Icons -->
    @if(setting('appearance.favicon'))
    <link rel="icon" type="image/x-icon" href="{{ setting('appearance.favicon') }}">
    <link rel="apple-touch-icon" sizes="180x180" href="{{ setting('appearance.favicon') }}">
    @else
    <link rel="icon" type="image/x-icon" href="{{ asset('favicon.ico') }}">
    <link rel="apple-touch-icon" sizes="180x180" href="{{ asset('favicon.ico') }}">
    @endif
    
    <!-- Dynamic Styles -->
    <link rel="stylesheet" href="{{ route('dynamic-styles.css') }}?v={{ config('app.version', '1.0') }}">
    
    <style>
        :root {
            --primary-color: {{ setting('theme.primary_color', '#1e40af') }};
            --warning-color: {{ setting('theme.warning_color', '#d97706') }};
            --font-family: {{ setting('theme.font_family', 'Inter, sans-serif') }};
            --hero-height: 100vh;
        }
        
        /* Mobile viewport adjustments */
        @media (max-width: 768px) {
            :root {
                --hero-height: calc(100vh - 60px);
            }
        }
        
        /* Base Styles */
        body {
            font-family: var(--font-family);
            overflow-x: hidden;
            margin: 0;
            padding: 0;
            -webkit-font-smoothing: antialiased;
            -moz-osx-font-smoothing: grayscale;
            position: relative;
            @if(setting('appearance.background_image'))
                background-image: url('{{ setting('appearance.background_image') }}');
                background-size: cover;
                background-position: center;
                background-repeat: no-repeat;
                background-attachment: fixed;
            @endif
        }
        
        /* Gradient overlay */
        body::before {
            content: '';
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            @if(setting('appearance.background_image'))
                @php
                    $primaryColor = setting('theme.primary_color', '#1e40af');
                    $opacity = setting('appearance.background_opacity', 0.8);
                    // Convert hex to RGB
                    $hex = ltrim($primaryColor, '#');
                    $r = hexdec(substr($hex, 0, 2));
                    $g = hexdec(substr($hex, 2, 2));
                    $b = hexdec(substr($hex, 4, 2));
                @endphp
                background: linear-gradient(135deg, 
                    rgba({{ $r }}, {{ $g }}, {{ $b }}, {{ $opacity }}) 0%, 
                    rgba(118, 75, 162, {{ $opacity }}) 100%);
            @else
                background: linear-gradient(135deg, var(--primary-color) 0%, #764ba2 100%);
            @endif
            z-index: -1;
        }
        
        /* Enhanced Navigation */
        .navbar {
            padding: 0.75rem 0;
            backdrop-filter: blur(20px);
            -webkit-backdrop-filter: blur(20px);
            background: rgba(255, 255, 255, 0.95) !important;
            box-shadow: 0 2px 20px rgba(0, 0, 0, 0.1);
            transition: all 0.3s ease;
        }
        
        .navbar.scrolled {
            background: rgba(255, 255, 255, 0.98) !important;
            box-shadow: 0 2px 30px rgba(0, 0, 0, 0.15);
        }
        
        .navbar-brand {
            font-size: 1.25rem;
            font-weight: 700;
            color: var(--primary-color) !important;
            transition: all 0.2s ease;
        }
        
        .navbar-brand:hover {
            transform: scale(1.05);
        }
        
        .navbar-brand img {
            height: 36px;
            width: auto;
            transition: all 0.2s ease;
        }
        
        .navbar-toggler {
            border: none;
            padding: 0.5rem;
            border-radius: 0.5rem;
            transition: all 0.2s ease;
        }
        
        .navbar-toggler:focus {
            box-shadow: none;
            background-color: rgba(0, 0, 0, 0.05);
        }
        
        /* Mobile Navigation Styles */
        @media (max-width: 768px) {
            .navbar {
                padding: 0.5rem 0;
            }
            
            .navbar-brand {
                font-size: 1.1rem;
            }
            
            .navbar-brand img {
                height: 32px;
            }
            
            .navbar-collapse {
                margin-top: 1rem;
                padding-top: 1rem;
                border-top: 1px solid rgba(0, 0, 0, 0.1);
            }
        }
        
        @media (max-width: 576px) {
            .navbar-brand {
                font-size: 1rem;
            }
            
            .navbar-brand img {
                height: 28px;
            }
        }
        
        /* Hero Section */
        .hero-section {
            @if(!setting('appearance.background_image'))
                background: linear-gradient(135deg, var(--primary-color) 0%, #764ba2 100%);
            @endif
            min-height: var(--hero-height);
            display: flex;
            align-items: center;
            color: white;
            padding: 120px 0 60px;
            position: relative;
            overflow: hidden;
        }
        
        .hero-section::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 100 100"><defs><pattern id="grid" width="10" height="10" patternUnits="userSpaceOnUse"><path d="M 10 0 L 0 0 0 10" fill="none" stroke="rgba(255,255,255,0.05)" stroke-width="0.5"/></pattern></defs><rect width="100" height="100" fill="url(%23grid)"/></svg>');
            opacity: 0.3;
        }
        
        .hero-content {
            position: relative;
            z-index: 2;
        }
        
        .hero-icon {
            font-size: 8rem;
            opacity: 0.2;
            animation: float 6s ease-in-out infinite;
        }
        
        @keyframes float {
            0%, 100% { transform: translateY(0px); }
            50% { transform: translateY(-20px); }
        }
        
        .hero-title {
            text-shadow: 0 4px 8px rgba(0, 0, 0, 0.3);
            line-height: 1.2;
            margin-bottom: 1.5rem;
        }
        
        .hero-subtitle {
            text-shadow: 0 2px 4px rgba(0, 0, 0, 0.2);
            margin-bottom: 2rem;
            opacity: 0.95;
        }
        
        /* Mobile Hero Adjustments */
        @media (max-width: 768px) {
            .hero-section {
                min-height: calc(100vh - 80px);
                padding: 80px 0 40px;
                text-align: center;
            }
            
            .hero-title {
                font-size: 2.25rem;
                margin-bottom: 1rem;
            }
            
            .hero-subtitle {
                font-size: 1.1rem;
                margin-bottom: 1.5rem;
            }
            
            .hero-icon {
                font-size: 6rem;
                margin-top: 2rem;
            }
        }
        
        @media (max-width: 576px) {
            .hero-section {
                padding: 60px 0 30px;
            }
            
            .hero-title {
                font-size: 2rem;
                line-height: 1.3;
            }
            
            .hero-subtitle {
                font-size: 1rem;
            }
            
            .hero-icon {
                font-size: 4.5rem;
                margin-top: 1.5rem;
            }
        }
        
        /* Enhanced Button Styles */
        .btn {
            font-weight: 600;
            padding: 0.75rem 2rem;
            border-radius: 0.75rem;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            border: 2px solid transparent;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            min-height: 48px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }
        
        .btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 15px rgba(0, 0, 0, 0.2);
        }
        
        .btn:active {
            transform: translateY(0);
        }
        
        .btn-primary {
            background-color: var(--primary-color);
            border-color: var(--primary-color);
            color: white;
        }
        
        .btn-primary:hover {
            background-color: #1e3a8a;
            border-color: #1e3a8a;
            color: white;
        }
        
        .btn-warning {
            background-color: var(--warning-color);
            border-color: var(--warning-color);
            color: white;
        }
        
        .btn-warning:hover {
            background-color: #b45309;
            border-color: #b45309;
            color: white;
        }
        
        .btn-outline-light {
            border-color: rgba(255, 255, 255, 0.5);
            color: white;
            background-color: rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(10px);
        }
        
        .btn-outline-light:hover {
            background-color: white;
            border-color: white;
            color: var(--primary-color);
        }
        
        .btn-outline-primary {
            border-color: var(--primary-color);
            color: var(--primary-color);
        }
        
        .btn-outline-primary:hover {
            background-color: var(--primary-color);
            border-color: var(--primary-color);
            color: white;
        }
        
        .btn-outline-secondary {
            border-color: #6b7280;
            color: #6b7280;
        }
        
        .btn-outline-secondary:hover {
            background-color: #6b7280;
            border-color: #6b7280;
            color: white;
        }
        
        /* Mobile Button Adjustments */
        @media (max-width: 768px) {
            .btn {
                padding: 0.75rem 1.5rem;
                font-size: 0.95rem;
                width: 100%;
                margin-bottom: 0.75rem;
            }
            
            .btn:last-child {
                margin-bottom: 0;
            }
        }
        
        @media (max-width: 576px) {
            .btn {
                padding: 0.6rem 1.25rem;
                font-size: 0.9rem;
            }
        }
        
        /* Feature Cards */
        .features-section {
            padding: 5rem 0;
            background: white;
            position: relative;
        }
        
        .features-section::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 100px;
            background: linear-gradient(180deg, var(--primary-color) 0%, white 100%);
            transform: skewY(-2deg);
            transform-origin: top left;
        }
        
        .feature-card {
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            height: 100%;
            border: none;
            border-radius: 1rem;
            overflow: hidden;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.07);
            background: white;
        }
        
        .feature-card:hover {
            transform: translateY(-8px);
            box-shadow: 0 20px 25px rgba(0, 0, 0, 0.15);
        }
        
        .feature-icon {
            font-size: 3.5rem;
            color: var(--primary-color);
            margin-bottom: 1.5rem;
            transition: all 0.3s ease;
        }
        
        .feature-card:hover .feature-icon {
            transform: scale(1.1);
            color: var(--warning-color);
        }
        
        .feature-card .card-body {
            padding: 2.5rem;
        }
        
        .feature-card .card-title {
            font-weight: 700;
            margin-bottom: 1rem;
            color: #1f2937;
        }
        
        .feature-card .card-text {
            color: #6b7280;
            line-height: 1.6;
        }
        
        /* Mobile Feature Adjustments */
        @media (max-width: 768px) {
            .features-section {
                padding: 3rem 0;
            }
            
            .feature-card .card-body {
                padding: 2rem 1.5rem;
            }
            
            .feature-icon {
                font-size: 3rem;
                margin-bottom: 1rem;
            }
            
            .feature-card {
                margin-bottom: 1.5rem;
            }
        }
        
        @media (max-width: 576px) {
            .features-section {
                padding: 2rem 0;
            }
            
            .feature-card .card-body {
                padding: 1.5rem 1rem;
            }
            
            .feature-icon {
                font-size: 2.5rem;
            }
        }
        
        /* Enhanced Footer */
        .footer {
            background: linear-gradient(135deg, #1f2937 0%, #111827 100%);
            color: #9ca3af;
            padding: 4rem 0 2rem;
            position: relative;
        }
        
        .footer::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 80px;
            background: linear-gradient(180deg, white 0%, #1f2937 100%);
            transform: skewY(-2deg);
            transform-origin: top left;
        }
        
        .footer h5 {
            color: white;
            margin-bottom: 1.5rem;
            font-weight: 700;
        }
        
        .footer a {
            color: #9ca3af;
            text-decoration: none;
            transition: all 0.2s ease;
            padding: 0.25rem 0;
            display: block;
        }
        
        .footer a:hover {
            color: white;
            transform: translateX(5px);
        }
        
        .footer .list-unstyled {
            padding-left: 0;
        }
        
        .footer .list-unstyled li {
            margin-bottom: 0.5rem;
        }
        
        /* Mobile Footer Adjustments */
        @media (max-width: 768px) {
            .footer {
                padding: 3rem 0 1.5rem;
            }
            
            .footer .row > div {
                margin-bottom: 2rem;
            }
        }
        
        @media (max-width: 576px) {
            .footer {
                padding: 2rem 0 1rem;
            }
            
            .footer h5 {
                font-size: 1.1rem;
                margin-bottom: 1rem;
            }
        }
        
        /* Utility Classes */
        .text-shadow {
            text-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }
        
        .text-shadow-lg {
            text-shadow: 0 4px 8px rgba(0, 0, 0, 0.3);
        }
        
        .glass-effect {
            background: rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(20px);
            -webkit-backdrop-filter: blur(20px);
            border: 1px solid rgba(255, 255, 255, 0.2);
        }
        
        .section-title {
            font-weight: 800;
            margin-bottom: 1rem;
            color: #1f2937;
        }
        
        .section-subtitle {
            color: #6b7280;
            font-size: 1.125rem;
            margin-bottom: 3rem;
        }
        
        /* Animation Classes */
        .fade-in {
            opacity: 0;
            transform: translateY(30px);
            transition: all 0.6s ease;
        }
        
        .fade-in.visible {
            opacity: 1;
            transform: translateY(0);
        }
        
        /* Loading States */
        .btn.loading {
            pointer-events: none;
            opacity: 0.7;
        }
        
        .btn.loading::after {
            content: '';
            position: absolute;
            width: 16px;
            height: 16px;
            margin: auto;
            border: 2px solid transparent;
            border-top-color: currentColor;
            border-radius: 50%;
            animation: spin 1s linear infinite;
        }
        
        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }
        
        /* Accessibility Improvements */
        @media (prefers-reduced-motion: reduce) {
            * {
                animation-duration: 0.01ms !important;
                animation-iteration-count: 1 !important;
                transition-duration: 0.01ms !important;
            }
            
            .hero-icon {
                animation: none;
            }
        }
        
        /* High Contrast Mode */
        @media (prefers-contrast: high) {
            .btn {
                border-width: 3px;
            }
            
            .feature-card {
                border: 2px solid #000;
            }
        }
        
        /* Touch-specific improvements */
        @media (hover: none) and (pointer: coarse) {
            .feature-card:hover {
                transform: none;
            }
            
            .btn:hover {
                transform: none;
            }
        }
    </style>
</head>
<body>
    <!-- Enhanced Navigation -->
    <nav class="navbar navbar-expand-lg navbar-light fixed-top" id="mainNavbar">
        <div class="container">
            <a class="navbar-brand d-flex align-items-center" href="#" aria-label="Home">
                @if(setting('appearance.logo'))
                    <img src="{{ setting('appearance.logo') }}" 
                        alt="{{ setting('system.name', config('app.name')) }}" 
                        class="me-2">
                @endif
                <span class="d-none d-md-inline">{{ setting('system.name', config('app.name')) }}</span>
                <span class="d-md-none">{{ setting('system.name', config('app.name')) }}</span>
            </a>
            
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            
            <div class="collapse navbar-collapse" id="navbarNav">
                <div class="ms-auto">
                    @auth
                    <a href="{{ route('dashboard') }}" class="btn btn-primary">
                        <i class="bi bi-speedometer2 me-1"></i>
                        <span class="d-none d-sm-inline">Dashboard</span>
                        <span class="d-sm-none">Home</span>
                    </a>
                    @else
                    <div class="d-flex flex-column flex-sm-row gap-2">
                        <a href="{{ url('/track') }}" class="btn btn-outline-primary">
                            <i class="bi bi-search me-1"></i>
                            <span class="d-none d-sm-inline">Track Document</span>
                            <span class="d-sm-none">Track</span>
                        </a>
                        <a href="{{ route('login') }}" class="btn btn-primary">
                            <i class="bi bi-box-arrow-in-right me-1"></i>
                            <span class="d-none d-sm-inline">Staff Login</span>
                            <span class="d-sm-none">Login</span>
                        </a>
                    </div>
                    @endauth
                </div>
            </div>
        </div>
    </nav>
    
    <!-- Enhanced Hero Section -->
    <section class="hero-section" id="hero">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-lg-6 order-2 order-lg-1">
                    <div class="hero-content fade-in">
                        <h1 class="display-4 fw-bold hero-title">
                            {{ setting('system.name', 'Document Tracking System') }} for 
                            <span class="text-warning">{{ setting('municipality.name', 'Madridejos') }}</span>
                        </h1>
                        <p class="lead hero-subtitle">
                            Streamine your document workflow with our comprehensive tracking system. 
                            Monitor, route, and manage official documents with transparency and efficiency.
                        </p>
                        
                        <div class="d-flex flex-column flex-sm-row gap-3 hero-buttons">
                            <a href="{{ url('/track') }}" class="btn btn-warning btn-lg" id="trackBtn">
                                <i class="bi bi-search me-2"></i>Track Document
                            </a>
                            @auth
                            <a href="{{ route('dashboard') }}" class="btn btn-outline-light btn-lg">
                                <i class="bi bi-speedometer2 me-2"></i>
                                <span class="d-none d-sm-inline">Go to Dashboard</span>
                                <span class="d-sm-none">Dashboard</span>
                            </a>
                            @else
                            <a href="{{ route('login') }}" class="btn btn-outline-light btn-lg">
                                <i class="bi bi-box-arrow-in-right me-2"></i>
                                <span class="d-none d-sm-inline">Staff Login</span>
                                <span class="d-sm-none">Login</span>
                            </a>
                            @endauth
                        </div>
                    </div>
                </div>
                
                <div class="col-lg-6 text-center order-1 order-lg-2">
                    <i class="bi bi-file-earmark-text hero-icon fade-in"></i>
                </div>
            </div>
        </div>
    </section>
    
    <!-- Enhanced Features Section -->
    <section class="features-section" id="features">
        <div class="container">
            <div class="row text-center mb-5">
                <div class="col-12">
                    <h2 class="section-title fade-in">Key Features</h2>
                    <p class="section-subtitle fade-in">Everything you need to manage documents efficiently</p>
                </div>
            </div>
            
            <div class="row">
                <div class="col-lg-4 col-md-6 mb-4">
                    <div class="card feature-card fade-in">
                        <div class="card-body text-center">
                            <i class="bi bi-search feature-icon"></i>
                            <h5 class="card-title">Real-time Tracking</h5>
                            <p class="card-text">Track your documents in real-time with detailed status updates and location information throughout the entire process.</p>
                        </div>
                    </div>
                </div>
                
                <div class="col-lg-4 col-md-6 mb-4">
                    <div class="card feature-card fade-in">
                        <div class="card-body text-center">
                            <i class="bi bi-shield-check feature-icon"></i>
                            <h5 class="card-title">Secure System</h5>
                            <p class="card-text">Your documents are protected with enterprise-grade security, encryption, and role-based access controls.</p>
                        </div>
                    </div>
                </div>
                
                <div class="col-lg-4 col-md-6 mb-4">
                    <div class="card feature-card fade-in">
                        <div class="card-body text-center">
                            <i class="bi bi-bell feature-icon"></i>
                            <h5 class="card-title">Smart Notifications</h5>
                            <p class="card-text">Get instant notifications when your document status changes, requires action, or reaches important milestones.</p>
                        </div>
                    </div>
                </div>
                
                <div class="col-lg-4 col-md-6 mb-4">
                    <div class="card feature-card fade-in">
                        <div class="card-body text-center">
                            <i class="bi bi-graph-up feature-icon"></i>
                            <h5 class="card-title">Advanced Analytics</h5>
                            <p class="card-text">Comprehensive analytics and reporting tools to improve workflow efficiency and track performance metrics.</p>
                        </div>
                    </div>
                </div>
                
                <div class="col-lg-4 col-md-6 mb-4">
                    <div class="card feature-card fade-in">
                        <div class="card-body text-center">
                            <i class="bi bi-people feature-icon"></i>
                            <h5 class="card-title">Multi-user Collaboration</h5>
                            <p class="card-text">Seamlessly collaborate with multiple users and departments with role-based permissions and workflows.</p>
                        </div>
                    </div>
                </div>
                
                <div class="col-lg-4 col-md-6 mb-4">
                    <div class="card feature-card fade-in">
                        <div class="card-body text-center">
                            <i class="bi bi-phone feature-icon"></i>
                            <h5 class="card-title">Mobile Optimized</h5>
                            <p class="card-text">Access your documents from any device with our responsive design that works perfectly on mobile and desktop.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
    
    <!-- Enhanced Footer -->
    <footer class="footer">
        <div class="container">
            <div class="row">
                <div class="col-lg-4 col-md-6 mb-4">
                    <h5>{{ setting('system.name', 'Document Tracking System') }}</h5>
                    <p>Streamlining document management for {{ setting('municipality.name', 'Madridejos') }} with efficiency, transparency, and modern technology solutions.</p>
                    
                    <!-- Social Links (if configured) -->
                    <div class="d-flex gap-3 mt-3">
                        @if(setting('social.facebook'))
                        <a href="{{ setting('social.facebook') }}" class="text-muted" target="_blank" aria-label="Facebook">
                            <i class="bi bi-facebook fs-5"></i>
                        </a>
                        @endif
                        @if(setting('social.twitter'))
                        <a href="{{ setting('social.twitter') }}" class="text-muted" target="_blank" aria-label="Twitter">
                            <i class="bi bi-twitter fs-5"></i>
                        </a>
                        @endif
                    </div>
                </div>
                
                <div class="col-lg-2 col-md-3 col-sm-6 mb-4">
                    <h5>Quick Links</h5>
                    <ul class="list-unstyled">
                        <li><a href="{{ url('/track') }}">Track Document</a></li>
                        <li><a href="{{ route('login') }}">Staff Login</a></li>
                        <li><a href="#features">Features</a></li>
                        <li><a href="#contact">Contact</a></li>
                    </ul>
                </div>
                
                <div class="col-lg-2 col-md-3 col-sm-6 mb-4">
                    <h5>Support</h5>
                    <ul class="list-unstyled">
                        <li><a href="#contact">Contact Us</a></li>
                        <li><a href="#faq">FAQ</a></li>
                        <li><a href="#help">Help Center</a></li>
                        <li><a href="#status">System Status</a></li>
                    </ul>
                </div>
                
                <div class="col-lg-4 col-md-6 mb-4" id="contact">
                    <h5>Contact Information</h5>
                    <div class="contact-info">
                        <p class="mb-2">
                            <i class="bi bi-geo-alt me-2"></i>
                            {{ setting('municipality.address', 'Madridejos, Cebu, Philippines') }}
                        </p>
                        <p class="mb-2">
                            <i class="bi bi-telephone me-2"></i>
                            <a href="tel:{{ setting('municipality.contact_phone', '+63 XXX XXX XXXX') }}">
                                {{ setting('municipality.contact_phone', '+63 XXX XXX XXXX') }}
                            </a>
                        </p>
                        <p class="mb-2">
                            <i class="bi bi-envelope me-2"></i>
                            <a href="mailto:{{ setting('municipality.email', 'info@madridejos.gov.ph') }}">
                                {{ setting('municipality.email', 'info@madridejos.gov.ph') }}
                            </a>
                        </p>
                        <p class="mb-0">
                            <i class="bi bi-clock me-2"></i>
                            Office Hours: Mon-Fri 8:00 AM - 5:00 PM
                        </p>
                    </div>
                </div>
            </div>
            
            <hr class="my-4 border-secondary">
            
            <div class="row align-items-center">
                <div class="col-md-6">
                    <p class="mb-0">&copy; {{ date('Y') }} {{ setting('municipality.name', 'Municipality of Madridejos') }}. All rights reserved.</p>
                </div>
                <div class="col-md-6 text-md-end">
                    <p class="mb-0">
                        Powered by {{ setting('system.name', 'Document Tracking System') }} 
                        <span class="text-muted">v{{ config('app.version', '1.0') }}</span>
                    </p>
                </div>
            </div>
        </div>
    </footer>
    
    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    
    <!-- Enhanced Custom JavaScript -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Enhanced smooth scrolling for anchor links
            document.querySelectorAll('a[href^="#"]').forEach(anchor => {
                anchor.addEventListener('click', function (e) {
                    e.preventDefault();
                    const target = document.querySelector(this.getAttribute('href'));
                    if (target) {
                        const headerOffset = 80;
                        const elementPosition = target.getBoundingClientRect().top;
                        const offsetPosition = elementPosition + window.pageYOffset - headerOffset;
                        
                        window.scrollTo({
                            top: offsetPosition,
                            behavior: 'smooth'
                        });
                    }
                });
            });
            
            // Enhanced navbar background on scroll
            const navbar = document.getElementById('mainNavbar');
            let lastScrollTop = 0;
            
            window.addEventListener('scroll', function() {
                const scrollTop = window.pageYOffset || document.documentElement.scrollTop;
                
                if (scrollTop > 50) {
                    navbar.classList.add('scrolled');
                } else {
                    navbar.classList.remove('scrolled');
                }
                
                // Hide/show navbar on mobile when scrolling
                if (window.innerWidth <= 768) {
                    if (scrollTop > lastScrollTop && scrollTop > 100) {
                        navbar.style.transform = 'translateY(-100%)';
                    } else {
                        navbar.style.transform = 'translateY(0)';
                    }
                }
                
                lastScrollTop = scrollTop;
            });
            
            // Intersection Observer for fade-in animations
            const observerOptions = {
                threshold: 0.1,
                rootMargin: '0px 0px -50px 0px'
            };
            
            const observer = new IntersectionObserver((entries) => {
                entries.forEach(entry => {
                    if (entry.isIntersecting) {
                        entry.target.classList.add('visible');
                    }
                });
            }, observerOptions);
            
            // Observe all fade-in elements
            document.querySelectorAll('.fade-in').forEach(el => {
                observer.observe(el);
            });
            
            // Enhanced button interactions
            document.querySelectorAll('.btn').forEach(button => {
                button.addEventListener('click', function() {
                    // Add loading state for navigation buttons
                    if (this.href && !this.href.includes('#')) {
                        this.classList.add('loading');
                        
                        // Remove loading state if navigation is cancelled
                        setTimeout(() => {
                            this.classList.remove('loading');
                        }, 3000);
                    }
                });
                
                // Touch feedback for mobile
                if ('ontouchstart' in window) {
                    button.addEventListener('touchstart', function() {
                        this.style.transform = 'scale(0.98)';
                    }, { passive: true });
                    
                    button.addEventListener('touchend', function() {
                        setTimeout(() => {
                            this.style.transform = '';
                        }, 100);
                    }, { passive: true });
                }
            });
            
            // Feature card stagger animation
            const featureCards = document.querySelectorAll('.feature-card');
            featureCards.forEach((card, index) => {
                setTimeout(() => {
                    card.classList.add('visible');
                }, index * 100);
            });
            
            // Navbar collapse on mobile link click
            const navbarCollapse = document.getElementById('navbarNav');
            if (navbarCollapse) {
                navbarCollapse.addEventListener('click', function(e) {
                    if (e.target.classList.contains('nav-link') && window.innerWidth < 992) {
                        const bsCollapse = new bootstrap.Collapse(navbarCollapse);
                        bsCollapse.hide();
                    }
                });
            }
            
            // Keyboard navigation support
            document.addEventListener('keydown', function(e) {
                // Escape key to close mobile menu
                if (e.key === 'Escape' && navbarCollapse?.classList.contains('show')) {
                    const bsCollapse = new bootstrap.Collapse(navbarCollapse);
                    bsCollapse.hide();
                }
            });
            
            // Performance optimization: Lazy load images
            if ('IntersectionObserver' in window) {
                const imageObserver = new IntersectionObserver((entries, observer) => {
                    entries.forEach(entry => {
                        if (entry.isIntersecting) {
                            const img = entry.target;
                            if (img.dataset.src) {
                                img.src = img.dataset.src;
                                img.removeAttribute('data-src');
                                observer.unobserve(img);
                            }
                        }
                    });
                });
                
                document.querySelectorAll('img[data-src]').forEach(img => {
                    imageObserver.observe(img);
                });
            }
            
            // Add visual feedback for form interactions
            document.querySelectorAll('input, textarea, select').forEach(input => {
                input.addEventListener('focus', function() {
                    this.parentElement.classList.add('focused');
                });
                
                input.addEventListener('blur', function() {
                    this.parentElement.classList.remove('focused');
                });
            });
            
            // Track page views for analytics (if configured)
            if (typeof gtag !== 'undefined') {
                gtag('config', 'GA_MEASUREMENT_ID', {
                    page_title: 'Landing Page',
                    page_location: window.location.href
                });
            }
        });
        
        // Handle back button navigation
        window.addEventListener('pageshow', function(event) {
            if (event.persisted) {
                // Reset any loading states
                document.querySelectorAll('.btn.loading').forEach(btn => {
                    btn.classList.remove('loading');
                });
            }
        });
        
        // Preload critical resources
        const preloadLinks = [
            '{{ route("public.track.index") }}',
            '{{ route("login") }}'
        ];
        
        preloadLinks.forEach(url => {
            const link = document.createElement('link');
            link.rel = 'prefetch';
            link.href = url;
            document.head.appendChild(link);
        });
    </script>
</body>
</html>