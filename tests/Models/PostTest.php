<?php

namespace TCG\Voyager\Tests;

use Illuminate\Support\Facades\Auth;
use TCG\Voyager\Models\Category;
use TCG\Voyager\Models\Post;

class PostTest extends TestCase
{
    public function testCanCreateAPageWithLoggedInUserAutoAssigned()
    {
        $user = Auth::loginUsingId(1);

        $post = new Post();

        $post->fill([
            'category_id'      => Category::first()->id,
            'slug'             => 'test-slug',
            'title'            => 'Test Title',
            'excerpt'          => 'Test Excerpt',
            'body'             => 'Test Body',
            'meta_description' => 'Test Description',
            'meta_keywords'    => 'Test Meta Keywords',
        ]);

        $post->save();

        $this->assertEquals(1, $post->author_id);
        $this->assertEquals('test-slug', $post->slug);
        $this->assertEquals('Test Title', $post->title);
        $this->assertEquals('Test Excerpt', $post->excerpt);
        $this->assertEquals('Test Body', $post->body);
        $this->assertEquals('Test Description', $post->meta_description);
        $this->assertEquals('Test Meta Keywords', $post->meta_keywords);

        $this->assertEquals($post->authorId, Auth::user());
        $this->assertEquals($post->category, Category::first());
        $this->assertTrue($post->published() !== null);
    }
}
