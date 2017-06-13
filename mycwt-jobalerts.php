<?php
/**
 * @package WordPress
 * @subpackage carwashtrader
 * template name: my cwt - job alerts
*/

get_header(); 
include 'includes/secure.php';

global $current_user, $wp_roles;
get_currentuserinfo();
$error = array();   
$terms = array(); 
	$formLabel = 'Update';
                    
if ( 'POST' == $_SERVER['REQUEST_METHOD'] && !empty( $_POST['action'] )) {

	$jobs = $_POST['jobs'];

	wp_set_object_terms( $current_user->ID, $jobs, 'cwt_job_types', false);
	clean_object_term_cache( $current_user->ID, 'cwt_job_types' );

	unset($_POST);
	wp_redirect('/my-cwt/?preferences=true');
}


include 'includes/header.php';
?>
	<!-- MyCWT - Job Alerts -->
        
        <h1><?php echo (get_field('display_title', $pageObj->ID) != '') ? get_field('display_title', $pageObj->ID) : $pageObj->post_title; ?></h1>
        
        <article>
        	<h2><?php echo $pageObj->post_title; ?></h2>

        	<?php echo apply_filters('the_content', $pageObj->post_content); ?>
            
            <div class="formBox blank">
            <form method="post" id="adduser" action="<?php //the_permalink(); ?>" enctype="multipart/form-data">
                
                <?php if ( sizeof($error) > 0 ) echo '<p class="error">' . implode("<br />", $error) . '</p>'; ?>

                <div class="formBoxSplit">
	                <label>Categories</label>
	                <div class="options">
					<?php 
						$types = get_terms('cwt_job_types', array('hide_empty'=>false,'parent'=>0)); 
						for($t=0; $t<sizeof($types); $t++)
						{
							echo '<input type="checkbox" id="pref'.$types[$t]->term_id.'" name="jobs[]" value="'.$types[$t]->slug.'" ';
							if(is_object_in_term( $current_user->ID, 'cwt_job_types', $types[$t]->term_id ))
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