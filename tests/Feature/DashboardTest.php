<?php

namespace TCG\Voyager\Tests\Feature;

use Illuminate\Support\Facades\Auth;
use TCG\Voyager\Tests\TestCase;
use TCG\Voyager\Facades\Voyager;

class DashboardTest extends TestCase
{
    public function setUp()
    {
        parent::setUp();

        $this->install();
    }

    /**
     * Test Dashboard Widgets.
     *
     * This test will make sure the configured widgets are being shown on
     * the dashboard page.
     */
    public function testWidgetsAreBeingShownOnDashboardPage()
    {
        // We must first login and visit the dashboard page.
        Auth::loginUsingId(1);

        $this->visit(route('voyager.dashboard'))
            ->see(__('voyager::generic.dashboard'));

        // Test UserDimmer widget
        $this->see(trans_choice('voyager::dimmer.user', 1))
             ->click(__('voyager::dimmer.user_link_text'))
             ->seePageIs(route('voyager.users.index'))
             ->click(__('voyager::generic.dashboard'))
             ->seePageIs(route('voyager.dashboard'));

        // Test PostDimmer widget
        $this->see(trans_choice('voyager::dimmer.post', 4))
             ->click(__('voyager::dimmer.post_link_text'))
             ->seePageIs(route('voyager.posts.index'))
             ->click(__('voyager::generic.dashboard'))
             ->seePageIs(route('voyager.dashboard'));

        // Test PageDimmer widget
        $this->see(trans_choice('voyager::dimmer.page', 1))
             ->click(__('voyager::dimmer.page_link_text'))
             ->seePageIs(route('voyager.pages.index'))
             ->click(__('voyager::generic.dashboard'))
             ->seePageIs(route('voyager.dashboard'))
             ->see(__('voyager::generic.dashboard'));
    }

    /**
     * Test Accessible Dashboard Widgets.
     *
     * This test will make sure the user will only see widgets that he should
     * see.
     */
    public function testOnlyShouldDisplayWidgetsAreBeingShownOnDashboardPage()
    {
        // We must first login and visit the dashboard page.
        $user = \Auth::loginUsingId(1);

        // Remove `browse_users` permission
        $user->role->permissions()->detach(
            $user->role->permissions()->where('key', 'browse_users')->first()
        );
        // Remove `browse_pages` permission
        $user->role->permissions()->detach(
            $user->role->permissions()->where('key', 'browse_pages')->first()
        );

        $this->visit(route('voyager.dashboard'))
            ->see(__('voyager::generic.dashboard'));

        // Test UserDimmer widget
        $this->dontSee('<h4>1 '.trans_choice('voyager::dimmer.user', 1).'</h4>')
             ->dontSee(__('voyager::dimmer.user_link_text'));

        // Test PostDimmer widget
        $this->see(trans_choice('voyager::dimmer.post', 4))
             ->click(__('voyager::dimmer.post_link_text'))
             ->seePageIs(route('voyager.posts.index'))
             ->click(__('voyager::generic.dashboard'))
             ->seePageIs(route('voyager.dashboard'));

        // Test UserDimmer widget
        $this->dontSee('<h4>1 '.trans_choice('voyager::dimmer.page', 1).'</h4>')
             ->dontSee(__('voyager::dimmer.page_link_text'));
    }

    /**
     * Test See Correct Footer Version Number.
     *
     * This test will make sure the footer contains the correct version number.
     */
    public function testSeeingCorrectFooterVersionNumber()
    {
        // We must first login and visit the dashboard page.
        Auth::loginUsingId(1);

        $this->visit(route('voyager.dashboard'))
             ->see(Voyager::getVersion());
    }
}
