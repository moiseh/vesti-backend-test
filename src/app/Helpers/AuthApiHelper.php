<?php
namespace App\Helpers;

use Exception;
use GuzzleHttp\Client;
use Illuminate\Support\Facades\Cache;

class AuthApiHelper {
    /**
     * @var string
     */
    private $loginApiUrl;

    /**
     * @var string
     */
    private $apiEmail;

    /**
     * @var string
     */
    private $apiPassword;

    /**
     * Constructor method
     * 
     * @param string $loginApiUrl
     * @param string $apiEmail
     * @param string $apiPassword
     */
    public function __construct($loginApiUrl = null, $apiEmail = null, $apiPassword = null) {
        if ( empty($loginApiUrl) ) {
            $this->loginApiUrl = env('API_URL') . '/login';
        }

        if ( empty($apiEmail) ) {
            $this->apiEmail = env('API_EMAIL');
        }

        if ( empty($apiPassword) ) {
            $this->apiPassword = env('API_PASSWORD');
        }
    }

    /**
     * Obtem a string de token do cache.
     * Caso necessario, fazer consulta remota para buscar um novo token.
     * 
     * @return string
     */
    public function getToken() {
        $token = Cache::get('api_token');

        if ( empty($token) ) {
            $token = $this->getTokenResponse();
            Cache::put('api_token', $token, 600); // grava token por 10 minutos
        }

        return $token;
    }

    /**
     * Pede um novo token para API.
     * 
     * @return string
     */
    private function getTokenResponse() {
        $client = new Client();
        $result = $client->post($this->loginApiUrl, [
            'form_params' => [
                'email' => $this->apiEmail,
                'password' => $this->apiPassword,
            ]
        ]);

        if ( $result->getStatusCode() != 200 ) {
            throw new Exception('Erro na requisição.');
        }

        $response = json_decode($result->getBody(), true);
        
        if ( isset($response['success']) && ( $response['success'] === false ) ) {
            throw new Exception( sprintf('Erro retornado: %s', $response['message']) );
        }

        if ( empty($response['token']) ) {
            throw new Exception('Erro ao obter token de autenticacao.');
        }

        return $response['token'];
    }
}

