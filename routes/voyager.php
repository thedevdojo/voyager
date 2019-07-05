<?php

Route::get('admin', function(){
    echo 'yeah!';
});

Route::view('/admin', 'voyager::dashboard');