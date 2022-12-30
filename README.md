# payu_php_payment_gateway_integration

Simplest Way to Integrate Payu Money Payment Gateway

Login to Payu Account.

Submit All the mandatory sections in onboarding.

Get the Merchant Key , Salt for Hashing.

We are using Web Checkout Flows -> PayU Hosted Checkout integration

Created a Html form in index.html
Set the action of form to payment.php to post all the data.

Payment.php is getting all the posted values and creates a hash by using Merchant Key and Salt provided by PayU.

Payment page will auto submit the data from payment form to test payu link.

It will open the payment page with all type of payment modes in Payu.

Thank You.
