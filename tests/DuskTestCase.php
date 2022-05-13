<?php

namespace Outl1ne\NovaSettings\Tests;

use Laravel\Nova\Nova;
use Laravel\Dusk\Browser;
use Laravel\Nova\Fields\Text;
use Outl1ne\NovaSettings\NovaSettings;
use Illuminate\Foundation\Application;

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
        parent::setUp();

        Nova::tools([
            new NovaSettings(),
        ]);

        NovaSettings::addSettingsFields([
            Text::make('Hello Field', 'hello_field'),
        ]);

        tap($this->app->make('config'), function ($config) {
            $config->set('app.url', static::baseServeUrl());
            $config->set('filesystems.disks.public.url', static::baseServeUrl() . '/storage');
        });
    }

    /**
     * Get base path.
     *
     * @return string
     */
    protected function getBasePath()
    {
        return realpath(__DIR__ . '/../vendor/laravel/nova-dusk-suite');
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
            'Outl1ne\NovaSettings\NovaSettingsServiceProvider',
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
        });
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
     * Setup Laravel for the test.
     *
     * @param  callable|null  $callback
     * @return void
     */
    protected function setupLaravel(callable $callback = null)
    {
        $this->artisan('migrate:fresh')->run();
        $this->artisan('db:seed', ['--class' => \Database\Seeders\DatabaseSeeder::class])->run();

        if (is_callable($callback)) {
            $callback($this->app);
        }
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

    protected function captureFailuresFor($browsers)
    {
        $browsers->each(function (Browser $browser, $key) {
            $name = str_replace('\\', '_', get_class($this)) . '_' . $this->getName(false);
            $browser->screenshot('failure-' . $this->getName() . '-' . $key);
        });
    }
}
