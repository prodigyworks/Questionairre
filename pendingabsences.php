<?php
	require_once("absencelib.php");
	
	$crud = new AbsenceCrud();
	$crud->allowAdd = false;
	$crud->allowEdit = false;
	
	if (isUserInRole("ADMIN") || isUserInRole("MANAGEMENT")) {
		$crud->allowEdit = true;
	}
	
	if (isset($_GET['id'])) {
		$crud->sql = 
			"SELECT A.*, " .
			"B.firstname, B.lastname " .
			"FROM {$_SESSION['DB_PREFIX']}absence A " .
			"INNER JOIN {$_SESSION['DB_PREFIX']}members B " .
			"ON B.member_id = A.memberid " .
			"WHERE B.member_id = " . $_GET['id'] . " " .
			"AND A.acceptedby IS NULL " .
			"AND A.rejectedby IS NULL";
		
	} else {
		$crud->sql = 
			"SELECT A.*, " .
			"B.firstname, B.lastname " .
			"FROM {$_SESSION['DB_PREFIX']}absence A " .
			"INNER JOIN {$_SESSION['DB_PREFIX']}members B " .
			"ON B.member_id = A.memberid  " .
			"WHERE A.acceptedby IS NULL " .
			"AND A.rejectedby IS NULL";
	}
	
	$crud->sql = ($crud->sql);
	
	$crud->run();
?>