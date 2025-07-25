<?php

namespace App\Controllers;

use App\Validators\WeaponRequestValidator;
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
        $stores = $this->weaponRepository->getAllStores();
        View::render('weapon/create', ['stores' => $stores]);
    }

    public function store()
    {
         $validator = new WeaponRequestValidator($_POST);

        if (!$validator->validate()) {
            session_start();
            $_SESSION['errors'] = $validator->errors();
            $_SESSION['old'] = $validator->old();
            header('Location: /weapon.php?action=create');
            exit;
        }
        $data = $_POST;
        $this->weaponRepository->create($data);
    
        session_start();
        $_SESSION['success'] = 'Weapon created successfully!';
        header('Location: weapon.php');
        exit;
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
        $weapon = $this->weaponRepository->find($id);
        if (!$weapon) {
            View::render('404'); 
            return;
        }

        View::render('weapon/show', compact('weapon'));
    }

    public function delete($id)
    {
        echo "Delete Weapon Logic for ID: $id";
    }
}
