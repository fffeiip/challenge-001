<?php

namespace App\Controllers;
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
    
}
