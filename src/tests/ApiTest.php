<?php

use App\Helpers\ApiClientHelper;
use App\Helpers\AuthApiHelper;
use App\Helpers\ProductApiHelper;
use App\Helpers\ViewProductApiHelper;

class ApiTest extends TestCase {
    const INTEGRATION_ID = 62659;

    public function testProductApi() {
        $productApi = new ProductApiHelper();
        $products = $productApi->fetchRemoteProducts();

        $this->assertIsArray($products);
    }

    public function testViewProductApi() {
        $productView = new ViewProductApiHelper( self::INTEGRATION_ID );
        $packItems = $productView->getPackItems();

        $this->assertIsArray( $packItems );
        $this->assertEquals( $packItems[0]['color']['name'], 'SUN KISSES' );
    }

    public function testApiClient() {
        try {
            $args = [];
            $args['scheme_url'] = env('API_SCHEME');
            $args['date'] = 'xxxxx'; // define data invalida para provocar um Exception
            $url = env('API_URL') . '/products?' . http_build_query($args);
    
            $client = new ApiClientHelper();
            $response = $client->getJsonResponse($url);
        }
        catch (Exception $e) {
            $this->assertStringContainsString('The date does not match the format', $e->getMessage());
        }
    }

    public function testAuthApi() {
        $auth = new AuthApiHelper();
        $token = $auth->getToken();

        // verifica se tamanho do token retornado esta correto
        $this->assertEquals( strlen($token), 599 );
    }
}
