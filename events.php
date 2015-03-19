<?php 
	include "system-db.php";
	
	start_db();
	
	$startdate = ($_GET['from']);
	$enddate = ($_GET['to']);
	
	$sql ="SELECT A.id, A.startdatetime, A.enddatetime, A.vehicleid, A.ordernumber, A.bookingtype, 
		   B.name AS drivername, 
		   C.registration AS vehiclename, C.description, 
		   D.description AS trailername,
		   E.fgcolour, E.bgcolour
		   FROM {$_SESSION['DB_PREFIX']}booking A 
		   LEFT OUTER JOIN {$_SESSION['DB_PREFIX']}driver B 
		   ON B.id = A.driverid 
		   LEFT OUTER JOIN {$_SESSION['DB_PREFIX']}vehicle C 
		   ON C.id = A.vehicleid 
		   LEFT OUTER JOIN {$_SESSION['DB_PREFIX']}trailer D 
		   ON D.id = A.trailerid
		   LEFT OUTER JOIN {$_SESSION['DB_PREFIX']}bookingstatus E 
		   ON E.id = A.statusid
		   WHERE ((A.startdatetime >= '$startdate' AND A.startdatetime <= '$enddate') OR (A.enddatetime >= '$startdate' AND A.enddatetime <= '$enddate'))
		   AND A.statusid IN (3, 4, 5, 6, 7, 8, 9)";
	$result = mysql_query($sql);
	$first = true;
	$json = array();
	
	//Check whether the query was successful or not
	if($result) {
		while (($member = mysql_fetch_assoc($result))) {
			array_push(
					$json, 
					array(
							"id" => $member['id'],
							"color" => $member['bgcolour'],
							"textColor" => $member['fgcolour'],
							"start_date" => $member['startdatetime'],
							"end_date" => $member['enddatetime'],
							"text" => $member['vehiclename'] . " - " . $member['drivername'] . " - " . $member['trailername'],
							"section_id" => $member['vehicleid']
						)
				);
		}

	} else {
		logError($sql . " - " . mysql_error());
	}
	
	echo json_encode($json);
?>