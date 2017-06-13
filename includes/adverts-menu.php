<?php 

	$pageIdentifier = '';



	switch($pageObj->ID) {

		case 14: case 175: 

			$pageIdentifier = 'car-wash-listings'; break;

		case 16: case 27: 

			$pageIdentifier = 'car-wash-for-sale-listings'; break;

		case 20: case 327: 

			$pageIdentifier = 'supplier-listings'; break;

		case 22: 

			$pageIdentifier = 'product-listings'; break;

		case 24: case 263: 

			$pageIdentifier = 'job-listings'; break;
		
		case 26: 

			$pageIdentifier = 'about-us'; break;
			
		case 28: 

		$pageIdentifier = 'contact-us'; break;
		
		case 44: 

		$pageIdentifier = 'book-advert'; break;
		
		
	}



	echo outputAdverts($pageIdentifier, 'side-bar', 3); 

?>

<!-- <div class="advert"><img src="assets/images/adspace-skyscraper.gif" /></div> -->