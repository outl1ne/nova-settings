<?php

namespace OptimistDigital\NovaSettings;

use Laravel\Nova\Nova;
use Laravel\Nova\Tool;
use Illuminate\Support\Str;
use OptimistDigital\NovaSettings\Models\Settings;

class NovaSettings extends Tool
{
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
        if (config('nova-settings.show_in_sidebar', true)) {
            return view('nova-settings::navigation', [
                'fields' => static::getFields(),
                'basePath' => config('nova-settings.base_path', 'nova-settings'),
            ]);
        }
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
        return static::getStore()->addSettingsFields($fields, $casts, $path);
    }

    /**
     * Define casts.
     *
     * @param array $casts Casts same as Laravel's casts on a model.
     **/
    public static function addCasts($casts = [])
    {
        return static::getStore()->addCasts($casts);
    }

    public static function getFields($path = null)
    {
        if (!$path) return static::getStore()->getRawFields();
        return static::getStore()->getFields($path);
    }

    public static function clearFields()
    {
        return static::getStore()->clearFields();
    }

    public static function getCasts()
    {
        return static::getStore()->getCasts();
    }

    public static function getSetting($settingKey, $default = null)
    {
        return static::getStore()->getSetting($settingKey, $default);
    }

    public static function getSettings(array $settingKeys = null)
    {
        return static::getStore()->getSettings($settingKeys);
    }

    public static function setSettingValue($settingKey, $value = null)
    {
        return static::getStore()->setSettingValue($settingKey, $value);
    }

    public static function getSettingsModel(): string
    {
        return config('nova-settings.models.settings', Settings::class);
    }

    public static function doesPathExist($path)
    {
        return array_key_exists($path, static::getFields());
    }

    protected static function getStore(): NovaSettingsStore
    {
        return app()->make(NovaSettingsStore::class);
    }
}
