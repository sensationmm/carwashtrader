<?php

	session_start();
	
	if(isset($_POST["action"])) {
		if($_POST["action"] == 'filter') {
			$returnPage = $_POST["filterPage"];
			$_SESSION["cwt_jobSearchTerms"] = $_POST["search_terms"];
			$_SESSION["cwt_jobSearchPostcode"] = $_POST["search_postcode"];
			$_SESSION["cwt_jobSearchOrderBy"] = $_POST["orderby"];
			$_SESSION["cwt_jobSearchOrder"] = $_POST["order"];
			$_SESSION["cwt_setJobFilter"] = $_POST["filterID"];
			
			$_SESSION["cwt_jobFiltered"] = 'true';
		}
		else if($_POST["action"] == 'filterClear') {
			$returnPage = $_POST["filterPage"];
			unset($_SESSION["cwt_jobSearchTerms"]);
			unset($_SESSION["cwt_jobSearchPostcode"]);
			unset($_SESSION["cwt_jobSearchOrderBy"]);
			unset($_SESSION["cwt_jobSearchOrder"]);
			unset($_SESSION["cwt_setJobFilter"]);	
			unset($_SESSION["cwt_jobFiltered"]);
		}
	} else {
		$returnPage = '/jobs/';
		unset($_SESSION["cwt_jobsSearchTerms"]);
		unset($_SESSION["cwt_jobSearchPostcode"]);
		unset($_SESSION["cwt_jobSearchOrderBy"]);
		unset($_SESSION["cwt_jobSearchOrder"]);
		unset($_SESSION["cwt_setJobFilter"]);	
		unset($_SESSION["cwt_jobFiltered"]);
	}
	
	header('Location: '.$returnPage.'#listings');

?>