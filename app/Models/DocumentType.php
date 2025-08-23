<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DocumentType extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'code', 'description', 'retention_period', 'is_active'];

    protected $casts = ['is_active' => 'boolean'];

    public function documents()
    {
        return $this->hasMany(Document::class);
    }

    public function routingTemplates()
    {
        return $this->hasMany(RoutingTemplate::class);
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
}
