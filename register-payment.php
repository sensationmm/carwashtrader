<?php
    
    global $wpdb;
    
    include '../../../wp-blog-header.php';

    //removed as user id is now passed to page in url
    //$usernames = $wpdb->get_results("SELECT ID FROM $wpdb->users ORDER BY ID DESC LIMIT 1");
     
    //foreach ($usernames as $username) {


        $user = get_userdata($_GET["user_id"]);
        if($user->roles[0] != 'supplier') {
            if($user->roles[0] == 'guest') //set email alert preference to yes auto for guest users - they dont see the option
                update_user_meta( $user->ID, 'emails_daily', 'yes');
            header('Location: /registration-confirmation/?user_id='.$_GET["user_id"].'&hash='.$_GET["hash"]);
        } else {
            $date = date(Ymd);
            __update_user_meta($user->ID, 'subscription_expiry', $date);
            header('Location: /registration-subscription/?id='.$user->ID);
        }


    //}

?>