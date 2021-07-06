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

        Nova::provideToScript([
            'novaSettings' => [
                'basePath' => config('nova-settings.base_path', 'nova-settings'),
            ],
        ]);
    }

    public function renderNavigation()
    {
        return view('nova-settings::navigation', [
            'fields' => static::$fields,
            'basePath' => config('nova-settings.base_path', 'nova-settings'),
        ]);
    }

    public static function getSettingsTableName(): string
    {
        return config('nova-settings.table', 'nova_settings');
    }

    public static function getPageName($key): string
    {
        if (__("novaSettings.$key") === "novaSettings.$key") {
            return Str::title(str_replace('-', ' ', $key));
        } else {
            return __("novaSettings.$key");
        }
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

        static::$fields[$path] = static::$fields[$path] ?? [];
        if (is_callable($fields)) $fields = [$fields];
        static::$fields[$path] = array_merge(static::$fields[$path], $fields ?? []);

        static::$casts = array_merge(static::$casts, $casts ?? []);
    }

    /**
     * Define casts.
     *
     * @param array $casts Casts same as Laravel's casts on a model.
     **/
    public static function addCasts($casts = [])
    {
        static::$casts = array_merge(static::$casts, $casts);
    }

    public static function getFields($path = 'general')
    {
        $rawFields = array_map(function ($fieldItem) {
            return is_callable($fieldItem) ? call_user_func($fieldItem) : $fieldItem;
        }, static::$fields[$path] ?? static::$fields);

        $fields = [];
        foreach ($rawFields as $rawField) {
            if (is_array($rawField)) $fields = array_merge($fields, $rawField);
            else $fields[] = $rawField;
        }

        return $fields;
    }

    public static function clearFields()
    {
        static::$fields = [];
        static::$casts = [];
        static::$cache = [];
    }

    public static function getCasts()
    {
        return static::$casts;
    }

    public static function getSetting($settingKey, $default = null)
    {
        if (isset(static::$cache[$settingKey])) return static::$cache[$settingKey];
        static::$cache[$settingKey] = static::getSettingsModel()::getValueForKey($settingKey) ?? $default;
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

    public static function setSettingValue($settingKey, $value = null)
    {
        $setting = static::getSettingsModel()::firstOrCreate(['key' => $settingKey]);
        $setting->value = $value;
        $setting->save();
        unset(static::$cache[$settingKey]);
        return $setting;
    }

    public static function getSettingsModel(): string
    {
        return config('nova-settings.models.settings', Settings::class);
    }

    public static function doesPathExist($path)
    {
        return array_key_exists($path, static::$fields);
    }
}
