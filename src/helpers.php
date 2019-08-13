<?php

use OptimistDigital\NovaSettings\Models\Settings;

if (!function_exists('nova_get_settings')) {
    function nova_get_settings()
    {
        return Settings::all()->pluck('value', 'key');
    }
}
