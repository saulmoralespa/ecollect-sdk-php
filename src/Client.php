<?php


namespace Saulmoralespa\Ecollect;

use GuzzleHttp\Client as GuzzleClient;
use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\Utils;

class Client
{
    const API_BASE = "https://e-collect.com/app_express/api/";
    const SANDBOX_API_BASE = "https://test1.e-collect.com/app_express/api/";

    protected static $_sandbox = false;
    protected $_entityCode;
    protected $_apiKey;

    public function __construct($entityCode, $apiKey)
    {

        $this->_entityCode = $entityCode;
        $this->_apiKey = $apiKey;
    }

    public function sandboxMode($status = false)
    {
        if ($status)
            self::$_sandbox = true;
    }

    public function client()
    {
        return new GuzzleClient([
            'base_uri' => self::$_sandbox ? self::SANDBOX_API_BASE : self::API_BASE
        ]);
    }

    public function getSessionToken()
    {
        try{
            $response = $this->client()->post(__FUNCTION__,
                [
                    "headers" => [
                        "Content-Type" => "application/json"
                    ],
                    "json" => [
                        "EntityCode" => $this->_entityCode,
                        "ApiKey" => $this->_apiKey
                    ]
                ]);
            return self::responseJson($response);
        }catch (RequestException $exception){
            throw new \Exception($exception->getMessage());
        }
    }

    public function createTransactionPayment(array $params)
    {
        try{

            $params = array_merge(
               [
                   "EntityCode" => $this->_entityCode,
                   "SessionToken" => $this->getSessionToken()->SessionToken
               ],
               $params
            );

            $response = $this->client()->post(__FUNCTION__,
                [
                    "headers" => [
                        "Content-Type" => "application/json"
                    ],
                    "json" => $params
                ]);
            return self::responseJson($response);
        }catch (RequestException $exception){
            throw new \Exception($exception->getMessage());
        }
    }

    public function getTransactionInformation($tickeId)
    {
        try{

            $params = array_merge(
                [
                    "EntityCode" => $this->_entityCode,
                    "SessionToken" => $this->getSessionToken()->SessionToken,
                    "TicketId" => $tickeId
                ]
            );

            $response = $this->client()->post(__FUNCTION__,
                [
                    "headers" => [
                        "Content-Type" => "application/json"
                    ],
                    "json" => $params
                ]);
            return self::responseJson($response);
        }catch (RequestException $exception){
            throw new \Exception($exception->getMessage());
        }
    }

    public static function responseJson($response)
    {
        return Utils::jsonDecode(
            $response->getBody()->getContents()
        );
    }
}