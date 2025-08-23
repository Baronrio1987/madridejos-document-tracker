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
                                if (str_starts_with($value, 'http')) {
                                    $processed[$setting->key] = $value;
                                } else {
                                    // If it starts with storage/, remove it to avoid double storage/
                                    if (str_starts_with($value, 'storage/')) {
                                        $value = substr($value, 8);
                                    }
                                    $processed[$setting->key] = asset('storage/' . $value);
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
                    
                    // Process the value based on type (same logic as above)
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
                                if (str_starts_with($value, 'http')) {
                                    $processed[$setting->key] = $value;
                                } else {
                                    // If it starts with storage/, remove it to avoid double storage/
                                    if (str_starts_with($value, 'storage/')) {
                                        $value = substr($value, 8);
                                    }
                                    $processed[$setting->key] = asset('storage/' . $value);
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