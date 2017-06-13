

<?php
	global $setFilter;
	if(0 != $setFilter) {
		echo '<script type="text/javascript">';
		echo '$(document).ready(function() {';
		echo '$(".filterMobile .header").css("text-transform","uppercase").css("color","#cb2b34").html("Listings filtered");';
		echo '})';
		echo '</script>';
	}
?>

<nav class="header">
	<a href="/" title="Go to Homepage"><img src="assets/images/mobile-home.png" width="30" height="30" /></a>
	<div class="navToggle"><img src="assets/images/mobile-nav.png" width="30" height="30" /></div>

<?php if ( is_user_logged_in() ) { ?>
	<div class="accountToggle"><img src="assets/images/mobile-account.png" width="30" height="30" /></div>
<?php } ?>
</nav>

<?php global $pageObj, $isMyCWT; ?>
<nav class="mobile">
	<ul>

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
				echo '<li id="menu-item-'.$nav[$n]->object_id.'" '.(($isPublicSite) ? 'class="public"' : '').'><a ';
				if($nav[$n]->object_id == $pageObj->ID || ($nav[$n]->object_id == 14 && $pageObj->ID == 175))
					echo 'class="active" ';
				echo 'href="'.get_permalink($nav[$n]->object_id).'">'.$nav[$n]->title.'</a></li>';
			}
		}
	?>
    <li><a <?php if($pageObj->ID == 44 || $pageObj->ID == 396) echo 'class="active"'; ?> href="/book-advert/">Book Advert</a></li>
<?php if ( is_user_logged_in() ) { ?>
    <li><a href="<?php echo wp_logout_url('/login/'); ?>">Log Out</a></li>
<?php } else { ?>
	<li><a class="grey<?php if($pageObj->ID == 38) echo ' active'; ?>" href="/login/">Login/Register</a></li>
<?php } ?>
	</ul>
	<?php
		echo '</ul></div>';
	?>
</nav>


<nav class="account">
	<div class="filter">
    	<?php
			global $isMyCWT, $menuParent, $menuTitle, $current_user;
			$subpages = get_pages('parent=40&post_type=page&sort_column=menu_order&sort_order=ASC');
		?>
        <ul>
		<li <?php if($pageObj->ID == 40) echo 'class="active"'; ?>><a href="/my-cwt/">My CWT</a></li>
        <?php
			for($i=0; $i<sizeof($subpages); $i++)
			{
				if(myCWTShow($subpages[$i]->ID, $current_user->roles[0], 'true')) {
					echo '<li';
					if($subpages[$i]->ID == $pageObj->ID || $subpages[$i]->ID == $pageObj->post_parent)
						echo ' class="active" ';
					echo '>';
					echo '<a href="'.get_permalink($subpages[$i]->ID).'" title="Go to '.$subpages[$i]->post_title.'">';
					echo $subpages[$i]->post_title.'</a>';
					echo '</li>';
				}
			}
		?>
        </ul>
    </div>
</nav>

<div class="mobileMask"></div>