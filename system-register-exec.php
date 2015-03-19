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
	
	logError("START 1", false);
	
	$p = new paypal_class(); // paypal class
	logError("START 2", false);
	$p->admin_mail 	= EMAIL_ADD; // set notification email
	logError("START 2.1", false);
	$courseid = 1;
	$coursename = "Public Questionnaire";
	$amount = 100.0;
	$qty = 1;
	logError("START 2.2", false);
	$address1 = clean($_POST['address1']);
	logError("START 2.21", false);
	$city = clean($_POST['city']);
	logError("START 2.22", false);
	$postcode = clean($_POST['postcode']);
	logError("START 2.3", false);
	$county = clean($_POST['county']);
	$country = clean($_POST['country']);
	$currencycode = "GBP";
	logError("START 3", false);
	
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
	logError("START 4", false);
	
	//Input Validations
	if($fname == '') {
		$errmsg_arr[] = 'First name missing';
		$errflag = true;
	}
	if($lname == '') {
		$errmsg_arr[] = 'Last name missing';
		$errflag = true;
	}
	
	if (! isset($_GET['id'])) {
		$login = clean($_POST['login']);
		if($login == '') {
			$errmsg_arr[] = 'Login ID missing';
			$errflag = true;
		}
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
	logError("START 5", false);
	
	$guid = uniqid();
	$memberid = 0;
	//If there are input validations, redirect back to the registration form
	if($errflag) {
		$_SESSION['ERRMSG_ARR'] = $errmsg_arr;
		session_write_close();
		header("location: " . $_SERVER['HTTP_REFERER']);
		exit();
	}
	
	if (! isset($_GET['id'])) {
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
		
	logError("START 6", false);
		//Create INSERT query
		$qry = "INSERT INTO {$_SESSION['DB_PREFIX']}members " .
				"(firstname, lastname, login, passwd, email, imageid, accepted, guid, status, metacreateddate, metacreateduserid, metamodifieddate, metamodifieduserid) " .
				"VALUES" .
				"('$fname','$lname','$login', '".md5($_POST['password'])."', '$email', $imageid, 'Y', '$guid', 'Y', NOW(), " . getLoggedOnMemberID() . ", NOW(), " .  getLoggedOnMemberID() . ")";
		$result = @mysql_query($qry);
		$memberid = mysql_insert_id();
		
		if (! $result) {
			logError("$qry - " . mysql_error());
		}
	logError("START 7", false);
		
		//Create INSERT query
		$qry = "INSERT INTO {$_SESSION['DB_PREFIX']}userroles(memberid, roleid, metacreateddate, metacreateduserid, metamodifieddate, metamodifieduserid) VALUES($memberid, 'PUBLIC', NOW(), " . getLoggedOnMemberID() . ", NOW(), " .  getLoggedOnMemberID() . ")";
		$result = @mysql_query($qry);
		$qry = "INSERT INTO {$_SESSION['DB_PREFIX']}userroles(memberid, roleid, metacreateddate, metacreateduserid, metamodifieddate, metamodifieduserid) VALUES($memberid, 'USER', NOW(), " . getLoggedOnMemberID() . ", NOW(), " .  getLoggedOnMemberID() . ")";
		$result = @mysql_query($qry);
		
	logError("START 8", false);
		
		if (isset($_POST['accounttype'])) {
			$accountrole = $_POST['accounttype'];

			$qry = "INSERT INTO {$_SESSION['DB_PREFIX']}userroles(memberid, roleid, metacreateddate, metacreateduserid, metamodifieddate, metamodifieduserid) VALUES($memberid, '$accountrole', NOW(), " . getLoggedOnMemberID() . ", NOW(), " .  getLoggedOnMemberID() . ")";
			$result = @mysql_query($qry);
		}
	logError("START 9", false);
		
		$_SESSION['SESS_FIRST_NAME'] = $fname;
		$_SESSION['SESS_LAST_NAME'] = $lname;
		$_SESSION['SESS_IMAGE_ID'] = $imageid;
		
		sendRoleMessage("ADMIN", "User Registration", "User " . $login . " has been registered as a user.<br>Password : " . $_POST['password']);
		sendUserMessage($memberid, "User Registration", "<h3>Welcome $fname $lname.</h3><br>You have been invited to become a member of 'iAfrica Database'.<br>Please click on the <a href='" . getSiteConfigData()->domainurl . "/index.php'>link</a> to activate your account.<br><br><h4>Login details</h4>User ID : $login<br>Password : " . $_POST['password']);
	logError("START 10", false);
		
		if($result) {
			$itemresult = mysql_query(
					"INSERT INTO {$_SESSION['DB_PREFIX']}purchases 
					 (
					 productid, productname, quantity, 
					 amount, fname, lname, address, 
					 city, county, postcode, country, 
					 email, status, posteddate
					 ) 
					 VALUES 
					 (
					 '$courseid', '$coursename', '$qty', 
					 '$amount', '$fname', '$lname', '$address1', 
					 '$city', '$county', '$postcode', '$country', 
					 '$email', 'pending', NOW()
					 )
					 ");
			
	logError("START 11", false);
			if (! $itemresult) {
				logError(mysql_error());
			}
	logError("START 12", false);
			
			$p->add_field('business', PAYPAL_EMAIL_ADD); // Call the facilitator eaccount
			$p->add_field('cmd', "_cart"); // cmd should be _cart for cart checkout
			$p->add_field('upload', '1');
			$p->add_field('return', getSiteConfigData()->domainurl . "/system-register-success.php"); // return URL after the transaction got over
			$p->add_field('cancel_return', getSiteConfigData()->domainurl . "/system-register-cancel.php"); // cancel URL if the trasaction was cancelled during half of the transaction
			$p->add_field('notify_url', getSiteConfigData()->domainurl . "/system-register-ipn.php"); // Notify URL which received IPN (Instant Payment Notification)
			$p->add_field('currency_code', $currencycode);
			$p->add_field('invoice', mysql_insert_id());
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
			$p->dump_fields(); // Show the posted values for a reference, comment this line before app goes live	
		
		} else {
			logError("1 Query failed:" . mysql_error());
		}
		
	} else {
		$memberid = $_GET['id'];
		$qry = "UPDATE {$_SESSION['DB_PREFIX']}members " .
				"SET email = '$email', " .
				"firstname = '$fname', " .
				"lastname = '$lname', " .
				"imageid = $imageid, " .
				"lastaccessdate = NOW(), ";
				
		if (isset($_POST['postcode'])) {
			$qry .= "postcode = '$postcode', ";
		}
		
		$qry .= "passwd = '" . md5($password) . "', metamodifieddate = NOW(), metamodifieduserid = " . getLoggedOnMemberID() . " " .
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
	}
	
	//Check whether the query was successful or not
?>