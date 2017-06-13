<?php
/**
 * @package WordPress
 * @subpackage carwashtrader
 * template name: my cwt - add job
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
		else  $error[] = 'Please enter the position of the job';
	
	if (!empty ($_POST['addpost_description'])) {
		$description = $_POST['addpost_description'];
		$description = strip_tags(html_entity_decode($description), '<p><blockquote><strong><em><del><ul><ol><li>');
	}
	else $error[] = 'Please enter a brief description of the job';
	
	if(sizeof($_POST["addpost_cats"]) == 0)
		$error[] = 'Please select the job type';
	else
		$terms = $_POST["addpost_cats"];

	if (!empty ($_POST['addpost_address'])) $address =  sanitize_text_field($_POST['addpost_address']); 
		else  $error[] = 'Please enter the address for the job';

	if (!empty ($_POST['addpost_apply'])) $apply = sanitize_text_field($_POST['addpost_apply']);
		else  $error[] = 'Please enter some details on how to apply';

	
	if(strpos($_POST['addpost_postcode'], ' ') == '') {
		$postcode =  $_POST['addpost_postcode'];
		$error[] = 'Please enter a valid postcode, including the space';
	}
	else if (!empty ($_POST['addpost_postcode'])) $postcode =  sanitize_text_field(strip_tags($_POST['addpost_postcode'], '<p><b><i><u><ul><ol><li>')); 
		else  $error[] = 'Please enter the postcode for the job';
	if (!empty ($_POST['addpost_salary'])) $salary =  $_POST['addpost_salary']; 
		else  $error[] = 'Please enter the salary for the job - use TBC if not yet set';

	// Add the content of the form to $post as an array
	$post = array(
		'post_title'	=> $title,
		'post_content'	=> $description,
		'tax_input'		=> array('cwt_job_types' => $_POST['addpost_cats']), 
		'post_status'	=> 'publish',		
		'post_type'	=> $_POST['addpost_type'],
        'post_status' => 'draft'
	);
	
	$hours = sanitize_text_field($_POST['addpost_hours']);
	
	if($_POST['action'] == 'new-post') {

		$formAction = 'new-post';
		$formLabel = 'Post';

		if (sizeof($error) == 0) {
			
			$newJobID = wp_insert_post($post);
			
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
		
			//__update_post_meta($newJobID, 'job_name', $title);
			//__update_post_meta($newJobID, 'job_description', $description);
			__update_post_meta($newJobID, 'job_address', $address);
			__update_post_meta($newJobID, 'job_postcode', $postcode);
			__update_post_meta($newJobID, 'job_geocode', $geocode);
			__update_post_meta($newJobID, 'job_hours', $hours);
			__update_post_meta($newJobID, 'job_salary', $salary);
			__update_post_meta($newJobID, 'job_apply', $apply);
	
			do_action('wp_insert_post', 'wp_insert_post');
			unset($_POST);

			//redirect to payment
			wp_redirect('/my-cwt/jobs/payment/?id='.$newJobID);
		}
	} else if($_POST['action'] == 'edit-post') {
		
		$editPostID = $_POST["postID"];
		$formAction = 'edit-post';
		$formLabel = 'Edit';
		$terms = wp_get_post_terms( $editPostID, 'cwt_job_types', array("fields" => "ids"));

		if(sizeof($error) == 0) {
			$carwashID = $_POST["postID"];
			$post = array(
				'ID' => $carwashID,
				'post_title'	=> $title,
				'post_content'	=> $description,
				'tax_input'		=> array('cwt_job_types' => $_POST['addpost_cats']),
	        	'post_status' => 'publish'
			);
			
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
			
			//__update_post_meta($carwashID, 'job_name', $title);
			//__update_post_meta($carwashID, 'job_description', $description);
			__update_post_meta($carwashID, 'job_address', $address);
			__update_post_meta($carwashID, 'job_postcode', $postcode);
			__update_post_meta($carwashID, 'job_geocode', $geocode);
			__update_post_meta($carwashID, 'job_hours', $hours);
			__update_post_meta($carwashID, 'job_salary', $salary);
			__update_post_meta($carwashID, 'job_apply', $apply);
			
			wp_update_post($post);
			do_action('wp_update_post', 'wp_update_post');
			unset($_POST);
			wp_redirect('/my-cwt/jobs/?edited=true');
		}
	}
}
else if(isset($_GET["id"])) {
	
	$editPostID = $_GET["id"];
	if(is_numeric($editPostID)) {
		$job = get_post($editPostID);
		if($job->post_author != $current_user->ID)
			wp_redirect('/my-cwt/jobs/');
		else {
			$title = $job->post_title;
			$description = html_entity_decode($job->post_content);
			$address = get_field('job_address', $job->ID);
			$postcode = get_field('job_postcode', $job->ID);

			$hours = html_entity_decode(get_field('job_hours', $job->ID));
			$salary = html_entity_decode(get_field('job_salary', $job->ID));
			$apply = html_entity_decode(get_field('job_apply', $job->ID));
			
			//array of all types checked
			$terms = wp_get_post_terms( $job->ID, 'cwt_job_types', array("fields" => "ids"));
		
			$formAction = 'edit-post';
			$formLabel = 'Edit';
		}
	} else { wp_redirect('/my-cwt/jobs/'); }
}
else {
	$terms = array();
	$formAction = 'new-post';
	$formLabel = 'Post';
}


include 'includes/header.php';
?>
	<!-- MyCWT - Jobs Add -->
        
        <h1><?php echo (get_field('display_title', $pageObj->ID) != '') ? get_field('display_title', $pageObj->ID) : $pageObj->post_title; ?></h1>
        
        <article>
        	<h2><?php echo $pageObj->post_title; ?></h2>
        	<?php echo apply_filters('the_content', $pageObj->post_content); ?>
            
            <?php if($formAction == 'edit-post') { ?>
            <div class="listingEdit">
            	<a href="javascript:deleteListing(<?php echo $editPostID; ?>);" title="Delete Job" onclick="return confirm('Are you sure? This action cannot be undone');">Delete this Job</a>
                
                <form id="deleteListing" method="post" action="/my-cwt/jobs/">
                <input type="hidden" name="listingID" id="listingID" value="" />
				<input type="hidden" name="action" value="deleteJob" />
                </form>
            </div>
            <?php } ?>
            
            <div class="formBox">
                
                <?php if ( !is_user_logged_in() ) : ?>
                    <p class="warning">
                        <?php _e('You must be logged in to edit this job.', 'profile'); ?>
                    </p><!-- .warning -->
				<?php else : ?>
                    <?php if ( sizeof($error) > 0 ) echo '<p class="error">' . implode("<br />", $error) . '</p>'; ?>
                    
                    <form method="post" id="adduser" action="<?php //the_permalink(); ?>" enctype="multipart/form-data">
                    <label for="addpost_title">Position *</label>
                    <input type="text" id="addpost_title" size="20" name="addpost_title" value="<?php echo $title; ?>" />
                    
                    <label for="addpost_description">Description *
                    	<span>(NB. Please use the toolbar to format your text as you would like it to appear on your listing. Links can be added using the icon third from the right)</span></label>
                   	<?php wp_editor( $description, 'addpost_description', $editorSettings); ?> 
                    
                    <div class="clear"></div>
                    <label>Type *</label>
                    <div class="options">
					<?php 
						$types = get_terms('cwt_job_types', array('hide_empty'=>false)); 
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
                    
                    <label for="addpost_address">Address *</label>
                    <input type="text" id="addpost_address" size="20" name="addpost_address" value="<?php echo $address; ?>" />
                    
                    <label for="addpost_postcode">Postcode *</label>
                    <input type="text" id="addpost_postcode" size="20" name="addpost_postcode" value="<?php echo $postcode; ?>" />
                    <div class="inputNote">NB. This must be a correctly formatted UK postcode, including the space, for the map function to work correctly</div>
                    
                    <label for="addpost_hours">Hours</label>
                    <input type="text" id="addpost_hours" size="20" name="addpost_hours" value="<?php echo $hours; ?>" />
                    
                    <label for="addpost_salary">Salary *</label>
                    <input type="text" id="addpost_salary" size="20" name="addpost_salary" value="<?php echo $salary; ?>" />
                    
                    <label for="addpost_apply">How to Apply *
                    	<span>(NB. Please use the toolbar to format your text as you would like it to appear on your listing. Links can be added using the icon third from the right)</span></label>
                   	<?php wp_editor( $apply, 'addpost_apply', $editorSettings); ?> 
                        
                    <input type="submit" value="<?php echo $formLabel; ?>" id="submit" name="submit" /></p>
                    <?php if(isset($editPostID)) echo '<input type="hidden" name="postID" value="'.$editPostID.'" />'; ?>
                    <input type="hidden" name="addpost_type" id="addpost_type" value="cwt_jobs" />
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