<?php
/**
 * @package WordPress
 * @subpackage carwashtrader
 * template name: jobs map
*/
session_start();

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

if ( get_query_var('paged') ) { $paged = get_query_var('paged'); }
elseif ( get_query_var('page') ) { $paged = get_query_var('page'); }
else { $paged = 1; }

$listings = array('post_type' => 'cwt_jobs',
					'orderby' => $searchOrderBy,
					'order' => $searchOrder,
					'posts_per_page' => 1500,
					'paged' => $paged);

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


remove_all_filters('posts_orderby');//prevent plugin clashing with custom ordering
$results = query_posts($listings);

//Place map pins
$mapPins = '';
$count = 0;
for($i=0; $i<sizeof($results); $i++)
{
	$postcode = get_field('job_geocode', $results[$i]->ID);
	if($results[$i]->post_title != '' && $postcode != '' && $postcode != 'M16 7JL')
	{
		$count++;
		
		if($postcode != '')
		{
			$mapPins .= $postcode."{!}".htmlspecialchars(addslashes($results[$i]->post_title))."{!}";
			$mapPins .= '<a href=&#34;/jobs/?showID='.$results[$i]->ID.'#showListing&#34; title=&#34;View Listing&#34;>View Listing</a>{!}';
			//$mapPins .= get_field('job_address', $results[$i]->ID).", "
			$mapPins .= get_field('job_postcode', $results[$i]->ID).";";
		}
	}
}

global $bodyTagContent;

if($searchPostcode != '' && $searchPostcode != 'Required for proximity search, eg. SW1, GU6, CO1') {
	$bodyTagContent = 'onload="initialize(\''.$searchPostcode.'\', \'map_canvas\', false, \''.$mapPins.'\');"';
} else {
	$bodyTagContent = 'onload="initialize(\'0\', \'map_canvas\', false, \''.$mapPins.'\');"';
}
get_header(); 
include 'includes/header.php';
?>
	<!-- Jobs Map -->
        
        <?php 
        	$bgImage = get_field('intro_image', $pageObj->ID); 
        	$displayTitle = get_field('display_title', $pageObj->ID);
        ?>

        <h1<?php echo ($bgImage != '') ? ' class="imageHeader" style="background-image:url('.$bgImage.');"' : ''; ?>>
        <?php echo ($displayTitle != '') ? $displayTitle : $pageObj->post_title; ?></h1>
        
        <article>
        	<a name="listings"></a>
            <?php
				$searchTerms = 'Search terms, eg. name, location';
				$searchPostcode = 'Required for proximity search, eg. SW1, GU6, CO1';
				$searchResults = '';
				$searchOrderBy = 'post_date';
				$searchOrder = 'DESC';
				$filtered = false;
				$filterList = array();
				
				if(isset($_SESSION["cwt_jobFiltered"]) && $_SESSION["cwt_jobFiltered"] == 'true') {
					$filtered = true;
					$searchTerms = $_SESSION["cwt_jobSearchTerms"];
					if($searchTerms != 'Search terms, eg. name, location')
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
                <div class="filterOrder">Order By: 
                <input type="radio" name="orderby" id="order_date" value="post_date" checked="checked" /><label for="order_date">Date</label>
                <input type="radio" name="orderby" id="order_name" value="post_title" <?php if($searchOrderBy == 'post_title') echo 'checked="checked" '; ?>/><label for="order_name">Name</label>
                <input type="radio" name="orderby" id="order_postcode" value="distance" <?php if($searchOrderBy == 'distance') echo 'checked="checked" '; ?>/><label for="order_postcode">Distance</label>
                </div>
                <div class="filterOrder">Order: 
                <input type="radio" name="order" id="order_desc" value="DESC" checked="checked" <?php if($searchOrder == 'DESC') echo 'checked="checked" '; ?>/><label for="order_desc">Descending</label>
                <input type="radio" name="order" id="order_asc" value="ASC" <?php if($searchOrder == 'ASC') echo 'checked="checked" '; ?>/><label for="order_asc">Ascending</label>
                </div>
                <input type="hidden" name="filterID" id="filterID" value="<?php echo $setFilter; ?>" />
                <input type="hidden" name="filterPage" id="filterPage" value="<?php echo $filterPage; ?>" />
				<input type="hidden" name="action" value="filter" />
                </form>
                <form id="filterClearForm" method="post" action="/wp-content/themes/carwashtrader/set-filterJobs.php">
                <input type="hidden" name="filterPage" id="filterPage" value="<?php echo $filterPage; ?>" />
				<input type="hidden" name="action" value="filterClear" />
                </form>
                <div class="viewToggle"><a href="/jobs/#listings" title="Go to list view">List view</a></div>
            </div>
            
            <h2>Job Listings</h2>
            
            <?php
				
				if ( have_posts() ) {
					
					echo '<div class="mainMap" id="map_canvas"></div>';
					
				} else {
					$noresults = get_page(170);
					echo '<div class="confirmation">';
					echo '<h3>'.get_field('display_title', $noresults->ID).'</h3>';
					echo apply_filters('the_content', $noresults->post_content);
					echo '</div>';
				}
			?>
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