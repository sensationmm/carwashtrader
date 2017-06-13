<?php
/**
 * @package WordPress
 * @subpackage carwashtrader
 * template name: register
*/

get_header(); 
include 'includes/header.php';
?>
	<!-- Register -->
        
        <?php 
            $bgImage = get_field('intro_image', $pageObj->ID); 
            $displayTitle = get_field('display_title', $pageObj->ID);
        ?>

        <h1<?php echo ($bgImage != '') ? ' class="imageHeader" style="background-image:url('.$bgImage.');"' : ''; ?>>
        <?php echo ($displayTitle != '') ? $displayTitle : $pageObj->post_title; ?></h1>
        
        <article>
        	<h2><?php echo $post->post_title; ?></h2>
			<?php echo apply_filters('the_content', $post->post_content);?>
            
            <div class="formBox">
            <?php
				$types = get_pages('parent='.$post->ID.'&post_type=page&sort_column=menu_order&sort_order=ASC');
				
				for($i=0; $i<sizeof($types); $i++)
				{
					echo '<div class="accountType account'.$types[$i]->post_name.'">';
					echo '<h3>'.$types[$i]->post_title.'</h3>';
					echo '<p>'.$types[$i]->post_content.'</p>';
					echo '</div>';
					
					if(($i+1)%2 == 0)
						echo '<div class="clear"></div>';
				}
			?>
            <p>For a full price list please <a href="/prices/" title="View price list">click here</a>.</p>
            </div>
            
            <div class="formBox">
            <?php echo do_shortcode('[pie_register_form]'); ?>
            </div>
            
        </article>
        
        <section>
        	<?php include 'includes/menu-jobalerts.php'; ?>
        	<?php include 'includes/menu-reviews.php'; ?>
            
            <?php include 'includes/adverts-menu.php'; ?>
        </section>
    </div>

    <script type="text/javascript">
        var allRadios = document.getElementsByName('radio_8[]');
        var x = 0;
        for(x = 0; x < allRadios.length; x++){
            allRadios[x].checked = false;
        }
    </script>

<?php 
include 'includes/footer.php';
get_footer(); 
?> 