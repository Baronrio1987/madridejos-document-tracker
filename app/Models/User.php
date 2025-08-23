<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    protected $fillable = [
        'name', 'email', 'password', 'employee_id', 'department_id', 
        'role', 'position', 'phone', 'is_active'
    ];

    protected $hidden = ['password', 'remember_token'];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'last_login_at' => 'datetime',
        'is_active' => 'boolean',
    ];

    // Relationships
    public function department()
    {
        return $this->belongsTo(Department::class);
    }

    public function createdDocuments()
    {
        return $this->hasMany(Document::class, 'created_by');
    }

    public function routedDocuments()
    {
        return $this->hasMany(DocumentRoute::class, 'routed_by');
    }

    public function receivedDocuments()
    {
        return $this->hasMany(DocumentRoute::class, 'received_by');
    }

    public function histories()
    {
        return $this->hasMany(DocumentHistory::class);
    }

    public function notifications()
    {
        return $this->hasMany(Notification::class);
    }

    public function activityLogs()
    {
        return $this->hasMany(ActivityLog::class);
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeByRole($query, $role)
    {
        return $query->where('role', $role);
    }

    // Helper methods
    public function isAdmin()
    {
        return $this->role === 'admin';
    }

    public function isDepartmentHead()
    {
        return $this->role === 'department_head';
    }

    public function canManageUsers()
    {
        return in_array($this->role, ['admin', 'department_head']);
    }

    public function canCreateDocuments()
    {
        return in_array($this->role, ['admin', 'department_head', 'encoder']);
    }

    public function canViewReports()
    {
        return in_array($this->role, ['admin', 'department_head']);
    }

    // Boot method to handle model events
    protected static function boot()
    {
        parent::boot();

        // Optional: Handle deletion events
        static::deleting(function ($user) {
            // Log the deletion attempt
            \Log::info('Attempting to delete user', [
                'user_id' => $user->id,
                'user_name' => $user->name,
                'user_email' => $user->email,
                'deleted_by' => auth()->id(),
            ]);
        });

        static::deleted(function ($user) {
            // Log successful deletion
            \Log::info('User successfully deleted', [
                'user_id' => $user->id,
                'user_name' => $user->name,
                'user_email' => $user->email,
                'deleted_by' => auth()->id(),
            ]);
        });
    }

    // Accessor for full name with role
    public function getFullNameWithRoleAttribute()
    {
        return $this->name . ' (' . ucfirst(str_replace('_', ' ', $this->role)) . ')';
    }

    // Check if user has pending documents
    public function hasPendingDocuments()
    {
        return $this->createdDocuments()->whereIn('status', ['pending', 'in_progress'])->exists();
    }

    // Get user's current workload
    public function getCurrentWorkload()
    {
        return [
            'pending_documents' => $this->createdDocuments()->where('status', 'pending')->count(),
            'in_progress_documents' => $this->createdDocuments()->where('status', 'in_progress')->count(),
            'pending_routes' => $this->routedDocuments()->where('status', 'pending')->count(),
        ];
    }
}