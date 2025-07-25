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

    public function create(array $data): bool
    {
    $stmt = $this->pdo->prepare("
        INSERT INTO stores (
        name, slug, address_line1, address_line2, city, state_region, country, phone, email
        ) VALUES (
        :name, :slug, :address_line1, :address_line2, :city, :state_region, :country, :phone, :email
        )
    ");
    return $stmt->execute([
        'name' => $data['name'],
        'slug' => $data['slug'],
        'address_line1' => $data['address_line1'],
        'address_line2' => $data['address_line2'],
        'city' => $data['city'],
        'state_region' => $data['state_region'],
        'country' => $data['country'],
        'phone' => $data['phone'],
        'email' => $data['email']
    ]);
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
