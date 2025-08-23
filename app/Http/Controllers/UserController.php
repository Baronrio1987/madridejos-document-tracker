<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Department;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use App\Http\Requests\UserRequest;

class UserController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('can:viewAny,App\Models\User')->only(['index']);
        $this->middleware('can:view,user')->only(['show']);
        $this->middleware('can:create,App\Models\User')->only(['create', 'store']);
        $this->middleware('can:update,user')->only(['edit', 'update']);
        $this->middleware('can:delete,user')->only(['destroy']);
    }

    public function index(Request $request)
    {
        $query = User::with('department');

        // Apply filters
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%")
                    ->orWhere('employee_id', 'like', "%{$search}%");
            });
        }

        if ($request->filled('department_id')) {
            $query->where('department_id', $request->department_id);
        }

        if ($request->filled('role')) {
            $query->where('role', $request->role);
        }

        if ($request->filled('status')) {
            $isActive = $request->status === 'active';
            $query->where('is_active', $isActive);
        }

        $users = $query->orderBy('name')->paginate(15);
        $departments = Department::active()->get();

        return view('users.index', compact('users', 'departments'));
    }

    public function show(User $user)
    {
        $user->load(['department', 'createdDocuments', 'routedDocuments']);

        // Get user statistics
        $stats = [
            'documents_created' => $user->createdDocuments()->count(),
            'documents_routed' => $user->routedDocuments()->count(),
            'pending_documents' => $user->createdDocuments()->pending()->count(),
            'completed_documents' => $user->createdDocuments()->completed()->count(),
        ];

        return view('users.show', compact('user', 'stats'));
    }

    public function create()
    {
        $departments = Department::active()->get();
        return view('users.create', compact('departments'));
    }

    public function store(UserRequest $request)
    {
        try {
            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => Hash::make($request->password),
                'employee_id' => $request->employee_id,
                'department_id' => $request->department_id,
                'role' => $request->role,
                'position' => $request->position,
                'phone' => $request->phone,
                'is_active' => $request->boolean('is_active', true),
            ]);

            return redirect()->route('users.show', $user)
                ->with('success', 'User created successfully.');
        } catch (\Exception $e) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'Error creating user: ' . $e->getMessage());
        }
    }

    public function edit(User $user)
    {
        $departments = Department::active()->get();
        return view('users.edit', compact('user', 'departments'));
    }

    public function update(UserRequest $request, User $user)
    {
        try {
            $data = [
                'name' => $request->name,
                'email' => $request->email,
                'employee_id' => $request->employee_id,
                'department_id' => $request->department_id,
                'role' => $request->role,
                'position' => $request->position,
                'phone' => $request->phone,
                'is_active' => $request->boolean('is_active'),
            ];

            // Only update password if provided
            if ($request->filled('password')) {
                $data['password'] = Hash::make($request->password);
            }

            $user->update($data);

            return redirect()->route('users.show', $user)
                ->with('success', 'User updated successfully.');
        } catch (\Exception $e) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'Error updating user: ' . $e->getMessage());
        }
    }

    public function destroy(User $user)
    {
        try {
            // Prevent self-deletion
            if ($user->id === Auth::id()) {
                return redirect()->back()
                    ->with('error', 'You cannot delete your own account.');
            }

            // Check if user is the last admin
            if ($user->role === 'admin') {
                $adminCount = User::where('role', 'admin')
                                 ->where('is_active', true)
                                 ->where('id', '!=', $user->id)
                                 ->count();
                
                if ($adminCount === 0) {
                    return redirect()->back()
                        ->with('error', 'Cannot delete the last admin user.');
                }
            }

            // FIXED: Handle related records before deletion
            DB::transaction(function () use ($user) {
                // Option 1: Transfer ownership to a system user or admin
                // Find a system admin to transfer ownership to
                $systemAdmin = User::where('role', 'admin')
                                  ->where('id', '!=', $user->id)
                                  ->where('is_active', true)
                                  ->first();

                if ($systemAdmin) {
                    // Transfer created documents to system admin
                    $user->createdDocuments()->update(['created_by' => $systemAdmin->id]);
                    
                    // Transfer routed documents to system admin
                    $user->routedDocuments()->update(['routed_by' => $systemAdmin->id]);
                    
                    // Transfer received documents to system admin
                    $user->receivedDocuments()->update(['received_by' => $systemAdmin->id]);
                    
                    // Transfer document histories to system admin
                    $user->histories()->update(['user_id' => $systemAdmin->id]);
                } else {
                    // If no admin found, set to null (make sure your database allows null)
                    $user->createdDocuments()->update(['created_by' => null]);
                    $user->routedDocuments()->update(['routed_by' => null]);
                    $user->receivedDocuments()->update(['received_by' => null]);
                    $user->histories()->update(['user_id' => null]);
                }

                // Delete related notifications
                $user->notifications()->delete();

                // ACTUAL DELETE: Now delete the user
                $user->delete();
            });

            return redirect()->route('users.index')
                ->with('success', 'User deleted successfully.');
                
        } catch (\Exception $e) {
            \Log::error('Error deleting user: ' . $e->getMessage(), [
                'user_id' => $user->id,
                'admin_id' => Auth::id(),
                'error' => $e->getTraceAsString()
            ]);
            
            return redirect()->back()
                ->with('error', 'Error deleting user: ' . $e->getMessage());
        }
    }

    public function toggleStatus(User $user)
    {
        // Check authorization
        if (!auth()->user()->isAdmin() && !auth()->user()->isDepartmentHead()) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized action.',
            ], 403);
        }

        // Prevent self-deactivation
        if ($user->id === auth()->id()) {
            return response()->json([
                'success' => false,
                'message' => 'You cannot change your own status.',
            ], 422);
        }

        // Check if trying to deactivate the last admin
        if ($user->role === 'admin' && $user->is_active) {
            $activeAdminCount = User::where('role', 'admin')
                                   ->where('is_active', true)
                                   ->where('id', '!=', $user->id)
                                   ->count();
            
            if ($activeAdminCount === 0) {
                return response()->json([
                    'success' => false,
                    'message' => 'Cannot deactivate the last admin user.',
                ], 422);
            }
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
    }

    public function profile()
    {
        $user = User::findOrFail(Auth::id());
        $user->load('department');

        return view('users.profile', compact('user'));
    }

    public function updateProfile(Request $request)
    {
        $user = User::findOrFail(Auth::id());

        $request->validate([
            'name' => 'required|string|max:255',
            'email' => ['required', 'string', 'email', 'max:255', Rule::unique('users')->ignore($user->id)],
            'phone' => 'nullable|string|max:20',
            'current_password' => 'nullable|required_with:password',
            'password' => 'nullable|string|min:8|confirmed',
        ]);

        try {
            $data = [
                'name' => $request->name,
                'email' => $request->email,
                'phone' => $request->phone,
            ];

            // Verify current password if changing password
            if ($request->filled('password')) {
                if (!Hash::check($request->current_password, $user->password)) {
                    return redirect()->back()
                        ->withErrors(['current_password' => 'Current password is incorrect.']);
                }
                $data['password'] = Hash::make($request->password);
            }

            $user->update($data);

            return redirect()->back()
                ->with('success', 'Profile updated successfully.');
        } catch (\Exception $e) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'Error updating profile: ' . $e->getMessage());
        }
    }
}