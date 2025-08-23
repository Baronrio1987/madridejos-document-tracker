<?php

namespace App\Http\Controllers;

use App\Models\Document;
use App\Models\DocumentRoute;
use App\Models\Department;
use App\Models\RoutingTemplate;
use App\Models\DocumentHistory;
use App\Models\ActivityLog;
use App\Models\Notification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class DocumentRouteController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index(Request $request)
    {
        $user = Auth::user();
        $query = DocumentRoute::with(['document', 'fromDepartment', 'toDepartment', 'routedBy', 'receivedBy']);

        // Filter based on user role
        if (!$user->isAdmin()) {
            $query->where(function($q) use ($user) {
                $q->where('to_department_id', $user->department_id)
                  ->orWhere('from_department_id', $user->department_id);
            });
        }

        // Apply filters
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('department_id')) {
            $query->where(function($q) use ($request) {
                $q->where('to_department_id', $request->department_id)
                  ->orWhere('from_department_id', $request->department_id);
            });
        }

        if ($request->filled('date_from')) {
            $query->whereDate('routed_at', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('routed_at', '<=', $request->date_to);
        }

        $routes = $query->orderBy('routed_at', 'desc')->paginate(15);
        $departments = Department::active()->get();

        return view('routes.index', compact('routes', 'departments'));
    }

    public function create(Document $document)
    {
        $this->authorize('route', $document);
        
        $departments = Department::active()
                                ->where('id', '!=', $document->current_department_id)
                                ->get();
        
        $templates = RoutingTemplate::active()
                                  ->where('document_type_id', $document->document_type_id)
                                  ->get();

        return view('routes.create', compact('document', 'departments', 'templates'));
    }

    public function store(Request $request, Document $document)
    {
        $this->authorize('route', $document);

        $request->validate([
            'to_department_id' => 'required|exists:departments,id|different:from_department_id',
            'routing_purpose' => 'required|string|max:500',
            'instructions' => 'nullable|string|max:1000',
        ]);

        DB::beginTransaction();
        try {
            // Create route
            $route = DocumentRoute::create([
                'document_id' => $document->id,
                'from_department_id' => $document->current_department_id,
                'to_department_id' => $request->to_department_id,
                'routed_by' => Auth::id(),
                'routing_purpose' => $request->routing_purpose,
                'instructions' => $request->instructions,
                'status' => 'pending',
                'routed_at' => now(),
            ]);

            // Update document current department
            $document->update([
                'current_department_id' => $request->to_department_id,
                'status' => 'in_progress',
            ]);

            // Log routing
            DocumentHistory::create([
                'document_id' => $document->id,
                'user_id' => Auth::id(),
                'action' => 'routed',
                'description' => "Document routed to {$route->toDepartment->name}",
                'new_values' => $route->toArray(),
                'ip_address' => request()->ip(),
                'user_agent' => request()->userAgent(),
            ]);

            // Create notification for destination department
            $departmentUsers = $route->toDepartment->users()->active()->get();
            foreach ($departmentUsers as $user) {
                Notification::create([
                    'user_id' => $user->id,
                    'document_id' => $document->id,
                    'title' => 'New Document Received',
                    'message' => "Document {$document->tracking_number} has been routed to your department",
                    'type' => 'info',
                ]);
            }

            ActivityLog::log('document_routed', 
                "Routed document {$document->tracking_number} to {$route->toDepartment->name}", 
                $document
            );

            DB::commit();

            return redirect()->route('documents.show', $document)
                           ->with('success', 'Document routed successfully.');

        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->back()
                           ->withInput()
                           ->with('error', 'Error routing document: ' . $e->getMessage());
        }
    }

    public function receive(DocumentRoute $route)
    {
        $this->authorize('receive', $route);

        DB::beginTransaction();
        try {
            $route->update([
                'status' => 'received',
                'received_by' => Auth::id(),
                'received_at' => now(),
            ]);

            // Log receipt
            DocumentHistory::create([
                'document_id' => $route->document_id,
                'user_id' => Auth::id(),
                'action' => 'received',
                'description' => "Document received by {$route->toDepartment->name}",
                'new_values' => ['received_at' => now()],
                'ip_address' => request()->ip(),
                'user_agent' => request()->userAgent(),
            ]);

            ActivityLog::log('document_received', 
                "Received document {$route->document->tracking_number}", 
                $route->document
            );

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Document received successfully.',
            ]);

        } catch (\Exception $e) {
            DB::rollback();
            return response()->json([
                'success' => false,
                'message' => 'Error receiving document: ' . $e->getMessage(),
            ], 500);
        }
    }

    public function process(Request $request, DocumentRoute $route)
    {
        $this->authorize('process', $route);

        $request->validate([
            'remarks' => 'nullable|string|max:1000',
        ]);

        DB::beginTransaction();
        try {
            $route->update([
                'status' => 'processed',
                'processed_at' => now(),
                'remarks' => $request->remarks,
            ]);

            // Log processing
            DocumentHistory::create([
                'document_id' => $route->document_id,
                'user_id' => Auth::id(),
                'action' => 'processed',
                'description' => "Document processed by {$route->toDepartment->name}",
                'new_values' => ['processed_at' => now(), 'remarks' => $request->remarks],
                'ip_address' => request()->ip(),
                'user_agent' => request()->userAgent(),
            ]);

            ActivityLog::log('document_processed', 
                "Processed document {$route->document->tracking_number}", 
                $route->document
            );

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Document processed successfully.',
            ]);

        } catch (\Exception $e) {
            DB::rollback();
            return response()->json([
                'success' => false,
                'message' => 'Error processing document: ' . $e->getMessage(),
            ], 500);
        }
    }

    public function getRoutingTemplate(RoutingTemplate $template)
    {
        $template->load('documentType');
        $departments = Department::whereIn('id', $template->route_sequence)->get();
        
        return response()->json([
            'template' => $template,
            'departments' => $departments,
        ]);
    }

    public function bulkRoute(Request $request)
    {
        $this->authorize('bulkRoute', DocumentRoute::class);

        $request->validate([
            'document_ids' => 'required|array',
            'document_ids.*' => 'exists:documents,id',
            'to_department_id' => 'required|exists:departments,id',
            'routing_purpose' => 'required|string|max:500',
            'instructions' => 'nullable|string|max:1000',
        ]);

        DB::beginTransaction();
        try {
            $successCount = 0;
            $documents = Document::whereIn('id', $request->document_ids)->get();

            foreach ($documents as $document) {
                if ($document->current_department_id == $request->to_department_id) {
                    continue; // Skip if already in target department
                }

                // Create route
                DocumentRoute::create([
                    'document_id' => $document->id,
                    'from_department_id' => $document->current_department_id,
                    'to_department_id' => $request->to_department_id,
                    'routed_by' => Auth::id(),
                    'routing_purpose' => $request->routing_purpose,
                    'instructions' => $request->instructions,
                    'status' => 'pending',
                    'routed_at' => now(),
                ]);

                // Update document
                $document->update([
                    'current_department_id' => $request->to_department_id,
                    'status' => 'in_progress',
                ]);

                // Log routing
                DocumentHistory::create([
                    'document_id' => $document->id,
                    'user_id' => Auth::id(),
                    'action' => 'bulk_routed',
                    'description' => "Document bulk routed to department ID {$request->to_department_id}",
                    'ip_address' => request()->ip(),
                    'user_agent' => request()->userAgent(),
                ]);

                $successCount++;
            }

            ActivityLog::log('bulk_route', "Bulk routed {$successCount} documents");

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => "Successfully routed {$successCount} documents.",
            ]);

        } catch (\Exception $e) {
            DB::rollback();
            return response()->json([
                'success' => false,
                'message' => 'Error in bulk routing: ' . $e->getMessage(),
            ], 500);
        }
    }
}
