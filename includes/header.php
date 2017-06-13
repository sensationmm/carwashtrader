<?php global $pageObj, $isMyCWT; include 'prices.php'; ?>
	<header>
		<div class="body">
        	<div class="logo"><a href="/" title="Go to Homepage"><img class="logo" src="assets/images/carwash-trader-long.svg" /></a></div>
            
            <?php if ( is_user_logged_in() ) { 
				global $current_user;
				get_currentuserinfo();
			?>
                <div class="accountWelcome">
                    Welcome <?php echo $current_user->user_firstname.'!'; ?>
                </div>
                
                <div class="account">
                    <ul>
                    <?php if($current_user->roles[0] != 'administrator') { ?>
                    <li><a class="red<?php if($isMyCWT == 'true') echo ' active'; ?>" href="/my-cwt/">My CWT</a></li>
                    <?php } ?>
                    <li><a <?php if($pageObj->ID == 44 || $pageObj->ID == 396) echo 'class="active"'; ?> href="/book-advert/">Book Advert</a></li>
                    <li><a class="grey" href="<?php echo wp_logout_url('/login/'); ?>">Logout</a></li>
                    </ul>
                </div>
			<?php } else { ?>
                <div class="accountWelcome"></div>
                
                <div class="account">
                    <ul>
                    <li><a <?php if($pageObj->ID == 44 || $pageObj->ID == 396) echo 'class="active"'; ?> href="/book-advert/">Book Advert</a></li>
                    <li><a class="grey<?php if($pageObj->ID == 38) echo ' active'; ?>" href="/login/">Login/Register</a></li>
                    </ul>
                </div>
			<?php } ?>
        </div>
    </header>
    
    <div class="body">    
        <div class="nav">
            <nav>
            <?php
				$nav = wp_get_nav_menu_items(2);
				
				echo '<div class="menu-header-container"><ul id="menu-header" class="menu">';
				for($n=0; $n<sizeof($nav); $n++) {
					$isPublicSite = (!is_user_logged_in() || $current_user->roles[0] == 'guest');
					$showNavItem = true;
					$showOnPublic = get_field('show_on_public_site', $nav[$n]->object_id);
					if($isPublicSite && $showOnPublic != 'yes') {
						$showNavItem = false;
					}
					
					if($showNavItem) {
						// Raph - Overwrite
						// $publicClass = (($isPublicSite) ? 'class="public"' : '');
						$publicClass = '';
						echo '<li id="menu-item-'.$nav[$n]->object_id.'" '.$publicClass.'><a ';
						if($nav[$n]->object_id == $pageObj->ID || ($nav[$n]->object_id == 14 && $pageObj->ID == 175))
							echo 'class="active" ';
						echo 'href="'.get_permalink($nav[$n]->object_id).'">'.$nav[$n]->title.'</a></li>';
					}
				}
				echo '</ul></div>';
			?>
            </nav>
        </div>