<?php

if (!function_exists('outputAriaForHelpterText')) {
    /**
     * If row has helperText, output the appropriate aria-described by tag for the input
     *
     * @param DataRow $row
     *
     * @return string HTML output.
     */
    function outputAriaForHelpterText($row)
    {
        return isset($row->details->helperText) ? 'aria-describedby="'.$row->field.'HelpBlock"' : '';
    }
}
