<?php

 
    $packages = $wpdb->get_results('SELECT item FROM cwt_subscription WHERE user_id = '.$current_user->ID.' ORDER BY id DESC LIMIT 1');
    foreach($packages as $package)
        $packageName = $package->item;

    if($packageName == '') {
        echo '<div class="error">';
        echo 'WARNING: You have not yet completed your subscription';
        echo '<br />You must <a href="/registration-subscription/?id='.$current_user->ID.'">make the subscription payment</a> ';
        echo 'before you can access the full features of your account</div>';
    } else {

    	$businessName = get_field('user_business_name', 'user_'.$current_user->ID);
    	
        $searchSupplier = array(     
            'post_type' => 'cwt_suppliers' ,
            'author' => $current_user->ID,
            'post_status' => 'publish|draft'
        );
        $supplier = query_posts($searchSupplier);

    	if($supplier[0]->post_title == 'BUSINESS NAME HERE' || $supplier[0]->post_title == '') {
    		echo '<div class="error">';
    		echo 'WARNING: Your supplier details have not been completed';
    		echo '<br />You must <a href="/my-cwt/edit-supplier/">complete them</a> before you can add any products or jobs to your profile';
    		echo '</div>';
    	}
    }
?>
<h2>Supplier Homepage</h2>

<?php if($_GET["edited"] == true) { ?>
    <div class="confirmation edited">
        <h3>Supplier edited successfully!</h3>
        <p>You can make more changes by clicking the edit link below</p>
    </div>
<?php } else if($_GET["profile"] == true) { ?>
    <div class="confirmation edited">
        <h3>Profile edited successfully!</h3>
        <p>You can make more changes by clicking <a href="/my-cwt/edit-profile/" title="Edit My Profile">here</a>.</p>
    </div>
<?php } else if($_GET["preferences"] == true) { ?>
    <div class="confirmation edited">
        <h3>Email preferences edited successfully!</h3>
        <p>You can make more changes by clicking <a href="/my-cwt/email-preferences/" title="Edit My Email Preferences">here</a>.</p>
    </div>
<?php } else if($_GET["priority"] == true) { ?>
    <div class="confirmation edited">
        <h3>Priority emails purchased successfully!</h3>
        <p>You can edit your email preferences <a href="/my-cwt/email-preferences/" title="Edit My Email Preferences">here</a>.</p>
    </div>
<?php } else if($_GET["sent"] == true) { ?>
    <div class="confirmation edited">
        <h3>Your message has been sent!</h3>
        <p>It will be delivered to all users who have consented to receive direct messages in their Email Preferences.</p>
        <p>You can send another <a href="/my-cwt/send-message/" title="Send a Message">here</a>.</p>
    </div>
<?php } ?>

            
<div class="cols">
    <div class="colBox">
        <ul>
        <li><b>First Name:</b> <?php echo $current_user->user_firstname; ?></li>
        <li><b>Surname:</b> <?php echo $current_user->user_lastname; ?></li>
        <li><b>Username:</b> <?php echo $current_user->user_login; ?></li>
        <li><b>Email:</b> <?php echo $current_user->user_email; ?></li>
        </ul>
    </div>
    <div class="colPad">&nbsp;</div>
    <div class="colBox">

        <?php if($packageName != '') { ?>
        <ul>
        <li><b>Current package:</b><br /><?php echo $packageName; ?></li>
        <?php
            $expiryDate = get_field('subscription_expiry', 'user_'.$current_user->ID);
            $expiryDateYear = substr($expiryDate, 0, 4);
            $expiryDateMonth = substr($expiryDate, 4, 2);
            $expiryDateDay = substr($expiryDate, 6, 2);
            $expiryTimestamp = mktime(0,0,0,$expiryDateMonth,$expiryDateDay,$expiryDateYear);
            $expiryDate = date('l jS F, Y', mktime(0,0,0,$expiryDateMonth,$expiryDateDay,$expiryDateYear));
        ?>
        <li><b>Renewal Date:</b> <br />
            <?php 
                if($packageName == 'Supplier 1 Month Subscription') {
                    echo date('jS', $expiryTimestamp).' of every month';
                } else if($packageName == 'Supplier 1 Year Subscription') {
                    echo date('jS F', $expiryTimestamp).' every year';
                }
            ?>
            <br />(<a href="mailto:admin@carwashtrader.co.uk?subject=Subscription%20Cancellation&body=Hi%0D%0A%0D%0APlease%20cancel%20my%20subscription%20to%20Car%20Wash%20Trader%0D%0A%0D%0AUsername:%20<?php echo $current_user->user_login; ?>%0D%0A%0D%0AThanks%0D%0A<?php echo $current_user->first_name; ?>%20<?php echo $current_user->last_name; ?>" title="Cancel subscription">Email us to cancel</a> with 7 days notice)</li>
        </ul>
        <?php } else { ?>
            <p><br /><br />Subscription not yet completed<br /><a href="/registration-subscription/?id=<?php echo $current_user->ID; ?>">Make payment here</a>.</p>
        <?php } ?>
    </div>
    <div class="clear"></div>
</div>

<h3>My Listings Summary</h3>

<div class="summaryRow">
    <div class="summaryBox">
        <h4>Products</h4>
        <?php $products = query_posts(array('post_type' => 'cwt_products', 'posts_per_page' => -1, author => $current_user->ID)); ?>
        <div class="summaryCount"><?php echo sizeof($products); ?></div>
    </div>
    <div class="summaryBox">
        <h4>Jobs</h4>
        <?php $jobs = query_posts(array('post_type' => 'cwt_jobs', 'posts_per_page' => -1, author => $current_user->ID)); ?>
        <div class="summaryCount"><?php echo sizeof($jobs); ?></div>
    </div>
    <div class="summaryBox">
        <h4>Adverts</h4>
        <?php $adverts = query_posts(array('post_type' => 'cwt_adverts', 'posts_per_page' => -1, author => $current_user->ID)); ?>
        <div class="summaryCount"><?php echo sizeof($adverts); ?></div>
    </div>
</div>


<?php
    if($supplier[0]->post_title != 'BUSINESS NAME HERE' & $supplier[0]->post_title != '') {
        $listings = array('post_type' => 'cwt_suppliers',
                            'orderby' => 'modified',
                            'order' => 'desc',
                            'author' => $current_user->ID);
        
        remove_all_filters('posts_orderby');//prevent plugin clashing with custom ordering
        $results = query_posts($listings);
        
        if(sizeof($results) > 0) {
            for($r=0; $r<sizeof($results); $r++) {
                $listingID = $results[$r]->ID;
            
                $title = $results[$r]->post_title;
                $description = $results[$r]->post_content;
                $address = get_field('supplier_address', $listingID);
                $postcode = get_field('supplier_postcode', $listingID);
                $geocode = get_field('supplier_postcode', $listingID);
                $contact = get_field('supplier_contactnumber', $listingID);
                $website = get_field('supplier_website', $listingID); 
                $openinghours = get_field('supplier_openinghours', $listingID); 
                $prices = get_field('supplier_prices', $listingID); 

                echo '<div class="listingEdit">';
                echo '<a href="/my-cwt/edit-supplier/" title="Edit my Supplier details">Edit my details</a></div>';
                
                echo outputSupplierListing($listingID,$title,$description,$address,$postcode,$geocode,$contact,$website,$openinghours,$prices);
            }
        }
    }
?>





