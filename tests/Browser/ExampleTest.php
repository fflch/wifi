<?php

namespace Tests\Browser;

use Laravel\Dusk\Browser;
use Tests\DuskTestCase;

class ExampleTest extends DuskTestCase
{
    public function test_home_redirects_to_solicitar(): void
    {
        $this->browse(function (Browser $browser) {
            $browser->visit('/')
                ->screenshot('home-page')
                ->driver->getCurrentURL();
        });
    }
}
