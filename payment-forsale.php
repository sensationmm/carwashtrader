<?php
/**
 * @package WordPress
 * @subpackage carwashtrader
 * template name: payment for sale
*/

get_header(); 

if(!isset($_GET["id"]) || !is_numeric($_GET["id"])) 
    header('Location: /my-cwt/car-washes/');

$forsale = get_post($_GET["id"]);

if($forsale->post_type != 'cwt_carwashes' || get_field('premium_paypayID', $forsale->ID) != '') {
    header('Location: /my-cwt/car-washes/');
} else {

    $check = 'SELECT id FROM cwt_transaction WHERE transaction_type = "forsale" AND type_id = '.$forsale->ID.' AND expired = 0';
    $check = mysql_query($check);
    if(mysql_num_rows($check) > 0) {
        $check = mysql_fetch_assoc($check);
        $transactionID = $check["id"];
    } else {
        $trans = 'INSERT INTO cwt_transaction (transaction_type, type_id) VALUES ("forsale", '.$forsale->ID.')';
        $trans_query = mysql_query($trans) or die('Transaction: '.$trans.' - '.mysql_error());
        $transactionID = mysql_insert_id();
    }

}

include 'includes/header.php';
?>
	<!-- Payment For Sale -->
        
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
                $title = $forsale->post_title;
                $description = $forsale->post_content;
                $address = get_field('carwash_address', $forsale->ID);
                $postcode = get_field('carwash_postcode', $forsale->ID);
                $geocode = get_field('carwash_geocode', $forsale->ID);
                $contact = get_field('carwash_contactnumber', $forsale->ID);
                $website = get_field('carwash_website', $forsale->ID); 
                $openinghours = get_field('carwash_openinghours', $forsale->ID); 
                $prices = get_field('carwash_prices', $forsale->ID);

                $for_sale = get_field('sale_for_sale', $forsale->ID);
                $advert_title = get_field('sale_advert_title', $forsale->ID);
                $listing_info = get_field('sale_listing_info', $forsale->ID);
                $business_size = get_field('sale_business_size', $forsale->ID);
                $price = get_field('sale_price', $forsale->ID);
                $leasehold_freehold = get_field('sale_leasehold_freehold', $forsale->ID);
                $leasehold_years = get_field('sale_leasehold_years', $forsale->ID);
                $purchase_link = get_field('sale_purchase_link', $forsale->ID);
                
                echo outputCarwashForSaleListing($advert_title,$listing_info,$business_size,$price,$leasehold_freehold,$leasehold_years,$purchase_link,$forsale->ID,$title,$description,$address,$postcode,$geocode,$contact,$website,$openinghours,$prices,$distance,$doDistance);
            ?>

            <div class="paymentRow">
                <div class="paymentBox months1">
                    <h3>1 Month Listing</h3>
                    <h4 class="price"><?php echo outputPrice($priceForsale_1m); ?></h4>
                    <form id="paypal1Month" method="post" action= "https://www.paypal.com/cgi-bin/webscr">
                    <input type="hidden" name="cmd" value="_xclick" />
                    <input type="hidden" name="business" value="btrepca@gmail.com" />
                    <input type="hidden" name="item_name" value="For Sale Listing (1 month)" />
                    <input type="hidden" name="amount" value="<?php echo $priceForsale_1m; ?>" />
                    <input type="hidden" name="no_shipping" value="1" />
                    <input type="hidden" name="return" value="/paypal-pdt.php" />
                    <input type="hidden" name="on1" value="Booking Name" />
                    <input type="hidden" name="os1" value="<?php echo $current_user->user_firstname.' '.$current_user->user_lastname; ?>" />
                    <input type="hidden" name="on2" value="TxID" />
                    <input type="hidden" name="os2" value="<?php echo $transactionID; ?>" />
                    <input type="hidden" name="on3" value="forsaleID" />
                    <input type="hidden" name="os3" value="<?php echo $forsale->ID; ?>" />
                    <input type="hidden" name="currency_code" value="GBP" />
                    </form>

                    <div class="submit">
                        <a href="book-now/" onClick="document.getElementById('paypal1Month').submit();return false;" title="Proceed to Paypal">
                        Proceed to Paypal</a>
                    </div>
                </div>

                <div class="paymentBox months3">
                    <h3>3 Month Listing</h3>
                    <h4 class="price"><?php echo outputPrice($priceForsale_3m); ?></h4>
                    <form id="paypal3Month" method="post" action= "https://www.paypal.com/cgi-bin/webscr">
                    <input type="hidden" name="cmd" value="_xclick" />
                    <input type="hidden" name="business" value="btrepca@gmail.com" />
                    <input type="hidden" name="item_name" value="For Sale Listing (3 months)" />
                    <input type="hidden" name="amount" value="<?php echo $priceForsale_3m; ?>" />
                    <input type="hidden" name="no_shipping" value="1" />
                    <input type="hidden" name="return" value="/paypal-pdt.php" />
                    <input type="hidden" name="on1" value="Booking Name" />
                    <input type="hidden" name="os1" value="<?php echo $current_user->user_firstname.' '.$current_user->user_lastname; ?>" />
                    <input type="hidden" name="on2" value="TxID" />
                    <input type="hidden" name="os2" value="<?php echo $transactionID; ?>" />
                    <input type="hidden" name="on3" value="forsaleID" />
                    <input type="hidden" name="os3" value="<?php echo $forsale->ID; ?>" />
                    <input type="hidden" name="currency_code" value="GBP" />
                    </form>

                    <div class="submit">
                        <a href="book-now/" onClick="document.getElementById('paypal3Month').submit();return false;" title="Proceed to Paypal">
                        Proceed to Paypal</a>
                    </div>
                </div>

                <div class="paymentBox months6">
                    <h3>6 Month Listing</h3>
                    <h4 class="price"><?php echo outputPrice($priceForsale_6m); ?></h4>
                    <form id="paypal6Month" method="post" action= "https://www.paypal.com/cgi-bin/webscr">
                    <input type="hidden" name="cmd" value="_xclick" />
                    <input type="hidden" name="business" value="btrepca@gmail.com" />
                    <input type="hidden" name="item_name" value="For Sale Listing (6 months)" />
                    <input type="hidden" name="amount" value="<?php echo $priceForsale_6m; ?>" />
                    <input type="hidden" name="no_shipping" value="1" />
                    <input type="hidden" name="return" value="/paypal-pdt.php" />
                    <input type="hidden" name="on1" value="Booking Name" />
                    <input type="hidden" name="os1" value="<?php echo $current_user->user_firstname.' '.$current_user->user_lastname; ?>" />
                    <input type="hidden" name="on2" value="TxID" />
                    <input type="hidden" name="os2" value="<?php echo $transactionID; ?>" />
                    <input type="hidden" name="on3" value="forsaleID" />
                    <input type="hidden" name="os3" value="<?php echo $forsale->ID; ?>" />
                    <input type="hidden" name="currency_code" value="GBP" />
                    </form>

                    <div class="submit">
                        <a href="book-now/" onClick="document.getElementById('paypal6Month').submit();return false;" title="Proceed to Paypal">
                        Proceed to Paypal</a>
                    </div>
                </div>

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