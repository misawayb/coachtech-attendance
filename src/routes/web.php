<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('attendance');
});


Route::get('/attendance', function () {
    return view('attendance');
});

Route::get('/admin/login', function() {
    return view('admin.login');
})->name('admin.login');

