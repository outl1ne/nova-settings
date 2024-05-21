<?php

namespace Outl1ne\NovaSettings;

use Illuminate\Support\Str;

use function array_diff;
use function array_keys;
use function array_map;
use function array_merge;
use function call_user_func;
use function collect;
use function is_array;
use function is_callable;

abstract class NovaSettingsStore
{
    protected $fields = [];
    protected $casts = [];

    public function addSettingsFields($fields = [], $casts = [], $path = 'general')
    {
        $path = Str::lower(Str::slug($path));

        if (is_callable($fields)) $fields = [$fields];
        $this->fields[$path] = array_merge($this->fields[$path] ?? [], $fields ?? []);

        $this->casts = array_merge($this->casts, $casts ?? []);

        return $this;
    }

    public function addCasts($casts = [])
    {
        $this->casts = array_merge($this->casts, $casts);
        return $this;
    }

    public function getRawFields()
    {
        return $this->fields;
    }

    public function getFields($path = 'general')
    {
        $rawFields = array_map(function ($fieldItem) {
            return is_callable($fieldItem) ? call_user_func($fieldItem) : $fieldItem;
        }, $this->fields[$path] ?? $this->fields);

        $fields = [];
        foreach ($rawFields as $rawField) {
            if (is_array($rawField)) $fields = array_merge($fields, $rawField);
            else $fields[] = $rawField;
        }

        return $fields;
    }

    public function getCasts()
    {
        return $this->casts;
    }

    public function getSetting($settingKey, $default = null)
    {
        if ($cached = $this->getCached($settingKey)) return $cached;

        $settingValue = $this->getSettingsModelClass()::getValueForKey($settingKey) ?? $default;

        $this->setCached($settingKey, $settingValue);

        return $settingValue;
    }

    public function getSettings(array $settingKeys = null, array $defaults = [])
    {
        if (!empty($settingKeys)) {
            $cached = $this->getCached($settingKeys);

            $hasMissingKeys = !empty(array_diff($settingKeys, array_keys($cached)));

            if (!$hasMissingKeys) return $cached;

            $settings = $this->getSettingsModelClass()::whereIn('key', $settingKeys)
                ->get()
                ->pluck('value', 'key');

            return collect($settingKeys)->flatMap(function ($settingKey) use ($settings, $defaults) {
                $settingValue = $settings[$settingKey] ?? null;

                if (isset($settingValue)) {
                    $this->setCached($settingKey, $settingValue);
                    return [$settingKey => $settingValue];
                } else {
                    $defaultValue = $defaults[$settingKey] ?? null;
                    return [$settingKey => $defaultValue];
                }
            })->toArray();
        }

        return $this->getSettingsModelClass()::all()
            ->tap(function ($setting) {
                $this->setCached($setting->key, $setting->value);
            })
            ->pluck('value', 'key')
            ->toArray();
    }

    public function setSettingValue($settingKey, $value = null)
    {
        $setting = $this->getSettingsModelClass()::firstOrCreate(['key' => $settingKey]);
        $setting->value = $value;
        $setting->save();

        $this->setCached($settingKey, $setting->value);

        return $setting;
    }

    public abstract function clearCache($keyNames = null);

    public function clearFields()
    {
        $this->fields = [];
        $this->casts = [];

        $this->clearCache();
    }

    protected abstract function getCached($keyNames = null);

    protected abstract function setCached($keyName, $value);

    /**
     * @return class-string<\Outl1ne\NovaSettings\Models\Settings>
     */
    protected function getSettingsModelClass()
    {
        return NovaSettings::getSettingsModel();
    }
}
