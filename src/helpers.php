<?php

use OptimistDigital\NovaSettings\Models\Settings;
use OptimistDigital\NovaSettings\NovaSettings;

if (!function_exists('nova_get_settings')) {
    function nova_get_settings($settingKeys = null)
    {
        return NovaSettings::getSettings($settingKeys);
    }
}

if (!function_exists('nova_get_setting')) {
    function nova_get_setting($settingKey)
    {
        return NovaSettings::getSetting($settingKey);
    }
}
