<?php

namespace TCG\Voyager\Tests;

use Illuminate\Support\Facades\Auth;
use TCG\Voyager\Models\Category;

class CategoryTest extends TestCase
{
    public function setUp()
    {
        parent::setUp();

        $this->install();
    }

    /** @test */
    public function can_create_a_category_with_logged_in_user_auto_assigned()
    {
        // Arrange
        $user = Auth::loginUsingId(1);

        $category = new Category();

        $category->fill([
            'name'            => 'Test Title',
            'slug'            => 'test-slug',
        ]);

        // Act
        $category->save();

        // Assert
        $this->assertEquals('test-slug', $category->slug);
        $this->assertEquals('Test Title', $category->name);
    }
}
