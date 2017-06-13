<?php
	$listings = array('post_type' => 'cwt_jobs',
									'posts_per_page' => 3);
	remove_all_filters('posts_orderby');//prevent plugin clashing with custom ordering
	$results = query_posts($listings);

	if(sizeof($results) > 0) {
?>
        	<div class="jobalerts">
            	<div class="header">Job Alerts</div>
                <ul class="feature">
                <?php
                	for($jl=0; $jl<sizeof($results); $jl++) {
                		echo '<li>';
                		echo $results[$jl]->post_title;
                		echo '<div class="featureInfo">';
                		$salary = get_field('job_salary',$results[$jl]->ID);
                		if($salary != '')
                			echo '<br />'.html_entity_decode($salary);
                		echo '<br /><a href="/jobs/?showID='.$results[$jl]->ID.'#showListing">Apply now</a>';
                		echo '</div>';
                		echo '</li>';
                	}
                ?>
                </ul>
            	<!--div class="footer">Register to sign up for free job alerts</div-->
            </div>
<?php } ?>