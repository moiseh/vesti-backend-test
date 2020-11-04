<?php
namespace App\Console\Commands;

use App\Helpers\AuthApiHelper;
use GuzzleHttp\Client;
use Illuminate\Console\Command;
use Exception;

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
     * Contador de total produtos sincronizados.
     * 
     * @var int
     */
    private $totalSync = 0;

    /**
     * Execute the console command.
     *
     * @return void
     */
    public function handle() {
        $page = 1;

        // enquanto houver proximas paginas, segue sincronizando os produtos
        while ( $this->fetchRemoteProducts($page) ) {
            $page ++;
        }

        $this->info( sprintf('Fim. Um total de %s produtos foram sincronizados.', $this->totalSync) );
    }

    /**
     * Busca lista de produtos da API remota
     * 
     * @return boolean TRUE caso exisir proxima pagina, FALSE caso for a ultima
     */
    private function fetchRemoteProducts($page = 1) {
        $response = $this->callProductsList($page);

        $productsInfo = $response['body']['response'];
        $productsData = $productsInfo['data'];
        $currentPage = $productsInfo['current_page'];
        $lastPage = $productsInfo['last_page'];

        foreach ( $productsData as $product ) {
            if ( $this->syncProductToDb($product) ) {
                $this->totalSync ++;
            }
        }

        $hasNextPage = ( $currentPage < $lastPage );
        return $hasNextPage;
    }

    /**
     * Obtem retorno de requisicao de produtos
     * 
     * @return array
     * @throws Exception
     */
    private function callProductsList($page) {
        static $token = null;

        // evita chamar demasiadamente a busca pelo token
        if ( is_null($token) ) {
            $auth = new AuthApiHelper();
            $token = $auth->getToken();
        }

        $client = new Client();
        $apiProductsUrl = $this->buildProductsRequestUrl($page);

        $result = $client->get($apiProductsUrl, [
            'headers' => [
                "authorization" => "Bearer {$token}",
            ]
        ]);

        if ( $result->getStatusCode() != 200 ) {
            throw new Exception('Erro na requisição.');
        }

        $response = json_decode($result->getBody(), true);

        if ( empty($response['body']['response']) ) {
            throw new Exception('Aparentemente houve algum erro no retorno de produtos.');
        }

        return $response;
    }

    /**
     * Sincroniza o produto retornado no DB local
     * 
     * @return TRUE em caso de sucesso, FALSE caso haja falha
     */
    private function syncProductToDb($product = []) {
        // dd($product);

        return true;
    }

    /**
     * Monta a string da URL de requisicao.
     * 
     * @return string
     */
    private function buildProductsRequestUrl($page = 1) {
        $date = $this->argument('date');
        $hour = $this->argument('hour');

        $urlArgs = [];
        $urlArgs['scheme_url'] = env('API_SCHEME');
        $urlArgs['page'] = $page;

        // inclui parametro date opcional
        if ( !empty($date) ) {
            $urlArgs['date'] = $date;
        }

        // inclui parametro hour opcional
        if ( !empty($hour) ) {
            $urlArgs['hour'] = $hour;
        }

        return env('API_URL') . '/products?' . http_build_query($urlArgs);
    }
}
