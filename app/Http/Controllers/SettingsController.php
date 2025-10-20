<?php
// app/Http/Controllers/SettingsController.php

namespace App\Http\Controllers;

use App\Models\SystemSetting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;

class SettingsController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('can:manage-settings');
    }

    public function index()
    {
        try {
            $allSettings = SystemSetting::orderBy('group')->orderBy('key')->get();
            
            if ($allSettings->isEmpty()) {
                $settings = [];
            } else {
                $settings = [];
                foreach ($allSettings as $setting) {
                    $groupName = $setting->group;
                    
                    if (!isset($settings[$groupName])) {
                        $settings[$groupName] = [];
                    }
                    
                    $settings[$groupName][] = $setting;
                }
            }
            
            return view('admin.settings.index', compact('settings'));
            
        } catch (\Exception $e) {
            \Log::error('Settings index error: ' . $e->getMessage());
            return view('admin.settings.index', ['settings' => []]);
        }
    }

    public function update(Request $request)
{
    $request->validate([
        'settings' => 'nullable|array',
        'files.*' => 'nullable|file|mimes:jpeg,png,jpg,gif,svg,pdf,doc,docx|max:2048'
    ]);

    try {
        $updatedCount = 0;

        // Handle regular settings
        if ($request->has('settings')) {
            foreach ($request->settings as $key => $value) {
                $setting = SystemSetting::where('key', $key)->first();
                
                if ($setting) {
                    if ($setting->type === 'boolean') {
                        $value = $value === '1' ? true : false;
                    } elseif ($setting->type === 'integer') {
                        $value = (int) $value;
                    } elseif ($setting->type === 'float') {
                        $value = (float) $value;
                    } elseif ($setting->type === 'json') {
                        if (is_string($value)) {
                            $decoded = json_decode($value, true);
                            $value = $decoded !== null ? $decoded : $value;
                        }
                    }
                    
                    $setting->update(['value' => $value]);
                    $updatedCount++;
                }
            }
        }

        // Handle file uploads
        if ($request->hasFile('files')) {
            foreach ($request->file('files') as $key => $file) {
                $setting = SystemSetting::where('key', $key)->first();
                
                if ($setting && $setting->type === 'file' && $file->isValid()) {
                    // Delete old file if exists
                    $oldPath = $setting->getRawOriginal('value');
                    if ($oldPath) {
                        // Clean the old path (remove 'storage/' prefix if present)
                        $cleanOldPath = preg_replace('#^storage/#', '', $oldPath);
                        
                        if (Storage::disk('public')->exists($cleanOldPath)) {
                            Storage::disk('public')->delete($cleanOldPath);
                            \Log::info("Deleted old file: {$cleanOldPath}");
                        }
                    }
                    
                    // Determine subdirectory based on setting key
                    $directory = 'settings';
                    if (str_contains($key, 'logo')) {
                        $directory = 'logos';
                    } elseif (str_contains($key, 'favicon')) {
                        $directory = 'icons';
                    } elseif (str_contains($key, 'background')) {
                        $directory = 'backgrounds';
                    }
                    
                    // Store new file with timestamp and clean filename
                    $filename = time() . '_' . Str::slug(pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME)) . '.' . $file->getClientOriginalExtension();
                    $path = $file->storeAs($directory, $filename, 'public');
                    
                    // Store ONLY the relative path (e.g., 'logos/1234567890_filename.png')
                    // WITHOUT the 'storage/' prefix
                    $setting->update(['value' => $path]);
                    $updatedCount++;
                    
                    \Log::info("File uploaded successfully", [
                        'key' => $key,
                        'path' => $path,
                        'full_path' => Storage::disk('public')->path($path),
                        'url' => Storage::disk('public')->url($path)
                    ]);
                }
            }
        }

        // Clear caches
        Cache::forget('system_settings');
        
        try {
            \Artisan::call('config:clear');
            \Artisan::call('view:clear');
            \Artisan::call('cache:clear');
        } catch (\Exception $e) {
            \Log::warning('Could not clear cache: ' . $e->getMessage());
        }

        return redirect()->back()
                       ->with('success', "Settings updated successfully. ({$updatedCount} settings changed)")
                       ->with('info', 'Changes will take effect immediately. Refresh if needed.');

    } catch (\Exception $e) {
        \Log::error('Settings update error: ' . $e->getMessage(), [
            'trace' => $e->getTraceAsString()
        ]);
        return redirect()->back()
                       ->with('error', 'Error updating settings: ' . $e->getMessage());
    }
}

    public function create()
    {
        return view('admin.settings.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'key' => 'required|string|unique:system_settings,key',
            'value' => 'required_unless:type,file',
            'type' => 'required|in:string,integer,boolean,json,file,float,color',
            'group' => 'required|string',
            'description' => 'nullable|string',
            'is_public' => 'boolean',
            'file_value' => 'required_if:type,file|file|mimes:jpeg,png,jpg,gif,svg,pdf,doc,docx|max:2048'
        ]);

        try {
            $data = $request->except(['file_value']);
            
            if ($request->type === 'file' && $request->hasFile('file_value')) {
                $file = $request->file('file_value');
                $filename = time() . '_' . Str::slug(pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME)) . '.' . $file->getClientOriginalExtension();
                $path = $file->storeAs('settings', $filename, 'public');
                $data['value'] = $path;
            }

            SystemSetting::create($data);
            Cache::forget('system_settings');

            return redirect()->route('admin.settings.index')
                           ->with('success', 'Setting created successfully.');

        } catch (\Exception $e) {
            \Log::error('Setting creation error: ' . $e->getMessage());
            return redirect()->back()
                           ->withInput()
                           ->with('error', 'Error creating setting: ' . $e->getMessage());
        }
    }

    public function destroy(SystemSetting $setting)
    {
        try {
            if ($setting->type === 'file' && $setting->getRawOriginal('value')) {
                Storage::disk('public')->delete($setting->getRawOriginal('value'));
            }
            
            $setting->delete();
            Cache::forget('system_settings');

            return response()->json([
                'success' => true,
                'message' => 'Setting deleted successfully.',
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error deleting setting: ' . $e->getMessage(),
            ], 500);
        }
    }

    public function generateCss()
    {
        try {
            $css = $this->buildDynamicCss();
            
            // Store it in a file for caching
            Storage::disk('public')->put('css/dynamic-styles.css', $css);
            
            return response($css, 200, [
                'Content-Type' => 'text/css',
                'Cache-Control' => 'public, max-age=3600', // Cache for 1 hour
                'Pragma' => 'cache',
                'Expires' => gmdate('D, d M Y H:i:s', time() + 3600) . ' GMT'
            ]);
            
        } catch (\Exception $e) {
            \Log::error('CSS generation error: ' . $e->getMessage());
            
            // Return empty CSS if there's an error
            return response('/* CSS generation error */', 200, [
                'Content-Type' => 'text/css'
            ]);
        }
    }

    public function seed()
    {
        try {
            \Artisan::call('db:seed', ['--class' => 'SystemSettingSeeder']);
            Cache::forget('system_settings');
            
            return response()->json([
                'success' => true,
                'message' => 'Default settings loaded successfully.',
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error loading default settings: ' . $e->getMessage(),
            ], 500);
        }
    }

    private function buildDynamicCss()
    {
        try {
            $settings = SystemSetting::whereIn('group', ['appearance', 'theme'])->get()->keyBy('key');
            
            $css = ":root {\n";
            
            // Colors with fallbacks
            $primaryColor = isset($settings['theme.primary_color']) ? $settings['theme.primary_color']->getProcessedValue() : '#1e40af';
            $secondaryColor = isset($settings['theme.secondary_color']) ? $settings['theme.secondary_color']->getProcessedValue() : '#64748b';
            $successColor = isset($settings['theme.success_color']) ? $settings['theme.success_color']->getProcessedValue() : '#059669';
            $warningColor = isset($settings['theme.warning_color']) ? $settings['theme.warning_color']->getProcessedValue() : '#d97706';
            $dangerColor = isset($settings['theme.danger_color']) ? $settings['theme.danger_color']->getProcessedValue() : '#dc2626';
            
            $css .= "    --primary-color: {$primaryColor};\n";
            $css .= "    --secondary-color: {$secondaryColor};\n";
            $css .= "    --success-color: {$successColor};\n";
            $css .= "    --warning-color: {$warningColor};\n";
            $css .= "    --danger-color: {$dangerColor};\n";
            
            // Fonts
            $fontFamily = isset($settings['theme.font_family']) ? $settings['theme.font_family']->getProcessedValue() : 'Inter, sans-serif';
            $fontSize = isset($settings['theme.font_size']) ? $settings['theme.font_size']->getProcessedValue() : 14;
            
            $css .= "    --font-family: {$fontFamily};\n";
            $css .= "    --font-size: {$fontSize}px;\n";
            
            $css .= "}\n\n";
            
            // Body styles
            $css .= "body {\n";
            $css .= "    font-family: var(--font-family);\n";
            $css .= "    font-size: var(--font-size);\n";
            
            // Background image - with proper error handling
            $hasBackground = false;
            if (isset($settings['appearance.background_image'])) {
                $bgImage = $settings['appearance.background_image']->getProcessedValue();
                $opacity = isset($settings['appearance.background_opacity']) ? 
                        $settings['appearance.background_opacity']->getProcessedValue() : 0.1;
                
                if ($bgImage) {
                    $hasBackground = true;
                    $css .= "    position: relative;\n";
                    $css .= "}\n\n";
                    $css .= "body::before {\n";
                    $css .= "    content: '';\n";
                    $css .= "    position: fixed;\n";
                    $css .= "    top: 0;\n";
                    $css .= "    left: 0;\n";
                    $css .= "    width: 100%;\n";
                    $css .= "    height: 100%;\n";
                    $css .= "    background-image: url('{$bgImage}');\n";
                    $css .= "    background-size: cover;\n";
                    $css .= "    background-position: center;\n";
                    $css .= "    background-repeat: no-repeat;\n";
                    $css .= "    background-attachment: fixed;\n";
                    $css .= "    opacity: {$opacity};\n";
                    $css .= "    z-index: -1;\n";
                    $css .= "    pointer-events: none;\n";
                }
            }
            
            if (!$hasBackground) {
                $css .= "}\n\n";
            }
            
            // Primary button styles
            $css .= ".btn-primary {\n";
            $css .= "    background-color: var(--primary-color) !important;\n";
            $css .= "    border-color: var(--primary-color) !important;\n";
            $css .= "    color: white !important;\n";
            $css .= "}\n\n";
            
            $css .= ".btn-primary:hover {\n";
            $css .= "    opacity: 0.9;\n";
            $css .= "}\n\n";
            
            // Success button
            $css .= ".btn-success {\n";
            $css .= "    background-color: var(--success-color) !important;\n";
            $css .= "    border-color: var(--success-color) !important;\n";
            $css .= "}\n\n";
            
            // Warning button
            $css .= ".btn-warning {\n";
            $css .= "    background-color: var(--warning-color) !important;\n";
            $css .= "    border-color: var(--warning-color) !important;\n";
            $css .= "}\n\n";
            
            // Danger button
            $css .= ".btn-danger {\n";
            $css .= "    background-color: var(--danger-color) !important;\n";
            $css .= "    border-color: var(--danger-color) !important;\n";
            $css .= "}\n\n";
            
            // Links
            $css .= "a {\n";
            $css .= "    color: var(--primary-color);\n";
            $css .= "}\n\n";
            
            $css .= "a:hover {\n";
            $css .= "    opacity: 0.8;\n";
            $css .= "}\n";
            
            return $css;
            
        } catch (\Exception $e) {
            \Log::error('buildDynamicCss error: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString()
            ]);
            return $this->getDefaultCss();
        }
    }

    private function getDefaultCss()
    {
        return ":root {
        --primary-color: #1e40af;
        --secondary-color: #64748b;
        --success-color: #059669;
        --warning-color: #d97706;
        --danger-color: #dc2626;
        --font-family: Inter, sans-serif;
        --font-size: 14px;
    }

    body {
        font-family: var(--font-family);
        font-size: var(--font-size);
    }

    .btn-primary {
        background-color: var(--primary-color) !important;
        border-color: var(--primary-color) !important;
        color: white !important;
    }

    .btn-primary:hover {
        opacity: 0.9;
    }

    a {
        color: var(--primary-color);
    }

    a:hover {
        opacity: 0.8;
    }";
    }

    public function loadDefaults()
    {
        try {
            $defaultSettings = $this->getDefaultSettings();
            $count = 0;
            
            foreach ($defaultSettings as $setting) {
                SystemSetting::updateOrCreate(
                    ['key' => $setting['key']],
                    $setting
                );
                $count++;
            }
            
            Cache::forget('system_settings');
            
            return response()->json([
                'success' => true,
                'message' => 'Default settings loaded successfully.',
                'count' => $count
            ]);
            
        } catch (\Exception $e) {
            \Log::error('Load defaults error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error loading default settings: ' . $e->getMessage(),
            ], 500);
        }
    }

    public function resetAll()
    {
        try {
            // Delete all existing settings
            SystemSetting::truncate();
            
            // Load default settings
            $defaultSettings = $this->getDefaultSettings();
            $count = 0;
            
            foreach ($defaultSettings as $setting) {
                SystemSetting::create($setting);
                $count++;
            }
            
            Cache::forget('system_settings');
            
            return response()->json([
                'success' => true,
                'message' => 'All settings have been reset to defaults.',
                'count' => $count
            ]);
            
        } catch (\Exception $e) {
            \Log::error('Reset all settings error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error resetting settings: ' . $e->getMessage(),
            ], 500);
        }
    }

    private function getDefaultSettings()
    {
        return [
            // System Settings
            ['key' => 'system.name', 'value' => 'Document Tracking System', 'type' => 'string', 'group' => 'general', 'description' => 'Name of the system', 'is_public' => true],
            ['key' => 'system.version', 'value' => '1.0.0', 'type' => 'string', 'group' => 'general', 'description' => 'System version', 'is_public' => true],
            ['key' => 'system.maintenance_mode', 'value' => false, 'type' => 'boolean', 'group' => 'general', 'description' => 'Enable maintenance mode', 'is_public' => false],
            
            // Municipality Settings
            ['key' => 'municipality.name', 'value' => 'Municipality of Madridejos', 'type' => 'string', 'group' => 'municipality', 'description' => 'Municipality name', 'is_public' => true],
            ['key' => 'municipality.province', 'value' => 'Cebu', 'type' => 'string', 'group' => 'municipality', 'description' => 'Province name', 'is_public' => true],
            ['key' => 'municipality.address', 'value' => 'Poblacion, Madridejos, Cebu', 'type' => 'string', 'group' => 'municipality', 'description' => 'Municipality address', 'is_public' => true],
            ['key' => 'municipality.contact_email', 'value' => 'info@madridejos.gov.ph', 'type' => 'string', 'group' => 'municipality', 'description' => 'Contact email', 'is_public' => true],
            ['key' => 'municipality.contact_phone', 'value' => '(032) 123-4567', 'type' => 'string', 'group' => 'municipality', 'description' => 'Contact phone', 'is_public' => true],
            
            // Theme Settings
            ['key' => 'theme.primary_color', 'value' => '#1e40af', 'type' => 'color', 'group' => 'theme', 'description' => 'Primary theme color', 'is_public' => true],
            ['key' => 'theme.secondary_color', 'value' => '#64748b', 'type' => 'color', 'group' => 'theme', 'description' => 'Secondary theme color', 'is_public' => true],
            ['key' => 'theme.success_color', 'value' => '#059669', 'type' => 'color', 'group' => 'theme', 'description' => 'Success color', 'is_public' => true],
            ['key' => 'theme.warning_color', 'value' => '#d97706', 'type' => 'color', 'group' => 'theme', 'description' => 'Warning color', 'is_public' => true],
            ['key' => 'theme.danger_color', 'value' => '#dc2626', 'type' => 'color', 'group' => 'theme', 'description' => 'Danger color', 'is_public' => true],
            ['key' => 'theme.font_family', 'value' => 'Inter, sans-serif', 'type' => 'string', 'group' => 'theme', 'description' => 'Font family', 'is_public' => true],
            ['key' => 'theme.font_size', 'value' => 14, 'type' => 'integer', 'group' => 'theme', 'description' => 'Base font size (px)', 'is_public' => true],
            ['key' => 'theme.border_radius', 'value' => 0.5, 'type' => 'float', 'group' => 'theme', 'description' => 'Border radius (rem)', 'is_public' => true],
            
            // Appearance Settings
            ['key' => 'appearance.logo', 'value' => '', 'type' => 'file', 'group' => 'appearance', 'description' => 'System logo', 'is_public' => true],
            ['key' => 'appearance.favicon', 'value' => '', 'type' => 'file', 'group' => 'appearance', 'description' => 'Website favicon', 'is_public' => true],
            ['key' => 'appearance.background_image', 'value' => '', 'type' => 'file', 'group' => 'appearance', 'description' => 'Background image', 'is_public' => true],
            ['key' => 'appearance.background_opacity', 'value' => 0.1, 'type' => 'float', 'group' => 'appearance', 'description' => 'Background image opacity', 'is_public' => true],
            // Document Settings
            ['key' => 'document.tracking_prefix', 'value' => 'MDJ', 'type' => 'string', 'group' => 'document', 'description' => 'Document tracking number prefix', 'is_public' => false],
            ['key' => 'document.auto_archive_days', 'value' => 365, 'type' => 'integer', 'group' => 'document', 'description' => 'Auto-archive documents after days', 'is_public' => false],
            ['key' => 'document.max_file_size', 'value' => 10, 'type' => 'integer', 'group' => 'document', 'description' => 'Maximum file upload size (MB)', 'is_public' => false],
            
            // Notification Settings
            ['key' => 'notification.email_enabled', 'value' => true, 'type' => 'boolean', 'group' => 'notification', 'description' => 'Enable email notifications', 'is_public' => false],
            ['key' => 'notification.sms_enabled', 'value' => false, 'type' => 'boolean', 'group' => 'notification', 'description' => 'Enable SMS notifications', 'is_public' => false],
            
            // Security Settings
            ['key' => 'security.session_timeout', 'value' => 120, 'type' => 'integer', 'group' => 'security', 'description' => 'Session timeout (minutes)', 'is_public' => false],
            ['key' => 'security.password_min_length', 'value' => 8, 'type' => 'integer', 'group' => 'security', 'description' => 'Minimum password length', 'is_public' => false],
        ];
    }
}