<?php

namespace OptimistDigital\NovaSettings\Models;

use Illuminate\Database\Eloquent\Model;
use OptimistDigital\NovaSettings\NovaSettings;

class Settings extends Model
{
    protected $primaryKey = 'key';
    public $incrementing = false;
    public $timestamps = false;
    public $fillable = ['key', 'value'];

    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);
        $this->setTable(NovaSettings::getSettingsTableName());
    }

    protected static function booted()
    {
        static::updated(function ($setting) {
            NovaSettings::getStore()->clearCache($setting->key);
        });
    }

    public function setValueAttribute($value)
    {
        $this->attributes['value'] = is_array($value) ? json_encode($value) : $value;
    }

    public function getValueAttribute($value)
    {
        $originalCasts = $this->casts;
        $this->casts =  NovaSettings::getCasts();

        if ($this->hasCast($this->key)) {
            $value = $this->castAttribute($this->key, $value);
        }

        $this->casts = $originalCasts;

        return $value;
    }

    public static function getValueForKey($key)
    {
        $setting = static::where('key', $key)->get()->first();
        return isset($setting) ? $setting->value : null;
    }
}
