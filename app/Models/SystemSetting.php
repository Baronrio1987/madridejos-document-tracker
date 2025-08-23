<?php
// app/Models/SystemSetting.php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class SystemSetting extends Model
{
    use HasFactory;

    protected $fillable = ['key', 'value', 'type', 'group', 'description', 'is_public'];

    protected $casts = [
        'is_public' => 'boolean',
    ];

    public function setValueAttribute($value)
    {
        switch ($this->type) {
            case 'boolean':
                $this->attributes['value'] = $value ? '1' : '0';
                break;
            case 'json':
                $this->attributes['value'] = is_array($value) ? json_encode($value) : $value;
                break;
            case 'file':
                // For files, just store the path as-is
                $this->attributes['value'] = $value;
                break;
            default:
                $this->attributes['value'] = (string) $value;
        }
    }

    public function getProcessedValue()
    {
        $value = $this->attributes['value'];
        
        switch ($this->type) {
            case 'boolean':
                return filter_var($value, FILTER_VALIDATE_BOOLEAN);
            case 'integer':
                return (int) $value;
            case 'float':
                return (float) $value;
            case 'json':
                return $value ? json_decode($value, true) : null;
            case 'file':
                if (!$value) return null;
                
                // Check if it's already a full URL
                if (str_starts_with($value, 'http')) {
                    return $value;
                }
                
                // If it starts with storage/, remove it to avoid double storage/
                if (str_starts_with($value, 'storage/')) {
                    $value = substr($value, 8);
                }
                
                return asset('storage/' . $value);
            default:
                return $value;
        }
    }

    public static function get($key, $default = null)
    {
        $setting = static::where('key', $key)->first();
        return $setting ? $setting->getProcessedValue() : $default;
    }

    public static function set($key, $value, $type = 'string', $group = 'general')
    {
        return static::updateOrCreate(
            ['key' => $key],
            ['value' => $value, 'type' => $type, 'group' => $group]
        );
    }

    public function scopeByGroup($query, $group)
    {
        return $query->where('group', $group);
    }

    public function scopePublic($query)
    {
        return $query->where('is_public', true);
    }

    public function getRawValueAttribute()
    {
        return $this->attributes['value'];
    }
}