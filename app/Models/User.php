<?php

namespace App\Models;

use Core\Model;

class User extends Model
{
    protected static $table = 'users';
    protected static $softDelete = false;

    // Hide password hash when converting to array
    public function toArray(): array
    {
        $arr = get_object_vars($this);
        unset($arr['password_hash']);
        return $arr;
    }
}