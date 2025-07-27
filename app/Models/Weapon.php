<?php
namespace App\Models;

use Core\Model;

class Weapon extends Model
{
    protected static $table = 'weapons';

    public function store(): ?Store
    {
        return Store::find($this->store_id);
    }
}