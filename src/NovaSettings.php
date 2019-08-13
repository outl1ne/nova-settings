<?php

namespace OptimistDigital\NovaSettings;

use Laravel\Nova\Nova;
use Laravel\Nova\Tool;

class NovaSettings extends Tool
{
    protected static $settingsFields = [];
    protected static $customFormatter;

    public function boot()
    {
        Nova::script('nova-settings', __DIR__ . '/../dist/js/tool.js');
        Nova::style('nova-settings', __DIR__ . '/../dist/css/tool.css');
    }

    public function renderNavigation()
    {
        return view('nova-settings::navigation');
    }

    /**
     * Define settings fields and an optional custom format function.
     *
     * @param array $settingsFields Array of Nova fields to be displayed.
     * @param \Closure $customFormatter A function that takes key and value as arguments, formats and returns the value.
     **/
    public static function setSettingsFields($settingsFields = [], $customFormatter = null)
    {
        self::$settingsFields = $settingsFields;
        self::$customFormatter = $customFormatter;
    }

    public static function getSettingsFields()
    {
        return self::$settingsFields;
    }

    public static function getCustomFormatter()
    {
        return self::$customFormatter;
    }
}
