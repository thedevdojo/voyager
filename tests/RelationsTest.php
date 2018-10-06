<?php

namespace TCG\Voyager\Tests;

use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Storage;
use TCG\Voyager\Events\BreadAdded;
use TCG\Voyager\Events\BreadDataAdded;
use TCG\Voyager\Events\BreadDataDeleted;
use TCG\Voyager\Events\BreadDataUpdated;
use TCG\Voyager\Events\BreadDeleted;
use TCG\Voyager\Events\BreadImagesDeleted;
use TCG\Voyager\Events\BreadUpdated;
use TCG\Voyager\Events\FileDeleted;
use TCG\Voyager\Events\TableAdded;
use TCG\Voyager\Events\TableDeleted;
use TCG\Voyager\Events\TableUpdated;
use TCG\Voyager\Models\DataType;
use TCG\Voyager\Models\Page;

class RelationsTest extends TestCase
{
    use DatabaseTransactions;

    /**
     * Test based on this: User has relationship with Role
     */
    public function testGetRelationAjax()
    {
        Event::fake();
        Auth::loginUsingId(1);


        $url = route('voyager.users.relation',['type' => "user_belongsto_role_relationship", "page" => 1]);
        $response = $this->call('GET', $url);
        $this->assertEquals(200, $response->status(), $url.' did not return a 200');
        $data = json_decode($response->getContent(),true);
        $this->assertArrayHasKey("results",$data,"Json response incorrect");
        $first_item = $data['results'][1];
        $this->assertArrayHasKey("id",$first_item,"Json response incorrect");
        $this->assertArrayHasKey("text",$first_item,"Json response incorrect");








    }


}
