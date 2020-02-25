<?php

namespace OptimistDigital\NovaSettings;

use Laravel\Nova\Nova;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;
use OptimistDigital\NovaSettings\Http\Middleware\Authorize;

class ToolServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        $this->translations();

        $this->loadViewsFrom(__DIR__ . '/../resources/views', 'nova-settings');

        $this->registerRoutes();

        if ($this->app->runningInConsole()) {
            // Publish migrations
            $this->publishes([
                __DIR__ . '/../database/migrations' => database_path('migrations'),
            ], 'migrations');
        }
    }

    protected function registerRoutes()
    {
        if ($this->app->routesAreCached()) return;

        Route::middleware(['nova', Authorize::class])
            ->group(__DIR__ . '/../routes/api.php');
    }

    protected function translations()
    {
        if ($this->app->runningInConsole()) {
            $this->publishes([__DIR__ . '/../resources/lang' => resource_path('lang/vendor/nova-settings')], 'translations');
        } else if (method_exists('Nova', 'translations')) {
            $locale = app()->getLocale();
            $fallbackLocale = config('app.fallback_locale');

            if ($this->attemptToLoadTranslations($locale, 'project')) return;
            if ($this->attemptToLoadTranslations($locale, 'local')) return;
            if ($this->attemptToLoadTranslations($fallbackLocale, 'project')) return;
            if ($this->attemptToLoadTranslations($fallbackLocale, 'local')) return;
            $this->attemptToLoadTranslations('en', 'local');
        }
    }

    protected function attemptToLoadTranslations($locale, $from)
    {
        $filePath = $from === 'local'
            ? __DIR__ . '/../resources/lang/' . $locale . '.json'
            : resource_path('lang/vendor/nova-settings') . '/' . $locale . '.json';

        $localeFileExists = File::exists($filePath);
        if ($localeFileExists) {
            Nova::translations($filePath);
            return true;
        }
        return false;
    }
}
