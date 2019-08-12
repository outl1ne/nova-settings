<?php

namespace OptimistDigital\NovaEcommerce\Nova;

use Illuminate\Http\Request;
use Laravel\Nova\Fields\Select;
use Laravel\Nova\Fields\Heading;
use Laravel\Nova\Contracts\Resolvable;
use OptimistDigital\NovaEcommerce\Models\Settings as SettingsModel;
use Laravel\Nova\Fields\Boolean;
use OptimistDigital\NovaEcommerce\Models\Tax;
use OptimistDigital\NovaEcommerce\NovaEcommerce;
use Whitecube\NovaFlexibleContent\Flexible;
use OptimistDigital\MultiselectField\Multiselect;
use OptimistDigital\NovaEcommerce\Models\Product;
use OptimistDigital\NovaEcommerce\Models\Category;
use Laravel\Nova\Http\Requests\NovaRequest;
use Laravel\Nova\Fields\Number;
use OptimistDigital\NovaEcommerce\Currencies;

class Settings
{
    public function resolveFields(Request $request)
    {
        $fields = collect($this->fields($request));

        $fields->whereInstanceOf(Resolvable::class)->each(function (&$field) {
            if (!empty($field->attribute)) {
                $setting = SettingsModel::where('key', $field->attribute)->first();
                $field->resolve([$field->attribute => isset($setting) ? $setting->value : '']);
            }
        });

        return $fields;
    }

    public function saveFields(NovaRequest $request)
    {
        $fields = collect($this->fields($request));

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
