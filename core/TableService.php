<?php

namespace Core;

use Core\Database;
use PDO;

class TableService
{
    /**
     * Paginate, sort, and filter generically for any table using positional placeholders.
     * Free-text filters on specified columns, exact-match scopes, and dropdown filters via GET.
     *
     * @param string $table             DB table name
     * @param string $modelClass        Fully qualified model class
     * @param array  $filterColumns     Columns for free-text LIKE search
     * @param array  $allowedSort       Columns allowed to sort by
     * @param int    $perPage           Rows per page
     * @param array  $extraWhereMapping Associative col=>value for fixed WHERE clauses (scoping)
     *
     * @return array ['items'=>[], 'meta'=>[filter,sortBy,sortDir,page,perPage,total,totalPages]]
     */
    public static function paginate(
        string $table,
        string $modelClass,
        array $filterColumns = [],
        array $allowedSort = [],
        int $perPage = 10,
        array $extraWhereMapping = []
    ): array {
        $db = Database::getInstance()->pdo();

        // 1) Sanitize inputs
        $filter = isset($_GET['filter']) ? trim($_GET['filter']) : '';
        $sort   = isset($_GET['sort']) && in_array($_GET['sort'], $allowedSort, true)
                  ? $_GET['sort']
                  : ($allowedSort[0] ?? 'id');
        $dir    = (isset($_GET['dir']) && strtolower($_GET['dir']) === 'desc') ? 'DESC' : 'ASC';
        $page   = max(1, (int)($_GET['page'] ?? 1));
        $offset = ($page - 1) * $perPage;

        // 2) Build WHERE clauses and bindings
        $clauses = [];
        $bindings = [];

        // 2a) Fixed scopes
        foreach ($extraWhereMapping as $col => $val) {
            $clauses[]   = "`{$col}` = ?";
            $bindings[] = $val;
        }

        // 2b) Free-text search
        if ($filter !== '' && $filterColumns) {
            $parts = [];
            foreach ($filterColumns as $col) {
                $parts[]    = "`{$col}` LIKE ?";
                $bindings[] = "%{$filter}%";
            }
            $clauses[] = '(' . implode(' OR ', $parts) . ')';
        }

        // 2c) Dropdown filters (any GET except control & scopes)
        $control = ['filter','sort','dir','page'];
        foreach ($_GET as $k => $v) {
            if (in_array($k, $control, true) || $v === '' || array_key_exists($k, $extraWhereMapping)) {
                continue;
            }
            // treat as dropdown: LIKE
            $clauses[]   = "`{$k}` LIKE ?";
            $bindings[] = "%{$v}%";
        }

        // Always exclude soft-deleted rows
        $clauses[] = "`deleted_at` IS NULL";

        $whereSql = $clauses ? ' WHERE ' . implode(' AND ', $clauses) : '';

        // 3) Total count
        $countSql = "SELECT COUNT(*) FROM `{$table}`{$whereSql}";
        $stmt     = $db->prepare($countSql);
        $stmt->execute($bindings);
        $total    = (int)$stmt->fetchColumn();
        $totalPages = (int)ceil($total / $perPage);

        // 4) Fetch data
        $dataSql = "SELECT * FROM `{$table}`{$whereSql} ORDER BY `{$sort}` {$dir} LIMIT ? OFFSET ?";
        $stmt    = $db->prepare($dataSql);
        $i = 1;
        foreach ($bindings as $val) {
            $stmt->bindValue($i++, $val);
        }
        $stmt->bindValue($i++, $perPage, PDO::PARAM_INT);
        $stmt->bindValue($i++, $offset,  PDO::PARAM_INT);
        $stmt->execute();
        $items = $stmt->fetchAll(PDO::FETCH_CLASS, $modelClass);

        return [
            'items' => $items,
            'meta'  => [
                'filter'     => $filter,
                'sortBy'     => $sort,
                'sortDir'    => strtolower($dir),
                'page'       => $page,
                'perPage'    => $perPage,
                'total'      => $total,
                'totalPages' => $totalPages,
            ],
        ];
    }
}
