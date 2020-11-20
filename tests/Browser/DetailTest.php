<?php

namespace OptimistDigital\NovaSettings\Tests\Browser;

use App\Models\User;
use Laravel\Dusk\Browser;
use OptimistDigital\NovaSettings\Tests\DuskTestCase;

class DetailTest extends DuskTestCase
{
    /**
     * @test
     */
    public function sample_test()
    {
        $this->setupLaravel();

        $this->browse(function (Browser $browser) {
            $browser->loginAs(User::find(1))
                ->visit('nova')
                ->assertSee('Dashboard');

            $browser->blank();
        });
    }
}
