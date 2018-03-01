<?php

namespace TCG\Voyager\Tests;

use Illuminate\Support\Facades\Auth;
use TCG\Voyager\Facades\Voyager;

class DashboardTest extends TestCase
{
    protected $withDummy = true;

    public function setUp()
    {
        parent::setUp();

        $this->install();
    }

    public function testWeHaveAccessToTheMainSections()
    {
        // We must first login and visit the dashboard page.
        Auth::loginUsingId(1);

        $this->visit(route('voyager.dashboard'));

        $this->see(__('voyager.generic.dashboard'));

        // We can see number of Users.
        $this->see(trans_choice('voyager.dimmer.user', 1));

        // list them.
        $this->click(__('voyager.dimmer.user_link_text'));
        $this->seePageIs(route('voyager.users.index'));

        // and return to dashboard from there.
        $this->click(__('voyager.generic.dashboard'));
        $this->seePageIs(route('voyager.dashboard'));

        // We can see number of posts.
        $this->see(trans_choice('voyager.dimmer.post', 4));

        // list them.
        $this->click(__('voyager.dimmer.post_link_text'));
        $this->seePageIs(route('voyager.posts.index'));

        // and return to dashboard from there.
        $this->click(__('voyager.generic.dashboard'));
        $this->seePageIs(route('voyager.dashboard'));

        // We can see number of Pages.
        $this->see(trans_choice('voyager.dimmer.page', 1));

        // list them.
        $this->click(__('voyager.dimmer.page_link_text'));
        $this->seePageIs(route('voyager.pages.index'));

        // and return to Dashboard from there.
        $this->click(__('voyager.generic.dashboard'));
        $this->seePageIs(route('voyager.dashboard'));
        $this->see(__('voyager.generic.dashboard'));
    }

    public function testSeeingCorrectFooterVersionNumber()
    {
        // We must first login and visit the dashboard page.
        Auth::loginUsingId(1);

        $this->visit(route('voyager.dashboard'));

        $this->see(Voyager::getVersion());
    }
}
