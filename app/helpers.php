<?php

use App\Models\SystemSetting;
use App\Helpers\SettingsHelper;

if (!function_exists('setting')) {
    /**
     * Get a system setting value
     *
     * @param string $key
     * @param mixed $default
     * @return mixed
     */
    function setting($key, $default = null)
    {
        return SettingsHelper::get($key, $default);
    }
}

if (!function_exists('public_setting')) {
    /**
     * Get a public system setting value
     *
     * @param string $key
     * @param mixed $default
     * @return mixed
     */
    function public_setting($key, $default = null)
    {
        return SettingsHelper::getPublic($key, $default);
    }
}

if (!function_exists('qr_setting')) {
    /**
     * Get QR code related setting
     *
     * @param string $key
     * @param mixed $default
     * @return mixed
     */
    function qr_setting($key, $default = null)
    {
        return setting("qr.{$key}", $default);
    }
}

if (!function_exists('system_name')) {
    /**
     * Get the system name
     *
     * @return string
     */
    function system_name()
    {
        return setting('system.name', 'Document Management System');
    }
}

if (!function_exists('municipality_name')) {
    /**
     * Get the municipality name
     *
     * @return string
     */
    function municipality_name()
    {
        return setting('municipality.name', 'Municipality of Madridejos');
    }
}