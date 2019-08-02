<?php
include_once('config.php');

$payment_data = json_decode(file_get_contents('php://input'), true);

$transaction_details = array(
    'amount' => $payment_data["amount"],
    'currency_code' => $payment_data["currency_code"],
    'payment_method_token' => $payment_data["payment_method_token"],
    'order_id' => $payment_data["order_id"],
    'browser_info'=> $payment_data["browser_info"],
    'attempt_3dsecure' => $payment_data["attempt_3dsecure"],
    'three_ds_version' => $payment_data["three_ds_version"],
    'redirect_url' => $sly_redirect_url,
    'callback_url' => $sly_callback_url
);

// print_r($transaction_details);
// exit();

$transaction = $sly->purchase($sly_gateway, $transaction_details);

header("Content-type: text/xml");
echo $transaction->asXml();
?>