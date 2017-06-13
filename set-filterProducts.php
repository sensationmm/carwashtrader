<?php

	session_start();
	
	if(isset($_POST["action"])) {
		if($_POST["action"] == 'filter') {
			$returnPage = $_POST["filterPage"];
			$_SESSION["cwt_productSearchTerms"] = $_POST["search_terms"];
			$_SESSION["cwt_productSearchPostcode"] = $_POST["search_postcode"];
			$_SESSION["cwt_productSearchOrderBy"] = $_POST["orderby"];
			$_SESSION["cwt_productSearchOrder"] = $_POST["order"];
			$_SESSION["cwt_productSetFilter"] = $_POST["filterID"];
			
			$_SESSION["cwt_productFiltered"] = 'true';
		}
		else if($_POST["action"] == 'filterClear') {
			$returnPage = $_POST["filterPage"];
			unset($_SESSION["cwt_productSearchTerms"]);
			unset($_SESSION["cwt_productSearchPostcode"]);
			unset($_SESSION["cwt_productSearchOrderBy"]);
			unset($_SESSION["cwt_productSearchOrder"]);
			unset($_SESSION["cwt_productSetFilter"]);	
			unset($_SESSION["cwt_productFiltered"]);
		}
	} else {
		$returnPage = '/products/';
		unset($_SESSION["cwt_productSearchTerms"]);
		unset($_SESSION["cwt_productSearchPostcode"]);
		unset($_SESSION["cwt_productSearchOrderBy"]);
		unset($_SESSION["cwt_productSearchOrder"]);
		unset($_SESSION["cwt_productSetFilter"]);	
		unset($_SESSION["cwt_productFiltered"]);
	}
	
	header('Location: '.$returnPage.'#listings');

?>