<?php
/**
 * @package WordPress
 * @subpackage carwashtrader
 * template name: forgot password
*/

get_header(); 
include 'includes/header.php';
?>
	<!-- Forgot password -->
        
        <h1><?php echo (get_field('display_title', $post->ID) != '') ? get_field('display_title', $post->ID) : $post->post_title; ?></h1>
        
        <article>
        	<h2><?php echo $post->post_title; ?></h2>
			<?php echo apply_filters('the_content', $post->post_content);?>
            
            <div class="formBox">
            <?php echo do_shortcode('[pie_register_forgot_password]'); ?>
            </div>
            
        </article>
        
        <section>
        	<?php include 'includes/menu-jobalerts.php'; ?>
        	<?php include 'includes/menu-reviews.php'; ?>
            
            <?php include 'includes/adverts-menu.php'; ?>
        </section>
    </div>

<?php 
include 'includes/footer.php';
get_footer(); 
?> 