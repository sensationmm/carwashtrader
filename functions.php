<?php 

	//Hide posts from menu - use custom post types for intuitiveness
	function remove_menus(){
  
	  remove_menu_page( 'edit.php' );                   //Posts
	  remove_menu_page( 'upload.php' );                 //Media
	  remove_menu_page( 'edit-comments.php' );          //Comments
	  
	}
	add_action( 'admin_menu', 'remove_menus' );
	add_theme_support( 'post-thumbnails' );


	function change_link( $permalink, $post ) {
	    if( $post->post_type == 'cwt_carwashes' ) { // assuming the post type is video
	        $permalink = home_url( '/?showID='.$post->ID );
	    } else if( $post->post_type == 'cwt_suppliers' ) { // assuming the post type is video
	        $permalink = home_url( '/suppliers/?showID='.$post->ID );
	    } else if( $post->post_type == 'cwt_products' ) { // assuming the post type is video
	        $permalink = home_url( '/products/?showID='.$post->ID );
	    } else if( $post->post_type == 'cwt_jobs' ) { // assuming the post type is video
	        $permalink = home_url( '/jobs/?showID='.$post->ID );
	    } else if( $post->post_type == 'cwt_adverts' ) { // assuming the post type is video
	        $permalink = '';
	    }
	    return $permalink;
	}
	add_filter('post_type_link',"change_link",10,2);
	
	
	//Register custom user types
	add_action( 'init', 'create_user_types');
	function create_user_types() {
		add_role('guest', __('Guest'), array('read' => true, 'edit_posts' => true, 'delete_posts' => false,));
		add_role('trader', __('Trader'), array('read' => true, 'edit_posts' => true, 'delete_posts' => false,));
		add_role('estateagent', __('Estate Agent'), array('read' => true, 'edit_posts' => true, 'delete_posts' => false,));
		add_role('supplier', __('Supplier'), array('read' => true, 'edit_posts' => true, 'delete_posts' => false,));
	}


	function search_url_rewrite_rule() {
		if ( is_search() && !empty($_GET['s'])) {
			wp_redirect(home_url("/search/") .'?'. urlencode(get_query_var('s')));
			exit();
		}	
	}
	add_action('template_redirect', 'search_url_rewrite_rule');


	//Register custom post types
	add_action( 'init', 'create_post_type');
	function create_post_type() {
	  register_post_type( 'cwt_carwashes',
		array('labels' => array( 'name' => __( 'Carwashes' ), 'singular_name' => __( 'Carwash' )),
		  'public' => true, 'has_archive' => true, 'menu_position' => 5, 'supports' => array('title','editor','author','thumbnail'))
	  );
	  
	  register_post_type( 'cwt_suppliers',
		array('labels' => array( 'name' => __( 'Suppliers' ), 'singular_name' => __( 'Supplier' )),
		  'public' => true, 'has_archive' => true, 'menu_position' => 5, 'supports' => array('title','editor','author','thumbnail'))
	  );
	  
	  register_post_type( 'cwt_products',
		array('labels' => array( 'name' => __( 'Products' ), 'singular_name' => __( 'Product' )),
		  'public' => true, 'has_archive' => true, 'menu_position' => 5, 'supports' => array('title','editor','author','thumbnail'))
	  );
	  
	  register_post_type( 'cwt_jobs',
		array('labels' => array( 'name' => __( 'Jobs' ), 'singular_name' => __( 'Job' )),
		  'public' => true, 'has_archive' => true, 'menu_position' => 5, 'supports' => array('title','editor','author'))
	  );
	  
	  register_post_type( 'cwt_adverts',
		array('labels' => array( 'name' => __( 'Adverts' ), 'singular_name' => __( 'Advert' )),
		  'public' => true, 'has_archive' => true, 'menu_position' => 5, 'supports' => array('title','editor','author','thumbnail'))
	  );
	}
	   
    // Return data to display
    function user_data_columns( $val, $column_name, $user_id ) {
        $user = get_userdata( $user_id );
        
        switch ($column_name) {
            case 'registered-date' : return $user->user_registered;
            case 'id' : return $user->ID;
            default: return $return;
        }
    }
    add_filter( 'manage_users_custom_column', 'user_data_columns', 10, 3 );
    
    // == e ==
	
	// Custom Taxonomy Code
	add_action( 'init', 'build_taxonomies');
	function build_taxonomies() {
		register_taxonomy( 'cwt_carwash_types',array('cwt_carwashes'),array('hierarchical'=>true,'label'=>'Car Wash Types','query_var'=>true,'rewrite'=>true));
		register_taxonomy( 'cwt_forsale_types',array('cwt_carwashes'),array('hierarchical'=>true,'label'=>'For Sale Types','query_var'=>true,'rewrite'=>true));
		register_taxonomy( 'cwt_prodsupp_cats',array('cwt_products','cwt_suppliers'),array('hierarchical'=>true,'label'=>'Product/Supplier Categories','query_var'=>true,'rewrite'=>true));
		register_taxonomy( 'cwt_job_types',array('cwt_jobs'),array('hierarchical'=>true,'label'=>'Job Types','query_var'=>true,'rewrite'=>true));
		register_taxonomy( 'cwt_advert_types','cwt_adverts',array('hierarchical'=>true,'label'=>'Advert Types','query_var'=>true,'rewrite'=>true));
		register_taxonomy( 'cwt_advert_locations','cwt_adverts',array('hierarchical'=>true,'label'=>'Advert Locations','query_var'=>true,'rewrite'=>true));
	}

	// Filter to fix the Post Author Dropdown
	add_filter('wp_dropdown_users', 'theme_post_author_override');
	function theme_post_author_override($output) {
		global $post, $user_ID;
	  // return if this isn't the theme author override dropdown
	  if (!preg_match('/post_author_override/', $output)) return $output;

	  // return if we've already replaced the list (end recursion)
	  if (preg_match ('/post_author_override_replaced/', $output)) return $output;

	  // replacement call to wp_dropdown_users
		$output = wp_dropdown_users(array(
		  'echo' => 0,
			'name' => 'post_author_override_replaced',
			'selected' => empty($post->ID) ? $user_ID : $post->post_author,
			'include_selected' => true
		));

		// put the original name back
		$output = preg_replace('/post_author_override_replaced/', 'post_author_override', $output);

	  return $output;
	}
	
	
	//Theme Menus
	function register_my_menus() {
	  register_nav_menus(
		array(
		  'header-nav' => __( 'Header Navigation' ),
		  'footer-nav' => __( 'Footer Navigation' )
		)
	  );
	}
	add_action( 'init', 'register_my_menus' );
	
	
	function acf_set_featured_image( $value, $post_id, $field  ){
		
		if($value != ''){
			//Add the value which is the image ID to the _thumbnail_id meta data for the current post
			add_post_meta($post_id, '_thumbnail_id', $value);
		}
	 
		return $value;
	}
	
	// acf/update_value/name={$field_name} - filter for a specific field based on it's name
	add_filter('acf/update_value/name=carwash_image', 'acf_set_featured_image', 10, 3);
	
	
	
	/**
	* Updates post meta for a post. It also automatically deletes or adds the value to field_name if specified
	*
	* @access     protected
	* @param      integer     The post ID for the post we're updating
	* @param      string      The field we're updating/adding/deleting
	* @param      string      [Optional] The value to update/add for field_name. If left blank, data will be deleted.
	* @return     void
	*/
	function __update_post_meta( $post_id, $field_name, $value = '' ) {
		if ( empty( $value ) OR ! $value )
		{
			delete_post_meta( $post_id, $field_name );
		}
		elseif ( ! get_post_meta( $post_id, $field_name ) )
		{
			add_post_meta( $post_id, $field_name, $value );
		}
		else
		{
			update_post_meta( $post_id, $field_name, $value );
		}
	}
	
	/**
	* Updates post meta for a post. It also automatically deletes or adds the value to field_name if specified
	*
	* @access     protected
	* @param      integer     The user ID for the user we're updating
	* @param      string      The field we're updating/adding/deleting
	* @param      string      [Optional] The value to update/add for field_name. If left blank, data will be deleted.
	* @return     void
	*/
	function __update_user_meta( $user_id, $field_name, $value = '' ) {
		if ( empty( $value ) OR ! $value )
		{
			delete_user_meta( $user_id, $field_name );
		}
		elseif ( ! get_user_meta( $user_id, $field_name ) )
		{
			add_user_meta( $user_id, $field_name, $value );
		}
		else
		{
			update_user_meta( $user_id, $field_name, $value );
		}
	}
	
	
	function insert_attachment($file_id,$post_id,$featuredImage) {
        require_once(ABSPATH . "wp-admin" . '/includes/image.php');
        require_once(ABSPATH . "wp-admin" . '/includes/file.php');
        require_once(ABSPATH . "wp-admin" . '/includes/media.php');
        $attach_id = media_handle_upload( $file_id, $post_id );
        if (is_int($attach_id)&&($featuredImage)) update_post_meta($post_id,'_thumbnail_id',$attach_id);
        return $attach_id;
    }
 
	function get_all_thumbnails() {
		global $post;
		$args = array(
		'order'          => 'ASC',
		'orderby'        => 'menu_order',
		'post_type'      => 'attachment',
		'post_parent'    => $post->ID,
		'post_mime_type' => 'image',
		'post_status'    => null,
		'numberposts'    => -1,
		);
		$attachments = get_posts($args);
		$i = 0;
			if ($attachments) {
				foreach ($attachments as $attachment) {
				echo wp_get_attachment_link($attachment->ID, 'medium', false, false);
				$i = $i + 1;
				}
			}
		if ($i != 0) echo '<div style="clear: both;"><small>' . $i . ' pictures</small></div>';
	 
	}
	
	
	function getDistance($src) {
		if($src < 1)
			$src = round($src, 1);
		else
			$src = round($src);
		
		return $src.' miles away';
	}
	
	function sort_listings_by_distance($a, $b) {
		if($a->distance == $b->distance){ 
			return 0 ; 
		}
		return ($a->distance < $b->distance) ? -1 : 1;
	}
	
	function outputCarwashListing($listingID,$title,$description,$address,$postcode,$geocode,$contact,$website,$openinghours,$prices,$distance=0,$doDistance='false',$isMYCWT='false') 	{
		global $wpdb, $current_user;
		$title = trim($title);
		$address = trim($address);
		$postcode = trim($postcode);

		$listing = '';
		$listing .= '<div class="listing">';
			$listing .= '<div class="listingInner ';

			if(get_field('sale_for_sale', $listingID) == 'yes' && $isMYCWT == 'true') $listing .= 'listing-forsale';
			else $listing .= 'listing-carwashes';

			if(isset($_GET["showID"])) $listing .= ' expanded';
			$listing .= '">';
				$listing .= '<div class="listing-photo">';
					$listing .= '<div class="listing-photoInner';
					$featuredImage = wp_get_attachment_image_src(get_post_thumbnail_id($listingID), 'large');
					if($featuredImage != '') $listing .= ' listing-photo-present';
					$listing .= '">';
					$listing .= '<a href="'.$featuredImage[0].'" data-lightbox="listing'.$listingID.'">';
					$listing .= get_the_post_thumbnail($listingID, array(70,70), array('style' => 'background:white'));
					$listing .= '</a></div>';
					$listing .= '<div class="listingHidden">';
			
					$attachments = get_posts( array(
						'post_type' => 'attachment',
						'posts_per_page' => -1,
						'post_parent' => $listingID
					) );

					$featured = get_field('_thumbnail_id', $listingID );
			
					if ( sizeof($attachments) > 1 ) {
						$listing .= '1 of '.sizeof($attachments);
						$cover = true;
						foreach ( $attachments as $attachment ) {
							if($attachment->ID != $featured) {
								$thumbImg = wp_get_attachment_image_src($attachment->ID, 'large');
								$listing .= '<p><a ';
								if($cover) $listing .= 'class="galleryLink" ';
								else $listing .= 'class="galleryHidden" ';
								$listing .= 'href="'.$thumbImg[0].'" data-lightbox="listing'.$listingID.'">Image gallery</a></p>';
								$cover = false;
							}
						}
					}
					
					$listing .= '<div class="mapThumb" id="map_canvas'.$listingID.'"></div>';
					$listing .= '<div class="mapLoc">'.$geocode.'</div>';
					$latlng = explode(',', $geocode);
					$listing .= '<p><a class="mapLink" href="" onclick="showLocation(\''.$latlng[0].'\',\''.$latlng[1].'\');return false;">View map</a></p>';
					
					$listing .= '</div>';
				$listing .= '</div>';
				
				$listing .= '<div class="listingInfo">';
					$listing .= '<h3>'.$title.'</h3>';

					//ratings
					if($isMYCWT == 'false') {

						$rating = 'SELECT * FROM cwt_cbratingsystem_ratings_summary WHERE post_id = '.$listingID;
						$rating = $wpdb->get_results($rating);

						$numRatings = ($rating[0]->per_post_rating_count != '') ? $rating[0]->per_post_rating_count : 0;
						$reviewData = $rating[0]->per_post_rating_summary;
						$reviewData = $reviewData / 100 * 5;

						if($reviewData != 0)
							$ratings = round($reviewData);
						else $ratings = 0;
						$listing .= '<div class="listingReview"><a href="/reviews/?id='.$listingID.'">';
						$listing .= $numRatings.' reviews ';
						for($r=1; $r<=5; $r++) {
							if($r <= $ratings) $listing .= '&#9733;';
							else $listing .= '&#9734;';
						}
						$listing .= '<img class="leaveReview" src="assets/images/icon-leaveReview.svg" alt="Leave Review" />';
						$listing .= '</a></div>';
					}
					$listing .= '<div class="clear"></div>';

					$listing .= '<div class="listingInfoDetailsLeft">';
				
						if($address != '') {
							$listing .= '<div class="listingSection">'.$address;
							if($postcode != '')
								$listing .= ' '.$postcode;
							if($doDistance == 'true')
								$listing .= '<br /><br />'.getDistance($distance);
							$listing .= '</div>';
						}
				
						if($openinghours != '') {
							$listing .= '<div class="listingHidden">';
							$listing .= '<div class="listingSection">';
							$listing .= '<b>Opening Hours</b> <br />'.html_entity_decode($openinghours).'</div></div>';
						}
				
					$listing .= '</div>';
					$listing .= '<div class="listingInfoDetailsRight">';

						if($description != '') {
							$listing .= '<div class="listingSection">';
								$listing .= '<b>Company Information</b><br />';
					
								$listing .= '<div class="listingShown">';
								if(strlen(strip_tags($description)) > 120)
									$listing .= substr(strip_tags(html_entity_decode($description)), 0, 120).'...';  
								else
									$listing .= strip_tags($description);
								$listing .= '</div>';
					
								$listing .= '<div class="listingHidden">'.html_entity_decode($description).'</div>';
							$listing .= '</div>';
						}
				
						$listing .= '<div class="listingHidden">';
							$listing .= '<div class="listingSection">';
								if($prices != '' ) {
									$listing .= '<b>Prices</b> <br />';
									$listing .= html_entity_decode($prices);
								}
							$listing .= '</div>';
						$listing .= '</div>';
					$listing .= '</div>';

					$listing .= '<div class="clear"></div>';
				$listing .= '</div>';
				
				$listing .= '<div class="clear"></div>';
				
				if(!isset($_GET["showID"]))
					$listing .= '<div class="listingInfoReadMore">Read More</div>';
				
			$listing .= '</div>';
			
			if($contact != '' || $website != '' || $current_user->roles[0] == 'administrator') {
				$listing .= '<div class="listingContact">';
					if($contact != '') $listing .= 'Tel: <b>'.$contact.'</b>';
					else $listing .= '&nbsp;';

					if($current_user->roles[0] == 'administrator') { 
						$listing .= '<div class="listingAdmin">';
						$listing .= '<a href="/wp-admin/post.php?post='.$listingID.'&action=edit" target="_blank">Edit as Admin</a></div>'; 
					}

					if($website != '') {
						$listing .= '<div class="listingWebsite">';
						if(substr($website, 0, 4) != 'http')
							$website = 'http://'.$website;
						$listing .= '<a href="'.$website.'" title="Visit '.get_the_title().' website" target="_blank">';
						$listing .= 'Visit Website</a></div>';
					}
				$listing .= '</div>';
			}
		$listing .= '</div>	';
		
		return $listing;
	}
	
	function outputSupplierListing($listingID,$title,$description,$address,$postcode,$geocode,$contact,$website,$openinghours,$prices,$distance=0,$doDistance='false') 	{
		global $current_user;
		$title = trim($title);
		$address = trim($address);
		$postcode = trim($postcode);

		$listing = '';
		$listing .= '<div class="listing">';
			$listing .= '<div class="listingInner listing-suppliers';
			if(isset($_GET["showID"])) $listing .= ' expanded';
			$listing .= '">';
				$listing .= '<div class="listing-photo">';
					$listing .= '<div class="listing-photoInner">';
					$featuredImage = wp_get_attachment_image_src(get_post_thumbnail_id($listingID), 'large');
					$listing .= '<a href="'.$featuredImage[0].'" data-lightbox="listing'.$listingID.'">';
					$listing .= get_the_post_thumbnail($listingID, array(70,70), array('style' => 'background:white'));
					$listing .= '</a></div>';
					$listing .= '<div class="listingHidden">';
			
					$attachments = get_posts( array(
						'post_type' => 'attachment',
						'posts_per_page' => -1,
						'post_parent' => $listingID
					) );
					
					$listing .= '<div class="mapThumb" id="map_canvas'.$listingID.'"></div>';
					$listing .= '<div class="mapLoc">'.$geocode.'</div>';
					$latlng = explode(',', $geocode);
					$listing .= '<p><a class="mapLink" href="" onclick="showLocation(\''.$latlng[0].'\',\''.$latlng[1].'\');return false;">View map</a></p>';
					
					$listing .= '</div>';
				$listing .= '</div>';
				
				$listing .= '<div class="listingInfo">';
					$listing .= '<h3>'.$title.'</h3>';
					$listing .= '<div class="listingInfoDetailsLeft">';
				
						if($address != '') {
							$listing .= '<div class="listingSection">'.$address;
							if($postcode != '')
								$listing .= ' '.$postcode;
							if($doDistance == 'true')
								$listing .= '<br /><br />'.getDistance($distance);
							$listing .= '</div>';
						}
				
						if($openinghours != '') {
							$listing .= '<div class="listingHidden">';
							$listing .= '<div class="listingSection">';
							$listing .= '<b>Opening Hours</b> <br />'.html_entity_decode($openinghours).'</div></div>';
						}
				
					$listing .= '</div>';
					$listing .= '<div class="listingInfoDetailsRight">';
						$listing .= '<div class="listingSection">';
							$listing .= '<b>Company Information</b><br />';
				
							$listing .= '<div class="listingShown">';
							if(strlen(strip_tags($description)) > 120)
								$listing .= substr(strip_tags(html_entity_decode($description)), 0, 120).'...';  
							else
								$listing .= html_entity_decode($description);
							$listing .= '</div>';
				
							$listing .= '<div class="listingHidden">'.html_entity_decode($description).'</div>';
						$listing .= '</div>';
				
						$listing .= '<div class="listingHidden">';
							$listing .= '<div class="listingSection">';
								if($prices != '' ) {
									$listing .= '<b>Prices</b> <br />';
									$listing .= html_entity_decode($prices);
								}
							$listing .= '</div>';


							$terms = wp_get_post_terms( $listingID, 'cwt_prodsupp_cats', array("fields" => "all", "orderby" => 'term_order'));
							if(sizeof($terms) > 0) {
								$listing .= '<ul>';
								for($t=0; $t<sizeof($terms); $t++) {
									$offset = 0;
									$ancestors = get_ancestors( $terms[$t]->term_id, 'cwt_prodsupp_cats' );
									if(sizeof($ancestors) > 0)
										$offset = sizeof($ancestors) * 25;

									$listing .= '<li style="padding-left:'.$offset.'px">'.$terms[$t]->name.'</li>';
								}
								echo '</ul>';
							}

						$listing .= '</div>';
					$listing .= '</div>';
				$listing .= '</div>';
				
				$listing .= '<div class="clear"></div>';
				
				if(!isset($_GET["showID"]))
					$listing .= '<div class="listingInfoReadMore">Read More</div>';
				
			$listing .= '</div>';
			
			if($contact != '' || $website != '' || $current_user->roles[0] == 'administrator') {
				$listing .= '<div class="listingContact">';
					if($contact != '') $listing .= 'Tel: <b>'.$contact.'</b>';
					else $listing .= '&nbsp;';

					if($current_user->roles[0] == 'administrator') { 
						$listing .= '<div class="listingAdmin">';
						$listing .= '<a href="/wp-admin/post.php?post='.$listingID.'&action=edit" target="_blank">Edit as Admin</a></div>'; 
					}

					if($website != '') {
						$listing .= '<div class="listingWebsite">';
						if(substr($website, 0, 4) != 'http')
							$website = 'http://'.$website;
						$listing .= '<a href="'.$website.'" title="Visit '.get_the_title().' website" target="_blank">';
						$listing .= 'Visit Website</a></div>';
					}
				$listing .= '</div>';
			}
		$listing .= '</div>	';
		
		return $listing;
	}
	
	function outputProductListing($listingID,$title,$description,$contact,$link,$price,$supplier=0) {
		global $current_user;
		$listing = '';
		$listing .= '<div class="listing">';
			$listing .= '<div class="listingInner listing-products';
			if(isset($_GET["showID"])) $listing .= ' expanded';
			$listing .= '">';
				$listing .= '<div class="listing-photo">';
					$listing .= '<div class="listing-photoInner">';
					$featuredImage = wp_get_attachment_image_src(get_post_thumbnail_id($listingID), 'large');
					$listing .= '<a href="'.$featuredImage[0].'" data-lightbox="listing'.$listingID.'">';
					$listing .= get_the_post_thumbnail($listingID, array(70,70), array('style' => 'background:white'));
					$listing .= '</a></div>';
					$listing .= '<div class="listingHidden">';
					if($supplier != 0)
						$listing .= '<p><a href="/suppliers/?showID='.$supplier.'#showListing" title="View Supplier">View Supplier</a></p>';
					$listing .= '</div>';
				$listing .= '</div>';
				
				$listing .= '<div class="listingInfo">';
					$listing .= '<h3>'.$title.'</h3>';
					$listing .= '<div class="listingInfoDetailsLeft">';
				
						$listing .= '<div class="listingSection">';
						if($price != '') {
							$listing .= '<b>Price</b> '.$price;
						}
						$listing .= '</div>';
				
						if($contact != '') {
							$listing .= '<div class="listingSection"><b>How to Buy</b><br />';
							$listing .= $contact;
							$listing .= '</div>';
						}
				
					$listing .= '</div>';
					$listing .= '<div class="listingInfoDetailsRight">';
						$listing .= '<div class="listingSection">';
							$listing .= '<b>Product Information</b><br />';
							$listing .= '<div class="listingShown">';
							if(strlen(strip_tags($description)) > 120)
								$listing .= substr(strip_tags(html_entity_decode($description)), 0, 120).'...';  
							else
								$listing .= html_entity_decode($description);
							$listing .= '</div>';
				
							$listing .= '<div class="listingHidden">'.html_entity_decode($description).'</div>';
						$listing .= '</div>';
				
						$listing .= '<div class="listingHidden">';
							$terms = wp_get_post_terms( $listingID, 'cwt_prodsupp_cats', array("fields" => "all", "orderby" => 'term_order'));
							if(sizeof($terms) > 0) {
								$listing .= '<ul>';
								for($t=0; $t<sizeof($terms); $t++) {
									$offset = 0;
									$ancestors = get_ancestors( $terms[$t]->term_id, 'cwt_prodsupp_cats' );
									if(sizeof($ancestors) > 0)
										$offset = sizeof($ancestors) * 25;

									$listing .= '<li style="padding-left:'.$offset.'px">'.$terms[$t]->name.'</li>';
								}
								echo '</ul>';
							}

						$listing .= '</div>';
					$listing .= '</div>';
				$listing .= '</div>';
				
				$listing .= '<div class="clear"></div>';
				
				if(!isset($_GET["showID"]))
					$listing .= '<div class="listingInfoReadMore">Read More</div>';
				
			$listing .= '</div>';
			
			if($link != '' || $current_user->roles[0] == 'administrator') {
				$listing .= '<div class="listingContact">';
					$listing .= '&nbsp;';

					if($current_user->roles[0] == 'administrator') { 
						$listing .= '<div class="listingAdmin">';
						$listing .= '<a href="/wp-admin/post.php?post='.$listingID.'&action=edit" target="_blank">Edit as Admin</a></div>'; 
					}

					$listing .= '<div class="listingWebsite">';
					if(substr($link, 0, 4) != 'http')
						$link = 'http://'.$link;
					$listing .= '<a href="'.$link.'" title="View '.get_the_title().'" target="_blank">';
					$listing .= 'View info</a></div>';
				$listing .= '</div>';
			}
		$listing .= '</div>	';
		
		return $listing;
	}
	
	function outputCarwashForSaleListing($advert_title,$listing_info,$business_size,$price,$leasehold_freehold,$leasehold_years,$purchase_link,$listingID,$title,$description,$address,$postcode,$geocode,$contact,$website,$openinghours,$prices,$distance=0,$doDistance='false') 	{
		global $wpdb, $current_user;
		$advert_title = trim($advert_title);
		$title = trim($title);
		$address = trim($address);
		$postcode = trim($postcode);

		$listing = '';
		$listing .= '<div class="listing">';
			$listing .= '<div class="listingInner listing-forsale';
			if(isset($_GET["showID"])) $listing .= ' expanded';
			$listing .= '">';
				$listing .= '<div class="listing-photo">';
					$listing .= '<div class="listing-photoInner">';
					$featuredImage = wp_get_attachment_image_src(get_post_thumbnail_id($listingID), 'large');
					$listing .= '<a href="'.$featuredImage[0].'" data-lightbox="listing'.$listingID.'">';
					$listing .= get_the_post_thumbnail($listingID, array(70,70));
					$listing .= '</a></div>';
					$listing .= '<div class="listingHidden">';
			
					$attachments = get_posts( array(
						'post_type' => 'attachment',
						'posts_per_page' => -1,
						'post_parent' => $listingID
					) );
					
					$featured = get_field('_thumbnail_id', $listingID );

					if ( sizeof($attachments) > 1 ) {
						$listing .= '1 of '.sizeof($attachments);
						$cover = true;
						foreach ( $attachments as $attachment ) {
							if($attachment->ID != $featured) {
								$thumbImg = wp_get_attachment_image_src($attachment->ID, 'large');
								$listing .= '<p><a ';
								if($cover) $listing .= 'class="galleryLink" ';
								else $listing .= 'class="galleryHidden" ';
								$listing .= 'href="'.$thumbImg[0].'" data-lightbox="listing'.$listingID.'">Image gallery</a></p>';
								$cover = false;
							}
						}
					}
					
					$listing .= '<div class="mapThumb" id="map_canvas'.$listingID.'"></div>';
					$listing .= '<div class="mapLoc">'.$geocode.'</div>';
					$latlng = explode(',', $geocode);
					$listing .= '<p><a class="mapLink" href="" onclick="showLocation(\''.$latlng[0].'\',\''.$latlng[1].'\');return false;">View map</a></p>';
					
					
					$listing .= '</div>';
				$listing .= '</div>';
				
				$listing .= '<div class="listingInfo">';
					$listing .= '<h3>'.$advert_title.'</h3>';
					$listing .= '<div class="listingInfoDetailsLeft">';
				
						if($price != '') {
							$listing .= '<div class="listingSection clearbreaks"><b>Price</b> <br />'.html_entity_decode($price).'</div>';
						}
				
						if($leasehold_freehold != '') {
							$listing .= '<div class="listingSection clearbreaks"><b>Leasehold/Freehold</b> <br />';
							switch($leasehold_freehold) {
								case 'lease': $listing .= 'Leasehold <br />('.html_entity_decode($leasehold_years).' years remaining)'; break;
								case 'free': $listing .= 'Freehold'; break;
							}
							$listing .= '</div>';
						}
				
						if($business_size != '') {
							$listing .= '<div class="listingSection clearbreaks"><div class="listingHidden"><b>Business Size</b> ';
							$listing .= html_entity_decode($business_size).' employees';
							$listing .= '</div></div>';
						}
				
					$listing .= '</div>';
					$listing .= '<div class="listingInfoDetailsRight">';
						$listing .= '<div class="listingSection">';
							$listing .= '<div class="listingShown">';
							if(strlen(strip_tags($listing_info)) > 120)
								$listing .= substr(strip_tags(html_entity_decode($listing_info)), 0, 120).'...';  
							else
								$listing .= html_entity_decode($listing_info);
							$listing .= '</div>';
				
							$listing .= '<div class="listingHidden">'.html_entity_decode($listing_info).'</div>';
						$listing .= '</div>';
				
						$listing .= '<div class="listingHidden">';

							$listing .= '<div class="listingSection">';
								$listing .= '<h4>'.$title.'</h4>';
							$listing .= '</div>';

							//ratings
							$rating = 'SELECT * FROM cwt_cbratingsystem_ratings_summary WHERE post_id = '.$listingID;
							$rating = $wpdb->get_results($rating);

							$numRatings = ($rating[0]->per_post_rating_count != '') ? $rating[0]->per_post_rating_count : 0;
							$reviewData = $rating[0]->per_post_rating_summary;
							$reviewData = $reviewData / 100 * 5;

							if($reviewData != 0)
								$ratings = round($reviewData);
							else $ratings = 0;
							$listing .= '<div class="listingReview forsale"><a href="/reviews/?id='.$listingID.'">';
							$listing .= $numRatings.' reviews ';
							for($r=1; $r<=5; $r++) {
								if($r <= $ratings) $listing .= '&#9733;';
								else $listing .= '&#9734;';
							}
							$listing .= '</a></div>';

							$listing .= '<div class="clear"></div>';

							if($address != '') {
								$listing .= '<div class="listingSection">'.$address;
								if($postcode != '')
									$listing .= ' '.$postcode;
								if($doDistance == 'true')
									$listing .= '<br /><br />'.getDistance($distance);
								$listing .= '</div>';
							}

							if($openinghours != '') {
								$listing .= '<div class="listingSection">';
								$listing .= '<b>Opening Hours</b> <br />'.html_entity_decode($openinghours).'</div>';
							}

							$listing .= '<div class="listingSection">';
							$listing .= '<b>Company Information</b><br />';
							$listing .= html_entity_decode($description);
							$listing .= '</div>';

							if($prices != '' ) {
								$listing .= '<b>Prices</b> <br />';
								$listing .= html_entity_decode($prices);
							}

							if($website != '') {
								$listing .= '<div class="listingSection">';
								if(substr($website, 0, 4) != 'http')
									$website = 'http://'.$website;
								$listing .= '<a style="text-decoration:underline;" href="'.$website.'" title="Visit '.get_the_title().' website" target="_blank">';
								$listing .= 'Visit Website</a></div>';
								}


						$listing .= '</div>';
					$listing .= '</div>';
				$listing .= '</div>';
				
				$listing .= '<div class="clear"></div>';
				
				if(!isset($_GET["showID"]))
					$listing .= '<div class="listingInfoReadMore">Read More</div>';
				
			$listing .= '</div>';
			
			if($contact != '' || $purchase_link != '' || $current_user->roles[0] == 'administrator') {
				$listing .= '<div class="listingContact">';
					if($contact != '') $listing .= 'Tel: <b>'.$contact.'</b>';
					else $listing .= '&nbsp;';

					if($current_user->roles[0] == 'administrator') { 
						$listing .= '<div class="listingAdmin">';
						$listing .= '<a href="/wp-admin/post.php?post='.$listingID.'&action=edit" target="_blank">Edit as Admin</a></div>'; 
					}

					if($purchase_link != '') {
						$listing .= '<div class="listingWebsite">';
						if(substr($purchase_link, 0, 4) != 'http')
							$purchase_link = 'http://'.$purchase_link;
						$listing .= '<a href="'.$purchase_link.'" title="Visit '.get_the_title().' website" target="_blank">';
						$listing .= 'More details</a></div>';
					}
				$listing .= '</div>';
			}
		$listing .= '</div>	';
		
		return $listing;
	}
	
	function outputJobListing($listingID,$title,$description,$address,$postcode,$geocode,$hours,$salary,$apply,$distance=0,$doDistance='false') 	{
		
		global $current_user;
		$title = trim($title);
		$address = trim($address);
		$postcode = trim($postcode);

		$listing = '';
		$listing .= '<div class="listing">';
			$listing .= '<div class="listingInner listing-jobs';
			if(isset($_GET["showID"])) $listing .= ' expanded';
			$listing .= '">';
				$listing .= '<div class="listing-photo">';
					$listing .= '<div class="listingHidden">';
			
					$listing .= '<div class="mapThumb" id="map_canvas'.$listingID.'"></div>';
					$listing .= '<div class="mapLoc">'.$geocode.'</div>';
					$latlng = explode(',', $geocode);
					$listing .= '<p><a class="mapLink" href="" onclick="showLocation(\''.$latlng[0].'\',\''.$latlng[1].'\');return false;">View map</a></p>';
					
					$listing .= '</div>';
				$listing .= '</div>';
				
				$listing .= '<div class="listingInfo jobListing">';
					$listing .= '<h3>'.$title.'</h3>';
					$listing .= '<div class="listingInfoDetailsLeft">';
				
						if($address != '') {
							$listing .= '<div class="listingSection">'.$address;
							if($postcode != '')
								$listing .= ' '.$postcode;
							if($doDistance == 'true')
								$listing .= '<br /><br />'.getDistance($distance);
							$listing .= '</div>';
						}
				
						if($salary != '') {
							$listing .= '<div class="listingSection">';
							$listing .= '<b>Salary</b><br />'.html_entity_decode($salary).'</div>';
						}
				
						if($hours != '') {
							$listing .= '<div class="listingHidden">';
							$listing .= '<div class="listingSection">';
							$listing .= '<b>Hours</b><br />'.html_entity_decode($hours).'</div></div>';
						}
				
					$listing .= '</div>';
					$listing .= '<div class="listingInfoDetailsRight">';
						$listing .= '<div class="listingSection">';
							$listing .= '<b>Job Description</b><br />';
				
							$listing .= '<div class="listingShown">';
							if(strlen(strip_tags($listing_info)) > 120)
								$listing .= substr(strip_tags(html_entity_decode($listing_info)), 0, 120).'...';  
							else
								$listing .= html_entity_decode($description);
							$listing .= '</div>';
				
							$listing .= '<div class="listingHidden">'.html_entity_decode($description).'</div>';
						$listing .= '</div>';
				
						$listing .= '<div class="listingHidden">';
							$listing .= '<div class="listingSection">';
								if($apply != '' ) {
									$listing .= '<b>How to apply</b><br />';
									$listing .= html_entity_decode($apply);
								}
							$listing .= '</div>';
						$listing .= '</div>';
					$listing .= '</div>';
				$listing .= '</div>';
				
				$listing .= '<div class="clear"></div>';
				
				if(!isset($_GET["showID"]))
					$listing .= '<div class="listingInfoReadMore">Read More</div>';
				
			$listing .= '</div>';

			if($current_user->roles[0] == 'administrator') {
				$listing .= '<div class="listingContact">';
				$listing .= '<div class="listingAdmin">';
				$listing .= '<a href="/wp-admin/post.php?post='.$listingID.'&action=edit" target="_blank">Edit as Admin</a></div>'; 
				$listing .= '</div>';
			}
		$listing .= '</div>	';
		
		return $listing;
	}


	function outputPrice($src) {
		$price = $src;
		$price = number_format($price, 2);
		$price = '&pound;'.$price;

		return $price;
	}


	function outputAdverts($pageIdentifier, $type, $num = 1, $offset = 0) {

		$advertList = '';
		$advertType = get_term_by('slug', $type, 'cwt_advert_types');
		$advertLocation = get_term_by('slug', $pageIdentifier, 'cwt_advert_locations');

		$adQuery = array('post_type' => 'cwt_adverts',
							'posts_per_page' => $num,
							'paged' => $offset);

		$adQuery['tax_query'] = array(
			array(
				'taxonomy' => 'cwt_advert_types',
				'field' => 'id',
				'terms' => $advertType->term_id
			),
			array(
				'taxonomy' => 'cwt_advert_locations',
				'field' => 'id',
				'terms' => $advertLocation->term_id
			)
		);

		remove_all_filters('posts_orderby');//prevent plugin clashing with custom ordering
		$adverts = query_posts($adQuery);

		if(sizeof($adverts) > 0) {
			$advertList .= '<div class="adverts">';
			for($r=0; $r<sizeof($adverts); $r++) {
				$advertList .= '<div class="advert">';
				$advertLink = get_field('advert_link', $adverts[$r]->ID);
				if(substr($advertLink, 0, 4) != 'http')
					$advertLink = 'http://'.$advertLink;
				$advertList .= '<a href="'.$advertLink.'" title="'.$adverts[$r]->post_title.'" target="_blank">';
				$advertList .= get_the_post_thumbnail($adverts[$r]->ID);
				$advertList .= '</a>';


				//$advertList .= '<img src="assets/images/adspace-skyscraper.gif" />';
				$advertList .= '</div>';
			}
			if($r < $num) {
				for($c=$r; $c<$num; $c++) {
					$advertList .= '<div class="advert"><a href="/book-advert/" title="Book advert">';
					$advertList .= '<img src="assets/images/adspace-inlisting.gif" /></a></div>';
				}
			}
			$advertList .= '</div>';
		} else {
			if($type == 'side-bar') {
				$advertList = '<div class="advert"><a href="/book-advert/" title="Book advert">';
				$advertList .= '<img src="assets/images/adspace-skyscraper.gif" /></a></div>';
			}
			else if($type == 'in-listing') {
				$advertList .= '<div class="adverts">';
				$advertList .= '<div class="advert"><a href="/book-advert/" title="Book advert">';
				$advertList .= '<img src="assets/images/adspace-inlisting.gif" /></a></div>';
				$advertList .= '<div class="advert"><a href="/book-advert/" title="Book advert">';
				$advertList .= '<img src="assets/images/adspace-inlisting.gif" /></a></div>';
				$advertList .= '<div class="advert"><a href="/book-advert/" title="Book advert">';
				$advertList .= '<img src="assets/images/adspace-inlisting.gif" /></a></div>';
				$advertList .= '</div>';
			}
		}

		return $advertList;
	}


/**
 * Check term against custom cwt_email_prefs db table
 */
function checkEmailPref($user_id, $term_id) {
	global $wpdb;
	$check = $wpdb->get_results('SELECT COUNT(id) AS num FROM cwt_email_prefs WHERE user_id = '.$user_id.' AND term_id = '.$term_id, OBJECT);
	
	if($check[0]->num > 0)
		$return = true;
	else $return = false;

	return $return;
}


/* Add section to the edit user page in the admin to select email prefs. */
add_action( 'show_user_profile', 'my_edit_user_emailprefs_section' );
add_action( 'edit_user_profile', 'my_edit_user_emailprefs_section' );

	/**
 * Adds an additional settings section on the edit user/profile page in the admin.  This section allows users to 
 * select an alert from a checkbox of terms from the email prefs taxonomy.
 * @param object $user The user object currently being edited.
 */
function my_edit_user_emailprefs_section( $user ) {

	$tax = get_taxonomy( 'cwt_carwash_types' );

	/* Make sure the user can assign terms of the carwashes taxonomy before proceeding. */
	if ( !current_user_can( $tax->cap->assign_terms ) )
		return;

	/* Get the terms of the 'carwashes' taxonomy. */
	$termsCarwash = get_terms( 'cwt_carwash_types', array( 'hide_empty' => false ) ); 
	$termsForSale = get_terms( 'cwt_forsale_types', array( 'hide_empty' => false ) ); 
	$termsProdSupp = get_terms( 'cwt_prodsupp_cats', array( 'hide_empty' => false, 'parent' => 0 ) ); 
	$termsJobs = get_terms( 'cwt_job_types', array( 'hide_empty' => false ) ); 
	$termsEmailPrefs = get_terms( 'cwt_prefs_carwashes', array( 'hide_empty' => false ) ); 
	?>

	<h3><?php _e( 'Email Alert Settings' ); ?></h3>

	<table class="form-table" cellpadding="0" cellspacing="0">
		<tr>
			<th><label for="carwashes"><?php _e( 'Car Washes' ); ?></label></th>
			<td style="vertical-align:top"><?php
			/* If there are any carwashes terms, loop through them and display checkboxes. */
			if ( !empty( $termsCarwash ) ) {

				foreach ( $termsCarwash as $term ) { ?>
					<input type="checkbox" name="carwashes[]" id="carwashes-<?php echo esc_attr( $term->slug ); ?>" value="<?php echo esc_attr( $term->term_id ); ?>" 
					<?php if(checkEmailPref( $user->ID, $term->term_id )) echo 'checked="checked"'; ?> /> 
					<label for="carwashes-<?php echo esc_attr( $term->slug ); ?>">
					<?php echo $term->name; ?></label> <br />
				<?php }
			}
			/* If there are no carwashes terms, display a message. */
			else {
				_e( 'There are no car wash types available.' );
			}
			?></td>
			<td rowspan="3" style="vertical-align:top">
				<table cellpadding="0" cellspacing="0">
				<tr>
					<th style="padding:0;"><label for="prodsupp"><?php _e( 'Products/Suppliers' ); ?></label></th>
					<td style="padding:0;"><?php
					/* If there are any prodsupp terms, loop through them and display checkboxes. */
					if ( !empty( $termsProdSupp ) ) {

						foreach ( $termsProdSupp as $term ) { ?>
							<input type="checkbox" name="prodsupp[]" id="prodsupp-<?php echo esc_attr( $term->slug ); ?>" value="<?php echo esc_attr( $term->term_id ); ?>" 
							<?php if(checkEmailPref( $user->ID, $term->term_id )) echo 'checked="checked"'; ?> /> 
							<label for="prodsupp-<?php echo esc_attr( $term->slug ); ?>">
							<?php echo $term->name; ?></label> <br />

							<?php
							$termsSubProdSupp = get_terms( 'cwt_prodsupp_cats', array( 'hide_empty' => false, 'parent' => $term->term_id ) ); 
							if ( !empty( $termsSubProdSupp ) ) {

								foreach ( $termsSubProdSupp as $termSub ) { ?>
									<input style="margin-left:20px;" type="checkbox" name="prodsupp[]" id="prodsupp-<?php echo esc_attr( $termSub->slug ); ?>" value="<?php echo esc_attr( $termSub->term_id ); ?>" 
									<?php if(checkEmailPref( $user->ID, $termSub->term_id )) echo 'checked="checked"'; ?> /> 
									<label for="prodsupp-<?php echo esc_attr( $termSub->slug ); ?>">
									<?php echo $termSub->name; ?></label> <br />
								<?php }
							}
							?>
						<?php }
					}
					/* If there are no carwashes terms, display a message. */
					else {
						_e( 'There are no product/supplier categories available.' );
					}
					?></td>
				</tr>
				</table>
			</td>
		</tr>
		<tr>
			<th><label for="forsale"><?php _e( 'Car Washes For Sale' ); ?></label></th>
			<td style="vertical-align:top"><?php
			/* If there are any forsale terms, loop through them and display checkboxes. */
			if ( !empty( $termsForSale ) ) {

				foreach ( $termsForSale as $term ) { ?>
					<input type="checkbox" name="forsale[]" id="forsale-<?php echo esc_attr( $term->slug ); ?>" value="<?php echo esc_attr( $term->term_id ); ?>" 
					<?php if(checkEmailPref( $user->ID, $term->term_id )) echo 'checked="checked"'; ?> /> 
					<label for="forsale-<?php echo esc_attr( $term->slug ); ?>">
					<?php echo $term->name; ?></label> <br />
				<?php }
			}
			/* If there are no carwashes terms, display a message. */
			else {
				_e( 'There are no car wash for sale types available.' );
			}
			?></td>
		</tr>
		<tr>
			<th><label for="jobs"><?php _e( 'Jobs' ); ?></label></th>
			<td style="vertical-align:top"><?php
			/* If there are any jobs terms, loop through them and display checkboxes. */
			if ( !empty( $termsJobs ) ) {

				foreach ( $termsJobs as $term ) { ?>
					<input type="checkbox" name="jobs[]" id="jobs-<?php echo esc_attr( $term->slug ); ?>" value="<?php echo esc_attr( $term->term_id ); ?>" 
					<?php if(checkEmailPref( $user->ID, $term->term_id )) echo 'checked="checked"'; ?> /> 
					<label for="jobs-<?php echo esc_attr( $term->slug ); ?>">
					<?php echo $term->name; ?></label> <br />
				<?php }
			}
			/* If there are no carwashes terms, display a message. */
			else {
				_e( 'There are no job types available.' );
			}
			?></td>
		</tr>
	</table>
<?php }

/* Update the email prefs terms when the edit user page is updated. */
add_action( 'personal_options_update', 'my_save_user_emailprefs_terms' );
add_action( 'edit_user_profile_update', 'my_save_user_emailprefs_terms' );

/**
 * Saves the term selected on the edit user/profile page in the admin. This function is triggered when the page 
 * is updated.  We just grab the posted data and use wp_set_object_terms() to save it.
 *
 * @param int $user_id The ID of the user to save the terms for.
 */
function my_save_user_emailprefs_terms( $user_id ) {
	global $wpdb;
	$tax = get_taxonomy( 'cwt_carwash_types' );

	/* Make sure the current user can edit the user and assign terms before proceeding. */
	if ( !current_user_can( 'edit_user', $user_id ) && current_user_can( $tax->cap->assign_terms ) )
		return false;

	$carwashes = $_POST['carwashes'];
	$forsale = $_POST['forsale'];
	$prodsupp = $_POST['prodsupp'];
	$jobs = $_POST['jobs'];

	/* Remove all current terms and reset where checked */
	$wpdb->query('DELETE FROM cwt_email_prefs WHERE user_id = '.$user_id);
	for($c=0; $c<sizeof($carwashes); $c++) {
		$wpdb->query('INSERT INTO cwt_email_prefs (user_id, term_id) VALUES ('.$user_id.','.$carwashes[$c].')');
	}
	for($f=0; $f<sizeof($forsale); $f++) {
		$wpdb->query('INSERT INTO cwt_email_prefs (user_id, term_id) VALUES ('.$user_id.','.$forsale[$f].')');
	}
	for($p=0; $p<sizeof($prodsupp); $p++) {
		$wpdb->query('INSERT INTO cwt_email_prefs (user_id, term_id) VALUES ('.$user_id.','.$prodsupp[$p].')');
	}
	for($j=0; $j<sizeof($jobs); $j++) {
		$wpdb->query('INSERT INTO cwt_email_prefs (user_id, term_id) VALUES ('.$user_id.','.$jobs[$j].')');
	}
}


/** 
 * Functions to add snapshots of recent activity to wordpress dashboard
 **/
function output_dashboard_posts ($post_type, $url) {

	global $post;
	$args = array( 'posts_per_page' => 5,
				'post_type' => $post_type,
				'orderby' => 'post_date',
				'order' => 'desc',
				'post_status' => 'publish');

	remove_all_filters('posts_orderby');//prevent plugin clashing with custom ordering
	$results = query_posts( $args );
	echo '<ol>';
	for($r=0; $r<sizeof($results); $r++) {
		echo '<li>('.date('d/m/Y', strtotime($results[$r]->post_date)).')&nbsp;';
		$author = get_userdata($results[$r]->post_author);
		echo '<a href="/wp-admin/post.php?post='.$results[$r]->ID.'&action=edit">';
		echo $results[$r]->post_title.'</a>&nbsp;['.$author->first_name.' '.$author->last_name.'] ';
		echo '<a href="/'.$url.'/?showID='.$results[$r]->ID.'#showListing" target="_blank">View in Site</a></li>';
	}
	echo '</ol>';
}

//Adverts
function dashboard_ad_requests() {

	global $post;
	$args = array( 'posts_per_page' => -1,
				'post_type' => 'cwt_adverts',
				'orderby' => 'post_date',
				'order' => 'desc',
				'post_status' => 'draft');

	remove_all_filters('posts_orderby');//prevent plugin clashing with custom ordering
	$results = query_posts( $args );
	echo '<ol>';
	for($r=0; $r<sizeof($results); $r++) {
		echo '<li>('.date('d/m/Y', strtotime($results[$r]->post_date)).')&nbsp;';
		$author = get_userdata($results[$r]->post_author);
		echo '<a href="/wp-admin/post.php?post='.$results[$r]->ID.'&action=edit">';
		echo $results[$r]->post_title.'</a>&nbsp;['.$author->first_name.' '.$author->last_name.']</li>';
	}
	echo '</ol>';

}

function add_dashboard_ad_requests() {
       wp_add_dashboard_widget( 'dashboard_ad_requests', __( 'Advert Applications Pending' ), 'dashboard_ad_requests' );
}
add_action('wp_dashboard_setup', 'add_dashboard_ad_requests' );

//Car Washes
function dashboard_recent_carwashes() {

	output_dashboard_posts('cwt_carwashes', 'home');

}

function add_dashboard_recent_carwashes() {
       wp_add_dashboard_widget( 'dashboard_recent_carwashes', __( 'Recent Car Washes' ), 'dashboard_recent_carwashes' );
}
add_action('wp_dashboard_setup', 'add_dashboard_recent_carwashes' );

//For Sale
function dashboard_recent_forsale() {

	global $post;
	$args = array( 'posts_per_page' => 5,
				'post_type' => 'cwt_carwashes',
				'orderby' => 'post_date',
				'order' => 'desc',
				'post_status' => 'publish',
				'meta_key' => 'sale_for_sale',
				'meta_value' => '',
				'meta_compare' => '!=');

	remove_all_filters('posts_orderby');//prevent plugin clashing with custom ordering
	$results = query_posts( $args );
	echo '<ol>';
	for($r=0; $r<sizeof($results); $r++) {
		if(get_field('sale_advert_title',$results[$r]->ID) != '') {
			echo '<li>('.date('d/m/Y', strtotime($results[$r]->post_date)).')&nbsp;';
			$author = get_userdata($results[$r]->post_author);
			echo '<a href="/carwash-for-sale/?showID='.$results[$r]->ID.'#showListing" target="_blank">';
			echo get_field('sale_advert_title',$results[$r]->ID).'</a>&nbsp;['.$author->first_name.' '.$author->last_name.']</li>';
		}
	}
	echo '</ol>';

}
function add_dashboard_recent_forsale() {
       wp_add_dashboard_widget( 'dashboard_recent_forsale', __( 'Recent Car Washes For Sale' ), 'dashboard_recent_forsale' );
}
add_action('wp_dashboard_setup', 'add_dashboard_recent_forsale' );

//Suppliers
function dashboard_recent_suppliers() {

	output_dashboard_posts('cwt_suppliers', 'suppliers');
}

function add_dashboard_recent_suppliers() {

       wp_add_dashboard_widget( 'dashboard_recent_suppliers', __( 'Recent Suppliers' ), 'dashboard_recent_suppliers' );
}
add_action('wp_dashboard_setup', 'add_dashboard_recent_suppliers' );

//Products
function dashboard_recent_products() {
	output_dashboard_posts('cwt_products', 'products');
}
function add_dashboard_recent_products() {

       wp_add_dashboard_widget( 'dashboard_recent_products', __( 'Recent Products' ), 'dashboard_recent_products' );
}
add_action('wp_dashboard_setup', 'add_dashboard_recent_products' );

//Jobs
function dashboard_recent_jobs() {

	output_dashboard_posts('cwt_jobs', 'jobs');

}
function add_dashboard_recent_jobs() {
       wp_add_dashboard_widget( 'dashboard_recent_jobs', __( 'Recent Jobs' ), 'dashboard_recent_jobs' );
}
add_action('wp_dashboard_setup', 'add_dashboard_recent_jobs' );



/** 
 * Add Subscriptions Page
 */
add_action( 'admin_menu', 'cwt_subscriptions_page' );

function cwt_subscriptions_page() {
	add_menu_page( 'Subscriptions', 'Subscriptions', 'manage_options', 'cwt_subscriptions_page_options', 'cwt_subscriptions_page_options', '', '12');
}

function cwt_subscriptions_page_options() {
	global $wpdb;
	if ( !current_user_can( 'manage_options' ) )  {
		wp_die( __( 'You do not have sufficient permissions to access this page.' ) );
	}

	echo '<div class="wrap">';
	echo screen_icon();
	echo '<h2>Subscriptions</h2>';

	$args = 'FROM cwt_subscription s LEFT JOIN cwt_transaction t ON s.id = t.type_id ';
	$args .= 'WHERE s.item != "" ';
	switch($filter) {
		case 'all': break;
		case 'job': $args .= 'AND SUBSTRING(s.item, 0, 3) = "Job" '; break;
		case 'product': $args .= 'AND SUBSTRING(s.item, 0, 3) = "Pro" '; break;
		case 'supplier': $args .= 'AND SUBSTRING(s.item, 0, 3) = "Sup" '; break;
		case 'email': $args .= 'AND SUBSTRING(s.item, 0, 3) = "Ema" '; break;
		case 'forsale': $args .= 'AND SUBSTRING(s.item, 0, 3) = "For " '; break;
	}
	$args .= 'ORDER BY t.transaction_date DESC';

	$pagenum = isset( $_GET['pagenum'] ) ? intval(absint( $_GET['pagenum'] )) : 1;
	$limit = 10; // number of rows in page
	$offset = ( $pagenum - 1 ) * $limit;
	$total = $wpdb->get_var('SELECT COUNT(s.id) '.$args);
	$num_of_pages = ceil( $total / $limit );

	$subscriptions = $wpdb->get_results('SELECT s.item, s.user_id, s.paid, t.transaction_id, t.transaction_date '.$args.' LIMIT '.$offset.', '.$limit);

	/*echo '<pre>';
	print_r($subscriptions);
	echo '</pre>';*/

	for($s=0; $s<sizeof($subscriptions); $s++) {
		$table .= '<tr>';
		$table .= '<td>'.$subscriptions[$s]->item.'</td>';
		$user = get_userdata($subscriptions[$s]->user_id);
		$table .= '<td>'.$user->first_name.' '.$user->last_name.' (ID: '.$subscriptions[$s]->user_id.')</td>';
		$table .= '<td>'.$subscriptions[$s]->transaction_id.'</td>';
		$table .= '<td>'.$subscriptions[$s]->transaction_date.'</td>';
		$table .= '</tr>';
	}

	$pageNext = (($pagenum != $num_of_pages) ? ($pagenum+1) : 1);
	$pagePrev = (($pagenum != 1) ? ($pagenum-1) : $num_of_pages);

	$filter = isset( $_GET['filter'] ) ? $_GET['filter'] : 'all';

	/*echo '<ul class="subsubsub">';
	echo '<li><a href="/wp-admin/admin.php?page=cwt_subscriptions_page_options&filter=all" class="current">All <span class="count">(5)</span></a> | </li>';
	echo '<li><a href="/wp-admin/admin.php?page=cwt_subscriptions_page_options&filter=supplier">Subscriptions <span class="count">(5)</span></a> | </li>';
	echo '<li><a href="/wp-admin/admin.php?page=cwt_subscriptions_page_options&filter=product">Product Listings <span class="count">(5)</span></a> | </li>';
	echo '<li><a href="/wp-admin/admin.php?page=cwt_subscriptions_page_options&filter=forsale">Car Wash for Sale <span class="count">(5)</span></a> | </li>';
	echo '<li><a href="/wp-admin/admin.php?page=cwt_subscriptions_page_options&filter=job">Job Listings <span class="count">(5)</span></a> | </li>';
	echo '<li><a href="/wp-admin/admin.php?page=cwt_subscriptions_page_options&filter=email">Priority Emails <span class="count">(5)</span></a></li>';
	echo '</ul>';*/


	echo '<div class="tablenav top">';
	echo '<div class="tablenav-pages"><span class="displaying-num">'.$total.' items</span>';
	if($num_of_pages != 1) {
		echo '<span class="pagination-links"><a class="first-page" title="Go to the first page" href="/wp-admin/admin.php?page=cwt_subscriptions_page_options&filter='.$filter.'">&laquo;</a>';
		echo '<a class="prev-page" title="Go to the previous page" href="/wp-admin/admin.php?page=cwt_subscriptions_page_options&filter='.$filter.'&pagenum='.$pagePrev.'">&lt;</a>';
		echo '<span class="paging-input">';
		echo $pagenum.' of <span class="total-pages">'.$num_of_pages.'</span></span>';
		echo '<a class="next-page" title="Go to the next page" href="/wp-admin/admin.php?page=cwt_subscriptions_page_options&filter='.$filter.'&pagenum='.$pageNext.'">&gt;</a>';
		echo '<a class="last-page" title="Go to the last page" href="/wp-admin/admin.php?page=cwt_subscriptions_page_options&filter='.$filter.'&pagenum='.$num_of_pages.'">&raquo;</a></span></div>';
	}
	echo '</div>';

	echo '<table class="wp-list-table widefat fixed posts" cellspacing="0">';
	echo '<thead>';
	echo '<tr>';
	echo '<th>Subscription Type</th>';
	echo '<th>User</th>';
	echo '<th>Paypal ID</th>';
	echo '<th>Payment Date</th>';
	echo '</tr>';
	echo '</thead>';
	echo $table;
	echo '</table>';

	echo '</div>';
}


add_action( 'admin_init', 'redirect_non_admin_users' );
/**
 * Redirect non-admin users to home page
 *
 * This function is attached to the 'admin_init' action hook.
 */
function redirect_non_admin_users() {
	if ( ! current_user_can( 'manage_options' ) && '/wp-admin/admin-ajax.php' != $_SERVER['PHP_SELF'] ) {
		wp_redirect( home_url() );
		exit;
	}
}

function get_terms_filter( $terms, $taxonomies, $args )
{
	global $wpdb;
	$taxonomy = $taxonomies[0];
	if ( ! is_array($terms) && count($terms) < 1 )
		return $terms;
	$filtered_terms = array();
	foreach ( $terms as $term )
	{
		$result = $wpdb->get_var("SELECT COUNT(*) FROM $wpdb->posts p JOIN $wpdb->term_relationships rl ON p.ID = rl.object_id WHERE rl.term_taxonomy_id = $term->term_id AND p.post_status = 'publish' LIMIT 1");
		if ( intval($result) > 0 )
			$filtered_terms[] = $term;
	}


	if(is_page(array('home','jobs','suppliers','carwashes-for-sale','products')))
		return $filtered_terms;
	else
		return $terms;
}
add_filter('get_terms', 'get_terms_filter', 10, 3);


function posttype_admin_css() {
    global $post_type;
    $post_types = array(
                        /* set post types */ 
                        'post_type_name',
                        'post',
                        'page',
                        'cwt_carwashes',
                        'cwt_products',
                        'cwt_suppliers',
                        'cwt_jobs',
                  );
    if(in_array($post_type, $post_types))
    echo '<style type="text/css">#post-preview, #view-post-btn{display: none;}</style>';
}
add_action( 'admin_head-post-new.php', 'posttype_admin_css' );
add_action( 'admin_head-post.php', 'posttype_admin_css' );
	
	function myCWTShow($pageID, $userRole, $isMyCWT) {
		global $wpdb, $current_user;
		$return = true;
		$privilegeShow = 'true';
		
		if($isMyCWT == 'true') {//if a MyCWT subpage
			
			$showForUsers = array();
			if(get_field('show_for_users', $pageID) != '')
				$showForUsers = get_field('show_for_users', $pageID);

			//should page be hidden if user hasnt paid/completed details?
			$isPrivilegeRestricted = get_field('privilege_restricted', $pageID);

			//check user has completed required details for access
			$privilege = 'true';
			switch($userRole) {
				case 'supplier':

					$packages = $wpdb->get_results('SELECT item FROM cwt_subscription WHERE user_id = '.$current_user->ID.' ORDER BY id DESC LIMIT 1');
				    
				    foreach($packages as $package)
				        $packageName = $package->item;

				    $businessQuery = array(
						'post_type' => 'cwt_suppliers',
						'author' => $current_user->ID
					);
					$business = query_posts($businessQuery);
				    $businessName = $business[0]->post_title;

				    if($packageName == '' || $businessName == '' || $businessName == 'BUSINESS NAME HERE')
				    	$privilege = 'false';

					break;

				case 'trader':

					$businessName = get_field('user_business_name', 'user_'.$current_user->ID);
	
					if($businessName == '')
						$privilege = 'false';

					break;
			}
			if(($isPrivilegeRestricted == 'yes' || $isPrivilegeRestricted == '') && $privilege == 'false')
				$privilegeShow = 'false';
			if(in_array($userRole, $showForUsers) && $privilegeShow == 'true')
				$return = true;
			else
				$return = false;
		}
		
		return $return;
	}
?>