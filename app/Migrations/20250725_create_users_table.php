<?php

namespace App\Migrations;

use Core\Database;
use Core\MigrationInterface;

class CreateUsersTable implements MigrationInterface 
{
    public function up(): void 
    {
        $db = Database::getInstance()->pdo();
        $sql = <<<'SQL'
CREATE TABLE IF NOT EXISTS users (
  id INTEGER PRIMARY KEY AUTOINCREMENT,
  store_id INTEGER,
  username TEXT NOT NULL UNIQUE,
  password_hash TEXT NOT NULL,
  role TEXT NOT NULL,
  created_at TEXT NOT NULL DEFAULT (datetime('now')),
  updated_at TEXT NOT NULL DEFAULT (datetime('now')),
  FOREIGN KEY(store_id) REFERENCES stores(id) ON DELETE CASCADE
);
SQL;
        $db->exec($sql);
    }
    public function down(): void {
        Database::getInstance()->pdo()->exec("DROP TABLE IF EXISTS users");
    }
}