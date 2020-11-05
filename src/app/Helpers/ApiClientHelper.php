<?php
namespace App\Helpers;

use Exception;
use GuzzleHttp\Client;

class ApiClientHelper extends Client {
    /**
     * Executa chamada GET para API
     */
    public function getJsonResponse($url, $options = []) {
        $options = $this->injectRequiredOptions($options);
        $result = $this->get($url, $options);

        if ( $result->getStatusCode() != 200 ) {
            throw new Exception('Erro na requisição.');
        }

        $response = json_decode($result->getBody(), true);

        $this->validateResponseBody($response);

        return $response;
    }

    /**
     * Injeta headers e opcoes obrigatorias para o request
     * 
     * @return array
     */
    private function injectRequiredOptions($options = []) {
        // adiciona o Bearer token auth nos headers, caso ainda nao existir
        if ( empty($options['headers']['authorization']) ) {
            static $token = null;

            // evita chamar demasiadamente a busca pelo token
            if ( is_null($token) ) {
                $auth = new AuthApiHelper();
                $token = $auth->getToken();
            }

            $options['headers']['authorization'] = "Bearer {$token}";
        }

        return $options;
    }

    /**
     * Valida retorno do JSON
     * 
     * @throws Exception
     */
    private function validateResponseBody($response = []) {
        if ( empty($response['body']['response']) ) {
            if ( !empty($response['body']['result']['messages'])) {
                $messages = [];
                foreach ( $response['body']['result']['messages'] as $field => $message ) {
                    $messages[] = $field . ': ' . implode(', ', $message);
                }

                throw new Exception( sprintf("Erro no retorno:\n%s", implode("\n", $messages)) );
            }
            else {
                throw new Exception('Ocorreu algum erro na chamada :(');
            }
        }
    }
}

