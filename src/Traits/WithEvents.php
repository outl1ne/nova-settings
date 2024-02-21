<?php

namespace Outl1ne\NovaSettings\Traits;

use Closure;
use Laravel\Nova\Fields\FieldCollection;
use Laravel\Nova\Http\Requests\NovaRequest;
use Outl1ne\NovaSettings\NovaSettings;

trait WithEvents
{
    protected function processWithEvents(
        NovaRequest     $request,
        FieldCollection $fields,
        Closure         $callback,
    ): mixed
    {
        if (is_callable(NovaSettings::$beforeUpdating)) {
            call_user_func_array(NovaSettings::$beforeUpdating, [
                'request' => &$request,
                'fields' => &$fields,
            ]);
        }

        $result = $callback($request, $fields);

        if (is_callable(NovaSettings::$afterUpdated)) {
            call_user_func_array(NovaSettings::$afterUpdated, [
                'request' => &$request,
                'result' => &$result,
            ]);
        }

        return $result;
    }
}