<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ActivityLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id', 'action', 'model_type', 'model_id', 'description',
        'properties', 'ip_address', 'user_agent'
    ];

    protected $casts = [
        'properties' => 'array',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function model()
    {
        if ($this->model_type && $this->model_id) {
            return $this->morphTo('model', 'model_type', 'model_id');
        }
        return null;
    }

    public static function log($action, $description, $model = null, $properties = [])
    {
        return static::create([
            'user_id' => auth()->id(),
            'action' => $action,
            'model_type' => $model ? get_class($model) : null,
            'model_id' => $model ? $model->id : null,
            'description' => $description,
            'properties' => $properties,
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
        ]);
    }

    public function scopeByAction($query, $action)
    {
        return $query->where('action', $action);
    }

    public function scopeByUser($query, $userId)
    {
        return $query->where('user_id', $userId);
    }

    public function scopeByModel($query, $model)
    {
        return $query->where('model_type', get_class($model))
                    ->where('model_id', $model->id);
    }
}
