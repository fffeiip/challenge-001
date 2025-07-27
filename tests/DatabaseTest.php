<?php
use PHPUnit\Framework\TestCase;

class DatabaseTest extends TestCase
{
    public function testInsertAndRead()
    {
        // In-memory SQLite
        $pdo = new PDO('sqlite::memory:');
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        // Create table, insert, read back
        $pdo->exec('CREATE TABLE test (id INTEGER PRIMARY KEY, name TEXT)');
        $stmt = $pdo->prepare('INSERT INTO test (name) VALUES (?)');
        $stmt->execute(['foo']);
        $id = $pdo->lastInsertId();

        $row = $pdo
            ->query('SELECT * FROM test WHERE id = ' . $id)
            ->fetch(PDO::FETCH_ASSOC);

        $this->assertEquals('foo', $row['name'], 'Inserted value should match read value');
    }
}
