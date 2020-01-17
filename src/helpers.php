<?php

use OptimistDigital\NovaSettings\Models\Settings;

if (!function_exists('nova_get_settings')) {
    function nova_get_settings($keys = null)
    {
        $query = Settings::query();
        if (isset($keys)) $query->whereIn('key', $keys);
        return $query->get()->pluck('value', 'key')->toArray();
    }
}

if (!function_exists('nova_get_setting')) {
    function nova_get_setting($key)
    {
        $setting = Settings::find($key);
        return isset($setting) ? $setting->value : null;
    }
}
