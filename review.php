<?php
/**
 * @package WordPress
 * @subpackage carwashtrader
 * template name: review
*/

get_header(); 
include 'includes/header.php';
?>
	<!-- Review -->
        
        <?php 
        	$bgImage = get_field('intro_image', $pageObj->ID); 
        	$displayTitle = get_field('display_title', $pageObj->ID);
        ?>

        <h1<?php echo ($bgImage != '') ? ' class="imageHeader" style="background-image:url('.$bgImage.');"' : ''; ?>>
        <?php echo ($displayTitle != '') ? $displayTitle : $pageObj->post_title; ?></h1>
        
        <article>
            <?php 
			if(isset($_GET["id"])) {
				$post->ID = $_GET["id"];
				$reviewItem = get_post($post->ID);
				
				echo '<h2>'.$reviewItem->post_title.' Reviews</h2>';
				echo apply_filters('the_content', $pageObj->post_content);
				echo '<div class="clear"></div>';
				echo '<div class="formBox">';
				if(is_user_logged_in())
					echo apply_filters('the_content', '[cbratingsystem form_id="1" post_id="'.$post->ID.'" showreview="1"]');
				else
					echo '<p>You must be <a href="/login/" title="Log In">logged in</a> to leave a review</p>';
				echo '</div>';
				
				//Get reviews for this post
				$reviews = 'SELECT * FROM cwt_cbratingsystem_user_ratings WHERE comment_status = "approved" AND post_id = '.$post->ID.' ORDER BY created DESC';
				$reviews = $wpdb->get_results($reviews);
				for($r=0; $r<sizeof($reviews); $r++) {
				?>
					

					<div id="cbrating-1-review-5" data-review-id="5" data-post-id="" data-form-id="1" class="cbratingsinglerevbox reviews_wrapper_basic_theme review_wrapper review_wrapper_post-_form-1 review_wrapper_post-_form-1_review-5">    
						<div class="cbratingboxinner approved reviews_rating_basic_theme review_rating review_rating_review-5">  
							<div class="reviews_user_details_basic_theme review_user_details">
								<p class="cbrating_user_name">
									<span class="user_gravatar"><?php echo $reviews[$r]->user_name; ?></span>
								</p>
								<span class="user_rate_time">
									<?php echo date('jS M Y', $reviews[$r]->created); ?>
								</span>
							</div>
							<div class="clear" style="clear:both;"></div>
							<div data-form-id="1" class="all_criteria_warpper_basic_theme all-criteria-wrapper all-criteria-wrapper-form-1">
								<div data-form-id="1" data-criteria-id="0" class="criteria_warpper_basic_theme criteria-wrapper criteria-id-wrapper-0 criteria-id-wrapper-0-form-1 ">
									<div class="criteria_label_warpper_basic_theme criteria-label-wrapper">
										<span class="criteria-label criteria-label-id-0">
											<strong>Rating</strong>
										</span>
									</div>
									<div style="width: 100px;" title="regular" id="criteria-star-wrapper-5" data-form-id="1" data-criteria-id="0" class="criteria-star-wrapper criteria-star-wrapper-id- criteria-star-wrapper-id-0-form-1">
										<?php
											$reviewData = maybe_unserialize($reviews[$r]->rating);
											
											$reviewRating = intval($reviewData["0_actualValue"]);
											$reviewText = '';
											for($i=0; $i<5; $i++) {
												if(($i+1) <= $reviewRating)
													echo '<img title="regular" alt="1" src="http://carwashd.wwwsr12.supercp.com/wp-content/plugins/cbratingsystem/images/star-on.png">';
												else
													echo '<img title="regular" alt="1" src="http://carwashd.wwwsr12.supercp.com/wp-content/plugins/cbratingsystem/images/star-off.png">';

												if(($i+1) == $reviewRating) 
													$reviewText = $reviewData["0_stars"][$i];
											}

										?>
										<input readonly="readonly" value="3" name="score" type="hidden">
									</div>
									<div class="readonly_criteria_average_label_basic_theme readonly-criteria-average-label criteria-average-label-form-1-label-0">
										<span class="starTitle"><?php echo $reviewText; ?></span>
									</div>
								</div>
							</div>
							<div class="review_user_rating_comment_basic_theme review_user_rating_comment">
								<strong>Comment : </strong> <p class="comment"><?php echo $reviews[$r]->comment; ?></p>
							</div>
							<div class="clear" style="clear:both;"></div>
						</div>
					</div>

				<?php
				}

			} else {
				echo '<h2>All Reviews</h2>';
				echo apply_filters('the_content', $pageObj->post_content);


				
				//Get reviews for this post
				$reviews = 'SELECT * FROM cwt_cbratingsystem_user_ratings WHERE comment_status = "approved" ORDER BY created DESC';
				$reviews = $wpdb->get_results($reviews);
				for($r=0; $r<sizeof($reviews); $r++) {
				?>
					

					<div id="cbrating-1-review-5" data-review-id="5" data-post-id="" data-form-id="1" class="cbratingsinglerevbox reviews_wrapper_basic_theme review_wrapper review_wrapper_post-_form-1 review_wrapper_post-_form-1_review-5">    
						<div class="cbratingboxinner approved reviews_rating_basic_theme review_rating review_rating_review-5">  
							<div class="reviews_user_details_basic_theme review_user_details">
								<p class="cbrating_user_name">
									<?php $carwash = get_post($reviews[$r]->post_id); ?>
									<span class="user_gravatar"><b><?php echo $carwash->post_title; ?></b></span>
								</p>
								<p class="cbrating_user_name">
									<span class="user_gravatar"><?php echo $reviews[$r]->user_name; ?></span>
								</p>
								<span class="user_rate_time">
									<?php echo date('jS M Y', $reviews[$r]->created); ?>
								</span>
							</div>
							<div class="clear" style="clear:both;"></div>
							<div data-form-id="1" class="all_criteria_warpper_basic_theme all-criteria-wrapper all-criteria-wrapper-form-1">
								<div data-form-id="1" data-criteria-id="0" class="criteria_warpper_basic_theme criteria-wrapper criteria-id-wrapper-0 criteria-id-wrapper-0-form-1 ">
									<div class="criteria_label_warpper_basic_theme criteria-label-wrapper">
										<span class="criteria-label criteria-label-id-0">
											<strong>Rating</strong>
										</span>
									</div>
									<div style="width: 100px;" title="regular" id="criteria-star-wrapper-5" data-form-id="1" data-criteria-id="0" class="criteria-star-wrapper criteria-star-wrapper-id- criteria-star-wrapper-id-0-form-1">
										<?php
											$reviewData = maybe_unserialize($reviews[$r]->rating);
											
											$reviewRating = intval($reviewData["0_actualValue"]);
											$reviewText = '';
											for($i=0; $i<5; $i++) {
												if(($i+1) <= $reviewRating)
													echo '<img title="regular" alt="1" src="http://carwashd.wwwsr12.supercp.com/wp-content/plugins/cbratingsystem/images/star-on.png">';
												else
													echo '<img title="regular" alt="1" src="http://carwashd.wwwsr12.supercp.com/wp-content/plugins/cbratingsystem/images/star-off.png">';

												if(($i+1) == $reviewRating) 
													$reviewText = $reviewData["0_stars"][$i];
											}

										?>
										<input readonly="readonly" value="3" name="score" type="hidden">
									</div>
									<div class="readonly_criteria_average_label_basic_theme readonly-criteria-average-label criteria-average-label-form-1-label-0">
										<span class="starTitle"><?php echo $reviewText; ?></span>
									</div>
								</div>
							</div>
							<div class="review_user_rating_comment_basic_theme review_user_rating_comment">
								<strong>Comment : </strong> <p class="comment"><?php echo $reviews[$r]->comment; ?></p>
							</div>
							<div class="clear" style="clear:both;"></div>
						</div>
					</div>

				<?php
				}
			}
			?>
            <?php  ?>



			<link rel="stylesheet" id="cbrp-basic-style-css" href="/wp-content/plugins/cbratingsystem/css/basic.style.css?ver=3.3.3" type="text/css" media="all">
			<link rel="stylesheet" id="cbrp-basic-review-style-css" href="/wp-content/plugins/cbratingsystem/css/basic.review.style.css?ver=3.3.3" type="text/css" media="all">

			<script type="text/javascript" src="http://carwashd.wwwsr12.supercp.com/wp-content/plugins/cbratingsystem/js/cbrating.common.script.js?ver=3.3.3"></script>
			<script type="text/javascript" src="http://carwashd.wwwsr12.supercp.com/wp-content/plugins/cbratingsystem/js/chosen.jquery.js?ver=3.3.3"></script>
			<script type="text/javascript" src="http://carwashd.wwwsr12.supercp.com/wp-content/plugins/cbratingsystem/js/jquery.selectize.min.js?ver=3.3.3"></script>
			<script type="text/javascript" src="http://carwashd.wwwsr12.supercp.com/wp-content/plugins/cbratingsystem/js/jquery.raty.min.js?ver=4.0.1"></script>
			<script type="text/javascript">
			/* <![CDATA[ */
			var cbrating_prefix = {"string_prefix":"","string_postfix":"characters"};
			/* ]]> */
			</script>
			<script type="text/javascript" src="http://carwashd.wwwsr12.supercp.com/wp-content/plugins/cbratingsystem/js/cbratingsystem.front.js?ver=3.3.3"></script>
			<script type="text/javascript" src="http://carwashd.wwwsr12.supercp.com/wp-content/plugins/cbratingsystem/js/cbratingsystem.front.review.js?ver=3.3.3"></script>
        </article>
        
        <section>
        	<?php include 'includes/adverts-menu.php'; ?>
        </section>
    </div>

<?php 
include 'includes/footer.php';
get_footer(); 
?> 