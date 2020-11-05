<?php
namespace App\Console\Commands;

use App\Helpers\ProductApiHelper;
use App\Helpers\ProductStoreHelper;
use Illuminate\Console\Command;

class SyncProductCommand extends Command {
    /**
     * Nome do comando
     *
     * @var string
     */
    protected $name = 'sync:products';

    /**
     * Descricao do comando.
     *
     * @var string
     */
    protected $description = "Sincroniza os produtos da API para o db da APP";

    /**
     * Assinatura do comando, incluindo parametros opcionais.
     *
     * @var string
     */
    protected $signature = 'sync:products {date?} {hour?}';

    /**
     * Executa o comando no console.
     *
     * @return void
     */
    public function handle() {
        $productApi = new ProductApiHelper( $this->argument('date'), $this->argument('hour') );
        $products = $productApi->fetchRemoteProducts();
        $synced = 0;

        foreach ( $products as $product ) {
            $productStore = new ProductStoreHelper($product);

            if ( $productStore->syncToDatabase() ) {
                $synced ++;
            }
        }

        $this->info( sprintf('Fim. Um total de %s produtos foram sincronizados.', $synced) );
    }
}
