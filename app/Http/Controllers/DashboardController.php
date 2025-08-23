<?php

namespace App\Http\Controllers;

use App\Models\Document;
use App\Models\Department;
use App\Models\DocumentRoute;
use App\Models\User;
use App\Models\DocumentType;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        
        // Basic statistics - ensure all keys exist
        $stats = [
            'total_documents' => Document::count(),
            'pending_documents' => Document::where('status', 'pending')->count(),
            'in_progress_documents' => Document::where('status', 'in_progress')->count(),
            'completed_documents' => Document::where('status', 'completed')->count(),
            'overdue_documents' => Document::where('target_completion_date', '<', now())
                                         ->whereNotIn('status', ['completed', 'cancelled'])
                                         ->count(),
            'total_users' => User::where('is_active', true)->count(),
            'total_departments' => Department::where('is_active', true)->count(),
        ];

        // User-specific statistics based on role
        if ($user->role === 'admin') {
            $userStats = $this->getAdminStats();
        } elseif ($user->role === 'department_head') {
            $userStats = $this->getDepartmentHeadStats($user);
        } else {
            $userStats = $this->getUserStats($user);
        }

        // Recent activities
        $recentDocuments = Document::with(['documentType', 'originDepartment', 'currentDepartment', 'creator'])
                                 ->latest()
                                 ->limit(10)
                                 ->get();

        // Pending routes for current user's department
        $pendingRoutes = collect(); // Initialize as empty collection
        if ($user->department_id) {
            $pendingRoutes = DocumentRoute::with(['document', 'fromDepartment'])
                                        ->where('to_department_id', $user->department_id)
                                        ->where('status', 'pending')
                                        ->latest()
                                        ->limit(5)
                                        ->get();
        }

        // Monthly document creation chart data
        $monthlyData = $this->getMonthlyDocumentData();

        // Priority distribution - ensure all priorities are represented
        $priorityData = [
            'low' => Document::where('priority', 'low')->count(),
            'normal' => Document::where('priority', 'normal')->count(),
            'high' => Document::where('priority', 'high')->count(),
            'urgent' => Document::where('priority', 'urgent')->count(),
        ];

        // Status distribution for analytics
        $statusData = [
            'pending' => $stats['pending_documents'],
            'in_progress' => $stats['in_progress_documents'],
            'completed' => $stats['completed_documents'],
            'cancelled' => Document::where('status', 'cancelled')->count(),
        ];

        // Department performance data
        $departmentData = $this->getDepartmentPerformanceData();

        // Document type data
        $documentTypeData = DocumentType::withCount('documents')
                                       ->orderBy('documents_count', 'desc')
                                       ->limit(10)
                                       ->get();

        return view('dashboard.index', compact(
            'stats', 
            'userStats', 
            'recentDocuments', 
            'pendingRoutes', 
            'monthlyData', 
            'priorityData',
            'statusData',
            'departmentData',
            'documentTypeData'
        ));
    }

    private function getAdminStats()
    {
        return [
            'total_users' => User::where('is_active', true)->count(),
            'total_departments' => Department::where('is_active', true)->count(),
            'total_document_types' => DocumentType::where('is_active', true)->count(),
            'pending_approvals' => 0, // Will be implemented when approval system is added
        ];
    }

    private function getDepartmentHeadStats($user)
    {
        $departmentId = $user->department_id;
        
        return [
            'department_documents' => $departmentId ? Document::where('current_department_id', $departmentId)->count() : 0,
            'department_pending' => $departmentId ? Document::where('current_department_id', $departmentId)
                                      ->where('status', 'pending')->count() : 0,
            'department_overdue' => $departmentId ? Document::where('current_department_id', $departmentId)
                                      ->where('target_completion_date', '<', now())
                                      ->whereNotIn('status', ['completed', 'cancelled'])
                                      ->count() : 0,
            'pending_approvals' => 0, // Will be implemented when approval system is added
        ];
    }

    private function getUserStats($user)
    {
        return [
            'my_documents' => Document::where('created_by', $user->id)->count(),
            'my_pending' => Document::where('created_by', $user->id)->where('status', 'pending')->count(),
            'my_completed' => Document::where('created_by', $user->id)->where('status', 'completed')->count(),
            'notifications' => $user->notifications()->where('is_read', false)->count(),
        ];
    }

    private function getMonthlyDocumentData()
    {
        $months = [];
        $data = [];
        
        for ($i = 11; $i >= 0; $i--) {
            $month = Carbon::now()->subMonths($i);
            $months[] = $month->format('M Y');
            $data[] = Document::whereYear('created_at', $month->year)
                            ->whereMonth('created_at', $month->month)
                            ->count();
        }
        
        return [
            'labels' => $months,
            'data' => $data
        ];
    }

    private function getDepartmentPerformanceData()
    {
        return Department::where('is_active', true)
                        ->withCount([
                            'currentDocuments',
                            'currentDocuments as pending_documents_count' => function ($query) {
                                $query->where('status', 'pending');
                            },
                            'currentDocuments as completed_documents_count' => function ($query) {
                                $query->where('status', 'completed');
                            }
                        ])
                        ->get()
                        ->map(function ($department) {
                            $completionRate = $department->current_documents_count > 0 
                                ? round(($department->completed_documents_count / $department->current_documents_count) * 100, 2)
                                : 0;
                            
                            return [
                                'name' => $department->name,
                                'total' => $department->current_documents_count,
                                'pending' => $department->pending_documents_count,
                                'completed' => $department->completed_documents_count,
                                'completion_rate' => $completionRate,
                            ];
                        });
    }
}