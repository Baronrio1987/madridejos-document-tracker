<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Page Not Found - {{ config('app.name') }}</title>
    
    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700&display=swap" rel="stylesheet" />
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css" rel="stylesheet">
    
    <style>
        body {
            font-family: 'Inter', sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        
        .error-card {
            background: white;
            border-radius: 1rem;
            box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1);
            border: 0;
            max-width: 500px;
            width: 100%;
        }
        
        .error-number {
            font-size: 6rem;
            font-weight: 700;
            color: #1e40af;
            line-height: 1;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-8 col-lg-6">
                <div class="card error-card">
                    <div class="card-body text-center p-5">
                        <div class="error-number mb-3">404</div>
                        <h2 class="mb-3">Page Not Found</h2>
                        <p class="text-muted mb-4">
                            Sorry, the page you are looking for could not be found. 
                            It may have been moved, deleted, or you entered the wrong URL.
                        </p>
                        
                        <div class="d-flex flex-column flex-sm-row gap-3 justify-content-center">
                            <a href="{{ url('/') }}" class="btn btn-primary">
                                <i class="bi bi-house me-2"></i>Go Home
                            </a>
                            <button class="btn btn-outline-secondary" onclick="history.back()">
                                <i class="bi bi-arrow-left me-2"></i>Go Back
                            </button>
                        </div>
                        
                        <!-- Quick Links -->
                        <div class="mt-4 pt-4 border-top">
                            <p class="text-muted small mb-3">Quick Links:</p>
                            <div class="d-flex flex-wrap gap-3 justify-content-center">
                                @auth
                                <a href="{{ route('dashboard') }}" class="text-decoration-none small">
                                    <i class="bi bi-speedometer2 me-1"></i>Dashboard
                                </a>
                                <a href="{{ route('documents.index') }}" class="text-decoration-none small">
                                    <i class="bi bi-file-earmark-text me-1"></i>Documents
                                </a>
                                @else
                                <a href="{{ route('login') }}" class="text-decoration-none small">
                                    <i class="bi bi-box-arrow-in-right me-1"></i>Login
                                </a>
                                @endauth
                                <a href="{{ url('/track') }}" class="text-decoration-none small">
                                    <i class="bi bi-search me-1"></i>Track Document
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>