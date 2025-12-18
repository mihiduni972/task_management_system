<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('frontend.login.index');
});

Route::get('/login', function () {
    return view('frontend.login.index');
})->name('login');
