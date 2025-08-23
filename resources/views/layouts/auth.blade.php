<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    
    <title>@yield('title', 'Authentication') - {{ setting('system.name', config('app.name')) }}</title>
    
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
    
    <!-- Dynamic Styles -->
    <link rel="stylesheet" href="{{ route('dynamic-styles.css') }}?v={{ time() }}">
    
    <style>
        body {
            font-family: {{ setting('theme.font_family', 'Inter, sans-serif') }};
            background: linear-gradient(135deg, {{ setting('theme.primary_color', '#667eea') }} 0%, #764ba2 100%);
            min-height: 100vh;
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
        
        .auth-card {
            background: white;
            border-radius: {{ setting('theme.border_radius', '1') }}rem;
            box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
            border: 0;
        }
        
        .auth-header {
            background: linear-gradient(135deg, {{ setting('theme.primary_color', '#1e40af') }} 0%, #1e3a8a 100%);
            color: white;
            border-radius: {{ setting('theme.border_radius', '1') }}rem {{ setting('theme.border_radius', '1') }}rem 0 0;
            padding: 2rem;
            text-align: center;
        }
        
        .form-control:focus {
            border-color: {{ setting('theme.primary_color', '#1e40af') }};
            box-shadow: 0 0 0 0.2rem rgba(30, 64, 175, 0.25);
        }
        
        .btn-primary {
            background-color: {{ setting('theme.primary_color', '#1e40af') }};
            border-color: {{ setting('theme.primary_color', '#1e40af') }};
            padding: 0.75rem 2rem;
            font-weight: 600;
            border-radius: {{ setting('theme.border_radius', '0.5') }}rem;
        }
        
        .btn-primary:hover {
            background-color: #1e3a8a;
            border-color: #1e3a8a;
        }
    </style>
    
    @stack('styles')
</head>
<body>
    <div class="container">
        <div class="row justify-content-center align-items-center min-vh-100">
            <div class="col-md-6 col-lg-5">
                <div class="card auth-card">
                    <div class="auth-header">
                        <img src="{{ setting('appearance.logo', asset('images/logos/logo-white.png')) }}" 
                            alt="{{ setting('system.name', config('app.name')) }}" 
                            class="logo-auth mb-3"
                            style="height: 80px; width: auto;">
                        <h3 class="mb-1">{{ setting('system.name', config('app.name')) }}</h3>
                        <p class="mb-0 opacity-75">{{ setting('municipality.name', 'Municipality of Madridejos') }}</p>
                    </div>
                    
                    <div class="card-body p-4">
                        <!-- Flash Messages -->
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
                        
                        @if(session('warning'))
                            <div class="alert alert-warning alert-dismissible fade show" role="alert">
                                <i class="bi bi-exclamation-triangle me-2"></i>
                                {{ session('warning') }}
                                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                            </div>
                        @endif
                        
                        @if(session('info'))
                            <div class="alert alert-info alert-dismissible fade show" role="alert">
                                <i class="bi bi-info-circle me-2"></i>
                                {{ session('info') }}
                                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                            </div>
                        @endif
                        
                        @if(session('status'))
                            <div class="alert alert-success alert-dismissible fade show" role="alert">
                                <i class="bi bi-check-circle me-2"></i>
                                {{ session('status') }}
                                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                            </div>
                        @endif
                        
                        @yield('content')
                    </div>
                    
                    <div class="card-footer text-center bg-light border-0">
                        <small class="text-muted">
                            © {{ date('Y') }} {{ setting('municipality.name', 'Municipality of Madridejos') }}. All rights reserved.
                        </small>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    
    <script>
        // CSRF Token Setup
        window.Laravel = {
            csrfToken: '{{ csrf_token() }}'
        };
        
        // Auto-hide alerts
        setTimeout(function() {
            const alerts = document.querySelectorAll('.alert');
            alerts.forEach(function(alert) {
                if (alert.classList.contains('alert-success') || 
                    alert.classList.contains('alert-info')) {
                    const bsAlert = new bootstrap.Alert(alert);
                    bsAlert.close();
                }
            });
        }, 5000);
    </script>
    
    @stack('scripts')
</body>
</html>