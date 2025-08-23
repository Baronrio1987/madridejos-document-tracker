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
    
    <title>Track Document - {{ setting('system.name', config('app.name')) }}</title>
    
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
            padding-top: var(--navbar-height);
            overflow-x: hidden;
            min-height: 100vh;
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

        /* Enhanced Tracking Card */
        .tracking-card {
            background: white;
            border-radius: 1.5rem;
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25);
            border: 0;
            overflow: hidden;
            position: relative;
            margin: 2rem 0;
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
        
        .tracking-card .card-body {
            padding: 3rem 2rem;
        }
        
        .tracking-icon {
            font-size: 5rem;
            color: var(--primary-color);
            margin-bottom: 2rem;
            opacity: 0.8;
            animation: pulse 2s ease-in-out infinite;
        }
        
        @keyframes pulse {
            0%, 100% { transform: scale(1); opacity: 0.8; }
            50% { transform: scale(1.05); opacity: 1; }
        }
        
        .tracking-input {
            font-size: 1.25rem;
            padding: 1rem 1.5rem;
            border-radius: 1rem;
            border: 2px solid #e5e7eb;
            text-transform: uppercase;
            font-weight: 600;
            letter-spacing: 0.05em;
            transition: all 0.3s ease;
            min-height: 56px;
            background: #f9fafb;
        }
        
        .tracking-input:focus {
            border-color: var(--primary-color);
            box-shadow: 0 0 0 0.25rem rgba(30, 64, 175, 0.1);
            background: white;
            transform: translateY(-2px);
        }
        
        .tracking-input::placeholder {
            color: #9ca3af;
            font-weight: 400;
            letter-spacing: normal;
        }
        
        .btn-track {
            font-size: 1.25rem;
            padding: 1rem 2.5rem;
            border-radius: 1rem;
            background: linear-gradient(135deg, var(--primary-color) 0%, #1e3a8a 100%);
            border: none;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            box-shadow: 0 10px 25px rgba(30, 64, 175, 0.3);
            min-height: 56px;
            position: relative;
            overflow: hidden;
        }
        
        .btn-track::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.2), transparent);
            transition: left 0.5s ease;
        }
        
        .btn-track:hover::before {
            left: 100%;
        }
        
        .btn-track:hover {
            transform: translateY(-3px);
            box-shadow: 0 15px 35px rgba(30, 64, 175, 0.4);
        }
        
        .btn-track:active {
            transform: translateY(-1px);
        }

        /* Mobile Tracking Adjustments */
        @media (max-width: 768px) {
            .tracking-card {
                margin: 1rem 0;
                border-radius: 1rem;
            }
            
            .tracking-card .card-body {
                padding: 2rem 1.5rem;
            }
            
            .tracking-icon {
                font-size: 4rem;
                margin-bottom: 1.5rem;
            }
            
            .tracking-input {
                font-size: 1.1rem;
                padding: 0.875rem 1.25rem;
                min-height: 52px;
            }

            .btn-track {
                font-size: 1.1rem;
                padding: 0.875rem 2rem;
                width: 100%;
                min-height: 52px;
            }
        }

        @media (max-width: 576px) {
            .tracking-card .card-body {
                padding: 1.5rem 1rem;
            }
            
            .tracking-icon {
                font-size: 3.5rem;
                margin-bottom: 1rem;
            }
            
            .tracking-input {
                font-size: 1rem;
                padding: 0.75rem 1rem;
                min-height: 48px;
            }

            .btn-track {
                font-size: 1rem;
                padding: 0.75rem 1.5rem;
                min-height: 48px;
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
        
        .btn:hover {
            transform: translateY(-2px);
        }
        
        .btn:active {
            transform: translateY(0);
        }
        
        .btn-primary {
            background-color: var(--primary-color);
            border-color: var(--primary-color);
            color: white;
            box-shadow: 0 4px 6px rgba(30, 64, 175, 0.2);
        }
        
        .btn-primary:hover {
            background-color: #1e3a8a;
            border-color: #1e3a8a;
            color: white;
            box-shadow: 0 8px 15px rgba(30, 64, 175, 0.3);
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

        /* Mobile Button Adjustments */
        @media (max-width: 768px) {
            .mobile-btn-stack .btn {
                width: 100%;
                margin-bottom: 0.75rem;
                justify-content: center;
            }
            
            .mobile-btn-stack .btn:last-child {
                margin-bottom: 0;
            }
        }

        /* Enhanced Quick Links Card */
        .quick-links-card {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(20px);
            border-radius: 1rem;
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1);
            border: 1px solid rgba(255, 255, 255, 0.2);
            margin-top: 1.5rem;
        }
        
        .quick-links-card .card-body {
            padding: 1.5rem;
        }
        
        .quick-links-card h6 {
            color: #374151;
            font-weight: 700;
            margin-bottom: 1rem;
        }

        /* Information Section */
        .info-section {
            background: rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(10px);
            border-radius: 1rem;
            padding: 1.5rem;
            margin-top: 2rem;
            border: 1px solid rgba(255, 255, 255, 0.2);
        }
        
        .info-item {
            background: rgba(255, 255, 255, 0.1);
            border-radius: 0.75rem;
            padding: 1rem;
            margin-bottom: 1rem;
            transition: all 0.2s ease;
        }
        
        .info-item:hover {
            background: rgba(255, 255, 255, 0.15);
            transform: translateY(-2px);
        }
        
        .info-item h6 {
            color: white;
            font-weight: 700;
            margin-bottom: 0.5rem;
        }
        
        .info-item p {
            color: rgba(255, 255, 255, 0.9);
            margin-bottom: 0;
        }
        
        .info-item small {
            color: rgba(255, 255, 255, 0.7);
        }
        
        .info-item a {
            color: rgba(255, 255, 255, 0.9);
            text-decoration: none;
            font-weight: 500;
        }
        
        .info-item a:hover {
            color: white;
            text-decoration: underline;
        }

        /* Alert Styles */
        .alert {
            border-radius: 1rem;
            border: none;
            padding: 1rem 1.5rem;
            margin-bottom: 1.5rem;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }
        
        .alert-danger {
            background: linear-gradient(135deg, #fee2e2 0%, #fecaca 100%);
            color: #991b1b;
        }

        /* Form Validation */
        .is-invalid {
            border-color: #ef4444 !important;
            animation: shake 0.5s ease-in-out;
        }
        
        @keyframes shake {
            0%, 100% { transform: translateX(0); }
            25% { transform: translateX(-5px); }
            75% { transform: translateX(5px); }
        }
        
        .invalid-feedback {
            display: block;
            font-size: 0.875rem;
            color: #dc2626;
            margin-top: 0.5rem;
            font-weight: 500;
        }

        /* Footer Styles */
        .footer-content {
            text-align: center;
            color: rgba(255, 255, 255, 0.9);
            margin-top: 3rem;
            padding: 2rem 0;
        }
        
        .footer-content p {
            margin-bottom: 0.5rem;
            font-weight: 600;
        }
        
        .footer-content small {
            color: rgba(255, 255, 255, 0.7);
            font-style: italic;
        }

        /* Loading States */
        .btn.loading {
            pointer-events: none;
            opacity: 0.8;
        }
        
        .btn.loading::after {
            content: '';
            position: absolute;
            width: 20px;
            height: 20px;
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
            
            .tracking-icon {
                animation: none;
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
        }
        
        /* Touch-specific improvements */
        @media (hover: none) and (pointer: coarse) {
            .btn:hover {
                transform: none;
            }
            
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
                        <a href="{{ route('dashboard') }}" class="btn btn-primary">
                            <i class="bi bi-speedometer2 me-1"></i>
                            <span class="d-none d-sm-inline">Dashboard</span>
                            <span class="d-sm-none">Home</span>
                        </a>
                        <a href="{{ url('/') }}" class="btn btn-outline-secondary">
                            <i class="bi bi-house me-1"></i>
                            <span class="d-none d-sm-inline">Home</span>
                        </a>
                    </div>
                    @else
                    <div class="d-flex flex-column flex-sm-row gap-2 mobile-btn-stack">
                        <a href="{{ route('login') }}" class="btn btn-outline-primary">
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
            <div class="col-md-8 col-lg-6">
                <!-- Main Tracking Card -->
                <div class="card tracking-card">
                    <div class="card-body text-center">
                        <i class="bi bi-search tracking-icon"></i>
                        <h2 class="mb-3 fw-bold text-dark">Track Your Document</h2>
                        <p class="text-muted mb-4 lead">
                            Enter your tracking number to view document status and routing history.
                        </p>
                        
                        <!-- Flash Messages -->
                        @if(session('error'))
                            <div class="alert alert-danger d-flex align-items-center" role="alert">
                                <i class="bi bi-exclamation-triangle me-2 flex-shrink-0"></i>
                                <div>{{ session('error') }}</div>
                            </div>
                        @endif
                        
                        <form action="{{ route('public.track.search') }}" method="POST" id="trackingForm" novalidate>
                            @csrf
                            <div class="mb-4">
                                <div class="position-relative">
                                    <input type="text" 
                                           class="form-control tracking-input @error('tracking_number') is-invalid @enderror" 
                                           name="tracking_number" 
                                           id="trackingNumber"
                                           placeholder="Enter tracking number (e.g., {{ setting('document.tracking_prefix', 'MDJ') }}-202412-0001)" 
                                           value="{{ old('tracking_number') }}"
                                           required
                                           maxlength="50"
                                           autocomplete="off"
                                           aria-describedby="trackingHelp">
                                    <div class="position-absolute top-50 end-0 translate-middle-y me-3">
                                        <i class="bi bi-qr-code-scan text-muted" style="cursor: pointer;" onclick="openQRScanner()" title="Scan QR Code"></i>
                                    </div>
                                </div>
                                @error('tracking_number')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <small id="trackingHelp" class="form-text text-muted">
                                    Format: {{ setting('document.tracking_prefix', 'MDJ') }}-YYYYMM-XXXX
                                </small>
                            </div>
                            
                            <button type="submit" class="btn btn-track w-100" id="trackButton">
                                <i class="bi bi-search me-2"></i>
                                <span class="btn-text">Track Document</span>
                            </button>
                        </form>

                    </div>
                </div>
                
                <!-- Information Cards -->
                <div class="info-section">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <div class="info-item">
                                <h6><i class="bi bi-info-circle me-2"></i>Tracking Format</h6>
                                <p>{{ setting('document.tracking_prefix', 'MDJ') }}-YYYYMM-XXXX</p>
                                <small>Example: {{ setting('document.tracking_prefix', 'MDJ') }}-202412-0001</small>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="info-item">
                                <h6><i class="bi bi-headset me-2"></i>Need Help?</h6>
                                <p class="mb-1">
                                    <i class="bi bi-envelope me-1"></i>
                                    <a href="mailto:{{ setting('municipality.contact_email', 'info@madridejos.gov.ph') }}">
                                        {{ setting('municipality.contact_email', 'info@madridejos.gov.ph') }}
                                    </a>
                                </p>
                                <p class="mb-0">
                                    <i class="bi bi-telephone me-1"></i>
                                    <a href="tel:{{ str_replace(' ', '', setting('municipality.contact_phone', '(032) 123-4567')) }}">
                                        {{ setting('municipality.contact_phone', '(032) 123-4567') }}
                                    </a>
                                </p>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="info-item">
                                <h6><i class="bi bi-clock me-2"></i>Office Hours</h6>
                                <p class="mb-1">Monday - Friday</p>
                                <small>8:00 AM - 5:00 PM</small>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="info-item">
                                <h6><i class="bi bi-geo-alt me-2"></i>Location</h6>
                                <p class="mb-1">{{ setting('municipality.name', 'Municipality of Madridejos') }}</p>
                                <small>{{ setting('municipality.address', 'Madridejos, Cebu, Philippines') }}</small>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Quick Links -->
                <div class="card quick-links-card">
                    <div class="card-body">
                        <h6 class="text-center mb-3"><i class="bi bi-lightning me-2"></i>Quick Actions</h6>
                        <div class="row g-3">
                            <div class="col-6 col-sm-3">
                                <a href="{{ url('/') }}" class="btn btn-outline-primary w-100">
                                    <i class="bi bi-house d-block mb-1"></i>
                                    <small>Home</small>
                                </a>
                            </div>
                            <div class="col-6 col-sm-3">
                                @guest
                                <a href="{{ route('login') }}" class="btn btn-outline-secondary w-100">
                                    <i class="bi bi-box-arrow-in-right d-block mb-1"></i>
                                    <small>Staff Login</small>
                                </a>
                                @else
                                <a href="{{ route('dashboard') }}" class="btn btn-outline-success w-100">
                                    <i class="bi bi-speedometer2 d-block mb-1"></i>
                                    <small>Dashboard</small>
                                </a>
                                @endguest
                            </div>
                            <div class="col-6 col-sm-3">
                                <button type="button" class="btn btn-outline-info w-100" onclick="showTrackingTips()">
                                    <i class="bi bi-question-circle d-block mb-1"></i>
                                    <small>Help</small>
                                </button>
                            </div>
                            <div class="col-6 col-sm-3">
                                <button type="button" class="btn btn-outline-warning w-100" onclick="clearForm()">
                                    <i class="bi bi-arrow-clockwise d-block mb-1"></i>
                                    <small>Clear</small>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Enhanced Footer -->
    <div class="footer-content">
        <div class="container">
            <p>&copy; {{ date('Y') }} {{ setting('municipality.name', 'Municipality of Madridejos') }}. All rights reserved.</p>
            <small>Serving the people with transparency and efficiency</small>
        </div>
    </div>
    
    <!-- Help Modal -->
    <div class="modal fade" id="helpModal" tabindex="-1" aria-labelledby="helpModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="helpModalLabel">
                        <i class="bi bi-question-circle me-2"></i>Document Tracking Help
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="row g-4">
                        <div class="col-md-6">
                            <h6><i class="bi bi-search me-2 text-primary"></i>How to Track</h6>
                            <ol class="list-unstyled">
                                <li class="mb-2">1. Enter your tracking number in the format: <strong>{{ setting('document.tracking_prefix', 'MDJ') }}-YYYYMM-XXXX</strong></li>
                                <li class="mb-2">2. Click "Track Document" to search</li>
                                <li class="mb-2">3. View your document's current status and history</li>
                            </ol>
                        </div>
                        <div class="col-md-6">
                            <h6><i class="bi bi-info-circle me-2 text-info"></i>Tracking Number</h6>
                            <p>Your tracking number was provided when you submitted your document. It consists of:</p>
                            <ul class="list-unstyled">
                                <li><strong>{{ setting('document.tracking_prefix', 'MDJ') }}</strong> - Office prefix</li>
                                <li><strong>YYYYMM</strong> - Year and month</li>
                                <li><strong>XXXX</strong> - Sequential number</li>
                            </ul>
                        </div>
                        <div class="col-12">
                            <h6><i class="bi bi-exclamation-triangle me-2 text-warning"></i>Troubleshooting</h6>
                            <div class="accordion" id="faqAccordion">
                                <div class="accordion-item">
                                    <h2 class="accordion-header">
                                        <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faq1">
                                            Document not found?
                                        </button>
                                    </h2>
                                    <div id="faq1" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
                                        <div class="accordion-body">
                                            <ul>
                                                <li>Check the tracking number format</li>
                                                <li>Ensure all characters are correct</li>
                                                <li>Contact our office if the issue persists</li>
                                            </ul>
                                        </div>
                                    </div>
                                </div>
                                <div class="accordion-item">
                                    <h2 class="accordion-header">
                                        <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faq2">
                                            Status not updated?
                                        </button>
                                    </h2>
                                    <div id="faq2" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
                                        <div class="accordion-body">
                                            <p>Document status updates may take 1-2 business days. If your document status hasn't changed for several days, please contact our office.</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <a href="mailto:{{ setting('municipality.contact_email', 'info@madridejos.gov.ph') }}" class="btn btn-primary">
                        <i class="bi bi-envelope me-2"></i>Contact Support
                    </a>
                </div>
            </div>
        </div>
    </div>
    
    <!-- QR Scanner Modal (placeholder for future implementation) -->
    <div class="modal fade" id="qrModal" tabindex="-1" aria-labelledby="qrModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="qrModalLabel">
                        <i class="bi bi-qr-code-scan me-2"></i>QR Code Scanner
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body text-center">
                    <div class="p-4">
                        <i class="bi bi-qr-code-scan text-muted" style="font-size: 4rem;"></i>
                        <h6 class="mt-3">QR Code Scanner</h6>
                        <p class="text-muted">This feature will be available soon. For now, please enter your tracking number manually.</p>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    
    <!-- Enhanced Custom JavaScript -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const trackingInput = document.getElementById('trackingNumber');
            const trackingForm = document.getElementById('trackingForm');
            const trackButton = document.getElementById('trackButton');
            
            // Auto-format tracking number input
            trackingInput.addEventListener('input', function() {
                // Convert to uppercase
                this.value = this.value.toUpperCase();
                
                // Remove any characters that aren't letters, numbers, or hyphens
                this.value = this.value.replace(/[^A-Z0-9\-]/g, '');
                
                // Clear invalid state when user starts typing
                if (this.classList.contains('is-invalid')) {
                    this.classList.remove('is-invalid');
                    const feedback = this.parentElement.querySelector('.invalid-feedback');
                    if (feedback) {
                        feedback.style.display = 'none';
                    }
                }
            });
            
            // Real-time validation
            trackingInput.addEventListener('blur', function() {
                validateTrackingNumber(this.value);
            });
            
            // Enhanced form validation
            trackingForm.addEventListener('submit', function(e) {
                const trackingNumber = trackingInput.value.trim();
                
                if (!validateTrackingNumber(trackingNumber)) {
                    e.preventDefault();
                    return false;
                }
                
                // Show loading state
                setLoadingState(true);
                
                // Store tracking number in localStorage for recent searches
                addToRecentSearches(trackingNumber);
            });
            
            // Focus management
            trackingInput.addEventListener('focus', function() {
                this.select();
            });
            
            // Enter key handling
            trackingInput.addEventListener('keypress', function(e) {
                if (e.key === 'Enter') {
                    e.preventDefault();
                    trackingForm.dispatchEvent(new Event('submit'));
                }
            });
            
            // Clear form functionality
            window.clearForm = function() {
                trackingInput.value = '';
                trackingInput.classList.remove('is-invalid');
                trackingInput.focus();
                
                // Clear any error messages
                const feedback = document.querySelector('.invalid-feedback');
                if (feedback) {
                    feedback.style.display = 'none';
                }
            };
            
            // Show tracking tips modal
            window.showTrackingTips = function() {
                const helpModal = new bootstrap.Modal(document.getElementById('helpModal'));
                helpModal.show();
            };
            
            // QR Scanner placeholder
            window.openQRScanner = function() {
                const qrModal = new bootstrap.Modal(document.getElementById('qrModal'));
                qrModal.show();
            };
            
            // Validation function
            function validateTrackingNumber(value) {
                const prefix = '{{ setting("document.tracking_prefix", "MDJ") }}';
                const pattern = new RegExp(`^${prefix}-\\d{6}-\\d{4}<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="default">
    <meta name="theme-color" content="{{ setting('theme.primary_color', '#1e40af') }}">
    
    <title>Track Document - {{ setting('system.name', config('app.name')) }}</title>
    
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
            padding-top: var(--navbar-height);
            overflow-x: hidden;
            min-height: 100vh;
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

        /* Enhanced Tracking Card */
        .tracking-card {
            background: white;
            border-radius: 1.5rem;
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25);
            border: 0;
            overflow: hidden;
            position: relative;
            margin: 2rem 0;
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
        
        .tracking-card .card-body {
            padding: 3rem 2rem;
        }
        
        .tracking-icon {
            font-size: 5rem;
            color: var(--primary-color);
            margin-bottom: 2rem;
            opacity: 0.8;
            animation: pulse 2s ease-in-out infinite;
        }
        
        @keyframes pulse {
            0%, 100% { transform: scale(1); opacity: 0.8; }
            50% { transform: scale(1.05); opacity: 1; }
        }
        
        .tracking-input {
            font-size: 1.25rem;
            padding: 1rem 1.5rem;
            border-radius: 1rem;
            border: 2px solid #e5e7eb;
            text-transform: uppercase;
            font-weight: 600;
            letter-spacing: 0.05em;
            transition: all 0.3s ease;
            min-height: 56px;
            background: #f9fafb;
        }
        
        .tracking-input:focus {
            border-color: var(--primary-color);
            box-shadow: 0 0 0 0.25rem rgba(30, 64, 175, 0.1);
            background: white;
            transform: translateY(-2px);
        }
        
        .tracking-input::placeholder {
            color: #9ca3af;
            font-weight: 400;
            letter-spacing: normal;
        }
        
        .btn-track {
            font-size: 1.25rem;
            padding: 1rem 2.5rem;
            border-radius: 1rem;
            background: linear-gradient(135deg, var(--primary-color) 0%, #1e3a8a 100%);
            border: none;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            box-shadow: 0 10px 25px rgba(30, 64, 175, 0.3);
            min-height: 56px;
            position: relative;
            overflow: hidden;
        }
        
        .btn-track::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.2), transparent);
            transition: left 0.5s ease;
        }
        
        .btn-track:hover::before {
            left: 100%;
        }
        
        .btn-track:hover {
            transform: translateY(-3px);
            box-shadow: 0 15px 35px rgba(30, 64, 175, 0.4);
        }
        
        .btn-track:active {
            transform: translateY(-1px);
        }

        /* Mobile Tracking Adjustments */
        @media (max-width: 768px) {
            .tracking-card {
                margin: 1rem 0;
                border-radius: 1rem;
            }
            
            .tracking-card .card-body {
                padding: 2rem 1.5rem;
            }
            
            .tracking-icon {
                font-size: 4rem;
                margin-bottom: 1.5rem;
            }
            
            .tracking-input {
                font-size: 1.1rem;
                padding: 0.875rem 1.25rem;
                min-height: 52px;
            }

            .btn-track {
                font-size: 1.1rem;
                padding: 0.875rem 2rem;
                width: 100%;
                min-height: 52px;
            }
        }

        @media (max-width: 576px) {
            .tracking-card .card-body {
                padding: 1.5rem 1rem;
            }
            
            .tracking-icon {
                font-size: 3.5rem;
                margin-bottom: 1rem;
            }
            
            .tracking-input {
                font-size: 1rem;
                padding: 0.75rem 1rem;
                min-height: 48px;
            }

            .btn-track {
                font-size: 1rem;
                padding: 0.75rem 1.5rem;
                min-height: 48px;
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
        
        .btn:hover {
            transform: translateY(-2px);
        }
        
        .btn:active {
            transform: translateY(0);
        }
        
        .btn-primary {
            background-color: var(--primary-color);
            border-color: var(--primary-color);
            color: white;
            box-shadow: 0 4px 6px rgba(30, 64, 175, 0.2);
        }
        
        .btn-primary:hover {
            background-color: #1e3a8a;
            border-color: #1e3a8a;
            color: white;
            box-shadow: 0 8px 15px rgba(30, 64, 175, 0.3);
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

        /* Mobile Button Adjustments */
        @media (max-width: 768px) {
            .mobile-btn-stack .btn {
                width: 100%;
                margin-bottom: 0.75rem;
                justify-content: center;
            }
            
            .mobile-btn-stack .btn:last-child {
                margin-bottom: 0;
            }
        }

        /* Enhanced Quick Links Card */
        .quick-links-card {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(20px);
            border-radius: 1rem;
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1);
            border: 1px solid rgba(255, 255, 255, 0.2);
            margin-top: 1.5rem;
        }
        
        .quick-links-card .card-body {
            padding: 1.5rem;
        }
        
        .quick-links-card h6 {
            color: #374151;
            font-weight: 700;
            margin-bottom: 1rem;
        }

        /* Information Section */
        .info-section {
            background: rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(10px);
            border-radius: 1rem;
            padding: 1.5rem;
            margin-top: 2rem;
            border: 1px solid rgba(255, 255, 255, 0.2);
        }
        
        .info-item {
            background: rgba(255, 255, 255, 0.1);
            border-radius: 0.75rem;
            padding: 1rem;
            margin-bottom: 1rem;
            transition: all 0.2s ease;
        }
        
        .info-item:hover {
            background: rgba(255, 255, 255, 0.15);
            transform: translateY(-2px);
        }
        
        .info-item h6 {
            color: white;
            font-weight: 700;
            margin-bottom: 0.5rem;
        }
        
        .info-item p {
            color: rgba(255, 255, 255, 0.9);
            margin-bottom: 0;
        }
        
        .info-item small {
            color: rgba(255, 255, 255, 0.7);
        }
        
        .info-item a {
            color: rgba(255, 255, 255, 0.9);
            text-decoration: none;
            font-weight: 500;
        }
        
        .info-item a:hover {
            color: white;
            text-decoration: underline;
        }

        /* Alert Styles */
        .alert {
            border-radius: 1rem;
            border: none;
            padding: 1rem 1.5rem;
            margin-bottom: 1.5rem;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }
        
        .alert-danger {
            background: linear-gradient(135deg, #fee2e2 0%, #fecaca 100%);
            color: #991b1b;
        }

        /* Form Validation */
        .is-invalid {
            border-color: #ef4444 !important;
            animation: shake 0.5s ease-in-out;
        }
        
        @keyframes shake {
            0%, 100% { transform: translateX(0); }
            25% { transform: translateX(-5px); }
            75% { transform: translateX(5px); }
        }
        
        .invalid-feedback {
            display: block;
            font-size: 0.875rem;
            color: #dc2626;
            margin-top: 0.5rem;
            font-weight: 500;
        }

        /* Footer Styles */
        .footer-content {
            text-align: center;
            color: rgba(255, 255, 255, 0.9);
            margin-top: 3rem;
            padding: 2rem 0;
        }
        
        .footer-content p {
            margin-bottom: 0.5rem;
            font-weight: 600;
        }
        
        .footer-content small {
            color: rgba(255, 255, 255, 0.7);
            font-style: italic;
        }

        /* Loading States */
        .btn.loading {
            pointer-events: none;
            opacity: 0.8;
        }
        
        .btn.loading::after {
            content: '';
            position: absolute;
            width: 20px;
            height: 20px;
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
            
            .tracking-icon {
                animation: none;
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
        }
        
        /* Touch-specific improvements */
        @media (hover: none) and (pointer: coarse) {
            .btn:hover {
                transform: none;
            }
            
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
                        <a href="{{ route('dashboard') }}" class="btn btn-primary">
                            <i class="bi bi-speedometer2 me-1"></i>
                            <span class="d-none d-sm-inline">Dashboard</span>
                            <span class="d-sm-none">Home</span>
                        </a>
                        <a href="{{ url('/') }}" class="btn btn-outline-secondary">
                            <i class="bi bi-house me-1"></i>
                            <span class="d-none d-sm-inline">Home</span>
                        </a>
                    </div>
                    @else
                    <div class="d-flex flex-column flex-sm-row gap-2 mobile-btn-stack">
                        <a href="{{ route('login') }}" class="btn btn-outline-primary">
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
            <div class="col-md-8 col-lg-6">
                <!-- Main Tracking Card -->
                <div class="card tracking-card">
                    <div class="card-body text-center">
                        <i class="bi bi-search tracking-icon"></i>
);
                
                if (value.length < 5) {
                    showValidationError('Please enter a valid tracking number (at least 5 characters).');
                    return false;
                }
                
                if (!pattern.test(value)) {
                    showValidationError(`Tracking number must be in format: ${prefix}-YYYYMM-XXXX`);
                    return false;
                }
                
                clearValidationError();
                return true;
            }
            
            function showValidationError(message) {
                trackingInput.classList.add('is-invalid');
                let feedback = trackingInput.parentElement.querySelector('.invalid-feedback');
                if (!feedback) {
                    feedback = document.createElement('div');
                    feedback.className = 'invalid-feedback';
                    trackingInput.parentElement.appendChild(feedback);
                }
                feedback.textContent = message;
                feedback.style.display = 'block';
            }
            
            function clearValidationError() {
                trackingInput.classList.remove('is-invalid');
                const feedback = trackingInput.parentElement.querySelector('.invalid-feedback');
                if (feedback) {
                    feedback.style.display = 'none';
                }
            }
            
            function setLoadingState(loading) {
                if (loading) {
                    trackButton.disabled = true;
                    trackButton.classList.add('loading');
                    trackButton.querySelector('.btn-text').textContent = 'Searching...';
                } else {
                    trackButton.disabled = false;
                    trackButton.classList.remove('loading');
                    trackButton.querySelector('.btn-text').textContent = 'Track Document';
                }
            }
            
            function addToRecentSearches(trackingNumber) {
                try {
                    let recent = JSON.parse(localStorage.getItem('recentTrackingSearches') || '[]');
                    recent = recent.filter(item => item !== trackingNumber);
                    recent.unshift(trackingNumber);
                    recent = recent.slice(0, 5); // Keep only last 5 searches
                    localStorage.setItem('recentTrackingSearches', JSON.stringify(recent));
                } catch (e) {
                    console.warn('Could not save to localStorage:', e);
                }
            }
            
            // Load recent searches
            function loadRecentSearches() {
                try {
                    const recent = JSON.parse(localStorage.getItem('recentTrackingSearches') || '[]');
                    if (recent.length > 0) {
                        // Could add a recent searches section here
                        console.log('Recent searches:', recent);
                    }
                } catch (e) {
                    console.warn('Could not load from localStorage:', e);
                }
            }
            
            // Initialize recent searches
            loadRecentSearches();
            
            // Touch feedback for mobile
            if ('ontouchstart' in window) {
                document.querySelectorAll('.btn').forEach(button => {
                    button.addEventListener('touchstart', function() {
                        this.style.transform = 'scale(0.98)';
                    }, { passive: true });
                    
                    button.addEventListener('touchend', function() {
                        setTimeout(() => {
                            this.style.transform = '';
                        }, 100);
                    }, { passive: true });
                });
            }
            
            // Handle page visibility change
            document.addEventListener('visibilitychange', function() {
                if (document.visibilityState === 'visible') {
                    // Reset loading state if page becomes visible again
                    setLoadingState(false);
                }
            });
            
            // Handle back button
            window.addEventListener('pageshow', function(event) {
                if (event.persisted) {
                    // Reset loading state if page was cached
                    setLoadingState(false);
                }
            });
            
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
            
            // Auto-focus input on desktop
            if (window.innerWidth > 768) {
                setTimeout(() => {
                    trackingInput.focus();
                }, 500);
            }
            
            // Keyboard shortcuts
            document.addEventListener('keydown', function(e) {
                // Ctrl/Cmd + K to focus search
                if ((e.ctrlKey || e.metaKey) && e.key === 'k') {
                    e.preventDefault();
                    trackingInput.focus();
                    trackingInput.select();
                }
                
                // Escape to clear form
                if (e.key === 'Escape') {
                    clearForm();
                }
            });
            
            // Analytics tracking (if configured)
            if (typeof gtag !== 'undefined') {
                gtag('config', 'GA_MEASUREMENT_ID', {
                    page_title: 'Document Tracking',
                    page_location: window.location.href
                });
            }
        });
    </script>
</body>
</html>