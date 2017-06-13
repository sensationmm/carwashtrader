<?php
	switch($filterPage) {
		case '/jobs/': 
			$filterListRequired = 'cwt_job_types';
			$postType = 'cwt_jobs';
			break;
		case '/carwash-for-sale/': 
			$filterListRequired = 'cwt_forsale_types';
			$postType = 'cwt_carwashes';
			break;
		case '/suppliers/': 
			$filterListRequired = 'cwt_prodsupp_cats';
			$postType = 'cwt_suppliers';
			$filterSet = $_SESSION["cwt_supplierSetFilter"];
			break;
		case '/products/': 
			$filterListRequired = 'cwt_prodsupp_cats';
			$postType = 'cwt_products';
			$filterSet = $_SESSION["cwt_productSetFilter"];
			break;
		case '/home/': 
		case '/':
		default:
			$filterListRequired = 'cwt_carwash_types';
			$postType = 'cwt_carwashes';
			break;
	}
?>
			<div class="filter">
            	<div class="header">Filter Listings</div>
                <ul>
                <?php
                	echo '<li';
					if(0 == $setFilter)
						echo ' class="active"';
					echo '><a href="" onclick="setFilter(0);return false;">All</a></li>';
					
					$args = array('hide_empty'=>true,
								  'parent'=>0,
								  'orderby'=>'term_order',
								  'post_type' => $postType);

					$types = get_terms($filterListRequired, $args); 
					
					$typesKeys = array_keys($types);

					for($t=0; $t<sizeof($types); $t++)
					{
						/*
						$types[$typesKeys[$t]]->term_id
						$types[$typesKeys[$t]]->name
						*/

						//problem with prods and supps sharing taxonomy so restrict
						if($filterListRequired == 'cwt_prodsupp_cats') {
							$checkRight = array(
							  'post_type' => $postType,
							  'numberposts' => -1,
							  'tax_query' => array(
							    array(
							      'taxonomy' => $filterListRequired,
							      'field' => 'id',
							      'terms' => $types[$typesKeys[$t]]->term_id
							    )
							  )
							);
							$checkRightPosts = get_posts($checkRight); 
							$checkRightPosts = sizeof($checkRightPosts);
						} else $checkRightPosts = 1;

						$ancestors = get_ancestors( $types[$typesKeys[$t]]->term_id, 'cwt_prodsupp_cats' );

						if((sizeof($ancestors) == 0 || $types[$typesKeys[$t]]->parent == $filterSet) && $checkRightPosts > 0) {

							if($types[$typesKeys[$t]]->term_id != '') {
								echo '<li';
								if($types[$typesKeys[$t]]->term_id == $setFilter)
									echo ' class="active"';
								echo '><a  href="" onclick="setFilter('.$types[$typesKeys[$t]]->term_id.');return false;">';
								echo $types[$typesKeys[$t]]->name.'</a></li>';

								$typesSub = get_terms($filterListRequired, array('hide_empty'=>true,'parent'=>$types[$typesKeys[$t]]->term_id,'orderby'=>'term_order')); 
								for($s=0; $s<sizeof($typesSub); $s++)
								{
									/*
									$types[$typesKeys[$t]]->term_id
									$types[$typesKeys[$t]]->name
									*/

									$ancestorsSub = get_ancestors( $typesSub[$s]->term_id, 'cwt_prodsupp_cats' );

									$filterterm = get_term( $setFilter, 'cwt_prodsupp_cats' );

									if($filterListRequired == 'cwt_prodsupp_cats') {
										$checkRight = array(
										  'post_type' => $postType,
										  'numberposts' => -1,
										  'tax_query' => array(
										    array(
										      'taxonomy' => $filterListRequired,
										      'field' => 'id',
										      'terms' => $typesSub[$s]->term_id
										    )
										  )
										);
										$checkRightPosts = get_posts($checkRight); 
										$checkRightPosts = sizeof($checkRightPosts);
									} else $checkRightPosts = 1;

									if((sizeof($ancestorsSub) == 0 || $typesSub[$s]->parent == $filterSet || $typesSub[$s]->parent == $filterterm->parent) && $checkRightPosts > 0) {
									
										echo '<li class="sub';
										if($typesSub[$s]->term_id == $setFilter)
											echo ' active';
										echo '""><a style="font-size:0.8em;margin-left:10px;" href="" onclick="setFilter('.$typesSub[$s]->term_id.');return false;">';
										echo $typesSub[$s]->name.'</a></li>';
									}
								}
							}
					
						}
					}
				?>
                </ul>
            </div>