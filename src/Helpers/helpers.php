<?php

if (!function_exists('setting')) {
    function setting($key, $default = null)
    {
        return TCG\Voyager\Facades\Voyager::setting($key, $default);
    }
}

if (!function_exists('menu')) {
    function menu($menuName, $type = null, array $options = [])
    {
        return TCG\Voyager\Models\Menu::display($menuName, $type, $options);
    }
}

if (!function_exists('page')) {
    function page(string $slug = null, string $id = null, array $author = null)
    {
        return TCG\Voyager\Models\Page::display($slug, $id, $author);
    }
}

if (!function_exists('post')) {
    function post(string $slug = null, string $id = null, array $author = null)
    {
        return TCG\Voyager\Models\Post::display($slug, $id, $author);
    }
}
