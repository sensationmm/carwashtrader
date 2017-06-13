<?php
/**
 * @package WordPress
 * @subpackage carwashtrader
 * template name: register subscription
*/

get_header(); 

if(!isset($_GET["id"]) || !is_numeric($_GET["id"])) 
    header('Location: /login/');

$check = 'SELECT * FROM cwt_subscription WHERE user_id = '.$_GET["id"];
$check_query = mysql_query($check);
$check = mysql_fetch_assoc($check_query);
if($check["paid"] == 'yes') {
    header('Location: /login/');
} else if(mysql_num_rows($check_query) == 0) {

    //create supplier post instance
    $newSupplier = array(
        'post_title'    => 'BUSINESS NAME HERE',
        'post_status'   => 'publish',       
        'post_type' => 'cwt_suppliers' ,
        'post_author' => $_GET["id"],
        'post_status' => 'draft'
    );
    $newSupplier = wp_insert_post($newSupplier);
    do_action('wp_insert_post', 'wp_insert_post');

    $book = 'INSERT INTO cwt_subscription (date, user_id) VALUES ("00000000", '.$_GET["id"].')';
    $book_query = mysql_query($book) or die('Booking: '.$book.' - '.mysql_error());

    $bookingID = mysql_insert_id();

    $trans = 'INSERT INTO cwt_transaction (transaction_type, type_id) VALUES ("subscription", '.$bookingID.')';
    $trans_query = mysql_query($trans) or die('Transaction: '.$trans.' - '.mysql_error());
    $transactionID = mysql_insert_id();

} else {
    $bookingID = $check["id"];
    $transaction = 'SELECT * FROM cwt_transaction WHERE type_id = '.$bookingID;
    $transaction_query = mysql_query($transaction);
    $transaction = mysql_fetch_assoc($transaction_query);
    $transactionID = $transaction["id"];
}

include 'includes/header.php';
?>
	<!-- Register Subscription -->
        
        <?php 
            $bgImage = get_field('intro_image', $pageObj->ID); 
            $displayTitle = get_field('display_title', $pageObj->ID);
        ?>

        <h1<?php echo ($bgImage != '') ? ' class="imageHeader" style="background-image:url('.$bgImage.');"' : ''; ?>>
        <?php echo ($displayTitle != '') ? $displayTitle : $pageObj->post_title; ?></h1>
        
        <article>
        	<h2><?php echo $post->post_title; ?></h2>
            <div class="confirmation"><?php echo apply_filters('the_content', $post->post_content);?></div>

            <?php 
                $user = get_userdata($_GET["id"]); 
                if(gettype($user) == 'object') {
            ?>

            <div class="formBox">
                <div class="accountType accountsupplier">
                    <h3>One Month Subscription</h3>
                    <h4 class="price"><?php echo outputPrice($priceSubscribe_1m).'pm'; ?></h4>

                    <!--form id="paypalMonth" method="post" action= "https://www.paypal.com/cgi-bin/webscr">
                    <input type="hidden" name="cmd" value="_xclick" />
                    <input type="hidden" name="business" value="btrepca@gmail.com" />
                    <input type="hidden" name="item_name" value="Supplier 1 Month Subscription" />
                    <input type="hidden" name="amount" value="<?php echo $priceSubscribe_1m; ?>" />
                    <input type="hidden" name="no_shipping" value="1" />
                    <input type="hidden" name="return" value="/paypal-pdt.php" />
                    <input type="hidden" name="on1" value="Booking Name" />
                    <input type="hidden" name="os1" value="<?php echo $user->user_firstname.' '.$user->user_lastname; ?>" />
                    <input type="hidden" name="on2" value="TxID" />
                    <input type="hidden" name="os2" value="<?php echo $transactionID; ?>" />
                    <input type="hidden" name="on3" value="bookingID" />
                    <input type="hidden" name="os3" value="<?php echo $bookingID; ?>" />
                    <input type="hidden" name="currency_code" value="GBP" />
                    </form-->

                    <form id="paypalMonth" name="_xclick" action="https://www.paypal.com/cgi-bin/webscr" method="post">
                    <input type="hidden" name="cmd" value="_xclick-subscriptions">
                    <input type="hidden" name="business" value="btrepca@gmail.com">
                    <input type="hidden" name="item_name" value="Supplier 1 Month Subscription" />
                    <input type="hidden" name="currency_code" value="GBP">
                    <input type="hidden" name="no_shipping" value="1">
                    <input type="hidden" name="a3" value="<?php echo $priceSubscribe_1m; ?>">
                    <input type="hidden" name="p3" value="1">
                    <input type="hidden" name="t3" value="M">
                    <input type="hidden" name="src" value="1">
                    <input type="hidden" name="sra" value="1">
                    <input type="hidden" name="on1" value="Booking Name" />
                    <input type="hidden" name="os1" value="<?php echo $user->user_firstname.' '.$user->user_lastname; ?>" />
                    <input type="hidden" name="on2" value="TxID" />
                    <input type="hidden" name="os2" value="<?php echo $transactionID; ?>" />
                    <input type="hidden" name="on3" value="bookingID" />
                    <input type="hidden" name="os3" value="<?php echo $bookingID; ?>" />
                    </form>

                    <div class="submit">
                    <a href="book-now/" onClick="document.getElementById('paypalMonth').submit();return false;" title="Proceed to Paypal">
                    Proceed to Paypal</a></div>
                </div>

                <div class="accountType accountsupplier">
                    <h3>One Year Subscription</h3>
                    <h4 class="price"><?php echo outputPrice($priceSubscribe_1y).'pa'; ?></h4>

                    <!--form id="paypalYear" method="post" action= "https://www.paypal.com/cgi-bin/webscr">
                    <input type="hidden" name="cmd" value="_xclick" />
                    <input type="hidden" name="business" value="btrepca@gmail.com" />
                    <input type="hidden" name="item_name" value="Supplier 1 Year Subscription" />
                    <input type="hidden" name="amount" value="<?php echo $priceSubscribe_1y; ?>" />
                    <input type="hidden" name="no_shipping" value="1" />
                    <input type="hidden" name="return" value="/paypal-pdt.php" />
                    <input type="hidden" name="on1" value="Booking Name" />
                    <input type="hidden" name="os1" value="<?php echo $user->user_firstname.' '.$user->user_lastname; ?>" />
                    <input type="hidden" name="on2" value="TxID" />
                    <input type="hidden" name="os2" value="<?php echo $transactionID; ?>" />
                    <input type="hidden" name="on3" value="bookingID" />
                    <input type="hidden" name="os3" value="<?php echo $bookingID; ?>" />
                    <input type="hidden" name="currency_code" value="GBP" />
                    </form-->

                    <form id="paypalYear" name="_xclick" action="https://www.paypal.com/cgi-bin/webscr" method="post">
                    <input type="hidden" name="cmd" value="_xclick-subscriptions">
                    <input type="hidden" name="business" value="btrepca@gmail.com">
                    <input type="hidden" name="item_name" value="Supplier 1 Year Subscription" />
                    <input type="hidden" name="currency_code" value="GBP">
                    <input type="hidden" name="no_shipping" value="1">
                    <input type="hidden" name="a3" value="<?php echo $priceSubscribe_1y; ?>">
                    <input type="hidden" name="p3" value="1">
                    <input type="hidden" name="t3" value="Y">
                    <input type="hidden" name="src" value="1">
                    <input type="hidden" name="sra" value="1">
                    <input type="hidden" name="on1" value="Booking Name" />
                    <input type="hidden" name="os1" value="<?php echo $user->user_firstname.' '.$user->user_lastname; ?>" />
                    <input type="hidden" name="on2" value="TxID" />
                    <input type="hidden" name="os2" value="<?php echo $transactionID; ?>" />
                    <input type="hidden" name="on3" value="bookingID" />
                    <input type="hidden" name="os3" value="<?php echo $bookingID; ?>" />
                    </form>

                    <div class="submit">
                    <a href="book-now/" onClick="document.getElementById('paypalYear').submit();return false;" title="Proceed to Paypal">
                    Proceed to Paypal</a></div>
                </div>

            </div>
            <?php } ?>
        </article>
        
        <section>
        	<?php include 'includes/adverts-menu.php'; ?>
        </section>
    </div>

<?php 
include 'includes/footer.php';
get_footer(); 
?> 