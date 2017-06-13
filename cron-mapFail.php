<?php
    
    global $wpdb;
    
    include '../../../wp-blog-header.php';
	$subject = 'Google Map Failures';
	include 'includes/email-template.php';

    //echo 'Date: '.date('Ymd');

    $withoutGeocode = 0;

    $failedGeo = $wpdb->get_results('SELECT post_id FROM cwt_postmeta WHERE meta_key IN ("carwash_geocode","job_geocode","supplier_geocode")');
    $failed = $wpdb->get_results('SELECT post_id FROM cwt_postmeta WHERE meta_key IN ("carwash_postcode","job_postcode","supplier_postcode")');
    
    $messageBody = '';
    for($e=0; $e<sizeof($failed); $e++) {
    	$postID = $failed[$e]->post_id;

        $post = get_post($postID);

        switch($post->post_type) {
            case 'cwt_carwashes': $geocode = 'carwash_geocode'; $postcode = 'carwash_postcode'; break;
            case 'cwt_suppliers': $geocode = 'supplier_geocode'; $postcode = 'supplier_postcode'; break;
            case 'cwt_jobs': $geocode = 'job_geocode'; $postcode = 'job_postcode'; break;
        }

        if(get_field($geocode, $postID) == '') {
            $withoutGeocode++;
            $messageBody .= '<li><a href="http://www.carwashtrader.co.uk/wp-admin/post.php?post='.$postID.'&action=edit">';
            $messageBody .= $post->post_title.' (Post ID: '.$postID.') '.get_field($postcode, $postID).'</a></li>';
            $messageBody .= "\r\n";
        }
    }
echo $withoutGeocode;
    echo $messageBody;
 
    /*if(sizeof($failed) > 0) {
        //send notification email
        $message = "\r\n<tr><td>";
        $message .= '<p>Hi '.$author->first_name.',</p>';
        $message .= '<p>The following '.sizeof($failed).' listings failed the Google Maps API call:</p>';
        $message .= '<ul>'.$messageBody.'</ul>';
        $message .= '<p>Thanks, <br />The CWT Team</p>';
        $message .= "\r\n";
        $message .= '</td></tr>';

        mail('kevin@sensationmultimedia.co.uk', $subject, $emailHeader.$message.$emailFooter, $headers) or die("Error");
    }*/

?>