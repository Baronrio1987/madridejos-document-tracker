<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\SystemSetting;

class SystemSettingSeeder extends Seeder
{
    public function run()
    {
        $settings = [

            [
                'key' => 'qr.default_size',
                'value' => 300,
                'type' => 'integer',
                'group' => 'qr_codes',
                'description' => 'Default QR code size in pixels',
                'is_public' => false
            ],
            [
                'key' => 'qr.include_logo',
                'value' => true,
                'type' => 'boolean',
                'group' => 'qr_codes',
                'description' => 'Include municipality logo in QR codes',
                'is_public' => false
            ],
            [
                'key' => 'qr.max_bulk_generate',
                'value' => 100,
                'type' => 'integer',
                'group' => 'qr_codes',
                'description' => 'Maximum documents for bulk QR generation',
                'is_public' => false
            ],
            // System Settings (matches your views)
            ['key' => 'system.name', 'value' => 'Document Tracking System', 'type' => 'string', 'group' => 'general', 'description' => 'System name displayed throughout the application'],
            ['key' => 'municipality.name', 'value' => 'Municipality of Lawis', 'type' => 'string', 'group' => 'general', 'description' => 'Municipality name'],
            ['key' => 'municipality.email', 'value' => 'info@lawis.gov.ph', 'type' => 'string', 'group' => 'general', 'description' => 'Municipality contact email'],
            
            // Appearance Settings (matches your views)
            ['key' => 'appearance.logo', 'value' => null, 'type' => 'file', 'group' => 'appearance', 'description' => 'Main logo image'],
            ['key' => 'appearance.favicon', 'value' => null, 'type' => 'file', 'group' => 'appearance', 'description' => 'Site favicon'],
            ['key' => 'appearance.background_image', 'value' => null, 'type' => 'file', 'group' => 'appearance', 'description' => 'Background image for the site'],
            ['key' => 'appearance.background_opacity', 'value' => 0.1, 'type' => 'float', 'group' => 'appearance', 'description' => 'Background image opacity (0-1)'],
            
            // Theme Settings (matches your views)
            ['key' => 'theme.primary_color', 'value' => '#4dff00', 'type' => 'color', 'group' => 'theme', 'description' => 'Primary theme color'],
            ['key' => 'theme.secondary_color', 'value' => '#6c757d', 'type' => 'color', 'group' => 'theme', 'description' => 'Secondary theme color'],
            ['key' => 'theme.success_color', 'value' => '#059669', 'type' => 'color', 'group' => 'theme', 'description' => 'Success color'],
            ['key' => 'theme.warning_color', 'value' => '#d97706', 'type' => 'color', 'group' => 'theme', 'description' => 'Warning color'],
            ['key' => 'theme.danger_color', 'value' => '#dc2626', 'type' => 'color', 'group' => 'theme', 'description' => 'Danger color'],
            ['key' => 'theme.font_family', 'value' => 'Inter, sans-serif', 'type' => 'string', 'group' => 'theme', 'description' => 'Default font family'],
            ['key' => 'theme.font_size', 'value' => 14, 'type' => 'integer', 'group' => 'theme', 'description' => 'Base font size in pixels'],
            ['key' => 'theme.border_radius', 'value' => 0.5, 'type' => 'float', 'group' => 'theme', 'description' => 'Default border radius in rem'],
            ['key' => 'theme.sidebar_width', 'value' => 250, 'type' => 'integer', 'group' => 'theme', 'description' => 'Sidebar width in pixels'],
        ];

        foreach ($settings as $setting) {
            SystemSetting::updateOrCreate(
                ['key' => $setting['key']],
                $setting
            );
        }
    }
}