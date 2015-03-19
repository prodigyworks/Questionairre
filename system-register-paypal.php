<?php
	//Include database connection details
	require_once('system-db.php');
	require_once('sqlfunctions.php');
	require_once("paypal_class.php");

	start_db();

	try {
		$imageid = getImageData("image", 100, 100);

	} catch (Exception $e) {
		$errmsg_arr[] = $e->getMessage();
	}

	$courseid = 1;
	$coursename = "Public Questionnaire";
	$amount = 100.0;
	$qty = 1;
	$address1 = clean($_POST['address1']);
	$city = clean($_POST['city']);
	$postcode = clean($_POST['postcode']);
	$county = clean($_POST['county']);
	$country = clean($_POST['country']);
	$currencycode = "GBP";

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

	$memberid = 0;
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

	$itemresult = mysql_query(
			"INSERT INTO {$_SESSION['DB_PREFIX']}purchases
			 (
			 productid, productname, quantity,
			 amount, fname, lname, address,
			 city, county, postcode, country,
			 email, status, posteddate,
			 login, imageid, passwd,
			 membershiptype
			 )
			 VALUES
			 (
			 '$courseid', '$coursename', '$qty',
			 '$amount', '$fname', '$lname', '$address1',
			 '$city', '$county', '$postcode', '$country',
			 '$email', 'pending', NOW(),
			 '$login', $imageid, '$password',
			 '$membershiptype'
			 )
			 ");

	if (! $itemresult) {
		logError(mysql_error());
	}
	
	$invoiceid = mysql_insert_id();
	
	mysql_query("COMMIT");

	$p = new paypal_class(); // paypal class
	$p->admin_mail 	= getSiteConfigData()->paypalnotifieremail; // set notification email
	$p->add_field('business', getSiteConfigData()->paypalfacilitatoremail); // Call the facilitator eaccount
	$p->add_field('cmd', "_cart"); // cmd should be _cart for cart checkout
	$p->add_field('upload', '1');
	$p->add_field('return', getSiteConfigData()->domainurl . "/system-register-paypal-success.php"); // return URL after the transaction got over
	$p->add_field('cancel_return', getSiteConfigData()->domainurl . "/system-register-cancel.php"); // cancel URL if the trasaction was cancelled during half of the transaction
	$p->add_field('notify_url', getSiteConfigData()->domainurl . "/system-register-paypal-ipn.php"); // Notify URL which received IPN (Instant Payment Notification)
	$p->add_field('currency_code', $currencycode);
	$p->add_field('invoice', $invoiceid);
	$p->add_field('item_name_1', $coursename);
	$p->add_field('item_number_1', $courseid);
	$p->add_field('quantity_1', $qty);
	$p->add_field('amount_1', $amount);
	$p->add_field('first_name', $fname);
	$p->add_field('last_name', $lname);
	$p->add_field('address1', $address1);
	$p->add_field('city', $city);
	$p->add_field('state', $county);
	$p->add_field('country', $country);
	$p->add_field('zip', $postcode);
	$p->add_field('email', $email);
	$p->submit_paypal_post(); // POST it to paypal
//	$p->dump_fields(); // Show the posted values for a reference, comment this line before app goes live
?>