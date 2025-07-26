<?php

class StoreRepository
{
    private $pdo;

    private const VALID_SORT_COLUMNS = [
        'name', 'city', 'state_region', 'country', 'phone', 'email', 'created_at', 'id'
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
        $perPage = max(1, min(100, (int)($params['per_page'] ?? 10))); // Limit max per page
        $sortBy = $this->validateSortColumn($params['sort_by'] ?? 'name');
        $sortDir = $this->validateSortDirection($params['sort_dir'] ?? 'ASC');
        
        // Build the base query
        $whereConditions = [];
        $queryParams = [];
        
        // Apply filters
        if (!empty($params['search'])) {
            $search = '%' . $params['search'] . '%';
            $whereConditions[] = "(name LIKE :search OR city LIKE :search2 OR state_region LIKE :search3 OR country LIKE :search4 OR email LIKE :search5)";
            $queryParams[':search'] = $search;
            $queryParams[':search2'] = $search;
            $queryParams[':search3'] = $search;
            $queryParams[':search4'] = $search;
            $queryParams[':search5'] = $search;
        }
        
        if (!empty($params['city'])) {
            $whereConditions[] = "city LIKE :city";
            $queryParams[':city'] = '%' . $params['city'] . '%';
        }
        
        if (!empty($params['state_region'])) {
            $whereConditions[] = "state_region LIKE :state_region";
            $queryParams[':state_region'] = '%' . $params['state_region'] . '%';
        }
        
        if (!empty($params['country'])) {
            $whereConditions[] = "country LIKE :country";
            $queryParams[':country'] = '%' . $params['country'] . '%';
        }

        // Build WHERE clause
        $whereClause = empty($whereConditions) ? '' : 'WHERE ' . implode(' AND ', $whereConditions);
        
        // Get total count for pagination
        $countSql = "SELECT COUNT(*) FROM stores $whereClause";
        $countStmt = $this->pdo->prepare($countSql);
        $countStmt->execute($queryParams);
        $total = (int)$countStmt->fetchColumn();
        
        // Calculate pagination
        $totalPages = ceil($total / $perPage);
        $offset = ($page - 1) * $perPage;
        
        // Get the actual data
        $dataSql = "SELECT * FROM stores $whereClause ORDER BY $sortBy $sortDir LIMIT :limit OFFSET :offset";
        $dataStmt = $this->pdo->prepare($dataSql);
        
        // Bind all parameters
        foreach ($queryParams as $key => $value) {
            $dataStmt->bindValue($key, $value, PDO::PARAM_STR);
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

    public function findAllWithoutPagination(): array
    {
        $stmt = $this->pdo->query("SELECT * FROM stores ORDER BY name ASC");
        return $stmt->fetchAll();
    }

    public function findById(int $id)
    {
        $stmt = $this->pdo->prepare("SELECT * FROM stores WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch();
    }

    
    public function save(array $data): bool
    {
        // For now, we only handle INSERT. An update would check for an 'id' in $data.
        $sql = "INSERT INTO stores (name, slug, address_line1, address_line2, city, state_region, country, phone, email) 
                VALUES (:name, :slug, :address_line1, :address_line2, :city, :state_region, :country, :phone, :email)";

        $stmt = $this->pdo->prepare($sql);

        return $stmt->execute([
            ':name' => $data['name'],
            ':slug' => $data['slug'],
            ':address_line1' => $data['address_line1'],
            ':address_line2' => $data['address_line2'] ?? null,
            ':city' => $data['city'],
            ':state_region' => $data['state_region'],
            ':country' => $data['country'],
            ':phone' => $data['phone'] ?? null,
            ':email' => $data['email'] ?? null,
        ]);
    }

    public function update(array $data,array $store,int $id): bool
    {
        $sql = "UPDATE stores SET 
                name = :name, 
                slug = :slug, 
                address_line1 = :address_line1, 
                address_line2 = :address_line2, 
                city = :city, 
                state_region = :state_region, 
                country = :country, 
                phone = :phone, 
                email = :email 
                WHERE id = :id";

        $stmt = $this->pdo->prepare($sql);

        return $stmt->execute([
            ':name' => $data['name'] ?? $store['name'],
            ':slug' => $data['slug'] ?? $store['slug'],
            ':address_line1' => $data['address_line1'] ?? $store['address_line1'],
            ':address_line2' => $data['address_line2'] ?? $store['address_line2'],
            ':city' => $data['city'] ?? $store['city'],
            ':state_region' => $data['state_region'] ?? $store['state_region'],
            ':country' => $data['country']  ?? $store['country'],
            ':phone' => $data['phone'] ?? $store['phone'],
            ':email' => $data['email'] ?? $store['email'],
            ':id' => $id,
        ]);
    }


    public function delete(int $id): bool
    {
        $stmt = $this->pdo->prepare("DELETE FROM stores WHERE id = ?");
        return $stmt->execute([$id]);
    }

    public function getFilterOptions(): array
    {
        $options = [];
        
        // Get unique cities
        $stmt = $this->pdo->query("SELECT DISTINCT city FROM stores WHERE city IS NOT NULL AND city != '' ORDER BY city");
        $options['cities'] = $stmt->fetchAll(PDO::FETCH_COLUMN);
        
        // Get unique states/regions
        $stmt = $this->pdo->query("SELECT DISTINCT state_region FROM stores WHERE state_region IS NOT NULL AND state_region != '' ORDER BY state_region");
        $options['states'] = $stmt->fetchAll(PDO::FETCH_COLUMN);
        
        // Get unique countries
        $stmt = $this->pdo->query("SELECT DISTINCT country FROM stores WHERE country IS NOT NULL AND country != '' ORDER BY country");
        $options['countries'] = $stmt->fetchAll(PDO::FETCH_COLUMN);
        
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

    
}
