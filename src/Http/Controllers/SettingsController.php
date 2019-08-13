<?php

namespace OptimistDigital\NovaSettings\Http\Controllers;

use Illuminate\Routing\Controller;
use Illuminate\Http\Request;
use OptimistDigital\NovaSettings\NovaSettings;

class SettingsController extends Controller
{
    public function get(Request $request)
    {
        $fields = collect(NovaSettings::getSettingsFields());

        $fields->whereInstanceOf(Resolvable::class)->each(function (&$field) {
            if (!empty($field->attribute)) {
                $setting = SettingsModel::where('key', $field->attribute)->first();
                $field->resolve([$field->attribute => isset($setting) ? $setting->value : '']);
            }
        });

        return $fields;
    }

    public function save(Request $request)
    {
        $fields = collect(NovaSettings::getSettingsFields());

        $fields->whereInstanceOf(Resolvable::class)->each(function ($field) use ($request) {
            if (empty($field->attribute)) return;

            $existingRow = SettingsModel::where('key', $field->attribute)->first();

            $tempResource =  new \stdClass;
            $field->fill($request, $tempResource);

            if (isset($existingRow)) {
                $existingRow->update(['value' => $tempResource->{$field->attribute}]);
            } else {
                SettingsModel::create([
                    'key' => $field->attribute,
                    'value' => $tempResource->{$field->attribute},
                ]);
            }
        });
    }
}
