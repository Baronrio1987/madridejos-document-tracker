<?php

use App\Http\Controllers\DashboardController;
use App\Http\Controllers\DocumentController;
use App\Http\Controllers\DocumentRouteController;
use App\Http\Controllers\DocumentAttachmentController;
use App\Http\Controllers\DocumentCommentController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\DepartmentController;
use App\Http\Controllers\DocumentTypeController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\AnalyticsController;
use App\Http\Controllers\SettingsController;
use App\Http\Controllers\QrCodeController;
use App\Http\Controllers\PublicTrackingController;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

// Authentication Routes (Laravel UI) - FIRST
Auth::routes([
    'register' => false,
    'reset' => true,
    'verify' => false,
]);

// CSS Route - ISOLATED to prevent conflicts
Route::get('/assets/css/dynamic-styles.css', function() {
    $controller = app(SettingsController::class);
    return $controller->generateCss();
})->name('dynamic-styles.css');

// Public Routes
Route::get('/', function () {
    if (Auth::check()) {
        \Log::info('Root route: User authenticated, redirecting to dashboard', ['user_id' => Auth::id()]);
        return redirect('/dashboard');
    }
    return view('welcome');
});

// Public Document Tracking Routes
Route::prefix('track')->name('public.track.')->group(function () {
    Route::get('/', [PublicTrackingController::class, 'index'])->name('index');
    Route::post('/', [PublicTrackingController::class, 'search'])->name('search');
    Route::get('/{trackingNumber}', [PublicTrackingController::class, 'show'])
        ->name('show')
        ->where('trackingNumber', '[A-Z0-9\-]+');
});

// Debug route (remove in production)
Route::get('/debug-login', function() {
    return [
        'authenticated' => auth()->check(),
        'user' => auth()->user()?->only(['id', 'name', 'email']),
        'intended_url' => session('url.intended'),
        'current_route' => request()->route()?->getName(),
        'current_url' => request()->url(),
        'RouteServiceProvider_HOME' => \App\Providers\RouteServiceProvider::HOME,
    ];
})->name('debug.login');

// Authenticated Routes with Active User Check
Route::middleware(['auth', 'active.user', 'activity.log'])->group(function () {

    // Dashboard
    Route::get('/dashboard', function() {
        \Log::info('Dashboard accessed', ['user_id' => auth()->id()]);
        return app(DashboardController::class)->index();
    })->name('dashboard');

    // User Profile Routes
    Route::prefix('profile')->name('profile.')->group(function () {
        Route::get('/', [UserController::class, 'profile'])->name('show');
        Route::put('/', [UserController::class, 'updateProfile'])->name('update');
    });

    // File Downloads and Viewing
    Route::get('/attachments/{attachment}/view', [DocumentAttachmentController::class, 'view'])
        ->name('attachments.view');
    Route::get('/attachments/{attachment}/download', [DocumentAttachmentController::class, 'download'])
        ->name('attachments.download');

    // Document Routes
    Route::prefix('documents')->name('documents.')->group(function () {
        
        // List and search
        Route::get('/', [DocumentController::class, 'index'])->name('index');
        Route::get('/track/{trackingNumber}', [DocumentController::class, 'track'])
            ->name('track')
            ->where('trackingNumber', '[A-Z0-9\-]+');

        // Create routes - Requires specific roles
        Route::middleware('role:admin,department_head,encoder')->group(function () {
            Route::get('/create', [DocumentController::class, 'create'])->name('create');
            Route::post('/', [DocumentController::class, 'store'])->name('store');
        });

        // SPECIFIC DOCUMENT ROUTES - THESE MUST COME BEFORE THE GENERAL {document} ROUTES
        // Document status update
        Route::patch('/{document}/status', [DocumentController::class, 'updateStatus'])
            ->name('update-status')
            ->middleware('can:update,document');
        
        // Attachment routes
        Route::post('/{document}/attachments', [DocumentAttachmentController::class, 'store'])
            ->name('attachment.store')
            ->middleware('can:update,document');
        Route::delete('/{document}/attachments/{attachment}', [DocumentAttachmentController::class, 'destroy'])
            ->name('attachment.destroy')
            ->middleware('can:update,document');

        // Comment routes
        Route::post('/{document}/comments', [DocumentCommentController::class, 'store'])
            ->name('comment.store');
        Route::put('/{document}/comments/{comment}', [DocumentCommentController::class, 'update'])
            ->name('comment.update')
            ->middleware('can:update,comment');
        Route::delete('/{document}/comments/{comment}', [DocumentCommentController::class, 'destroy'])
            ->name('comment.destroy')
            ->middleware('can:delete,comment');

        // Document routing
        Route::get('/{document}/route', [DocumentRouteController::class, 'create'])
            ->name('routing.create')
            ->middleware('can:update,document');
        Route::post('/{document}/route', [DocumentRouteController::class, 'store'])
            ->name('routing.store')
            ->middleware('can:update,document');

        // QR Code generation
        Route::post('/{document}/qr-code', [DocumentController::class, 'generateQrCode'])
            ->name('generate-qr')
            ->middleware('can:view,document');

        // GENERAL DOCUMENT ROUTES - THESE MUST COME LAST
        Route::get('/{document}', [DocumentController::class, 'show'])
            ->name('show')
            ->middleware('can:view,document');
        Route::get('/{document}/edit', [DocumentController::class, 'edit'])
            ->name('edit')
            ->middleware('can:update,document');
        Route::put('/{document}', [DocumentController::class, 'update'])
            ->name('update')
            ->middleware('can:update,document');
        Route::delete('/{document}', [DocumentController::class, 'destroy'])
            ->name('destroy')
            ->middleware('can:delete,document');
    });

    // Document Routes Management
    Route::prefix('document-routes')->name('document-routes.')->group(function () {
        Route::get('/', [DocumentRouteController::class, 'index'])->name('index');
        
        Route::patch('/{route}/receive', [DocumentRouteController::class, 'receive'])
            ->name('receive')
            ->middleware('can:receive,route');
        Route::patch('/{route}/process', [DocumentRouteController::class, 'process'])
            ->name('process')
            ->middleware('can:process,route');

        // Bulk operations for admins and department heads
        Route::middleware('role:admin,department_head')->group(function () {
            Route::post('/bulk-route', [DocumentRouteController::class, 'bulkRoute'])->name('bulk-route');
        });
    });

    // Routing Templates API endpoint
    Route::get('/routing-templates/{template}', [DocumentRouteController::class, 'getRoutingTemplate'])
        ->name('routing-templates.show')
        ->middleware('role:admin,department_head,encoder');

    // Notifications
    Route::prefix('notifications')->name('notifications.')->group(function () {
        Route::get('/', [NotificationController::class, 'index'])->name('index');
        Route::patch('/{notification}/read', [NotificationController::class, 'markAsRead'])->name('mark-read');
        Route::patch('/mark-all-read', [NotificationController::class, 'markAllAsRead'])->name('mark-all-read');
        Route::delete('/{notification}', [NotificationController::class, 'destroy'])->name('destroy');
    });

    // User Management Routes - Admin and Department Heads only
    Route::prefix('users')->name('users.')->middleware('role:admin,department_head')->group(function () {
        Route::get('/', [UserController::class, 'index'])->name('index');
        
        // Admin only - create new users
        Route::middleware('role:admin')->group(function () {
            Route::get('/create', [UserController::class, 'create'])->name('create');
            Route::post('/', [UserController::class, 'store'])->name('store');
        });

        Route::prefix('{user}')->group(function () {
            Route::get('/', [UserController::class, 'show'])->name('show');
            
            // Edit routes with proper authorization
            Route::get('/edit', [UserController::class, 'edit'])
                ->name('edit')
                ->middleware('can:update,user');
            Route::put('/', [UserController::class, 'update'])
                ->name('update')
                ->middleware('can:update,user');
            
            // Status toggle
            Route::patch('/toggle-status', [UserController::class, 'toggleStatus'])
                ->name('toggle-status')
                ->middleware('can:update,user');

            // Admin only - delete users
            Route::middleware('role:admin')->group(function () {
                Route::delete('/', [UserController::class, 'destroy'])
                    ->name('destroy')
                    ->middleware('can:delete,user');
            });
        });
    });

    // Reports - Available to Department Heads and Admin
    Route::middleware('role:admin,department_head')->prefix('reports')->name('reports.')->group(function () {
        Route::get('/', [ReportController::class, 'index'])->name('index');
        Route::get('/document-summary', [ReportController::class, 'documentSummary'])->name('document-summary');
        Route::get('/department-performance', [ReportController::class, 'departmentPerformance'])->name('department-performance');
        Route::get('/monthly-trends', [ReportController::class, 'monthlyTrends'])->name('monthly-trends');
        
        // Export functionality
        Route::post('/export', [ReportController::class, 'export'])->name('export');
    });

    // Analytics - Available to Department Heads and Admin
    Route::middleware('role:admin,department_head')->prefix('analytics')->name('analytics.')->group(function () {
        Route::get('/', [AnalyticsController::class, 'index'])->name('index');
        Route::get('/dashboard-data', [AnalyticsController::class, 'dashboardData'])->name('dashboard-data');
        Route::get('/department-performance', [AnalyticsController::class, 'departmentPerformance'])->name('department-performance');
        Route::get('/user-activity', [AnalyticsController::class, 'userActivity'])->name('user-activity');
        Route::get('/document-flow', [AnalyticsController::class, 'documentFlow'])->name('document-flow');
        
        // Chart data endpoints
        Route::get('/charts/monthly-documents', [AnalyticsController::class, 'monthlyDocumentsChart'])->name('charts.monthly-documents');
        Route::get('/charts/status-distribution', [AnalyticsController::class, 'statusDistributionChart'])->name('charts.status-distribution');
        Route::get('/charts/priority-distribution', [AnalyticsController::class, 'priorityDistributionChart'])->name('charts.priority-distribution');
        
        // Export routes
        Route::post('/export', [AnalyticsController::class, 'exportData'])->name('export');
        Route::post('/export-data', [AnalyticsController::class, 'exportData'])->name('export-data');
        
        // Department specific routes
        Route::get('/department/{department}/details', [AnalyticsController::class, 'departmentDetails'])->name('department-details');
        Route::get('/department/{department}/report', [AnalyticsController::class, 'generateDepartmentReport'])->name('department-report');
    });

    // Administration Routes - Admin Only
    Route::middleware('role:admin')->prefix('admin')->name('admin.')->group(function () {
        
        // System Settings
        Route::prefix('settings')->name('settings.')->group(function () {
            Route::get('/', [SettingsController::class, 'index'])->name('index');
            Route::put('/', [SettingsController::class, 'update'])->name('update');
            Route::get('/create', [SettingsController::class, 'create'])->name('create');
            Route::post('/', [SettingsController::class, 'store'])->name('store');
            Route::delete('/{setting}', [SettingsController::class, 'destroy'])->name('destroy');
            
            // Bulk operations
            Route::post('/load-defaults', [SettingsController::class, 'loadDefaults'])->name('load-defaults');
            Route::post('/reset-all', [SettingsController::class, 'resetAll'])->name('reset-all');
            Route::post('/generate-css', [SettingsController::class, 'generateCss'])->name('generate-css');
            Route::post('/seed', [SettingsController::class, 'seed'])->name('seed');
            
            // Import/Export settings
            Route::post('/export', [SettingsController::class, 'export'])->name('export');
            Route::post('/import', [SettingsController::class, 'import'])->name('import');
        });
        
        // Department Management
        Route::resource('departments', DepartmentController::class);
        Route::patch('/departments/{department}/toggle-status', [DepartmentController::class, 'toggleStatus'])
            ->name('departments.toggle-status');

        // Document Type Management
        Route::resource('document-types', DocumentTypeController::class);
        Route::patch('/document-types/{documentType}/toggle-status', [DocumentTypeController::class, 'toggleStatus'])
            ->name('document-types.toggle-status');

        // System Maintenance
        Route::prefix('maintenance')->name('maintenance.')->group(function () {
            Route::get('/', [SettingsController::class, 'maintenance'])->name('index');
            Route::post('/clear-cache', [SettingsController::class, 'clearCache'])->name('clear-cache');
            Route::post('/clear-logs', [SettingsController::class, 'clearLogs'])->name('clear-logs');
            Route::post('/backup-database', [SettingsController::class, 'backupDatabase'])->name('backup-database');
            Route::post('/optimize', [SettingsController::class, 'optimize'])->name('optimize');
        });

        // Activity Logs
        Route::prefix('activity-logs')->name('activity-logs.')->group(function () {
            Route::get('/', [SettingsController::class, 'activityLogs'])->name('index');
            Route::delete('/clear', [SettingsController::class, 'clearActivityLogs'])->name('clear');
            Route::post('/export', [SettingsController::class, 'exportActivityLogs'])->name('export');
        });
    });

    // QR Code routes
    Route::prefix('qr-codes')->name('qr-codes.')->group(function () {
        Route::get('/', [QrCodeController::class, 'showGenerator'])->name('index');
        Route::get('/document/{document}', [QrCodeController::class, 'showGenerator'])->name('document');
        Route::post('/generate/{document}', [QrCodeController::class, 'generate'])->name('generate');
        Route::get('/show/{document}', [QrCodeController::class, 'show'])->name('show');
        Route::get('/image', [QrCodeController::class, 'serveImage'])->name('show-image');
        Route::get('/download/{document}', [QrCodeController::class, 'download'])->name('download');
        Route::post('/bulk-generate', [QrCodeController::class, 'bulkGenerate'])->name('bulk-generate');
    });

    // Help and Documentation
    Route::prefix('help')->name('help.')->group(function () {
        Route::get('/', function() {
            return view('help.index');
        })->name('index');
        
        Route::get('/user-guide', function() {
            return view('help.user-guide');
        })->name('user-guide');
        
        Route::get('/faq', function() {
            return view('help.faq');
        })->name('faq');
        
        Route::get('/contact', function() {
            return view('help.contact');
        })->name('contact');
    });

    // API Documentation (for developers)
    Route::middleware('role:admin')->group(function () {
        Route::get('/api-docs', function() {
            return view('admin.api-docs');
        })->name('api-docs');
    });
});

// Home route redirect
Route::get('/home', function () {
    \Log::info('Home route accessed, redirecting to dashboard', ['user_id' => auth()->id()]);
    return redirect('/dashboard');
})->middleware('auth')->name('home');

// Health Check Route (for monitoring)
Route::get('/health', function () {
    return response()->json([
        'status' => 'healthy',
        'timestamp' => now()->toISOString(),
        'app' => config('app.name'),
        'environment' => config('app.env'),
        'maintenance' => app()->isDownForMaintenance(),
        'database' => \Illuminate\Support\Facades\DB::connection()->getPdo() ? 'connected' : 'disconnected',
    ]);
})->name('health-check');

// Sitemap for SEO (public)
Route::get('/sitemap.xml', function() {
    return response()->view('sitemap')
        ->header('Content-Type', 'text/xml');
})->name('sitemap');

// Robots.txt
Route::get('/robots.txt', function() {
    return response("User-agent: *\nDisallow: /admin\nDisallow: /api\nDisallow: /dashboard\n", 200)
        ->header('Content-Type', 'text/plain');
})->name('robots');

// Fallback route for 404 errors
Route::fallback(function () {
    \Log::warning('404 - Route not found', [
        'url' => request()->url(),
        'method' => request()->method(),
        'ip' => request()->ip(),
        'user_agent' => request()->userAgent(),
        'user_id' => auth()->id(),
    ]);
    
    if (request()->expectsJson()) {
        return response()->json([
            'error' => 'Route not found',
            'message' => 'The requested resource could not be found.'
        ], 404);
    }
    
    return response()->view('errors.404', [], 404);
});