<?php
include '..'. DIRECTORY_SEPARATOR . 'vendor' . DIRECTORY_SEPARATOR . 'autoload.php';

use OnCash\Client;

$client = new Client([
    "api_token" => 'TOKEN_HERE'
]);

$service = $client
    ->methods(); //instance of OnCash\Service\MethodService

$methods = $service
    ->setSort([
        ['id', 'DESC']
    ])
    ->all();

printf('Current sort order:%s', PHP_EOL);

foreach ($methods as $method) {
    printf('%s%s', $method->name, PHP_EOL);
}
