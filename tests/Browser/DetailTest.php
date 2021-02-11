<?php

namespace OptimistDigital\NovaSettings\Tests\Browser;

use App\Models\User;
use Laravel\Dusk\Browser;
use Laravel\Nova\Fields\Text;
use OptimistDigital\NovaSettings\NovaSettings;
use OptimistDigital\NovaSettings\Tests\DuskTestCase;

class DetailTest extends DuskTestCase
{
    public function test_settings_appears_in_sidebar_with_no_fields()
    {
        $this->browse(function (Browser $browser) {
            $browser->loginAs(User::find(1))
                ->visit('nova')
                ->assertSee('Settings');

            $browser->blank();
        });
    }

    public function test_settings_appears_in_sidebar_with_fields()
    {
        $this->browse(function (Browser $browser) {
            $browser->loginAs(User::find(1))
                ->visit('nova')
                ->assertSee('Settings');

            $browser->blank();
        });
    }
}
