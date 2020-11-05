<?php
namespace App\Helpers;

class ViewProductApiHelper {
    /**
     * @var int
     */
    private $integrationId;

    /**
     * Constructor method
     */
    public function __construct($integrationId) {
        $this->integrationId = $integrationId;
    }

    /**
     * Retorna os itens (packs) do produto
     * 
     * @return array
     */
    public function getPackItems() {
        $response = $this->callProductView();
        $items = [];

        if ( !empty($response['packs']) ) {
            foreach ( $response['packs'] as $pack ) {
                $items = array_merge($items, $pack['itens']);
            }
        }

        return $items;
    }

    /**
     * Obtem visualizacao de produto
     * 
     * @return array
     * @throws Exception
     */
    private function callProductView() {
        $client = new ApiClientHelper();
        $apiUrl = $this->buildProductRequestUrl();
        
        return  $client->getJsonResponse($apiUrl);
    }

    /**
     * Monta a string da URL de requisicao
     * 
     * @return string
     */
    private function buildProductRequestUrl() {
        $args = [];
        $args['scheme_url'] = env('API_SCHEME');

        return env('API_URL') . '/products/' . $this->integrationId . '?' . http_build_query($args);
    }
}

