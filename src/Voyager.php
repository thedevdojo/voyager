<?php

namespace TCG\Voyager;

class Voyager
{
    public function routes()
    {
        require __DIR__.'/../routes/voyager.php';
    }

    public function assetUrl($path)
    {
        return route('voyager.voyager_assets').'?path='.urlencode($path);
    }
}
