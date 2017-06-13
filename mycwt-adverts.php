<?php
/**
 * @package WordPress
 * @subpackage carwashtrader
 * template name: my cwt - adverts
*/

get_header(); 
include 'includes/secure.php';

global $current_user, $wp_roles;
get_currentuserinfo();
$error = array();    

include 'includes/header.php';
?>
	<!-- MyCWT - Adverts -->
        
        <h1><?php echo (get_field('display_title', $pageObj->ID) != '') ? get_field('display_title', $pageObj->ID) : $pageObj->post_title; ?></h1>
        
        <article>
        	<h2>My Adverts</h2>
        	<?php echo apply_filters('the_content', $pageObj->post_content); ?>

        	<div class="addListing">
			<a href="/book-advert/" title="Book an Advert">Book an Advert</a>
			</div>
            
            <?php
            	$listings = array('post_type' => 'cwt_adverts',
									'orderby' => 'modified',
									'order' => 'desc',
									'author' => $current_user->ID);

            	$urls = array('Car Wash Listings' => '/',
            				  'Product Listings' => '/products/',
            				  'Car Wash For Sale Listings' => '/carwash-for-sale/',
            				  'Supplier Listings' => '/suppliers/',
            				  'Job Listings' => '/jobs/');

            	remove_all_filters('posts_orderby');//prevent plugin clashing with custom ordering
				$adverts = query_posts($listings);
				
				if(sizeof($adverts) > 0) {
					for($r=0; $r<sizeof($adverts); $r++) {
						$listingID = $adverts[$r]->ID;

						$terms = wp_get_post_terms( $listingID, 'cwt_advert_types', array("fields" => "names"));
						$locs = wp_get_post_terms( $listingID, 'cwt_advert_locations', array("fields" => "names"));

						echo '<div class="purpleBox advert">';

						echo '<div class="advertThumb">';
						echo '<a href="'.$urls[$locs[0]].'">'.get_the_post_thumbnail($adverts[$r]->ID, array(100,100)).'</a>';
						echo '</div>';
						echo '<h3><a href="'.$urls[$locs[0]].'">'.$adverts[$r]->post_title.'</a></h3>';
						$expiry = get_field('_expiration-date', $listingID);
						echo '<ul>';
						if($expiry != '')
							echo '<li><b>Expiry</b>: '.date('jS F Y', $expiry).'</li>';

						echo '<li><b>Type</b>: '.$terms[0].'</li>';

						echo '<li><b>Location</b>: '.implode($locs, ', ').'</li>';
						echo '</ul>';
						echo '</div>';
					}
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