<?php

namespace Outl1ne\NovaSettings\Http\Controllers;

use Laravel\Nova\Panel;
use Illuminate\Http\Request;
use Laravel\Nova\ResolvesFields;
use Illuminate\Routing\Controller;
use Laravel\Nova\Contracts\Resolvable;
use Outl1ne\NovaSettings\NovaSettings;
use Illuminate\Support\Facades\Storage;
use Laravel\Nova\Fields\FieldCollection;
use Illuminate\Support\Facades\Validator;
use Laravel\Nova\Http\Requests\NovaRequest;
use Illuminate\Http\Resources\ConditionallyLoadsAttributes;

class SettingsController extends Controller
{
    use ResolvesFields, ConditionallyLoadsAttributes;

    public function get(Request $request)
    {
        if (!NovaSettings::canSeeSettings()) return $this->unauthorized();

        $path = $request->get('path', 'general');
        $label = NovaSettings::getPageName($path);
        $fields = $this->assignToPanels($label, $this->availableFields($path));
        $panels = $this->panelsWithDefaultLabel($label, app(NovaRequest::class));

        $addResolveCallback = function (&$field) {
            if (!empty($field->attribute)) {
                $setting = NovaSettings::getSettingsModel()::firstOrNew(['key' => $field->attribute]);
                $fakeResource = $this->makeFakeResource($field->attribute, isset($setting) ? $setting->value : '');
                $field->resolve($fakeResource);
            }

            if (!empty($field->meta['fields'])) {
                foreach ($field->meta['fields'] as $_field) {
                    $setting = NovaSettings::getSettingsModel()::where('key', $_field->attribute)->first();
                    $fakeResource = $this->makeFakeResource($_field->attribute, isset($setting) ? $setting->value : null);
                    $_field->resolve($fakeResource);
                }
            }
        };

        $fields->each(function (&$field) use ($addResolveCallback) {
            $addResolveCallback($field);
        });

        return response()->json([
            'panels' => $panels,
            'fields' => $fields,
            'authorizations' => NovaSettings::getAuthorizations(),
        ], 200);
    }

    public function save(NovaRequest $request)
    {
        if (!NovaSettings::getAuthorizations('authorizedToUpdate')) return $this->unauthorized();

        $fields = $this->availableFields($request->get('path', 'general'));

        // NovaDependencyContainer support
        $fields = $fields->map(function ($field) {
            if (!empty($field->attribute)) return $field;
            if (!empty($field->meta['fields'])) return $field->meta['fields'];
            return null;
        })->filter()->flatten();

        $rules = [];
        foreach ($fields as $field) {
            $fakeResource = $this->makeFakeResource($field->attribute, nova_get_setting($field->attribute));
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

            $tempResource = new \Laravel\Nova\Support\Fluent;
            $field->fill($request, $tempResource);

            if (!array_key_exists($field->attribute, $tempResource->getAttributes())) return;

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
        if (!NovaSettings::getAuthorizations('authorizedToUpdate')) return $this->unauthorized();

        $existingRow = NovaSettings::getSettingsModel()::where('key', $fieldName)->first();
        if (isset($existingRow)) {
            $field = $this->findField(collect(NovaSettings::getFields($pathName)), $fieldName);

            // Delete file if exists
            if (isset($field) && $field instanceof \Laravel\Nova\Fields\File) {
                $disk = $field->getStorageDisk();
                Storage::disk($disk)->delete($existingRow->value);
            }

            $existingRow->value = null;
            $existingRow->save();
        }

        return response('', 204);
    }

    protected function findField($fields, $fieldName)
    {
        if (empty($fields)) return null;

        $field = $fields->firstWhere('attribute', $fieldName);

        // Target field might be inside container field
        if (empty($field)) {
            foreach ($fields as $value) {
                if ($value instanceof \Laravel\Nova\Panel) {
                    $field = $this->findField(collect($value->data), $fieldName);
                    if (!empty($field)) return $field;
                }

                if (class_exists('\Eminiarts\Tabs\Tabs') && $value instanceof \Eminiarts\Tabs\Tabs) {
                    $field = $this->findField(collect($value->data, $fieldName));
                    if (!empty($field)) return $field;
                }
            }
        }

        return $field;
    }

    protected function availableFields($path = 'general')
    {
        return (new FieldCollection($this->filter(NovaSettings::getFields($path))))->authorized(request());
    }

    protected function fields(Request $request, $path = 'general')
    {
        return NovaSettings::getFields($path);
    }

    protected function makeFakeResource(string $fieldName, $fieldValue)
    {
        $fakeResource = new \Laravel\Nova\Support\Fluent;
        $fakeResource->{$fieldName} = $fieldValue;
        return $fakeResource;
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

    protected function unauthorized()
    {
        return response()->json(['error' => 'Unauthorized'], 403);
    }

    protected function assignToPanels($label, FieldCollection $fields)
    {
        return $fields->map(function ($field) use ($label) {
            if (!$field->panel) $field->panel = $label;
            return $field;
        });
    }
}
