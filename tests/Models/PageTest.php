<?php

namespace TCG\Voyager\Tests;

use TCG\Voyager\Models\Page;
use Illuminate\Support\Facades\Auth;

class PageTest extends TestCase
{
    public function setUp()
    {
        parent::setUp();

        $this->install();
    }

    /** @test */
    public function can_create_a_page_with_logged_in_user_auto_assigned()
    {
    	// Arrange
    	$user = Auth::loginUsingId(1);

    	$page = new Page;

    	$page->fill([
    		'slug' => 'test-slug',
    		'title' => 'Test Title',
    		'excerpt' => 'Test Excerpt',
    		'body' => 'Test Body',
    		'meta_description' => 'Test Description',
    		'meta_keywords' => 'Test Meta Keywords',
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
}
