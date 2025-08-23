<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Storage; // ADD THIS LINE

class Document extends Model
{
    use HasFactory;

    protected $fillable = [
        'tracking_number', 'title', 'description', 'document_type_id',
        'origin_department_id', 'current_department_id', 'created_by',
        'priority', 'status', 'date_received', 'target_completion_date',
        'actual_completion_date', 'remarks', 'is_confidential'
    ];

    protected $casts = [
        'date_received' => 'date',
        'target_completion_date' => 'date',
        'actual_completion_date' => 'date',
        'is_confidential' => 'boolean',
    ];
    
    public function documentType(): BelongsTo
    {
        return $this->belongsTo(DocumentType::class);
    }

    public function originDepartment(): BelongsTo
    {
        return $this->belongsTo(Department::class, 'origin_department_id');
    }

    public function currentDepartment(): BelongsTo
    {
        return $this->belongsTo(Department::class, 'current_department_id');
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function routes(): HasMany
    {
        return $this->hasMany(DocumentRoute::class);
    }

    public function histories()
    {
        return $this->hasMany(DocumentHistory::class);
    }

    public function notifications()
    {
        return $this->hasMany(Notification::class);
    }

    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function scopeInProgress($query)
    {
        return $query->where('status', 'in_progress');
    }

    public function scopeCompleted($query)
    {
        return $query->where('status', 'completed');
    }

    public function scopeHighPriority($query)
    {
        return $query->whereIn('priority', ['high', 'urgent']);
    }

    public function scopeConfidential($query)
    {
        return $query->where('is_confidential', true);
    }

    public static function generateTrackingNumber()
    {
        $year = date('Y');
        $month = date('m');
        $count = self::whereYear('created_at', $year)
                    ->whereMonth('created_at', $month)
                    ->count() + 1;
        
        return sprintf('MDJ-%s%s-%04d', $year, $month, $count);
    }

    public function getCurrentRoute()
    {
        return $this->routes()->latest()->first();
    }

    public function isOverdue()
    {
        return $this->target_completion_date && 
               $this->target_completion_date->isPast() && 
               $this->status !== 'completed';
    }

    public function attachments(): HasMany
    {
        return $this->hasMany(DocumentAttachment::class)->where('is_active', true);
    }

    public function allAttachments(): HasMany
    {
        return $this->hasMany(DocumentAttachment::class);
    }

    public function comments()
    {
        return $this->hasMany(DocumentComment::class);
    }

    public function approvals()
    {
        return $this->hasMany(DocumentApproval::class);
    }

    public function reminders()
    {
        return $this->hasMany(DocumentReminder::class);
    }

    public function activityLogs()
    {
        return $this->morphMany(ActivityLog::class, 'model');
    }

    public function getTotalFileSizeAttribute()
    {
        return $this->attachments()->sum('file_size');
    }

    public function hasPendingApprovals()
    {
        return $this->approvals()->pending()->exists();
    }

    public function isApproved()
    {
        return $this->approvals()->where('is_final_approval', true)->approved()->exists();
    }

    public function hasOverdueReminders()
    {
        return $this->reminders()->pending()->exists();
    }

    public function getAttachmentsCountAttribute()
    {
        return $this->attachments()->count();
    }

    public function getCommentsCountAttribute()
    {
        return $this->comments()->count();
    }

    public function getRoutesCountAttribute()
    {
        return $this->routes()->count();
    }

    public function hasAttachments()
    {
        return $this->attachments()->exists();
    }

    public function hasComments()
    {
        return $this->comments()->exists();
    }

    public function hasRoutes()
    {
        return $this->routes()->exists();
    }

    public function getSafeAttachmentsAttribute()
    {
        try {
            return $this->attachments;
        } catch (\Exception $e) {
            return collect();
        }
    }

    public function canUpdateStatus()
    {
        return !in_array($this->status, ['completed', 'archived']);
    }

    public function getStatusColorAttribute()
    {
        return [
            'pending' => 'warning',
            'in_progress' => 'info', 
            'completed' => 'success',
            'cancelled' => 'danger',
            'archived' => 'secondary'
        ][$this->status] ?? 'secondary';
    }

    public function getQrCodeUrlAttribute()
    {
        return route('qr-codes.generate', $this);
    }

    public function getTrackingUrlAttribute()
    {
        return route('public.track.show', $this->tracking_number);
    }

    public function hasQrCode()
    {
        $path = 'qr-codes/document_' . $this->tracking_number . '.png';
        return Storage::disk('public')->exists($path);
    }

    public function getQrCodePath()
    {
        $path = 'qr-codes/document_' . $this->tracking_number . '.png';
        return Storage::disk('public')->exists($path) ? Storage::disk('public')->url($path) : null;
    }
}