<?php
    
    global $wpdb;
    
    include '../../../wp-blog-header.php';
	$subject = 'Google Map Failures';
	include 'includes/email-template.php';

    //echo 'Date: '.date('Ymd');

    $withoutGeocode = 0;

    $failedGeo = $wpdb->get_results('SELECT post_id FROM cwt_postmeta WHERE meta_key IN ("carwash_geocode","job_geocode","supplier_geocode")');
    $failed = $wpdb->get_results('SELECT post_id FROM cwt_postmeta WHERE meta_key IN ("carwash_postcode","job_postcode","supplier_postcode") AND post_id NOT IN (1018,1284,2326,3118,3193,3223)');
    
    $messageBody = '';
    for($e=0; $e<sizeof($failed); $e++) {
        if($count < 100) {
        	$postID = $failed[$e]->post_id;
            $post = get_post($postID);

            switch($post->post_type) {
                case 'cwt_carwashes': $geocodeType = 'carwash_geocode'; $postcodeType = 'carwash_postcode'; break;
                case 'cwt_suppliers': $geocodeType = 'supplier_geocode'; $postcodeType = 'supplier_postcode'; break;
                case 'cwt_jobs': $geocodeType = 'job_geocode'; $postcodeType = 'job_postcode'; break;
            }

            $postcode = get_field($postcodeType, $postID);

            if(get_field($geocodeType, $postID) == '') {

                $geocode = '';
                $resp = wp_remote_get( "https://maps.google.com/maps/api/geocode/json?address=".urlencode($postcode)."&key=AIzaSyDO05i3qVrVSj441LaSXNyxkG8zeaEQOHM&sensor=false" );
                if ( 200 == $resp['response']['code'] ) {
                    $body = $resp['body'];
                    $data = json_decode($body);
                    if($data->status=="OK"){
                        $latitude = $data->results[0]->geometry->location->lat;
                        $longitude = $data->results[0]->geometry->location->lng;
                        $geocode = $latitude.','.$longitude;
                        
                        __update_post_meta($postID, $geocodeType, $geocode);
                        $withoutGeocode++;
                    } else echo $data->status;
                } else echo $resp['response']['code'];
                $count++;
            }
        }
    }

    //echo '<br /><br />Total: '.$withoutGeocode;

?>