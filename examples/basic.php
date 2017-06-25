<?php
include '..'. DIRECTORY_SEPARATOR . 'vendor' . DIRECTORY_SEPARATOR . 'autoload.php';

use OnCash\Client;

$merchant_id = 558;

$client = new Client([
    "api_token" => 'TOKEN_HERE'
]);

$orders = $client
    ->merchants($merchant_id)//instance of OnCash\Model\MerchantModel
    ->orders()//instance of OnCash\Service\OrderService
    ->all(); //array of OnCash\Model\OrderModel

foreach ($orders as $id => $order) { //instance of OnCash\Model\OrderModel
    if ($order->is_paid) { //Equivalent $order['is_paid']
        printf('Order #%d is paid.%s', $order->id, PHP_EOL);

        if ($order->is_transferred) {
            printf('Order #%d is transferred.%s', $order->id, PHP_EOL);
        } else {
            printf('Warning! Order #%d is paid, but not transferred. Attempts: %d.%s', $order->id, count($order->notifications), PHP_EOL);
        }
    } else {
        printf('Order #%d is not paid.%s', $order->id, PHP_EOL);
    }

    printf('Order #%d dump:%s', $order->id, PHP_EOL);

    foreach ($order as $key => $value) {
        printf("\t");
        printf('["%s"] => ', $key);
        printf('%s%s', $value, PHP_EOL);
    }
}