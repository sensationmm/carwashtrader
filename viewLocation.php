<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" style="width:100%;height:100%;">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<base href="/wp-content/themes/carwashtrader/" />
<title>Carwash Trader</title>
<link rel="stylesheet" href="assets/css/style.css" />
<link rel="stylesheet" href="assets/css/lightbox.css" />
<link rel="shortcut icon" type="image/x-icon" href="assets/images/favicon.ico" />
<script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jquery/1.11.1/jquery.min.js"></script>
<script type="text/javascript" src="js/site-functions.js"></script>
<script src="http://maps.googleapis.com/maps/api/js?key=AIzaSyCYFmKVV5rHF2--oM2bj0LtqfFsaKcyijc&sensor=false" type="text/javascript"></script>
<script src="js/map.js" type="text/javascript"></script>
</head>
<body style="width:100%;height:100%;" onload="initialize('<?php echo $_GET["lat"].','.$_GET["lng"]; ?>', 'map_canvas', false);">
<div class="mainMap" id="map_canvas" style="width:100%;height:100%;"></div>
</body>
</html>