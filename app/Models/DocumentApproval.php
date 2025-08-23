<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DocumentApproval extends Model
{
    use HasFactory;

    protected $fillable = [
        'document_id', 'approver_id', 'status', 'remarks', 'signature_path',
        'approved_at', 'approval_level', 'is_final_approval'
    ];

    protected $casts = [
        'approved_at' => 'datetime',
        'is_final_approval' => 'boolean',
    ];

    public function document()
    {
        return $this->belongsTo(Document::class);
    }

    public function approver()
    {
        return $this->belongsTo(User::class, 'approver_id');
    }

    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function scopeApproved($query)
    {
        return $query->where('status', 'approved');
    }

    public function scopeRejected($query)
    {
        return $query->where('status', 'rejected');
    }

    public function scopeByLevel($query, $level)
    {
        return $query->where('approval_level', $level);
    }

    public function approve($remarks = null)
    {
        $this->update([
            'status' => 'approved',
            'approved_at' => now(),
            'remarks' => $remarks,
        ]);
    }

    public function reject($remarks = null)
    {
        $this->update([
            'status' => 'rejected',
            'remarks' => $remarks,
        ]);
    }
}
