<?php
/**
 * @package WordPress
 * @subpackage carwashtrader
 * template name: products
*/
get_header(); 

//reset search criteria if not coming from self or map
$pageReferrer = $_SERVER['HTTP_REFERER'];
$pageReferrer = str_replace('http://','',$pageReferrer);
$pageReferrer = substr($pageReferrer, strpos($pageReferrer, '/')+1);
if(substr($pageReferrer,0,4) != 'prod')
	include 'unset-filter.php';

include 'includes/header.php';
?>
	<!-- Products -->
        
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
				$searchResults = '';
				$searchOrderBy = 'post_date';
				$searchOrder = 'DESC';
				$filtered = false;
				$filterList = array();
				
				if(isset($_SESSION["cwt_productFiltered"]) && $_SESSION["cwt_productFiltered"] == 'true') {
					$filtered = true;
					$searchTerms = $_SESSION["cwt_productSearchTerms"];
					if($searchTerms != 'eg. name, keywords')
						$searchResults = '<div class="searchResults">Searching for: '.$searchTerms.'</div>';
					
					$searchOrderBy = $_SESSION["cwt_productSearchOrderBy"];
					$searchOrder = $_SESSION["cwt_productSearchOrder"];
				}
				if(isset($_SESSION["cwt_productSetFilter"])  && is_numeric($_SESSION["cwt_productSetFilter"])) {
					$setFilter = $_SESSION["cwt_productSetFilter"];
				}
			?>
            
        	<div class="filterBox">
            	Search Product Listings
                <?php $filterPage = '/products/'; ?>
                <form id="filterForm" class="filter" method="post" action="/wp-content/themes/carwashtrader/set-filterProducts.php">
                <label class="textInput" for="search_terms">Search terms</label>
                <input type="text" id="search_terms" name="search_terms" value="<?php echo $searchTerms; ?>" onclick="this.select();" />
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
                <form id="filterClearForm" method="post" action="/wp-content/themes/carwashtrader/set-filterProducts.php">
                <input type="hidden" name="filterPage" id="filterPage" value="<?php echo $filterPage; ?>" />
				<input type="hidden" name="action" value="filterClear" />
                </form>
            </div>

            <div class="filterMobile"><?php include 'includes/menu-filter.php'; ?></div>
            
            <h2>Products Listings</h2>
        	<a name="showListing"></a>
            
            <?php
            	if(isset($_GET["showID"])) {

            		$showListing = get_post($_GET["showID"]);

            		$listingID = $showListing->ID;
						
					$title = $showListing->post_title;
					$description = $showListing->post_content;
					$contactdetails = get_field('product_contactdetails', $listingID);
					$link = get_field('product_link', $listingID);
					$price = get_field('product_price', $listingID);

					$author = $results[$r]->post_author;
					$supplierDetails = array('post_type' => 'cwt_suppliers', 'author' => $author);
					$supplierID = query_posts($supplierDetails);
					
					echo outputProductListing($listingID,$title,$description,$contactdetails,$link,$price,$supplierID[0]->ID);


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

					$listings = array('post_type' => 'cwt_products',
										'orderby' => $searchOrderBy,
										'order' => $searchOrder,
										'posts_per_page' => -1);
					
					if($searchTerms != '' && $searchTerms != 'eg. name, keywords') {
						$listings['s'] = $searchTerms;
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
					//if($current_user-> ID == 17) { echo '<div class="clear"></div><pre>';print_r($listings);echo '</pre>';} 
										
					/*if($filtered)
						echo $searchResults;*/
					
					remove_all_filters('posts_orderby');//prevent plugin clashing with custom ordering
					$results = query_posts($listings);

					global $wp_query; 
         			$totalNumPages = ceil(($wp_query->found_posts/15));

					//echo '<pre>';print_r($results);echo '</pre>';
					
					$advertCount = 0;
					if(sizeof($results) > 0) {

						$start = ($paged -1)*15;
						$end = $paged*15;
						for($r=$start; $r<$end; $r++) {
							//the_post();
							//$listingObject = get_post();
							//$listingID = get_the_ID();
							$listingID = $results[$r]->ID;

							if($listingID != '') {
								$title = $results[$r]->post_title;
								$description = $results[$r]->post_content;
								$contactdetails = get_field('product_contactdetails', $listingID);
								$link = get_field('product_link', $listingID);
								$price = get_field('product_price', $listingID);

								$author = $results[$r]->post_author;
								$supplierDetails = array('post_type' => 'cwt_suppliers', 'author' => $author);
								$supplierID = query_posts($supplierDetails);
								
								echo outputProductListing($listingID,$title,$description,$contactdetails,$link,$price,$supplierID[0]->ID);
							}
							//Adverts
							$a = ($r>15) ? $r%15 : $r;
							if($a > 0 && ($a+1)<15 && (($a+1)%5) == 0 && sizeof($results) > $advertCount*5) {
								$advertCount++;
								echo outputAdverts('product-listings', 'in-listing', 3, $advertCount); 
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