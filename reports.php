<?php
    
    global $wpdb;
    
    include '../../../wp-blog-header.php';
	
    echo '<table border="1">';
    echo '<tr><th colspan="6">New Listings by Month</th></tr>';
    echo '<tr><th>Month</th><th>Car Washes</th><th>Suppliers</th><th>Products</th><th>Jobs</th><th>Adverts</th></tr>';
    $month = date('m');
    $year = date('Y');
    for($i=0; $i<6; $i++) {
        echo '<tr>';
        echo '<td>'.date('M Y', mktime(0,0,0,$month,1,$year)).'</td>';
        $countposts = get_posts('post_type=cwt_carwashes&year='.$year.'&monthnum='.$month.'&posts_per_page=-1');
        echo '<td>'.sizeof($countposts).'</td>';
        $countposts = get_posts('post_type=cwt_suppliers&year='.$year.'&monthnum='.$month.'&posts_per_page=-1');
        echo '<td>'.sizeof($countposts).'</td>';
        $countposts = get_posts('post_type=cwt_products&year='.$year.'&monthnum='.$month.'&posts_per_page=-1');
        echo '<td>'.sizeof($countposts).'</td>';
        $countposts = get_posts('post_type=cwt_jobs&year='.$year.'&monthnum='.$month.'&posts_per_page=-1');
        echo '<td>'.sizeof($countposts).'</td>';
        $countposts = get_posts('post_type=cwt_adverts&year='.$year.'&monthnum='.$month.'&posts_per_page=-1');
        echo '<td>'.sizeof($countposts).'</td>';
        echo '</tr>';
        $month--;
    }
    echo '</table>';

    echo '<br /><br />';
    
    echo '<table border="1">';
    echo '<tr><th colspan="5">Revenues</th></tr>';
    echo '<tr><th>Month</th><th>Subscription</th><th>Sale Listings</th><th>Priority Emails</th></tr>';
    $month = date('m');
    $year = date('Y');
    for($i=0; $i<5; $i++) {
        echo '<tr>';
        echo '<td>'.date('M Y', mktime(0,0,0,$month,1,$year)).'</td>';
        $count = $wpdb->get_results('SELECT SUM(transaction_value) AS count FROM cwt_transaction WHERE transaction_type = "subscription" AND MONTH(transaction_date) = '.$month);
        echo '<td>'.$count[0]->count.'</td>';
        $count = $wpdb->get_results('SELECT SUM(transaction_value) AS count FROM cwt_transaction WHERE transaction_type = "forsale" AND MONTH(transaction_date) = '.$month);
        echo '<td>'.$count[0]->count.'</td>';
        $count = $wpdb->get_results('SELECT SUM(transaction_value) AS count FROM cwt_transaction WHERE transaction_type = "priorityemails" AND MONTH(transaction_date) = '.$month);
        echo '<td>'.$count[0]->count.'</td>';
        echo '</tr>';
        $month--;
    }
    $count = $wpdb->get_results('SELECT count(id) AS count FROM cwt_transaction WHERE transaction_date > "2015-03-31" AND transaction_id IS NULL');      
    echo '<tr><td colspan="4">Payment process begun but not paid: '.$count[0]->count.'</td></tr>';
    echo '</table>';

?>