<?php

use OptimistDigital\NovaSettings\Models\Settings;

if (!function_exists('nova_get_settings')) {
    function nova_get_settings()
    {
        return Settings::all()->pluck('value', 'key')->toArray();
    }
}

if (!function_exists('nova_get_setting_value')) {
    function nova_get_setting_value($key)
    {
        $setting = Settings::find($key);
        return is_null($setting) ? null : $setting->value;
    }
}
