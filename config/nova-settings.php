<?php

return [

    /**
     * Set a name for the settings table
     */
    'table' => 'nova_settings',

    /**
     * URL path of settings page
     */
    'base_path' => 'nova-settings',

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
        'settings' => \Outl1ne\NovaSettings\Models\Settings::class,
    ],

    /**
     * Show the sidebar menu
     */
    'show_in_sidebar' => true,

    /*
    |--------------------------------------------------------------------------
    | Cache settings
    |--------------------------------------------------------------------------
    |
    | Here you may specify which of the cache connection should be used to
    | cache the settings. `:memory:` is the default which is a simple
    | in-memory cache through a singleton service class property.
    | `null` will disable caching.
    |
    */
    'cache' => [
        'store' => env('NOVA_SETTINGS_CACHE_DRIVER', ':memory:'),

        'prefix' => 'nova-settings:',
    ],
];
