<?php
include_once('config.php');
$transaction_id = $_GET['transaction_id'];

$response = $tourcms->request('/c/booking/gatewaytransaction/spreedlycomplete.xml?id=' . $transaction_id, $channel_id, 'POST', null);
error_log("FINAL PAGE ". print_r($response, TRUE));

$transaction = array(
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
  'challenge_url' => (string)$response->booking->transaction->challenge_url
);

header("Content-type: application/json");
echo json_encode($transaction);
//print $response->booking->transaction->transaction_json;
?>