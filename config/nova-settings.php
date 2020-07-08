<?php

return [
    /**
     * Reload the entire page on save. Useful when updating any Nova UI related settings.
     */

    'reload_page_on_save' => false,

    /**
     * We need to know which eloquent model should be used to retrieve your permissions.
     * Of course, it is often just the default model but you may use whatever you like.
     *
     * The model you want to use as a model needs to extend the original model.
     */
    'models' => [
        'settings' => \OptimistDigital\NovaSettings\Models\Settings::class,
    ],
];
