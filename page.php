<?php
/**
 * @package WordPress
 * @subpackage carwashtrader
*/

get_header(); 
include 'includes/header.php';
?>
	<!-- Page -->
        
        <?php
			$headerImage = '';
			$introImage = get_field('intro_image', $pageObj->ID);
			if($introImage != '')
				$headerImage = ' style="background-image:url('.$introImage.');" class="imageHeader"';
		?>
        
        <h1<?php echo $headerImage; ?>><?php echo (get_field('display_title', $pageObj->ID) != '') ? get_field('display_title', $pageObj->ID) : $pageObj->post_title; ?></h1>
        
        <article>
        	<h2><?php echo $pageObj->post_title; ?></h2>
            <div class="purpleBox">
            	<?php echo apply_filters('the_content', $pageObj->post_content); ?>
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