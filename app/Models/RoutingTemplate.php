<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RoutingTemplate extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'document_type_id', 'route_sequence', 'description', 'is_active'];

    protected $casts = [
        'route_sequence' => 'array',
        'is_active' => 'boolean',
    ];

    public function documentType()
    {
        return $this->belongsTo(DocumentType::class);
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
}
