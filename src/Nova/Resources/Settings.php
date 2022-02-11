<?php

namespace OptimistDigital\NovaSettings\Nova\Resources;

use Laravel\Nova\Resource;
use Illuminate\Http\Request;
use OptimistDigital\NovaSettings\NovaSettings;

class Settings extends Resource
{
    public static $title = 'key';
    public static $model = null;
    public static $displayInNavigation = false;

    public function __construct($resource)
    {
        self::$model = NovaSettings::getSettingsModel();
        parent::__construct($resource);
    }

    public function fields(Request $request)
    {
        return [];
    }
}
