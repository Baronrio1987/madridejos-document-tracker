<?php
namespace App\Http\Controllers;

use App\Models\DocumentType;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class DocumentTypeController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('can:manage-departments'); // Using same permission as departments
    }

    public function index(Request $request)
    {
        $query = DocumentType::withCount(['documents', 'routingTemplates']);

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('code', 'like', "%{$search}%");
            });
        }

        if ($request->filled('status')) {
            $isActive = $request->status === 'active';
            $query->where('is_active', $isActive);
        }

        $documentTypes = $query->orderBy('name')->paginate(15);

        return view('admin.document-types.index', compact('documentTypes'));
    }

    public function show(DocumentType $documentType)
    {
        $documentType->load('routingTemplates');

        $stats = [
            'total_documents' => $documentType->documents()->count(),
            'pending_documents' => $documentType->documents()->pending()->count(),
            'completed_documents' => $documentType->documents()->completed()->count(),
            'routing_templates' => $documentType->routingTemplates()->active()->count(),
        ];

        return view('admin.document-types.show', compact('documentType', 'stats'));
    }

    public function create()
    {
        return view('admin.document-types.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'required|string|max:20|unique:document_types,code',
            'description' => 'nullable|string|max:1000',
            'retention_period' => 'required|integer|min:1',
            'is_active' => 'boolean',
        ]);

        try {
            $documentType = DocumentType::create([
                'name' => $request->name,
                'code' => strtoupper($request->code),
                'description' => $request->description,
                'retention_period' => $request->retention_period,
                'is_active' => $request->boolean('is_active', true),
            ]);

            return redirect()->route('admin.document-types.show', $documentType)
                           ->with('success', 'Document type created successfully.');

        } catch (\Exception $e) {
            return redirect()->back()
                           ->withInput()
                           ->with('error', 'Error creating document type: ' . $e->getMessage());
        }
    }

    public function edit(DocumentType $documentType)
    {
        return view('admin.document-types.edit', compact('documentType'));
    }

    public function update(Request $request, DocumentType $documentType)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'code' => [
                'required',
                'string',
                'max:20',
                Rule::unique('document_types')->ignore($documentType->id)
            ],
            'description' => 'nullable|string|max:1000',
            'retention_period' => 'required|integer|min:1',
            'is_active' => 'boolean',
        ]);

        try {
            $documentType->update([
                'name' => $request->name,
                'code' => strtoupper($request->code),
                'description' => $request->description,
                'retention_period' => $request->retention_period,
                'is_active' => $request->boolean('is_active'),
            ]);

            return redirect()->route('admin.document-types.show', $documentType)
                           ->with('success', 'Document type updated successfully.');

        } catch (\Exception $e) {
            return redirect()->back()
                           ->withInput()
                           ->with('error', 'Error updating document type: ' . $e->getMessage());
        }
    }

    public function destroy(DocumentType $documentType)
    {
        try {
            // Check if document type has associated documents
            if ($documentType->documents()->exists()) {
                return redirect()->back()
                               ->with('error', 'Cannot delete document type with associated documents.');
            }

            $documentType->delete();

            return redirect()->route('admin.document-types.index')
                           ->with('success', 'Document type deleted successfully.');

        } catch (\Exception $e) {
            return redirect()->back()
                           ->with('error', 'Error deleting document type: ' . $e->getMessage());
        }
    }

    public function toggleStatus(DocumentType $documentType)
    {
        try {
            $documentType->update(['is_active' => !$documentType->is_active]);
            
            $status = $documentType->is_active ? 'activated' : 'deactivated';
            
            return response()->json([
                'success' => true,
                'message' => "Document type {$status} successfully.",
                'is_active' => $documentType->is_active,
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error updating document type status: ' . $e->getMessage(),
            ], 500);
        }
    }
}