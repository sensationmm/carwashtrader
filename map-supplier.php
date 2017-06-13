<?php
/**
 * @package WordPress
 * @subpackage carwashtrader
 * template name: map supplier
*/
session_start();

$searchTerms = 'eg. name, keywords';
$searchPostcode = 'Required for proximity search, eg. SW1, GU6, CO1';
$searchResults = '';
$searchOrderBy = 'post_date';
$searchOrder = 'DESC';
$filtered = false;
$filterList = array();

if(isset($_SESSION["cwt_supplierFiltered"]) && $_SESSION["cwt_supplierFiltered"] == 'true') {
	$filtered = true;
	$searchTerms = $_SESSION["cwt_supplierSearchTerms"];
	if($searchTerms != 'eg. name, keywords')
		$searchResults = '<div class="searchResults">Searching for: '.$searchTerms.'</div>';
	$searchPostcode = $_SESSION["cwt_supplierSearchPostcode"];
	
	$searchOrderBy = $_SESSION["cwt_supplierSearchOrderBy"];
	$searchOrder = $_SESSION["cwt_supplierSearchOrder"];
}
if(isset($_SESSION["cwt_supplierSetFilter"])  && is_numeric($_SESSION["cwt_supplierSetFilter"])) {
	$setFilter = $_SESSION["cwt_supplierSetFilter"];
}

if ( get_query_var('paged') ) { $paged = get_query_var('paged'); }
elseif ( get_query_var('page') ) { $paged = get_query_var('page'); }
else { $paged = 1; }

$listings = array('post_type' => 'cwt_suppliers',
					'orderby' => $searchOrderBy,
					'order' => $searchOrder,
					'posts_per_page' => 1500,
					'paged' => $paged,
					'post_status' => 'publish');

if($post->ID == 278) {
	$listings['meta_key'] = 'sale_for_sale';
	$listings['meta_value'] = 'yes';
	$filterPage = '/suppliers/map-view/';
}
else
	$filterPage = '/suppliers/';

if($searchTerms != '' && $searchTerms != 'eg. name, keywords') {
	$listings['s'] = $searchTerms;
	/*$listings['meta_query'] = array('relation' => 'OR', 
									array('key' => 'supplier_address',
										  'value' => $searchTerms,
										  'compare' => 'LIKE'),
									array('key' => 'supplier_postcode',
										  'value' => $searchTerms,
										  'compare' => 'LIKE'));*/
}
if($setFilter != '' && $setFilter != 0) {
	$listings['tax_query'] = array(
								array(
									'taxonomy' => 'cwt_prodsupp_cats',
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
	$postcode = get_field('supplier_geocode', $results[$i]->ID);
	if($results[$i]->post_title != '' && $postcode != '' && $postcode != 'M16 7JL')
	{
		$count++;
		
		if($postcode != '')
		{
			$mapPins .= $postcode."{!}".htmlspecialchars(addslashes($results[$i]->post_title))."{!}";
			$mapPins .= '<a href=&#34;/suppliers/?showID='.$results[$i]->ID.'#showListing&#34; title=&#34;View Listing&#34;>View Listing</a>{!}';
			//$mapPins .= get_field('supplier_address', $results[$i]->ID).", ";
			$mapPins .= get_field('supplier_postcode', $results[$i]->ID).";";
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
	<!-- Map Supplier -->
        
        <?php 
        	$bgImage = get_field('intro_image', $pageObj->ID); 
        	$displayTitle = get_field('display_title', $pageObj->ID);
        ?>

        <h1<?php echo ($bgImage != '') ? ' class="imageHeader" style="background-image:url('.$bgImage.');"' : ''; ?>>
        <?php echo ($displayTitle != '') ? $displayTitle : $pageObj->post_title; ?></h1>
        
        <article>
        	<a name="listings"></a>
            
        	<div class="filterBox">
            	Search Supplier Listings
                <?php $filterPage = '/suppliers/'; ?>
                <form id="filterForm" class="filter" method="post" action="/wp-content/themes/carwashtrader/set-filterSuppliers.php">
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
                <form id="filterClearForm" method="post" action="/wp-content/themes/carwashtrader/set-filterSuppliers.php">
                <input type="hidden" name="filterPage" id="filterPage" value="<?php echo $filterPage; ?>" />
				<input type="hidden" name="action" value="filterClear" />
                </form>
                <div class="viewToggle"><a href="/suppliers/#listings" title="Go to list view">List view</a></div>
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