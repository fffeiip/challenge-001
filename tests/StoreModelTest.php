<?php
use PHPUnit\Framework\TestCase;
use App\Models\Store;

class StoreModelTest extends TestCase
{
    public static function setUpBeforeClass(): void
    {
        // ensure migrations + seeds have run
        passthru('php ' . escapeshellarg(__DIR__ . '/../scripts/init.php'));
    }

    public function testWhereInReturnsExpectedStores()
    {
        // we know seed inserted stores with IDs 1–5
        $stores = Store::whereIn('id', [1,2,3]);
        $this->assertCount(3, $stores, 'Should return 3 stores');
        foreach ($stores as $store) {
            $this->assertInstanceOf(Store::class, $store, 'Each item should be a Store instance');
            $this->assertContains($store->id, [1,2,3], 'Store ID should be in the specified list');
        }
    }
}
