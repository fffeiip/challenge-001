<?php

namespace App\Controllers;

use App\Repositories\WeaponRepository;

class WeaponController
{
    protected $weaponRepository;

    public function __construct()
    {
        $this->weaponRepository = new WeaponRepository();
    }

    public function index()
    {
        echo "List of Weapons";
    }

    public function create()
    {
        echo "Create Weapon Form";
    }

    public function store()
    {
        echo "Store Weapon Logic";
    }

    public function edit($id)
    {
        echo "Edit Weapon Form for ID: $id";
    }

    public function update($id)
    {
        echo "Update Weapon Logic for ID: $id";
    }

    public function show($id)
    {
        echo "Show Weapon Details for ID: $id";
    }

    public function delete($id)
    {
        echo "Delete Weapon Logic for ID: $id";
    }
}
