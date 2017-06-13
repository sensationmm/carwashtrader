<?php
/**
 * @package WordPress
 * @subpackage carwashtrader
 * template name: my cwt - send message
*/

get_header(); 
include 'includes/secure.php';
$subject = 'User Message';
include 'includes/email-template.php';

global $current_user, $wp_roles;
get_currentuserinfo();
$error = array();   
$send_target = array(); 

//wp-editor settings
$editorSettings = array('media_buttons' => false,
						'textarea_rows' => 6,
						'teeny' => true,
						'quicktags' => false);
                    
if ( 'POST' == $_SERVER['REQUEST_METHOD'] && !empty( $_POST['action'] ) && $_POST["action"] == 'send_message') {			

	$send_target = $_POST["send_target"];
	$send_message = $_POST["send_message"];

	if (sizeof($send_target) == 0) {
		$error[] = 'Please select which type of user(s) to send to';
		$send_target = array(); 
	}

	if ($send_target == '') 
		$error[] = 'Please enter your message';

	if(sizeof($error) == 0) {

		for($u=0; $u<sizeof($send_target); $u++) {
			$users = array('role' => $send_target[$u], 'fields' => array('ID','display_name','user_email'));
	    	$targets = get_users($users);

	    	for($t=0; $t<sizeof($targets); $t++) {

        		$allow = get_the_author_meta('emails_private', $targets[$t]->ID);
        		if($allow == 'yes') {
                    $message = "\r\n";
		    		$message .= '<tr><td><p><b>PLEASE NOTE: You are receiving this message because you have consented to receive messages from ';
		    		$message .= 'Car Wash Trader users</b>. You can modify this under My Email Preferences when you ';
		    		$message .= '<a href="http://www.carwashtrader.co.uk/login/">log in</a> to your control panel.</p>';

		    		//$message .= '<p><b>From</b>: '.$current_user->first_name.' '.$current_user->last_name.'<br />';
		    		//$message .= '<b>Email</b>: '.$current_user->user_email.'</p>';
                    $message .= "\r\n";
		    		$message .= $send_message;
		    		$message .= '</td></tr>';

	    			mail($targets[$t]->user_email, $subject, $emailHeader.$message.$emailFooter, $headers) or die("Error");
	    		}
	    	}
	    }

		unset($_POST);
		wp_redirect('/my-cwt/?sent=true');
	}
}


include 'includes/header.php';
?>
	<!-- MyCWT - Send Message -->
        
        <h1><?php echo (get_field('display_title', $pageObj->ID) != '') ? get_field('display_title', $pageObj->ID) : $pageObj->post_title; ?></h1>
        
        <article>
        	<h2><?php echo $pageObj->post_title; ?></h2>

        	<?php 
        		if(apply_filters('the_content', $pageObj->post_content) != '')
        			echo '<div class="purpleBox">'.apply_filters('the_content', $pageObj->post_content).'</div>'; 
        	?>

        	<div class="formBox">
        		<?php if ( sizeof($error) > 0 ) echo '<p class="error">' . implode("<br />", $error) . '</p>'; ?>
                <form method="post">
				<label>Send to User Type *</label>
                <div class="options">
					<input value="guest" id="target_guest" name="send_target[]" type="checkbox" <?php if(in_array('guest', $send_target)) echo 'checked="checked" '; ?> />
					<label class="inline" for="target_guest">Guest</label>
					<input value="trader" id="target_trader" name="send_target[]" type="checkbox" <?php if(in_array('trader', $send_target)) echo 'checked="checked" '; ?> />
					<label class="inline" for="target_trader">Trader</label>
					<input value="estateagent" id="target_estateagent" name="send_target[]" type="checkbox" <?php if(in_array('estateagent', $send_target)) echo 'checked="checked" '; ?> />
					<label class="inline" for="target_estateagent">Estate Agent</label>
					<input value="supplier" id="target_supplier" name="send_target[]" type="checkbox" <?php if(in_array('supplier', $send_target)) echo 'checked="checked" '; ?> />
					<label class="inline" for="target_supplier">Supplier</label>
                </div>
                    
                <label for="addpost_description">Message *</label>
               	<?php wp_editor( $send_message, 'send_message', $editorSettings); ?> 
                        
		        <input type="submit" value="Send" id="submit" name="submit" />
		        <input type="hidden" name="action" value="send_message" />
		        <?php wp_nonce_field( 'email-preferences' ); ?>
        		</form>
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