<?php

namespace App\Models;

use App\Traits\ModelTrait;

class Employee extends Model
{
    use ModelTrait;
    public int $id;
    public string $name;
    public string $email;
    public int $age;
    public string $designation;
    public string $created_at;
}