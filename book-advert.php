<?php
/**
 * @package WordPress
 * @subpackage carwashtrader
 * template name: book advert
*/

get_header(); 

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
	if (!empty ($_POST['addpost_title'])) $title =  htmlentities($_POST['addpost_title']); 
		else  $error[] = 'Please enter the name of the advert - this will show in the image alt text.';

	if (empty($_FILES['addpost_attachment']['name']))
		$error[] = 'Please upload the image for the advert';
	
	if (!empty ($_POST['addpost_link'])) $link =  htmlentities($_POST['addpost_link']); 
		else  $error[] = 'Please enter the link for the advert - either your website or an offer page.';
	
	if (!empty ($_POST['addpost_contactname'])) $contactname =  htmlentities($_POST['addpost_contactname']); 
		else  $error[] = 'Please enter your contact name.';
	
	if (!empty ($_POST['addpost_contactemail'])) $contactemail =  htmlentities($_POST['addpost_contactemail']); 
		else  $error[] = 'Please enter your contact email address.';
	
	if (!empty ($_POST['addpost_contacttelephone'])) $contacttelephone =  htmlentities($_POST['addpost_contacttelephone']); 
		else  $error[] = 'Please enter your contact telephone number.';

	if(!is_numeric($_POST["addpost_types"]))
		$error[] = 'Please select the placement of your advert';
	else
		$type = $_POST["addpost_types"];

	if(sizeof($_POST["addpost_cats"]) == 0)
		$error[] = 'Please select what page(s) you would like your advert to show on';
	else
		$terms = $_POST["addpost_cats"];
	
	$contactname = htmlentities($_POST['addpost_contactname']);
	$contactemail = htmlentities($_POST['addpost_contactemail']);
	$contacttelephone = htmlentities($_POST['addpost_contacttelephone']);
	$contactaddress = htmlentities($_POST['addpost_contactaddress']);
	$description = htmlentities($_POST['addpost_description']);
	$link = htmlentities($_POST['addpost_link']);
	
	// Add the content of the form to $post as an array
	$post = array(
		'post_title'	=> $title,
		'post_content'	=> $description,
		'tax_input'		=> array('cwt_advert_locations' => $_POST['addpost_cats']), 
		'post_type'	=> $_POST['addpost_type'],
		'post_status' => 'draft'
	);

	if(is_user_logged_in()) {
		$post["post_author"] = $current_user->ID;
	} else {
		$post["post_author"] = 1;
	}
	print_r($_FILES["addpost_attachment"]);
	
	if($_POST['action'] == 'new-post') {
		//upload images
		if (sizeof($error) == 0) {
			
			$advertID = wp_insert_post($post);
			 
			if (!empty($_FILES['addpost_attachment']['name'])) {
				foreach ($_FILES as $file_id => $array) {
					if($file_id=="addpost_attachment") {
						$featuredImage = true;
						$attachment_id = insert_attachment($file_id,$advertID,$featuredImage);
					}
				}
			}
		
			__update_post_meta($advertID, 'advert_link', $link);
			__update_post_meta($advertID, 'advert_contact_name', $contactname);
			__update_post_meta($advertID, 'advert_contact_email', $contactemail);
			__update_post_meta($advertID, 'advert_contact_telephone', $contacttelephone);
			__update_post_meta($advertID, 'advert_contact_address', $contactaddress);
	
			do_action('wp_insert_post', 'wp_insert_post');

			wp_set_object_terms( $advertID, array(intval($_POST['addpost_types'])), 'cwt_advert_types' );
			unset($_POST);
			wp_redirect('/book-advert/confirmation/');
		} else {
			$formAction = 'new-post';
			$formLabel = 'Send Request';
		}

	}
}
else {
	$terms = array();
	$formAction = 'new-post';
	$formLabel = 'Send Request';

	if(is_user_logged_in) {
		$contactname = $current_user->user_firstname.' '.$current_user->user_lastname;
		$contactemail = $current_user->user_email;
	}
}


include 'includes/header.php';
?>
	<!-- Book Advert -->
        
        <?php
			$headerImage = '';
			$introImage = get_field('intro_image', $pageObj->ID);
			if($introImage != '')
				$headerImage = ' style="background-image:url('.$introImage.');" class="imageHeader"';
		?>
        
        <h1<?php echo $headerImage; ?>><?php echo (get_field('display_title', $pageObj->ID) != '') ? get_field('display_title', $pageObj->ID) : $pageObj->post_title; ?></h1>
        
        <article>
        	<h2><?php echo $pageObj->post_title; ?></h2>
        	<div class="purpleBox"><?php echo apply_filters('the_content', $pageObj->post_content); ?></div>

            <div class="formBox">
                
                <?php if ( sizeof($error) > 0 ) echo '<p class="error">' . implode("<br />", $error) . '</p>'; ?>
                    
                    <form method="post" id="adduser" action="<?php //the_permalink(); ?>" enctype="multipart/form-data">
                    <label for="addpost_title">Name *</label>
                    <input type="text" id="addpost_title" size="20" name="addpost_title" value="<?php echo $title; ?>" />
                    
                    <label for="addpost_description">Message<span>(NB. Tell us anything you think is relevant about your advert)</span></label>
                   	<?php wp_editor( $description, 'addpost_description', $editorSettings); ?> 
                    
                    <label for="addpost_attachment">Image *</label>
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
                    
                    <label for="addpost_link">Link *</label>
                    <input type="text" id="addpost_link" size="20" name="addpost_link" value="<?php echo $link; ?>" />
                    
                    <label for="addpost_contactname">Your Name *</label>
                    <input type="text" id="addpost_contactname" size="20" name="addpost_contactname" value="<?php echo $contactname; ?>" />
                    
                    <label for="addpost_contactemail">Email *</label>
                    <input type="text" id="addpost_contactemail" size="20" name="addpost_contactemail" value="<?php echo $contactemail; ?>" />
                    
                    <label for="addpost_contacttelephone">Telephone *</label>
                    <input type="text" id="addpost_contacttelephone" size="20" name="addpost_contacttelephone" value="<?php echo $contacttelephone; ?>" />
                    
                    <label for="addpost_contactaddress">Address</label>
                    <input type="text" id="addpost_contactaddress" size="20" name="addpost_contactaddress" value="<?php echo $contactaddress; ?>" />
                    
                    <div class="clear"></div>

                    <label>Placement *</label>
                    <div class="options">
					<?php 
						$types = get_terms('cwt_advert_types', array('hide_empty'=>false,'parent'=>0)); 
						for($t=0; $t<sizeof($types); $t++)
						{
							echo '<input value="'.$types[$t]->term_id.'" id="addpost_types'.$t.'" name="addpost_types" type="radio" ';
							if($types[$t]->term_id == $type)
								echo 'checked="checked" ';
							echo '/>';
							echo '<label class="inline" for="addpost_types'.$t.'">'.$types[$t]->name.'</label>';
						}
					?>
                    </div>

                    <div class="clear"></div>

                    <label>Show on Pages *</label>
                    <div class="options">
					<?php 
						$locations = get_terms('cwt_advert_locations', array('hide_empty'=>false,'parent'=>0)); 
						for($l=0; $l<sizeof($locations); $l++)
						{
							echo '<input type="checkbox" id="addpost_cats'.$l.'" name="addpost_cats[]" value="'.$locations[$l]->term_id.'" ';
							if(in_array($locations[$l]->term_id, $terms))
								echo 'checked="checked" ';
							echo '/>';
							echo '<label for="addpost_cats'.$l.'">'.$locations[$l]->name.'</label>';

							echo '<div class="clear"></div>';
						}
					?>
                    </div>
                        
                    <input type="submit" value="<?php echo $formLabel; ?>" id="submit" name="submit" /></p>
                    <input type="hidden" name="addpost_type" id="addpost_type" value="cwt_adverts" />
                    <input type="hidden" name="action" value="<?php echo $formAction; ?>" />
                    <?php wp_nonce_field( 'new-post' ); ?>
                    </form>
            
            </div>
        </article>
        
        <section>
        	<?php include 'includes/adverts-menu.php'; ?>
        </section>
    </div>

<?php 
include 'includes/footer.php';
get_footer(); 
?> 