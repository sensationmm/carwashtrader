<?php
    
    global $wpdb;
    
    include '../../../wp-blog-header.php';

    $subject = "Daily Update";
    $headers = "From: Car Wash Trader <noreply@carwashtrader.co.uk>\nContent-Type: text/html;  charset=iso-8859-1";
    $emailHeader = '<html><head><meta http-equiv="Content-Type" content="text/html; charset=UTF-8" /><title>Car Wash Trader Daily Update</title></head><body style="padding:0px;margin:0px;"><table style="width:100%;background:#cecece;font-family:arial;" cellpadding="0" cellspacing="0"><tr><td></td><td style="width:600px;"><table style="width:100%;background:#ffffff;" cellpadding="0" cellspacing="0"><tr style="background:#ccbcf3;"><td style="padding:20px;"><table width="100%"><tr><td><img src="http://www.carwashtrader.co.uk/wp-content/themes/carwashtrader/assets/images/carwash-trader-email-header.gif" alt="Car Wash Trader" /></td><td style="text-align:right;color:#ffffff;font-weight:bold;font-size:30px;">Daily Update</td></tr></table></td></tr><tr><td><table cellpadding="0" cellspacing="20" style="width:100%;">';
    $emailFooter = '</table></td></tr><tr style="background:#ccbcf3;"><td style="padding:10px;"><table cellpadding="0" cellspacing="0" style="width:100%;"><tr><td style="color:#666666;"><b>Tel</b>: 0800 073 4540</td><td style="text-align:right;color:#666666;"><b>Email</b>: <a style="color:#666666;" href="mailto:info@carwashtrader.co.uk" title="Email Us">info@carwashtrader.co.uk</a></td></tr></table></td></tr></table></td><td></td></tr></table></body></html>';

    $date24 = new DateTime();
    $date24->sub(new DateInterval('P1D'));

    $results24_year = $date24->format('Y');
    $results24_month = $date24->format('m');
    $results24_day = $date24->format('j');

    $priority = array('post_type' => array('cwt_carwashes','cwt_suppliers','cwt_products','cwt_jobs'),
                        'orderby' => 'modified',
                        'year' => $results24_year,
                        'monthnum' => $results24_month,
                        'day' => $results24_day);
    $results24 = query_posts($priority);


    $date48 = new DateTime();
    $date48->sub(new DateInterval('P2D'));

    $results48_year = $date48->format('Y');
    $results48_month = $date48->format('m');
    $results48_day = $date48->format('j');

    $normal = array('post_type' => array('cwt_carwashes','cwt_suppliers','cwt_products','cwt_jobs'),
                        'orderby' => 'modified',
                        'year' => $results48_year,
                        'monthnum' => $results48_month,
                        'day' => $results48_day);
    $results48 = query_posts($priority);

    $users = array('fields' => array('ID','display_name','user_email'));
    $results = get_users($users);

    foreach($results as $result) {

        $message = '';

        $userID = $result->ID;
        $userName = $result->display_name;
        $userEmail = $result->user_email;

        $userData = get_userdata($userID);
        $userRole = $userData->roles[0];

        $userPreference = get_the_author_meta('emails_daily', $userID);

        $listCarwashes = '';
        $listForSale = '';
        $listSuppliers = '';
        $listProducts = '';
        $listJobs = '';

        if($userRole != 'administrator' && $userPreference == 'yes') {

            $prefs = $wpdb->get_results( 'SELECT term_id FROM cwt_email_prefs WHERE user_id = '.$userID, ARRAY_N);
            for($a=0; $a<sizeof($prefs); $a++)
                $prefs[$a] = $prefs[$a][0]; //convert to 1d array

            $checkPriorityEmails = get_the_author_meta('emails_priority', $userID);
            if($checkPriorityEmails == '')
                $checkPriorityEmails = 0;

            if($checkPriorityEmails > 0)
                $listings = $results24;
            else
                $listings = $results48;
            for($p=0; $p<sizeof($listings); $p++) {
                $listingID = $listings[$p]->ID;
                $listingType = $listings[$p]->post_type;
                $listingTitle = $listings[$p]->post_title;
                $listingContent = strip_tags($listings[$p]->post_content);
                switch($listingType) {
                    case 'cwt_carwashes':
                        if(get_field('sale_for_sale', $listingID) == 'yes') {
                            $taxonomy = 'cwt_forsale_types';
                            $showInList = 'listForSale';
                            $listingURL = 'car-washes-for-sale';
                        } else {
                            $taxonomy = 'cwt_carwash_types';
                            $showInList = 'listCarwashes';
                            $listingURL = '';
                        }
                        break;
                    case 'cwt_suppliers':
                        $taxonomy = 'cwt_prodsupp_cats';
                            $showInList = 'listSuppliers';
                            $listingURL = 'suppliers';
                        break;
                    case 'cwt_products':
                        $taxonomy = 'cwt_prodsupp_cats';
                            $showInList = 'listProducts';
                            $listingURL = 'products';
                        break;
                    case 'cwt_jobs':
                        $taxonomy = 'cwt_job_types';
                            $showInList = 'listJobs';
                            $listingURL = 'jobs';
                        break;
                }
                $taxonomy = wp_get_post_terms($listingID, $taxonomy);
                for($t=0; $t<sizeof($taxonomy); $t++)
                    $taxonomy[$t] = $taxonomy[$t]->term_id; //convert to 1d array

                $matches = array_intersect($prefs, $taxonomy); //compare listing taxonomy with user preferences - returns matches
                if(sizeof($matches) > 0) { //in preferences

                    $listingEntry = '<tr>';
                    $listingEntry .= '<td>';
                    $listingEntry .= '<table style="width:100%;" cellpadding="0" cellspacing="0">';
                    $listingEntry .= '<tr style="vertical-align:top;">';
                    $listingEntry .= '<td style="width:100px;">';
                    $listingLink = 'http://www.carwashtrader.co.uk/'.$listingURL.'/?showID='.$listingID;
                    $listingEntry .= '<a href="'.$listingLink.'" target="_blank">';

                    $img = get_the_post_thumbnail($listingID, array(100,100));
                    if($img == '')
                        $img = '<img width="100px" style="border:0px;" src="http://www.carwashtrader.co.uk/wp-content/themes/carwashtrader/assets/images/listing-image.jpg" />';

                    $listingEntry .= $img.'</a></td>';
                    $listingEntry .= '<td style="width:20px;"></td>';
                    $listingEntry .= '<td><h2 style="color:#5e3088;font-weight:bold;font-size:20px;margin:0px 0px 5px 0px;padding:0px;">';
                    $listingEntry .= '<a href="'.$listingLink.'" style="color:#5e3088;" target="_blank">'.$listingTitle.'</a></h2>';
                    $listingEntry .= '<p style="color:#666666;font-size:14px;margin:0px 0px 5px 0px;padding:0px;">';
                    if(strlen($listingContent) > 120)
                        $listingEntry .= substr($listingContent, 0, 120).'...';  
                    else
                        $listingEntry .= html_entity_decode($listingContent);
                    $listingEntry .= '</p>';
                    $listingEntry .= '<p style="text-align:right;font-size:11px;">';
                    $listingEntry .= '<a style="color:#666666;" href="'.$listingLink.'" target="_blank">Read more &gt;</a></p>';
                    $listingEntry .= '</td></tr></table>';
                    $listingEntry .= "\r\n";
                    $listingEntry .= '</td></tr>';

                    $$showInList .= $listingEntry;
                } //if match

            } //listing loop

            if($listCarwashes != '' || $listForSale != '' || $listSuppliers != '' || $listProducts != '' || $listJobs != '') {

                $message = "\r\n";
                $message .= '<tr><td><p><b>PLEASE NOTE: You are receiving this message because you have consented to receive daily update ';
                $message .= 'emails for the latest listings from Car Wash Trader</b>. You can modify this, your preferences for what to ';
                $message .= 'receive and subscribe to priority emails under My Email Preferences when you ';
                $message .= '<a style="color:#666666;" href="http://www.carwashtrader.co.uk/login/">log in</a> to your control panel.</p>';
                if($checkPriorityEmails > 1)
                    $message .= '<p>You currently have '.($checkPriorityEmails - 1).' priority emails remaining.</p>';
                else if($checkPriorityEmails == 1) {
                    $message .= '<p>Your priority emails have run out. You can purchase more from My Email Preferences in ';
                    $message .= 'your control panel.</p></td></tr>';
                }
                $message .= '</td></tr>';


                if($listCarwashes != '') {
                    $message .= "\r\n";
                    $message .= '<tr><td style="padding:5px 10px;background:#5e3088;">';
                    $message .= '<h1 style="color:#ffffff;font-weight:bold;font-size:22px;margin:0px;padding:0px;">Car Washes</h1></td></tr>';
                    $message .= $listCarwashes;
                }

                if($listForSale != '') {
                    $message .= "\r\n";
                    $message .= '<tr><td style="padding:5px 10px;background:#5e3088;">';
                    $message .= '<h1 style="color:#ffffff;font-weight:bold;font-size:22px;margin:0px;padding:0px;">Car Washes For Sale</h1></td></tr>';
                    $message .= $listForSale;
                }

                if($listSuppliers != '') {
                    $message .= "\r\n";
                    $message .= '<tr><td style="padding:5px 10px;background:#5e3088;">';
                    $message .= '<h1 style="color:#ffffff;font-weight:bold;font-size:22px;margin:0px;padding:0px;">Suppliers</h1></td></tr>';
                    $message .= $listSuppliers;
                }

                if($listProducts != '') {
                    $message .= "\r\n";
                    $message .= '<tr><td style="padding:5px 10px;background:#5e3088;">';
                    $message .= '<h1 style="color:#ffffff;font-weight:bold;font-size:22px;margin:0px;padding:0px;">Products</h1></td></tr>';
                    $message .= $listProducts;
                }

                if($listJobs != '') {
                    $message .= "\r\n";
                    $message .= '<tr><td style="padding:5px 10px;background:#5e3088;">';
                    $message .= '<h1 style="color:#ffffff;font-weight:bold;font-size:22px;margin:0px;padding:0px;">Jobs</h1></td></tr>';
                    $message .= $listJobs;
                }

                //if priority email remove 1
                if($checkPriorityEmails > 0) {
                    $newPriorityNum = ($checkPriorityEmails - 1);
                    update_user_meta( $userID, 'emails_priority', $newPriorityNum);
                }

                //send email
                mail($userEmail, $subject, $emailHeader.$message.$emailFooter, $headers) or die("Error");
            }
        } //check not admin

    } //user loop
    
?>