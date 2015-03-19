<?php
	//Include database connection details
	require_once('system-db.php');
	require_once('sqlfunctions.php');

	start_db();
	
	try {
		$imageid = getImageData("image", 100, 100);

	} catch (Exception $e) {
		$errmsg_arr[] = $e->getMessage();
	}

	$courseid = 1;
	$redemptioncode = clean($_POST['redemptioncode']);
	$address1 = clean($_POST['address1']);
	$city = clean($_POST['city']);
	$postcode = clean($_POST['postcode']);
	$county = clean($_POST['county']);
	$country = clean($_POST['country']);

	//Array to store validation errors
	$errmsg_arr = array();

	//Validation error flag
	$errflag = false;

	//Sanitize the POST values
	$fname = clean($_POST['fname']);
	$lname = clean($_POST['lname']);
	$password = clean($_POST['password']);
	$cpassword = clean($_POST['cpassword']);
	$email = clean($_POST['email']);
	$cemail = clean($_POST['confirmemail']);
	$membershiptype = clean($_POST['membershiptype']);

	$mobile = "";

	//Input Validations
	if($fname == '') {
		$errmsg_arr[] = 'First name missing';
		$errflag = true;
	}
	if($lname == '') {
		$errmsg_arr[] = 'Last name missing';
		$errflag = true;
	}

	$login = clean($_POST['login']);
	if($login == '') {
		$errmsg_arr[] = 'Login ID missing';
		$errflag = true;
	}

	if($password == '') {
		$errmsg_arr[] = 'Password missing';
		$errflag = true;
	}

	if($cpassword == '') {
		$errmsg_arr[] = 'Confirm password missing';
		$errflag = true;
	}

	if( strcmp($password, $cpassword) != 0 ) {
		$errmsg_arr[] = 'Passwords do not match';
		$errflag = true;
	}

	if( strcmp($email, $cemail) != 0 ) {
		$errmsg_arr[] = 'Email addresses do not match';
		$errflag = true;
	}

	//If there are input validations, redirect back to the registration form
	if($errflag) {
		$_SESSION['ERRMSG_ARR'] = $errmsg_arr;
		session_write_close();
		header("location: " . $_SERVER['HTTP_REFERER']);
		exit();
	}

	//Check for duplicate login ID
	$qry = "SELECT * FROM {$_SESSION['DB_PREFIX']}members WHERE login='$login'";
	$result = mysql_query($qry);
	if($result) {
		if(mysql_num_rows($result) > 0) {
			$errmsg_arr[] = 'Login ID already in use';
			$errflag = true;
		}
		@mysql_free_result($result);
		
	} else {
		logError($qry . " - " . mysql_error());
	}

	//Check for duplicate login ID
	if($login != '') {
		$qry = "SELECT * FROM {$_SESSION['DB_PREFIX']}members WHERE login='$login'";
		$result = mysql_query($qry);
		if($result) {
			if(mysql_num_rows($result) > 0) {
				$errmsg_arr[] = 'Login ID already in use';
				$errflag = true;
			}
			@mysql_free_result($result);
		}
	}
	
	$guid = uniqid();
	$memberid = 0;
	
	if (! $errflag) {
		$foundredemption = false;
		$sql = "SELECT id
				FROM {$_SESSION['DB_PREFIX']}redemptioncode
				WHERE code = '$redemptioncode'
				AND redeemed = 'N'";
	
		$result = mysql_query($sql);
	
		if (! $result) {
			logError($sql . " - " . mysql_error());
		}
	
		while (($member = mysql_fetch_assoc($result))) {
			$foundredemption = true;
			$redemptioncodeid = $member['id'];
	
			$sql = "UPDATE {$_SESSION['DB_PREFIX']}redemptioncode SET
					redeemed = 'Y'
					WHERE id = '$redemptioncodeid'";
	
			$itemresult = mysql_query($sql);
	
			if (! $itemresult) {
				logError($sql . " - " . mysql_error());
			}
		}

		if (! $foundredemption) {
			$errmsg_arr[] = 'Code cannot be redeemed';
			$errflag = true;
		}
	}

	//If there are input validations, redirect back to the registration form
	if($errflag) {
		$_SESSION['ERRMSG_ARR'] = $errmsg_arr;
		session_write_close();
		header("location: " . $_SERVER['HTTP_REFERER']);
		exit();
	}
	
	$passwd = md5($password);
	$fullname = $fname . " " . $lname;
	$origmemberid = getLoggedOnMemberID();

	$qry = "INSERT INTO {$_SESSION['DB_PREFIX']}members
			(
			firstname, lastname, fullname, login, passwd, email, imageid, accepted, guid, status, membershiptype,
			redemptioncodeid, address, city, county, country, postcode,
			metacreateddate, metacreateduserid, metamodifieddate, metamodifieduserid
			)
			VALUES
			(
			'$fname','$lname', '$fullname', '$login', '$passwd', '$email', $imageid, 'Y', '$guid', 'Y', '$membershiptype',
			'$redemptioncodeid', '$address1', '$city', '$county', '$country', '$postcode',
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
	
	sendRoleMessage("ADMIN", "User Registration", "User $login has been registered as a user.<br>Password : $password");
	sendUserMessage($memberid, "User Registration", "<h3>Welcome $fname $lname.</h3><br>You have been invited to become a member of 'iAfrica Database'.<br>Please click on the <a href='" . getSiteConfigData()->domainurl . "/index.php'>link</a> to activate your account.<br><br><h4>Login details</h4>User ID : $login<br>Password : $password");
	
	mysql_query("COMMIT");

	header("location: system-register-success.php");
?>