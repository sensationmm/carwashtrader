<?php
/**
 * @package WordPress
 * @subpackage carwashtrader
 * template name: my cwt - products
*/

get_header(); 
include 'includes/secure.php';

global $current_user, $wp_roles;
get_currentuserinfo();
$error = array();    

include 'includes/header.php';
?>
	<!-- MyCWT - Products -->
        
        <h1><?php echo (get_field('display_title', $page->ID) != '') ? get_field('display_title', $page->ID) : $page->post_title; ?></h1>
        
        <article>
        	<h2>My Products</h2>
        	<?php echo apply_filters('the_content', $page->post_content); ?>
            
            <?php if($_GET["added"] == true) { ?>
                <div class="confirmation added">
                    <h3>Product added successfully!</h3>
                    <p>You can add another <a href="/my-cwt/products/add-product/" title="Add a Product">here</a></p>
                </div>
            <?php } else if($_GET["edited"] == true) { ?>
                <div class="confirmation edited">
                    <h3>Product edited successfully!</h3>
                    <p>You can edit another by clicking the edit link below</p>
                </div>
            <?php } else { 
				echo '<div class="addListing">';
				echo '<a href="/my-cwt/products/add-product/" title="Add a Product">Add a Product</a>';
				echo '</div>';
			}
			
			if($_POST["action"] == 'deleteProduct' && is_numeric($_POST["listingID"])) {
				wp_delete_post($_POST["listingID"]);
				echo '<div class="confirmation deleted">';
                    echo '<h3>Product deleted successfully!</h3>';
                    echo '<p>Your listing will no longer appear on the site</p>';
                echo '</div>';
			}
			?>
            
            <?php
				if ( get_query_var('paged') ) { $paged = get_query_var('paged'); }
				elseif ( get_query_var('page') ) { $paged = get_query_var('page'); }
				else { $paged = 1; }

				$listings = array('post_type' => 'cwt_products',
									'orderby' => 'modified',
									'order' => 'desc',
									'posts_per_page' => 15,
									'paged' => $paged,
									'author' => $current_user->ID,
									'post_status' => 'draft|publish');
				
				remove_all_filters('posts_orderby');//prevent plugin clashing with custom ordering
				$results = query_posts($listings);
				
				if(sizeof($results) > 0) {
					for($r=0; $r<sizeof($results); $r++) {
						$listingID = $results[$r]->ID;
					
						$title = $results[$r]->post_title;
						$description = $results[$r]->post_content;
						$link = get_field('product_link', $listingID); 
						$contactdetails = get_field('product_contactdetails', $listingID); 
						$price = get_field('product_price', $listingID); 

						$paid = get_field('premium_paypalID', $listingID);
						
						//if($paid != '' && $results[$r]->post_status == 'publish') {
							echo '<div class="listingEdit">';
							echo '<a href="/my-cwt/products/edit-product/?id='.$listingID.'" title="Edit this Product">Edit this product</a></div>';
						//}
						/*if($paid == '') {
							echo '<div class="listingEdit">';
							echo '<a href="/my-cwt/products/payment/?id='.$listingID.'" title="Pay for listing">LISTING NOT PUBLISHED - PAY FOR THIS LISTING</a></div>';
						}*/
						
						echo outputProductListing($listingID,$title,$description,$contactdetails,$link,$price);
					}
					if(paginate_links() != '')
						echo '<div class="pagination">'.paginate_links().'</div>';
				} else {
					$noresults = get_page(256);
					echo '<div class="confirmation noresults">';
					echo '<h3>'.get_field('display_title', $noresults->ID).'</h3>';
					echo apply_filters('the_content', $noresults->post_content);
					echo '</div>';
				}
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