<?php

	session_start();
	
	if(isset($_POST["action"])) {
		if($_POST["action"] == 'filter') {
			$returnPage = $_POST["filterPage"];
			$_SESSION["cwt_supplierSearchTerms"] = $_POST["search_terms"];
			$_SESSION["cwt_supplierSearchPostcode"] = $_POST["search_postcode"];
			$_SESSION["cwt_supplierSearchOrderBy"] = $_POST["orderby"];
			$_SESSION["cwt_supplierSearchOrder"] = $_POST["order"];
			$_SESSION["cwt_supplierSetFilter"] = $_POST["filterID"];
			
			$_SESSION["cwt_supplierFiltered"] = 'true';
		}
		else if($_POST["action"] == 'filterClear') {
			$returnPage = $_POST["filterPage"];
			unset($_SESSION["cwt_supplierSearchTerms"]);
			unset($_SESSION["cwt_supplierSearchPostcode"]);
			unset($_SESSION["cwt_supplierSearchOrderBy"]);
			unset($_SESSION["cwt_supplierSearchOrder"]);
			unset($_SESSION["cwt_supplierSetFilter"]);	
			unset($_SESSION["cwt_supplierFiltered"]);
		}
	} else {
		$returnPage = '/suppliers/';
		unset($_SESSION["cwt_supplierSearchTerms"]);
		unset($_SESSION["cwt_supplierSearchPostcode"]);
		unset($_SESSION["cwt_supplierSearchOrderBy"]);
		unset($_SESSION["cwt_supplierSearchOrder"]);
		unset($_SESSION["cwt_supplierSetFilter"]);	
		unset($_SESSION["cwt_supplierFiltered"]);
	}
	
	header('Location: '.$returnPage.'#listings');

?>