<?php
/**
 * @package WordPress
 * @subpackage carwashtrader
 * template name: map
*/
session_start();

$searchTerms = 'eg. name, keywords';
$searchPostcode = 'Required for proximity search, eg. SW1, GU6, CO1';
$searchResults = '';
$searchOrderBy = 'post_date';
$searchOrder = 'DESC';
$filtered = false;
$filterList = array();

if(isset($_SESSION["cwt_filtered"]) && $_SESSION["cwt_filtered"] == 'true') {
	$filtered = true;
	$searchTerms = $_SESSION["cwt_searchTerms"];
	if($searchTerms != 'eg. name, keywords')
		$searchResults = '<div class="searchResults">Searching for: '.$searchTerms.'</div>';
	$searchPostcode = $_SESSION["cwt_searchPostcode"];
	
	$searchOrderBy = $_SESSION["cwt_searchOrderBy"];
	$searchOrder = $_SESSION["cwt_searchOrder"];
}
if(isset($_SESSION["cwt_setFilter"])  && is_numeric($_SESSION["cwt_setFilter"])) {
	$setFilter = $_SESSION["cwt_setFilter"];
}

if ( get_query_var('paged') ) { $paged = get_query_var('paged'); }
elseif ( get_query_var('page') ) { $paged = get_query_var('page'); }
else { $paged = 1; }

$listings = array('post_type' => 'cwt_carwashes',
					'posts_per_page' => 1500,
					'paged' => $paged);

if($post->ID == 278) {
	$listings['meta_key'] = 'sale_for_sale';
	$listings['meta_value'] = 'yes';
	$filterPage = '/carwash-for-sale/map-view/';
	$listingsPage = 'carwash-for-sale';
}
else {
	$filterPage = '/car-wash-map-view/';	
	$listingsPage = 'home';
}

if($searchTerms != '' && $searchTerms != 'eg. name, keywords') {
	$listings['s'] = $searchTerms;
	/*$listings['meta_query'] = array('relation' => 'OR', 
									array('key' => 'carwash_address',
										  'value' => $searchTerms,
										  'compare' => 'LIKE'),
									array('key' => 'carwash_postcode',
										  'value' => $searchTerms,
										  'compare' => 'LIKE'));*/
}
if($setFilter != '' && $setFilter != 0) {
	$listings['tax_query'] = array(
								array(
									'taxonomy' => 'cwt_carwash_types',
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
	$postcode = get_field('carwash_geocode', $results[$i]->ID);
	if($results[$i]->post_title != '' && $postcode != '' && $postcode != 'M16 7JL')
	{
		$count++;
		
		if($postcode != '')
		{
			$mapPins .= $postcode."{!}".htmlspecialchars(addslashes($results[$i]->post_title))."<br />";
			$mapPins .= '<a href=&#34;/'.$listingsPage.'/?showID='.$results[$i]->ID.'#showListing&#34; title=&#34;View Listing&#34;>View Listing</a>{!}';
			//$mapPins .= get_field('carwash_address', $results[$i]->ID).", "
			$mapPins .= get_field('carwash_postcode', $results[$i]->ID).";";
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

//reset search criteria if not coming from self or map
$pageReferrer = $_SERVER['HTTP_REFERER'];
$pageReferrer = str_replace('http://','',$pageReferrer);
$pageReferrer = substr($pageReferrer, strpos($pageReferrer, '/')+1);
if(substr($pageReferrer,0,4) != '')
	unset($_SESSION);
include 'includes/header.php';
?>
	<!-- Map -->
        
        <?php 
        	$bgImage = get_field('intro_image', $pageObj->ID); 
        	$displayTitle = get_field('display_title', $pageObj->ID);
        ?>

        <h1<?php echo ($bgImage != '') ? ' class="imageHeader" style="background-image:url('.$bgImage.');"' : ''; ?>>
        <?php echo ($displayTitle != '') ? $displayTitle : $pageObj->post_title; ?></h1>
        <?php echo '<!-- '.$searchOrderBy.'/'.$searchOrder.' -->' ; ?>
        <article>
        	<a name="listings"></a>
            
        	<div class="filterBox">
            	Search Car Wash Listings
                <form id="filterForm" class="filter" method="post" action="/wp-content/themes/carwashtrader/set-filter.php">
                <label class="textInput" for="search_terms">Search terms</label>
                <input type="text" id="search_terms" name="search_terms" value="<?php echo $searchTerms; ?>" onclick="this.select();" />
                <label class="textInput" for="search_postcode">First half of postcode</label>
                <input type="text" id="search_postcode" name="search_postcode" value="<?php echo $searchPostcode; ?>" onclick="this.select();" />
                <!--input type="submit" value="Search" /-->
                <a class="formButton" href="" onclick="setFilter();return false;" value="Clear">Search</a>
                <?php if($filtered) { ?>
                <a class="formButton" href="" onclick="clearFilter();return false;" value="Clear">Clear</a>
                <?php } ?>
                <input type="hidden" name="filterID" id="filterID" value="<?php echo $setFilter; ?>" />
                <input type="hidden" name="filterPage" id="filterPage" value="<?php echo $filterPage; ?>" />
				<input type="hidden" name="action" value="filter" />
                </form>
                <form id="filterClearForm" method="post" action="/wp-content/themes/carwashtrader/set-filter.php">
                <input type="hidden" name="filterPage" id="filterPage" value="<?php echo $filterPage; ?>" />
				<input type="hidden" name="action" value="filterClear" />
                </form>
                <div class="viewToggle"><a href="/<?php echo $listingsPage; ?>/#listings" title="Go to list view">List view</a></div>
            </div>
            
            <h2><?php echo $pageObj->post_title; ?></h2>
            
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