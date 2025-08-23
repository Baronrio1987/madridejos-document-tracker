<?php
// app/Http/Controllers/ReportController.php - FIXED VERSION

namespace App\Http\Controllers;

use App\Models\Document;
use App\Models\Department;
use App\Models\DocumentType;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class ReportController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('can:view-reports');
    }

    public function index()
    {
        return view('reports.index');
    }

    public function documentSummary(Request $request)
    {
        $request->validate([
            'date_from' => 'nullable|date',
            'date_to' => 'nullable|date|after_or_equal:date_from',
            'department_id' => 'nullable|exists:departments,id',
            'document_type_id' => 'nullable|exists:document_types,id',
            'status' => 'nullable|in:pending,in_progress,completed,cancelled,archived',
            'format' => 'in:html,pdf,excel',
        ]);

        $query = Document::with(['documentType', 'originDepartment', 'currentDepartment']);

        // Apply filters
        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        if ($request->filled('department_id')) {
            $query->where(function($q) use ($request) {
                $q->where('origin_department_id', $request->department_id)
                  ->orWhere('current_department_id', $request->department_id);
            });
        }

        if ($request->filled('document_type_id')) {
            $query->where('document_type_id', $request->document_type_id);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $documents = $query->orderBy('created_at', 'desc')->get();

        // Generate statistics
        $stats = [
            'total' => $documents->count(),
            'pending' => $documents->where('status', 'pending')->count(),
            'in_progress' => $documents->where('status', 'in_progress')->count(),
            'completed' => $documents->where('status', 'completed')->count(),
            'overdue' => $documents->filter(function($doc) {
                return $doc->isOverdue();
            })->count(),
        ];

        $departments = Department::active()->get();
        $documentTypes = DocumentType::active()->get();

        $format = $request->get('format', 'html');

        if ($format === 'pdf') {
            $pdf = Pdf::loadView('reports.document-summary-pdf', compact('documents', 'stats', 'request'));
            return $pdf->download('document-summary-' . date('Y-m-d') . '.pdf');
        }

        return view('reports.document-summary', compact('documents', 'stats', 'departments', 'documentTypes', 'request'));
    }

    public function departmentPerformance(Request $request)
    {
        $request->validate([
            'date_from' => 'nullable|date',
            'date_to' => 'nullable|date|after_or_equal:date_from',
            'format' => 'in:html,pdf',
        ]);

        $departments = Department::active()->get();
        $performanceData = [];

        foreach ($departments as $department) {
            $query = Document::where('current_department_id', $department->id);

            if ($request->filled('date_from')) {
                $query->whereDate('created_at', '>=', $request->date_from);
            }

            if ($request->filled('date_to')) {
                $query->whereDate('created_at', '<=', $request->date_to);
            }

            $total = $query->count();
            $pending = $query->where('status', 'pending')->count();
            $completed = $query->where('status', 'completed')->count();
            $overdue = $query->where('target_completion_date', '<', now())
                           ->whereNotIn('status', ['completed', 'cancelled'])
                           ->count();

            // Calculate average processing time
            $avgProcessingTime = DB::table('document_routes')
                                 ->where('to_department_id', $department->id)
                                 ->whereNotNull('received_at')
                                 ->whereNotNull('processed_at')
                                 ->selectRaw('AVG(TIMESTAMPDIFF(HOUR, received_at, processed_at)) as avg_time')
                                 ->value('avg_time');

            $performanceData[] = [
                'department' => $department,
                'total_documents' => $total,
                'pending_documents' => $pending,
                'completed_documents' => $completed,
                'overdue_documents' => $overdue,
                'completion_rate' => $total > 0 ? round(($completed / $total) * 100, 2) : 0,
                'avg_processing_time' => round($avgProcessingTime ?: 0, 2),
            ];
        }

        // Overall statistics
        $overallStats = [
            'total_documents' => collect($performanceData)->sum('total_documents'),
            'total_pending' => collect($performanceData)->sum('pending_documents'),
            'total_completed' => collect($performanceData)->sum('completed_documents'),
            'total_overdue' => collect($performanceData)->sum('overdue_documents'),
            'overall_completion_rate' => collect($performanceData)->avg('completion_rate'),
            'total_departments' => $departments->count(),
        ];

        if ($request->get('format') === 'pdf') {
            $pdf = Pdf::loadView('reports.department-performance-pdf', compact('performanceData', 'overallStats', 'request'));
            return $pdf->download('department-performance-' . date('Y-m-d') . '.pdf');
        }

        return view('reports.department-performance', compact('performanceData', 'overallStats', 'request'));
    }

    public function monthlyTrends(Request $request)
    {
        $request->validate([
            'months' => 'nullable|integer|min:1|max:24',
            'format' => 'in:html,pdf',
        ]);

        $monthsBack = $request->get('months', 12);
        $data = [];
        
        for ($i = $monthsBack - 1; $i >= 0; $i--) {
            $month = Carbon::now()->subMonths($i);
            
            $created = Document::whereYear('created_at', $month->year)
                             ->whereMonth('created_at', $month->month)
                             ->count();
                             
            $completed = Document::whereYear('actual_completion_date', $month->year)
                               ->whereMonth('actual_completion_date', $month->month)
                               ->count();
                               
            $data[] = [
                'month' => $month->format('M Y'),
                'month_name' => $month->format('F Y'),
                'created' => $created,
                'completed' => $completed,
                'completion_rate' => $created > 0 ? round(($completed / $created) * 100, 2) : 0,
            ];
        }

        // Calculate trends
        $trends = [
            'total_created' => collect($data)->sum('created'),
            'total_completed' => collect($data)->sum('completed'),
            'avg_monthly_created' => collect($data)->avg('created'),
            'avg_monthly_completed' => collect($data)->avg('completed'),
            'avg_completion_rate' => collect($data)->avg('completion_rate'),
        ];

        if ($request->get('format') === 'pdf') {
            $pdf = Pdf::loadView('reports.monthly-trends-pdf', compact('data', 'trends'));
            return $pdf->download('monthly-trends-' . date('Y-m-d') . '.pdf');
        }

        return view('reports.monthly-trends', compact('data', 'trends'));
    }

    public function userActivity(Request $request)
    {
        $request->validate([
            'date_from' => 'nullable|date',
            'date_to' => 'nullable|date|after_or_equal:date_from',
            'department_id' => 'nullable|exists:departments,id',
            'format' => 'in:html,pdf',
        ]);

        $query = User::active()->with('department');

        if ($request->filled('department_id')) {
            $query->where('department_id', $request->department_id);
        }

        $users = $query->get();

        $userStats = [];
        foreach ($users as $user) {
            $documentsQuery = $user->createdDocuments();
            $routesQuery = $user->routedDocuments();

            if ($request->filled('date_from')) {
                $documentsQuery->whereDate('created_at', '>=', $request->date_from);
                $routesQuery->whereDate('created_at', '>=', $request->date_from);
            }

            if ($request->filled('date_to')) {
                $documentsQuery->whereDate('created_at', '<=', $request->date_to);
                $routesQuery->whereDate('created_at', '<=', $request->date_to);
            }

            $userStats[] = [
                'user' => $user,
                'documents_created' => $documentsQuery->count(),
                'documents_routed' => $routesQuery->count(),
                'pending_documents' => $user->createdDocuments()->pending()->count(),
                'completed_documents' => $user->createdDocuments()->completed()->count(),
            ];
        }

        // Sort by activity
        $userStats = collect($userStats)->sortByDesc(function ($stat) {
            return $stat['documents_created'] + $stat['documents_routed'];
        });

        $departments = Department::active()->get();

        if ($request->get('format') === 'pdf') {
            $pdf = Pdf::loadView('reports.user-activity-pdf', compact('userStats', 'request'));
            return $pdf->download('user-activity-' . date('Y-m-d') . '.pdf');
        }

        return view('reports.user-activity', compact('userStats', 'departments', 'request'));
    }

    public function customReport(Request $request)
    {
        $request->validate([
            'report_type' => 'required|in:overdue,urgent,completed_today,pending_routes',
            'format' => 'in:html,pdf',
        ]);

        $reportType = $request->report_type;
        $documents = collect();
        $title = '';
        $description = '';

        switch ($reportType) {
            case 'overdue':
                $documents = Document::with(['documentType', 'originDepartment', 'currentDepartment'])
                                   ->where('target_completion_date', '<', now())
                                   ->whereNotIn('status', ['completed', 'cancelled'])
                                   ->orderBy('target_completion_date')
                                   ->get();
                $title = 'Overdue Documents Report';
                $description = 'Documents that have passed their target completion date';
                break;

            case 'urgent':
                $documents = Document::with(['documentType', 'originDepartment', 'currentDepartment'])
                                   ->whereIn('priority', ['high', 'urgent'])
                                   ->whereNotIn('status', ['completed', 'cancelled'])
                                   ->orderBy('priority')
                                   ->orderBy('created_at')
                                   ->get();
                $title = 'Urgent & High Priority Documents';
                $description = 'Documents requiring immediate attention';
                break;

            case 'completed_today':
                $documents = Document::with(['documentType', 'originDepartment', 'currentDepartment'])
                                   ->whereDate('actual_completion_date', today())
                                   ->orderBy('actual_completion_date', 'desc')
                                   ->get();
                $title = 'Documents Completed Today';
                $description = 'Documents completed on ' . today()->format('F d, Y');
                break;

            case 'pending_routes':
                $routes = DB::table('document_routes')
                           ->join('documents', 'document_routes.document_id', '=', 'documents.id')
                           ->join('departments as from_dept', 'document_routes.from_department_id', '=', 'from_dept.id')
                           ->join('departments as to_dept', 'document_routes.to_department_id', '=', 'to_dept.id')
                           ->join('document_types', 'documents.document_type_id', '=', 'document_types.id')
                           ->where('document_routes.status', 'pending')
                           ->select(
                               'documents.*',
                               'document_types.name as document_type_name',
                               'from_dept.name as from_department_name',
                               'to_dept.name as to_department_name',
                               'document_routes.routed_at',
                               'document_routes.routing_purpose'
                           )
                           ->orderBy('document_routes.routed_at')
                           ->get();

                $title = 'Pending Document Routes';
                $description = 'Documents waiting to be received by destination departments';
                
                if ($request->get('format') === 'pdf') {
                    $pdf = Pdf::loadView('reports.pending-routes-pdf', compact('routes', 'title', 'description'));
                    return $pdf->download('pending-routes-' . date('Y-m-d') . '.pdf');
                }

                return view('reports.pending-routes', compact('routes', 'title', 'description'));
        }

        $stats = [
            'total' => $documents->count(),
            'pending' => $documents->where('status', 'pending')->count(),
            'in_progress' => $documents->where('status', 'in_progress')->count(),
            'completed' => $documents->where('status', 'completed')->count(),
        ];

        if ($request->get('format') === 'pdf') {
            $pdf = Pdf::loadView('reports.custom-report-pdf', compact('documents', 'stats', 'title', 'description'));
            return $pdf->download(Str::slug($title) . '-' . date('Y-m-d') . '.pdf');
        }

        return view('reports.custom-report', compact('documents', 'stats', 'title', 'description'));
    }
}