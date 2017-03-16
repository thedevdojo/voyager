<?php
if (! function_exists('__')) {
    function __() {
        return call_user_func_array('trans', func_get_args());
    }
}
