<?php

namespace App\Http\Controllers;

use App\Models\Document;
use App\Models\Department;
use App\Models\DocumentType;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\DocumentsExport;
use App\Exports\DocumentRoutesExport;
use App\Exports\UsersExport;
use App\Exports\DepartmentPerformanceExport;
use App\Exports\MultiSheetExport;

class AnalyticsController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('can:view-analytics');
    }

    public function index()
    {
        // Overview statistics - ensure all keys exist
        $stats = [
            'total_documents' => Document::count(),
            'pending_documents' => Document::where('status', 'pending')->count(),
            'completed_documents' => Document::where('status', 'completed')->count(),
            'overdue_documents' => Document::where('target_completion_date', '<', now())
                                         ->whereNotIn('status', ['completed', 'cancelled'])
                                         ->count(),
            'total_users' => User::where('is_active', true)->count(),
            'total_departments' => Department::where('is_active', true)->count(),
        ];

        // Document status distribution - ensure all statuses are represented
        $statusData = [
            'pending' => Document::where('status', 'pending')->count(),
            'in_progress' => Document::where('status', 'in_progress')->count(),
            'completed' => Document::where('status', 'completed')->count(),
            'cancelled' => Document::where('status', 'cancelled')->count(),
            'archived' => Document::where('status', 'archived')->count(),
        ];

        // Priority distribution - ensure all priorities are represented
        $priorityData = [
            'low' => Document::where('priority', 'low')->count(),
            'normal' => Document::where('priority', 'normal')->count(),
            'high' => Document::where('priority', 'high')->count(),
            'urgent' => Document::where('priority', 'urgent')->count(),
        ];

        // Monthly document creation trend (last 12 months)
        $monthlyData = $this->getMonthlyDocumentData();

        // Department performance
        $departmentData = $this->getDepartmentPerformanceData();

        // Document type distribution
        $documentTypeData = DocumentType::withCount('documents')
                                       ->orderBy('documents_count', 'desc')
                                       ->limit(10)
                                       ->get();

        return view('analytics.index', compact(
            'stats', 'statusData', 'priorityData', 'monthlyData', 
            'departmentData', 'documentTypeData'
        ));
    }

    public function departmentPerformance()
    {
        $departments = Department::where('is_active', true)->get();
        $performanceData = [];

        foreach ($departments as $department) {
            $totalDocuments = Document::where('current_department_id', $department->id)->count();
            $completedDocuments = Document::where('current_department_id', $department->id)
                                        ->where('status', 'completed')->count();
            $overdueDocuments = Document::where('current_department_id', $department->id)
                                      ->where('target_completion_date', '<', now())
                                      ->whereNotIn('status', ['completed', 'cancelled'])
                                      ->count();

            // Calculate average processing time
            $averageProcessingTime = DB::table('document_routes')
                                     ->where('to_department_id', $department->id)
                                     ->whereNotNull('received_at')
                                     ->whereNotNull('processed_at')
                                     ->selectRaw('AVG(TIMESTAMPDIFF(HOUR, received_at, processed_at)) as avg_time')
                                     ->value('avg_time');

            $performanceData[] = [
                'department' => $department,
                'total_documents' => $totalDocuments,
                'completed_documents' => $completedDocuments,
                'overdue_documents' => $overdueDocuments,
                'completion_rate' => $totalDocuments > 0 ? round(($completedDocuments / $totalDocuments) * 100, 2) : 0,
                'average_processing_time' => round($averageProcessingTime ?: 0, 2),
            ];
        }

        return view('analytics.department-performance', compact('performanceData'));
    }

    public function userActivity()
    {
        $userStats = User::where('is_active', true)
                        ->withCount([
                            'createdDocuments',
                            'routedDocuments',
                            'receivedDocuments'
                        ])
                        ->orderBy('created_documents_count', 'desc')
                        ->limit(20)
                        ->get();

        // Recent activities - check if activity_logs table exists
        $recentActivities = collect();
        if (DB::getSchemaBuilder()->hasTable('activity_logs')) {
            $recentActivities = DB::table('activity_logs')
                               ->join('users', 'activity_logs.user_id', '=', 'users.id')
                               ->select('activity_logs.*', 'users.name as user_name')
                               ->orderBy('activity_logs.created_at', 'desc')
                               ->limit(50)
                               ->get();
        }

        return view('analytics.user-activity', compact('userStats', 'recentActivities'));
    }

    public function documentFlow()
    {
        // Document flow between departments
        $flowData = DB::table('document_routes')
                     ->join('departments as from_dept', 'document_routes.from_department_id', '=', 'from_dept.id')
                     ->join('departments as to_dept', 'document_routes.to_department_id', '=', 'to_dept.id')
                     ->selectRaw('from_dept.name as from_department, to_dept.name as to_department, COUNT(*) as count')
                     ->groupBy('from_dept.id', 'to_dept.id', 'from_dept.name', 'to_dept.name')
                     ->orderBy('count', 'desc')
                     ->get();

        // Processing time analysis
        $processingTimes = DB::table('document_routes')
                            ->join('departments', 'document_routes.to_department_id', '=', 'departments.id')
                            ->whereNotNull('received_at')
                            ->whereNotNull('processed_at')
                            ->selectRaw('
                                departments.name as department,
                                AVG(TIMESTAMPDIFF(HOUR, received_at, processed_at)) as avg_processing_time,
                                MIN(TIMESTAMPDIFF(HOUR, received_at, processed_at)) as min_processing_time,
                                MAX(TIMESTAMPDIFF(HOUR, received_at, processed_at)) as max_processing_time
                            ')
                            ->groupBy('departments.id', 'departments.name')
                            ->get();

        return view('analytics.document-flow', compact('flowData', 'processingTimes'));
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

//    public function exportData(Request $request)
 //   {
//        $request->validate([
//            'type' => 'required|in:documents,routes,users,activities',
//            'format' => 'required|in:csv,excel',
//            'date_from' => 'nullable|date',
//            'date_to' => 'nullable|date|after_or_equal:date_from',
 //       ]);

        // This would integrate with Laravel Excel or similar package
        // For now, return JSON data that can be processed by frontend
        
//        $data = [];
//        $filename = '';

 //       switch ($request->type) {
 //           case 'documents':
 //               $query = Document::with(['documentType', 'originDepartment', 'currentDepartment']);
 //               if ($request->filled('date_from')) {
 //                   $query->whereDate('created_at', '>=', $request->date_from);
 //               }
 //               if ($request->filled('date_to')) {
 //                   $query->whereDate('created_at', '<=', $request->date_to);
 //               }
 //               $data = $query->get();
 //               $filename = 'documents-export-' . date('Y-m-d');
  //              break;
  //              
  //          case 'routes':
  //              $data = DB::table('document_routes')
  //                       ->join('documents', 'document_routes.document_id', '=', 'documents.id')
  //                       ->join('departments as from_dept', 'document_routes.from_department_id', '=', 'from_dept.id')
  //                       ->join('departments as to_dept', 'document_routes.to_department_id', '=', 'to_dept.id')
  //                       ->select('documents.tracking_number', 'from_dept.name as from_department', 
  //                               'to_dept.name as to_department', 'document_routes.status', 'document_routes.routed_at')
  //                       ->get();
  //              $filename = 'routes-export-' . date('Y-m-d');
  //              break;
  //              
  //          case 'users':
  //              $data = User::with('department')->where('is_active', true)->get();
  //              $filename = 'users-export-' . date('Y-m-d');
  //              break;
  //      }
//
  //      return response()->json([
  //          'success' => true,
  //          'data' => $data,
  //          'filename' => $filename,
  //      ]);
  //  }

  public function exportData(Request $request)
    {
        $request->validate([
            'type' => 'required|in:documents,routes,users,departments,multi',
            'format' => 'required|in:xlsx,csv,excel',
            'date_from' => 'nullable|date',
            'date_to' => 'nullable|date|after_or_equal:date_from',
            'department_id' => 'nullable|exists:departments,id',
            'status' => 'nullable|string',
        ]);

        try {
            $filters = $request->only(['date_from', 'date_to', 'department_id', 'status', 'priority', 'document_type_id']);
            $filename = $this->generateFilename($request->type, $request->format);

            switch ($request->type) {
                case 'documents':
                    return Excel::download(new DocumentsExport($filters), $filename);
                    
                case 'routes':
                    return Excel::download(new DocumentRoutesExport($filters), $filename);
                    
                case 'users':
                    return Excel::download(new UsersExport($filters), $filename);
                    
                case 'departments':
                    return Excel::download(new DepartmentPerformanceExport($filters['date_from'] ?? null, $filters['date_to'] ?? null), $filename);
                    
                case 'multi':
                    return Excel::download(new MultiSheetExport($filters), $filename);
                    
                default:
                    return response()->json(['error' => 'Invalid export type'], 400);
            }
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Export failed: ' . $e->getMessage()
            ], 500);
        }
    }

    private function generateFilename($type, $format)
    {
        $timestamp = date('Y-m-d_H-i-s');
        return "madridejos_{$type}_export_{$timestamp}.{$format}";
    }

   public function departmentDetails(Department $department)
    {
        $stats = [
            'total_documents' => Document::where('current_department_id', $department->id)->count(),
            'completed_documents' => Document::where('current_department_id', $department->id)
                ->where('status', 'completed')->count(),
            'pending_documents' => Document::where('current_department_id', $department->id)
                ->where('status', 'pending')->count(),
            'in_progress_documents' => Document::where('current_department_id', $department->id)
                ->where('status', 'in_progress')->count(),
            'overdue_documents' => Document::where('current_department_id', $department->id)
                ->where('target_completion_date', '<', now())
                ->whereNotIn('status', ['completed', 'cancelled'])
                ->count(),
            'avg_processing_time' => DB::table('document_routes')
                ->where('to_department_id', $department->id)
                ->whereNotNull('received_at')
                ->whereNotNull('processed_at')
                ->selectRaw('AVG(TIMESTAMPDIFF(HOUR, received_at, processed_at)) as avg_time')
                ->value('avg_time') ?: 0,
            'recent_documents' => Document::where('current_department_id', $department->id)
                ->with(['documentType', 'creator'])
                ->latest()
                ->limit(10)
                ->get(),
        ];
        
        return view('analytics.department-details', compact('department', 'stats'));
    }

    public function generateDepartmentReport(Department $department)
    {
        $filters = [
            'department_id' => $department->id,
            'date_from' => request('date_from'),
            'date_to' => request('date_to'),
        ];
        
        $filename = "department_{$department->code}_report_" . date('Y-m-d_H-i-s') . ".xlsx";
        
        return Excel::download(new DocumentsExport($filters), $filename);
    }

    // Add alias method for backward compatibility
    public function export(Request $request)
    {
        return $this->exportData($request);
    }
}