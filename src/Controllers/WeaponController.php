<?php

namespace App\Controllers;

use App\Repositories\WeaponRepository;
use App\Core\View;

class WeaponController
{
    protected $weaponRepository;

    public function __construct()
    {
        $this->weaponRepository = new WeaponRepository();
    }

    public function index()
    {
        $page = $_GET['page'] ?? 1;
        $sort = $_GET['sort'] ?? 'name';
        $order = $_GET['order'] ?? 'asc';
        $filter = trim($_GET['filter'] ?? '');
        $status = trim($_GET['status'] ?? '');

        $weapons = $this->weaponRepository->getAll($page, $sort, $order, $filter, $status);

        $viewData = [
            'weapons' => $weapons['data'],
            'total' => $weapons['total'],
            'page' => $page,
            'sort' => $sort,
            'order' => $order,
            'filter' => $filter,
            'perPage' => $weapons['perPage'] ?? 10
        ];

        View::render('weapon/index', $viewData);
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
