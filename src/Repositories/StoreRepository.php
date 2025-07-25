<?php

namespace App\Repositories;

use App\Core\Database;
use App\Interfaces\StoreRepositoryInterface;
use PDO;

class StoreRepository implements StoreRepositoryInterface
{
    protected $pdo;

    public function __construct()
    {
        $this->pdo = Database::getInstance()->getConnection();
    }

    public function getAll(): array
    {
        $stmt = $this->pdo->prepare("SELECT * FROM stores");
        $stmt->execute();
        return $stores = $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function find()
    {
        // Will implement later
    }

    public function create()
    {
       // Will implement later
    }

    public function update()
    {
        // Will implement later
    }

    public function delete()
    {
        // Will implement later
    }
    
}
