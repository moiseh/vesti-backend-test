<?php
namespace App\Helpers;

use Exception;

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
        $client = new ApiClientHelper();
        $apiProductsUrl = $this->buildProductsRequestUrl($page);
        $response = $client->getJsonResponse($apiProductsUrl);

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

