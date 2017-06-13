<?php
/**
 * @package WordPress
 * @subpackage carwashtrader
 * template name: my cwt - carwash
*/

get_header(); 
include 'includes/secure.php';

global $current_user, $wp_roles;
get_currentuserinfo();
$error = array();    

include 'includes/header.php';
?>
	<!-- MyCWT - Carwashes -->
        
        <h1><?php echo (get_field('display_title', $pageObj->ID) != '') ? get_field('display_title', $pageObj->ID) : $pageObj->post_title; ?></h1>
        
        <article>
        	<h2>My Carwashes</h2>
        	<?php echo apply_filters('the_content', $pageObj->post_content); ?>
            
            <?php if($_GET["added"] == true) { ?>
                <div class="confirmation added">
                    <h3>Car Wash added successfully!</h3>
                    <p>You can add another <a href="/my-cwt/car-washes/add-car-wash/" title="Add a Car Wash">here</a></p>
                </div>
            <?php } else if($_GET["edited"] == true) { ?>
                <div class="confirmation edited">
                    <h3>Car Wash edited successfully!</h3>
                    <p>You can edit another by clicking the edit link below</p>
                </div>
            <?php } else { 
				$businessName = get_field('user_business_name', 'user_'.$current_user->ID);
				
				if($businessName == '') {
					echo '<div class="error">';
					echo 'WARNING: Your business details have not been completed';
					echo '<br />You must <a href="/my-cwt/edit-business/">complete them</a> before you can add any businesses or jobs to your profile';
					echo '</div>';
				} else {
					echo '<div class="addListing">';
					echo '<a href="/my-cwt/car-washes/add-car-wash/" title="Add a Car Wash">Add a Car Wash</a>';
					echo '</div>';
				}
			}
			
			if($_POST["action"] == 'deleteCarWash' && is_numeric($_POST["listingID"])) {
				wp_delete_post($_POST["listingID"]);
				echo '<div class="confirmation deleted">';
                    echo '<h3>Car Wash deleted successfully!</h3>';
                    echo '<p>Your listing will no longer appear on the site</p>';
                echo '</div>';
			}
			?>
            
            <?php
				if ( get_query_var('paged') ) { $paged = get_query_var('paged'); }
				elseif ( get_query_var('page') ) { $paged = get_query_var('page'); }
				else { $paged = 1; }

				$listings = array('post_type' => 'cwt_carwashes',
									'orderby' => 'modified',
									'order' => 'desc',
									'posts_per_page' => 15,
									'paged' => $paged,
									'author' => $current_user->ID);
				
				remove_all_filters('posts_orderby');//prevent plugin clashing with custom ordering
				$results = query_posts($listings);
				
				if(sizeof($results) > 0) {
					for($r=0; $r<sizeof($results); $r++) {
						$listingID = $results[$r]->ID;
					
						$title = $results[$r]->post_title;
						$description = $results[$r]->post_content;
						$address = get_field('carwash_address', $listingID);
						$postcode = get_field('carwash_postcode', $listingID);
						$geocode = get_field('carwash_postcode', $listingID);
						$contact = get_field('carwash_contactnumber', $listingID);
						$website = get_field('carwash_website', $listingID); 
						$openinghours = get_field('carwash_openinghours', $listingID); 
						$prices = get_field('carwash_prices', $listingID); 
						
						echo '<div class="listingEdit">';
						echo '<a href="/my-cwt/car-washes/edit-car-wash/?id='.$listingID.'" title="Edit this Car Wash">Edit this carwash</a></div>';
						echo '<div class="listingSell">';

						if(get_field('sale_for_sale', $listingID) == 'yes') {
							$paid = get_field('premium_paypalID', $listingID);
							
							if($paid != '')
								echo '<a href="/my-cwt/car-washes/sell-car-wash/?id='.$listingID.'" title="Edit Sale Listings">Edit Sale Listing</a></div>';
							else
								echo '<a href="/my-cwt/car-washes/payment/?id='.$listingID.'" title="Pay for listing">SALE LISTING NOT PUBLISHED - PAY FOR THIS LISTING</a></div>';
						}
						else
							echo '<a href="/my-cwt/car-washes/sell-car-wash/?id='.$listingID.'" title="Sell this Car Wash">Sell this carwash</a></div>';
						
						echo outputCarwashListing($listingID,$title,$description,$address,$postcode,$geocode,$contact,$website,$openinghours,$prices,0,'false','true');
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