<?php
/**
 * @package WordPress
 * @subpackage carwashtrader
 * template name: my cwt - edit business
*/

get_header(); 
include 'includes/secure.php';
/* Get user info. */
global $current_user, $wp_roles;
get_currentuserinfo();

/* Load the registration file. */
require_once( ABSPATH . WPINC . '/registration.php' );
$error = array();    
/* If profile was saved, update profile. */
if ( 'POST' == $_SERVER['REQUEST_METHOD'] && !empty( $_POST['action'] ) && $_POST['action'] == 'update-user' ) {

    /* Update user information. */
    if ( !empty( $_POST['user_business_name'] ) )
        update_user_meta( $current_user->ID, 'user_business_name', esc_attr( $_POST['user_business_name'] ) );
	else
		$error[] = __('You must provide a business name', 'profile');
    if ( !empty( $_POST['user_telephone'] ) )
        update_user_meta($current_user->ID, 'user_telephone', esc_attr( $_POST['user_telephone'] ) );
	else
		$error[] = __('You must provide a contact number', 'profile');
    if ( !empty( $_POST['user_address'] ) )
        update_user_meta( $current_user->ID, 'user_address', esc_attr( $_POST['user_address'] ) );
	else
		$error[] = __('You must provide a business address', 'profile');
    if ( !empty( $_POST['user_postcode'] ) )
        update_user_meta( $current_user->ID, 'user_postcode', esc_attr( $_POST['user_postcode'] ) );
	else
		$error[] = __('You must provide a postcode', 'profile');
	
    if ( sizeof($error) == 0 ) {
		
   		//$error[] = __('Your business details were edited successfully', 'profile');
        /*/action hook for plugins and extra fields saving
        do_action('edit_user_profile_update', $current_user->ID);
        wp_redirect( get_permalink() );
        exit;*/

        wp_redirect('/my-cwt/?edited=true');
    }
	
	
}

include 'includes/header.php';
?>
	<!-- My CWT - Edit Profile -->
        
        <h1><?php echo (get_field('display_title', $post->ID) != '') ? get_field('display_title', $post->ID) : $post->post_title; ?></h1>
        
        <article>
        	<h2><?php echo $post->post_title; ?></h2>
        	<?php echo apply_filters('the_content', $post->post_content); ?>
        	<div class="formBox">
				<?php if ( have_posts() ) : while ( have_posts() ) : the_post(); ?>
                <div id="post-<?php the_ID(); ?>">
                    <div class="entry-content entry">
                        <?php the_content(); ?>
                        <?php if ( !is_user_logged_in() ) : ?>
                                <p class="warning">
                                    <?php _e('You must be logged in to edit your profile.', 'profile'); ?>
                                </p><!-- .warning -->
                        <?php else : ?>
                            <?php if ( count($error) > 0 ) echo '<p class="error">' . implode("<br />", $error) . '</p>'; ?>
                            <form method="post" id="adduser" action="<?php the_permalink(); ?>">
                                <p class="form-username">
                                    <label for="user_business_name"><?php _e('Business Name', 'profile'); ?></label>
                                    <input class="text-input" name="user_business_name" type="text" id="user_business_name" value="<?php the_author_meta( 'user_business_name', $current_user->ID ); ?>" />
                                </p>
                                <p class="form-username">
                                    <label for="user_telephone"><?php _e('Telephone'); ?></label>
                                    <input class="text-input" name="user_telephone" type="text" id="user_telephone" value="<?php the_author_meta( 'user_telephone', $current_user->ID ); ?>" />
                                </p>
                                <p class="form-email">
                                    <label for="user_address"><?php _e('Address', 'profile'); ?></label>
                                    <input class="text-input" name="user_address" type="text" id="user_address" value="<?php the_author_meta( 'user_address', $current_user->ID ); ?>" />
                                </p>
                                <p class="form-password">
                                    <label for="user_postcode"><?php _e('Postcode', 'profile'); ?> </label>
                                    <input class="text-input" name="user_postcode" type="text" id="user_postcode" value="<?php the_author_meta( 'user_postcode', $current_user->ID ); ?>" />
                    <div class="inputNote">NB. This must be a correctly formatted UK postcode, including the space, for the map function to work correctly</div>
                                </p>
            
                                <?php 
                                    //action hook for plugin and extra fields
                                    //do_action('edit_user_profile',$current_user); 
                                ?>
                                <p class="form-submit">
                                    <?php echo $referer; ?>
                                    <input name="updateuser" type="submit" id="updateuser" class="submit button" value="<?php _e('Update', 'profile'); ?>" />
                                    <?php wp_nonce_field( 'update-user' ) ?>
                                    <input name="action" type="hidden" id="action" value="update-user" />
                                </p><!-- .form-submit -->
                            </form><!-- #adduser -->
                        <?php endif; ?>
                    </div><!-- .entry-content -->
                </div><!-- .hentry .post -->
                <?php endwhile; ?>
            <?php else: ?>
                <p class="no-data">
                    <?php _e('Sorry, no page matched your criteria.', 'profile'); ?>
                </p><!-- .no-data -->
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