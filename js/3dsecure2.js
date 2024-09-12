function sendPayment(opts = {}) {

  var browser_size = '01';
  // The accept header from your server side rendered page. You'll need to inject it into the page. Below is an example.
  var acceptHeader = document.getElementById('aceptheadercapture').value;
  console.info(acceptHeader);
  // The request should include the browser data collected by using `Spreedly.ThreeDS.serialize().
  let browser_info = Spreedly.ThreeDS.serialize(
    browser_size,
    acceptHeader
  );

  let defaults = {
  browser_info: browser_info,
  attempt_3dsecure: "true",
  three_ds_version: "2",
  };

  let purchaseParams = Object.assign({}, defaults, opts);

  console.info( purchaseParams );

  // Choose a browser size for your application. This will be the size of the challenge
  // iframe that will be presented to a user. *note* If you're creating a modal, you
  // should make the surrounding DOM node a little larger than the option selected
  // below.
  //
  // '01' - 250px x 400px
  // '02' - 390px x 300px
  // '03' - 500px x 600px
  // '04' - 600px x 400px
  // '05' - fullscreen

  fetch('payment.php', {
      method: 'POST',
      body: JSON.stringify(purchaseParams)
    })
    .then(response => response.json())
    .then(data => checkPaymentResponse(data))
  .catch(error => console.error(error));
}

var checkPaymentResponse = function(data) {
  console.info(data);
  var stateNodeText = data.state;
  console.info(stateNodeText);

  if(stateNodeText == "succeeded") {
    alert("Success");
  } else if(stateNodeText == "error") {
    alert("Error");
  } else if(stateNodeText == "pending") {
    setLifeCycle(data);
  }
}

var setLifeCycle = function(data) {

  console.warn("In lifecycle");

  console.info(data);
  var tokenNodeText = data.token;
  console.info(tokenNodeText);
  var transIdNodeText = data.tourcms_transaction_id;
  console.info(transIdNodeText);
  window.transactionId = transIdNodeText;

  var SpreedlyCodeNode = document.querySelector('#spreedly-iframe');
  var SpreedlyEnvKey = SpreedlyCodeNode.dataset.environmentKey;

  var lifecycle = new Spreedly.ThreeDS.Lifecycle({
    environmentKey: SpreedlyEnvKey,
    // Spreedly environment key
    hiddenIframeLocation: 'spreedly-threeds-hidden-iframe',
    // The DOM node that you'd like to inject hidden iframes (required)
    challengeIframeLocation: 'spreedly-threeds-challenge-iframe',
    // The DOM node that you'd like to inject the challenge flow (required)
    transactionToken: tokenNodeText,
    // The token for the transaction - used to poll for state (required)
  })

  console.info("Lifecycle created");

  Spreedly.on('3ds:status', statusUpdates);

  console.info("Status event handler set");

  // All of the following attributes are required
  var transactionData = {
    state: data.state,
    // The current state of the transaction. 'pending', 'succeeded', etc
    required_action: data.required_action,
    // The next action to be performed in the 3D Secure workflow
    device_fingerprint_form: data.device_fingerprint_form.cdata,
    // Available when the required_action is on the device fingerprint step
    checkout_form: data.checkout_form.cdata,
    // Available when the required_action is on the 3D Secure 1.0 fallback step
    checkout_url: data.checkout_url,
    // Available when the required_action is on the 3D Secure 1.0 fallback step
    challenge_url: data.challenge_url,
    // Available when the required_action is challenge
    challenge_form: data.challenge_form.cdata
    // Available when the required_action is challenge
  };

  console.info("Transaction data:");

  console.info(transactionData);

  lifecycle.start(transactionData);

  console.info("Lifecycle started");
}

var statusUpdates = function(event) {
  console.info('statusUpdates');
  console.info(event);
  if (event.action === 'succeeded') {
    // finish your checkout and redirect to success page
    alert("Succeeded, we would checkout and redirect to success");
    //fetch(`complete.php?transaction_id=` + window.transactionId, { method: 'POST' });
  } else if(event.action === 'error') {
    // present an error to the user to retry
    alert("Error, user should try again");
  } else if (event.action === 'trigger-completion') {
    // 1. make a request to your backend to do an authenticated call to Spreedly to complete the request
    //    The completion call is `https://core.spreedly.com/v1/transactions/[transaction-token]/complete`
    // 2a. if the transaction is marked as "succeeded" finish your checkout and redirect to success page
    // 2b. if the transaction is marked "pending" and the required action is "challenge" you'll need to call finalize `event.finalize(transaction)` with the transaction data from the authenticated completion call.
    console.info("Event:");
    console.info(event);
    // This is an example of the authenticated call that you'd make
    // to your service.
    console.info("About to call complete");
    fetch(`complete.php?transaction_id=` + window.transactionId, { method: 'POST' })
    .then(response => response.json())
    .then((data) => {
      console.info("Data");
      console.info(data);
      if (data.state === 'succeeded') {
        // finish your checkout and redirect to success page
        alert("Succeeded");
      }

      if (data.state === 'pending' && data.required_action === 'challenge') {
        alert("Challenge");
        event.finalize(data);
        // TODO: Show the modal div that wraps the challengeIframeLocation
        //       ("your-challenge-id-here" below would be the id you specify
        //       for challengeIframeLocation)

        // example HTML
        // <div id="your-challenge-id-here" class="hidden">
        // </div>

        document.getElementById('spreedly-threeds-challenge-iframe').classList.remove('hidden');
      }

      if(data.state === 'gateway_processing_failed') {
        alert("Error gateway_processing_failed");
      }
  })
}
}
