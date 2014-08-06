<?php
   function do_process($ipn){
      global $wpdb;
//      echo "Loading data to the database";
//      echo $_POST['txn_id'];
//      echo $_POST['first_name'];
//      echo $_POST['last_name'];
//      echo $_POST['payer_email'];
//      echo $_POST['payment_gross'];
//      echo $_POST['address_street'];
//      echo $_POST['address_city'];
//      echo $_POST['address_state'];
//      echo $_POST['address_zip'];
//      echo $_POST['payment_status'];
//      echo $_POST['custom'];
      $wpdb->query($wpdb->prepare("INSERT INTO wp_payment_notifications (TransactionID, FirstName, LastName, PayerEmail, PaymentAmount, AddressStreet, AddressCity, AddressState, AddressZip, PaymentStatus, Custom, Memo) VALUES (%s, %s, %s, %s, %f, %s, %s, %s, %s, %s, %s, %s)",
      $_POST['txn_id'],
      $_POST['first_name'],
      $_POST['last_name'],
      $_POST['payer_email'],
      $_POST['payment_gross'],
      $_POST['address_street'],
      $_POST['address_city'],
      $_POST['address_state'],
      $_POST['address_zip'],
      $_POST['payment_status'],
      $_POST['custom'],
      $_POST['memo']));
   }
/**
 * Returns false if a transaction id has already been processed
 */
function check_txn_id($txn_id){
     echo "Checking id....";
      global $wpdb;
      $wpdb->query(
         $wpdb->prepare("SELECT 1 from wp_payment_notifications WHERE TransactionID = %s",
         $txn_id));
      echo "\n".$wpdb->num_rows."\n";
      return ($wpdb->num_rows == 0);
}

// Read POST data
// reading posted data directly from $_POST causes serialization
// issues with array data in POST. Reading raw POST data from input stream instead.
$raw_post_data = file_get_contents('php://input');
$raw_post_array = explode('&', $raw_post_data);
$myPost = array();
foreach ($raw_post_array as $keyval) {
	$keyval = explode ('=', $keyval);
	if (count($keyval) == 2)
		$myPost[$keyval[0]] = urldecode($keyval[1]);
}
// read the post from PayPal system and add 'cmd'
$req = 'cmd=_notify-validate';
if(function_exists('get_magic_quotes_gpc')) {
	$get_magic_quotes_exists = true;
}
foreach ($myPost as $key => $value) {
	if($get_magic_quotes_exists == true && get_magic_quotes_gpc() == 1) {
		$value = urlencode(stripslashes($value));
	} else {
		$value = urlencode($value);
	}
	$req .= "&$key=$value";
}

// Post IPN data back to PayPal to validate the IPN data is genuine
// Without this step anyone can fake IPN data

$ch = curl_init(get_option('paypal_url'));
if ($ch == FALSE) {
	return FALSE;
}

curl_setopt($ch, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_1);
curl_setopt($ch, CURLOPT_POST, 1);
curl_setopt($ch, CURLOPT_RETURNTRANSFER,1);
curl_setopt($ch, CURLOPT_POSTFIELDS, $req);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 1);
curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2);
curl_setopt($ch, CURLOPT_FORBID_REUSE, 1);

if(WP_DEBUG == true) {
	curl_setopt($ch, CURLOPT_HEADER, 1);
	curl_setopt($ch, CURLINFO_HEADER_OUT, 1);
}

// CONFIG: Optional proxy configuration
//curl_setopt($ch, CURLOPT_PROXY, $proxy);
//curl_setopt($ch, CURLOPT_HTTPPROXYTUNNEL, 1);

// Set TCP timeout to 30 seconds
curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 30);
curl_setopt($ch, CURLOPT_HTTPHEADER, array('Connection: Close'));

// CONFIG: Please download 'cacert.pem' from "http://curl.haxx.se/docs/caextract.html" and set the directory path
// of the certificate as shown below. Ensure the file is readable by the webserver.
// This is mandatory for some environments.

//$cert = __DIR__ . "./cacert.pem";
//curl_setopt($ch, CURLOPT_CAINFO, $cert);

$res = curl_exec($ch);
if (curl_errno($ch) != 0) // cURL error
	{
	if(WP_DEBUG == true) {	
		error_log(date('[Y-m-d H:i e] '). "Can't connect to PayPal to validate IPN message: " . curl_error($ch) . PHP_EOL, 3);
	}
	curl_close($ch);
	exit;

} else {
		// Log the entire HTTP response if debug is switched on.
		if(WP_DEBUG == true) {
			error_log(date('[Y-m-d H:i e] '). "HTTP request of validation request:". curl_getinfo($ch, CURLINFO_HEADER_OUT) ." for IPN payload: $req" . PHP_EOL);
			error_log(date('[Y-m-d H:i e] '). "HTTP response of validation request: $res" . PHP_EOL);

			// Split response headers and payload
			list($headers, $res) = explode("\r\n\r\n", $res, 2);
		}
		curl_close($ch);
}

// Inspect IPN validation result and act accordingly

if (strcmp ($res, "VERIFIED") == 0) {
   echo "message verified";
   if($_POST['payment_status'] != 'Completed'){
      // check whether the payment_status is Completed
      error_log('payment not yet completed');
   }

   // check that txn_id has not been previously processed
   if (!check_txn_id($_POST['txn_id'])){
	   error_log('transaction already processed');
	   echo 'transaction already processed';
           die();
   }
   if($_POST['receiver_email'] != get_option('paypal_email')){
      // check that receiver_email is your PayPal email
      error_log('receiver email does not match');
   }
   do_process($_POST);
   // process payment and mark item as paid.

   // assign posted variables to local variables
   //$item_name = $_POST['item_name'];
   //$item_number = $_POST['item_number'];
   //$payment_status = $_POST['payment_status'];
   //$payment_amount = $_POST['mc_gross'];
   //$payment_currency = $_POST['mc_currency'];
   //$txn_id = $_POST['txn_id'];
   //$receiver_email = $_POST['receiver_email'];
   //$payer_email = $_POST['payer_email'];

   if(WP_DEBUG == true) {
      error_log(date('[Y-m-d H:i e] '). "Verified IPN: $req ". PHP_EOL);
   }
} else if (strcmp ($res, "INVALID") == 0) {
   // log for manual investigation
	// Add business logic here which deals with invalid IPN messages
	if(WP_DEBUG == true) {
		error_log(date('[Y-m-d H:i e] '). "Invalid IPN: $req" . PHP_EOL);
	}
}

?>
