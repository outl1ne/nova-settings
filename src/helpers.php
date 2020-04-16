<?php

use OptimistDigital\NovaSettings\NovaSettings;

if (!function_exists('nova_get_settings')) {
    function nova_get_settings($settingKeys = null)
    {
        return NovaSettings::getSettings($settingKeys);
    }
}

if (!function_exists('nova_get_setting')) {
    function nova_get_setting($settingKey, $default = null)
    {
        return NovaSettings::getSetting($settingKey, $default);
    }
}
