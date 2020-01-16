<?php

namespace TCG\Voyager\Tests\Unit;

use Illuminate\Support\Facades\Config;
use TCG\Voyager\Facades\Voyager;
use TCG\Voyager\Tests\TestCase;

class VoyagerTest extends TestCase
{
    /**
     * Dimmers returns an array filled with widget collections.
     *
     * This test will make sure that the dimmers method will give us an array
     * of the collection of the configured widgets.
     */
    public function testDimmersReturnsCollectionOfConfiguredWidgets()
    {
        Config::set('voyager.dashboard.widgets', [
            'TCG\\Voyager\\Tests\\Stubs\\Widgets\\AccessibleDimmer',
            'TCG\\Voyager\\Tests\\Stubs\\Widgets\\AccessibleDimmer',
        ]);

        $dimmers = Voyager::dimmers();

        $this->assertEquals(2, $dimmers[0]->count());
    }

    /**
     * Dimmers returns an array filled with widget collections.
     *
     * This test will make sure that the dimmers method will give us a
     * collection of the configured widgets which also should be displayed.
     */
    public function testDimmersReturnsCollectionOfConfiguredWidgetsWhichShouldBeDisplayed()
    {
        Config::set('voyager.dashboard.widgets', [
            'TCG\\Voyager\\Tests\\Stubs\\Widgets\\AccessibleDimmer',
            'TCG\\Voyager\\Tests\\Stubs\\Widgets\\InAccessibleDimmer',
            'TCG\\Voyager\\Tests\\Stubs\\Widgets\\InAccessibleDimmer',
        ]);

        $dimmers = Voyager::dimmers();

        $this->assertEquals(1, $dimmers[0]->count());
    }

    /**
     * Dimmers returns an array filled with widget collections.
     *
     * Tests that we build N / 3 (rounded up) widget collections where
     * N is the total amount of widgets set in configuration
     */
    public function testCreateEnoughDimmerCollectionsToContainAllAvailableDimmers()
    {
        Config::set('voyager.dashboard.widgets', [
            'TCG\\Voyager\\Tests\\Stubs\\Widgets\\AccessibleDimmer',
            'TCG\\Voyager\\Tests\\Stubs\\Widgets\\AccessibleDimmer',
            'TCG\\Voyager\\Tests\\Stubs\\Widgets\\AccessibleDimmer',
            'TCG\\Voyager\\Tests\\Stubs\\Widgets\\AccessibleDimmer',
            'TCG\\Voyager\\Tests\\Stubs\\Widgets\\AccessibleDimmer',
        ]);

        $dimmers = Voyager::dimmers();

        $this->assertEquals(2, count($dimmers));
        $this->assertEquals(3, $dimmers[0]->count());
        $this->assertEquals(2, $dimmers[1]->count());
    }
}
