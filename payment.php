<?php
include_once('config.php');

$payment_data = json_decode(file_get_contents('php://input'), true);

// Create a new SimpleXMLElement to hold the payment details
$payment = new SimpleXMLElement('<payment />');

// Set the Spreedly payment method token to use
$payment->addChild('spreedly_payment_method', $payment_data["payment_method_token"]);

// Must set the Booking ID on the XML, so TourCMS knows which to update
$payment->addChild('booking_id', 677);

// Must set the value of the payment
$payment->addChild('payment_value', '20');

// Must set the currency
$payment->addChild('payment_currency', 'GBP');

$payment->addChild('browser_info', $payment_data["browser_info"]);

$payment->addChild('attempt_3dsecure', $payment_data["attempt_3dsecure"]);

$payment->addChild('three_ds_version', $payment_data["three_ds_version"]);

$payment->addChild('redirect_url', $sly_redirect_url);

$payment->addChild('callback_url', $sly_callback_url);

error_log($payment->asXml());

//exit();

// Call TourCMS API, charging the card
$result = $tourcms->spreedly_create_payment($payment, $channel_id);

error_log("result");

error_log($result->asXml());

header("Content-type: text/xml");
echo $result->asXml();
?>