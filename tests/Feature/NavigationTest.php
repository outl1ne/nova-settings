<?php

namespace OptimistDigital\NovaSettings\Tests\Feature;

use Laravel\Nova\Fields\Text;
use OptimistDigital\NovaSettings\NovaSettings;
use OptimistDigital\NovaSettings\Tests\IntegrationTestCase;

class NavigationTest extends IntegrationTestCase
{
    public function test_general_navigation_renders_with_no_fields()
    {
        $settingsTool = new NovaSettings;
        $navigationView = $settingsTool->renderNavigation()->render();
        $this->assertStringContainsString('dusk="nova-settings"', $navigationView);
    }

    public function test_general_navigation_renders_with_fields()
    {
        NovaSettings::addSettingsFields([
            Text::make('Test'),
        ]);

        $settingsTool = new NovaSettings;
        $navigationView = $settingsTool->renderNavigation()->render();

        $this->assertStringContainsString('dusk="nova-settings"', $navigationView);
    }

    public function test_multiple_navigation_renders()
    {
        NovaSettings::addSettingsFields([
            Text::make('Test'),
        ]);

        NovaSettings::addSettingsFields([
            Text::make('TestTwo'),
        ], [], 'Other');

        $settingsTool = new NovaSettings;
        $navigationView = $settingsTool->renderNavigation()->render();

        $this->assertStringContainsString('dusk="nova-settings-general"', $navigationView);
        $this->assertStringContainsString('dusk="nova-settings-other"', $navigationView);
    }
}
