<?php

namespace Routes;

use Routes\Route;

Route::get('/','HomeController@index');
Route::get('/register', 'HomeController@register');
Route::get('/login', 'HomeController@login');
Route::post('/register', 'HomeController@postLogin');

Route::get("/user/{id}/role/{roleId}", "HomeController@showUser");
Route::get("/post/{postId}", "HomeController@showPost");


