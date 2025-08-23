<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="default">
    <meta name="theme-color" content="{{ setting('theme.primary_color', '#1e40af') }}">
    
    <title>{{ $document->tracking_number }} - Track Document</title>
    
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
    <link rel="stylesheet" href="{{ route('dynamic-styles.css') }}?v={{ time() }}">
    
    <style>
        :root {
            --primary-color: {{ setting('theme.primary_color', '#1e40af') }};
            --success-color: {{ setting('theme.success_color', '#059669') }};
            --warning-color: {{ setting('theme.warning_color', '#d97706') }};
            --danger-color: {{ setting('theme.danger_color', '#dc2626') }};
            --font-family: {{ setting('theme.font_family', 'Inter, sans-serif') }};
            --navbar-height: 70px;
        }
        
        /* Mobile viewport adjustments */
        @media (max-width: 768px) {
            :root {
                --navbar-height: 60px;
            }
        }
        
        body {
            font-family: var(--font-family);
            background: linear-gradient(135deg, var(--primary-color) 0%, #764ba2 100%);
            min-height: 100vh;
            padding-top: var(--navbar-height);
            overflow-x: hidden;
            margin: 0;
            padding-bottom: 2rem;
            -webkit-font-smoothing: antialiased;
            -moz-osx-font-smoothing: grayscale;
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
        
        /* Enhanced Navigation */
        .navbar {
            height: var(--navbar-height);
            backdrop-filter: blur(20px);
            -webkit-backdrop-filter: blur(20px);
            background: rgba(255, 255, 255, 0.95) !important;
            box-shadow: 0 2px 20px rgba(0, 0, 0, 0.1);
            transition: all 0.3s ease;
        }
        
        .navbar-brand {
            font-size: 1.25rem;
            font-weight: 700;
            color: var(--primary-color) !important;
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
        }
        
        .navbar-toggler:focus {
            box-shadow: none;
            background-color: rgba(0, 0, 0, 0.05);
        }

        /* Mobile Navigation Adjustments */
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
            .navbar {
                padding: 0.4rem 0;
            }
            
            .navbar-brand {
                font-size: 1rem;
            }
            
            .navbar-brand img {
                height: 28px;
            }
        }

        /* Enhanced Tracking Cards */
        .tracking-card {
            background: white;
            border-radius: 1.5rem;
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25);
            border: 0;
            overflow: hidden;
            position: relative;
            margin: 2rem 0;
            transition: all 0.3s ease;
        }
        
        .tracking-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 35px 60px -12px rgba(0, 0, 0, 0.3);
        }
        
        .tracking-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 4px;
            background: linear-gradient(90deg, var(--primary-color) 0%, #764ba2 100%);
        }
        
        .tracking-card .card-header {
            background: linear-gradient(135deg, #f8fafc 0%, #f1f5f9 100%);
            border-bottom: 1px solid #e2e8f0;
            padding: 2rem;
        }
        
        .tracking-card .card-body {
            padding: 2rem;
        }

        /* Mobile Card Adjustments */
        @media (max-width: 768px) {
            .tracking-card {
                margin: 1rem 0;
                border-radius: 1rem;
            }
            
            .tracking-card .card-header {
                padding: 1.5rem 1rem;
            }
            
            .tracking-card .card-body {
                padding: 1.5rem 1rem;
            }
        }

        @media (max-width: 576px) {
            .tracking-card .card-header {
                padding: 1rem;
            }
            
            .tracking-card .card-body {
                padding: 1rem;
            }
        }
        
        /* Enhanced Status Badges */
        .status-badge {
            padding: 0.75rem 1.5rem;
            border-radius: 2rem;
            font-size: 0.9rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            transition: all 0.2s ease;
        }
        
        .status-badge:hover {
            transform: translateY(-1px);
            box-shadow: 0 6px 8px rgba(0, 0, 0, 0.15);
        }
        
        .status-pending { 
            background: linear-gradient(135deg, #fef3c7 0%, #fde047 100%); 
            color: #92400e; 
        }
        .status-in-progress { 
            background: linear-gradient(135deg, #dbeafe 0%, #60a5fa 100%); 
            color: #1e40af; 
        }
        .status-completed { 
            background: linear-gradient(135deg, #d1fae5 0%, #34d399 100%); 
            color: #065f46; 
        }
        .status-cancelled { 
            background: linear-gradient(135deg, #fee2e2 0%, #f87171 100%); 
            color: #991b1b; 
        }
        .status-archived { 
            background: linear-gradient(135deg, #f3f4f6 0%, #9ca3af 100%); 
            color: #374151; 
        }

        /* Mobile Status Badge Adjustments */
        @media (max-width: 768px) {
            .status-badge {
                padding: 0.5rem 1rem;
                font-size: 0.8rem;
            }
        }

        @media (max-width: 576px) {
            .status-badge {
                padding: 0.4rem 0.8rem;
                font-size: 0.75rem;
            }
        }
        
        /* Enhanced Timeline */
        .timeline {
            position: relative;
            padding-left: 40px;
        }
        
        .timeline::before {
            content: '';
            position: absolute;
            left: 20px;
            top: 0;
            bottom: 0;
            width: 3px;
            background: linear-gradient(180deg, var(--primary-color) 0%, #e2e8f0 100%);
            border-radius: 2px;
        }
        
        .timeline-item {
            position: relative;
            margin-bottom: 2rem;
            animation: fadeInUp 0.6s ease forwards;
            opacity: 0;
            transform: translateY(20px);
        }
        
        .timeline-item:nth-child(1) { animation-delay: 0.1s; }
        .timeline-item:nth-child(2) { animation-delay: 0.2s; }
        .timeline-item:nth-child(3) { animation-delay: 0.3s; }
        .timeline-item:nth-child(4) { animation-delay: 0.4s; }
        .timeline-item:nth-child(5) { animation-delay: 0.5s; }
        
        @keyframes fadeInUp {
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
        
        .timeline-marker {
            position: absolute;
            left: -30px;
            top: 10px;
            width: 20px;
            height: 20px;
            border-radius: 50%;
            border: 3px solid white;
            box-shadow: 0 0 0 3px var(--primary-color), 0 4px 6px rgba(0, 0, 0, 0.1);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 0.7rem;
            font-weight: bold;
            color: white;
            z-index: 2;
        }
        
        .timeline-marker.completed {
            background: linear-gradient(135deg, var(--success-color) 0%, #34d399 100%);
            box-shadow: 0 0 0 3px var(--success-color), 0 4px 6px rgba(0, 0, 0, 0.1);
        }
        
        .timeline-marker.current {
            background: linear-gradient(135deg, var(--warning-color) 0%, #fbbf24 100%);
            box-shadow: 0 0 0 3px var(--warning-color), 0 4px 6px rgba(0, 0, 0, 0.1);
            animation: pulse 2s ease-in-out infinite;
        }
        
        .timeline-marker.pending {
            background: linear-gradient(135deg, #e2e8f0 0%, #cbd5e1 100%);
            box-shadow: 0 0 0 3px #e2e8f0, 0 4px 6px rgba(0, 0, 0, 0.1);
        }
        
        @keyframes pulse {
            0%, 100% { 
                transform: scale(1); 
                box-shadow: 0 0 0 3px var(--warning-color), 0 4px 6px rgba(0, 0, 0, 0.1); 
            }
            50% { 
                transform: scale(1.1); 
                box-shadow: 0 0 0 6px var(--warning-color), 0 6px 8px rgba(0, 0, 0, 0.15); 
            }
        }
        
        .timeline-content {
            background: linear-gradient(135deg, #f8fafc 0%, #f1f5f9 100%);
            padding: 1.5rem;
            border-radius: 1rem;
            border-left: 4px solid var(--primary-color);
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.05);
            transition: all 0.3s ease;
        }
        
        .timeline-content:hover {
            transform: translateX(5px);
            box-shadow: 0 8px 15px rgba(0, 0, 0, 0.1);
        }
        
        .timeline-content h6 {
            color: #1f2937;
            font-weight: 700;
            margin-bottom: 0.75rem;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }
        
        .timeline-content .text-muted {
            color: #6b7280 !important;
            line-height: 1.6;
        }
        
        .timeline-content .badge {
            font-size: 0.75rem;
            padding: 0.4rem 0.8rem;
            border-radius: 1rem;
        }

        /* Mobile Timeline Adjustments */
        @media (max-width: 768px) {
            .timeline {
                padding-left: 30px;
            }
            
            .timeline::before {
                left: 15px;
                width: 2px;
            }
            
            .timeline-marker {
                left: -25px;
                width: 16px;
                height: 16px;
                top: 8px;
            }
            
            .timeline-content {
                padding: 1rem;
            }
            
            .timeline-content h6 {
                font-size: 0.9rem;
            }
        }

        @media (max-width: 576px) {
            .timeline {
                padding-left: 25px;
            }
            
            .timeline::before {
                left: 12px;
            }
            
            .timeline-marker {
                left: -22px;
                width: 14px;
                height: 14px;
                top: 6px;
            }
            
            .timeline-content {
                padding: 0.75rem;
            }
            
            .timeline-content h6 {
                font-size: 0.85rem;
                margin-bottom: 0.5rem;
            }
        }
        
        /* Enhanced Info Items */
        .info-item {
            border-bottom: 1px solid #e5e7eb;
            padding: 1rem 0;
            transition: all 0.2s ease;
        }
        
        .info-item:hover {
            background-color: rgba(30, 64, 175, 0.02);
            border-radius: 0.5rem;
            padding-left: 1rem;
            padding-right: 1rem;
            margin-left: -1rem;
            margin-right: -1rem;
        }
        
        .info-item:last-child {
            border-bottom: none;
        }
        
        .info-item .form-label {
            font-weight: 700;
            color: #374151;
            margin-bottom: 0.5rem;
            font-size: 0.875rem;
            text-transform: uppercase;
            letter-spacing: 0.05em;
        }
        
        .info-item .fw-semibold {
            color: #1f2937;
            font-size: 1rem;
        }

        /* Mobile Info Item Adjustments */
        @media (max-width: 576px) {
            .info-item {
                padding: 0.75rem 0;
            }
            
            .info-item .form-label {
                font-size: 0.8rem;
            }
            
            .info-item .fw-semibold {
                font-size: 0.9rem;
            }
        }

        /* Enhanced Button Styles */
        .btn {
            font-weight: 600;
            padding: 0.75rem 1.5rem;
            border-radius: 0.75rem;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            border: 2px solid transparent;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            min-height: 44px;
            position: relative;
            overflow: hidden;
        }
        
        .btn::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.2), transparent);
            transition: left 0.5s ease;
        }
        
        .btn:hover::before {
            left: 100%;
        }
        
        .btn:hover {
            transform: translateY(-2px);
        }
        
        .btn:active {
            transform: translateY(0);
        }
        
        .btn-primary {
            background: linear-gradient(135deg, var(--primary-color) 0%, #1e3a8a 100%);
            border-color: var(--primary-color);
            color: white;
            box-shadow: 0 4px 6px rgba(30, 64, 175, 0.2);
        }
        
        .btn-primary:hover {
            box-shadow: 0 8px 15px rgba(30, 64, 175, 0.3);
            color: white;
        }
        
        .btn-outline-primary {
            border-color: var(--primary-color);
            color: var(--primary-color);
            background: rgba(255, 255, 255, 0.9);
            backdrop-filter: blur(10px);
        }
        
        .btn-outline-primary:hover {
            background-color: var(--primary-color);
            border-color: var(--primary-color);
            color: white;
        }
        
        .btn-outline-secondary {
            border-color: #6b7280;
            color: #6b7280;
            background: rgba(255, 255, 255, 0.9);
            backdrop-filter: blur(10px);
        }
        
        .btn-outline-secondary:hover {
            background-color: #6b7280;
            border-color: #6b7280;
            color: white;
        }
        
        .btn-outline-success {
            border-color: var(--success-color);
            color: var(--success-color);
            background: rgba(255, 255, 255, 0.9);
            backdrop-filter: blur(10px);
        }
        
        .btn-outline-success:hover {
            background-color: var(--success-color);
            border-color: var(--success-color);
            color: white;
        }

        /* Mobile Button Adjustments */
        @media (max-width: 768px) {
            .mobile-btn-stack .btn {
                width: 100%;
                margin-bottom: 0.75rem;
                padding: 0.75rem 1rem;
            }
            
            .mobile-btn-stack .btn:last-child {
                margin-bottom: 0;
            }
        }

        @media (max-width: 576px) {
            .btn {
                padding: 0.6rem 1rem;
                font-size: 0.9rem;
            }
        }

        /* Enhanced Footer */
        .footer-content {
            text-align: center;
            color: rgba(255, 255, 255, 0.9);
            margin-top: 3rem;
            padding: 2rem 0;
            background: rgba(255, 255, 255, 0.05);
            backdrop-filter: blur(10px);
            border-radius: 2rem 2rem 0 0;
        }
        
        .footer-content p {
            margin-bottom: 0.5rem;
            font-weight: 600;
        }
        
        .footer-content small {
            color: rgba(255, 255, 255, 0.7);
            font-style: italic;
        }

        /* Progress Indicator */
        .progress-indicator {
            background: rgba(255, 255, 255, 0.1);
            border-radius: 1rem;
            padding: 1rem;
            margin-bottom: 2rem;
            backdrop-filter: blur(10px);
        }
        
        .progress-bar-custom {
            height: 8px;
            border-radius: 4px;
            background: linear-gradient(90deg, var(--primary-color) 0%, var(--success-color) 100%);
            transition: width 1s ease-in-out;
        }
        
        .progress-steps {
            display: flex;
            justify-content: space-between;
            margin-top: 1rem;
            color: rgba(255, 255, 255, 0.8);
            font-size: 0.8rem;
        }

        /* Overdue indicator */
        .overdue-indicator {
            background: linear-gradient(135deg, #fee2e2 0%, #fecaca 100%);
            color: #991b1b;
            padding: 0.5rem 1rem;
            border-radius: 0.75rem;
            font-weight: 600;
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            margin-top: 0.5rem;
            animation: pulse-warning 2s ease-in-out infinite;
        }
        
        @keyframes pulse-warning {
            0%, 100% { opacity: 1; }
            50% { opacity: 0.8; }
        }

        /* Print Styles */
        @media print {
            body {
                background: white !important;
                padding-top: 0 !important;
                color: black !important;
            }
            
            .navbar, .btn, .footer-content {
                display: none !important;
            }
            
            .tracking-card {
                box-shadow: none !important;
                border: 1px solid #ccc !important;
                page-break-inside: avoid;
            }
            
            .timeline::before {
                background: #000 !important;
            }
            
            .timeline-marker {
                background: #000 !important;
                border-color: #000 !important;
                box-shadow: none !important;
            }
            
            .status-badge {
                background: #f0f0f0 !important;
                color: #000 !important;
                border: 1px solid #ccc !important;
            }
        }

        /* Accessibility Improvements */
        @media (prefers-reduced-motion: reduce) {
            * {
                animation-duration: 0.01ms !important;
                animation-iteration-count: 1 !important;
                transition-duration: 0.01ms !important;
            }
        }
        
        /* High Contrast Mode */
        @media (prefers-contrast: high) {
            .tracking-card {
                border: 3px solid #000;
            }
            
            .btn {
                border-width: 3px;
            }
            
            .timeline-content {
                border: 2px solid #000;
            }
        }
        
        /* Touch-specific improvements */
        @media (hover: none) and (pointer: coarse) {
            .tracking-card:hover,
            .btn:hover,
            .timeline-content:hover,
            .info-item:hover {
                transform: none;
            }
        }
        
        /* Container adjustments for mobile */
        @media (max-width: 576px) {
            .container {
                padding-left: 1rem;
                padding-right: 1rem;
            }
        }

        /* Action buttons container */
        .action-buttons {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(20px);
            border-radius: 1.5rem;
            padding: 1.5rem;
            margin-top: 2rem;
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1);
        }

        @media (max-width: 576px) {
            .action-buttons {
                padding: 1rem;
                border-radius: 1rem;
            }
        }
    </style>
</head>
<body>
    <!-- Enhanced Navigation -->
    <nav class="navbar navbar-expand-lg navbar-light fixed-top">
        <div class="container">
            <a class="navbar-brand d-flex align-items-center" href="{{ url('/') }}" aria-label="Home">
                @if(setting('appearance.logo'))
                    <img src="{{ setting('appearance.logo') }}" 
                        alt="{{ setting('system.name', config('app.name')) }}" 
                        class="me-2">
                @endif
                <span class="d-none d-sm-inline">{{ setting('system.name', config('app.name')) }}</span>
                <span class="d-sm-none">{{ Str::limit(setting('system.name', config('app.name')), 20) }}</span>
            </a>
            
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            
            <div class="collapse navbar-collapse" id="navbarNav">
                <div class="ms-auto">
                    @auth
                    <div class="d-flex flex-column flex-sm-row gap-2 mobile-btn-stack">
                        <a href="{{ route('public.track.index') }}" class="btn btn-outline-primary">
                            <i class="bi bi-search me-1"></i>
                            <span class="d-none d-sm-inline">New Search</span>
                            <span class="d-sm-none">Search</span>
                        </a>
                        <button onclick="window.print()" class="btn btn-outline-secondary">
                            <i class="bi bi-printer me-1"></i>
                            <span class="d-none d-sm-inline">Print</span>
                        </button>
                        <a href="{{ route('dashboard') }}" class="btn btn-primary">
                            <i class="bi bi-speedometer2 me-1"></i>
                            <span class="d-none d-sm-inline">Dashboard</span>
                        </a>
                        <a href="{{ url('/') }}" class="btn btn-outline-secondary">
                            <i class="bi bi-house me-1"></i>
                            <span class="d-none d-sm-inline">Home</span>
                        </a>
                    </div>
                    @else
                    <div class="d-flex flex-column flex-sm-row gap-2 mobile-btn-stack">
                        <a href="{{ route('public.track.index') }}" class="btn btn-outline-primary">
                            <i class="bi bi-search me-1"></i>
                            <span class="d-none d-sm-inline">New Search</span>
                            <span class="d-sm-none">Search</span>
                        </a>
                        <button onclick="window.print()" class="btn btn-outline-secondary">
                            <i class="bi bi-printer me-1"></i>
                            <span class="d-none d-sm-inline">Print</span>
                        </button>
                        <a href="{{ route('login') }}" class="btn btn-outline-success">
                            <i class="bi bi-box-arrow-in-right me-1"></i>
                            <span class="d-none d-sm-inline">Staff Login</span>
                            <span class="d-sm-none">Login</span>
                        </a>
                        <a href="{{ url('/') }}" class="btn btn-outline-secondary">
                            <i class="bi bi-house me-1"></i>
                            <span class="d-none d-sm-inline">Home</span>
                        </a>
                    </div>
                    @endauth
                </div>
            </div>
        </div>
    </nav>
    
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-10">

                <!-- Document Information -->
                <div class="card tracking-card">
                    <div class="card-header">
                        <div class="d-flex justify-content-between align-items-start flex-wrap">
                            <div class="mb-2">
                                <h2 class="mb-2 fw-bold">
                                    <i class="bi bi-file-earmark-text me-2 text-primary"></i>
                                    {{ $document->tracking_number }}
                                </h2>
                                <p class="text-muted mb-0 h5">{{ $document->title }}</p>
                            </div>
                            <div class="text-end">
                                <span class="status-badge status-{{ $document->status }}">
                                    <i class="bi bi-{{ $document->status === 'completed' ? 'check-circle' : ($document->status === 'in_progress' ? 'clock' : ($document->status === 'cancelled' ? 'x-circle' : 'hourglass')) }}"></i>
                                    {{ ucfirst(str_replace('_', ' ', $document->status)) }}
                                </span>
                                @if($document->isOverdue())
                                <div class="overdue-indicator mt-2">
                                    <i class="bi bi-exclamation-triangle"></i>
                                    Overdue
                                </div>
                                @endif
                            </div>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="info-item">
                                    <label class="form-label">
                                        <i class="bi bi-tag me-1"></i>Document Type
                                    </label>
                                    <p class="fw-semibold mb-0">{{ $document->documentType->name }}</p>
                                </div>
                                
                                <div class="info-item">
                                    <label class="form-label">
                                        <i class="bi bi-building me-1"></i>Origin Department
                                    </label>
                                    <p class="fw-semibold mb-0">{{ $document->originDepartment->name }}</p>
                                </div>
                                
                                <div class="info-item">
                                    <label class="form-label">
                                        <i class="bi bi-calendar-event me-1"></i>Date Received
                                    </label>
                                    <p class="fw-semibold mb-0">
                                        {{ $document->date_received->format('F d, Y') }}
                                        <small class="text-muted">({{ $document->date_received->diffForHumans() }})</small>
                                    </p>
                                </div>
                            </div>
                            
                            <div class="col-md-6">
                                <div class="info-item">
                                    <label class="form-label">
                                        <i class="bi bi-activity me-1"></i>Current Status
                                    </label>
                                    <p class="fw-semibold mb-0">
                                        <span class="status-badge status-{{ $document->status }}">
                                            <i class="bi bi-{{ $document->status === 'completed' ? 'check-circle' : ($document->status === 'in_progress' ? 'clock' : ($document->status === 'cancelled' ? 'x-circle' : 'hourglass')) }}"></i>
                                            {{ ucfirst(str_replace('_', ' ', $document->status)) }}
                                        </span>
                                    </p>
                                </div>
                                
                                <div class="info-item">
                                    <label class="form-label">
                                        <i class="bi bi-geo-alt me-1"></i>Current Department
                                    </label>
                                    <p class="fw-semibold mb-0">{{ $document->currentDepartment->name }}</p>
                                </div>
                                
                                @if($document->target_completion_date)
                                <div class="info-item">
                                    <label class="form-label">
                                        <i class="bi bi-flag me-1"></i>Target Completion
                                    </label>
                                    <p class="fw-semibold mb-0">
                                        {{ $document->target_completion_date->format('F d, Y') }}
                                        @if($document->isOverdue())
                                            <span class="badge bg-danger ms-2">
                                                <i class="bi bi-exclamation-triangle me-1"></i>Overdue
                                            </span>
                                        @else
                                            <small class="text-muted">({{ $document->target_completion_date->diffForHumans() }})</small>
                                        @endif
                                    </p>
                                </div>
                                @endif
                            </div>
                        </div>
                        
                        @if($document->description && !$document->is_confidential)
                        <div class="info-item mt-3">
                            <label class="form-label">
                                <i class="bi bi-file-text me-1"></i>Description
                            </label>
                            <p class="mb-0">{{ $document->description }}</p>
                        </div>
                        @endif
                    </div>
                </div>
                
                <!-- Routing History -->
                @if($document->routes->count() > 0)
                <div class="card tracking-card">
                    <div class="card-header">
                        <h5 class="mb-0 d-flex align-items-center">
                            <i class="bi bi-arrow-left-right me-2 text-primary"></i>
                            Document Routing History
                            <span class="badge bg-primary ms-2">{{ $document->routes->count() }} {{ Str::plural('Route', $document->routes->count()) }}</span>
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="timeline">
                            @foreach($document->routes as $index => $route)
                            <div class="timeline-item">
                                <div class="timeline-marker {{ $route->status === 'processed' ? 'completed' : ($route->status === 'received' ? 'current' : 'pending') }}">
                                    @if($route->status === 'processed')
                                        <i class="bi bi-check"></i>
                                    @elseif($route->status === 'received')
                                        <i class="bi bi-clock"></i>
                                    @else
                                        {{ $index + 1 }}
                                    @endif
                                </div>
                                <div class="timeline-content">
                                    <div class="d-flex justify-content-between align-items-start flex-wrap">
                                        <div class="mb-2 flex-grow-1">
                                            <h6 class="mb-2">
                                                <i class="bi bi-building me-1 text-primary"></i>
                                                {{ $route->fromDepartment->name }} 
                                                <i class="bi bi-arrow-right mx-2 text-muted"></i>
                                                {{ $route->toDepartment->name }}
                                            </h6>
                                            
                                            @if($route->routing_purpose)
                                            <p class="text-muted mb-2">
                                                <i class="bi bi-info-circle me-1"></i>
                                                <strong>Purpose:</strong> {{ $route->routing_purpose }}
                                            </p>
                                            @endif
                                            
                                            @if($route->instructions)
                                            <p class="text-muted mb-2">
                                                <i class="bi bi-clipboard-check me-1"></i>
                                                <strong>Instructions:</strong> {{ $route->instructions }}
                                            </p>
                                            @endif
                                            
                                            <div class="timeline-dates">
                                                <small class="text-muted d-block">
                                                    <i class="bi bi-send me-1"></i>
                                                    <strong>Routed:</strong> {{ $route->routed_at->format('M d, Y \a\t g:i A') }}
                                                </small>
                                                @if($route->received_at)
                                                <small class="text-muted d-block">
                                                    <i class="bi bi-inbox me-1"></i>
                                                    <strong>Received:</strong> {{ $route->received_at->format('M d, Y \a\t g:i A') }}
                                                </small>
                                                @endif
                                                @if($route->processed_at)
                                                <small class="text-muted d-block">
                                                    <i class="bi bi-check-circle me-1"></i>
                                                    <strong>Processed:</strong> {{ $route->processed_at->format('M d, Y \a\t g:i A') }}
                                                @endif
                                            </div>
                                        </div>
                                        <div class="ms-3">
                                            <span class="badge bg-{{ $route->status === 'processed' ? 'success' : ($route->status === 'received' ? 'warning' : 'secondary') }}">
                                                @if($route->status === 'processed')
                                                    <i class="bi bi-check-circle me-1"></i>Processed
                                                @elseif($route->status === 'received')
                                                    <i class="bi bi-clock me-1"></i>In Progress
                                                @else
                                                    <i class="bi bi-hourglass me-1"></i>Pending
                                                @endif
                                            </span>
                                        </div>
                                    </div>
                                    
                                    @if($route->remarks)
                                    <div class="mt-3 p-3 bg-light rounded-3">
                                        <small class="text-dark">
                                            <i class="bi bi-chat-quote me-1"></i>
                                            <strong>Remarks:</strong> {{ $route->remarks }}
                                        </small>
                                    </div>
                                    @endif
                                </div>
                            </div>
                            @endforeach
                        </div>
                    </div>
                </div>
                @endif
                
                <!-- Enhanced Action Buttons -->
                <div class="action-buttons text-center">
                    <h6 class="mb-3 text-dark">
                        <i class="bi bi-lightning me-2"></i>Quick Actions
                    </h6>
                    <div class="d-flex flex-column flex-sm-row gap-3 justify-content-center">
                        <a href="{{ route('public.track.index') }}" class="btn btn-primary">
                            <i class="bi bi-search me-2"></i>Track Another Document
                        </a>
                        <button onclick="window.print()" class="btn btn-outline-secondary">
                            <i class="bi bi-printer me-2"></i>Print This Page
                        </button>
                        <button onclick="shareDocument()" class="btn btn-outline-primary">
                            <i class="bi bi-share me-2"></i>Share
                        </button>
                        <button onclick="refreshStatus()" class="btn btn-outline-success" id="refreshBtn">
                            <i class="bi bi-arrow-clockwise me-2"></i>Refresh Status
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Enhanced Footer -->
    <div class="footer-content">
        <div class="container">
            <div class="row">
                <div class="col-md-8 mx-auto">
                    <p class="mb-2">&copy; {{ date('Y') }} {{ setting('municipality.name', 'Municipality of Madridejos') }}. All rights reserved.</p>
                    <small class="d-block mb-2">
                        For inquiries, contact us at 
                        <a href="mailto:{{ setting('municipality.contact_email', 'info@madridejos.gov.ph') }}" class="text-white">
                            {{ setting('municipality.contact_email', 'info@madridejos.gov.ph') }}
                        </a> 
                        or 
                        <a href="tel:{{ str_replace(' ', '', setting('municipality.contact_phone', '(032) 123-4567')) }}" class="text-white">
                            {{ setting('municipality.contact_phone', '(032) 123-4567') }}
                        </a>
                    </small>
                    <small class="text-muted">Serving the people with transparency and efficiency</small>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Share Modal -->
    <div class="modal fade" id="shareModal" tabindex="-1" aria-labelledby="shareModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="shareModalLabel">
                        <i class="bi bi-share me-2"></i>Share Document Status
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="shareUrl" class="form-label">Share this tracking URL:</label>
                        <div class="input-group">
                            <input type="text" class="form-control" id="shareUrl" readonly value="{{ request()->url() }}">
                            <button class="btn btn-outline-secondary" type="button" onclick="copyToClipboard()">
                                <i class="bi bi-clipboard"></i>
                            </button>
                        </div>
                        <small class="text-muted">Anyone with this link can view the document status</small>
                    </div>
                    
                    <div class="d-grid gap-2">
                        <button type="button" class="btn btn-primary" onclick="shareViaWhatsApp()">
                            <i class="bi bi-whatsapp me-2"></i>Share via WhatsApp
                        </button>
                        <button type="button" class="btn btn-info" onclick="shareViaEmail()">
                            <i class="bi bi-envelope me-2"></i>Share via Email
                        </button>
                        <button type="button" class="btn btn-success" onclick="shareViaSMS()">
                            <i class="bi bi-phone me-2"></i>Share via SMS
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Loading Overlay -->
    <div class="modal fade" id="loadingModal" tabindex="-1" data-bs-backdrop="static" data-bs-keyboard="false">
        <div class="modal-dialog modal-sm modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-body text-center p-4">
                    <div class="spinner-border text-primary mb-3" role="status">
                        <span class="visually-hidden">Loading...</span>
                    </div>
                    <h6 class="mb-2">Refreshing Status</h6>
                    <p class="text-muted mb-0">Please wait...</p>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    
    <!-- Enhanced Custom JavaScript -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Initialize timeline animations
            initializeAnimations();
            
            // Initialize tooltips
            const tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
            const tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
                return new bootstrap.Tooltip(tooltipTriggerEl);
            });
            
            // Share functionality
            window.shareDocument = function() {
                if (navigator.share) {
                    navigator.share({
                        title: 'Document Status - {{ $document->tracking_number }}',
                        text: 'Check the status of document {{ $document->tracking_number }}',
                        url: window.location.href
                    }).catch(err => {
                        console.log('Error sharing:', err);
                        showShareModal();
                    });
                } else {
                    showShareModal();
                }
            };
            
            function showShareModal() {
                const shareModal = new bootstrap.Modal(document.getElementById('shareModal'));
                shareModal.show();
            }
            
            // Copy to clipboard
            window.copyToClipboard = function() {
                const urlInput = document.getElementById('shareUrl');
                urlInput.select();
                urlInput.setSelectionRange(0, 99999);
                
                if (navigator.clipboard) {
                    navigator.clipboard.writeText(urlInput.value).then(() => {
                        showToast('Link copied to clipboard!', 'success');
                    });
                } else {
                    document.execCommand('copy');
                    showToast('Link copied to clipboard!', 'success');
                }
            };
            
            // Social sharing functions
            window.shareViaWhatsApp = function() {
                const text = `Check the status of document {{ $document->tracking_number }}: ${window.location.href}`;
                const url = `https://wa.me/?text=${encodeURIComponent(text)}`;
                window.open(url, '_blank');
            };
            
            window.shareViaEmail = function() {
                const subject = 'Document Status - {{ $document->tracking_number }}';
                const body = `Here's the tracking information for document {{ $document->tracking_number }}:\n\n${window.location.href}`;
                const url = `mailto:?subject=${encodeURIComponent(subject)}&body=${encodeURIComponent(body)}`;
                window.location.href = url;
            };
            
            window.shareViaSMS = function() {
                const text = `Document {{ $document->tracking_number }} status: ${window.location.href}`;
                const url = `sms:?body=${encodeURIComponent(text)}`;
                window.location.href = url;
            };
            
            // Refresh status functionality
            window.refreshStatus = function() {
                const refreshBtn = document.getElementById('refreshBtn');
                const loadingModal = new bootstrap.Modal(document.getElementById('loadingModal'));
                
                loadingModal.show();
                
                // Simulate API call - replace with actual endpoint
                setTimeout(() => {
                    loadingModal.hide();
                    showToast('Status refreshed successfully!', 'info');
                    
                    // In a real implementation, you would:
                    // fetch('/api/documents/{{ $document->id }}/refresh')
                    //     .then(response => response.json())
                    //     .then(data => {
                    //         // Update the page with new data
                    //         location.reload();
                    //     });
                }, 2000);
            };
            
            function initializeAnimations() {
                // Animate timeline items with stagger
                const timelineItems = document.querySelectorAll('.timeline-item');
                timelineItems.forEach((item, index) => {
                    setTimeout(() => {
                        item.style.opacity = '1';
                        item.style.transform = 'translateY(0)';
                    }, 100 * (index + 1));
                });
            }
            
            function showToast(message, type = 'info') {
                // Create toast container if it doesn't exist
                let container = document.querySelector('.toast-container');
                if (!container) {
                    container = document.createElement('div');
                    container.className = 'toast-container position-fixed top-0 end-0 p-3';
                    document.body.appendChild(container);
                }
                
                const toastId = 'toast-' + Date.now();
                const toast = document.createElement('div');
                toast.id = toastId;
                toast.className = `toast align-items-center text-white bg-${type} border-0`;
                toast.setAttribute('role', 'alert');
                toast.innerHTML = `
                    <div class="d-flex">
                        <div class="toast-body">
                            <i class="bi bi-${type === 'success' ? 'check-circle' : type === 'danger' ? 'exclamation-triangle' : 'info-circle'} me-2"></i>
                            ${message}
                        </div>
                        <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button>
                    </div>
                `;
                
                container.appendChild(toast);
                
                const bsToast = new bootstrap.Toast(toast, {
                    autohide: true,
                    delay: 5000
                });
                bsToast.show();
                
                // Remove toast element after it's hidden
                toast.addEventListener('hidden.bs.toast', function() {
                    toast.remove();
                });
            }
            
            // Navbar collapse on mobile link click
            const navbarCollapse = document.getElementById('navbarNav');
            if (navbarCollapse) {
                navbarCollapse.addEventListener('click', function(e) {
                    if (e.target.classList.contains('btn') && window.innerWidth < 992) {
                        const bsCollapse = new bootstrap.Collapse(navbarCollapse);
                        bsCollapse.hide();
                    }
                });
            }
            
            // Touch feedback for mobile
            if ('ontouchstart' in window) {
                document.querySelectorAll('.btn, .timeline-content, .info-item').forEach(element => {
                    element.addEventListener('touchstart', function() {
                        this.style.transform = 'scale(0.98)';
                    }, { passive: true });
                    
                    element.addEventListener('touchend', function() {
                        setTimeout(() => {
                            this.style.transform = '';
                        }, 100);
                    }, { passive: true });
                });
            }
            
            // Auto-refresh status every 5 minutes
            setInterval(() => {
                if (document.visibilityState === 'visible') {
                    // Only refresh if page is visible
                    console.log('Auto-refreshing status...');
                    // Implement silent refresh here
                }
            }, 300000); // 5 minutes
            
            // Handle page visibility change
            document.addEventListener('visibilitychange', function() {
                if (document.visibilityState === 'visible') {
                    // Page became visible, check for updates
                    console.log('Page visible, checking for updates...');
                }
            });
            
            // Keyboard shortcuts
            document.addEventListener('keydown', function(e) {
                // Ctrl/Cmd + P for print
                if ((e.ctrlKey || e.metaKey) && e.key === 'p') {
                    e.preventDefault();
                    window.print();
                }
                
                // Ctrl/Cmd + R for refresh
                if ((e.ctrlKey || e.metaKey) && e.key === 'r') {
                    e.preventDefault();
                    refreshStatus();
                }
                
                // S for share
                if (e.key === 's' && !e.ctrlKey && !e.metaKey) {
                    e.preventDefault();
                    shareDocument();
                }
            });
            
            // Initialize intersection observer for animations
            if ('IntersectionObserver' in window) {
                const observer = new IntersectionObserver((entries) => {
                    entries.forEach(entry => {
                        if (entry.isIntersecting) {
                            entry.target.classList.add('animate-in');
                        }
                    });
                }, { threshold: 0.1 });
                
                document.querySelectorAll('.tracking-card, .timeline-item').forEach(el => {
                    observer.observe(el);
                });
            }
            
            // Analytics tracking (if configured)
            if (typeof gtag !== 'undefined') {
                gtag('event', 'page_view', {
                    page_title: 'Document Tracking Results',
                    page_location: window.location.href,
                    custom_parameter: {
                        tracking_number: '{{ $document->tracking_number }}',
                        document_status: '{{ $document->status }}'
                    }
                });
            }
            
            // Add smooth scrolling behavior
            document.querySelectorAll('a[href^="#"]').forEach(anchor => {
                anchor.addEventListener('click', function (e) {
                    e.preventDefault();
                    const target = document.querySelector(this.getAttribute('href'));
                    if (target) {
                        target.scrollIntoView({
                            behavior: 'smooth',
                            block: 'start'
                        });
                    }
                });
            });
        });
        
        // Handle back button navigation
        window.addEventListener('pageshow', function(event) {
            if (event.persisted) {
                // Reset any loading states
                const loadingModal = bootstrap.Modal.getInstance(document.getElementById('loadingModal'));
                if (loadingModal) {
                    loadingModal.hide();
                }
            }
        });
        
        // Service Worker registration for offline support (optional)
        if ('serviceWorker' in navigator) {
            window.addEventListener('load', function() {
                navigator.serviceWorker.register('/sw.js').then(function(registration) {
                    console.log('ServiceWorker registration successful');
                }, function(err) {
                    console.log('ServiceWorker registration failed');
                });
            });
        }
    </script>
</body>
</html>