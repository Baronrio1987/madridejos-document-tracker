<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DocumentReminder extends Model
{
    use HasFactory;

    protected $fillable = [
        'document_id', 'user_id', 'title', 'message', 'remind_at',
        'type', 'is_sent', 'sent_at', 'is_recurring', 'recurring_pattern'
    ];

    protected $casts = [
        'remind_at' => 'datetime',
        'sent_at' => 'datetime',
        'is_sent' => 'boolean',
        'is_recurring' => 'boolean',
    ];

    public function document()
    {
        return $this->belongsTo(Document::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function scopePending($query)
    {
        return $query->where('is_sent', false)
                    ->where('remind_at', '<=', now());
    }

    public function scopeSent($query)
    {
        return $query->where('is_sent', true);
    }

    public function scopeRecurring($query)
    {
        return $query->where('is_recurring', true);
    }

    public function scopeByType($query, $type)
    {
        return $query->where('type', $type);
    }

    public function markAsSent()
    {
        $this->update([
            'is_sent' => true,
            'sent_at' => now(),
        ]);
    }

    public function isDue()
    {
        return !$this->is_sent && $this->remind_at <= now();
    }
}
