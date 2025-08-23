<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DocumentRoute extends Model
{
    use HasFactory;

    protected $fillable = [
        'document_id', 'from_department_id', 'to_department_id',
        'routed_by', 'received_by', 'routing_purpose', 'instructions',
        'status', 'routed_at', 'received_at', 'processed_at', 'remarks'
    ];

    protected $casts = [
        'routed_at' => 'datetime',
        'received_at' => 'datetime',
        'processed_at' => 'datetime',
    ];

    public function document()
    {
        return $this->belongsTo(Document::class);
    }

    public function fromDepartment()
    {
        return $this->belongsTo(Department::class, 'from_department_id');
    }

    public function toDepartment()
    {
        return $this->belongsTo(Department::class, 'to_department_id');
    }

    public function routedBy()
    {
        return $this->belongsTo(User::class, 'routed_by');
    }

    public function receivedBy()
    {
        return $this->belongsTo(User::class, 'received_by');
    }

    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function scopeReceived($query)
    {
        return $query->where('status', 'received');
    }

    public function getDurationAttribute()
    {
        if ($this->received_at && $this->routed_at) {
            return $this->routed_at->diffInHours($this->received_at);
        }
        return null;
    }
}
