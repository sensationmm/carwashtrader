			
            
            <?php
				global $isMyCWT, $menuParent, $menuTitle, $pageObj;
				$subpages = get_pages('parent='.$menuParent.'&post_type=page&sort_column=menu_order&sort_order=ASC');
				
				
			?>
        	<div class="filter">
            	<div class="header"><a href="<?php echo get_permalink($menuParent); ?>"><?php echo $menuTitle; ?></a></div>
                <ul>
                <?php
					for($i=0; $i<sizeof($subpages); $i++)
					{
						if(myCWTShow($subpages[$i]->ID, $current_user->roles[0], $isMyCWT)) {
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