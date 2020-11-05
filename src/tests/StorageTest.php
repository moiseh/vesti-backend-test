<?php

use Laravel\Lumen\Testing\DatabaseMigrations;
use App\Helpers\ProductStoreHelper;
use App\Helpers\StockStoreHelper;
use App\Models\Product;
use App\Models\Stock;

class StorageTest extends TestCase {
    const INTEGRATION_ID = 62659;

    use DatabaseMigrations;

    public function testProductStore() {
        $productStore = new ProductStoreHelper( $this->buildTestData() );
        $productStore->syncToDatabase(false);

        $this->assertCount( 1, Product::all() );
    }

    public function testStockStore() {
        $data = $this->buildTestData();
        $data['integration_product_id'] = self::INTEGRATION_ID;
        $product = Product::create($data);

        $stockStore = new StockStoreHelper( $product );
        $stockStore->synchronizeStocks();

        $this->assertCount( 6, Stock::all() );
    }

    /**
     * @return array
     */
    private function buildTestData() {
        return [
            'integration_id' => self::INTEGRATION_ID,
            'code' => 'ABC',
            'name' => 'Some name',
            'description' => 'Desc',
            'composition' => 'Composition',
            'price' => 22.5,
            'promotion' => false,
            'active' => true,
            'brand_name' => 'Brand XYZ',
            'size_group_name' => 'Group XYZ',
            'weight' => 10,
            'height' => 12,
            'width' => 14,
            'length' => 16,
        ];
    }
}
