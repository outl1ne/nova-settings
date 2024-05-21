<?php

namespace Outl1ne\NovaSettings;

use Laravel\Nova\Nova;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;
use Laravel\Nova\Http\Middleware\Authenticate;
use Outl1ne\NovaSettings\Http\Middleware\Authorize;
use Outl1ne\NovaTranslationsLoader\LoadsNovaTranslations;
use Outl1ne\NovaSettings\Http\Middleware\SettingsPathExists;

use function array_keys;
use function config;
use function config_path;
use function database_path;
use function in_array;
use function inertia;
use function is_array;

class NovaSettingsServiceProvider extends ServiceProvider
{
    use LoadsNovaTranslations;

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        $this->loadMigrationsFrom(__DIR__ . '/../database/migrations');
        $this->loadTranslations(__DIR__ . '/../lang', 'nova-settings', true);

        if ($this->app->runningInConsole()) {
            // Publish migrations
            $this->publishes([
                __DIR__ . '/../database/migrations' => database_path('migrations'),
            ], 'migrations');

            // Publish config
            $this->publishes([
                __DIR__ . '/../config/' => config_path(),
            ], 'config');
        }
    }

    public function register()
    {
        $this->registerRoutes();

        $this->mergeConfigFrom(
            __DIR__ . '/../config/nova-settings.php',
            'nova-settings'
        );

        $this->registerSettingsStore();
    }

    protected function registerSettingsStore()
    {
        $caching = config('nova-settings.cache.store');

        if (is_array(config('cache.stores')) && in_array($caching, array_keys(config('cache.stores')))) {
            $this->app->singleton(NovaSettingsStore::class, function () {
                return new NovaSettingsCacheStore();
            });
        } else if ($caching === ':memory:') {
            $this->app->singleton(NovaSettingsStore::class, function () {
                return new NovaSettingsInMemoryStore();
            });
        } else {
            $this->app->singleton(NovaSettingsStore::class, function () {
                return new NovaSettingsNoCacheStore();
            });
        }
    }

    protected function registerRoutes()
    {
        // Register nova routes
        Nova::router()->group(function ($router) {
            $path = config('nova-settings.base_path', 'nova-settings');

            $router
                ->get("{$path}/{pageId?}", fn ($pageId = 'general') => inertia('NovaSettings', ['basePath' => $path, 'pageId' => $pageId]))
                ->middleware(['nova', Authenticate::class])
                ->domain(config('nova.domain', null));
        });

        if ($this->app->routesAreCached()) return;

        Route::middleware(['nova', Authorize::class, SettingsPathExists::class])
            ->domain(config('nova.domain', null))
            ->group(__DIR__ . '/../routes/api.php');
    }
}
