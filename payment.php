<?php
include_once('config.php');

$payment_data = json_decode(file_get_contents('php://input'), true);

// Create a new SimpleXMLElement to hold the payment details
$payment = new SimpleXMLElement('<payment />');

// Set the Spreedly payment method token to use
$payment->addChild('spreedly_payment_method', $payment_data["payment_method_token"]);

// Must set the Booking ID on the XML, so TourCMS knows which to update
$payment->addChild('booking_id', 29764);

//$payment->addChild('paid_by', 'C');

// Must set the value of the payment
$payment->addChild('payment_value', $payment_data['amount']);

// Must set the currency
$payment->addChild('payment_currency', 'EUR');

$payment->addChild('browser_info', $payment_data["browser_info"]);

$payment->addChild('attempt_3dsecure', $payment_data["attempt_3dsecure"]);

$payment->addChild('three_ds_version', $payment_data["three_ds_version"]);

$payment->addChild('redirect_url', $sly_redirect_url);

error_log($payment->asXml());

//exit();

// Call TourCMS API, charging the card
$response = $tourcms->spreedly_create_payment($payment, $channel_id);

error_log("result");

error_log($response->asXml());

$transaction = array(
  'tourcms_transaction_id' => (string)$response->booking->transaction->transaction_id,
  'state' => (string)$response->booking->transaction->state,
  'succeeded' => (string)$response->booking->transaction->succeeded,
  'message' => (string)$response->booking->transaction->message,
  'token' => (string)$response->booking->transaction->transaction_token,
  'required_action' => (string)$response->booking->transaction->required_action,
  'checkout_url' => (string)$response->booking->transaction->checkout_url,
  'checkout_form' => array(
    'cdata' => htmlspecialchars_decode((string)$response->booking->transaction->checkout_form)
  ),
  'device_fingerprint_form' => array(
      'cdata' => htmlspecialchars_decode((string)$response->booking->transaction->device_fingerprint_form)
  ),
  'challenge_url' => (string)$response->booking->transaction->challenge_url,
  'challenge_form' => array(
      'cdata' => htmlspecialchars_decode((string)$response->booking->transaction->challenge_form)
  )
);

header("Content-type: application/json");
echo json_encode($transaction);
?>
