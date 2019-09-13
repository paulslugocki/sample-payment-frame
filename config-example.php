<?php
	include_once('vendor/autoload.php');
	include_once('spreedly.php');

	// TourCMS API

	use TourCMS\Utils\TourCMS as TourCMS;

	$tourcms_api_key = '';

	$marketplace_id = 0;

	$channel_id = 0;

	$tourcms = new TourCMS($marketplace_id, $tourcms_api_key, 'simplexml');

	$sly_booking_id = 0;

	$sly_currency = 'EUR';

	//$tourcms->set_base_url('http://localhost/api.tourcms.com');

	// Set up Spreedly object

	// Environment (e.g. Test)
	$sly_environment = '9xyyNh89TMduskMtOTh48V2wrJ8';


	$sly_redirect_url = 'https://www.example.com';

	$sly_callback_url = 'https://www.example.com';

	$sly = new Spreedly($sly_environment, $sly_access_secret, $sly_signing_secret);

?>