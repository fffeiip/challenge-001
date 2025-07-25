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

    public function getAll(int $page, string $sort, string $order, string $filter): array
    {
        $perPage = 10;
        $offset = ($page - 1) * $perPage;

        $allowedSort = ['name', 'email', 'phone', 'city', 'state', 'country', 'created_at'];
        if (!in_array($sort, $allowedSort)) {
            $sort = 'name';
        }

        $order = strtolower($order) === 'desc' ? 'DESC' : 'ASC';

        $query = "SELECT * FROM stores WHERE 
            name LIKE :filter OR 
            email LIKE :filter OR 
            phone LIKE :filter OR 
            city LIKE :filter OR 
            state_region LIKE :filter OR 
            country LIKE :filter 
            ORDER BY $sort $order 
            LIMIT :offset, :limit";
        
        $stmt = $this->pdo->prepare($query);
        $stmt->bindValue(':filter', "%$filter%", PDO::PARAM_STR);
        $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
        $stmt->bindValue(':limit', $perPage, PDO::PARAM_INT);
        $stmt->execute();

        $data = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Get total count for pagination
        $total = $this->count($filter);

        return [
            'data' => $data,
            'total' => (int)$total,
            'perPage' => $perPage
        ];
    }

    public function find(int $id): ?array
    {
        $stmt = $this->pdo->prepare("SELECT * FROM stores WHERE id = :id");
        $stmt->execute(['id' => $id]);
        $store = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($store) {
            $store['weapons'] = $this->getWeapons($id);
        }
        return $store ?: null;
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

     public function update(int $id, array $data): bool
    {
        $stmt = $this->pdo->prepare("
            UPDATE stores SET
            name = :name,
            address_line1 = :address_line1,
            address_line2 = :address_line2,
            city = :city,
            state_region = :state_region,
            country = :country,
            phone = :phone,
            email = :email
            WHERE id = :id
        ");
        return $stmt->execute([
            'id' => $id,
            'name' => $data['name'],
            'address_line1' => $data['address_line1'],
            'address_line2' => $data['address_line2'],
            'city' => $data['city'],
            'state_region' => $data['state_region'],
            'country' => $data['country'],
            'phone' => $data['phone'],
            'email' => $data['email']
        ]);
    }

    public function delete(int $id): bool
    {
        $stmt = $this->pdo->prepare("DELETE FROM stores WHERE id = :id");
        return $stmt->execute(['id' => $id]);
    }
    
    public function getWeapons(int $storeId): ?array
    {
        $stmt = $this->pdo->prepare("
            SELECT weapons.* 
            FROM weapons 
            WHERE store_id = :store_id
            ORDER BY weapons.name ASC
        ");
        $stmt->bindValue(':store_id', $storeId, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    public function count(string $filter): int
    {
        $stmt = $this->pdo->prepare("SELECT COUNT(*) FROM stores WHERE name LIKE :filter");
        $stmt->execute([':filter' => "%$filter%"]);
        return (int) $stmt->fetchColumn();
    }
}
