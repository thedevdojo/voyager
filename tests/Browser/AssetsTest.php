<?php

namespace TCG\Voyager\Tests\Browser;

use Laravel\Dusk\Browser as DuskBrowser;
use TCG\Voyager\Facades\Voyager;

class AssetsTest extends TestCase
{
    public function test_asset_css_loaded()
    {
        $this->browse(function (DuskBrowser $browser) {
            $browser->visit(Voyager::assetUrl('css/voyager.css'))
                ->assertDontSee('Not found');
        });
    }

    public function test_asset_js_loaded()
    {
        $this->browse(function (DuskBrowser $browser) {
            $browser->visit(Voyager::assetUrl('js/voyager.js'))
                ->assertDontSee('Not found');
        });
    }

    public function test_asset_not_loaded()
    {
        $this->browse(function (DuskBrowser $browser) {
            $browser->visit(Voyager::assetUrl('some/wrong.file'))
                ->assertSee('Not found');
        });
    }

    public function test_asset_cannot_load_file_outside()
    {
        $this->browse(function (DuskBrowser $browser) {
            $browser->visit(Voyager::assetUrl('../../../../../../.env'))
                ->assertSee('Not found');
        });
    }
}
