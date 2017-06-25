<?php
include '..'. DIRECTORY_SEPARATOR . 'vendor' . DIRECTORY_SEPARATOR . 'autoload.php';

use OnCash\Client;

$client = new Client([
    "api_token" => 'TOKEN_HERE'
]);

$service = $client
    ->methods(); //instance of OnCash\Service\MethodService

$methods = $service
    ->setRequire(['currency_code'])
    ->all();

printf('Methods with currency codes:%s', PHP_EOL);

foreach ($methods as $method) {
    printf('%s => %s%s', $method->name, $method->currency_code, PHP_EOL);
}
