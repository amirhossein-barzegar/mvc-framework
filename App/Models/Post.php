<?php

namespace App\Models;

use App\Models\Model;

class Post extends Model
{
    public function users()
    {
        $this->belongsToMany(User::class, 'user_id');
    }
}