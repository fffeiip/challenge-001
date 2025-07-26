<?php

namespace tests\repositories;

use WeaponRepository;
use DatabaseTestCase;

class WeaponRepositoryTest extends DatabaseTestCase
{
    private $weaponRepo;

    protected function setUp(): void
    {
        parent::setUp();
        $this->weaponRepo = new WeaponRepository();
    }

    public function testFindByIdFindsExistingWeapon()
    {
        $weapon = $this->weaponRepo->findById(1);
        $this->assertIsArray($weapon);
        $this->assertEquals('AR-15 Standard', $weapon['name']);
    }

    public function testFindByIdReturnsFalseForNonExistentWeapon()
    {
        $weapon = $this->weaponRepo->findById(9999);
        $this->assertFalse($weapon);
    }

    public function testFindAllReturnsAllWeapons()
    {
        // Note: The number of weapons in seeds.sql is 20
        $result = $this->weaponRepo->findAll();
        $this->assertCount(10, $result['data']); // Default per_page is 10
        $this->assertEquals(20, $result['total']);
    }

    public function testSaveCreatesNewWeapon()
    {
        $data = [
            'store_id' => 1,
            'name' => 'Test Pistol',
            'type' => 'Handgun',
            'caliber' => '9mm',
            'serial_number' => 'TEST12345',
            'price' => 599.99,
            'in_stock' => 10,
            'status' => 'active',
        ];

        $result = $this->weaponRepo->save($data);
        $this->assertTrue($result);

        $pdo = \Database::getConnection();
        $stmt = $pdo->query("SELECT * FROM weapons WHERE serial_number = 'TEST12345'");
        $newWeapon = $stmt->fetch();

        $this->assertEquals('Test Pistol', $newWeapon['name']);
        $this->assertEquals(599.99, $newWeapon['price']);
    }

    public function testUpdateModifiesExistingWeapon()
    {
        $data = [
            'store_id' => 1,
            'name' => 'AK-47 Gold Plated',
            'price' => 9999.99,
            'in_stock' => 1,
            'status' => 'active',
            'type' => 'Rifle',
            'caliber' => '7.62x39mm',
            'serial_number' => 'SN000001',
        ];

        $result = $this->weaponRepo->update($data, 1);
        $this->assertTrue($result);

        $updatedWeapon = $this->weaponRepo->findById(1);
        $this->assertEquals('AK-47 Gold Plated', $updatedWeapon['name']);
        $this->assertEquals(9999.99, $updatedWeapon['price']);
    }

    public function testDeleteRemovesWeapon()
    {
        $result = $this->weaponRepo->delete(1);
        $this->assertTrue($result);

        $deletedWeapon = $this->weaponRepo->findById(1);
        $this->assertFalse($deletedWeapon);
    }
}
