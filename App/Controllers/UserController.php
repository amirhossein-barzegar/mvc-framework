<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\Post;
use App\Models\User;

class UserController extends BaseController
{
    public function getPosts(): void
    {
        new Post();
        dd(Post::findById(1, 'users'));
    }
}