<?php
namespace App\Helpers;

use App\Models\Product;

class ProductStoreHelper {
    /**
     * @var array
     */
    private $product;

    /**
     * Constructor method
     * 
     * @param array $product
     */
    public function __construct($product = []) {
        $this->product = $product;
    }

    /**
     * Sincroniza o produto retornado no DB local
     * 
     * @param boolean $stockSync Informa se deve ser sincronizado tambem o estoque deste produto
     * @return TRUE em caso de sucesso, FALSE caso haja falha
     */
    public function syncToDatabase($stockSync = true) {
        $integrationId = $this->product['integration_id'];

        $product = Product::firstOrNew(['integration_product_id' => $integrationId])
            ->fill( $this->normalizeData() );

        $success = $product->save();

        // sincronizacao de estoques do produto
        if ( $success && $stockSync ) {
            $stockSync = new StockStoreHelper($product);
            $success = $stockSync->synchronizeStocks();
        }

        return $success;
    }

    /**
     * Converte dados do produto retornados pela API para o DB
     * @return array
     */
    private function normalizeData() {
        $data = $this->product;

        return [
            'code' => $data['code'],
            'name' => $data['name'],
            'description' => $data['description'],
            'composition' => $data['composition'],
            'price' => $data['price'],
            'promotion' => $data['promotion'],
            'active' => $data['active'],
            'brand_name' => $data['brand_name'],
            'size_group_name' => $data['size_group_name'],
            'weight' => $data['weight'],
            'height' => $data['height'],
            'width' => $data['width'],
            'length' => $data['length'],
        ];
    }
}

