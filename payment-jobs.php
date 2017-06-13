<?php
/**
 * @package WordPress
 * @subpackage carwashtrader
 * template name: payment jobs
*/

get_header(); 

if(!isset($_GET["id"]) || !is_numeric($_GET["id"])) 
    header('Location: /my-cwt/jobs/');

$job = get_post($_GET["id"]);

if($job->post_type != 'cwt_jobs' || get_field('premium_paypayID', $job->ID) != '') {
    header('Location: /my-cwt/jobs/');
} else {

    $check = 'SELECT id FROM cwt_transaction WHERE transaction_type = "job" AND type_id = '.$job->ID.' AND expired = 0';
    $check = mysql_query($check);
    if(mysql_num_rows($check) > 0) {
        $check = mysql_fetch_assoc($check);
        $transactionID = $check["id"];
    } else {
        $trans = 'INSERT INTO cwt_transaction (transaction_type, type_id) VALUES ("job", '.$job->ID.')';
        $trans_query = mysql_query($trans) or die('Transaction: '.$trans.' - '.mysql_error());
        $transactionID = mysql_insert_id();
    }

}

include 'includes/header.php';
?>
	<!-- Payment Jobs -->
        
        <?php 
            $bgImage = get_field('intro_image', $pageObj->ID); 
            $displayTitle = get_field('display_title', $pageObj->ID);
        ?>

        <h1<?php echo ($bgImage != '') ? ' class="imageHeader" style="background-image:url('.$bgImage.');"' : ''; ?>>
        <?php echo ($displayTitle != '') ? $displayTitle : $pageObj->post_title; ?></h1>
        
        <article>
        	<h2><?php echo $post->post_title; ?></h2>
            <?php echo apply_filters('the_content', $post->post_content); ?>

            <?php
                $title = $job->post_title;
                $description = $job->post_content;
                $address = get_field('job_address', $job->ID);
                $postcode = get_field('job_postcode', $job->ID);
                $geocode = get_field('job_geocode', $job->ID);
                $hours = get_field('job_hours', $job->ID);
                $salary = get_field('job_salary', $job->ID);
                $apply = get_field('job_apply', $job->ID);
                
                echo outputJobListing($job->ID,$title,$description,$address,$postcode,$geocode,$hours,$salary,$apply);
            ?>

            <div class="paymentRow">
                <div class="paymentBox months1">
                    <h3>1 Month Listing</h3>
                    <h4 class="price"><?php echo outputPrice($priceJob_1m); ?></h4>
                    <form id="paypal1Month" method="post" action= "https://www.paypal.com/cgi-bin/webscr">
                    <input type="hidden" name="cmd" value="_xclick" />
                    <input type="hidden" name="business" value="btrepca@gmail.com" />
                    <input type="hidden" name="item_name" value="Job Listing (1 month)" />
                    <input type="hidden" name="amount" value="<?php echo $priceJob_1m; ?>" />
                    <input type="hidden" name="no_shipping" value="1" />
                    <input type="hidden" name="return" value="/paypal-pdt.php" />
                    <input type="hidden" name="on1" value="Booking Name" />
                    <input type="hidden" name="os1" value="<?php echo $current_user->user_firstname.' '.$current_user->user_lastname; ?>" />
                    <input type="hidden" name="on2" value="TxID" />
                    <input type="hidden" name="os2" value="<?php echo $transactionID; ?>" />
                    <input type="hidden" name="on3" value="jobID" />
                    <input type="hidden" name="os3" value="<?php echo $job->ID; ?>" />
                    <input type="hidden" name="currency_code" value="GBP" />
                    </form>

                    <div class="submit">
                        <a href="book-now/" onClick="document.getElementById('paypal1Month').submit();return false;" title="Proceed to Paypal">
                        Proceed to Paypal</a>
                    </div>
                </div>

                <div class="paymentBox months3">
                    <h3>3 Month Listing</h3>
                    <h4 class="price"><?php echo outputPrice($priceJob_3m); ?></h4>
                    <form id="paypal3Month" method="post" action= "https://www.paypal.com/cgi-bin/webscr">
                    <input type="hidden" name="cmd" value="_xclick" />
                    <input type="hidden" name="business" value="btrepca@gmail.com" />
                    <input type="hidden" name="item_name" value="Job Listing (3 months)" />
                    <input type="hidden" name="amount" value="<?php echo $priceJob_3m; ?>" />
                    <input type="hidden" name="no_shipping" value="1" />
                    <input type="hidden" name="return" value="/paypal-pdt.php" />
                    <input type="hidden" name="on1" value="Booking Name" />
                    <input type="hidden" name="os1" value="<?php echo $current_user->user_firstname.' '.$current_user->user_lastname; ?>" />
                    <input type="hidden" name="on2" value="TxID" />
                    <input type="hidden" name="os2" value="<?php echo $transactionID; ?>" />
                    <input type="hidden" name="on3" value="jobID" />
                    <input type="hidden" name="os3" value="<?php echo $job->ID; ?>" />
                    <input type="hidden" name="currency_code" value="GBP" />
                    </form>

                    <div class="submit">
                        <a href="book-now/" onClick="document.getElementById('paypal3Month').submit();return false;" title="Proceed to Paypal">
                        Proceed to Paypal</a>
                    </div>
                </div>

                <!--div class="paymentBox months6">
                    <h3>6 Month Listing</h3>
                    <h4 class="price"><?php echo outputPrice($priceJob_6m); ?></h4>
                    <form id="paypal6Month" method="post" action= "https://www.paypal.com/cgi-bin/webscr">
                    <input type="hidden" name="cmd" value="_xclick" />
                    <input type="hidden" name="business" value="btrepca@gmail.com" />
                    <input type="hidden" name="item_name" value="Job Listing (6 months)" />
                    <input type="hidden" name="amount" value="<?php echo $priceJob_6m; ?>" />
                    <input type="hidden" name="no_shipping" value="1" />
                    <input type="hidden" name="return" value="/paypal-pdt.php" />
                    <input type="hidden" name="on1" value="Booking Name" />
                    <input type="hidden" name="os1" value="<?php echo $current_user->user_firstname.' '.$current_user->user_lastname; ?>" />
                    <input type="hidden" name="on2" value="TxID" />
                    <input type="hidden" name="os2" value="<?php echo $transactionID; ?>" />
                    <input type="hidden" name="on3" value="jobID" />
                    <input type="hidden" name="os3" value="<?php echo $job->ID; ?>" />
                    <input type="hidden" name="currency_code" value="GBP" />
                    </form>

                    <div class="submit">
                        <a href="book-now/" onClick="document.getElementById('paypal6Month').submit();return false;" title="Proceed to Paypal">
                        Proceed to Paypal</a>
                    </div>
                </div-->

            </div>
        </article>
        
        <section>
            <?php include 'includes/menu-submenu.php'; ?>
            
        	<?php include 'includes/adverts-menu.php'; ?>
        </section>
    </div>

<?php 
include 'includes/footer.php';
get_footer(); 
?> 