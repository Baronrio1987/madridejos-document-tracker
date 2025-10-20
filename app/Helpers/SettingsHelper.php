<?php

if (!function_exists('setting')) {
    function setting($key, $default = null) {
        static $settings = null;
        
        if ($settings === null) {
            try {
                $settingsModels = \App\Models\SystemSetting::all();
                $processed = [];
                
                foreach ($settingsModels as $setting) {
                    $value = $setting->getRawOriginal('value');
                    
                    // Process the value based on type
                    switch ($setting->type) {
                        case 'boolean':
                            $processed[$setting->key] = filter_var($value, FILTER_VALIDATE_BOOLEAN);
                            break;
                        case 'integer':
                            $processed[$setting->key] = (int) $value;
                            break;
                        case 'float':
                            $processed[$setting->key] = (float) $value;
                            break;
                        case 'json':
                            $processed[$setting->key] = $value ? json_decode($value, true) : null;
                            break;
                        case 'file':
                            if (!$value) {
                                $processed[$setting->key] = null;
                            } else {
                                // Check if it's already a full URL
                                if (filter_var($value, FILTER_VALIDATE_URL)) {
                                    $processed[$setting->key] = $value;
                                } else {
                                    // Clean the path - remove any 'storage/' prefix
                                    $cleanPath = preg_replace('#^storage/#', '', $value);
                                    $cleanPath = ltrim($cleanPath, '/');
                                    
                                    // Check if file exists
                                    if (\Illuminate\Support\Facades\Storage::disk('public')->exists($cleanPath)) {
                                        // Use Storage::url() for proper URL generation
                                        $processed[$setting->key] = \Illuminate\Support\Facades\Storage::disk('public')->url($cleanPath);
                                    } else {
                                        \Log::warning("Setting file not found: {$cleanPath}");
                                        $processed[$setting->key] = null;
                                    }
                                }
                            }
                            break;
                        default:
                            $processed[$setting->key] = $value;
                    }
                }
                
                $settings = $processed;
            } catch (\Exception $e) {
                \Log::warning('Could not load system settings: ' . $e->getMessage());
                $settings = [];
            }
        }
        
        return $settings[$key] ?? $default;
    }
}

if (!function_exists('public_setting')) {
    function public_setting($key, $default = null) {
        static $publicSettings = null;
        
        if ($publicSettings === null) {
            try {
                $settingsModels = \App\Models\SystemSetting::where('is_public', true)->get();
                $processed = [];
                
                foreach ($settingsModels as $setting) {
                    $value = $setting->getRawOriginal('value');
                    
                    // Process the value based on type
                    switch ($setting->type) {
                        case 'boolean':
                            $processed[$setting->key] = filter_var($value, FILTER_VALIDATE_BOOLEAN);
                            break;
                        case 'integer':
                            $processed[$setting->key] = (int) $value;
                            break;
                        case 'float':
                            $processed[$setting->key] = (float) $value;
                            break;
                        case 'json':
                            $processed[$setting->key] = $value ? json_decode($value, true) : null;
                            break;
                        case 'file':
                            if (!$value) {
                                $processed[$setting->key] = null;
                            } else {
                                // Check if it's already a full URL
                                if (filter_var($value, FILTER_VALIDATE_URL)) {
                                    $processed[$setting->key] = $value;
                                } else {
                                    // Clean the path - remove any 'storage/' prefix
                                    $cleanPath = preg_replace('#^storage/#', '', $value);
                                    $cleanPath = ltrim($cleanPath, '/');
                                    
                                    // Check if file exists
                                    if (\Illuminate\Support\Facades\Storage::disk('public')->exists($cleanPath)) {
                                        // Use Storage::url() for proper URL generation
                                        $processed[$setting->key] = \Illuminate\Support\Facades\Storage::disk('public')->url($cleanPath);
                                    } else {
                                        \Log::warning("Public setting file not found: {$cleanPath}");
                                        $processed[$setting->key] = null;
                                    }
                                }
                            }
                            break;
                        default:
                            $processed[$setting->key] = $value;
                    }
                }
                
                $publicSettings = $processed;
            } catch (\Exception $e) {
                \Log::warning('Could not load public settings: ' . $e->getMessage());
                $publicSettings = [];
            }
        }
        
        return $publicSettings[$key] ?? $default;
    }
}

if (!function_exists('setting_raw')) {
    /**
     * Get the raw value of a system setting (without processing)
     *
     * @param string $key
     * @param mixed $default
     * @return mixed
     */
    function setting_raw($key, $default = null) {
        try {
            $setting = \App\Models\SystemSetting::where('key', $key)->first();
            return $setting ? $setting->getRawOriginal('value') : $default;
        } catch (\Exception $e) {
            \Log::error("Setting raw helper error for key '{$key}': " . $e->getMessage());
            return $default;
        }
    }
}

if (!function_exists('clear_settings_cache')) {
    /**
     * Clear the settings cache
     * Call this after updating settings
     *
     * @return void
     */
    function clear_settings_cache() {
        // Reset the static cache in the helper functions
        // This is done by creating a new request, but we can't do that directly
        // Instead, we'll just note that the cache will be cleared on next request
        
        // Also clear Laravel's cache if you're using it
        try {
            \Illuminate\Support\Facades\Cache::forget('system_settings');
            \Illuminate\Support\Facades\Cache::forget('public_settings');
        } catch (\Exception $e) {
            \Log::warning('Could not clear settings cache: ' . $e->getMessage());
        }
    }
}