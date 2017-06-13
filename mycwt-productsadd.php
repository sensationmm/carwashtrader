<?php
/**
 * @package WordPress
 * @subpackage carwashtrader
 * template name: my cwt - add product
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

	// Do some minor form validation to make sure there is content
	if (!empty ($_POST['addpost_title'])) $title =  sanitize_text_field($_POST['addpost_title']); 
		else  $error[] = 'Please enter the name of the product';
	if (!empty ($_POST['addpost_description'])) {
		$description = $_POST['addpost_description'];
		$description = strip_tags(html_entity_decode($description), '<p><blockquote><strong><em><del><ul><ol><li>');
	}
	else $error[] = 'Please enter a brief description of the product';
	if (!empty ($_POST['addpost_price'])) $price =  $_POST['addpost_price']; 
		else  $error[] = 'Please enter the price of the product';

	if(sizeof($_POST["addpost_cats"]) == 0)
		$error[] = 'Please select the type of product';
	else
		$terms = $_POST["addpost_cats"];
	
	// Add the content of the form to $post as an array
	$post = array(
		'post_title'	=> $title,
		'post_content'	=> $description,
		'tax_input'		=> array('cwt_prodsupp_cats' => $_POST['addpost_cats']), 
		'post_status'	=> 'publish',		
		'post_type'	=> $_POST['addpost_type'],
	);
	
	$contactnumber = sanitize_text_field($_POST['addpost_contactnumber']);
	$link = sanitize_text_field($_POST['addpost_link']);
	
	if($_POST['action'] == 'new-post') {
		$formAction = 'new-post';
		$formLabel = 'Publish';

		//upload images
		if (sizeof($error) == 0) {
			
			$newProductID = wp_insert_post($post);
			 
			if (!empty($_FILES['addpost_attachment']['name'])) {
				foreach ($_FILES as $file_id => $array) {
					if($file_id=="addpost_attachment") {
						$featuredImage = true;
						$attachment_id = insert_attachment($file_id,$newProductID,$featuredImage);
					} else {
						$featuredImage = false;
						$attachment_id = insert_attachment($file_id,$newProductID,$featuredImage);
					}
				}
			}
		
			__update_post_meta($newProductID, 'product_price', $price);
			__update_post_meta($newProductID, 'product_contactdetails', $contactnumber);
			__update_post_meta($newProductID, 'product_link', $link);
	
			do_action('wp_insert_post', 'wp_insert_post');
			unset($_POST);
			//wp_redirect('/my-cwt/products/payment/');
			wp_redirect('/my-cwt/products/?added=true');
		}
	} else if($_POST['action'] == 'edit-post') {

		$formAction = 'edit-post';
		$formLabel = 'Edit';

		$editPostID = $_POST["postID"];
		$product = get_post($editPostID);
			
		//array of all types checked
		$terms = wp_get_post_terms( $product->ID, 'cwt_prodsupp_cats', array("fields" => "ids"));
		
		$featured = get_field('_thumbnail_id', $product->ID );

		if (sizeof($error) == 0) {
			$productID = $_POST["postID"];
			$post = array(
				'ID' => $productID,
				'post_title'	=> $title,
				'post_content'	=> $description,
				'tax_input'		=> array('cwt_prodsupp_cats' => $_POST['addpost_cats']),
				'post_status' => 'publish'
			);
			
			//remove images if checked
			if($_POST["delete0"] == 'yes') {
				$post_thumbnail_id = get_post_thumbnail_id( $_POST["deleteID0"] ); //get attachment id
				delete_post_thumbnail($_POST["deleteID0"]);  //remove featured image relationship
				wp_delete_attachment( $post_thumbnail_id, 'true' ); //remove attachment itself
				
				//set next attachment as featured image if one exists
				$attachments = get_children(array(
					'post_parent' => $productID, 
					'post_status' => 'inherit', 
					'post_type' => 'attachment', 
					'post_mime_type' => 'image', 
					'order' => 'ASC', 
					'orderby' => 'menu_order'
				));
				$attachmentKeys = array_keys($attachments);
				if ($attachments) {
					set_post_thumbnail($productID, $attachments[$attachmentKeys[0]]->ID);
				}
			}
			
			//add new images if set
			foreach ($_FILES as $file_id => $array) {
				if($file_id=="addpost_attachment") {
					$featuredImage = true;
					$attachment_id = insert_attachment($file_id,$productID,$featuredImage);
				} else {
					$featuredImage = false;
					$attachment_id = insert_attachment($file_id,$productID,$featuredImage);
				}
			}
			
			//__update_post_meta($productID, 'product_name', $title);
			//__update_post_meta($productID, 'product_description', $description);
			__update_post_meta($productID, 'product_price', $price);
			__update_post_meta($productID, 'product_contactdetails', $contactnumber);
			__update_post_meta($productID, 'product_link', $link);
			
			wp_update_post($post);
			do_action('wp_update_post', 'wp_update_post');
			unset($_POST);
			wp_redirect('/my-cwt/products/?edited=true');
		}
	}
}
else if(isset($_GET["id"])) {
	
	$editPostID = $_GET["id"];
	if(is_numeric($editPostID)) {
		$product = get_post($editPostID);
		if($product->post_author != $current_user->ID)
			wp_redirect('/my-cwt/products/');
		else {
			$title = $product->post_title;
			$description = html_entity_decode($product->post_content);
			$price = html_entity_decode(get_field('product_price', $product->ID));
			$contactnumber = get_field('product_contactdetails', $product->ID);
			$link = get_field('product_link', $product->ID);
			
			//array of all types checked
			$terms = wp_get_post_terms( $product->ID, 'cwt_prodsupp_cats', array("fields" => "ids"));
			
			$featured = get_field('_thumbnail_id', $product->ID );
		
			$formAction = 'edit-post';
			$formLabel = 'Edit';
		}
	} else { wp_redirect('/my-cwt/products/'); }
}
else {
	$terms = array();
	$formAction = 'new-post';
	$formLabel = 'Publish';
}


include 'includes/header.php';
?>
	<!-- MyCWT - Products Add -->
        
        <h1><?php echo (get_field('display_title', $pageObj->ID) != '') ? get_field('display_title', $pageObj->ID) : $pageObj->post_title; ?></h1>
        
        <article>
        	<h2><?php echo $pageObj->post_title; ?></h2>
        	<?php echo apply_filters('the_content', $pageObj->post_content); ?>
            
            <?php if($formAction == 'edit-post') { ?>
            <div class="listingEdit">
            	<a href="javascript:deleteListing(<?php echo $editPostID; ?>);" title="Delete Product" onclick="return confirm('Are you sure? This action cannot be undone');">Delete this Product</a>
                
                <form id="deleteListing" method="post" action="/my-cwt/products/">
                <input type="hidden" name="listingID" id="listingID" value="" />
				<input type="hidden" name="action" value="deleteProduct" />
                </form>
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
                    <label for="addpost_title">Name *</label>
                    <input type="text" id="addpost_title" size="20" name="addpost_title" value="<?php echo $title; ?>" />
                    
                    <label for="addpost_description">Description *
                    	<span>(NB. Please use the toolbar to format your text as you would like it to appear on your listing. Links can be added using the icon third from the right)</span></label>
                   	<?php wp_editor( $description, 'addpost_description', $editorSettings); ?> 
                    
                    <label for="addpost_attachment">Image</label>
                    <?php
						if($featured) {
							echo '<div class="thumb">'.get_the_post_thumbnail($product->ID, array(70,70));
							echo '<input type="checkbox" name="delete0" id="delete0" value="yes" />';
							echo '<input type="hidden" name="deleteID0" id="deleteID0" value="'.$product->ID.'" />';
							echo '<label for="delete0">Delete this image</label></div>';
						}
						else echo '<input type="file" id="addpost_attachment" name="addpost_attachment">';
					?>
                    <div class="clear"></div>
                    
                    
                    <label for="addpost_price">Price (&pound;) *</label>
                    <input type="text" id="addpost_price" size="20" name="addpost_price" value="<?php echo $price; ?>" />
                    
                    <label for="addpost_contactnumber">Contact Details</label>
                    <input type="text" id="addpost_contactnumber" size="20" name="addpost_contactnumber" value="<?php echo $contactnumber; ?>" />
                    
                    <label for="addpost_link">Link</label>
                    <input type="text" id="addpost_link" size="20" name="addpost_link" value="<?php echo $link; ?>" />
                    
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
                    <input type="hidden" name="addpost_type" id="addpost_type" value="cwt_products" />
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