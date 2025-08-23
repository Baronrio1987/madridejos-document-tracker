<?php

namespace App\Http\Controllers;

use App\Models\Document;
use App\Models\DocumentType;
use App\Models\Department;
use App\Models\DocumentHistory;
use App\Models\ActivityLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Http\Requests\DocumentRequest;


class DocumentController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('can:view,document')->only(['show']);
        $this->middleware('can:update,document')->only(['edit', 'update']);
        $this->middleware('can:delete,document')->only(['destroy']);
    }

    public function index(Request $request)
    {
        $query = Document::with(['documentType', 'originDepartment', 'currentDepartment', 'creator']);

        $user = Auth::user();
        if (!$user->isAdmin()) {
            if ($user->role === 'department_head') {
                $query->where(function ($q) use ($user) {
                    $q->where('current_department_id', $user->department_id)
                        ->orWhere('origin_department_id', $user->department_id);
                });
            } else {
                $query->where('created_by', $user->id);
            }
        }

        // Apply search filters
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('tracking_number', 'like', "%{$search}%")
                    ->orWhere('title', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%");
            });
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('priority')) {
            $query->where('priority', $request->priority);
        }

        if ($request->filled('document_type_id')) {
            $query->where('document_type_id', $request->document_type_id);
        }

        if ($request->filled('department_id')) {
            $query->where('current_department_id', $request->department_id);
        }

        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        // Sort by created date desc by default
        $documents = $query->orderBy('created_at', 'desc')->paginate(15);

        // Get filter options
        $documentTypes = DocumentType::active()->get();
        $departments = Department::active()->get();

        return view('documents.index', compact('documents', 'documentTypes', 'departments'));
    }

    public function show(Document $document)
    {
        $document->load([
            'documentType', 
            'originDepartment', 
            'currentDepartment', 
            'creator',
            'routes.fromDepartment', 
            'routes.toDepartment', 
            'routes.routedBy', 
            'routes.receivedBy',
            'histories.user', 
            'attachments.uploadedBy',
            'comments.user', 
            'approvals.approver'
        ]);

        // This check is redundant since we're already loading attachments above
        // if (!$document->relationLoaded('attachments')) {
        //     $document->load('attachments');
        // }

        return view('documents.show', compact('document'));
    }

    public function create()
    {
        $this->authorize('create', Document::class);

        $documentTypes = DocumentType::active()->get();
        $departments = Department::active()->get();

        return view('documents.create', compact('documentTypes', 'departments'));
    }

    public function store(DocumentRequest $request)
    {
        $this->authorize('create', Document::class);

        DB::beginTransaction();
        try {
            $document = Document::create([
                'tracking_number' => Document::generateTrackingNumber(),
                'title' => $request->title,
                'description' => $request->description,
                'document_type_id' => $request->document_type_id,
                'origin_department_id' => $request->origin_department_id,
                'current_department_id' => $request->origin_department_id,
                'created_by' => Auth::id(),
                'priority' => $request->priority,
                'date_received' => $request->date_received,
                'target_completion_date' => $request->target_completion_date,
                'is_confidential' => $request->boolean('is_confidential'),
                'remarks' => $request->remarks,
            ]);

            // Log creation
            DocumentHistory::create([
                'document_id' => $document->id,
                'user_id' => Auth::id(),
                'action' => 'created',
                'description' => 'Document created',
                'new_values' => $document->toArray(),
                'ip_address' => request()->ip(),
                'user_agent' => request()->userAgent(),
            ]);

            ActivityLog::log('document_created', "Created document: {$document->tracking_number}", $document);

            DB::commit();

            return redirect()->route('documents.show', $document)
                ->with('success', 'Document created successfully.');
        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->back()
                ->withInput()
                ->with('error', 'Error creating document: ' . $e->getMessage());
        }
    }

    public function edit(Document $document)
    {
        $this->authorize('update', $document);
        $document->load([
            'documentType', 
            'originDepartment', 
            'currentDepartment', 
            'creator.department',
            'attachments' => function($query) {
                $query->where('is_active', true)->orderBy('created_at', 'desc');
            },
            'routes'
        ]);

        $documentTypes = DocumentType::active()->get();
        $departments = Department::active()->get();

        return view('documents.edit', compact('document', 'documentTypes', 'departments'));
    }

    public function update(DocumentRequest $request, Document $document)
    {
        $this->authorize('update', $document);

        DB::beginTransaction();
        try {
            $oldValues = $document->toArray();

            $document->update([
                'title' => $request->title,
                'description' => $request->description,
                'document_type_id' => $request->document_type_id,
                'priority' => $request->priority,
                'target_completion_date' => $request->target_completion_date,
                'is_confidential' => $request->boolean('is_confidential'),
                'remarks' => $request->remarks,
            ]);

            // Log update
            DocumentHistory::create([
                'document_id' => $document->id,
                'user_id' => Auth::id(),
                'action' => 'updated',
                'description' => 'Document updated',
                'old_values' => $oldValues,
                'new_values' => $document->toArray(),
                'ip_address' => request()->ip(),
                'user_agent' => request()->userAgent(),
            ]);

            ActivityLog::log('document_updated', "Updated document: {$document->tracking_number}", $document);

            DB::commit();

            return redirect()->route('documents.show', $document)
                ->with('success', 'Document updated successfully.');
        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->back()
                ->withInput()
                ->with('error', 'Error updating document: ' . $e->getMessage());
        }
    }

    public function destroy(Document $document)
    {
        $this->authorize('delete', $document);

        DB::beginTransaction();
        try {
            $trackingNumber = $document->tracking_number;

            ActivityLog::log('document_deleted', "Deleted document: {$trackingNumber}", $document);

            $document->delete();

            DB::commit();

            return redirect()->route('documents.index')
                ->with('success', 'Document deleted successfully.');
        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->back()
                ->with('error', 'Error deleting document: ' . $e->getMessage());
        }
    }

    public function updateStatus(Request $request, Document $document)
    {
        try {
            // Authorization check
            $this->authorize('update', $document);

            // Validate the request
            $validated = $request->validate([
                'status' => 'required|string|in:pending,in_progress,completed,cancelled,archived',
                'remarks' => 'nullable|string|max:1000',
            ]);

            Log::info('Status update request received', [
                'document_id' => $document->id,
                'user_id' => Auth::id(),
                'old_status' => $document->status,
                'new_status' => $validated['status'],
                'request_data' => $request->all()
            ]);

            DB::beginTransaction();

            $oldStatus = $document->status;
            $newStatus = $validated['status'];

            // Prevent unnecessary updates
            if ($oldStatus === $newStatus) {
                return response()->json([
                    'success' => false,
                    'message' => 'Document already has this status.',
                ], 400);
            }

            // Check if document can be updated
            if (!$document->canUpdateStatus()) {
                return response()->json([
                    'success' => false,
                    'message' => 'This document cannot be updated from its current status.',
                ], 403);
            }

            // Update the document
            $updateData = [
                'status' => $newStatus,
            ];

            // Set completion date if status is completed
            if ($newStatus === 'completed') {
                $updateData['actual_completion_date'] = now();
            }

            $document->update($updateData);

            // Create history record
            DocumentHistory::create([
                'document_id' => $document->id,
                'user_id' => Auth::id(),
                'action' => 'status_changed',
                'description' => "Status changed from {$oldStatus} to {$newStatus}",
                'old_values' => json_encode(['status' => $oldStatus]),
                'new_values' => json_encode(['status' => $newStatus]),
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent(),
            ]);

            // Add remarks as comment if provided
            if (!empty($validated['remarks'])) {
                $document->comments()->create([
                    'user_id' => Auth::id(),
                    'comment' => $validated['remarks'],
                    'type' => 'general',
                    'is_internal' => true,
                ]);
            }

            // Log activity
            ActivityLog::log(
                'document_status_changed',
                "Changed status of document {$document->tracking_number} from {$oldStatus} to {$newStatus}",
                $document
            );

            DB::commit();

            Log::info('Status update successful', [
                'document_id' => $document->id,
                'old_status' => $oldStatus,
                'new_status' => $newStatus
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Document status updated successfully.',
                'data' => [
                    'old_status' => $oldStatus,
                    'new_status' => $newStatus,
                    'status_color' => $document->getStatusColorAttribute(),
                ]
            ]);

        } catch (\Illuminate\Validation\ValidationException $e) {
            DB::rollback();
            Log::warning('Status update validation failed', [
                'document_id' => $document->id,
                'errors' => $e->errors()
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Validation failed.',
                'errors' => $e->errors(),
            ], 422);

        } catch (\Illuminate\Auth\Access\AuthorizationException $e) {
            DB::rollback();
            Log::warning('Status update authorization failed', [
                'document_id' => $document->id,
                'user_id' => Auth::id()
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'You are not authorized to update this document.',
            ], 403);

        } catch (\Exception $e) {
            DB::rollback();
            
            Log::error('Status update error', [
                'document_id' => $document->id,
                'user_id' => Auth::id(),
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'An error occurred while updating the status. Please try again.',
                'error' => app()->environment('local') ? $e->getMessage() : 'Internal server error'
            ], 500);
        }
    }

    public function track($trackingNumber = null)
    {
        $trackingNumber = $trackingNumber ?: request('trackingNumber');
        
        $document = null;
        
        if ($trackingNumber) {
            $document = Document::where('tracking_number', $trackingNumber)
                            ->with([
                                'documentType', 
                                'originDepartment', 
                                'currentDepartment',
                                'routes.fromDepartment', 
                                'routes.toDepartment', 
                                'routes.routedBy', 
                                'routes.receivedBy'
                            ])
                            ->first();
        }
        
        return view('documents.track', compact('document', 'trackingNumber'));
    }

    public function generateQrCode(Document $document, Request $request)
    {
        $this->authorize('view', $document);
        
        try {
            $qrCodeService = app(QrCodeService::class);
            $options = [
                'size' => $request->get('size', 200),
                'label' => $request->get('label', $document->tracking_number),
            ];
            
            $result = $qrCodeService->generateAndSaveDocumentQr($document, $options);
            
            return response()->json([
                'success' => true,
                'qr_url' => $result['url'],
                'tracking_url' => route('public.track.show', $document->tracking_number),
                'message' => 'QR Code generated successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error generating QR Code: ' . $e->getMessage()
            ], 500);
        }
    }
}