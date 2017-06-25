<?php
include '..'. DIRECTORY_SEPARATOR . 'vendor' . DIRECTORY_SEPARATOR . 'autoload.php';

use OnCash\Client;

$client = new Client([
    "api_token" => 'TOKEN_HERE'
]);

//Lists

$methods = $client
    ->methods()//instance of OnCash\Service\MethodService
    ->setPerPage(5)//instance of OnCash\Service\MethodService
    ->setFilter([])//instance of OnCash\Service\MethodService
    ->setRequire([])//instance of OnCash\Service\MethodService
    ->setSort([])//instance of OnCash\Service\MethodService
    ->index()//instance of OnCash\Collection\ApiCollection
    ->all(); //array of OnCash\Model\MethodModel

$methods_1 = $client
    ->methods()//instance of OnCash\Service\MethodService
    ->all(); //array of OnCash\Model\MethodModel

$methods_2 = $client
    ->methods//instance of OnCash\Collection\ApiCollection
    ->all(); //array of OnCash\Model\MethodModel

#$methods == $methods_1 == $methods_2

//Detail

$method_id = 4;

$method_1 = $client
    ->methods($method_id); //instance of OnCash\Model\MethodModel WITHOUT PRELOADING! For relationship support only

$method_2 = $client
    ->methods($method_id, true); //instance of OnCash\Model\MethodModel with preloading

#$method_1 != $method_2