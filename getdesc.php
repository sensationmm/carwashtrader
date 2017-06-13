<?php

	session_start();
    
    global $wpdb;
    
    include '../../../wp-blog-header.php';

    $desc = 'SELECT * FROM cwt_postmeta WHERE meta_key = "carwash_description"';
    $desc_query = mysql_query($desc) or die(mysql_error());
    while($desc = mysql_fetch_assoc($desc_query))
    {
    	$update = 'UPDATE cwt_posts SET post_content = "'.$desc["meta_value"].'" WHERE ID = '.$desc["post_id"];
    	mysql_query($update);
    	echo $update.'<br />';
    }


?>