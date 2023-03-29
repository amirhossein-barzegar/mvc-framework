<?php

namespace Routes;

use Routes\Route;

Route::get('/','HomeController@index');
Route::post('/user/{id}/{name}', 'HomeController@postUser');
Route::get('/posts', 'UserController@getPosts');









Route::execute();
?>