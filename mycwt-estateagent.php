<?php
	$businessName = get_field('user_business_name', 'user_'.$current_user->ID);
	
	if($businessName == '') {
		echo '<div class="error">';
		echo 'WARNING: Your business details have not been completed';
		echo '<br />You must <a href="/my-cwt/edit-business/">complete them</a> before you can add any businesses to your profile';
		echo '</div>';
	}
?>
<h2>Commercial Estate Agent Homepage</h2>

<?php if($_GET["edited"] == true) { ?>
    <div class="confirmation edited">
        <h3>Business details edited successfully!</h3>
        <p>You can make more changes by clicking the edit link <a href="/my-cwt/edit-business/" title="Edit Business Details">here</a>.</p>
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
        <ul>
        <li><b>Business Name:</b> <?php echo $businessName; ?></li>
        <li><b>Telephone:</b> <?php echo get_field('user_telephone', 'user_'.$current_user->ID); ?></li>
        <li><b>Address:</b> <?php echo get_field('user_address', 'user_'.$current_user->ID); ?></li>
        <li><b>Postcode:</b> <?php echo get_field('user_postcode', 'user_'.$current_user->ID); ?></li>
        </ul>
    </div>
</div>

<h3>My Listings Summary</h3>

<div class="summaryRow">
    <div class="summaryBox summaryBox2">
        <h4>For Sale</h4>
        <?php $forsale = query_posts(array('post_type' => 'cwt_carwashes', 'posts_per_page' => -1, author => $current_user->ID, 'meta_key' => 'sale_for_sale', 'meta_value' => 'yes')); ?>
        <div class="summaryCount"><?php echo sizeof($forsale); ?></div>
    </div>
    <div class="summaryBox summaryBox2">
        <h4>Adverts</h4>
        <?php $adverts = query_posts(array('post_type' => 'cwt_adverts', 'posts_per_page' => -1, author => $current_user->ID)); ?>
        <div class="summaryCount"><?php echo sizeof($adverts); ?></div>
    </div>
</div>