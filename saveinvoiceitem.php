<?php
	//Include database connection details
	require_once('system-db.php');
	require_once("sqlprocesstoarray.php");
	
	start_db();
	
	$invoiceid = $_POST['invoiceid'];
	$id = $_POST['id'];
	$qty = ($_POST['qty']);
	$unitprice = ($_POST['unitprice']);
	$vatrate = $_POST['vatrate'];
	$vat = $_POST['vat'];
	$total = $_POST['total'];
	$templateid = ($_POST['templateid']);
	
	$qry = "SELECT caseid " .
			"FROM {$_SESSION['DB_PREFIX']}invoices B " .
			"WHERE B.id = $invoiceid";
	$result = mysql_query($qry);

	if($result) {
		while (($member = mysql_fetch_assoc($result))) {
			$caseid = $member['caseid'];
			
			addAuditLog("I", "U", $caseid);
		}
	}
	
	if ($id == "") {
		$qry = "INSERT INTO {$_SESSION['DB_PREFIX']}invoiceitems " .
				"(invoiceid, qty, unitprice, vatrate, vat, total, " .
				"templateid, metacreateddate, metacreateduserid, metamodifieddate, metamodifieduserid) " .
				"VALUES " .
				"($invoiceid, '$qty', '$unitprice', $vatrate, '$vat', $total, " .
				"'$templateid', NOW(), " . getLoggedOnMemberID() . ", NOW(), " .  getLoggedOnMemberID() . ")";
		$result = mysql_query($qry);
		
		if (! $result) {
			logError($qry . " - " . mysql_error());
		}
		
	} else {
		$qry = "UPDATE {$_SESSION['DB_PREFIX']}invoiceitems SET " .
				"qty = '$qty', " .
				"unitprice = '$unitprice', " .
				"vatrate = '$vatrate', " .
				"vat = '$vat', " .
				"total = $total, " .
				"templateid = '$templateid', metamodifieddate = NOW(), metamodifieduserid = " . getLoggedOnMemberID() . " " .
				"WHERE id = $id";
		$result = mysql_query($qry);
		
		if (! $result) {
			logError($qry . " - " . mysql_error());
		}
	}
	
	$qry = "UPDATE {$_SESSION['DB_PREFIX']}invoices SET " .
			"total = (SELECT SUM(B.total) FROM {$_SESSION['DB_PREFIX']}invoiceitems B WHERE B.invoiceid = $invoiceid), metamodifieddate = NOW(), metamodifieduserid = " . getLoggedOnMemberID() . " " .
			"WHERE id = $invoiceid";
	$result = mysql_query($qry);
	
	if (! $result) {
		logError($qry . " - " . mysql_error());
	}
	
	$qry = "SELECT A.*, C.total AS headertotal, B.name  " .
			"FROM {$_SESSION['DB_PREFIX']}invoiceitems A " .
			"INNER JOIN {$_SESSION['DB_PREFIX']}invoices C " .
			"ON C.id = A.invoiceid " .
			"LEFT OUTER JOIN {$_SESSION['DB_PREFIX']}invoiceitemtemplates B " .
			"ON B.id = A.templateid " .
			"WHERE A.invoiceid = $invoiceid " .
			"ORDER BY A.id";
	
	$json = new SQLProcessToArray();
	
	echo json_encode($json->fetch($qry));
?>