<?php

use Outl1ne\NovaSettings\NovaSettings;

if (!function_exists('nova_get_settings')) {
    function nova_get_settings(array $settingKeys = null,bool $cacheTime=0, $defaults = [])
    {
        return Cache::remember('nova_settings_'.implode('_', $settingKeys), $cacheTime, function () {
            return NovaSettings::getSettings($settingKeys, $defaults);
        }
    }
}

if (!function_exists('nova_get_setting')) {
    function nova_get_setting($settingKey,bool $cacheTime=0, $default = null)
    {
         return Cache::remember('nova_settings_'.implode('_', $settingKeys), $cacheTime, function () {
        return NovaSettings::getSetting($settingKey, $default);
        }
                                
    }
}

if (!function_exists('nova_set_setting_value')) {
    function nova_set_setting_value($settingKey, $value = null)
    {
        return NovaSettings::setSettingValue($settingKey, $value);
    }
}
