<?php

namespace App\Providers;

use App\Models\SystemSetting;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Cache;

class SettingsServiceProvider extends ServiceProvider
{
    public function boot()
    {
        View::composer('*', function ($view) {
            $globalSettings = Cache::remember('system_settings', 3600, function () {
                try {
                    $settings = SystemSetting::all();
                    $processed = [];
                    
                    foreach ($settings as $setting) {
                        $processed[$setting->key] = $setting->getProcessedValue();
                    }
                    
                    return $processed;
                } catch (\Exception $e) {
                    \Log::warning('Could not load system settings: ' . $e->getMessage());
                    return [];
                }
            });
            
            $view->with('globalSettings', $globalSettings);
        });
    }

    public function register()
    {
        // Don't register the setting function here since it's in the helper file
        // The helper file is loaded first due to composer autoload
    }
}