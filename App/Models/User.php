<?php

namespace App\Models;

use App\Models\Model;

class User extends Model {
    protected array $fillable = [
        'id', 'name', 'email'
    ];
    
    protected array $guarded = [
        'password'
    ];
    
    protected array $hidden = [
        'password'
    ];
    
    public int $id;
    public string $name;
    public string $email;
    
    /**
     * A user can have many posts
     * @return void
     */
    public function posts(): void
    {
        $this->hasMany(Post::class,'user_id');
    }
}

