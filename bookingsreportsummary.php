<?php
	require_once('pdfreport.php');
	require_once('system-db.php');
	
	class BookingSummaryReport extends PDFReport {
		var $y = 35;
		
		function newPage() {
			global $y;
			
			$this->addPage();
			
			$this->addHeading( 10, 6, "Report Bookings - Summary");
			$this->Image("images/logomainmed.png", 150, 2);
			$this->Image("images/report-footer.png", 55, 280);
				
			$y = $this->GetY() + 4;
			
			$this->addText(10, $y, "From Date : ", 8, 3, 'B', 30);
			$y = $this->addText(40, $y, (isset($_POST['datefrom'])) ? $_POST['datefrom'] : "");
			
			$this->addText(10, $y, "To Date : ", 8, 3, 'B', 30);
			$y = $this->addText(40, $y, (isset($_POST['datefrom'])) ? $_POST['dateto'] : "");

			if (isset($_POST['status']) && $_POST['status'] != "") {
				$this->addText(10, $y, "Status : ", 8, 3, 'B', 30);
				$y = $this->addText(40, $y, $_POST['datefrom'] == "A" ? "Accepted" : "Planned");
			}
			
			if (isset($_POST['vehicleid']) && $_POST['vehicleid'] != "0") {
				$sql = "SELECT description  " .
						"FROM {$_SESSION['DB_PREFIX']}vehicle " .
						"WHERE id = " . $_POST['vehicleid'] . " ";
				$result = mysql_query($sql);
			
				if ($result) {
					while (($member = mysql_fetch_assoc($result))) {
						$this->addText(10, $y, "Vehicle : ", 8, 3, 'B', 30);
						$y = $this->addText(40, $y, $member['description']);
					}
					
				} else {
					logError($sql);
				}
			}

			if (isset($_POST['driverid']) && $_POST['driverid'] != "0") {
				$sql = "SELECT name  " .
						"FROM {$_SESSION['DB_PREFIX']}driver " .
						"WHERE id = " . $_POST['driverid'] . " ";
				$result = mysql_query($sql);
			
				if ($result) {
					while (($member = mysql_fetch_assoc($result))) {
						$this->addText(10, $y, "Driver : ", 8, 3, 'B', 30);
						$y = $this->addText(40, $y, $member['name']);
					}
					
				} else {
					logError($sql);
				}
			}
			
		    $this->SetFont('Arial','', 6);
			$cols=array( "Registration"    => 18,
			             "Phone"  => 25,
			             "Trailer"  => 30,
			             "Driver"  => 35,
			             "Date / Time"  => 24,
			             "Delivery Point"  => 58);
		
			$this->addCols( 30, $cols);
			$cols=array( "Registration"    => "L",
			             "Phone"  => "L",
			             "Trailer"  => "L",
			             "Driver"  => "L",
			             "Date / Time"  => "L",
			             "Delivery Point"  => "L");
			$this->addLineFormat( $cols);
			
			$y = $this->GetY() + 6;
		}
		
		function __construct($orientation, $metric, $size) {
	        parent::__construct($orientation, $metric, $size);
			
			global $y;
			
			$and = "";
			
			if (isset($_POST['status']) && $_POST['status'] != "") {
				$and .= " AND A.bookingtype = '" . $_POST['status'] . "' ";
			}
			
			if (isset($_POST['datefrom']) && $_POST['datefrom'] != "") {
				$and .= " AND A.startdatetime >= '" . convertStringToDate($_POST['datefrom']) . "' ";
			}
			
			if (isset($_POST['dateto']) && $_POST['dateto'] != "") {
				$and .= " AND A.enddatetime <= '" . convertStringToDate($_POST['dateto']) . " 23:59:59" . "' ";
			}

			if (isset($_POST['vehicleid']) && $_POST['vehicleid'] != "0") {
				$and .= " AND A.vehicleid = " . $_POST['vehicleid'] . " ";
			}

			if (isset($_POST['driverid']) && $_POST['driverid'] != "0") {
				$and .= " AND A.driverid = " . $_POST['driverid'] . " ";
			}
			
			$orderid = "A.id";
			
			if ($_POST['orderby'] == "V") {
				$orderby = "C.registration, AA.departuretime";

			} else if ($_POST['orderby'] == "T") {
				$orderby = "AA.departuretime";

			} else if ($_POST['orderby'] == "D") {
				$orderby = "D.name, AA.departuretime";
							
			} else if ($_POST['orderby'] == "R") {
				$orderby = "B.name, AA.departuretime";
			}				
				
			$sql = "SELECT A.*, AA.place, DATE_FORMAT(AA.departuretime, '%d/%m/%Y %H:%i') AS departuretime, " .
					"B.description AS trailername, C.registration, D.name AS drivername, D.telephone " .
					"FROM {$_SESSION['DB_PREFIX']}booking A " .
					"LEFT OUTER JOIN {$_SESSION['DB_PREFIX']}bookingleg AA " .
					"ON AA.bookingid = A.id " .
					"LEFT OUTER JOIN {$_SESSION['DB_PREFIX']}trailer B " .
					"ON B.id = A.trailerid " .
					"LEFT OUTER JOIN {$_SESSION['DB_PREFIX']}vehicle C " .
					"ON C.id = A.vehicleid " .
					"LEFT OUTER JOIN {$_SESSION['DB_PREFIX']}driver D " .
					"ON D.id = A.driverid " .
					"WHERE 1 = 1 $and " .
					"ORDER BY $orderby";
			$result = mysql_query($sql);
			
			if ($result) {
				$first = true;
				
				while (($member = mysql_fetch_assoc($result))) {
					if ($first) {
						$this->newPage();
						$first = false;
					}
					
					$line=array( 
							 "Registration"    => $member['registration'],
				             "Phone"  => $member['telephone'],
				             "Trailer"  => $member['trailername'],
				             "Driver"  => $member['drivername'],
				             "Date / Time"  => $member['departuretime'],
				             "Delivery Point"  => $member['place']
				         );

					$size = $this->addLine( $y, $line );
					$y += $size;
					
					if ($y > 265) {
						$this->newPage();
					}
		 		}
				
			} else {
				logError($sql . " - " . mysql_error());
			}
		}
	}
	
	$pdf = new BookingSummaryReport( 'P', 'mm', 'A4');
	$pdf->Output();
?>