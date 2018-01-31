<?php

if (!function_exists('getUploadPath')) {

    /**
     * Return upload path
     *
     * @param Illuminate\Http\Request $request
     * @param string                  $path
     *
     * @return string
     */
    function getUploadPath($request, $path)
    {
        $_dirs = explode('/', $path);
        $_out = '';

        foreach ($_dirs as $dir) {
            if ('' === $dir) {
                continue;
            }

            if (preg_match('/%(.*?)%/', $dir)) {
                $dir = str_replace('%', '', $dir);

                if ($request->exists($dir)) {
                    $_out .= $request->input($dir) . '/';
                } elseif (strpos($dir, 'date:') !== false) {
                    $_out .= date(str_replace('date:', '', $dir)) . '/';
                } else {
                    throw new \Exception($dir . ' input is not exists');
                }

            } else {
                $_out .= $dir . '/';
            }
        }

        return $_out;
    }
}