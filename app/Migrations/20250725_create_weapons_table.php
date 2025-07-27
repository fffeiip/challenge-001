<?php
namespace App\Migrations;

use Core\Database;
use Core\MigrationInterface;

class CreateWeaponsTable implements MigrationInterface 
{
    public function up(): void 
    {
        $db = Database::getInstance()->pdo();
        $sql = <<<'SQL'
CREATE TABLE IF NOT EXISTS weapons (
  id INTEGER PRIMARY KEY AUTOINCREMENT,
  store_id INTEGER NOT NULL,
  name TEXT NOT NULL,
  type TEXT,
  caliber TEXT,
  serial_number TEXT UNIQUE,
  price REAL,
  in_stock INTEGER DEFAULT 0,
  status TEXT,
  created_at TEXT NOT NULL DEFAULT (datetime('now')),
  updated_at TEXT NOT NULL DEFAULT (datetime('now')),
  deleted_at TEXT,
  deleted_by INTEGER,
  FOREIGN KEY(store_id) REFERENCES stores(id) ON DELETE CASCADE,
  FOREIGN KEY(deleted_by) REFERENCES users(id)
);
SQL;
        $db->exec($sql);
    }
    public function down(): void {
        Database::getInstance()->pdo()->exec("DROP TABLE IF EXISTS weapons");
    }
}