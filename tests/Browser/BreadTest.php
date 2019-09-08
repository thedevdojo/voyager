<?php

namespace TCG\Voyager\Tests\Browser;

use Laravel\Dusk\Browser as DuskBrowser;
use TCG\Voyager\Facades\Voyager;

class BreadTest extends TestCase
{
    public function test_can_browse_user_bread()
    {
        $this->create_bread_for_table('users');
        $this->browse(function (DuskBrowser $browser) {
            $browser->visitRoute('voyager.users.index')
                ->assertSee('NoLayoutFoundException');

            // Create Layout
        });
    }

    protected function create_bread_for_table($table)
    {
        Voyager::deleteBread($table);
        $this->browse(function (DuskBrowser $browser) use ($table) {
            $browser->visitRoute('voyager.bread.create', $table)
                ->waitForText(__('voyager::manager.create_layout_first'))
                ->pause(500)
                ->press(__('voyager::generic.save'))
                ->pause(500)
                ->assertSee(__('voyager::manager.bread_saved_successfully', ['name' => $table]))
                ->visitRoute('voyager.bread.index')
                ->assertButtonEnabled('@edit-'.$table);
        });

        // Reload routes
        $this->getEnvironmentSetUp($this->app);
    }
}
