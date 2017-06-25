<?php
include '..'. DIRECTORY_SEPARATOR . 'vendor' . DIRECTORY_SEPARATOR . 'autoload.php';

use OnCash\Client;

$client = new Client([
    "api_token" => 'TOKEN_HERE'
]);

$service = $client
    ->methods(); //instance of OnCash\Service\MethodService

$methods = $service
    ->setFilter([
        ['id', 'between', [1, 50]], //id between 1 and 50
        ['currency_code', 'in', [840, 978]], //USD and EUR
        ['name', 'like', 'W1%'] //name start with W1
    ])
    ->all();

foreach ($methods as $method) {
    printf('Found: %s%s', $method->name, PHP_EOL);
}
