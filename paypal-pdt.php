<?php
	session_start();
    
    global $wpdb;
    
    include '../../../wp-blog-header.php';
	
	// Init cURL
	$request = curl_init();
	
	// Set request options
	curl_setopt_array($request, array
	(
	  CURLOPT_URL => 'https://www.paypal.com/cgi-bin/webscr',
	  CURLOPT_POST => TRUE,
	  CURLOPT_POSTFIELDS => http_build_query(array
		(
		  'cmd' => '_notify-synch',
		  'tx' => $_GET["tx"],
		  'at' => 'FyzkvVYe3fHV4_WEMNjFoiAy_ePS0LABguOV6muS7Ap5hsKdB7HiU3J7yfG', //auth token from paypal
		)),
	  CURLOPT_RETURNTRANSFER => TRUE,
	  CURLOPT_HEADER => FALSE
	));
	
	// Execute request and get response and status code
	$response = curl_exec($request);
	$status   = curl_getinfo($request, CURLINFO_HTTP_CODE);
	
	if($status == 200 AND strpos($response, 'SUCCESS') === 0)
	{
		// Remove SUCCESS part (7 characters long)
		$response = substr($response, 7);
		
		// URL decode
		$response = urldecode($response);
		
		// Turn into associative array
		preg_match_all('/^([^=\s]++)=(.*+)/m', $response, $m, PREG_PATTERN_ORDER);
		$response = array_combine($m[1], $m[2]);
		
		// Fix character encoding if different from UTF-8 (in my case)
		if(isset($response['charset']) AND strtoupper($response['charset']) !== 'UTF-8')
		{
		  foreach($response as $key => &$value)
		  {
			$value = mb_convert_encoding($value, 'UTF-8', $response['charset']);
		  }
		  $response['charset_original'] = $response['charset'];
		  $response['charset'] = 'UTF-8';
		}
		
		// Sort on keys for readability (handy when debugging)
		ksort($response);
		
		// Process purchase
		$package = $response["item_name"];//Subscription etc
		$txID = $response["option_selection2"];//TXID
		$value = $response["mc_gross"];
		$paypalID = $response["txn_id"];//Paypal transaction id
		$bookingID = $response["option_selection3"];//subscription db table id

		$isPriority = 'false';

		if($txID != '') {
			$check = 'SELECT transaction_id FROM cwt_transaction WHERE id = '.$txID;
			$check = mysql_query($check) or die('Check: '. mysql_error());
			$check = mysql_fetch_assoc($check);
		} else {
			$trans = 'INSERT INTO cwt_transaction (transaction_type, type_id) VALUES ("priorityemails", '.$current_user->ID.')';
	        $trans_query = mysql_query($trans) or die('Transaction: '.$trans.' - '.mysql_error());
	        $txID = mysql_insert_id();
	        $isPriority = 'true';
		}
		
		if($check["transaction_id"] == '' || $isPriority == 'true')
		{
			$record = 'UPDATE cwt_transaction ';
			$record .= 'SET transaction_id = "'.$paypalID.'", transaction_date = "'.date('Y-m-d H:i:s').'", ';
			$record .= 'transaction_value = '.$value.' WHERE id = '.$txID;
			mysql_query($record) or die('Record: '.$record.' - '.mysql_error());

			if($package == 'Supplier 1 Month Subscription' || $package == 'Supplier 1 Year Subscription') {

					//Set event to paid
					$record = 'UPDATE cwt_subscription SET item = "'.$package.'", paid = "yes" WHERE id = '.$bookingID;
					mysql_query($record) or die('Paid: '.$record.' - '.mysql_error());

					$subscribers = $wpdb->get_results("SELECT user_id FROM cwt_subscription WHERE id = ".$bookingID);
					foreach ($subscribers as $subscriber)
	        			$user = get_userdata($subscriber->user_id);

					if($package == 'Supplier 1 Month Subscription') {
						$expiry = date('Ymd', strtotime(date("Y-m-d", mktime()) . " +1 month"));
	            		__update_user_meta($user->ID, 'subscription_expiry', $expiry);

					} else if($package == 'Supplier 1 Year Subscription') {
						$expiry = date('Ymd', strtotime(date("Y-m-d", mktime()) . " +1 year"));
	            		__update_user_meta($user->ID, 'subscription_expiry', $expiry);
					}

					$action = '/subscription-success/'.$txID.'/';

			} else if(substr($package, 0, 3) == 'Job') {

					if($package == 'Job Listing (1 month)') {
						$expiry = date('Ymd', strtotime(date("Y-m-d", mktime()) . " +1 month"));
					} else if($package == 'Job Listing (3 months)') {
						$expiry = date('Ymd', strtotime(date("Y-m-d", mktime()) . " +3 month"));
					} else if($package == 'Job Listing (6 months)') {
						$expiry = date('Ymd', strtotime(date("Y-m-d", mktime()) . " +6 month"));
					}
            		__update_post_meta($bookingID, 'premium_expiry', $expiry);
            		__update_post_meta($bookingID, 'premium_paypalID', $paypalID);
            		//wp_publish_post($bookingID);

            		$post = array(
						'ID' => $bookingID,
						'post_date' => date('Y-m-d H:i:s'),
						'post_status' => 'publish'
					);
					wp_update_post($post);
					do_action('wp_update_post', 'wp_update_post');

					$action = '/my-cwt/jobs/?added=true';

			} else if(substr($package, 0, 7) == 'Product') {

					if($package == 'Product Listing (1 month)') {
						$expiry = date('Ymd', strtotime(date("Y-m-d", mktime()) . " +1 month"));
					} else if($package == 'Product Listing (3 months)') {
						$expiry = date('Ymd', strtotime(date("Y-m-d", mktime()) . " +3 month"));
					} else if($package == 'Product Listing (6 months)') {
						$expiry = date('Ymd', strtotime(date("Y-m-d", mktime()) . " +6 month"));
					}
            		__update_post_meta($bookingID, 'premium_expiry', $expiry);
            		__update_post_meta($bookingID, 'premium_paypalID', $paypalID);
            		//wp_publish_post($bookingID);

            		$post = array(
						'ID' => $bookingID,
						'post_date' => date('Y-m-d H:i:s'),
						'post_status' => 'publish'
					);
					wp_update_post($post);
					do_action('wp_update_post', 'wp_update_post');

					$action = '/my-cwt/products/?added=true';

			} else if(substr($package, 0, 8) == 'For Sale') {

					if($package == 'For Sale Listing (1 month)') {
						$expiry = date('Ymd', strtotime(date("Y-m-d", mktime()) . " +1 month"));
					} else if($package == 'For Sale Listing (3 months)') {
						$expiry = date('Ymd', strtotime(date("Y-m-d", mktime()) . " +3 month"));
					} else if($package == 'For Sale Listing (6 months)') {
						$expiry = date('Ymd', strtotime(date("Y-m-d", mktime()) . " +6 month"));
					}
            		__update_post_meta($bookingID, 'premium_expiry', $expiry);
            		__update_post_meta($bookingID, 'premium_paypalID', $paypalID);

            		$post = array(
						'ID' => $bookingID,
						'post_date' => date('Y-m-d H:i:s')
					);
					wp_update_post($post);
					do_action('wp_update_post', 'wp_update_post');

					$action = '/my-cwt/car-washes/?edited=true';

			} else if(substr($package, 0, 8) == 'Priority') {

					if($package == 'Priority Messages (10 emails)') {
						$num = 10;
					} else if($package == 'Priority Messages (20 emails)') {
						$num = 20;
					} else if($package == 'Priority Messages (30 emails)') {
						$num = 30;
					}
            		update_user_meta($bookingID, 'emails_priority', $num);

					$action = '/my-cwt/?priority=true';

			}
		}
		
		header('Location: '.$action);
	}
	else
	{
		header('Location: /subscription-failure/');
	}
	
	// Close connection
	curl_close($request);
?>