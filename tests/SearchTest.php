<?php

namespace TCG\Voyager\Tests;

use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use TCG\Voyager\Http\Controllers\VoyagerBaseController;
use TCG\Voyager\Models\DataRow;
use TCG\Voyager\Models\DataType;
use TCG\Voyager\Models\Post;
use TCG\Voyager\Models\User;

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

    /*public function testCanSearchByBelongstoRelationship()
    {
        $this->setupAuthorRelationship();

        $user = User::all()->first();
        $post = Post::all()->first();
        $post->author_id = $user->id;
        $post->save();

        $params = [
            'key'    => 'author_id',
            'filter' => 'contains',
            's'      => substr($user->name, 0, 2),
        ];

        $response = $this->fakeVisit('voyager.posts.index', 'GET', $params);

        $this->assertCount(1, $response->dataTypeContent);
        $this->assertEquals($post->id, $response->dataTypeContent->first()->id);

        $params['s'] = 'random';

        $response = $this->fakeVisit('voyager.posts.index', 'GET', $params);

        $this->assertCount(0, $response->dataTypeContent);
    }

    public function testCanOrderAndSearchByBelongstoRelationship()
    {
        $this->setupAuthorRelationship();

        $posts = Post::all();
        $user = User::all()->first();
        $post = $posts->first();
        $post->author_id = $user->id;
        $post->save();

        $other_user = factory(User::class)->create(['name' => 'Admin 2']);
        $other_post = $posts->last();
        $other_post->author_id = $other_user->id;
        $other_post->save();

        $params = [
            'key'    => 'author_id',
            'filter' => 'contains',
            's'      => substr($user->name, 0, 2),
            'sort_order' => 'asc',
            'order_by' => 'post_belongsto_user_relationship'
        ];

        $response = $this->fakeVisit('voyager.posts.index', 'GET', $params);

        $this->assertCount(2, $response->dataTypeContent);
        $this->assertEquals($post->id, $response->dataTypeContent[0]->id);
        $this->assertEquals($other_post->id, $response->dataTypeContent[1]->id);

        $params['sort_order'] = 'desc';

        $response = $this->fakeVisit('voyager.posts.index', 'GET', $params);

        $this->assertCount(2, $response->dataTypeContent);
        $this->assertEquals($other_post->id, $response->dataTypeContent[0]->id);
        $this->assertEquals($post->id, $response->dataTypeContent[1]->id);
    }

    protected function fakeVisit($route, $method = 'GET', $params = [])
    {
        $request = Request::create(route($route), $method, $params);
        $request->setRouteResolver(function() use ($route) {
            $stub = $this->getMockBuilder(Route::class)
                    ->addMethods(['getName'])
                    ->getMock();
            $stub->method('getName')->willReturn($route);
            return $stub;
        });

        return (new VoyagerBaseController())->index($request);
    }

    protected function setupAuthorRelationship()
    {
        DataRow::create([
            'data_type_id' => DataType::where('slug', 'posts')->first()->id,
            'field' => 'post_belongsto_user_relationship',
            'type' => 'relationship',
            'display_name' => 'Author',
            'details' => [
                'model' => 'TCG\Voyager\Models\User',
                'table' => 'users',
                'type' => 'belongsTo',
                'column' => 'author_id',
                'key' => 'id',
                'label' => 'name',
            ],
        ]);
    }*/
}
