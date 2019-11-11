<?php

namespace OptimistDigital\NovaSettings\Http\Controllers;

use Illuminate\Routing\Controller;
use Illuminate\Http\Request;
use OptimistDigital\NovaSettings\Models\Settings;
use OptimistDigital\NovaSettings\NovaSettings;
use Laravel\Nova\Contracts\Resolvable;
use Laravel\Nova\Http\Requests\NovaRequest;
use Laravel\Nova\ResolvesFields;
use Illuminate\Http\Resources\ConditionallyLoadsAttributes;

class SettingsController extends Controller
{
    use ResolvesFields,
        ConditionallyLoadsAttributes;


    public function get(Request $request)
    {
        $fields = $this->assignToPanels(__('Settings'), $this->availableFields());
        $panels = $this->panelsWithDefaultLabel(__('Settings'), new NovaRequest);

        $addResolveCallback = function (&$field) {
            if (!empty($field->attribute)) {
                $setting = Settings::where('key', $field->attribute)->first();
                $field->resolve([$field->attribute => isset($setting) ? $setting->value : '']);
            }
        };

        $fields->each(function (&$field) use ($addResolveCallback) {
            $addResolveCallback($field);
        });

        return response()->json([
            'panels' => $panels,
            'fields' => $fields,
        ], 200);
    }

    public function save(NovaRequest $request)
    {
        $fields = $this->availableFields();

        $fields->whereInstanceOf(Resolvable::class)->each(function ($field) use ($request) {
            if (empty($field->attribute)) return;

            $existingRow = Settings::where('key', $field->attribute)->first();

            $tempResource =  new \stdClass;
            $field->fill($request, $tempResource);

            if (!property_exists($tempResource, $field->attribute)) return;

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

    protected function availableFields()
    {
        return collect($this->filter(NovaSettings::getSettingsFields()));
    }

    protected function fields(Request $request)
    {
        return NovaSettings::getSettingsFields();
    }
}
