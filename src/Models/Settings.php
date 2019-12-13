<?php

namespace OptimistDigital\NovaSettings\Models;

use Illuminate\Database\Eloquent\Model;
use OptimistDigital\NovaSettings\NovaSettings;

class Settings extends Model
{
    protected $primaryKey = 'key';
    protected $table = 'nova_settings';
    public $incrementing = false;
    public $timestamps = false;
    public $fillable = ['key', 'value'];

    public function getValueAttribute($value)
    {
        $customFormatter = NovaSettings::getCustomFormatter();

        if (isset($customFormatter)) return call_user_func($customFormatter, $this->key, $value);

        return $value;
    }

    public static function getValueForKey($key)
    {
        $setting = static::where('key', $key)->get()->first();
        return isset($setting) ? $setting->value : null;
    }
}
