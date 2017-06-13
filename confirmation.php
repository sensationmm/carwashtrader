<?php
/**
 * @package WordPress
 * @subpackage carwashtrader
 * template name: confirmation
*/

get_header(); 
include 'includes/header.php';
?>
	<!-- Confirmation -->
        
        <h1><?php echo (get_field('display_title', $post->ID) != '') ? get_field('display_title', $post->ID) : $post->post_title; ?></h1>
        
        <article>
        	<h2><?php echo $post->post_title; ?></h2>
			<div class="confirmation"><?php echo apply_filters('the_content', $post->post_content);?></div>
        </article>
        
        <section>
        	<?php include 'includes/adverts-menu.php'; ?>
        </section>
    </div>

<?php 
include 'includes/footer.php';
get_footer(); 
?> 