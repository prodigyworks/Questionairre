<?php
	require_once("system-db.php");
	
	start_db();
	
	$questionid = $_POST['questionid'];
	$answers = $_POST['answers'];
	
	$correct = false;
	$correctanswers = array();
	$sql = "SELECT A.id, A.type
			FROM {$_SESSION['DB_PREFIX']}coursequestion A
			INNER JOIN {$_SESSION['DB_PREFIX']}coursepage B
			ON A.coursepageid = B.id
		    WHERE A.id = $questionid
		    AND A.required = 1";
	
	$result = mysql_query($sql);
	$correct = true;
	
	if (! $result) {
		logError($sql . " - " . mysql_error());
	}
	
	while (($member = mysql_fetch_assoc($result))) {
		if ($member['type'] == "O") {
			$answer = $answers[0];
			$correct = false;
			$sql = "SELECT A.*
					FROM {$_SESSION['DB_PREFIX']}courseanswer A
				    WHERE A.questionid = $questionid
				    AND A.correct = 1";
			
			$itemresult = mysql_query($sql);
			
			if (! $itemresult) {
				logError($sql . " - " . mysql_error());
			}
			
			while (($itemmember = mysql_fetch_assoc($itemresult))) {
				if ($itemmember['answer'] == $answer) {
					$correct = true;
				}
				
				array_push($correctanswers, $itemmember['answer']);
			}	
			
		} else if ($member['type'] == "M") {
			$sql = "SELECT A.*
					FROM {$_SESSION['DB_PREFIX']}courseanswer A
				    WHERE A.questionid = $questionid";
			
			$itemresult = mysql_query($sql);
			
			if (! $itemresult) {
				logError($sql . " - " . mysql_error());
			}
			
			$correct = true;
			
			while (($itemmember = mysql_fetch_assoc($itemresult))) {
				foreach($answers as $k => $v) {
					if ($itemmember['answer'] == $v) {
						$arrayAnswers[] = $v;
					
						if ($itemmember['correct'] == 0) {
							$correct = false;
							
						} else {
							array_push($correctanswers, $itemmember['answer']);
						}
						
					} else {
						if ($itemmember['correct'] == 1) {
							$correct = false;
							
							array_push($correctanswers, $itemmember['answer']);
						}
					}
				}
			}	
		}	
	}
	
	$line = array(
			"correct" 			=> $correct,
			"correctanswers"	=> $correctanswers
		);
	
	echo json_encode($line);
?>