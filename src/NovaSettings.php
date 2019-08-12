<?php

namespace OptimistDigital\NovaSettings;

use Laravel\Nova\Nova;
use Laravel\Nova\Tool;

class NovaSettings extends Tool
{
    protected static $settingsFields = [];

    public function boot()
    {
        Nova::script('nova-settings', __DIR__ . '/../dist/js/tool.js');
        Nova::style('nova-settings', __DIR__ . '/../dist/css/tool.css');
    }

    public function renderNavigation()
    {
        return view('nova-settings::navigation');
    }

    public static function setSettingsFields($settingsFields = [])
    {
        self::$settingsFields = $settingsFields;
    }

    public static function getSettingsFields()
    {
        return self::$settingsFields;
    }
}
