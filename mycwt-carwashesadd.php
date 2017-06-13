<?php
/**
 * @package WordPress
 * @subpackage carwashtrader
 * template name: my cwt - add carwash
*/

get_header(); 
include 'includes/secure.php';

global $current_user, $wp_roles;
get_currentuserinfo();
$error = array();   
$terms = array(); 

$formAction = 'new-post';
$formLabel = 'Publish';

//wp-editor settings
$editorSettings = array('media_buttons' => false,
						'textarea_rows' => 6,
						'teeny' => true,
						'quicktags' => false);
                    
if ( 'POST' == $_SERVER['REQUEST_METHOD'] && !empty( $_POST['action'] )) {

	// Do some minor form validation to make sure there is content
	if (!empty ($_POST['addpost_title'])) $title =  sanitize_text_field($_POST['addpost_title']); 
	else  $error[] = 'Please enter the name of the car wash';
	
	$allowableTags = '<p><blockquote><strong><em><del><ul><ol><li>';

	if (!empty ($_POST['addpost_description'])) {
		$description = $_POST['addpost_description'];
		$description = strip_tags(html_entity_decode($description), '<p><blockquote><strong><em><del><ul><ol><li>');
	}
	else $error[] = 'Please enter a brief description of the car wash';
	
	if (!empty ($_POST['addpost_address'])) $address =  sanitize_text_field($_POST['addpost_address']); 
		else  $error[] = 'Please enter the address of the car wash';
	
	if(strpos($_POST['addpost_postcode'], ' ') == '') {
		$postcode =  $_POST['addpost_postcode'];
		$error[] = 'Please enter a valid postcode, including the space';
	}
	else if (!empty ($_POST['addpost_postcode'])) $postcode =  sanitize_text_field($_POST['addpost_postcode']); 
		else  $error[] = 'Please enter the postcode of the car wash';

	if(sizeof($_POST["addpost_cats"]) == 0)
		$error[] = 'Please select the type(s) of carwash';
	else
		$terms = $_POST["addpost_cats"];

	// Add the content of the form to $post as an array
	$post = array(
		'post_title'	=> $title,
		'post_content' => $description,
		'tax_input'		=> array('cwt_carwash_types' => $_POST['addpost_cats']), 
		'post_status'	=> 'publish',		
		'post_type'	=> $_POST['addpost_type'] 
	);
	
	$prices = sanitize_text_field($_POST['addpost_prices']);
	$openinghours = sanitize_text_field($_POST['addpost_openinghours']);
	$contactnumber = sanitize_text_field($_POST['addpost_contactnumber']);
	$website = sanitize_text_field($_POST['addpost_website']);
	
	if($_POST['action'] == 'new-post') {
		//upload images
		if (sizeof($error) == 0) {
			
			$newCarwashID = wp_insert_post($post);
			 
			if (!empty($_FILES['addpost_attachment']['name'])) {
				foreach ($_FILES as $file_id => $array) {
					if($file_id=="addpost_attachment") {
						$featuredImage = true;
						$attachment_id = insert_attachment($file_id,$newCarwashID,$featuredImage);
					} else {
						$featuredImage = false;
						$attachment_id = insert_attachment($file_id,$newCarwashID,$featuredImage);
					}
				}
			}
			
			//set geocode for map
			$geocode = '';
			$resp = wp_remote_get( "https://maps.google.com/maps/api/geocode/json?address=".urlencode($postcode)."&key=AIzaSyDO05i3qVrVSj441LaSXNyxkG8zeaEQOHM&sensor=false" );
			if ( 200 == $resp['response']['code'] ) {
				$body = $resp['body'];
				$data = json_decode($body);
				if($data->status=="OK"){
					$latitude = $data->results[0]->geometry->location->lat;
					$longitude = $data->results[0]->geometry->location->lng;
					$geocode = $latitude.','.$longitude;
				}
			}
		
			//__update_post_meta($newCarwashID, 'carwash_name', $title);
			//__update_post_meta($newCarwashID, 'carwash_description', $description);
			if($address != '')
				__update_post_meta($newCarwashID, 'carwash_address', $address);
			if($postcode != '')
				__update_post_meta($newCarwashID, 'carwash_postcode', $postcode);
			if($prices != '')
				__update_post_meta($newCarwashID, 'carwash_prices', $prices);
			if($openinghours != '')
				__update_post_meta($newCarwashID, 'carwash_openinghours', $openinghours);
			if($contactnumber != '')
				__update_post_meta($newCarwashID, 'carwash_contactnumber', $contactnumber);
			if($website != '')
				__update_post_meta($newCarwashID, 'carwash_website', $website);
			if($geocode != '')
				__update_post_meta($newCarwashID, 'carwash_geocode', $geocode);
	
			do_action('wp_insert_post', 'wp_insert_post');
			unset($_POST);

			//estate agents sell by default
			if($current_user->roles[0] == 'estateagent')	
				$redirectURL = '/my-cwt/car-washes/sell-car-wash/?id='.$newCarwashID;
			else	
				$redirectURL = '/my-cwt/car-washes/?added=true';
			
			wp_redirect($redirectURL);
		}
	} else if($_POST['action'] == 'edit-post') {

		$formAction = 'edit-post';
		$formLabel = 'Edit';

		$editPostID = $_POST["postID"];
		$carwash = get_post($editPostID);
		$terms = wp_get_post_terms( $editPostID, 'cwt_carwash_types', array("fields" => "ids"));
			
		$featured = get_field('_thumbnail_id', $carwash->ID );
		
		$gallery = get_attached_media('image', $carwash->ID);
		unset($gallery[$featured]);//remove featured image from gallery array
		$galleryIDs = array_keys($gallery);
		
		if(sizeof($error) == 0) {
			$carwashID = $_POST["postID"];
			$post = array(
				'ID' => $carwashID,
				'post_title'	=> $title,
				'post_content' => $description,
				'tax_input'		=> array('cwt_carwash_types' => $_POST['addpost_cats'])
			);
			
			//remove images if checked
			if($_POST["delete0"] == 'yes') {
				$post_thumbnail_id = get_post_thumbnail_id( $_POST["deleteID0"] ); //get attachment id
				delete_post_thumbnail($_POST["deleteID0"]);  //remove featured image relationship
				wp_delete_attachment( $post_thumbnail_id, 'true' ); //remove attachment itself
				
				//set next attachment as featured image if one exists
				$attachments = get_children(array(
					'post_parent' => $carwashID, 
					'post_status' => 'inherit', 
					'post_type' => 'attachment', 
					'post_mime_type' => 'image', 
					'order' => 'ASC', 
					'orderby' => 'menu_order'
				));
				$attachmentKeys = array_keys($attachments);
				if ($attachments) {
					set_post_thumbnail($carwashID, $attachments[$attachmentKeys[0]]->ID);
				}
			}
			for($d=1; $d<=5; $d++) {
				if($_POST["delete".$d] == 'yes')
					wp_delete_attachment( $_POST["deleteID".$d], 'true' );
			}
			
			//add new images if set
			foreach ($_FILES as $file_id => $array) {
				if($file_id=="addpost_attachment") {
					$featuredImage = true;
					$attachment_id = insert_attachment($file_id,$carwashID,$featuredImage);
				} else {
					$featuredImage = false;
					$attachment_id = insert_attachment($file_id,$carwashID,$featuredImage);
				}
			}
			
			//set geocode for map
			$geocode = '';
			$resp = wp_remote_get( "https://maps.google.com/maps/api/geocode/json?address=".urlencode($postcode)."&key=AIzaSyDO05i3qVrVSj441LaSXNyxkG8zeaEQOHM&sensor=false" );
			if ( 200 == $resp['response']['code'] ) {
				$body = $resp['body'];
				$data = json_decode($body);
				if($data->status=="OK"){
					$latitude = $data->results[0]->geometry->location->lat;
					$longitude = $data->results[0]->geometry->location->lng;
					$geocode = $latitude.','.$longitude;
				}
			}
			
			//__update_post_meta($carwashID, 'carwash_name', $title);
			//__update_post_meta($carwashID, 'carwash_description', $description);
			__update_post_meta($carwashID, 'carwash_address', $address);
			__update_post_meta($carwashID, 'carwash_postcode', $postcode);
			__update_post_meta($carwashID, 'carwash_prices', $prices);
			__update_post_meta($carwashID, 'carwash_openinghours', $openinghours);
			__update_post_meta($carwashID, 'carwash_contactnumber', $contactnumber);
			__update_post_meta($carwashID, 'carwash_website', $website);
			__update_post_meta($carwashID, 'carwash_geocode', $geocode);
			
			wp_update_post($post);
			do_action('wp_update_post', 'wp_update_post');
			unset($_POST);
			wp_redirect('/my-cwt/car-washes/?edited=true');
		}
	}
}
else if(isset($_GET["id"])) {
	
	$editPostID = $_GET["id"];
	if(is_numeric($editPostID)) {
		$carwash = get_post($editPostID);
		if($carwash->post_author != $current_user->ID)
			wp_redirect('/my-cwt/car-washes/');
		else {
			$title = $carwash->post_title;
			$description = html_entity_decode($carwash->post_content);
			$address = get_field('carwash_address', $carwash->ID);
			$postcode = get_field('carwash_postcode', $carwash->ID);
			$prices = html_entity_decode(get_field('carwash_prices', $carwash->ID));
			$openinghours = html_entity_decode(get_field('carwash_openinghours', $carwash->ID));
			$contactnumber = get_field('carwash_contactnumber', $carwash->ID);
			$website = get_field('carwash_website', $carwash->ID);
			
			//array of all types checked
			$terms = wp_get_post_terms( $carwash->ID, 'cwt_carwash_types', array("fields" => "ids"));
			
			$featured = get_field('_thumbnail_id', $carwash->ID );
			
			$gallery = get_attached_media('image', $carwash->ID);
			unset($gallery[$featured]);//remove featured image from gallery array
			$galleryIDs = array_keys($gallery);
		
			$formAction = 'edit-post';
			$formLabel = 'Edit';
		}
	} else { wp_redirect('/my-cwt/car-washes/'); }
}
else {
	$terms = array();
	$formAction = 'new-post';
	$formLabel = 'Publish';
}


include 'includes/header.php';
?>
	<!-- MyCWT - Carwashes Add -->
        
        <h1><?php echo (get_field('display_title', $pageObj->ID) != '') ? get_field('display_title', $pageObj->ID) : $pageObj->post_title; ?></h1>
        
        <article>
        	<h2><?php echo $pageObj->post_title; ?></h2>
        	<?php echo apply_filters('the_content', $pageObj->post_content); ?>
            
            <?php if($formAction == 'edit-post') { ?>
            <div class="listingEdit">
            	<a href="javascript:deleteListing(<?php echo $editPostID; ?>);" title="Delete Car Wash" onclick="return confirm('Are you sure? This action cannot be undone');">Delete this Car Wash</a>
                
                <form id="deleteListing" method="post" action="/my-cwt/car-washes/">
                <input type="hidden" name="listingID" id="listingID" value="" />
				<input type="hidden" name="action" value="deleteCarWash" />
                </form>
            </div>
            <div class="listingSell">
				<a href="/my-cwt/car-washes/sell-car-wash/?id=<?php echo $editPostID; ?>" title="Sell this Car Wash">Sell this carwash</a>
			</div>
			<?php } ?>

            <div class="formBox">
                
                <?php if ( !is_user_logged_in() ) : ?>
                    <p class="warning">
                        <?php _e('You must be logged in to edit your profile.', 'profile'); ?>
                    </p><!-- .warning -->
				<?php else : ?>
                    <?php if ( sizeof($error) > 0 ) echo '<p class="error">' . implode("<br />", $error) . '</p>'; ?>
                    
                    <form method="post" id="adduser" action="<?php //the_permalink(); ?>" enctype="multipart/form-data">
                    <label for="addpost_title">Title *</label>
                    <input type="text" id="addpost_title" size="20" name="addpost_title" value="<?php echo $title; ?>" />
                    
                    <label for="addpost_description">Description *
                    	<span>(NB. Please use the toolbar to format your text as you would like it to appear on your listing. Links can be added using the icon third from the right)</span></label>
                   	<?php wp_editor( $description, 'addpost_description', $editorSettings); ?> 
                    
                    <label for="addpost_attachment">Main Image</label>
                    <?php
						if($featured) {
							echo '<div class="thumb">'.get_the_post_thumbnail($carwash->ID, array(70,70));
							echo '<input type="checkbox" name="delete0" id="delete0" value="yes" />';
							echo '<input type="hidden" name="deleteID0" id="deleteID0" value="'.$carwash->ID.'" />';
							echo '<label for="delete0">Delete this image</label></div>';
						}
						else echo '<input type="file" id="addpost_attachment" name="addpost_attachment">';
					?>
                    <div class="clear"></div>
                    
                    
                    <label for="addpost_address">Address *</label>
                    <input type="text" id="addpost_address" size="20" name="addpost_address" value="<?php echo $address; ?>" />
                    
                    <label for="addpost_postcode">Postcode *</label>
                    <input type="text" id="addpost_postcode" size="20" name="addpost_postcode" value="<?php echo $postcode; ?>" />
                    <div class="inputNote">NB. This must be a correctly formatted UK postcode, including the space, for the map function to work correctly</div>
                    
                    <label for="addpost_contactnumber">Contact Number</label>
                    <input type="text" id="addpost_contactnumber" size="20" name="addpost_contactnumber" value="<?php echo $contactnumber; ?>" />
                    
                    <label for="addpost_website">Website</label>
                    <input type="text" id="addpost_website" size="20" name="addpost_website" value="<?php echo $website; ?>" />
                    
                    <?php
						for($g=0; $g<5; $g++)
						{
							echo '<label for="addpost_attachment'.($g+1).'">Gallery Image '.($g+1).'</label>';
							if(sizeof($gallery) > $g) {
								echo '<div class="thumb">'.wp_get_attachment_image($gallery[$galleryIDs[$g]]->ID, array(70,70));
								echo '<input type="checkbox" name="delete'.($g+1).'" id="delete'.($g+1).'" value="yes" />';
								echo '<input type="hidden" name="deleteID'.($g+1).'" id="deleteID'.($g+1).'" value="'.$gallery[$galleryIDs[$g]]->ID.'" />';
								echo '<label for="delete'.($g+1).'">Delete this image</label></div>';
							}
							else {
								echo '<input type="file" id="addpost_attachment'.($g+1).'" name="addpost_attachment'.($g+1).'">';
							}
							echo '<div class="clear"></div>';
						}
					?>
                    
                    <label for="addpost_openinghours">Opening Hours
                    	<span>(NB. Please use the toolbar to format your text as you would like it to appear on your listing. Links can be added using the icon third from the right)</span></label>
                   	<?php wp_editor( $openinghours, 'addpost_openinghours', $editorSettings); ?> 
                    
                    <label for="addpost_prices">Prices
                    	<span>(NB. Please use the toolbar to format your text as you would like it to appear on your listing. Links can be added using the icon third from the right)</span></label>
                   	<?php wp_editor( $prices, 'addpost_prices', $editorSettings); ?> 
                    
                    <div class="clear"></div>
                    <label>Type</label>
                    <div class="options">
					<?php 
						$types = get_terms('cwt_carwash_types', array('hide_empty'=>false)); 
						for($t=0; $t<sizeof($types); $t++)
						{
							echo '<input type="checkbox" id="addpost_cats'.$t.'" name="addpost_cats[]" value="'.$types[$t]->term_id.'" ';
							if(in_array($types[$t]->term_id, $terms))
								echo 'checked="checked" ';
							echo '/>';
							echo '<label for="addpost_cats'.$t.'">'.$types[$t]->name.'</label><div class="clear"></div>';
						}
						
					?>
                    </div>
                        
                    <input type="submit" value="<?php echo $formLabel; ?>" id="submit" name="submit" /></p>
                    <?php if(isset($editPostID)) echo '<input type="hidden" name="postID" value="'.$editPostID.'" />'; ?>
                    <input type="hidden" name="addpost_type" id="addpost_type" value="cwt_carwashes" />
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