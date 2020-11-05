<?php
namespace App\Helpers;

use App\Models\Product;
use App\Models\Stock;

class StockStoreHelper {
    /**
     * @var Product
     */
    private $product;

    /**
     * Constructor method
     * 
     * @param array $product
     */
    public function __construct(Product $product) {
        $this->product = $product;
    }

    /**
     * Executa sincronizacao de estoques do produto
     * 
     * @return boolean
     */
    public function synchronizeStocks() {
        $integrationId = $this->product->integration_product_id;
        $productView = new ViewProductApiHelper( $integrationId );
        $packItems = $productView->getPackItems();

        foreach ( $packItems as $order => $item ) {
            foreach ( $item['sizes'] as $size ) {
                $sku = $size['sku'];
                $row = $this->normalizeData($item, $size, ++$order);

                $stock = Stock::firstOrNew(['sku' => $sku])
                    ->fill( $row );
    
                // em caso de falha ao gravar estoque, retorna falso e interrompe tudo
                if ( !$stock->save() ) {
                    return false;
                }
            }
        }

        return true;
    }

    /**
     * Converte dados do estoque retornados pela API para o DB
     * @return array
     */
    private function normalizeData($item = [], $size = [], $order) {
        return [
            'product_id' => $this->product->id,
            'color_name' => $item['color']['name'],
            'size_name' => $size['key'],
            'quantity' => $size['quantity'],
            'order' => $order,
        ];
    }
}

