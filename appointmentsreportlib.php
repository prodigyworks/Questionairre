<?php
	require_once('../system-db.php');
	require_once('../pdfreport.php');
	
	class AppointmentsReport extends PDFReport {
		function newPage() {
			$this->AddPage();
			
			$this->Image("../images/sa-header.png", 125.6, 1);
			$this->Image("../images/footer.png", 54, 280);
			
			$this->addText( 10, 6, "Appointments Report", 12, 4, 'B') + 15;
			$size = $this->addText( 10, 15, "Between : " . $_POST['datefrom'] . " - " . $_POST['dateto'], 10, 3, '') + 1;
			
			if ($_POST['categoryid'] != "0") {
				$size = $this->addText( 10, 22, "Category : " . GetCategoryName($_POST['categoryid']), 10, 3, '') + 1;
			}
			
			$size = 30;
			
			$this->SetFont('Arial','', 6);
				
			$cols=array( 
					"User"    => 25,
					"Company"  => 69,
					"Category"  => 25,
					"From"  => 23,
					"To"  => 23,
					"Logged By" => 25
				);
			
			$this->addCols($size, $cols);

			$cols=array(
					"User"    => "L",
					"Company"  => "L",
					"Category"  => "L",
					"From"  => "L",
					"To"  => "L",
					"Logged By" => "L"
				);
			$this->addLineFormat( $cols);

			return $size + 8;
		}
		
		function __construct($orientation, $metric, $size, $startdate, $enddate, $categoryid, $userid) {
			$dynamicY = 0;
			
	        parent::__construct($orientation, $metric, $size);
	        
			$dynamicY = $this->newPage();

			$and = "";
			$and .= "AND s_date <= '$enddate' AND e_date >= '$startdate' ";
			
			if ($categoryid != "0") {
				$and .= "AND E.category_id = $categoryid ";
			}
			
			if ($userid != null) {
				$and .= "AND C.member_id IN (" . ArrayToInClause($userid) . ") ";
			}
				
			try {
				$sql = "SELECT " .
						"DATE_FORMAT(A.s_date, '%d/%m/%Y') AS s_date, " .
						"DATE_FORMAT(A.e_date, '%d/%m/%Y') AS e_date, " .
						"TIME_FORMAT(A.s_time, '%H:%i') AS s_time, " .
						"TIME_FORMAT(A.e_time, '%H:%i') AS e_time, " .
						"A.event_id, E.category_id, E.name, E.color, E.background, C.fullname, B.title, F.fullname AS createddbyname " .
						"FROM dates A " .
						"INNER JOIN events B ON " .
						"B.event_id = A.event_id " .
						"INNER JOIN {$_SESSION['NEWDIARY_DB_PREFIX']}members C ON " .
						"C.member_id = B.user_id " .
						"LEFT OUTER JOIN {$_SESSION['NEWDIARY_DB_PREFIX']}members F ON " .
						"F.member_id = B.createdby " .
						"INNER JOIN categories E ON " .
						"E.category_id = B.category_id " . 
						"WHERE 1 = 1 $and " . 
						"ORDER BY C.fullname, s_date, s_time";
				$result = mysql_query(getFilteredData($sql));
				
				if ($result) {
					while (($member = mysql_fetch_assoc($result))) {
						$line=array(
								"User"    => $member['fullname'],
								"Company"  => $member['title'],
								"Category"  => $member['name'],
								"From"  => $member['s_date'] . " " . $member['s_time'],
								"To"  => $member['e_date'] . " " . $member['e_time'],
								"Logged By" => $member['createddbyname']
						);
						
						$dynamicY += $this->addLine( $dynamicY, $line );
						
						if ($dynamicY > 270) {
							$dynamicY = $this->newPage();
						}
					}
					
				} else {
					logError($sql . " - " . mysql_error());
				}
				
			} catch (Exception $e) {
				logError($e->getMessage());
			}
		}
	}
	
	start_db();
	
	$pdf = new AppointmentsReport( 'P', 'mm', 'A4', convertStringToDate($_POST['datefrom']), convertStringToDate($_POST['dateto']), $_POST['categoryid'], isset($_POST['userid']) ? $_POST['userid'] : null);
	$pdf->Output();
?>