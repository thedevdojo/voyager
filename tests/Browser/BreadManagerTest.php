<?php

namespace TCG\Voyager\Tests\Browser;

use Laravel\Dusk\Browser as DuskBrowser;
use TCG\Voyager\Facades\Bread;

class BreadManagerTest extends TestCase
{
    public function test_can_browse_breads()
    {
        Bread::deleteBread('users');
        $this->browse(function (DuskBrowser $browser) {
            $browser->visitRoute('voyager.bread.index')
                ->waitForText('users')
                ->assertButtonEnabled('@add-users');
        });
    }

    public function test_can_create_user_bread()
    {
        Bread::deleteBread('users');
        $this->browse(function (DuskBrowser $browser) {
            $browser->visitRoute('voyager.bread.create', 'users')
                ->waitForText(__('voyager::manager.create_layout_first'))
                ->pause(500)
                ->press(__('voyager::generic.save'))
                ->pause(500)
                ->assertSee(__('voyager::manager.bread_saved_successfully', ['name' => 'users']))
                ->visitRoute('voyager.bread.index')
                ->assertButtonEnabled('@edit-users');
        });

        Bread::clearBreads(); // Clear BREADs and reload them because phpunit and the browser are different clients
        $this->assertNotNull(Bread::getBread('users'));
        $this->assertNull(Bread::getBread('not_existing'));
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

    public function test_can_delete_user_bread()
    {
        $this->browse(function (DuskBrowser $browser) {
            $browser->visitRoute('voyager.bread.index')
                ->pause(500)
                ->assertButtonEnabled('@edit-users')
                ->press('@delete-users')
                ->waitForText(__('voyager::generic.yes'))
                ->press(__('voyager::generic.yes'))
                ->visitRoute('voyager.bread.index')
                ->assertButtonEnabled('@add-users');
        });
    }
}
