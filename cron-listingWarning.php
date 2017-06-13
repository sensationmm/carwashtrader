<?php
    
    global $wpdb;
    
    include '../../../wp-blog-header.php';
	$subject = 'Listing Warning';
	include 'includes/email-template.php';

    //echo 'Date: '.date('Ymd'); 
    $date = new DateTime();
    $date->sub(new DateInterval('P7D'));

    $expiring = $wpdb->get_results('SELECT post_id FROM cwt_postmeta WHERE meta_key = "premium_expiry" AND meta_value = "'.date('Ymd', $date).'"');

    for($e=0; $e<sizeof($expiring); $e++) {
    	$postID = $expiring[$e]->post_id;

    	$post = get_post($postID);
    	$author = get_userdata($post->post_author);
    	switch($post->post_type) {
    		case 'cwt_products': $productType = 'product'; break;
    		case 'cwt_jobs': $productType = 'job'; break;
    		case 'cwt_carwashes': $productType = 'sale'; break;
    	}

 		//send notification email
		$message = "\r\n<tr><td>";
		$message .= '<p>Hi '.$author->first_name.',</p>';
		$message .= '<p>Your '.$productType.' listing '.$post->post_title.' is due to expire in the next 7 days.</p>';
		$message .= '<p>We will send you another email when the listing expires, then if you wish you can renew it when you ';
		$message .= '<a href="http://www.carwashtrader.co.uk/login/">log in</a> to your control panel.</p>';
		$message .= '<p>Thanks, <br />The CWT Team</p>';
        $message .= "\r\n";
		$message .= '</td></tr>';

		mail($author->data->user_email, $subject, $emailHeader.$message.$emailFooter, $headers) or die("Error");	    
    }

    echo $e.' listings due to expire';

?>