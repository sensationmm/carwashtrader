<?php
/**
 * @package WordPress
 * @subpackage carwashtrader
 * template name: my cwt - job
*/

get_header(); 
include 'includes/secure.php';

global $current_user, $wp_roles;
get_currentuserinfo();
$error = array();    

include 'includes/header.php';
?>
	<!-- MyCWT - Jobs -->
        
        <h1><?php echo (get_field('display_title', $pageObj->ID) != '') ? get_field('display_title', $pageObj->ID) : $pageObj->post_title; ?></h1>
        
        <article>
        	<h2>My Jobs</h2>
        	<?php echo apply_filters('the_content', $page->post_content); ?>
            
            <?php if($_GET["added"] == true) { ?>
                <div class="confirmation added">
                    <h3>Job added successfully!</h3>
                    <p>You can add another <a href="/my-cwt/jobs/add-job/" title="Add a Job">here</a></p>
                </div>
            <?php } else if($_GET["edited"] == true) { ?>
                <div class="confirmation edited">
                    <h3>Job edited successfully!</h3>
                    <p>You can edit another by clicking the edit link below</p>
                </div>
            <?php } else { 

            	echo '<div class="addListing">';
				echo '<a href="/my-cwt/jobs/add-job/" title="Post a Job">Post a Job</a>';
				echo '</div>';
			}
			
			if($_POST["action"] == 'deleteJob' && is_numeric($_POST["listingID"])) {
				wp_delete_post($_POST["listingID"]);
				echo '<div class="confirmation deleted">';
                    echo '<h3>Job deleted successfully!</h3>';
                    echo '<p>Your listing will no longer appear on the site</p>';
                echo '</div>';
			}
			?>
            
            <?php
				if ( get_query_var('paged') ) { $paged = get_query_var('paged'); }
				elseif ( get_query_var('page') ) { $paged = get_query_var('page'); }
				else { $paged = 1; }

				$listings = array('post_type' => 'cwt_jobs',
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
						$address = get_field('job_address', $listingID);
						$postcode = get_field('job_postcode', $listingID);
						$geocode = get_field('job_geocode', $listingID);
						$hours = get_field('job_hours', $listingID);
						$salary = get_field('job_salary', $listingID);
						$apply = get_field('job_apply', $listingID);

						$paid = get_field('premium_paypalID', $listingID);
						
						if($paid != '' && $results[$r]->post_status == 'publish') {
							echo '<div class="listingEdit">';
							echo '<a href="/my-cwt/jobs/edit-job/?id='.$listingID.'" title="Add a Job">Edit this job</a></div>';
						} else {
							echo '<div class="listingEdit">';
							echo '<a href="/my-cwt/jobs/payment/?id='.$listingID.'" title="Pay for listing">LISTING NOT PUBLISHED - PAY FOR THIS LISTING</a></div>';
						}
						
						echo outputJobListing($listingID,$title,$description,$address,$postcode,$geocode,$hours,$salary,$apply);
	
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