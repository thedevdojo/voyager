<?php

if (!function_exists('getUploadPath')) {

    /**
     * Return upload path.
     *
     * @param string $slug
     * @param string $path
     *
     * @return string
     */
    function getUploadPath($slug, $path)
    {
        $_dirs = explode('/', $path);
        $_out = '';

        foreach ($_dirs as $dir) {
            if ('' === $dir) {
                continue;
            }

            if (preg_match('/%(.*?)%/', $dir)) {
                $dir = str_replace('%', '', $dir);

                if ($dir === 'slug') {
                    $_out .= $slug.'/';
                } elseif (strpos($dir, 'date:') !== false) {
                    $_out .= date(str_replace('date:', '', $dir)).'/';
                }
            } else {
                $_out .= $dir.'/';
            }
        }

        return $_out;
    }
}
