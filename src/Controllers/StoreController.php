<?php

namespace App\Controllers;

use App\Helpers\SlugGenerator;
use App\Core\View;

class StoreController
{
    protected $storeRepository;

    public function __construct()
    {
        $this->storeRepository = new \App\Repositories\StoreRepository();
    }

    public function index()
    {
        $stores = $this->storeRepository->getAll();

        $viewData = [
            'stores' => $stores
        ];

        View::render('store/index', $viewData);
    }

    public function create()
    {
        View::render('store/create');
    }

    public function store()
    {
        $slug = SlugGenerator::generate($_POST['name']);
        $this->storeRepository->create(array_merge($_POST, ['slug' => $slug]));

        header('Location: /store.php');
        exit;
    }

    public function show($id)
    {
        $store = $this->storeRepository->find($id);
        View::render('store/show', compact('store'));
    }

    public function edit($id)
    {
        $store = $this->storeRepository->find($id);
        View::render('store/edit', compact('store'));
    }

    public function update($id)
    {
        $data = $_POST;
        $this->storeRepository->update($id, $data);

        header('Location: /store.php');
        exit;
    }

    public function delete($id)
    {
        $this->storeRepository->delete($id);
        header('Location: /store.php');
        exit;
    }
    
}
