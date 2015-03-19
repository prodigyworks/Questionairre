<?php
	include("system-db.php");

	start_db();

	$id = $_POST['invoice'];
	$transactionid  = $_POST["txn_id"];
	$sql = "UPDATE {$_SESSION['DB_PREFIX']}purchases SET 
			status = 'complete',
			transactionid = '$transactionid'
			WHERE id = $id";

	if (! mysql_query($sql)) {
		logError($sql . " - " . mysql_error());
	}

	$sql = "SELECT *
			FROM {$_SESSION['DB_PREFIX']}purchases
			WHERE id = $id";

	$result = mysql_query($sql);

	if (! $result) {
		logError($sql . " - " . mysql_error());
	}

	while (($member = mysql_fetch_assoc($result))) {
		$firstname = mysql_escape_string($member['fname']);
		$lastname = mysql_escape_string($member['lname']);
		$login = mysql_escape_string($member['login']);
		$address = mysql_escape_string($member['address']);
		$city = mysql_escape_string($member['city']);
		$county = mysql_escape_string($member['county']);
		$country = mysql_escape_string($member['country']);
		$postcode = mysql_escape_string($member['postcode']);
		$email = mysql_escape_string($member['email']);
		$imageid = mysql_escape_string($member['imageid']);
		$passwd = mysql_escape_string($member['passwd']);
		$membershiptype = mysql_escape_string($member['membershiptype']);
		$origmemberid = getLoggedOnMemberID();
		$fullname = $firstname . " " . $lastname;
		$guid = uniqid();
	}
	
	$password = md5($passwd);

	$qry = "INSERT INTO {$_SESSION['DB_PREFIX']}members
			(
			firstname, lastname, fullname, login, passwd, email, imageid, accepted, guid, status, membershiptype,
			address, city, county, country, postcode,
			metacreateddate, metacreateduserid, metamodifieddate, metamodifieduserid
			)
			VALUES
			(
			'$firstname','$lastname', '$fullname', '$login', '$password', '$email', $imageid, 'Y', '$guid', 'Y',  '$membershiptype',
			'$address', '$city', '$county', '$country', '$postcode',
			NOW(), $origmemberid, NOW(), $origmemberid
			)";
	$result = @mysql_query($qry);
	$memberid = mysql_insert_id();

	if (! $result) {
		logError("$qry - " . mysql_error());
	}

	$qry = "INSERT INTO {$_SESSION['DB_PREFIX']}userroles(memberid, roleid, metacreateddate, metacreateduserid, metamodifieddate, metamodifieduserid) VALUES($memberid, 'PUBLIC', NOW(), $origmemberid, NOW(), $memberid)";
	$result = @mysql_query($qry);
	$qry = "INSERT INTO {$_SESSION['DB_PREFIX']}userroles(memberid, roleid, metacreateddate, metacreateduserid, metamodifieddate, metamodifieduserid) VALUES($memberid, 'USER', NOW(), $origmemberid, NOW(), $memberid)";
	$result = @mysql_query($qry);
	$qry = "INSERT INTO {$_SESSION['DB_PREFIX']}userroles(memberid, roleid, metacreateddate, metacreateduserid, metamodifieddate, metamodifieduserid) VALUES($memberid, 'PAYPAL', NOW(), $origmemberid, NOW(), $memberid)";
	$result = @mysql_query($qry);

	if ($membershiptype == "N") {
		$qry = "INSERT INTO {$_SESSION['DB_PREFIX']}userroles(memberid, roleid, metacreateddate, metacreateduserid, metamodifieddate, metamodifieduserid) VALUES($memberid, 'NHS', NOW(), $origmemberid, NOW(), $memberid)";
		$result = @mysql_query($qry);

	} else {
		$qry = "INSERT INTO {$_SESSION['DB_PREFIX']}userroles(memberid, roleid, metacreateddate, metacreateduserid, metamodifieddate, metamodifieduserid) VALUES($memberid, 'NONENHS', NOW(), $origmemberid, NOW(), $memberid)";
		$result = @mysql_query($qry);
	}

	sendRoleMessage("ADMIN", "User Registration", "User $login has been registered as a user.<br>Password : " . $_POST['password']);
	sendUserMessage($memberid, "User Registration", "<h3>Welcome $firstname $lastname.</h3><br>You have been invited to become a member of 'iAfrica Database'.<br>Please click on the <a href='" . getSiteConfigData()->domainurl . "/index.php'>link</a> to activate your account.<br><br><h4>Login details</h4>User ID : $login<br>Password : " . $_POST['password']);
	
	mysql_query("COMMIT");
?>




