<?php

namespace Routes;

use Routes\Route;

Route::get('/','HomeController@index');
Route::post('/user/{id}/{name}', 'HomeController@postUser');
Route::get('/posts', 'UserController@getPosts');

// api routes
Route::getApi('/posts', 'UserController@getApi');
Route::postApi('/posts/add', 'UserController@postApi');
Route::getApi('/posts/{id}/delete', 'UserController@deletePost');
Route::postApi('/posts/{id}/edit', 'UserController@editPost');









Route::execute();
?>