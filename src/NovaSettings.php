<?php

namespace Outl1ne\NovaSettings;

use Laravel\Nova\Nova;
use Laravel\Nova\Tool;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Laravel\Nova\Menu\MenuItem;
use Laravel\Nova\Menu\MenuSection;
use Outl1ne\NovaSettings\Models\Settings;

class NovaSettings extends Tool
{
    public function boot()
    {
        Nova::script('nova-settings', __DIR__ . '/../dist/js/entry.js');
    }

    public function menu(Request $request)
    {
        $fields = static::getFields();
        $basePath = config('nova-settings.base_path', 'nova-settings');
        $isAuthorized = static::canSeeSettings();
        $showInSidebar = config('nova-settings.show_in_sidebar', true);

        if (!$isAuthorized || !$showInSidebar || empty($fields)) return null;

        if (count($fields) == 1) {
            
            return MenuSection::make(__('novaSettings.navigationItemTitle'))
                ->path($basePath . '/' . array_key_first($fields))
                ->icon('adjustments');
        } 
        else {
            $menuItems = [];
            foreach ($fields as $key => $fields) {
                $menuItems[] = MenuItem::link(self::getPageName($key), "{$basePath}/{$key}");
            }

            return MenuSection::make(__('novaSettings.navigationItemTitle'), $menuItems)
                ->icon('adjustments')
                ->collapsable();
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

    public static function getAuthorizations($key = null)
    {
        $request = request();
        $fakeResource = new \Outl1ne\NovaSettings\Nova\Resources\Settings(NovaSettings::getSettingsModel()::make());

        $authorizations = [
            'authorizedToView' => $fakeResource->authorizedToView($request),
            'authorizedToCreate' => $fakeResource->authorizedToCreate($request),
            'authorizedToUpdate' => $fakeResource->authorizedToUpdate($request),
            'authorizedToDelete' => $fakeResource->authorizedToDelete($request),
        ];

        return $key ? $authorizations[$key] : $authorizations;
    }

    public static function canSeeSettings()
    {
        $auths = static::getAuthorizations();
        return $auths['authorizedToView'] || $auths['authorizedToUpdate'];
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

    public static function getSettings(array $settingKeys = null, array $defaults = [])
    {
        return static::getStore()->getSettings($settingKeys, $defaults);
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

    public static function getStore(): NovaSettingsStore
    {
        return app()->make(NovaSettingsStore::class);
    }
}
