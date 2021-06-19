<?php

namespace OptimistDigital\NovaSettings\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Laravel\Nova\ResolvesFields;
use Illuminate\Routing\Controller;
use Laravel\Nova\Contracts\Resolvable;
use Laravel\Nova\Fields\FieldCollection;
use Illuminate\Support\Facades\Validator;
use Laravel\Nova\Http\Requests\NovaRequest;
use OptimistDigital\NovaSettings\NovaSettings;
use Illuminate\Http\Resources\ConditionallyLoadsAttributes;
use Laravel\Nova\Panel;
use Illuminate\Support\Str;

class SettingsController extends Controller
{
    use ResolvesFields, ConditionallyLoadsAttributes;

    public function get(Request $request)
    {
        $path = $request->get('path', 'general');
        $label = ($path === 'general') ?
            __('novaSettings.navigationItemTitle') :
            Str::title(str_replace('-', ' ', $path));
        $fields = $this->assignToPanels($label, $this->availableFields($path));
        $panels = $this->panelsWithDefaultLabel($label , app(NovaRequest::class));

        $addResolveCallback = function (&$field) {
            if (!empty($field->attribute)) {
                $setting = NovaSettings::getSettingsModel()::findOrNew($field->attribute);
                $field->resolve([$field->attribute => isset($setting) ? $setting->value : '']);
            }

            if (!empty($field->meta['fields'])) {
                foreach ($field->meta['fields'] as $_field) {
                    $setting = NovaSettings::getSettingsModel()::where('key', $_field->attribute)->first();
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
        $fields = $this->availableFields($request->get('path', 'general'));

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
            $settingsClass = NovaSettings::getSettingsModel();

            // For nova-translatable support
            if (!empty($field->meta['translatable']['original_attribute'])) $field->attribute = $field->meta['translatable']['original_attribute'];

            $existingRow = $settingsClass::where('key', $field->attribute)->first();

            $tempResource =  new \stdClass;
            $field->fill($request, $tempResource);

            if (!property_exists($tempResource, $field->attribute)) return;

            if (isset($existingRow)) {
                $existingRow->value = $tempResource->{$field->attribute};
                $existingRow->save();
            } else {
                $newRow = new $settingsClass;
                $newRow->key = $field->attribute;
                $newRow->value = $tempResource->{$field->attribute};
                $newRow->save();
            }
        });

        if (config('nova-settings.reload_page_on_save', false) === true) {
            return response()->json(['reload' => true]);
        }

        return response('', 204);
    }

    public function deleteImage(Request $request, $pathName, $fieldName)
    {
        $existingRow = NovaSettings::getSettingsModel()::where('key', $fieldName)->first();
        if (isset($existingRow)) {
            $existingRow->value = null;
            $existingRow->save();
        }
        return response('', 204);
    }

    protected function availableFields($path = 'general')
    {
        return (new FieldCollection(($this->filter(NovaSettings::getFields($path)))))->authorized(request());
    }

    protected function fields(Request $request, $path = 'general')
    {
        return NovaSettings::getFields($path);
    }

    /**
     * Return the panels for this request with the default label.
     *
     * @param  string  $label
     * @param  \Laravel\Nova\Http\Requests\NovaRequest  $request
     * @return array
     */
    protected function panelsWithDefaultLabel($label, NovaRequest $request)
    {
        $method = $this->fieldsMethod($request);

        return with(
            collect(array_values($this->{$method}($request, $request->get('path', 'general'))))->whereInstanceOf(Panel::class)->unique('name')->values(),
            function ($panels) use ($label) {
                return $panels->when($panels->where('name', $label)->isEmpty(), function ($panels) use ($label) {
                    return $panels->prepend((new Panel($label))->withToolbar());
                })->all();
            }
        );
    }
}
