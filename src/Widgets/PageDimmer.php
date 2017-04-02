<?php

namespace TCG\Voyager\Widgets;

use Arrilot\Widgets\AbstractWidget;
use TCG\Voyager\Facades\Voyager;

class PageDimmer extends AbstractWidget
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
        $count = Voyager::model('Page')->count();
        $string = $count == 1 ? 'page' : 'pages';

        return view('voyager::dimmer', array_merge($this->config, [
            'icon'   => 'voyager-group',
            'title'  => "{$count} {$string}",
            'text'   => "You have {$count} {$string} in your database. Click on button below to view all pages.",
            'button' => [
                'text' => 'View all pages',
                'link' => route('voyager.pages.index'),
            ],
            'image' => voyager_asset('images/widget-backgrounds/03.png'),
        ]));
    }
}
