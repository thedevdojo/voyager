<?php

namespace TCG\Voyager\Widgets;

use Arrilot\Widgets\AbstractWidget;
use TCG\Voyager\Facades\Voyager;

class UserDimmer extends AbstractWidget
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
        $count  = Voyager::model('User')->count();
        $string = $count == 1 ? 'user' : 'users';

        return view('voyager::dimmer', array_merge($this->config, [
            'icon'   => config('voyager.widgets.user.icon'),
            'title'  => "{$count} {$string}",
            'text'   => "You have {$count} {$string} in your database. Click on button below to view all users.",
            'button' => [
                'text' => 'View all users',
                'link' => route('voyager.users.index'),
            ],
            'image' => url(config('voyager.assets_path').config('voyager.widgets.user.image')),
        ]));
    }
}
