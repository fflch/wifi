<?php

namespace Tests\Browser;

use Laravel\Dusk\Browser;
use App\Models\User;
use Tests\DuskTestCase;

class DebugTest extends DuskTestCase
{
    public function test_dashboard(): void
    {
        $user = User::factory()->create(["codpes" => 1]);

        $this->browse(function (Browser $browser) use ($user) {
            $browser->loginAs($user)
                ->visit("/dashboard");
            
            file_put_contents("/tmp/page_body.txt", $browser->driver->findElement(\Facebook\WebDriver\WebDriverBy::tagName("body"))->getText());
        });
    }
}
