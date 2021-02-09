<?php

namespace OptimistDigital\NovaSettings\Tests;

use Illuminate\Foundation\Application;
use Laravel\Dusk\Browser;
use Orchestra\Testbench\Dusk\Foundation\PackageManifest;

abstract class DuskTestCase extends \Orchestra\Testbench\Dusk\TestCase
{
    /**
     * The base serve host URL to use while testing the application.
     *
     * @var string
     */
    protected static $baseServeHost = '127.0.0.1';

    /**
     * The base serve port to use while testing the application.
     *
     * @var int
     */
    protected static $baseServePort = 8085;

    /**
     * Server specific setup. It may share alot with the main setUp() method, but
     * should exclude things like DB migrations so we don't end up wiping the
     * DB content mid test. Using this method means we can be explicit.
     *
     * @return void
     */
    protected function setUpDuskServer(): void
    {
        parent::setUpDuskServer();

        tap($this->app->make('config'), function ($config) {
            $config->set('app.url', static::baseServeUrl());
            $config->set('filesystems.disks.public.url', static::baseServeUrl().'/storage');
        });
    }

    /**
     * Get base path.
     *
     * @return string
     */
    protected function getBasePath()
    {
        return realpath(__DIR__.'/../vendor/laravel/nova-dusk-suite');
    }

    /**
     * Get package providers.
     *
     * @param  \Illuminate\Foundation\Application  $app
     *
     * @return array
     */
    protected function getPackageProviders($app)
    {
        return [
            'Fideloper\Proxy\TrustedProxyServiceProvider',
            'Laravel\Nova\NovaCoreServiceProvider',
            'Carbon\Laravel\ServiceProvider',
        ];
    }

    /**
     * Get application aliases.
     *
     * @param  \Illuminate\Foundation\Application  $app
     *
     * @return array
     */
    protected function getApplicationAliases($app)
    {
        return $app['config']['app.aliases'];
    }

    /**
     * Get application providers.
     *
     * @param  \Illuminate\Foundation\Application  $app
     *
     * @return array
     */
    protected function getApplicationProviders($app)
    {
        return $app['config']['app.providers'];
    }

    /**
     * Resolve application implementation.
     *
     * @return \Illuminate\Foundation\Application
     */
    protected function resolveApplication()
    {
        return tap(new Application($this->getBasePath()), function ($app) {
            $app->detectEnvironment(function () {
                return 'testing';
            });

            PackageManifest::swap($app, $this);
        });
    }

    /**
     * Resolve application core configuration implementation.
     *
     * @param  \Illuminate\Foundation\Application  $app
     *
     * @return void
     */
    protected function resolveApplicationConfiguration($app)
    {
        $app->make('Illuminate\Foundation\Bootstrap\LoadEnvironmentVariables')->bootstrap($app);

        parent::resolveApplicationConfiguration($app);
    }

    /**
     * Resolve application Console Kernel implementation.
     *
     * @param  \Illuminate\Foundation\Application  $app
     *
     * @return void
     */
    protected function resolveApplicationConsoleKernel($app)
    {
        $app->singleton('Illuminate\Contracts\Console\Kernel', 'App\Console\Kernel');
    }

    /**
     * Resolve application HTTP Kernel implementation.
     *
     * @param  \Illuminate\Foundation\Application  $app
     *
     * @return void
     */
    protected function resolveApplicationHttpKernel($app)
    {
        $app->singleton('Illuminate\Contracts\Http\Kernel', 'App\Http\Kernel');
    }

    /**
     * Resolve application HTTP exception handler.
     *
     * @param  \Illuminate\Foundation\Application  $app
     *
     * @return void
     */
    protected function resolveApplicationExceptionHandler($app)
    {
        $app->singleton('Illuminate\Contracts\Debug\ExceptionHandler', 'App\Exceptions\Handler');
    }

    /**
     * Define database migrations.
     *
     * @return void
     */
    protected function defineDatabaseMigrations()
    {
        $this->artisan('migrate:fresh', [
            '--seed' => true,
        ])->run();
    }

    /**
     * Run the given callback with searchable functionality enabled.
     *
     * @param  callable  $callback
     * @return void
     */
    protected function whileSearchable(callable $callback)
    {
        touch(base_path('.searchable'));

        try {
            $callback();
        } finally {
            @unlink(base_path('.searchable'));
        }
    }

    /**
     * Run the given callback with inline-create functionality enabled.
     *
     * @param  callable  $callback
     * @return void
     */
    protected function whileInlineCreate(callable $callback)
    {
        touch(base_path('.inline-create'));

        try {
            $callback();
        } finally {
            @unlink(base_path('.inline-create'));
        }
    }

    /**
     * @param  callable  $callback
     * @return void
     */
    protected function whileIndexQueryAscOrder(callable $callback)
    {
        touch(base_path('.index-query-asc-order'));

        try {
            $callback();
        } finally {
            @unlink(base_path('.index-query-asc-order'));
        }
    }

    /**
     * Create a new Browser instance.
     *
     * @param  \Facebook\WebDriver\Remote\RemoteWebDriver  $driver
     * @return \Laravel\Dusk\Browser
     */
    protected function newBrowser($driver)
    {
        return tap(new Browser($driver), function ($browser) {
            $browser->resize(env('DUSK_WIDTH'), env('DUSK_HEIGHT'));
        });
    }
}