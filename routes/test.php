<?php

use Illuminate\Support\Facades\Route;

Route::get('/test-blade', function () {
    return view('test');
});
