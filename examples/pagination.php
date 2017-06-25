<?php
include '..'. DIRECTORY_SEPARATOR . 'vendor' . DIRECTORY_SEPARATOR . 'autoload.php';

use OnCash\Client;

$client = new Client([
    "api_token" => 'TOKEN_HERE'
]);

$service = $client
    ->methods(); //instance of OnCash\Service\MethodService

$method_collection = $service
    ->setPerPage(5)//5 items per page
    ->index();  //instance of OnCash\Collection\ApiCollection

printf('Page count: %d%s', $method_collection->getMeta()->last_page, PHP_EOL);
printf('Items count: %d%s', count($method_collection), PHP_EOL); //items count from meta information

//Below is the prototype method $method_collection->all();

$items_count = count($method_collection); //items count from meta information
$items = [];

for ($i = 1; $i <= $method_collection->getMeta()->last_page; $i++) {
    $part = $method_collection->loadPage($i);
    $items += $part; //array merge with preserve keys
    printf('Page %d loaded. Items: %d%s', $i, count($part), PHP_EOL);
}

if (count($items) == count($method_collection)) {
    printf('Items loaded successful%s', PHP_EOL);
} else {
    printf('Items loading fail%s', PHP_EOL);
}
