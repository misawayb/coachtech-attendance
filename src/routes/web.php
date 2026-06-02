<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

// 仮で/home attendance作れたらconfig/fortify変更する
Route::get('/home', function () {
    return 'ログイン成功！';
});
