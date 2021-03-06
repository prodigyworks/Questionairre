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

	$matches = null;

	$guid = uniqid();

	//If there are input validations, redirect back to the registration form
	if($errflag) {
		$_SESSION['ERRMSG_ARR'] = $errmsg_arr;
		session_write_close();
		header("location: " . $_SERVER['HTTP_REFERER']);
		exit();
	}

	$memberid = $_GET['id'];
	$qry = "UPDATE {$_SESSION['DB_PREFIX']}members " .
			"SET email = '$email', " .
			"firstname = '$fname', " .
			"lastname = '$lname', " .
			"address = '$address1', " .
			"city = '$city', " .
			"county = '$county', " .
			"country = '$country', " .
			"postcode = '$postcode', " .
			"imageid = $imageid, " .
			"lastaccessdate = NOW(), " .
			"passwd = '" . md5($password) . "', metamodifieddate = NOW(), metamodifieduserid = " . getLoggedOnMemberID() . " " .
			"WHERE member_id = " . $_GET['id'];
	$result = mysql_query($qry);

	if (! $result) {
		logError("UPDATE members failed:" . mysql_error());
	}

	$_SESSION['SESS_FIRST_NAME'] = $fname;
	$_SESSION['SESS_LAST_NAME'] = $lname;
	$_SESSION['SESS_IMAGE_ID'] = $imageid;

	sendRoleMessage("ADMIN", "User Amendment", "<h3>User amendment.</h3><br>Your details have been amended by the System Administration.<br>Your password has been changed to: <i>$password</i>.");
	sendUserMessage($memberid, "User Amendment", "<h3>User amendment.</h3><br>Your details have been amended by the System Administration.<br>Your password has been changed to: <i>$password</i>.");

	header("location: system-register-amend.php");
?>