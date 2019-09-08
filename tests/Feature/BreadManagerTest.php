<?php

namespace TCG\Voyager\Tests\Feature;

use TCG\Voyager\Tests\Unit\TestCase;

class BreadManagerTest extends TestCase
{
    public function test_browse_bread_manager()
    {
        $this->get(route('voyager.bread.index'))
             ->assertStatus(200);
    }

    public function test_display_create_user_bread()
    {
        $this->get(route('voyager.bread.create', 'users'))
             ->assertStatus(200);
    }

    public function test_create_user_bread()
    {
        $this->post(route('voyager.bread.store', 'users'))
             ->assertStatus(200);
    }
}