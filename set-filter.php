<?php

	session_start();
	
	if(isset($_POST["action"])) {
		if($_POST["action"] == 'filter') {
			$returnPage = $_POST["filterPage"];
			$_SESSION["cwt_searchTerms"] = $_POST["search_terms"];
			$_SESSION["cwt_searchPostcode"] = $_POST["search_postcode"];
			$_SESSION["cwt_searchOrderBy"] = $_POST["orderby"];
			$_SESSION["cwt_searchOrder"] = $_POST["order"];
			$_SESSION["cwt_setFilter"] = $_POST["filterID"];
			
			$_SESSION["cwt_filtered"] = 'true';
		}
		else if($_POST["action"] == 'filterClear') {
			$returnPage = $_POST["filterPage"];
			unset($_SESSION["cwt_searchTerms"]);
			unset($_SESSION["cwt_searchPostcode"]);
			unset($_SESSION["cwt_searchOrderBy"]);
			unset($_SESSION["cwt_searchOrder"]);
			unset($_SESSION["cwt_setFilter"]);	
			unset($_SESSION["cwt_filtered"]);
		}
	} else {
		$returnPage = '/home/';
		unset($_SESSION["cwt_searchTerms"]);
		unset($_SESSION["cwt_searchPostcode"]);
		unset($_SESSION["cwt_searchOrderBy"]);
		unset($_SESSION["cwt_searchOrder"]);
		unset($_SESSION["cwt_setFilter"]);	
		unset($_SESSION["cwt_filtered"]);
	}
	
	header('Location: '.$returnPage.'#listings');

?>