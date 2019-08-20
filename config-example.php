<?php
	include_once('vendor/autoload.php');
	include_once('spreedly.php');

	// Set up Spreedly object

	// Environment (e.g. Test)
	$sly_environment = '';

	// Access secret (can be personal or app specific)
	$sly_access_secret = '';

	// Optional, used for signing callbacks, e.g. for PayPal etc
	$sly_signing_secret = '';

  $sly_return_url = 'http://tourcmsdev.macbook/scratch/sample-payment-iframe/return.php';

	$sly_callback_url = 'http://tourcmsdev.macbook/scratch/sample-payment-iframe/return.php';

	$sly = new Spreedly($sly_environment, $sly_access_secret, $sly_signing_secret);

	// TourCMS API

	use TourCMS\Utils\TourCMS as TourCMS;

	$tourcms_api_key = '';

	$marketplace_id = 0;

	$channel_id = 0;

?>