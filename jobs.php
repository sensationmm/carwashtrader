<?php
/**
 * @package WordPress
 * @subpackage carwashtrader
 * template name: jobs
*/
get_header(); 

//reset search criteria if not coming from self or map
$pageReferrer = $_SERVER['HTTP_REFERER'];
$pageReferrer = str_replace('http://','',$pageReferrer);
$pageReferrer = substr($pageReferrer, strpos($pageReferrer, '/')+1);
if(substr($pageReferrer,0,4) != 'jobs')
	include 'unset-filter.php';

include 'includes/header.php';
?>
	<!-- Jobs -->
        
        <?php 
        	$bgImage = get_field('intro_image', $pageObj->ID); 
        	$displayTitle = get_field('display_title', $pageObj->ID);
        ?>

        <h1<?php echo ($bgImage != '') ? ' class="imageHeader" style="background-image:url('.$bgImage.');"' : ''; ?>>
        <?php echo ($displayTitle != '') ? $displayTitle : $pageObj->post_title; ?></h1>
        
        <article>
        	<a name="listings"></a>
            <?php
				$searchTerms = 'eg. name, keywords';
				$searchPostcode = 'Required for proximity search, eg. SW1, GU6, CO1';
				$searchResults = '';
				$searchOrderBy = 'post_date';
				$searchOrder = 'DESC';
				$filtered = false;
				$filterList = array();
				
				if(isset($_SESSION["cwt_jobFiltered"]) && $_SESSION["cwt_jobFiltered"] == 'true') {
					$filtered = true;
					$searchTerms = $_SESSION["cwt_jobSearchTerms"];
					if($searchTerms != 'eg. name, keywords')
						$searchResults = '<div class="searchResults">Searching for: '.$searchTerms.'</div>';
					$searchPostcode = $_SESSION["cwt_jobSearchPostcode"];
					
					$searchOrderBy = $_SESSION["cwt_jobSearchOrderBy"];
					$searchOrder = $_SESSION["cwt_jobSearchOrder"];
				}
				if(isset($_SESSION["cwt_setJobFilter"])  && is_numeric($_SESSION["cwt_setJobFilter"])) {
					$setFilter = $_SESSION["cwt_setJobFilter"];
				}
			?>
            
        	<div class="filterBox">
            	Search Job Listings
                <?php $filterPage = '/jobs/'; ?>
                <form id="filterForm" class="filter" method="post" action="/wp-content/themes/carwashtrader/set-filterJobs.php">
                <label class="textInput" for="search_terms">Search terms</label>
                <input type="text" id="search_terms" name="search_terms" value="<?php echo $searchTerms; ?>" onclick="this.select();" />
                <label class="textInput" for="search_postcode">First half of postcode</label>
                <input type="text" id="search_postcode" name="search_postcode" value="<?php echo $searchPostcode; ?>" onclick="this.select();" />
                <!--input type="submit" value="Search" /-->
                <a class="formButton" href="" onclick="setFilter();return false;" value="Clear">Search</a>
                <?php if($filtered) { ?>
                <a class="formButton" href="" onclick="clearFilter();return false;" value="Clear">Clear</a>
                <?php } ?>
                <div class="filterOrderBy">Sort By: 
	                <input type="radio" name="orderby" id="order_date" value="post_date" checked="checked" onclick="selectOrder('order_desc');" />
	                	<label for="order_date">Date</label>
	                <input type="radio" name="orderby" id="order_name" value="post_title" <?php if($searchOrderBy == 'post_title') echo 'checked="checked" '; ?> onclick="selectOrder('order_asc');" />
	                	<label for="order_name">Name</label>
	                <input <?php if($searchPostcode == '' || $searchPostcode == 'Required for proximity search, eg. SW1, GU6, CO1') echo 'style="display:none;"'; ?> type="radio" name="orderby" id="order_postcode" value="distance" <?php if($searchOrderBy == 'distance') echo 'checked="checked" '; ?> onclick="selectOrder('order_asc');" />
	                	<label <?php if($searchPostcode == '' || $searchPostcode == 'Required for proximity search, eg. SW1, GU6, CO1') echo 'style="display:none;"'; ?> for="order_postcode" id="order_postcode_label">Distance</label>
                </div>
                <div class="filterOrder">Order: 
	                <input type="radio" name="order" id="order_desc" value="DESC" checked="checked" <?php if($searchOrder == 'DESC') echo 'checked="checked" '; ?>/>
	                	<label for="order_desc">Z-A</label>
	                <input type="radio" name="order" id="order_asc" value="ASC" <?php if($searchOrder == 'ASC') echo 'checked="checked" '; ?>/>
	                	<label for="order_asc">A-Z</label>
                </div>
                <input type="hidden" name="filterID" id="filterID" value="<?php echo $setFilter; ?>" />
                <input type="hidden" name="filterPage" id="filterPage" value="<?php echo $filterPage; ?>" />
				<input type="hidden" name="action" value="filter" />
                </form>
                <form id="filterClearForm" method="post" action="/wp-content/themes/carwashtrader/set-filterJobs.php">
                <input type="hidden" name="filterPage" id="filterPage" value="<?php echo $filterPage; ?>" />
				<input type="hidden" name="action" value="filterClear" />
                </form>
                <div class="viewToggle"><a href="/jobs/map-view/#listings" title="Go to list view">Map view</a></div>
            </div>

            <div class="filterMobile"><?php include 'includes/menu-filter.php'; ?></div>
            
            <h2>Job Listings</h2>
        	<a name="showListing"></a>
            
            <?php
            	if(isset($_GET["showID"])) {

            		$showListing = get_post($_GET["showID"]);

            		$listingID = $showListing->ID;

					$title = $showListing->post_title;
					$description = $showListing->post_content;
					$address = get_field('job_address', $listingID);
					$postcode = get_field('job_postcode', $listingID);
					$geocode = get_field('job_geocode', $listingID);
					$hours = get_field('job_hours', $listingID);
					$salary = get_field('job_salary', $listingID);
					$apply = get_field('job_apply', $listingID);
					
					echo outputJobListing($listingID,$title,$description,$address,$postcode,$geocode,$hours,$salary,$apply);

					echo '<p style="clear:both;"><a href="javascript:history.go(-1);" title="Back to previous page">&laquo; Back</a></p>';

					if(isset($_GET["showID"])) {
						echo '<script type="text/javascript">';
						echo 'initialize("'.$geocode.'", "map_canvas'.$listingID.'", true);';
						echo '</script>';
					}

            	} else {
					if ( get_query_var('paged') ) { $paged = get_query_var('paged'); }
					elseif ( get_query_var('page') ) { $paged = get_query_var('page'); }
					else { $paged = 1; }

					$listings = array('post_type' => 'cwt_jobs',
										'orderby' => $searchOrderBy,
										'order' => $searchOrder,
										'posts_per_page' => -1);
					
					if($searchTerms != '' && $searchTerms != 'eg. name, keywords') {
						$listings['s'] = $searchTerms;
						/*$listings['meta_query'] = array('relation' => 'OR', 
														array('key' => 'job_address',
															  'value' => $searchTerms,
															  'compare' => 'LIKE'),
														array('key' => 'job_postcode',
															  'value' => $searchTerms,
															  'compare' => 'LIKE'));*/
					}
					if($setFilter != '' && $setFilter != 0) {
						$listings['tax_query'] = array(
													array(
														'taxonomy' => 'cwt_job_types',
														'field' => 'id',
														'terms' => $setFilter
													)
												);
					}
					//if($current_user-> ID == 17) { echo '<div class="clear"></div><pre>';print_r($listings);echo '</pre>';} 
										
					/*if($filtered)
						echo $searchResults;*/
					
					remove_all_filters('posts_orderby');//prevent plugin clashing with custom ordering
					$results = query_posts($listings);

					global $wp_query; 
         			$totalNumPages = ceil(($wp_query->found_posts/15));
					
					$doDistance = 'false';
					if($searchPostcode != '' && $searchPostcode != 'Required for proximity search, eg. SW1, GU6, CO1') {
						$doDistance = 'true';
						//get search origin
						if(strpos($searchPostcode, ' ') != '')
							$postcodeArea = strtoupper(substr($searchPostcode, 0, strpos($searchPostcode, ' ')));
						else
							$postcodeArea = $searchPostcode;
							
						$location = 'SELECT eastings AS x, northings AS y FROM wp_postcodes WHERE postcode = "'.$postcodeArea.'"';
						$location = mysql_query($location) or die('Location: '.$location.' - '.mysql_error());
						if(mysql_num_rows($location) != '0')
							$location = mysql_fetch_assoc($location);
			
						for($r=0; $r<sizeof($results); $r++) {
							$listingID = $results[$r]->ID;
							$listingLoc = get_field('job_postcode', $listingID);
							if(strpos($listingLoc, ' ') != '')
								$postcodeArea = strtoupper(substr($listingLoc, 0, strpos($listingLoc, ' ')));
							else
								$postcodeArea = $listingLoc;
							$loc = 'SELECT eastings AS x, northings AS y FROM wp_postcodes WHERE postcode = "'.$postcodeArea.'"';
							$loc = mysql_query($loc) or die('Loc: '.mysql_error());
							$loc = mysql_fetch_assoc($loc);
							
							$dist1 = abs($location['x'] - $loc['x']);
							$dist2 = abs($location['y'] - $loc['y']);
							
							$dist1 = $dist1^2;
							$dist2 = $dist2^2;
							
							$distance_in_metres = $dist1 + $dist2;
							$distance_in_metres = $distance_in_metres^0.5;
							$distance_in_miles = $distance_in_metres/1609;
							$distance_in_miles = number_format($distance_in_miles, 3);
							
							$results[$r]->distance = $distance_in_miles;
						}
					}
					
					if($searchOrderBy == 'distance') {
						usort($results, 'sort_listings_by_distance');
					}
					//echo '<pre>';print_r($results);echo '</pre>';
					
					$advertCount = 0;
					if(sizeof($results) > 0) {

						$start = ($paged -1)*15;
						$end = $paged*15;
						for($r=$start; $r<$end; $r++) {
							$listingID = $results[$r]->ID;

							if($listingID != '') {

								$title = $results[$r]->post_title;
								$description = $results[$r]->post_content;
								$address = get_field('job_address', $listingID);
								$postcode = get_field('job_postcode', $listingID);
								$geocode = get_field('job_geocode', $listingID);
								$hours = get_field('job_hours', $listingID);
								$salary = get_field('job_salary', $listingID);
								$apply = get_field('job_apply', $listingID);
								
								$distance = '';
								if($doDistance == 'true')
									$distance = $results[$r]->distance;
								
								echo outputJobListing($listingID,$title,$description,$address,$postcode,$geocode,$hours,$salary,$apply,$distance,$doDistance);
							}
							//Adverts
							$a = ($r>15) ? $r%15 : $r;
							if($a > 0 && ($a+1)<15 && (($a+1)%5) == 0 && sizeof($results) > $advertCount*5) {
								$advertCount++;
								echo outputAdverts('job-listings', 'in-listing', 3, $advertCount); 
							}
							//End adverts
						}
						$paginateArgs = array('current' => $paged,
											  'total' => $totalNumPages);
						if(paginate_links($paginateArgs) != '')
							echo '<div class="pagination">'.paginate_links($paginateArgs).'</div>';
					} else {
						$noresults = get_page(170);
						echo '<div class="confirmation">';
						echo '<h3>'.get_field('display_title', $noresults->ID).'</h3>';
						echo apply_filters('the_content', $noresults->post_content);
						echo '</div>';
					}
				}
			?>
            
            <!--div class="adverts">
            	<div class="advert"></div>
            	<div class="advert"></div>
            	<div class="advert"></div>
            </div-->
        </article>
        
        <section>
        	<?php include 'includes/menu-filter.php'; ?>
        	<?php include 'includes/menu-jobalerts.php'; ?>
        	<?php include 'includes/menu-reviews.php'; ?>
            
            <?php include 'includes/adverts-menu.php'; ?>
        </section>
    </div>

<?php 
include 'includes/footer.php';
get_footer(); 
?> 