<?php

if (!function_exists('__')) {
    function __($key, array $par = [])
    {
        return trans($key, $par);
    }
}
