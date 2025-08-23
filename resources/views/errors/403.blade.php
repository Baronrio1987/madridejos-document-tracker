<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Access Denied - {{ config('app.name') }}</title>
    
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
            background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%);
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
            color: #d97706;
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
                        <div class="error-number mb-3">403</div>
                        <h2 class="mb-3">Access Denied</h2>
                        <p class="text-muted mb-4">
                            You don't have permission to access this resource. 
                            Please contact your administrator if you believe this is an error.
                        </p>
                        
                        <div class="d-flex flex-column flex-sm-row gap-3 justify-content-center">
                            @auth
                            <a href="{{ route('dashboard') }}" class="btn btn-primary">
                                <i class="bi bi-speedometer2 me-2"></i>Go to Dashboard
                            </a>
                            @else
                            <a href="{{ route('login') }}" class="btn btn-primary">
                                <i class="bi bi-box-arrow-in-right me-2"></i>Login
                            </a>
                            @endauth
                            <button class="btn btn-outline-secondary" onclick="history.back()">
                                <i class="bi bi-arrow-left me-2"></i>Go Back
                            </button>
                        </div>
                        
                        <!-- User Info -->
                        @auth
                        <div class="mt-4 pt-4 border-top">
                            <p class="text-muted small mb-2">Logged in as:</p>
                            <p class="small fw-semibold">{{ Auth::user()->name }} ({{ ucfirst(Auth::user()->role) }})</p>
                        </div>
                        @endauth
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
