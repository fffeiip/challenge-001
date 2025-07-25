<?php

namespace App\Controllers;

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
        echo '<pre>';
        print_r($stores);
        exit;
    }
    
}
