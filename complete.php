<?php
include_once('config.php');
$token = $_GET['token'];

$transaction = $sly->complete_transaction($token);

header("Content-type: application/json");
print $transaction;
?>