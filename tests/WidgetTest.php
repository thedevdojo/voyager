<?php

namespace TCG\Voyager\Tests;

use Arrilot\Widgets\Facade;
use Illuminate\Support\Facades\Auth;

class WidgetTest extends TestCase
{
    public function setUp()
    {
        parent::setUp();

        $this->install();

        Auth::loginUsingId(1);
    }

    public function testWidetsAreRegistered()
    {
        $dimmers = Facade::group('voyager::dimmers');

        $this->assertEquals(3, $dimmers->count());
    }

    public function testWidgetRenders()
    {
        $dimmers = Facade::group('voyager::dimmers');

        $html = "
            <div class=\"panel widget center bgimage\" style=\"margin-bottom:0;overflow:hidden;background-image:url('http://localhost/vendor/tcg/voyager/assets/images/widget-backgrounds/02.png');\">
                <div class=\"dimmer\"></div>
                <div class=\"panel-content\">
                    <i class='voyager-group'></i>
                    <h4>1 user</h4>
                    <p>You have 1 user in your database. Click on button below to view all users.</p>
                    <a href=\"http://localhost/admin/users\" class=\"btn btn-primary\">View all users</a>
                </div>
            </div>
            <hr>
            <div class=\"panel widget center bgimage\" style=\"margin-bottom:0;overflow:hidden;background-image:url('http://localhost/vendor/tcg/voyager/assets/images/widget-backgrounds/03.png');\">
                <div class=\"dimmer\"></div>
                <div class=\"panel-content\">
                    <i class='voyager-group'></i>        <h4>4 posts</h4>
                    <p>You have 4 posts in your database. Click on button below to view all posts.</p>
                    <a href=\"http://localhost/admin/posts\" class=\"btn btn-primary\">View all posts</a>
                </div>
            </div>
            <hr>
            <div class=\"panel widget center bgimage\" style=\"margin-bottom:0;overflow:hidden;background-image:url('http://localhost/vendor/tcg/voyager/assets/images/widget-backgrounds/03.png');\">
                <div class=\"dimmer\"></div>
                <div class=\"panel-content\">
                    <i class='voyager-group'></i>        <h4>1 page</h4>
                    <p>You have 1 page in your database. Click on button below to view all pages.</p>
                    <a href=\"http://localhost/admin/pages\" class=\"btn btn-primary\">View all pages</a>
                </div>
            </div>";

        $replace = function ($html) {
            return preg_replace('/\s+/', '', $html);
        };

        $this->assertEquals(
            $replace($html),
            $replace($dimmers->setSeparator('<hr>')->display())
        );
    }

    public function testRuntimeDisableWidget()
    {
    }

    public function testRuntimeEnableWidget()
    {
    }

    public function testOverwrittingWidgetView()
    {
    }
}
