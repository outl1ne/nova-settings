<?php

namespace OptimistDigital\NovaSettings\Models;

use Illuminate\Database\Eloquent\Model;
use OptimistDigital\NovaSettings\NovaSettings;
use Illuminate\Support\Collection as BaseCollection;

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

    public function setValueAttribute($value)
    {
        $this->attributes['value'] = is_array($value) ? json_encode($value) : $value;
    }

    public function getValueAttribute()
    {
        $value = $this->attributes['value'] ?? null;
        $casts = NovaSettings::getCasts();
        $castType = $casts[$this->key] ?? null;

        if (!$castType) return $value;

        switch ($castType) {
            case 'int':
            case 'integer':
                return (int) $value;
            case 'real':
            case 'float':
            case 'double':
                return $this->fromFloat($value);
            case 'decimal':
                return $this->asDecimal($value, explode(':', $casts[$this->key], 2)[1]);
            case 'string':
                return (string) $value;
            case 'bool':
            case 'boolean':
                return (bool) $value;
            case 'object':
                return $this->fromJson($value, true);
            case 'array':
            case 'json':
                return $this->fromJson($value);
            case 'collection':
                return new BaseCollection($this->fromJson($value));
            case 'date':
                return $this->asDate($value);
            case 'datetime':
            case 'custom_datetime':
                return $this->asDateTime($value);
            case 'timestamp':
                return $this->asTimestamp($value);
        }

        return $value;
    }

    public static function getValueForKey($key)
    {
        $setting = static::where('key', $key)->get()->first();
        return isset($setting) ? $setting->value : null;
    }
}
