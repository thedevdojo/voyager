<?php

namespace TCG\Voyager\Tests\Feature;

use TCG\Voyager\Tests\Unit\TestCase;

class DashboardTest extends TestCase
{
    public function test_dashboard()
    {
        $this->get(route('voyager.dashboard'))
             ->assertStatus(200)
             ->assertSeeText('Welcome to Voyager 2.0');
    }
}
