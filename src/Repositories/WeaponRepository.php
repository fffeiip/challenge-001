<?php

namespace App\Repositories;

use App\Core\Database;
use App\Interfaces\WeaponRepositoryInterface;
use PDO;

class WeaponRepository implements WeaponRepositoryInterface
{
    protected $pdo;

    public function __construct()
    {
        $this->pdo = Database::getInstance()->getConnection();
    }

    public function getAll(int $page, string $sort, string $order, string $filter, string $status): array
    {
        $limit = 10;
        $offset = ($page - 1) * $limit;

        $allowedSort = ['name', 'type', 'caliber', 'serial_number', 'price', 'status', 'created_at'];
        if (!in_array($sort, $allowedSort)) {
            $sort = 'name';
        }
        
        $order = strtolower($order) === 'desc' ? 'DESC' : 'ASC';
        
        $query = "SELECT w.*, s.name AS store_name 
                FROM weapons w 
                LEFT JOIN stores s ON w.store_id = s.id WHERE
                (w.name LIKE :filter OR 
                w.type LIKE :filter OR 
                w.caliber LIKE :filter OR 
                w.serial_number LIKE :filter) AND 
                w.deleted_at IS NULL";

        if ($status) {
            $query .= " AND w.status = :status";
        }

        $query .= " ORDER BY w.$sort $order LIMIT :offset, :limit";

        $stmt = $this->pdo->prepare($query);
       
        $stmt->bindValue(':filter', "%$filter%", PDO::PARAM_STR);
        $stmt->bindValue(':offset', (int)$offset, PDO::PARAM_INT);
        $stmt->bindValue(':limit', (int)$limit, PDO::PARAM_INT);

        if (!empty($status)) {
            $stmt->bindValue(':status', $status, PDO::PARAM_STR);
        }
        $stmt->execute();
        $data = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Get total count for pagination
        $total = $this->count($filter, $status);

        return [
            'data' => $data,
            'total' => (int) $total,
            'perPage' => $limit
        ];
    }

    public function find(int $id): ?array
    {
        return null; // Implementation for finding a weapon by ID
    }

    public function create(array $data): bool
    {
        return false; // Implementation for creating a new weapon
    }

    public function update(int $id, array $data): bool
    {
        return false; // Implementation for updating a weapon
    }

    public function delete(int $id): bool
    {
        return false; // Implementation for deleting a weapon
    }

    public function count(string $filter, string $status): int
    {
        $query = "SELECT COUNT(*) FROM weapons w WHERE
            (w.name LIKE :filter OR 
            w.type LIKE :filter OR 
            w.caliber LIKE :filter OR 
            w.serial_number LIKE :filter) AND 
            w.deleted_at IS NULL";
        
        if ($status) {
            $query .= " AND w.status = :status";
        }

        $stmt = $this->pdo->prepare($query);
        $stmt->bindValue(':filter', "%$filter%", PDO::PARAM_STR);

        if (!empty($status)) {
            $stmt->bindValue(':status', $status, PDO::PARAM_STR);
        }

        $stmt->execute();
        return (int) $stmt->fetchColumn();
    }
}
