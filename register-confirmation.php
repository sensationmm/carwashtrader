<?php
/**
 * @package WordPress
 * @subpackage carwashtrader
 * template name: register confirmation
*/

//Pie register plugin overwrites email verification steps so resetting here
if(isset($_GET["user_id"])) {
    update_user_meta( $_GET["user_id"], 'active', 0);
    update_user_meta( $_GET["user_id"], 'hash', $_GET["hash"] );
}

get_header(); 
include 'includes/header.php';
?>
	<!-- Register Confirmation -->
        
        <?php 
            $bgImage = get_field('intro_image', $pageObj->ID); 
            $displayTitle = get_field('display_title', $pageObj->ID);
        ?>

        <h1<?php echo ($bgImage != '') ? ' class="imageHeader" style="background-image:url('.$bgImage.');"' : ''; ?>>
        <?php echo ($displayTitle != '') ? $displayTitle : $pageObj->post_title; ?></h1>
        
        <article>
        	<h2><?php echo $post->post_title; ?></h2>
			<div class="confirmation register"><?php echo apply_filters('the_content', $post->post_content);?></div>
        </article>
        
        <section>
        	<?php include 'includes/adverts-menu.php'; ?>
        </section>
    </div>

<?php 
include 'includes/footer.php';
get_footer(); 
?> 