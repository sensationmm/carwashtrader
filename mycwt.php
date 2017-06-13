<?php
/**
 * @package WordPress
 * @subpackage carwashtrader
 * template name: my cwt
*/

get_header(); 
include 'includes/secure.php';
include 'includes/header.php';
?>
	<!-- My CWT -->
        
        <h1><?php echo (get_field('display_title', $post->ID) != '') ? get_field('display_title', $post->ID) : $post->post_title; ?></h1>
        
        <article>
        <?php 
			echo apply_filters('the_content', $post->post_content); 
			//if($current_user->ID == 17) {
				if($current_user->roles[0] == 'trader')
					include 'mycwt-trader.php';
				else if($current_user->roles[0] == 'estateagent')
					include 'mycwt-estateagent.php';
				else if($current_user->roles[0] == 'supplier')
					include 'mycwt-supplier.php';
				else if($current_user->roles[0] == 'guest')
					include 'mycwt-guest.php';
			//}
		?>
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