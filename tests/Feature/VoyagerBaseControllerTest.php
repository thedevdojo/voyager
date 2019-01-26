<?php

namespace TCG\Voyager\Tests\Feature;

use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\Auth;
use TCG\Voyager\Models\Post;
use TCG\Voyager\Tests\TestCase;

class VoyagerBaseControllerTest extends TestCase
{
    use DatabaseTransactions;

    protected $user;

    public function setUp()
    {
        parent::setUp();

        $this->user = Auth::loginUsingId(1);
    }

    public function testEditAndSaveRedirectToReferer()
    {
        $post = Post::where('status', 'PUBLISHED')->first();
        $params = [
            'key'    => 'status',
            'filter' => 'equals',
            's'      => 'PUBLISHED',
        ];
        $referer = route('voyager.posts.index').'?'.http_build_query($params);
        $this->get(route('voyager.posts.edit', ['post' => $post->id]), [
            'referer' => $referer,
        ])->see($post->title);
        $this->assertEquals($referer, session('url.intended'));

        $this->post(route('voyager.posts.index')."/{$post->id}", array_merge($post->toArray(), [
            '_method' => 'PUT',
            'status'  => 'DRAFT',
        ]), [
            'referer' => $referer,
        ])->assertRedirectedTo($referer);
        $this->assertNull(session('url.intended'));
        $post->refresh();
        $this->assertEquals('DRAFT', $post->status);

        $this->visit($referer)
            ->dontSee($post->title)
            ->see(Post::where('status', 'PUBLISHED')->first()->title);
    }

    public function testEditAndSaveRedirectToIndex()
    {
        $post = Post::where('status', 'PUBLISHED')->first();
        $this->visit(route('voyager.posts.edit', ['post' => $post->id]))->see($post->title);
        $this->assertNull(session('url.intended'));

        $this->post(route('voyager.posts.index')."/{$post->id}", array_merge($post->toArray(), [
            '_method' => 'PUT',
            'status'  => 'DRAFT',
        ]))->assertRedirectedTo(route('voyager.posts.index'));
        $post->refresh();
        $this->assertEquals('DRAFT', $post->status);
    }

    public function testDeleteRedirectToReferer()
    {
        $post = Post::first();

        $params = [
            'page' => '1',
        ];
        $referer = route('voyager.posts.index').'?'.http_build_query($params);
        $this->post(route('voyager.posts.index')."/{$post->id}", [
            '_method' => 'DELETE',
        ], [
            'referer' => $referer,
        ])->assertRedirectedTo($referer);
        $this->assertEmpty(Post::find($post->id));
    }

    public function testDeleteRedirectToIndex()
    {
        $post = Post::first();

        $this->post(route('voyager.posts.index')."/{$post->id}", [
            '_method' => 'DELETE',
        ])->assertRedirectedTo(route('voyager.posts.index'));
        $this->assertEmpty(Post::find($post->id));
    }
}
