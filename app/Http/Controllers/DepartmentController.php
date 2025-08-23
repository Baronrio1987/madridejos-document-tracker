<?php

namespace App\Http\Controllers;

use App\Models\Department;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class DepartmentController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('can:manage-departments');
    }

    public function index(Request $request)
    {
        $query = Department::withCount(['users', 'originDocuments', 'currentDocuments']);

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('code', 'like', "%{$search}%")
                  ->orWhere('head_name', 'like', "%{$search}%");
            });
        }

        if ($request->filled('status')) {
            $isActive = $request->status === 'active';
            $query->where('is_active', $isActive);
        }

        $departments = $query->orderBy('name')->paginate(15);

        return view('admin.departments.index', compact('departments'));
    }

    public function show(Department $department)
    {
        $department->load(['users' => function($query) {
            $query->active()->orderBy('name');
        }]);

        $stats = [
            'total_users' => $department->users()->count(),
            'active_users' => $department->users()->active()->count(),
            'origin_documents' => $department->originDocuments()->count(),
            'current_documents' => $department->currentDocuments()->count(),
            'pending_documents' => $department->currentDocuments()->pending()->count(),
        ];

        return view('admin.departments.show', compact('department', 'stats'));
    }

    public function create()
    {
        return view('admin.departments.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'required|string|max:20|unique:departments,code',
            'description' => 'nullable|string|max:1000',
            'head_name' => 'nullable|string|max:255',
            'is_active' => 'boolean',
        ]);

        try {
            $department = Department::create([
                'name' => $request->name,
                'code' => strtoupper($request->code),
                'description' => $request->description,
                'head_name' => $request->head_name,
                'is_active' => $request->boolean('is_active', true),
            ]);

            return redirect()->route('admin.departments.show', $department)
                           ->with('success', 'Department created successfully.');

        } catch (\Exception $e) {
            return redirect()->back()
                           ->withInput()
                           ->with('error', 'Error creating department: ' . $e->getMessage());
        }
    }

    public function edit(Department $department)
    {
        return view('admin.departments.edit', compact('department'));
    }

    public function update(Request $request, Department $department)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'code' => [
                'required',
                'string',
                'max:20',
                Rule::unique('departments')->ignore($department->id)
            ],
            'description' => 'nullable|string|max:1000',
            'head_name' => 'nullable|string|max:255',
            'is_active' => 'boolean',
        ]);

        try {
            $department->update([
                'name' => $request->name,
                'code' => strtoupper($request->code),
                'description' => $request->description,
                'head_name' => $request->head_name,
                'is_active' => $request->boolean('is_active'),
            ]);

            return redirect()->route('admin.departments.show', $department)
                           ->with('success', 'Department updated successfully.');

        } catch (\Exception $e) {
            return redirect()->back()
                           ->withInput()
                           ->with('error', 'Error updating department: ' . $e->getMessage());
        }
    }

    public function destroy(Department $department)
    {
        try {
            // Check if department has users or documents
            if ($department->users()->exists()) {
                return redirect()->back()
                               ->with('error', 'Cannot delete department with assigned users.');
            }

            if ($department->originDocuments()->exists() || $department->currentDocuments()->exists()) {
                return redirect()->back()
                               ->with('error', 'Cannot delete department with associated documents.');
            }

            $department->delete();

            return redirect()->route('admin.departments.index')
                           ->with('success', 'Department deleted successfully.');

        } catch (\Exception $e) {
            return redirect()->back()
                           ->with('error', 'Error deleting department: ' . $e->getMessage());
        }
    }

    public function toggleStatus(Department $department)
    {
        try {
            $department->update(['is_active' => !$department->is_active]);
            
            $status = $department->is_active ? 'activated' : 'deactivated';
            
            return response()->json([
                'success' => true,
                'message' => "Department {$status} successfully.",
                'is_active' => $department->is_active,
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error updating department status: ' . $e->getMessage(),
            ], 500);
        }
    }
}