<?php
// 🚨 Secure this in production! Only for dev/local
require_once __DIR__ . '/Core/Database.php';

use App\Core\Database;

$pdo = Database::getInstance()->getConnection();

// Drop and recreate database (you can skip dropping if unnecessary)
$pdo->exec("DROP TABLE IF EXISTS weapons");
$pdo->exec("DROP TABLE IF EXISTS stores");

// Recreate schema
$schema = file_get_contents(__DIR__ . '/DB/schema.sql');
$pdo->exec($schema);

// Seed data
$seed = file_get_contents(__DIR__ . '/DB/seed.sql');
$pdo->exec($seed);

echo "<h3>Database has been reset and seeded successfully.</h3>";
?>

<p>
    <a href="store.php" class="btn">Stores</a> | 
    <a href="weapon.php" class="btn">Weapons</a>
</p>