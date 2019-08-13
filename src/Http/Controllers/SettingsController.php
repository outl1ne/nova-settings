<?php

namespace OptimistDigital\NovaSettings\Http\Controllers;

use Illuminate\Routing\Controller;
use Illuminate\Http\Request;
use OptimistDigital\NovaSettings\Models\Settings;
use OptimistDigital\NovaSettings\NovaSettings;
use Laravel\Nova\Contracts\Resolvable;
use Laravel\Nova\Http\Requests\NovaRequest;

class SettingsController extends Controller
{
    public function get(Request $request)
    {
        $fields = collect(NovaSettings::getSettingsFields());

        $fields->whereInstanceOf(Resolvable::class)->each(function (&$field) {
            if (!empty($field->attribute)) {
                $setting = Settings::where('key', $field->attribute)->first();
                $field->resolve([$field->attribute => isset($setting) ? $setting->value : '']);
            }
        });

        return $fields;
    }

    public function save(NovaRequest $request)
    {
        $fields = collect(NovaSettings::getSettingsFields());

        $fields->whereInstanceOf(Resolvable::class)->each(function ($field) use ($request) {
            if (empty($field->attribute)) return;

            $existingRow = Settings::where('key', $field->attribute)->first();

            $tempResource =  new \stdClass;
            $field->fill($request, $tempResource);

            if (isset($existingRow)) {
                $existingRow->update(['value' => $tempResource->{$field->attribute}]);
            } else {
                Settings::create([
                    'key' => $field->attribute,
                    'value' => $tempResource->{$field->attribute},
                ]);
            }
        });

        return response('', 204);
    }
}
