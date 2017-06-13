<?php
/**
 * @package WordPress
 * @subpackage carwashtrader
 * template name: my cwt - sell carwash
*/

get_header(); 
include 'includes/secure.php';

global $current_user, $wp_roles;
get_currentuserinfo();
$error = array();   
$terms = array(); 

//wp-editor settings
$editorSettings = array('media_buttons' => false,
						'textarea_rows' => 6,
						'teeny' => true,
						'quicktags' => false);
                    
if ( 'POST' == $_SERVER['REQUEST_METHOD'] && !empty( $_POST['action'] )) {

	$carwashID = $_POST["postID"];
	$editPostID = $carwashID;
	$carwash = get_post($carwashID);
	
	//array of all types checked
	$terms = wp_get_post_terms( $carwash->ID, 'cwt_forsale_types', array("fields" => "ids"));

	// Do some minor form validation to make sure there is content
	if (!empty ($_POST['sale_advert_title'])) 
		$advert_title =  sanitize_text_field($_POST['sale_advert_title']); 
	else  $error[] = 'Please enter a title for the sale listing';

	if (!empty ($_POST['sale_listing_info'])) {
		$listing_info = $_POST['sale_listing_info'];
		$listing_info = strip_tags(html_entity_decode($listing_info), '<p><blockquote><strong><em><del><ul><ol><li>');
	}
	else $error[] = 'Please enter a brief summary of the sale listing';
	
	if (!empty ($_POST['sale_price'])) 
		$price =  $_POST['sale_price']; 
	else  $error[] = 'Please enter the price of the sale';
	
	if (!empty ($_POST['sale_purchase_link'])) 
		$purchase_link =  sanitize_text_field($_POST['sale_purchase_link']); 
	else  $error[] = 'Please enter the link where this car wash can be purchased';

	if(sizeof($_POST["sale_cats"]) == 0)
		$error[] = 'Please select the type of sale';
	else
		$terms = $_POST["sale_cats"];

	$for_sale = sanitize_text_field($_POST['sale_for_sale']);
	$business_size = sanitize_text_field($_POST['sale_business_size']);
	$leasehold_freehold = sanitize_text_field($_POST['sale_leasehold_freehold']);
	$leasehold_years = sanitize_text_field($_POST['sale_leasehold_years']);
	
	if($_POST['action'] == 'edit-post') {
		if(sizeof($error) == 0) {
			$post = array(
				'ID' => $carwashID,
				'tax_input'		=> array('cwt_forsale_types' => $_POST['sale_cats'])
			);
			
			__update_post_meta($carwashID, 'sale_for_sale', $for_sale);
			__update_post_meta($carwashID, 'sale_advert_title', $advert_title);
			__update_post_meta($carwashID, 'sale_listing_info', $listing_info);
			__update_post_meta($carwashID, 'sale_business_size', $business_size);
			__update_post_meta($carwashID, 'sale_price', $price);
			__update_post_meta($carwashID, 'sale_leasehold_freehold', $leasehold_freehold);
			__update_post_meta($carwashID, 'sale_leasehold_years', $leasehold_years);
			__update_post_meta($carwashID, 'sale_purchase_link', $purchase_link);
			
			wp_update_post($post);
			do_action('wp_update_post', 'wp_update_post');
			unset($_POST);

			if(get_field('premium_paypalID',$carwashID) == '')
				wp_redirect('/my-cwt/car-washes/payment/?id='.$carwashID);
			else
				wp_redirect('/my-cwt/car-washes/?edited=true');
		} else {
			$title = $carwash->post_title;
			$description = html_entity_decode($carwash->post_content);
			$address = html_entity_decode(get_field('carwash_address', $carwash->ID));
			$postcode = html_entity_decode(get_field('carwash_postcode', $carwash->ID));
			$prices = html_entity_decode(get_field('carwash_prices', $carwash->ID));
			$openinghours = html_entity_decode(get_field('carwash_openinghours', $carwash->ID));
			$contactnumber = html_entity_decode(get_field('carwash_contactnumber', $carwash->ID));
			$website = html_entity_decode(get_field('carwash_website', $carwash->ID));

			$formAction = 'edit-post';
			$formLabel = 'Sell Car Wash';
		}
	}
} else {

	
	$editPostID = $_GET["id"];
	if(is_numeric($editPostID)) {
		$carwash = get_post($editPostID);
		if($carwash->post_author != $current_user->ID)
			wp_redirect('/my-cwt/car-washes/');
		else {
			//regular listing fields
			$title = $carwash->post_title;
			$description = html_entity_decode($carwash->post_content);
			$address = html_entity_decode(get_field('carwash_address', $carwash->ID));
			$postcode = html_entity_decode(get_field('carwash_postcode', $carwash->ID));
			$prices = html_entity_decode(get_field('carwash_prices', $carwash->ID));
			$openinghours = html_entity_decode(get_field('carwash_openinghours', $carwash->ID));
			$contactnumber = html_entity_decode(get_field('carwash_contactnumber', $carwash->ID));
			$website = html_entity_decode(get_field('carwash_website', $carwash->ID));
			
			//for sale fields
			$for_sale = html_entity_decode(get_field('sale_for_sale', $carwash->ID));
			$advert_title = html_entity_decode(get_field('sale_advert_title', $carwash->ID));
			$listing_info = html_entity_decode(get_field('sale_listing_info', $carwash->ID));
			$business_size = html_entity_decode(get_field('sale_business_size', $carwash->ID));
			$price = html_entity_decode(get_field('sale_price', $carwash->ID));
			$leasehold_freehold = html_entity_decode(get_field('sale_leasehold_freehold', $carwash->ID));
			$leasehold_years = html_entity_decode(get_field('sale_leasehold_years', $carwash->ID));
			$purchase_link = html_entity_decode(get_field('sale_purchase_link', $carwash->ID));
			
			//array of all types checked
			$terms = wp_get_post_terms( $carwash->ID, 'cwt_forsale_types', array("fields" => "ids"));
		
			$formAction = 'edit-post';
			$formLabel = 'Sell Car Wash';
		}
	} else { wp_redirect('/my-cwt/car-washes/'); }
}


include 'includes/header.php';
?>
	<!-- MyCWT - Carwashes Sell -->
        
        <h1><?php echo (get_field('display_title', $pageObj->ID) != '') ? get_field('display_title', $pageObj->ID) : $pageObj->post_title; ?></h1>
        
        <article>
        	<h2><?php echo $pageObj->post_title; ?></h2>
        	<?php echo apply_filters('the_content', $pageObj->post_content); ?>
            
            <div class="listingEdit">
				<a href="/my-cwt/car-washes/" title="Back to Car Washes">Back to Car Washes</a>
			</div>
			
			<?php echo outputCarwashListing($editPostID,$title,$description,$address,$postcode,$geocode,$contact,$website,$openinghours,$prices); ?>

			<div class="formBox">
                
                <?php if ( !is_user_logged_in() ) : ?>
                    <p class="warning">
                        <?php _e('You must be logged in to edit your profile.', 'profile'); ?>
                    </p><!-- .warning -->
				<?php else : ?>
                    <?php if ( sizeof($error) > 0 ) echo '<p class="error">' . implode("<br />", $error) . '</p>'; ?>
                    
                    <form method="post" id="adduser" action="<?php //the_permalink(); ?>">
                    <label for="sale_for_sale">For Sale? *</label>
                    <select id="sale_for_sale" name="sale_for_sale">
                    <option value="no" <?php if($for_sale == 'no') echo 'selected="selected"'; ?>>No</option>
                    <option value="yes" <?php if($for_sale == 'yes') echo 'selected="selected"'; ?>>Yes</option>
                    </select>
                    <div class="clear"></div>

                    <label for="sale_advert_title">Advert Title *</label>
                    <input type="text" id="sale_advert_title" size="20" name="sale_advert_title" value="<?php echo $advert_title; ?>" />
                    
                    <label for="sale_listing_info">Listing Details *<span>(NB. Please use the toolbar to format your text as you would like it to appear on your listing)</span></label>
                   	<?php wp_editor( $listing_info, 'sale_listing_info', $editorSettings); ?> 
                    
                    <label for="sale_business_size">Num Employees</label>
                    <input type="text" id="sale_business_size" size="20" name="sale_business_size" value="<?php echo $business_size; ?>" />
                    
                    <label for="sale_price">Price *</label>
                    <input type="text" id="sale_price" size="20" name="sale_price" value="<?php echo $price; ?>" />
                    
                    <label for="sale_leasehold_freehold">Leasehold / Freehold *</label>
                    <select id="sale_leasehold_freehold" name="sale_leasehold_freehold">
                    <option value="lease" <?php if($leasehold_freehold == 'lease') echo 'selected="selected"'; ?>>Leasehold</option>
                    <option value="free" <?php if($leasehold_freehold == 'free') echo 'selected="selected"'; ?>>Freehold</option>
                    </select>
                    <div class="clear"></div>

                    <label for="sale_leasehold_years">Leasehold Years Left</label>
                    <input type="text" id="sale_leasehold_years" size="20" name="sale_leasehold_years" value="<?php echo $leasehold_years; ?>" />
                    
                    <label for="sale_purchase_link">Purchase Link *</label>
                    <input type="text" id="sale_purchase_link" size="20" name="sale_purchase_link" value="<?php echo $purchase_link; ?>" />
                    
                    <div class="clear"></div>
                    <label>Type *</label>
                    <div class="options">
					<?php 
						$types = get_terms('cwt_forsale_types', array('hide_empty'=>false)); 
						for($t=0; $t<sizeof($types); $t++)
						{
							echo '<input type="checkbox" id="sale_cats'.$t.'" name="sale_cats[]" value="'.$types[$t]->term_id.'" ';
							if(in_array($types[$t]->term_id, $terms))
								echo 'checked="checked" ';
							echo '/>';
							echo '<label for="sale_cats'.$t.'">'.$types[$t]->name.'</label><div class="clear"></div>';
						}
					?>
                    </div>
                        
                    <input type="submit" value="<?php echo $formLabel; ?>" id="submit" name="submit" /></p>
                    <?php if(isset($editPostID)) echo '<input type="hidden" name="postID" value="'.$editPostID.'" />'; ?>
                    <input type="hidden" name="action" value="<?php echo $formAction; ?>" />
                    <?php wp_nonce_field( 'new-post' ); ?>
                    </form>
                <?php endif; ?>
            
            </div>
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