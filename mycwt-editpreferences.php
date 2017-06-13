<?php
/**
 * @package WordPress
 * @subpackage carwashtrader
 * template name: my cwt - edit preferences
*/

get_header(); 
include 'includes/secure.php';

global $current_user, $wp_roles;
get_currentuserinfo();
$error = array();   
$terms = array(); 
	$formLabel = 'Update';
                    
if ( 'POST' == $_SERVER['REQUEST_METHOD'] && !empty( $_POST['action'] )) {
			
			//wp_update_post($post);
			//do_action('wp_update_post', 'wp_update_post');
			//unset($_POST);
			//wp_redirect('/my-cwt/?edited=true');
	if($_POST['action'] == 'edit-post') {
		$carwashes = $_POST['carwashes'];
		$forsale = $_POST['forsale'];
		$prodsupp = $_POST['prodsupp'];
		$jobs = $_POST['jobs'];

		/* Remove all current terms and reset where checked */
		$wpdb->query('DELETE FROM cwt_email_prefs WHERE user_id = '.$current_user->ID);
		for($c=0; $c<sizeof($carwashes); $c++) {
			$wpdb->query('INSERT INTO cwt_email_prefs (user_id, term_id) VALUES ('.$current_user->ID.','.$carwashes[$c].')');
		}
		for($f=0; $f<sizeof($forsale); $f++) {
			$wpdb->query('INSERT INTO cwt_email_prefs (user_id, term_id) VALUES ('.$current_user->ID.','.$forsale[$f].')');
		}
		for($p=0; $p<sizeof($prodsupp); $p++) {
			$wpdb->query('INSERT INTO cwt_email_prefs (user_id, term_id) VALUES ('.$current_user->ID.','.$prodsupp[$p].')');
		}
		for($j=0; $j<sizeof($jobs); $j++) {
			$wpdb->query('INSERT INTO cwt_email_prefs (user_id, term_id) VALUES ('.$current_user->ID.','.$jobs[$j].')');
		}

	/*
		wp_set_object_terms( $current_user->ID, $carwashes, 'cwt_carwash_types', false);
		clean_object_term_cache( $current_user->ID, 'cwt_carwash_types' );

		wp_set_object_terms( $current_user->ID, $forsale, 'cwt_forsale_types', false);
		clean_object_term_cache( $current_user->ID, 'cwt_forsale_types' );

		wp_set_object_terms( $current_user->ID, $jobs, 'cwt_job_types', false);
		clean_object_term_cache( $current_user->ID, 'cwt_job_types' );

		wp_set_object_terms( $current_user->ID, $prodsupp, 'cwt_prodsupp_cats', false);
		clean_object_term_cache( $current_user->ID, 'cwt_prodsupp_cats' );*/

		unset($_POST);
		wp_redirect('/my-cwt/?preferences=true');
	} else if($_POST["action"] == 'edit-preferences') {
		update_user_meta( $current_user->ID, 'emails_daily', esc_attr( $_POST['emails_daily'] ) );
		update_user_meta( $current_user->ID, 'emails_private', esc_attr( $_POST['emails_private'] ) );

		unset($_POST);
		wp_redirect('/my-cwt/?preferences=true');
	}
}


include 'includes/header.php';
?>
	<!-- MyCWT - Edit Preferences -->
        
        <h1><?php echo (get_field('display_title', $pageObj->ID) != '') ? get_field('display_title', $pageObj->ID) : $pageObj->post_title; ?></h1>
        
        <article>
        	<h2><?php echo $pageObj->post_title; ?></h2>

        	<?php 
        		if($current_user->roles[0] != 'guest' && $current_user->roles[0] != 'estateagent') {
	        		if($pageObj->post_content != '')
	        			echo '<div class="purpleBox">'.apply_filters('the_content', $pageObj->post_content).'</div>'; 
	        	} else if($current_user->roles[0] == 'guest') {
	        		$guestText = get_page(1960);
					if($guestText->post_content != '')
	        			echo '<div class="purpleBox">'.apply_filters('the_content', $guestText->post_content).'</div>'; 
	        	} else if($current_user->roles[0] == 'estateagent') {
	        		$estateText = get_page(1962);
					if($estateText->post_content != '')
	        			echo '<div class="purpleBox">'.apply_filters('the_content', $estateText->post_content).'</div>'; 
	        	}
        	?>

        	<div class="formBox">
        		<?php
					$emails_daily = get_the_author_meta('emails_daily', $current_user->ID);
        			$emails_private = get_the_author_meta('emails_private', $current_user->ID);
        			$emails_priority = get_the_author_meta('emails_priority', $current_user->ID);
        		?>
        		<form method="post">
        		<?php if($current_user->roles[0] != 'guest' && $current_user->roles[0] != 'estateagent') { ?>
				<label>Receive daily email?</label>
                <div class="options">
					<input value="yes" id="emails_daily_yes" name="emails_daily" type="radio" <?php if($emails_daily == 'yes') echo 'checked="checked" '; ?> />
					<label class="inline" for="emails_daily_yes">Yes</label>
					<input value="no" id="emails_daily_no" name="emails_daily" type="radio" <?php if($emails_daily == 'no' || $emails_daily == '') echo 'checked="checked" '; ?> />
					<label class="inline" for="emails_daily_no">No</label>
                </div>
                <?php } ?>

				<label>Allow other users to send you messages?</label>
                <div class="options">
					<input value="yes" id="emails_private_yes" name="emails_private" type="radio" <?php if($emails_private == 'yes') echo 'checked="checked" '; ?> />
					<label class="inline" for="emails_private_yes">Yes</label>
					<input value="no" id="emails_private_no" name="emails_private" type="radio" <?php if($emails_private == 'no' || $emails_private == '') echo 'checked="checked" '; ?> />
					<label class="inline" for="emails_private_no">No</label>
                </div>
                        
		        <input type="submit" value="Update" id="submit" name="submit" />
		        <input type="hidden" name="action" value="edit-preferences" />
		        <?php wp_nonce_field( 'email-preferences' ); ?>
        		</form>
        	</div>


        	<?php if($current_user->roles[0] != 'estateagent') { ?>
        	<h2>My Priority Emails</h2>
        	<div class="clear"></div>

        	<?php if($emails_priority == 0) { ?>
            <div class="paymentRow">
                <div class="paymentBox">
                    <h3>10 Priority Messages</h3>
                    <h4 class="price"><?php echo outputPrice($priceMessage_10); ?></h4>
                    <form id="paypal1Month" method="post" action= "https://www.paypal.com/cgi-bin/webscr">
                    <input type="hidden" name="cmd" value="_xclick" />
                    <input type="hidden" name="business" value="btrepca@gmail.com" />
                    <input type="hidden" name="item_name" value="Priority Messages (10 emails)" />
                    <input type="hidden" name="amount" value="<?php echo $priceMessage_10; ?>" />
                    <input type="hidden" name="no_shipping" value="1" />
                    <input type="hidden" name="return" value="/paypal-pdt.php" />
                    <input type="hidden" name="on1" value="Booking Name" />
                    <input type="hidden" name="os1" value="<?php echo $current_user->user_firstname.' '.$current_user->user_lastname; ?>" />
                    <input type="hidden" name="on2" value="TxID" />
                    <input type="hidden" name="os2" value="<?php echo $transactionID; ?>" />
                    <input type="hidden" name="on3" value="userID" />
                    <input type="hidden" name="os3" value="<?php echo $current_user->ID; ?>" />
                    <input type="hidden" name="currency_code" value="GBP" />
                    </form>

                    <div class="submit">
                        <a href="book-now/" onClick="document.getElementById('paypal1Month').submit();return false;" title="Proceed to Paypal">
                        Proceed to Paypal</a>
                    </div>
                </div>

                <div class="paymentBox">
                    <h3>20 Priority Messages</h3>
                    <h4 class="price"><?php echo outputPrice($priceMessage_20); ?></h4>
                    <form id="paypal3Month" method="post" action= "https://www.paypal.com/cgi-bin/webscr">
                    <input type="hidden" name="cmd" value="_xclick" />
                    <input type="hidden" name="business" value="btrepca@gmail.com" />
                    <input type="hidden" name="item_name" value="Priority Messages (20 emails)" />
                    <input type="hidden" name="amount" value="<?php echo $priceMessage_20; ?>" />
                    <input type="hidden" name="no_shipping" value="1" />
                    <input type="hidden" name="return" value="/paypal-pdt.php" />
                    <input type="hidden" name="on1" value="Booking Name" />
                    <input type="hidden" name="os1" value="<?php echo $current_user->user_firstname.' '.$current_user->user_lastname; ?>" />
                    <input type="hidden" name="on2" value="TxID" />
                    <input type="hidden" name="os2" value="<?php echo $transactionID; ?>" />
                    <input type="hidden" name="on3" value="userID" />
                    <input type="hidden" name="os3" value="<?php echo $current_user->ID; ?>" />
                    <input type="hidden" name="currency_code" value="GBP" />
                    </form>

                    <div class="submit">
                        <a href="book-now/" onClick="document.getElementById('paypal3Month').submit();return false;" title="Proceed to Paypal">
                        Proceed to Paypal</a>
                    </div>
                </div>

                <div class="paymentBox">
                    <h3>30 Priority Messages</h3>
                    <h4 class="price"><?php echo outputPrice($priceMessage_30); ?></h4>
                    <form id="paypal6Month" method="post" action= "https://www.paypal.com/cgi-bin/webscr">
                    <input type="hidden" name="cmd" value="_xclick" />
                    <input type="hidden" name="business" value="btrepca@gmail.com" />
                    <input type="hidden" name="item_name" value="Priority Messages (30 emails)" />
                    <input type="hidden" name="amount" value="<?php echo $priceMessage_30; ?>" />
                    <input type="hidden" name="no_shipping" value="1" />
                    <input type="hidden" name="return" value="/paypal-pdt.php" />
                    <input type="hidden" name="on1" value="Booking Name" />
                    <input type="hidden" name="os1" value="<?php echo $current_user->user_firstname.' '.$current_user->user_lastname; ?>" />
                    <input type="hidden" name="on2" value="TxID" />
                    <input type="hidden" name="os2" value="<?php echo $transactionID; ?>" />
                    <input type="hidden" name="on3" value="userID" />
                    <input type="hidden" name="os3" value="<?php echo $current_user->ID; ?>" />
                    <input type="hidden" name="currency_code" value="GBP" />
                    </form>

                    <div class="submit">
                        <a href="book-now/" onClick="document.getElementById('paypal6Month').submit();return false;" title="Proceed to Paypal">
                        Proceed to Paypal</a>
                    </div>
                </div>
                <div class="clear"></div>
            </div>
            <?php } else { ?>
            	<div class="formBox">You currently have <b><?php echo $emails_priority; ?></b> priority emails remaining</div>
        	<?php } ?>
        	<?php } ?>

        	<?php if($current_user->roles[0] != 'guest' && $current_user->roles[0] != 'estateagent') { ?>
        	<h2>My Daily Alerts</h2>
            
            <div class="formBox blank">
            <form method="post" id="adduser" action="<?php //the_permalink(); ?>">
                
                <?php if ( sizeof($error) > 0 ) echo '<p class="error">' . implode("<br />", $error) . '</p>'; ?>

                <div class="formBoxSplit">
	                <label>Car Washes</label>
	                <div class="options">
					<?php 
						$types = get_terms('cwt_carwash_types', array('hide_empty'=>false,'parent'=>0)); 
						for($t=0; $t<sizeof($types); $t++)
						{
							echo '<input type="checkbox" id="pref'.$types[$t]->term_id.'" name="carwashes[]" value="'.$types[$t]->term_id.'" ';
							if(checkEmailPref( $current_user->ID, $types[$t]->term_id ))
								echo 'checked="checked" ';
							echo '/>';

							echo '<label for="pref'.$types[$t]->term_id.'">'.$types[$t]->name.'</label>';

							echo '<div class="clear"></div>';
						}
						
					?>
	                </div>
					<div class="clear"></div>
                </div>

                <div class="formBoxSplit">
	                <label>Car Washes For Sale</label>
	                <div class="options">
					<?php 
						$types = get_terms('cwt_forsale_types', array('hide_empty'=>false,'parent'=>0)); 
						for($t=0; $t<sizeof($types); $t++)
						{
							echo '<input type="checkbox" id="pref'.$types[$t]->term_id.'" name="forsale[]" value="'.$types[$t]->term_id.'" ';
							if(checkEmailPref( $current_user->ID, $types[$t]->term_id ))
								echo 'checked="checked" ';
							echo '/>';

							echo '<label for="pref'.$types[$t]->term_id.'">'.$types[$t]->name.'</label>';

							echo '<div class="clear"></div>';
						}
						
					?>
	                </div>
					<div class="clear"></div>
                </div>

                <div class="formBoxSplit">
	                <label>Products &amp; Suppliers</label>
	                <div class="options">
					<?php 
						$types = get_terms('cwt_prodsupp_cats', array('hide_empty'=>false,'parent'=>0)); 
						for($t=0; $t<sizeof($types); $t++)
						{
							echo '<input type="checkbox" id="pref'.$types[$t]->term_id.'" name="prodsupp[]" value="'.$types[$t]->term_id.'" ';
							if(checkEmailPref( $current_user->ID, $types[$t]->term_id ))
								echo 'checked="checked" ';
							echo '/>';

							$typesSub = get_terms('cwt_prodsupp_cats', array('hide_empty'=>false,'parent'=>$types[$t]->term_id,'orderby'=>'term_order')); 
							
							echo '<label for="pref'.$types[$t]->term_id.'">'.$types[$t]->name.'</label>';

							if(sizeof($typesSub) > 0)
								echo '<span class="catsControl" rel="slider'.$t.'">(show subcategories)</span>';

							echo '<div class="clear"></div>';

							if(sizeof($typesSub) > 0) {
								echo '<div class="catsSlider" id="slider'.$t.'">';
								for($s=0; $s<sizeof($typesSub); $s++)
								{
									echo '<input type="checkbox" id="pref'.$types[$t]->term_id.'" name="prodsupp[]" value="'.$typesSub[$s]->term_id.'" ';
									if(checkEmailPref( $current_user->ID, $typesSub[$s]->term_id ))
										echo 'checked="checked" ';
									echo '/>';
									echo '<label for="pref'.$types[$t]->term_id.'">'.$typesSub[$s]->name.'</label><div class="clear"></div>';
								}
								echo '</div>';
								echo '';
							}
						}
						
					?>
	                </div>
					<div class="clear"></div>
                </div>

                <div class="formBoxSplit">
	                <label>Jobs</label>
	                <div class="options">
					<?php 
						$types = get_terms('cwt_job_types', array('hide_empty'=>false,'parent'=>0)); 
						for($t=0; $t<sizeof($types); $t++)
						{
							echo '<input type="checkbox" id="pref'.$types[$t]->term_id.'" name="jobs[]" value="'.$types[$t]->term_id.'" ';
							if(checkEmailPref( $current_user->ID, $types[$t]->term_id ))
								echo 'checked="checked" ';
							echo '/>';

							echo '<label for="pref'.$types[$t]->term_id.'">'.$types[$t]->name.'</label>';

							echo '<div class="clear"></div>';
						}
						
					?>
	                </div>
					<div class="clear"></div>
                </div>
                        
		        <input type="submit" value="<?php echo $formLabel; ?>" id="submit" name="submit" />
		        <input type="hidden" name="action" value="edit-post" />
		        <?php wp_nonce_field( 'new-post' ); ?>
	        	</form>
            </div>
            <?php } ?>
        </article>
        
        <section>
        	<?php include 'includes/menu-submenu.php'; ?>
            
            <?php include 'includes/adverts-menu.php'; ?>
        </section>
    </div>

<?php 
include 'includes/footer.php';
get_footer(); 
?> 