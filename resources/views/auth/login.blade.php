<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="default">
    
    <title>{{ __('Login') }} - {{ setting('system.name', config('app.name')) }}</title>
    
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
    
    <!-- Apple Touch Icons -->
    <link rel="apple-touch-icon" sizes="180x180" href="{{ setting('appearance.favicon', asset('favicon.ico')) }}">
    
    <!-- Dynamic Styles -->
    <link rel="stylesheet" href="{{ route('dynamic-styles.css') }}?v={{ time() }}">
    
    <style>
        :root {
            --primary-color: {{ setting('theme.primary_color', '#1e40af') }};
            --border-radius: {{ setting('theme.border_radius', '1') }}rem;
            --font-family: {{ setting('theme.font_family', 'Inter, sans-serif') }};
        }
        
        body {
            font-family: var(--font-family);
            background: linear-gradient(135deg, {{ setting('theme.primary_color', '#667eea') }} 0%, #764ba2 100%);
            min-height: 100vh;
            margin: 0;
            padding: 0;
            -webkit-font-smoothing: antialiased;
            -moz-osx-font-smoothing: grayscale;
            overflow-x: hidden;
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
        
        /* Mobile-optimized navbar */
        .navbar {
            padding: 0.75rem 0;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }
        
        .navbar-brand {
            font-size: 1.1rem;
            font-weight: 700;
        }
        
        .navbar-brand img {
            height: 32px;
            width: auto;
            margin-right: 0.5rem;
        }
        
        @media (max-width: 576px) {
            .navbar {
                padding: 0.5rem 0;
            }
            
            .navbar-brand {
                font-size: 1rem;
            }
            
            .navbar-brand img {
                height: 28px;
                margin-right: 0.25rem;
            }
            
            .btn-group .btn {
                padding: 0.375rem 0.75rem;
                font-size: 0.875rem;
            }
        }
        
        /* Mobile-first login container */
        .login-container {
            padding: 1rem;
            min-height: calc(100vh - 70px);
            display: flex;
            align-items: center;
            justify-content: center;
        }
        
        @media (max-width: 576px) {
            .login-container {
                padding: 0.75rem;
                min-height: calc(100vh - 60px);
            }
        }
        
        .login-card {
            background: white;
            border-radius: var(--border-radius);
            box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
            border: 0;
            width: 100%;
            max-width: 450px;
            overflow: hidden;
        }
        
        @media (max-width: 576px) {
            .login-card {
                max-width: 100%;
                margin: 0;
                border-radius: calc(var(--border-radius) * 0.75);
            }
        }
        
        .login-header {
            background: linear-gradient(135deg, var(--primary-color) 0%, #1e3a8a 100%);
            color: white;
            padding: 2rem;
            text-align: center;
        }
        
        @media (max-width: 576px) {
            .login-header {
                padding: 1.5rem 1rem;
            }
        }
        
        .logo-login {
            max-height: 80px;
            width: auto;
            margin-bottom: 1rem;
        }
        
        @media (max-width: 576px) {
            .logo-login {
                max-height: 60px;
                margin-bottom: 0.75rem;
            }
        }
        
        .login-header h2 {
            margin-bottom: 0.5rem;
            font-size: 1.75rem;
            font-weight: 700;
        }
        
        .login-header p {
            margin-bottom: 0;
            opacity: 0.9;
            font-size: 0.95rem;
        }
        
        @media (max-width: 576px) {
            .login-header h2 {
                font-size: 1.5rem;
            }
            
            .login-header p {
                font-size: 0.875rem;
            }
        }
        
        /* Enhanced form styling */
        .card-body {
            padding: 2rem;
        }
        
        @media (max-width: 576px) {
            .card-body {
                padding: 1.5rem;
            }
        }
        
        .form-label {
            font-weight: 600;
            color: #374151;
            margin-bottom: 0.5rem;
        }
        
        .input-group {
            margin-bottom: 0.5rem;
        }
        
        .input-group-text {
            background-color: #f9fafb;
            border-right: none;
            color: #6b7280;
            width: 45px;
            justify-content: center;
        }
        
        .form-control {
            border-left: none;
            padding: 0.75rem 1rem;
            font-size: 1rem;
            min-height: 48px; /* Touch-friendly */
            border-radius: 0 var(--border-radius) var(--border-radius) 0;
        }
        
        /* Prevent zoom on iOS */
        @media (max-width: 576px) {
            .form-control {
                font-size: 16px !important;
            }
        }
        
        .form-control:focus {
            border-color: var(--primary-color);
            box-shadow: 0 0 0 0.2rem rgba(30, 64, 175, 0.25);
            border-left: none;
        }
        
        .form-control:focus + .input-group-text {
            border-color: var(--primary-color);
        }
        
        .input-group .form-control:first-child {
            border-left: 1px solid #d1d5db;
            border-radius: var(--border-radius) 0 0 var(--border-radius);
        }
        
        /* Enhanced button styling */
        .btn {
            font-weight: 600;
            padding: 0.75rem 2rem;
            border-radius: var(--border-radius);
            min-height: 48px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            transition: all 0.2s ease;
        }
        
        .btn-primary {
            background-color: var(--primary-color);
            border-color: var(--primary-color);
        }
        
        .btn-primary:hover, .btn-primary:focus {
            background-color: #1e3a8a;
            border-color: #1e3a8a;
            transform: translateY(-1px);
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.15);
        }
        
        .btn-outline-secondary {
            border-color: #d1d5db;
            color: #6b7280;
        }
        
        .btn-outline-secondary:hover {
            background-color: #f9fafb;
            border-color: #9ca3af;
            color: #374151;
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
        
        @media (max-width: 576px) {
            .btn {
                padding: 0.625rem 1.5rem;
                font-size: 0.95rem;
            }
        }
        
        /* Toggle password button */
        #togglePassword {
            min-width: 45px;
            border-left: none !important;
            background-color: #f9fafb;
            border-color: #d1d5db;
            color: #6b7280;
        }
        
        #togglePassword:hover {
            background-color: #f3f4f6;
            color: #374151;
        }
        
        /* Form validation styling */
        .invalid-feedback {
            display: block;
            font-size: 0.875rem;
            color: #dc2626;
            margin-top: 0.25rem;
        }
        
        .is-invalid {
            border-color: #dc2626;
        }
        
        .is-invalid:focus {
            border-color: #dc2626;
            box-shadow: 0 0 0 0.2rem rgba(220, 38, 38, 0.25);
        }
        
        /* Alert styling */
        .alert {
            border-radius: var(--border-radius);
            border: none;
            font-weight: 500;
            margin-bottom: 1.5rem;
        }
        
        .alert-success {
            background-color: #d1fae5;
            color: #065f46;
            border-left: 4px solid #10b981;
        }
        
        .alert-danger {
            background-color: #fee2e2;
            color: #991b1b;
            border-left: 4px solid #ef4444;
        }

        .alert-warning {
            background-color: #fef3c7;
            color: #92400e;
            border-left: 4px solid #f59e0b;
        }

        .alert-info {
            background-color: #dbeafe;
            color: #1e40af;
            border-left: 4px solid #3b82f6;
        }
        
        /* Form check styling */
        .form-check {
            margin: 1rem 0;
        }
        
        .form-check-input {
            width: 1.25rem;
            height: 1.25rem;
            margin-top: 0;
            border-radius: 0.25rem;
        }
        
        .form-check-input:checked {
            background-color: var(--primary-color);
            border-color: var(--primary-color);
        }
        
        .form-check-label {
            margin-left: 0.5rem;
            font-weight: 500;
            color: #374151;
        }
        
        /* Footer styling */
        .card-footer {
            background-color: #f9fafb;
            border-top: 1px solid #e5e7eb;
            padding: 1rem 2rem;
            text-align: center;
        }
        
        @media (max-width: 576px) {
            .card-footer {
                padding: 1rem 1.5rem;
            }
        }
        
        /* Link styling */
        a {
            color: var(--primary-color);
            text-decoration: none;
            font-weight: 500;
        }
        
        a:hover {
            color: #1e3a8a;
            text-decoration: underline;
        }
        
        /* Loading state */
        .btn.loading {
            pointer-events: none;
            opacity: 0.7;
        }
        
        .spinner-border-sm {
            width: 1rem;
            height: 1rem;
        }
        
        /* Mobile navigation improvements */
        @media (max-width: 576px) {
            .d-flex.gap-2 {
                gap: 0.5rem !important;
            }
            
            .btn-group .btn:not(:last-child) {
                margin-right: 0.25rem;
            }
        }
        
        /* Accessibility improvements */
        .form-control:focus,
        .btn:focus,
        .form-check-input:focus {
            outline: 2px solid var(--primary-color);
            outline-offset: 2px;
        }
        
        /* Prevent horizontal scroll */
        .container {
            max-width: 100%;
            overflow-x: hidden;
        }
        
        /* Mobile keyboard adjustments */
        @media (max-width: 576px) {
            @supports (-webkit-appearance: none) {
                .login-container {
                    min-height: calc(100vh - 60px);
                    min-height: -webkit-fill-available;
                }
            }
        }
    </style>
</head>
<body>
    <!-- Enhanced Mobile Navigation -->
    <nav class="navbar navbar-expand-lg navbar-light bg-white shadow-sm fixed-top">
        <div class="container">
            <a class="navbar-brand fw-bold d-flex align-items-center" href="{{ url('/') }}">
                @if(setting('appearance.logo'))
                    <img src="{{ setting('appearance.logo') }}" 
                        alt="{{ setting('system.name', config('app.name')) }}" 
                        class="me-2">
                @endif
                <span class="d-none d-md-inline">{{ setting('system.name', config('app.name')) }}</span>
                <span class="d-md-none">{{ Str::limit(setting('system.name', config('app.name')), 15) }}</span>
            </a>
            
            <div class="ms-auto">
                @auth
                <div class="d-flex gap-2">
                    <a href="{{ route('dashboard') }}" class="btn btn-primary">
                        <i class="bi bi-speedometer2 me-1 d-none d-sm-inline"></i>
                        <span class="d-none d-sm-inline">Dashboard</span>
                        <span class="d-sm-none">Home</span>
                    </a>
                    <a href="{{ url('/') }}" class="btn btn-outline-secondary d-none d-sm-inline-flex">
                        <i class="bi bi-house me-2"></i>Home
                    </a>
                </div>
                @else
                <div class="d-flex gap-2">
                    <a href="{{ url('/track') }}" class="btn btn-outline-primary">
                        <i class="bi bi-search me-1 d-none d-sm-inline"></i>
                        <span class="d-none d-sm-inline">Track Document</span>
                        <span class="d-sm-none">Track</span>
                    </a>
                    <a href="{{ url('/') }}" class="btn btn-outline-secondary d-none d-sm-inline-flex">
                        <i class="bi bi-house me-2"></i>Home
                    </a>
                </div>
                @endauth
            </div>
        </div>
    </nav>

    <!-- Login Container -->
    <div class="login-container">
        <div class="login-card">
            <!-- Login Header -->
            <div class="login-header">
                @if(setting('appearance.logo'))
                    <img src="{{ setting('appearance.logo') }}" 
                        alt="{{ setting('system.name', config('app.name')) }}" 
                        class="logo-login">
                @endif
                <h2 class="mb-2">Welcome Back</h2>
                <p class="mb-0 opacity-90">{{ setting('municipality.name', 'Municipality of Madridejos') }}</p>
            </div>
            
            <!-- Login Form -->
            <div class="card-body">
                <!-- Status Messages -->
                @if (session('status'))
                    <div class="alert alert-success" role="alert">
                        <i class="bi bi-check-circle me-2"></i>
                        {{ session('status') }}
                    </div>
                @endif

                @if (session('success'))
                    <div class="alert alert-success" role="alert">
                        <i class="bi bi-check-circle me-2"></i>
                        {{ session('success') }}
                    </div>
                @endif

                @if (session('error'))
                    <div class="alert alert-danger" role="alert">
                        <i class="bi bi-exclamation-triangle me-2"></i>
                        {{ session('error') }}
                    </div>
                @endif

                @if (session('warning'))
                    <div class="alert alert-warning" role="alert">
                        <i class="bi bi-exclamation-triangle me-2"></i>
                        {{ session('warning') }}
                    </div>
                @endif

                @if (session('info'))
                    <div class="alert alert-info" role="alert">
                        <i class="bi bi-info-circle me-2"></i>
                        {{ session('info') }}
                    </div>
                @endif
                
                <!-- Validation Errors -->
                @if ($errors->any())
                    <div class="alert alert-danger">
                        <i class="bi bi-exclamation-triangle me-2"></i>
                        @if($errors->has('email') && str_contains($errors->first('email'), 'disabled'))
                            <!-- Special handling for account disabled message -->
                            <strong>Account Disabled</strong><br>
                            {{ $errors->first('email') }}
                            <br><br>
                            <small>If you believe this is an error, please contact your system administrator for assistance.</small>
                        @else
                            @foreach ($errors->all() as $error)
                                <div>{{ $error }}</div>
                            @endforeach
                        @endif
                    </div>
                @endif
                
                <form method="POST" action="{{ route('login') }}" id="loginForm">
                    @csrf
                    
                    <!-- Email Field -->
                    <div class="mb-3">
                        <label for="email" class="form-label">{{ __('Email Address') }}</label>
                        <div class="input-group">
                            <span class="input-group-text">
                                <i class="bi bi-envelope"></i>
                            </span>
                            <input id="email" 
                                   type="email" 
                                   class="form-control @error('email') is-invalid @enderror" 
                                   name="email" 
                                   value="{{ old('email') }}" 
                                   required 
                                   autocomplete="email" 
                                   autofocus
                                   placeholder="Enter your email address"
                                   aria-describedby="emailHelp">
                        </div>
                        @error('email')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <!-- Password Field -->
                    <div class="mb-3">
                        <label for="password" class="form-label">{{ __('Password') }}</label>
                        <div class="input-group">
                            <span class="input-group-text">
                                <i class="bi bi-lock"></i>
                            </span>
                            <input id="password" 
                                   type="password" 
                                   class="form-control @error('password') is-invalid @enderror" 
                                   name="password" 
                                   required 
                                   autocomplete="current-password"
                                   placeholder="Enter your password"
                                   aria-describedby="passwordHelp">
                            <button class="btn btn-outline-secondary" 
                                    type="button" 
                                    id="togglePassword"
                                    aria-label="Toggle password visibility">
                                <i class="bi bi-eye" id="toggleIcon"></i>
                            </button>
                        </div>
                        @error('password')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <!-- Remember Me -->
                    <div class="form-check">
                        <input class="form-check-input" 
                               type="checkbox" 
                               name="remember" 
                               id="remember" 
                               {{ old('remember') ? 'checked' : '' }}>
                        <label class="form-check-label" for="remember">
                            {{ __('Remember Me') }}
                        </label>
                    </div>
                    
                    <!-- Submit Button -->
                    <div class="d-grid mb-3">
                        <button type="submit" class="btn btn-primary" id="loginBtn">
                            <i class="bi bi-box-arrow-in-right me-2"></i>
                            {{ __('Login') }}
                        </button>
                    </div>
                    
                    <!-- Forgot Password Link -->
                    @if (Route::has('password.request'))
                        <div class="text-center">
                            <a href="{{ route('password.request') }}">
                                {{ __('Forgot Your Password?') }}
                            </a>
                        </div>
                    @endif
                </form>
            </div>
            
            <!-- Footer -->
            <div class="card-footer">
                <small class="text-muted">
                    © {{ date('Y') }} {{ setting('municipality.name', 'Municipality of Madridejos') }}. All rights reserved.
                </small>
            </div>
        </div>
    </div>
    
    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    
    <script>
        // Toggle password visibility
        document.getElementById('togglePassword').addEventListener('click', function() {
            const password = document.getElementById('password');
            const icon = document.getElementById('toggleIcon');
            
            if (password.type === 'password') {
                password.type = 'text';
                icon.classList.remove('bi-eye');
                icon.classList.add('bi-eye-slash');
            } else {
                password.type = 'password';
                icon.classList.remove('bi-eye-slash');
                icon.classList.add('bi-eye');
            }
        });

        // Form submission loading state
        document.getElementById('loginForm').addEventListener('submit', function() {
            const btn = document.getElementById('loginBtn');
            btn.disabled = true;
            btn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Signing in...';
        });

        // Auto-hide success/info alerts after 5 seconds
        setTimeout(function() {
            const alerts = document.querySelectorAll('.alert-success, .alert-info');
            alerts.forEach(function(alert) {
                if (alert.classList.contains('alert-success') || alert.classList.contains('alert-info')) {
                    const bsAlert = new bootstrap.Alert(alert);
                    bsAlert.close();
                }
            });
        }, 5000);
    </script>
</body>
</html>