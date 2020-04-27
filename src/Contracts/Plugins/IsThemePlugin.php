<?php

namespace TCG\Voyager\Contracts\Plugins;

interface IsThemePlugin extends IsGenericPlugin
{
    public function getStyleRoute(): string;
}
