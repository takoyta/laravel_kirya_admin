<?php

use Illuminate\Support\Facades\Route;


Route::get('login', 'LoginController@showLoginForm')->name('login');
Route::post('login', 'LoginController@login');

Route::get('logout', 'LoginController@logout')->name('logout');
