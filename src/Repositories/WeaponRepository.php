<?php

class WeaponRepository
{
    private $pdo;
    private const VALID_SORT_COLUMNS = [
        'name', 'type', 'caliber', 'serial_number', 'price', 'store_name', 'in_stock', 'status', 'id'
    ];
    
    private const VALID_SORT_DIRECTIONS = ['ASC', 'DESC'];

    public function __construct()
    {
        $this->pdo = Database::getConnection();
    }

    public function findAll(array $params = []): array
    {
        // Extract parameters with defaults
        $page = max(1, (int)($params['page'] ?? 1));
        $perPage = max(1, min(100, (int)($params['per_page'] ?? 10)));
        $sortBy = $this->validateSortColumn($params['sort_by'] ?? 'name');
        $sortDir = $this->validateSortDirection($params['sort_dir'] ?? 'ASC');
        
        // Build the base query with JOIN to get store name
        $baseQuery = "FROM weapons w 
                      JOIN stores s ON w.store_id = s.id";
        
        $whereConditions = [];
        $queryParams = [];
        
        // Apply filters
        if (!empty($params['search'])) {
            $search = '%' . $params['search'] . '%';
            $whereConditions[] = "(w.name LIKE :search OR w.type LIKE :search2 OR w.caliber LIKE :search3 OR w.serial_number LIKE :search4 OR s.name LIKE :search5)";
            $queryParams[':search'] = $search;
            $queryParams[':search2'] = $search;
            $queryParams[':search3'] = $search;
            $queryParams[':search4'] = $search;
            $queryParams[':search5'] = $search;
        }
        
        if (!empty($params['type'])) {
            $whereConditions[] = "w.type = :type";
            $queryParams[':type'] = $params['type'];
        }
        
        if (!empty($params['caliber'])) {
            $whereConditions[] = "w.caliber LIKE :caliber";
            $queryParams[':caliber'] = '%' . $params['caliber'] . '%';
        }
        
        if (!empty($params['store_id'])) {
            $whereConditions[] = "w.store_id = :store_id";
            $queryParams[':store_id'] = $params['store_id'];
        }
        
        if (!empty($params['status'])) {
            $whereConditions[] = "w.status = :status";
            $queryParams[':status'] = $params['status'];
        }
        
        if (isset($params['in_stock']) && $params['in_stock'] !== '') {
            $whereConditions[] = "w.in_stock = :in_stock";
            $queryParams[':in_stock'] = (int)$params['in_stock'];
        }
        
        if (!empty($params['price_min'])) {
            $whereConditions[] = "w.price >= :price_min";
            $queryParams[':price_min'] = (float)$params['price_min'];
        }
        
        if (!empty($params['price_max'])) {
            $whereConditions[] = "w.price <= :price_max";
            $queryParams[':price_max'] = (float)$params['price_max'];
        }

        // Build WHERE clause
        $whereClause = empty($whereConditions) ? '' : 'WHERE ' . implode(' AND ', $whereConditions);
        
        // Get total count for pagination
        $countSql = "SELECT COUNT(*) $baseQuery $whereClause";
        $countStmt = $this->pdo->prepare($countSql);
        $countStmt->execute($queryParams);
        $total = (int)$countStmt->fetchColumn();
        
        // Calculate pagination
        $totalPages = ceil($total / $perPage);
        $offset = ($page - 1) * $perPage;
        
        // Map sort column for query (handle store_name specially)
        $sortColumn = $sortBy === 'store_name' ? 's.name' : 'w.' . $sortBy;
        
        // Get the actual data
        $dataSql = "SELECT w.*, s.name as store_name 
                    $baseQuery 
                    $whereClause 
                    ORDER BY $sortColumn $sortDir 
                    LIMIT :limit OFFSET :offset";
        
        $dataStmt = $this->pdo->prepare($dataSql);
        
        // Bind all parameters
        foreach ($queryParams as $key => $value) {
            if (is_float($value)) {
                $dataStmt->bindValue($key, $value, PDO::PARAM_STR); // PDO handles float as string
            } elseif (is_int($value)) {
                $dataStmt->bindValue($key, $value, PDO::PARAM_INT);
            } else {
                $dataStmt->bindValue($key, $value, PDO::PARAM_STR);
            }
        }
        $dataStmt->bindValue(':limit', $perPage, PDO::PARAM_INT);
        $dataStmt->bindValue(':offset', $offset, PDO::PARAM_INT);
        
        $dataStmt->execute();
        $data = $dataStmt->fetchAll();
        
        return [
            'data' => $data,
            'total' => $total,
            'page' => $page,
            'per_page' => $perPage,
            'total_pages' => $totalPages,
            'has_next' => $page < $totalPages,
            'has_prev' => $page > 1,
            'sort_by' => $sortBy,
            'sort_dir' => $sortDir,
            'filters' => $params
        ];
    }


    public function findByStoreId(int $storeId, array $params = []): array
    {
        // Extract parameters with defaults
        $page = max(1, (int)($params['page'] ?? 1));
        $perPage = max(1, min(100, (int)($params['per_page'] ?? 10))); // Limit max per page
        $sortBy = $this->validateSortColumn($params['sort_by'] ?? 'name');
        $sortDir = $this->validateSortDirection($params['sort_dir'] ?? 'ASC');
        
        // Build the base query
        $whereConditions = ['store_id = :store_id'];
        $queryParams = [':store_id' => $storeId];
        
        // Apply filters
        if (!empty($params['search'])) {
            $search = '%' . $params['search'] . '%';
            $whereConditions[] = "(name LIKE :search OR type LIKE :search2 OR caliber LIKE :search3)";
            $queryParams[':search'] = $search;
            $queryParams[':search2'] = $search;
            $queryParams[':search3'] = $search;
        }
        
        if (!empty($params['type'])) {
            $whereConditions[] = "type LIKE :type";
            $queryParams[':type'] = '%' . $params['type'] . '%';
        }
        
        if (!empty($params['caliber'])) {
            $whereConditions[] = "caliber LIKE :caliber";
            $queryParams[':caliber'] = '%' . $params['caliber'] . '%';
        }
        
        if (!empty($params['in_stock'])) {
            if ($params['in_stock'] === 'available') {
                $whereConditions[] = "in_stock > 0";
            } elseif ($params['in_stock'] === 'out_of_stock') {
                $whereConditions[] = "in_stock = 0";
            }
        }
        
        if (!empty($params['price_min'])) {
            $whereConditions[] = "price >= :price_min";
            $queryParams[':price_min'] = (float)$params['price_min'];
        }
        
        if (!empty($params['price_max'])) {
            $whereConditions[] = "price <= :price_max";
            $queryParams[':price_max'] = (float)$params['price_max'];
        }

        // Build WHERE clause
        $whereClause = 'WHERE ' . implode(' AND ', $whereConditions);
        
        // Get total count for pagination
        $countSql = "SELECT COUNT(*) FROM weapons $whereClause";
        $countStmt = $this->pdo->prepare($countSql);
        $countStmt->execute($queryParams);
        $total = (int)$countStmt->fetchColumn();
        
        // Calculate pagination
        $totalPages = ceil($total / $perPage);
        $offset = ($page - 1) * $perPage;
        
        // Get the actual data
        $dataSql = "SELECT * FROM weapons $whereClause ORDER BY $sortBy $sortDir LIMIT :limit OFFSET :offset";
        $dataStmt = $this->pdo->prepare($dataSql);
        
        // Bind all parameters
        foreach ($queryParams as $key => $value) {
            if (is_int($value)) {
                $dataStmt->bindValue($key, $value, PDO::PARAM_INT);
            } elseif (is_float($value)) {
                $dataStmt->bindValue($key, $value, PDO::PARAM_STR);
            } else {
                $dataStmt->bindValue($key, $value, PDO::PARAM_STR);
            }
        }
        $dataStmt->bindValue(':limit', $perPage, PDO::PARAM_INT);
        $dataStmt->bindValue(':offset', $offset, PDO::PARAM_INT);
        
        $dataStmt->execute();
        $data = $dataStmt->fetchAll();
        
        return [
            'data' => $data,
            'total' => $total,
            'page' => $page,
            'per_page' => $perPage,
            'total_pages' => $totalPages,
            'has_next' => $page < $totalPages,
            'has_prev' => $page > 1,
            'sort_by' => $sortBy,
            'sort_dir' => $sortDir,
            'filters' => $params
        ];
    }

    
    public function getWeaponFilterOptions(int $storeId): array
    {
        $options = [];
        
        // Get unique weapon types for this store
        $stmt = $this->pdo->prepare("SELECT DISTINCT type FROM weapons WHERE store_id = :store_id AND type IS NOT NULL AND type != '' ORDER BY type");
        $stmt->execute([':store_id' => $storeId]);
        $options['types'] = $stmt->fetchAll(PDO::FETCH_COLUMN);
        
        // Get unique calibers for this store
        $stmt = $this->pdo->prepare("SELECT DISTINCT caliber FROM weapons WHERE store_id = :store_id AND caliber IS NOT NULL AND caliber != '' ORDER BY caliber");
        $stmt->execute([':store_id' => $storeId]);
        $options['calibers'] = $stmt->fetchAll(PDO::FETCH_COLUMN);
        
        return $options;
    }


    public function findById(int $id)
    {
        $stmt = $this->pdo->prepare("SELECT * FROM weapons WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch();
    }

   
    public function save(array $data): bool
    {
        $sql = "INSERT INTO weapons (store_id, name, type, caliber, serial_number, price, in_stock, status) 
                VALUES (:store_id, :name, :type, :caliber, :serial_number, :price, :in_stock, :status)";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute($this->prepareData($data));
    }

  
    public function update(array $data, int $id): bool
    {
        $sql = "UPDATE weapons SET store_id = :store_id, name = :name, type = :type, caliber = :caliber, 
                serial_number = :serial_number, price = :price, in_stock = :in_stock, status = :status
                WHERE id = :id";
        $stmt = $this->pdo->prepare($sql);
        $params = $this->prepareData($data);
        $params[':id'] = $id;
        return $stmt->execute($params);
    }

   
    public function delete(int $id): bool
    {
        $stmt = $this->pdo->prepare("DELETE FROM weapons WHERE id = ?");
        return $stmt->execute([$id]);
    }

     
    public function getFilterOptions(): array
    {
        $options = [];
        
        // Get unique weapon types
        $stmt = $this->pdo->query("SELECT DISTINCT type FROM weapons WHERE type IS NOT NULL AND type != '' ORDER BY type");
        $options['types'] = $stmt->fetchAll(PDO::FETCH_COLUMN);
        
        // Get unique calibers
        $stmt = $this->pdo->query("SELECT DISTINCT caliber FROM weapons WHERE caliber IS NOT NULL AND caliber != '' ORDER BY caliber");
        $options['calibers'] = $stmt->fetchAll(PDO::FETCH_COLUMN);
        
        // Get unique statuses
        $stmt = $this->pdo->query("SELECT DISTINCT status FROM weapons WHERE status IS NOT NULL AND status != '' ORDER BY status");
        $options['statuses'] = $stmt->fetchAll(PDO::FETCH_COLUMN);
        
        // Get stores for dropdown
        $stmt = $this->pdo->query("SELECT id, name FROM stores ORDER BY name");
        $options['stores'] = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        return $options;
    }

     
    private function validateSortColumn(string $column): string
    {
        return in_array($column, self::VALID_SORT_COLUMNS) ? $column : 'name';
    }

    
    private function validateSortDirection(string $direction): string
    {
        $direction = strtoupper($direction);
        return in_array($direction, self::VALID_SORT_DIRECTIONS) ? $direction : 'ASC';
    }

    private function prepareData(array $data): array
    {
        return [
            ':store_id' => $data['store_id'],
            ':name' => $data['name'],
            ':type' => $data['type'],
            ':caliber' => $data['caliber'],
            ':serial_number' => $data['serial_number'],
            ':price' => $data['price'],
            ':in_stock' => $data['in_stock'],
            ':status' => $data['status'],
        ];
    }
}
