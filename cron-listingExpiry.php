<?php
    
    global $wpdb;
    
    include '../../../wp-blog-header.php';
	$subject = 'Listing Expiry';
	include 'includes/email-template.php';

    //echo 'Date: '.date('Ymd');

    $expiring = $wpdb->get_results('SELECT post_id FROM cwt_postmeta WHERE meta_key = "premium_expiry" AND meta_value = "'.date('Ymd').'"');

    for($e=0; $e<sizeof($expiring); $e++) {
    	$postID = $expiring[$e]->post_id;

    	$post = get_post($postID);
    	$author = get_userdata($post->post_author);
    	switch($post->post_type) {
    		case 'cwt_products': $productType = 'product'; break;
    		case 'cwt_jobs': $productType = 'job'; break;
    		case 'cwt_carwashes': $productType = 'sale'; break;
    	}

	    //show transaction record as expired
    	$transRecord = $wpdb->query('UPDATE cwt_transaction SET expired = 1 WHERE type_id = '.$postID);

    	//set premium variables to expired
        __update_post_meta($postID, 'premium_expiry', '');
        __update_post_meta($postID, 'premium_paypalID', '');

        //unpublish post
        $listing = array(
			'ID' => $postID,
			'post_status' => 'draft'
		);
		wp_update_post($listing);
		do_action('wp_update_post', 'wp_update_post');
 

 		//send notification email
		$message = "\r\n<tr><td>";
		$message .= '<p>Hi '.$author->first_name.',</p>';
		$message .= '<p>Your '.$productType.' listing '.$post->post_title.' has now expired.</p>';
		$message .= '<p>You can renew this listing when you ';
		$message .= '<a href="http://www.carwashtrader.co.uk/login/">log in</a> to your control panel.</p>';
		$message .= '<p>Thanks, <br />The CWT Team</p>';
        $message .= "\r\n";
		$message .= '</td></tr>';

		mail($author->data->user_email, $subject, $emailHeader.$message.$emailFooter, $headers) or die("Error");	    
    }

?>