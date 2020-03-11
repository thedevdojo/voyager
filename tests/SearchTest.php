<?php

namespace TCG\Voyager\Tests;

use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\Auth;
use TCG\Voyager\Models\Post;

class SearchTest extends TestCase
{
    use DatabaseTransactions;

    protected $user;

    public function setUp(): void
    {
        parent::setUp();

        $this->user = Auth::loginUsingId(1);
    }

    public function testCanSearchEquals0()
    {
        $post = Post::where('featured', 0)->first();
        $post->featured = 1;
        $post->save();
        $params = [
            'key'    => 'featured',
            'filter' => 'equals',
            's'      => '0',
        ];
        $this->visit(route('voyager.posts.index').'?'.http_build_query($params))
            ->dontSee($post->title)
            ->see(Post::where('featured', 0)->first()->title);

        $params['s'] = 1;
        $this->visit(route('voyager.posts.index').'?'.http_build_query($params))
            ->see($post->title)
            ->dontSee(Post::where('featured', 0)->first()->title);
    }
}
