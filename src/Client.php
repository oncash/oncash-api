<?php

namespace OnCash;

use OnCash\Http\HttpClientInterface;
use OnCash\Http\SocketHttpClient;
use OnCash\Core\ApiClient;
use OnCash\Traits\RelationshipTrait;

class Client
{
    use RelationshipTrait;

    private $base_url = 'http://api.oncash.net/v1/';
    private $api_client = null;

    public function __construct($config = null, HttpClientInterface $http_client = null)
    {
        $base_content = [];
        if (is_array($config)) {
            if (array_key_exists('api_token', $config)) {
                $base_content['api_token'] = $config['api_token'];
            }
        }
        if (is_null($http_client)) {
            $http_client = new SocketHttpClient();
        }
        $this->api_client = new ApiClient($http_client, $this->base_url, $base_content);
    }

    public function getClient()
    {
        return $this->api_client;
    }

    public function merchants()
    {
        return $this->bind('OnCash\\Service\\MerchantService', func_get_args());
    }

    public function currencies()
    {
        return $this->bind('OnCash\\Service\\CurrencyService', func_get_args());
    }

    public function methods()
    {
        return $this->bind('OnCash\\Service\\MethodService', func_get_args());
    }

    public function tariffs()
    {
        return $this->bind('OnCash\\Service\\TariffService', func_get_args());
    }
}