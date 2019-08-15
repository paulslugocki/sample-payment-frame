<?php
include('config.php');
?><!DOCTYPE html>
<html>
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width">
  <title>Spreedly Sample iFrame Payment Page</title>
  <link rel="stylesheet" type="text/css" href="css/styles.css">
  <script src="https://core.spreedly.com/iframe/iframe-v1.min.js"></script>
  <script src="js/3dsecure2.js"></script>
</head>

<body>

  <input type="hidden" id="aceptheadercapture" value=<?php echo htmlspecialchars($_SERVER["HTTP_ACCEPT"]); ?>>

  <form id="payment-form" accept-charset="UTF-8" class="spf-form" method="POST"
    action="#" onsubmit="submitPaymentForm(); return false;">

    <input type="hidden" name="payment_method_token" id="payment_method_token" value="" />

    <fieldset class="spf-fs-name">
      <h2>Name</h2>
      <div class="spf-field spf-field-fname">
        <label class="internal" for="spf-fname">First Name</label>
        <input type="text" class="spf-input-text" id="first_name">
      </div>
      <div class="spf-field spf-field-lname">
        <label class="internal" for="spf-lname">Last Name</label>
        <input type="text" class="spf-input-text" id="last_name">
      </div>
    </fieldset>

    <!-- If you want billing address, uncomment -->
    <!--
    <fieldset class="spf-fs-address">
      <h2>Billing Address</h2>
      <div class="spf-field spf-field-street">
        <label class="internal" for="spf-street">Street Address</label>
        <input type="text" class="spf-input-text" id="address1">
      </div>
      <div class="spf-field spf-field-street-2">
        <input type="text" class="spf-input-text" id="address2">
      </div>
      <div class="spf-field spf-field-city">
        <label class="internal" for="spf-city">City</label>
        <input type="text" class="spf-input-text" id="city">
      </div>
      <div class="spf-field spf-field-state">
        <label class="internal" for="spf-state">State</label>
        <input type="text" class="spf-input-text" id="state">
      </div>
      <div class="spf-field spf-field-postcode">
        <label class="internal" for="spf-postcode">Zip Code</label>
        <input type="text" class="spf-input-text" id="zip">
      </div>
      <div class="spf-field spf-field-country">
        <label class="internal" for="spf-country">Country</label>
        <input type="text" class="spf-input-text" id="country">
      </div>
    </fieldset>
    -->

    <fieldset class="spf-fs-cc">
      <h2>Payment Details</h2>

      <div class="spf-field spf-field-exp">
        <label>Amount</label>
        <div class="spf-field-group">
          <select id="amount-input" class="spf-input-text" style="width: 90%">
            <option value="3001">3001 - 3D Secure 2 full frictionless flow (immediate transaction flow)</option>
            <option value="3002">3002 - Fallback from 3DS2 to 3DS1</option>
            <option value="3003">3003 - 3D Secure device fingerprint flow with direct authorize (requires lifecycle)</option>
            <option value="3004">3004 - 3D Secure device fingerprint flow to challenge (requires lifecycle and completion call)</option>
            <option value="3103">3103 - 3D Secure device fingerprint flow with forced failure</option>
            <option value="3104">3104 - 3D Secure challenge flow with forced failure</option>
          </select>
          <!-- <label class="spf-label-secondary" for="spf-exp-m">Month (MM)</label> -->
        </div>
      </div>

      <div class="spf-field">
        <label class="spf-field-group spf-number">Credit Card Number</label>
        <label class="spf-field-group spf-verification_value">CVV</label>
        <div id="spreedly-number-test" class="spf-field-group spf-number spf-field-cc">
        </div>
        <div id="spreedly-cvv-test" class="spf-field-group spf-verification_value spf-field-cc">
        </div>
      </div>

      <div class="spf-field spf-field-exp">
        <label>Expiration Date</label>
        <div class="spf-field-group spf-month">
          <input type="text" class="spf-input-text spf-exp" id="month" size="3" maxlength="2" placeholder="MM">
          <!-- <label class="spf-label-secondary" for="spf-exp-m">Month (MM)</label> -->
        </div>
        <span class="spf-exp-divider">/</span>
        <div class="spf-field-group spf-year">
          <input type="text" class="spf-input-text spf-exp" id="year" size="5" maxlength="4" placeholder="YYYY">
          <!-- <label class="spf-label-secondary" for="spf-exp-y">Year (YYYY)</label> -->
        </div>
      </div>
    </fieldset>

    <fieldset class="spf-fs-cc">
      <div class="spf-field">
        Hidden fields:
      <div id="spreedly-threeds-hidden-iframe" style="border: 1px solid red; height: 100px;"></div>

      <div id="spreedly-threeds-challenge-iframe" style="border: 1px solid red;  height: 100px;></div>
      </div>
      <div style="clear: both;">
    </fieldset>


    <fieldset class="spf-field-submit">
      <input type="submit" class="button" value="Submit Payment">
      <div id="message"></div>
      <div id="errors"></div>
    </fieldset>

    <script
      id="spreedly-iframe"
      data-environment-key="<?php echo $sly_environment; ?>"
      data-number-id="spreedly-number-test"
      data-cvv-id="spreedly-cvv-test">
    </script>
  </form>

  <script>
    Spreedly.init();

    Spreedly.on('paymentMethod', function(token, pmData) {
      var tokenField = document.getElementById("payment_method_token");
      tokenField.setAttribute("value", token);
      var masterForm = document.getElementById('payment-form');

      // Normally would now submit the form..
      // masterForm.submit();

      // For demonstration purposes just display the token
      var messageEl = document.getElementById('message');
      messageEl.innerHTML = "Success! The returned payment method token is: " + token;

      var amountBox = document.getElementById("amount-input");

      sendPayment({
		      'amount': amountBox.options[amountBox.selectedIndex].value,
					'currency_code':'USD',
					'payment_method_token':token,
          'order_id': ''
      });
    });

    Spreedly.on('errors', function(errors) {
      var messageEl = document.getElementById('errors');
      var errorBorder = "1px solid red";
      for(var i = 0; i < errors.length; i++) {
        var error = errors[i];
        if(error["attribute"]) {
          var masterFormElement = document.getElementById(error["attribute"]);
          if(masterFormElement) {
            masterFormElement.style.border = errorBorder
          } else {
            Spreedly.setStyle(error["attribute"], "border: " + errorBorder + ";");
          }
        }
        messageEl.innerHTML += error["message"] + "<br/>";
      }
    });

    Spreedly.on('ready', function(frame) {
      Spreedly.setFieldType('number', 'text');
      Spreedly.setFieldType('cvv', 'text');
      Spreedly.setNumberFormat('maskedFormat');
      Spreedly.setStyle('number','width: 67%; border-radius: 3px; border: 1px solid #ccc; padding: .65em .5em; font-size: 91%;');
      Spreedly.setStyle('cvv', 'width: 30%; border-radius: 3px; border: 1px solid #ccc; padding: .65em .5em; font-size: 91%;');
    });

    Spreedly.on('fieldEvent', function(name, event, activeElement, inputData) {
      if (event == 'input') {
        if (inputData["validCvv"]){
          Spreedly.setStyle('cvv', "background-color: #CDFFE6;")
        } else {
          Spreedly.setStyle('cvv', "background-color: #FFFFFF;")
        }
        if (inputData["validNumber"]){
          Spreedly.setStyle('number', "background-color: #CDFFE6;")
        } else {
          Spreedly.setStyle('number', "background-color: #FFFFFF;")
        }
      }
    });

    function submitPaymentForm() {
      var normalBorder = "1px solid #ccc";

      // These are the fields whose values we want to transfer *from* the
      // master form *to* the payment frame form. Add the following if
      // you're displaying the address:
      // ['address1', 'address2', 'city', 'state', 'zip', 'country']
      var paymentMethodFields = ['first_name', 'last_name', 'month', 'year']
      options = {};
      for(var i = 0; i < paymentMethodFields.length; i++) {
        var field = paymentMethodFields[i];

        // Reset existing styles (to clear previous errors)
        var fieldEl = document.getElementById(field);
        fieldEl.style.border = normalBorder;

        // add value to options
        options[field]  = fieldEl.value
      }

      // Reset frame styles
      Spreedly.setStyle('number', "border: " + normalBorder + ";");
      Spreedly.setStyle('cvv', "border: " + normalBorder + ";");

      // Reset previous messages
      document.getElementById('errors').innerHTML = "";
      document.getElementById('message').innerHTML = "";

      // Tokenize!
      Spreedly.tokenizeCreditCard(options);
    }
  </script>
</body>
</html>