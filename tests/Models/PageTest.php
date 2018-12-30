<?php

namespace TCG\Voyager\Tests;

use Illuminate\Support\Facades\Auth;
use TCG\Voyager\Models\Page;

class PageTest extends TestCase
{
    /** @test */
    public function can_create_a_page_with_logged_in_user_auto_assigned()
    {
        // Arrange
        $user = Auth::loginUsingId(1);

        $page = new Page();

        $page->fill([
            'slug'             => 'test-slug',
            'title'            => 'Test Title',
            'excerpt'          => 'Test Excerpt',
            'body'             => 'Test Body',
            'meta_description' => 'Test Description',
            'meta_keywords'    => 'Test Meta Keywords',
        ]);

        // Act
        $page->save();

        // Assert
        $this->assertEquals(1, $page->author_id);
        $this->assertEquals('test-slug', $page->slug);
        $this->assertEquals('Test Title', $page->title);
        $this->assertEquals('Test Excerpt', $page->excerpt);
        $this->assertEquals('Test Body', $page->body);
        $this->assertEquals('Test Description', $page->meta_description);
        $this->assertEquals('Test Meta Keywords', $page->meta_keywords);
    }

    /** @test */
    public function active_scope_returns_only_pages_with_status_equal_to_active()
    {
        // Arrange
        Auth::loginUsingId(1);

        $data = [
            'title'            => 'Test Title',
            'excerpt'          => 'Test Excerpt',
            'body'             => 'Test Body',
            'meta_description' => 'Test Description',
            'meta_keywords'    => 'Test Meta Keywords',
        ];

        $inactive = (new Page($data + ['slug' => str_random(8), 'status' => Page::STATUS_INACTIVE]));
        $inactive->save();
        $active = (new Page($data + ['slug' => str_random(8), 'status' => Page::STATUS_ACTIVE]));
        $active->save();

        // Act
        $results = Page::active()->get()->toArray();

        // Assert
        $this->assertContains($active->fresh()->toArray(), $results);
        $this->assertNotContains($inactive->fresh()->toArray(), $results);
    }
}
