<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Server Error - {{ config('app.name') }}</title>
    
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
            background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%);
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
            color: #dc2626;
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
                        <div class="error-number mb-3">500</div>
                        <h2 class="mb-3">Server Error</h2>
                        <p class="text-muted mb-4">
                            We're sorry, but something went wrong on our end. 
                            Our team has been notified and is working to fix the issue.
                        </p>
                        
                        <div class="d-flex flex-column flex-sm-row gap-3 justify-content-center">
                            <a href="{{ url('/') }}" class="btn btn-primary">
                                <i class="bi bi-house me-2"></i>Go Home
                            </a>
                            <button class="btn btn-outline-secondary" onclick="location.reload()">
                                <i class="bi bi-arrow-clockwise me-2"></i>Try Again
                            </button>
                        </div>
                        
                        <!-- Contact Info -->
                        <div class="mt-4 pt-4 border-top">
                            <p class="text-muted small mb-2">If the problem persists, please contact:</p>
                            <p class="small">
                                <i class="bi bi-envelope me-2"></i>
                                <a href="mailto:support@madridejos.gov.ph" class="text-decoration-none">support@madridejos.gov.ph</a>
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>