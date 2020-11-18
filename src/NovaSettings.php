<?php

namespace OptimistDigital\NovaSettings;

use Laravel\Nova\Nova;
use Laravel\Nova\Tool;
use Illuminate\Support\Str;
use OptimistDigital\NovaSettings\Models\Settings;

class NovaSettings extends Tool
{
    protected static $cache = [];
    protected static $fields = [];
    protected static $casts = [];

    public function boot()
    {
        Nova::script('nova-settings', __DIR__ . '/../dist/js/tool.js');
    }

    public function renderNavigation()
    {
        return view('nova-settings::navigation', ['fields' => self::$fields]);
    }

    /**
     * Define settings fields and an optional casts.
     *
     * @param array|callable $fields Array of fields/panels to be displayed or callable that returns an array.
     * @param array $casts Associative array same as Laravel's $casts on models.
     **/
    public static function addSettingsFields($fields = [], $casts = [], $path = 'general')
    {
        $path = Str::lower(Str::slug($path));

        self::$fields[$path] = self::$fields[$path] ?? [];
        if (is_callable($fields)) $fields = [$fields];
        self::$fields[$path] = array_merge(self::$fields[$path], $fields ?? []);

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

    public static function getFields($path = 'general')
    {
        $rawFields = array_map(function ($fieldItem) {
            return is_callable($fieldItem) ? call_user_func($fieldItem) : $fieldItem;
        }, self::$fields[$path] ?? self::$fields);

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

    public static function getSetting($settingKey, $default = null)
    {
        if (isset(static::$cache[$settingKey])) return static::$cache[$settingKey];
        static::$cache[$settingKey] = static::getSettingsModel()::find($settingKey)->value ?? $default;
        return static::$cache[$settingKey];
    }

    public static function getSettings(array $settingKeys = null)
    {
        if (!empty($settingKeys)) {
            $hasMissingKeys = !empty(array_diff($settingKeys, array_keys(static::$cache)));

            if (!$hasMissingKeys) return collect($settingKeys)->mapWithKeys(function ($settingKey) {
                return [$settingKey => static::$cache[$settingKey]];
            })->toArray();

            return static::getSettingsModel()::find($settingKeys)->map(function ($setting) {
                static::$cache[$setting->key] = $setting->value;
                return $setting;
            })->pluck('value', 'key')->toArray();
        }

        return static::getSettingsModel()::all()->map(function ($setting) {
            static::$cache[$setting->key] = $setting->value;
            return $setting;
        })->pluck('value', 'key')->toArray();
    }

    public static function getSettingsModel(): string
    {
        return config('nova-settings.models.settings', Settings::class);
    }

    public static function doesPathExist($path)
    {
        return array_key_exists($path, self::$fields);
    }
}
