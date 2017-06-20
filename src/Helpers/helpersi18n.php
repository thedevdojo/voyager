<?php

if (!function_exists('__')) {
    function __($key, $par = null)
    {
        if (isset($par)) {
            return trans($key, $par);
        } else {
            return trans($key);
        }
    }
}
