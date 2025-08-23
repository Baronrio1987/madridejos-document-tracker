<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Department extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'code', 'description', 'head_name', 'is_active'];

    protected $casts = ['is_active' => 'boolean'];

    public function users()
    {
        return $this->hasMany(User::class);
    }

    public function originDocuments()
    {
        return $this->hasMany(Document::class, 'origin_department_id');
    }

    public function currentDocuments()
    {
        return $this->hasMany(Document::class, 'current_department_id');
    }

    public function fromRoutes()
    {
        return $this->hasMany(DocumentRoute::class, 'from_department_id');
    }

    public function toRoutes()
    {
        return $this->hasMany(DocumentRoute::class, 'to_department_id');
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
}
