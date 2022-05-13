<?php

namespace Outl1ne\NovaSettings\Tests\Feature;

use Outl1ne\NovaSettings\Models\Settings;
use Outl1ne\NovaSettings\Tests\IntegrationTestCase;

class SettingsHelpersTest extends IntegrationTestCase
{
    public function test_nova_get_setting_works()
    {
        Settings::create(['key' => 'test', 'value' => '555']);

        $this->assertEquals('555', nova_get_setting('test'));
    }

    public function test_nova_get_settings_works()
    {
        Settings::create(['key' => 'test', 'value' => '555']);
        Settings::create(['key' => 'testtwo', 'value' => '123']);

        $this->assertEquals(['test' => '555', 'testtwo' => '123'], nova_get_settings(['test', 'testtwo']));
    }
}
