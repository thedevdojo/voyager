<?php

namespace TCG\Voyager\Widgets;

use App\Models\These;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use TCG\Voyager\Facades\Voyager;
// use App\Models\Thesis;

class TheseDimmer extends BaseDimmer
{
    /**
     * The configuration array.
     *
     * @var array
     */
    protected $config = [];

    /**
     * Treat this method as a controller action.
     * Return view() or other content to display.
     */

    public function run()
    {
        $count = These::all()->count();
        $string = trans_choice('voyager::dimmer.thesis', $count);

        return view('voyager::dimmer', array_merge($this->config, [
            'icon'   => 'voyager-documents',
            'title'  => "{$count} {$string}",
            'text'   => __('voyager::dimmer.researcher_text', ['count' => $count, 'string' => Str::lower($string)]),
            'button' => [
                'text' => __('View all theses'),
                'link' => url('admin/theses'),
            ],
            'image' => voyager_asset('images/widget-backgrounds/02.jpg'),
        ]));
    }

    /**
     * Determine if the widget should be displayed.
     *
     * @return bool
     */
    public function shouldBeDisplayed()
    {
        return Auth::user()->can('browse', app(These::class));
    }
}
