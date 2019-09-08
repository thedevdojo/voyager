<?php

namespace TCG\Voyager\Tests\Browser;

use Laravel\Dusk\Browser as DuskBrowser;
use TCG\Voyager\Facades\Voyager;

class BreadManagerTest extends BrowserTestCase
{
    public function test_can_browse_breads()
    {
        $this->browse(function (DuskBrowser $browser) {
            $browser->visitRoute('voyager.bread.index')
                ->waitForText('TABLE')
                ->assertSee('users')
                ->assertSee('failed_jobs')
                ->assertSee('migrations')
                ->assertSee('password_reset');
                // TODO: remove once a hide-tables feature is enabled
        });
    }

    public function test_can_create_user_bread()
    {
        $this->browse(function (DuskBrowser $browser) {
            $browser->visitRoute('voyager.bread.create', 'users')
                ->waitForText('Please create a Layout first')
                ->pause(500)
                ->press('Save')
                ->pause(500)
                ->assertSee('Bread "users" saved successfully!');
        });

        $this->assertNotNull(Voyager::getBread('users'));
        $this->assertNull(Voyager::getBread('not_existing'));
    }

    public function test_cannot_create_bread_for_non_existing_table()
    {
        $this->browse(function (DuskBrowser $browser) {
            $browser->visitRoute('voyager.bread.create', 'not_existing')
                ->assertSee('TableNotFoundException');
        });
    }

    public function test_cannot_edit_not_existing_bread()
    {
        $this->browse(function (DuskBrowser $browser) {
            $browser->visitRoute('voyager.bread.edit', 'not_existing')
                ->assertSee('BreadNotFoundException');
        });
    }
}
