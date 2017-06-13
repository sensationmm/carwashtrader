<?php
/**
 * @package WordPress
 * @subpackage carwashtrader
 * template name: my cwt - edit supplier
*/

get_header(); 
include 'includes/secure.php';

global $current_user, $wp_roles;
get_currentuserinfo();
$error = array();   
$terms = array(); 

$searchSupplier = array(     
    'post_type' => 'cwt_suppliers',
    'post_status' => 'publish|draft',
    'author' => $current_user->ID
);
$supplier = query_posts($searchSupplier);

$editPostID = $supplier[0]->ID;

if($editPostID == '') {
	$newSupplier = array(
        'post_title'    => 'BUSINESS NAME HERE',
        'post_status'   => 'publish',       
        'post_type' => 'cwt_suppliers' ,
        'post_author' => $current_user->ID,
        'post_status' => 'draft'
    );
    $newSupplier = wp_insert_post($newSupplier);
    do_action('wp_insert_post', 'wp_insert_post');
	$editPostID = $newSupplier;

	$check = 'SELECT id FROM cwt_subscription WHERE user_id = '.$current_user->ID;
	$check_query = mysql_query($check);
	if(mysql_num_rows($check_query) == 0) {
		$book = 'INSERT INTO cwt_subscription (date, user_id) VALUES ("00000000", '.$current_user->ID.')';
	    $book_query = mysql_query($book) or die('Booking: '.$book.' - '.mysql_error());

	    $bookingID = mysql_insert_id();

	    $trans = 'INSERT INTO cwt_transaction (transaction_type, type_id) VALUES ("subscription", '.$bookingID.')';
	    $trans_query = mysql_query($trans) or die('Transaction: '.$trans.' - '.mysql_error());
	}
}

//wp-editor settings
$editorSettings = array('media_buttons' => false,
						'textarea_rows' => 6,
						'teeny' => true,
						'quicktags' => false);
                    
if ( 'POST' == $_SERVER['REQUEST_METHOD'] && !empty( $_POST['action'] )) {

	// Do some minor form validation to make sure there is content
	if (!empty ($_POST['addpost_title']) || $_POST['addpost_title'] == 'BUSINESS NAME HERE') 
			$title =  sanitize_text_field($_POST['addpost_title']); 
		else  $error[] = 'Please enter the name of your business';

	if (!empty ($_POST['addpost_description'])) {
		$description = $_POST['addpost_description'];
		$description = strip_tags(html_entity_decode($description), '<p><blockquote><strong><em><del><ul><ol><li>');
	}
	else $error[] = 'Please enter a brief description of the business';
	if (!empty ($_POST['addpost_address'])) $address =  sanitize_text_field($_POST['addpost_address']); 
		else  $error[] = 'Please enter the address of the business';
	
	if(strpos($_POST['addpost_postcode'], ' ') == '') {
		$postcode =  $_POST['addpost_postcode'];
		$error[] = 'Please enter a valid postcode, including the space';
	}
	else if (!empty ($_POST['addpost_postcode'])) $postcode =  sanitize_text_field(strip_tags($_POST['addpost_postcode'], '<p><b><i><u><ul><ol><li>')); 
		else  $error[] = 'Please enter the postcode of the business';

	// Add the content of the form to $post as an array
	$post = array(
		'post_title'	=> $title,
		'tax_input'		=> array('cwt_prodsupp_cats' => $_POST['addpost_cats']), 
		'post_status'	=> 'publish',		
		'post_type'	=> $_POST['addpost_type']
	);
	
	$contactnumber = sanitize_text_field($_POST['addpost_contactnumber']);
	$website = sanitize_text_field($_POST['addpost_website']);
	
	if($_POST['action'] == 'edit-post') {
			$supplierID = $_POST["postID"];

		$supplier = get_post($supplierID);

		//array of all types checked
		$terms = wp_get_post_terms( $supplier->ID, 'cwt_prodsupp_cats', array("fields" => "ids"));
		
		$featured = get_field('_thumbnail_id', $supplier->ID );
	
		$formAction = 'edit-post';
		$formLabel = 'Edit';

		if(sizeof($error) == 0) {
			$post = array(
				'ID' => $supplierID,
				'post_title'	=> $title,
				'post_content'	=> $description,
				'tax_input'		=> array('cwt_prodsupp_cats' => $_POST['addpost_cats']),
				'post_status'	=> 'publish'
			);

			//remove images if checked
			if($_POST["delete0"] == 'yes') {
				$post_thumbnail_id = get_post_thumbnail_id( $_POST["deleteID0"] ); //get attachment id
				delete_post_thumbnail($_POST["deleteID0"]);  //remove featured image relationship
				wp_delete_attachment( $post_thumbnail_id, 'true' ); //remove attachment itself
			}
			
			//add new images if set
			foreach ($_FILES as $file_id => $array) {
				if($file_id=="addpost_attachment") {
					$featuredImage = true;
					$attachment_id = insert_attachment($file_id,$supplierID,$featuredImage);
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
			
			//__update_post_meta($supplierID, 'supplier_business_name', $title);
			//__update_post_meta($supplierID, 'supplier_description', $description);
			__update_post_meta($supplierID, 'supplier_address', $address);
			__update_post_meta($supplierID, 'supplier_postcode', $postcode);
			__update_post_meta($supplierID, 'supplier_geocode', $geocode);
			__update_post_meta($supplierID, 'supplier_contactnumber', $contactnumber);
			__update_post_meta($supplierID, 'supplier_website', $website);
			
			wp_update_post($post);
			do_action('wp_update_post', 'wp_update_post');
			unset($_POST);
			wp_redirect('/my-cwt/?edited=true');
		}
	}
}
else if($editPostID != '' && is_numeric($editPostID)) {
	
	$supplier = get_post($editPostID);
	if($supplier->post_author != $current_user->ID)
		wp_redirect('/my-cwt/');
	else {
		$title = $supplier->post_title;
		$description = html_entity_decode($supplier->post_content);
		$address = get_field('supplier_address', $supplier->ID);
		$postcode = get_field('supplier_postcode', $supplier->ID);
		$contactnumber = get_field('supplier_contactnumber', $supplier->ID);
		$website = get_field('supplier_website', $supplier->ID);

		//array of all types checked
		$terms = wp_get_post_terms( $supplier->ID, 'cwt_prodsupp_cats', array("fields" => "ids"));
		
		$featured = get_field('_thumbnail_id', $supplier->ID );
	
		$formAction = 'edit-post';
		$formLabel = 'Edit';
	}
}
else {
	$terms = array();
	$formAction = 'new-post';
	$formLabel = 'Publish';
}


include 'includes/header.php';
?>
	<!-- MyCWT - Edit Supplier -->
        
        <h1><?php echo (get_field('display_title', $pageObj->ID) != '') ? get_field('display_title', $pageObj->ID) : $pageObj->post_title; ?></h1>
        
        <article>
        	<h2><?php echo $pageObj->post_title; ?></h2>

        	<?php echo apply_filters('the_content', $pageObj->post_content); ?>
            
            <div class="formBox">
                
                <?php if ( !is_user_logged_in() ) : ?>
                    <p class="warning">
                        <?php _e('You must be logged in to edit your profile.', 'profile'); ?>
                    </p><!-- .warning -->
				<?php else : ?>
                    <?php if ( sizeof($error) > 0 ) echo '<p class="error">' . implode("<br />", $error) . '</p>'; ?>
                    
                    <form method="post" id="adduser" action="<?php //the_permalink(); ?>" enctype="multipart/form-data">
                    <label for="addpost_title">Business name *</label>
                    <input type="text" id="addpost_title" size="20" name="addpost_title" value="<?php echo $title; ?>" />
                    
                    <label for="addpost_description">Description *
                    	<span>(NB. Please use the toolbar to format your text as you would like it to appear on your listing. Links can be added using the icon third from the right)</span></label>
                   	<?php wp_editor( $description, 'addpost_description', $editorSettings); ?> 
                    
                    <label for="addpost_attachment">Image</label>
                    <?php
						if($featured) {
							echo '<div class="thumb">'.get_the_post_thumbnail($supplier->ID, array(70,70));
							echo '<input type="checkbox" name="delete0" id="delete0" value="yes" />';
							echo '<input type="hidden" name="deleteID0" id="deleteID0" value="'.$supplier->ID.'" />';
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
                    
                    <div class="clear"></div>
                    <label>Categories</label>
                    <div class="options">
					<?php 
						$types = get_terms('cwt_prodsupp_cats', array('hide_empty'=>false,'parent'=>0)); 
						for($t=0; $t<sizeof($types); $t++)
						{
							echo '<input type="checkbox" id="addpost_cats'.$t.'" name="addpost_cats[]" value="'.$types[$t]->term_id.'" ';
							if(in_array($types[$t]->term_id, $terms))
								echo 'checked="checked" ';
							echo '/>';

							$typesSub = get_terms('cwt_prodsupp_cats', array('hide_empty'=>false,'parent'=>$types[$t]->term_id,'orderby'=>'term_order')); 
							
							echo '<label for="addpost_cats'.$t.'">'.$types[$t]->name.'</label>';

							if(sizeof($typesSub) > 0)
								echo '<span class="catsControl" rel="slider'.$t.'">(show subcategories)</span>';

							echo '<div class="clear"></div>';

							if(sizeof($typesSub) > 0) {
								echo '<div class="catsSlider" id="slider'.$t.'">';
								for($s=0; $s<sizeof($typesSub); $s++)
								{
									echo '<input type="checkbox" id="addpost_subcats'.$t.'-'.$s.'" name="addpost_cats[]" value="'.$typesSub[$s]->term_id.'" ';
									if(in_array($typesSub[$s]->term_id, $terms))
										echo 'checked="checked" ';
									echo '/>';
									echo '<label for="addpost_subcats'.$t.'-'.$s.'">'.$typesSub[$s]->name.'</label><div class="clear"></div>';
								}
								echo '</div>';
								echo '';
							}
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