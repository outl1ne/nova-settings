<?php

namespace OptimistDigital\NovaSettings;

use Laravel\Nova\Nova;
use Laravel\Nova\Tool;
use OptimistDigital\NovaSettings\Models\Settings;

class NovaSettings extends Tool
{
    protected static $cache = [];

    protected static $fields = [];
    protected static $casts = [];

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
     * Define settings fields and an optional casts.
     *
     * @param array|callable $fields Array of fields/panels to be displayed or callable that returns an array.
     * @param array $casts Associative array same as Laravel's $casts on models.
     **/
    public static function addSettingsFields($fields = [], $casts = [])
    {
        if (is_callable($fields)) $fields = [$fields];
        self::$fields = array_merge(self::$fields, $fields ?? []);
        self::$casts = array_merge(self::$casts, $casts ?? []);
    }

    /**
     * Define casts.
     *
     * @param array $casts Casts same as Laravel's casts on a model.
     **/
    public static function addCasts($casts = [])
    {
        self::$casts = array_merge(self::$casts, $casts);
    }

    public static function getFields()
    {
        $rawFields = array_map(function ($fieldItem) {
            return is_callable($fieldItem) ? call_user_func($fieldItem) : $fieldItem;
        }, self::$fields);

        $fields = [];
        foreach ($rawFields as $rawField) {
            if (is_array($rawField)) $fields = array_merge($fields, $rawField);
            else $fields[] = $rawField;
        }

        return $fields;
    }

    public static function getCasts()
    {
        return self::$casts;
    }

    public static function getSetting($settingKey)
    {
        if (isset(static::$cache[$settingKey])) return static::$cache[$settingKey];
        static::$cache[$settingKey] = Settings::find($settingKey)->value ?? null;
        return static::$cache[$settingKey];
    }

    public static function getSettings(array $settingKeys = null)
    {
        if (!empty($settings)) {
            $hasMissingKeys = !empty(array_diff($settingKeys, array_keys(static::$cache)));

            if (!$hasMissingKeys) return array_map(function ($settingKey) {
                return static::$cache[$settingKey];
            }, $settingKeys);

            return Settings::find($settingKeys)->map(function ($setting) {
                static::$cache[$setting->key] = $setting->value;
                return $setting;
            })->pluck('value', 'key')->toArray();
        }

        return Settings::all()->map(function ($setting) {
            static::$cache[$setting->key] = $setting->value;
            return $setting;
        })->pluck('value', 'key')->toArray();
    }
}
