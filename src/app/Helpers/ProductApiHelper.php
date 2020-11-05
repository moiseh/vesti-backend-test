<?php
namespace App\Helpers;

use Exception;
use GuzzleHttp\Client;

class ProductApiHelper {
    /**
     * @var string
     */
    private $date;

    /**
     * @var string
     */
    private $hour;

    /**
     * Constructor method
     * 
     * @param string $date
     * @param string $hour
     */
    public function __construct($date = null, $hour = null) {
        $this->date = $date;
        $this->hour = $hour;
    }

    /**
     * Busca lista de produtos da API remota
     * 
     * @return array Lista de produtos obtido da API
     */
    public function fetchRemoteProducts($products = [], $page = 1) {
        $response = $this->callProductsList($page);
        $body = $response['body']['response'];
        $products = $body['data'];
        $currentPage = $body['current_page'];
        $lastPage = $body['last_page'];
        $hasNextPage = ( $currentPage < $lastPage );
        
        // enquanto houver proximos produtos, segue chamando funcao recursiva
        if ( $hasNextPage ) {
            $products = array_merge($products, $this->fetchRemoteProducts($products, ++$page));
        }

        return $products;
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
                'authorization' => "Bearer {$token}",
            ]
        ]);

        if ( $result->getStatusCode() != 200 ) {
            throw new Exception('Erro na requisição.');
        }

        $response = json_decode($result->getBody(), true);

        if ( empty($response['body']['response']) ) {
            if ( !empty($response['body']['result']['messages'])) {
                $messages = [];
                foreach ( $response['body']['result']['messages'] as $field => $message ) {
                    $messages[] = $field . ': ' . implode(', ', $message);
                }

                throw new Exception( sprintf("Erro no retorno de produtos:\n%s", implode("\n", $messages)) );
            }
            else {
                throw new Exception('Ocorreu algum erro no retorno de produtos :(');
            }
        }

        return $response;
    }

    /**
     * Monta a string da URL de requisicao
     * 
     * @return string
     */
    private function buildProductsRequestUrl($page = 1) {
        $args = [];
        $args['scheme_url'] = env('API_SCHEME');
        $args['page'] = $page;

        // inclui parametro date opcional
        if ( !empty($this->date) ) {
            $args['date'] = $this->date;
        }

        // inclui parametro hour opcional
        if ( !empty($this->hour) ) {
            $args['hour'] = $this->hour;
        }

        return env('API_URL') . '/products?' . http_build_query($args);
    }
}

