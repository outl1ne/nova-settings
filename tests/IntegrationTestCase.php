<?php

namespace Outl1ne\NovaSettings\Tests;

use Laravel\Nova\Nova;
use Laravel\Nova\NovaServiceProvider;
use Illuminate\Support\Facades\Route;
use Outl1ne\NovaSettings\NovaSettings;
use Orchestra\Testbench\TestCase as Orchestra;
use Outl1ne\NovaSettings\NovaSettingsServiceProvider;

abstract class IntegrationTestCase extends Orchestra
{
    public function setUp(): void
    {
        parent::setUp();

        NovaSettings::clearFields();
        Route::middlewareGroup('nova', []);
        Nova::$tools = [
            new NovaSettings,
        ];

        $this->setUpDatabase($this->app);
    }

    protected function getPackageProviders($app)
    {
        return [
            NovaServiceProvider::class,
            NovaSettingsServiceProvider::class,
        ];
    }

    protected function setUpDatabase()
    {
        $this->artisan('migrate:fresh');
    }
}
