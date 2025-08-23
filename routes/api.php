<?php

use App\Http\Controllers\DocumentController;
use App\Http\Controllers\DocumentRouteController;
use App\Http\Controllers\DocumentAttachmentController;
use App\Http\Controllers\DocumentCommentController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\DepartmentController;
use App\Http\Controllers\DocumentTypeController;
use App\Http\Controllers\AnalyticsController;
use App\Http\Controllers\SettingsController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
*/

// CHANGE: Use 'web' middleware instead of 'auth:sanctum' for web-based AJAX calls
Route::middleware(['web', 'auth', 'activity.log'])->group(function () {
    
    // User Information
    Route::get('/user', function (Request $request) {
        return $request->user()->load('department');
    });

    // User Management API - FIXED: Remove extra middleware since it's already applied above
    Route::prefix('users')->name('api.users.')->group(function () {
        
        // User Search (AJAX)
        Route::get('/search', function (Request $request) {
            $query = $request->get('q');
            $users = \App\Models\User::where('name', 'like', "%{$query}%")
                                   ->orWhere('email', 'like', "%{$query}%")
                                   ->orWhere('employee_id', 'like', "%{$query}%")
                                   ->limit(10)
                                   ->get(['id', 'name', 'email', 'employee_id']);
            return response()->json($users);
        })->name('search');
        
        // FIXED: User Status Toggle - Check permissions properly
        Route::patch('/{user}/toggle-status', function (\App\Models\User $user, Request $request) {
            // Check if user has permission
            if (!auth()->user()->isAdmin() && !auth()->user()->isDepartmentHead()) {
                return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
            }
            
            // Prevent self-deactivation
            if ($user->id === auth()->id()) {
                return response()->json(['success' => false, 'message' => 'You cannot change your own status'], 422);
            }
            
            try {
                $user->update(['is_active' => !$user->is_active]);
                
                $status = $user->is_active ? 'activated' : 'deactivated';
                
                return response()->json([
                    'success' => true,
                    'message' => "User {$status} successfully.",
                    'is_active' => $user->is_active,
                ]);
            } catch (\Exception $e) {
                return response()->json([
                    'success' => false,
                    'message' => 'Error updating user status: ' . $e->getMessage(),
                ], 500);
            }
        })->name('toggle-status');
        
        // User Statistics
        Route::get('/{user}/stats', function (\App\Models\User $user) {
            return response()->json([
                'documents_created' => $user->createdDocuments()->count(),
                'documents_routed' => $user->routedDocuments()->count(),
                'pending_documents' => $user->createdDocuments()->pending()->count(),
                'completed_documents' => $user->createdDocuments()->completed()->count(),
            ]);
        })->name('stats');
    });

    // Notifications API
    Route::prefix('notifications')->name('api.notifications.')->group(function () {
        Route::get('/unread-count', [NotificationController::class, 'getUnreadCount'])->name('unread-count');
        Route::get('/recent', [NotificationController::class, 'getRecent'])->name('recent');
        Route::patch('/{notification}/read', [NotificationController::class, 'markAsRead'])->name('mark-read');
        Route::patch('/mark-all-read', [NotificationController::class, 'markAllAsRead'])->name('mark-all-read');
        Route::delete('/{notification}', [NotificationController::class, 'destroy'])->name('destroy');
    });

    // Document API Endpoints
    Route::prefix('documents')->name('api.documents.')->group(function () {
        
        // Document Status Updates (AJAX)
        Route::patch('/{document}/status', [DocumentController::class, 'updateStatus'])->name('update-status');
        
        // Document Search and Filtering (AJAX)
        Route::get('/search', function (Request $request) {
            $query = $request->get('q');
            $documents = \App\Models\Document::where('tracking_number', 'like', "%{$query}%")
                                           ->orWhere('title', 'like', "%{$query}%")
                                           ->limit(10)
                                           ->get(['id', 'tracking_number', 'title']);
            return response()->json($documents);
        })->name('search');
        
        // Document Quick Stats (AJAX)
        Route::get('/{document}/stats', function (\App\Models\Document $document) {
            return response()->json([
                'routes_count' => $document->routes()->count(),
                'comments_count' => $document->comments()->count(),
                'attachments_count' => $document->attachments()->active()->count(),
                'total_file_size' => $document->getTotalFileSizeAttribute(),
            ]);
        })->name('stats');
    });

    // Document Routing API
    Route::prefix('document-routes')->name('api.document-routes.')->group(function () {
        
        // Route Actions (AJAX)
        Route::patch('/{route}/receive', [DocumentRouteController::class, 'receive'])->name('receive');
        Route::patch('/{route}/process', [DocumentRouteController::class, 'process'])->name('process');
        
        // Bulk Routing (AJAX)
        Route::post('/bulk-route', [DocumentRouteController::class, 'bulkRoute'])->name('bulk-route');
    });

    // Admin-only API routes
    Route::middleware('role:admin')->group(function () {
        
        // Department Management API
        Route::prefix('admin/departments')->name('api.admin.departments.')->group(function () {
            
            // Department Status Toggle (AJAX)
            Route::patch('/{department}/toggle-status', [DepartmentController::class, 'toggleStatus'])->name('toggle-status');
            
            // Department Users List
            Route::get('/{department}/users', function (\App\Models\Department $department) {
                return response()->json($department->users()->active()->get(['id', 'name', 'email', 'role']));
            })->name('users');
        });

        // Document Types API
        Route::prefix('admin/document-types')->name('api.admin.document-types.')->group(function () {
            
            // Document Type Status Toggle (AJAX)
            Route::patch('/{documentType}/toggle-status', [DocumentTypeController::class, 'toggleStatus'])->name('toggle-status');
        });

        // System Settings API
        Route::prefix('admin/settings')->name('api.admin.settings.')->group(function () {
            
            // Get Settings by Group
            Route::get('/group/{group}', function ($group) {
                $settings = \App\Models\SystemSetting::where('group', $group)->get();
                return response()->json($settings);
            })->name('by-group');
            
            // Update Single Setting
            Route::patch('/{setting}', function (Request $request, \App\Models\SystemSetting $setting) {
                $request->validate(['value' => 'required']);
                $setting->update(['value' => $request->value]);
                return response()->json(['success' => true, 'setting' => $setting]);
            })->name('update-single');
        });
    });

    // Quick Actions API (All authenticated users)
    Route::prefix('quick')->name('api.quick.')->group(function () {
        
        // Quick Search (AJAX)
        Route::get('/search', function (Request $request) {
            $query = $request->get('q');
            $limit = $request->get('limit', 5);
            
            $documents = \App\Models\Document::where('tracking_number', 'like', "%{$query}%")
                                           ->orWhere('title', 'like', "%{$query}%")
                                           ->limit($limit)
                                           ->get(['id', 'tracking_number', 'title', 'status']);
            
            return response()->json(['documents' => $documents]);
        })->name('search');
        
        // System Status Check
        Route::get('/system-status', function () {
            return response()->json([
                'status' => 'operational',
                'maintenance_mode' => false,
                'system_name' => setting('system.name', 'Document Tracking System'),
                'version' => '1.0.0',
            ]);
        })->name('system-status');
    });
});

// Public API Endpoints (no authentication required)
Route::prefix('public')->name('api.public.')->group(function () {
    
    // Public Document Tracking
    Route::get('/track/{trackingNumber}', function ($trackingNumber) {
        $document = \App\Models\Document::where('tracking_number', $trackingNumber)
                                      ->with(['documentType', 'originDepartment', 'currentDepartment'])
                                      ->first();
        
        if (!$document) {
            return response()->json(['error' => 'Document not found'], 404);
        }
        
        // Only return basic information for public tracking
        return response()->json([
            'tracking_number' => $document->tracking_number,
            'title' => $document->title,
            'document_type' => $document->documentType->name,
            'status' => $document->status,
            'current_department' => $document->currentDepartment->name,
            'date_received' => $document->date_received->format('M d, Y'),
            'target_completion_date' => $document->target_completion_date?->format('M d, Y'),
        ]);
    })->name('track');
});

// Health Check Endpoint
Route::get('/health', function () {
    return response()->json([
        'status' => 'healthy',
        'timestamp' => now()->toISOString(),
        'database' => \Illuminate\Support\Facades\DB::connection()->getPdo() ? 'connected' : 'disconnected',
    ]);
})->name('api.health-check');