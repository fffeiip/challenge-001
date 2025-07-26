<?php

namespace tests\repositories;

use StoreRepository;
use DatabaseTestCase;

class StoreRepositoryTest extends DatabaseTestCase
{
    private $storeRepo;

    protected function setUp(): void
    {
        parent::setUp();
        $this->storeRepo = new StoreRepository();
    }

    public function testFindByIdFindsExistingStore()
    {
        $store = $this->storeRepo->findById(1);
        $this->assertIsArray($store);
        $this->assertEquals('Oak Ridge Armory', $store['name']);
    }

    public function testFindByIdReturnsFalseForNonExistentStore()
    {
        $store = $this->storeRepo->findById(999);
        $this->assertFalse($store);
    }

    public function testFindAllReturnsAllStores()
    {
        $result = $this->storeRepo->findAll();
        $this->assertCount(5, $result['data']);
        $this->assertEquals(5, $result['total']);
    }

    public function testSaveCreatesNewStore()
    {
        $data = [
            'name' => 'Test Store',
            'slug' => 'test-store',
            'address_line1' => '123 Test St',
            'address_line2' => 'Suite 100',
            'city' => 'Testville',
            'state_region' => 'TS',
            'country' => 'Testland',
            'phone' => '555-1234',
            'email' => 'test@store.com'
        ];

        $result = $this->storeRepo->save($data);
        $this->assertTrue($result);

        $pdo = \Database::getConnection();
        $stmt = $pdo->query("SELECT * FROM stores WHERE slug = 'test-store'");
        $newStore = $stmt->fetch();

        $this->assertEquals('Test Store', $newStore['name']);
        $this->assertEquals('test@store.com', $newStore['email']);
    }

    public function testUpdateModifiesExistingStore()
    {
        $store = $this->storeRepo->findById(1);
        $data = [
            'name' => 'Updated Store Name',
            'city' => 'Updated City',
        ];

        $result = $this->storeRepo->update($data, $store, 1);
        $this->assertTrue($result);

        $updatedStore = $this->storeRepo->findById(1);
        $this->assertEquals('Updated Store Name', $updatedStore['name']);
        $this->assertEquals('Updated City', $updatedStore['city']);
        $this->assertEquals('USA', $updatedStore['country']); // Ensure other fields are untouched
    }

    public function testDeleteRemovesStore()
    {
        $result = $this->storeRepo->delete(1);
        $this->assertTrue($result);

        $deletedStore = $this->storeRepo->findById(1);
        $this->assertFalse($deletedStore);
    }
}
