<?php
/**
 * @package WordPress
 * @subpackage carwashtrader
 * template name: search page
*/

get_header(); 
include 'includes/header.php';
?>
	<!-- Search -->
        
        <?php
			$headerImage = '';
			$introImage = get_field('intro_image', $pageObj->ID);
			if($introImage != '')
				$headerImage = ' style="background-image:url('.$introImage.');" class="imageHeader"';
		?>
        
        <h1<?php echo $headerImage; ?>><?php echo (get_field('display_title', $pageObj->ID) != '') ? get_field('display_title', $pageObj->ID) : $pageObj->post_title; ?></h1>
        
        <article>
        	<h2><?php echo $pageObj->post_title; ?></h2>
        	<?php echo apply_filters('the_content', $pageObj->post_content); ?>

            <?php 
                $searchTerms = $_GET["search_terms"];
                $searchOrder = $_GET["order"];
                $searchOrderBy = $_GET["orderby"];
            ?>

            <div class="searchBox">
                <form id="filterForm" class="filter" method="get" action="/search/">
                <input type="text" name="search_terms" value="Search terms" onclick="this.select();" />
                <input type="submit" value="Search" />
                <?php if(isset($_POST["search_terms"])) { ?>
                <a class="formButton" href="" onclick="clearFilter();return false;" value="Clear">Clear</a>
                <?php } ?>
                <div class="filterOrder">Order By: 
                <input type="radio" name="orderby" id="order_date" value="post_date" checked="checked" /><label for="order_date">Date</label>
                <input type="radio" name="orderby" id="order_name" value="post_title" <?php if($searchOrderBy == 'post_title') echo 'checked="checked" '; ?>/><label for="order_name">Name</label>
                </div>
                <div class="filterOrder">Order: 
                <input type="radio" name="order" id="order_desc" value="DESC" checked="checked" <?php if($searchOrder == 'DESC') echo 'checked="checked" '; ?>/><label for="order_desc">Descending</label>
                <input type="radio" name="order" id="order_asc" value="ASC" <?php if($searchOrder == 'ASC') echo 'checked="checked" '; ?>/><label for="order_asc">Ascending</label>
                </div>
                </form>
            </div>

            <?php

                $listings = array('post_type' => 'page',
                                    'orderby' => $searchOrderBy,
                                    'order' => $searchOrder,
                                    'posts_per_page' => 15,
                                    'paged' => $paged);

                if(isset($_GET["search_terms"]) && $searchTerms != 'Search terms') {
                    remove_all_filters('posts_orderby');//prevent plugin clashing with custom ordering
                    $results = query_posts($listings);
                
                    echo '<h2>Results</h2>';

                    if(sizeof($results) > 0) {
                        for($r=0; $r<sizeof($results); $r++) {
                            echo $results[$r]->post_title.'<br />';
                        }

                    } else {
                        $noresults = get_page(170);
                        echo '<div class="confirmation">';
                        echo '<h3>'.get_field('display_title', $noresults->ID).'</h3>';
                        echo apply_filters('the_content', $noresults->post_content);
                        echo '</div>';
                    }

                } 
            ?>
        </article>
        
        <section>
        	<?php include 'includes/menu-jobalerts.php'; ?>
        	<?php include 'includes/menu-reviews.php'; ?>
            
            <?php include 'includes/adverts-menu.php'; ?>
        </section>
    </div>

<?php 
include 'includes/footer.php';
get_footer(); 
?> 