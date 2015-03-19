<?php
	require_once('system-db.php');
	require_once('pdfreport.php');
	
	class CertificateReport extends PDFReport {
		function __construct($orientation, $metric, $size, $id) {
	        parent::__construct($orientation, $metric, $size);
	        
	        $this->SetAutoPageBreak(true, 0);
	                  
			//Include database connection details
			start_db();
			
			$this->AddPage();
			$this->Image("images/logomain2.png", 90, 250);

			try {	
				$sql = "SELECT B.*, A.percentagepass, D.fullname
						FROM {$_SESSION['DB_PREFIX']}courseattempt A
						INNER JOIN {$_SESSION['DB_PREFIX']}coursemember C
						ON C.id = A.coursememberid
						INNER JOIN {$_SESSION['DB_PREFIX']}course B
						ON B.id = C.courseid
						INNER JOIN {$_SESSION['DB_PREFIX']}members D
						ON D.member_id = C.memberid
						WHERE A.id = $id";
				$result = mysql_query($sql);
				
				if ($result) {
					while (($member = mysql_fetch_assoc($result))) {
						$coursename = $member['title'];
						
						$this->AddText(50, 40, "Certificate of Achievement", 26, 26);
						$this->AddText(80, 60, $member['fullname'], 17, 17);
						$this->AddText(73, 80, "For successfully completing the course", 10, 10);
						$this->AddText(87, 100, "$coursename", 17, 17);
						$this->AddText(86, 120, "Date : " . date("d/M/Y"), 12, 12);
						$this->AddText(82, 140, "Pass percentage: " . $member['percentagepass'] . " %", 12, 12);
					}
					
				} else {
					logError($sql . " - " . mysql_error());
				}
				
			} catch (Exception $exception) {
				logError($exception->getMessage());
			}
			
		}
	}
	
	$report = new CertificateReport("P", "mm", "A4", $_GET['id']);
	$report->Output();
?>