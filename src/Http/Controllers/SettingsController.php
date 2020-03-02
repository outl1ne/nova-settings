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
use Laravel\Nova\Fields\FieldCollection;
use Illuminate\Support\Facades\Validator;

class SettingsController extends Controller
{
    use ResolvesFields, ConditionallyLoadsAttributes;

    public function get(Request $request)
    {
        $fields = $this->assignToPanels(__('Settings'), $this->availableFields());
        $panels = $this->panelsWithDefaultLabel(__('Settings'), new NovaRequest);

        $addResolveCallback = function (&$field) {
            if (!empty($field->attribute)) {
                $setting = Settings::where('key', $field->attribute)->first();
                $field->resolve([$field->attribute => isset($setting) ? $setting->value : '']);
            }

            if (!empty($field->meta['fields'])) {
                foreach ($field->meta['fields'] as $_field) {
                    $setting = Settings::where('key', $_field->attribute)->first();
                    $_field->resolve([$_field->attribute => isset($setting) ? $setting->value : '']);
                }
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

        // NovaDependencyContainer support
        $fields = $fields->map(function ($field) {
            if (!empty($field->attribute)) return $field;
            if (!empty($field->meta['fields'])) return $field->meta['fields'];
            return null;
        })->filter()->flatten();

        $rules = [];
        foreach ($fields as $field) {
            $fakeResource = new \stdClass;
            $fakeResource->{$field->attribute} = nova_get_setting($field->attribute);
            $field->resolve($fakeResource, $field->attribute); // For nova-translatable support
            $rules = array_merge($rules, $field->getUpdateRules($request));
        }

        Validator::make($request->all(), $rules)->validate();

        $fields->whereInstanceOf(Resolvable::class)->each(function ($field) use ($request) {
            if (empty($field->attribute)) return;
            if ($field->isReadonly(app(NovaRequest::class))) return;

            // For nova-translatable support
            if (!empty($field->meta['translatable']['original_attribute'])) $field->attribute = $field->meta['translatable']['original_attribute'];

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

    public function deleteImage(Request $request, $fieldName)
    {
        $existingRow = Settings::where('key', $fieldName)->first();
        if (isset($existingRow)) $existingRow->update(['value' => null]);
        return response('', 204);
    }

    protected function availableFields()
    {
        return new FieldCollection(($this->filter(NovaSettings::getFields())));
    }

    protected function fields(Request $request)
    {
        return NovaSettings::getFields();
    }
}
