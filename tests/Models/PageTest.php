<?php

namespace TCG\Voyager\Tests;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use TCG\Voyager\Models\Page;

class PageTest extends TestCase
{
    public function testCanCreateAPageWithLoggedInUserAutoAssigned()
    {
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

        $page->save();

        $this->assertEquals(1, $page->author_id);
        $this->assertEquals('test-slug', $page->slug);
        $this->assertEquals('Test Title', $page->title);
        $this->assertEquals('Test Excerpt', $page->excerpt);
        $this->assertEquals('Test Body', $page->body);
        $this->assertEquals('Test Description', $page->meta_description);
        $this->assertEquals('Test Meta Keywords', $page->meta_keywords);
    }

    public function testActiveScopeReturnsOnlyPagesWithStatusEqualToActive()
    {
        Auth::loginUsingId(1);

        $data = [
            'title'            => 'Test Title',
            'excerpt'          => 'Test Excerpt',
            'body'             => 'Test Body',
            'meta_description' => 'Test Description',
            'meta_keywords'    => 'Test Meta Keywords',
        ];

        $inactive = (new Page($data + ['slug' => Str::random(8), 'status' => Page::STATUS_INACTIVE]));
        $inactive->save();
        $active = (new Page($data + ['slug' => Str::random(8), 'status' => Page::STATUS_ACTIVE]));
        $active->save();

        $results = Page::active()->get()->toArray();

        $this->assertContains($active->fresh()->toArray(), $results);
        $this->assertNotContains($inactive->fresh()->toArray(), $results);
    }
}
