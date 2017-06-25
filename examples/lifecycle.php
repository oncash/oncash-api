<?php
include '..'. DIRECTORY_SEPARATOR . 'vendor' . DIRECTORY_SEPARATOR . 'autoload.php';

use OnCash\Client;

$client = new Client([
    "api_token" => 'TOKEN_HERE'
]);

$merchant_id = 534;
$order_id = 3;
$notification_id = 1;


//Attention! Below is a bad example to use! There is only to improve understanding of internal calls
//This is prototype of
//    $client
//        ->merchants($merchant_id)
//        ->orders($order_id)
//        ->notifications($notification_id, true);


$api_client = $client->getClient();

$merchant_service = new OnCash\Service\MerchantService($api_client, null);

$merchant = OnCash\Model\MerchantModel::get($api_client, $merchant_service, $merchant_id);

$order_service = new OnCash\Service\OrderService($api_client, $merchant);

$order = OnCash\Model\OrderModel::get($api_client, $order_service, $order_id);

$notification_service = new OnCash\Service\NotificationService($api_client, $order);

$notification = OnCash\Model\NotificationModel::find($api_client, $notification_service, $notification_id);