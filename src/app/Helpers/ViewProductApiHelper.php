<?php
namespace App\Helpers;

use Exception;
use GuzzleHttp\Client;

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
        static $token = null;

        // evita chamar demasiadamente a busca pelo token
        if ( is_null($token) ) {
            $auth = new AuthApiHelper();
            $token = $auth->getToken();
        }

        $client = new Client();
        $apiUrl = $this->buildProductRequestUrl();

        $result = $client->get($apiUrl, [
            'headers' => [
                'authorization' => "Bearer {$token}",
            ]
        ]);

        if ( $result->getStatusCode() != 200 ) {
            throw new Exception('Erro na requisição.');
        }

        $response = json_decode($result->getBody(), true);

        if ( empty($response['body']['response']) ) {
            throw new Exception('Aparentemente houve algum erro no retorno da visualizacao do produto.');
        }

        return $response['body']['response'];
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

