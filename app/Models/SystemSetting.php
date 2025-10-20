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
                // For files, just store the path as-is (without 'storage/' prefix)
                $this->attributes['value'] = $value;
                break;
            default:
                $this->attributes['value'] = (string) $value;
        }
    }

    public function getProcessedValue()
    {
        $value = $this->attributes['value'] ?? null;
        
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
                if (filter_var($value, FILTER_VALIDATE_URL)) {
                    return $value;
                }
                
                // Clean the path - remove any 'storage/' prefix to avoid duplication
                $cleanPath = preg_replace('#^storage/#', '', $value);
                $cleanPath = ltrim($cleanPath, '/');
                
                // Check if file exists
                if (!Storage::disk('public')->exists($cleanPath)) {
                    \Log::warning("File not found in storage: {$cleanPath}");
                    return null;
                }
                
                // Use Storage::url() for proper URL generation
                // This respects your APP_URL and filesystem configuration
                return Storage::disk('public')->url($cleanPath);
            default:
                return $value;
        }
    }

    public static function get($key, $default = null)
    {
        try {
            $setting = static::where('key', $key)->first();
            return $setting ? $setting->getProcessedValue() : $default;
        } catch (\Exception $e) {
            \Log::error("Error getting setting '{$key}': " . $e->getMessage());
            return $default;
        }
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
        return $this->attributes['value'] ?? null;
    }
    
    /**
     * Get the original value without any processing
     */
    public function getRawOriginal($key)
    {
        return $this->attributes[$key] ?? null;
    }
}