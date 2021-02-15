<?php

use Saulmoralespa\Ecollect\Client;
use PHPUnit\Framework\TestCase;

class EcollectTest extends TestCase
{
    public $ecollect;

    protected function setUp()
    {
        $dotenv = Dotenv\Dotenv::createMutable(__DIR__ . '/../');
        $dotenv->load();

        $entityCode = $_ENV['ENTITY_CODE'];
        $apiKey = $_ENV['APIKEY'];
        $this->ecollect = new Client($entityCode, $apiKey);
        $this->ecollect->sandboxMode(true);
    }

    public function testGetToken()
    {
        $response = $this->ecollect->getSessionToken();
        $this->assertAttributeNotEmpty("SessionToken", $response);
        $this->assertAttributeNotEmpty("ReturnCode", $response, "SUCCESS");
    }

    public function testCreateTransactionPayment()
    {
        $params = [
            "SrvCode" => 201,
            "TransValue" => "100",
            "SrvCurrency" => "COP",
            "URLRedirect" => "https://example.com",
            "URLResponse" => "https://webhook.site/280672f3-b5f1-4b56-afb5-40f907b97f03",
            "LangCode" => "ES",
            "ReferenceArray" => [
                "1325235563",
                "order número",
                "CC",
                "Andres Perez",
                "3154545624",
                "andresperez@gmail.com",
                "1"
            ]
        ];

        $response = $this->ecollect->createTransactionPayment($params);
        $this->assertAttributeNotEmpty("eCollectUrl", $response);
    }

    public function testGetTransactionInformation()
    {
        $tickedId = "271327";
        $response = $this->ecollect->getTransactionInformation($tickedId);
        $this->assertAttributeNotEmpty("TicketId", $response, $tickedId);;
    }
}