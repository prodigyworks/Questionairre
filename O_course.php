<?php
	require_once("system-db.php");
	
	class O_course {
		private $courseid;
		
		public function __construct($id) {
			$this->courseid = $id;
			
			start_db();
			
			foreach($_POST as $k => $v) {
				if (substr($k, 0, 5) == "group") {
					$_SESSION["COURSE_" .$id . "_" . $k] = $v;
				}
			}
			if (isset($_GET['results'])) {
				$this->showResults();
				
			} else if (isset($_POST['commit']) && $_POST['commit'] == "true") {
				$this->commit();
				
			} else {
				$this->load();
			}
		}
		
		public function showResults() {
			require_once("system-header.php");
			
			$sql = "SELECT C.id, C.type, C.coursepageid, B.pagenumber, C.questionnumber, C.question, C.showresults
					FROM {$_SESSION['DB_PREFIX']}course A
				    INNER JOIN {$_SESSION['DB_PREFIX']}coursepage B
				    ON A.id = B.courseid
				    INNER JOIN {$_SESSION['DB_PREFIX']}coursequestion C
				    ON B.id = C.coursepageid
				    WHERE A.id = $this->courseid
				    AND C.showresults = 1
				    ORDER BY C.id";
			
			$result = mysql_query($sql);
			
			if (! $result) {
				logError($sql . " - " . mysql_error());
			}
			
			$questioncount = 0;
			$questioncorrect = 0;
			$questionarray = array();
			
			while (($member = mysql_fetch_assoc($result))) {
				$questioncount++;
				
				if ($member['type'] == "O") {
					$answerArray = $this->checkRadioAnswer($member['id']);
					
				} else if ($member['type'] == "M") {
					$answerArray = $this->checkCheckAnswer($member['id']);
				}
				
				$passed = $answerArray['passed'];
				
				if ($passed) {
					$questioncorrect++;
				}
				
				$questionarray[] = array(
						"questionid"		=> $member['id'],
						"passed"			=> $passed,
						"pagenumber"		=> $member['pagenumber'],
						"question"			=> $member['question'],
						"questionnumber"	=> $member['questionnumber'],
						"answers"			=> $answerArray['answers']
					);
			}
			
			$percentage = (100 / $questioncount) * $questioncorrect;
			
			$sql = "SELECT percentagepass
					FROM {$_SESSION['DB_PREFIX']}course A
				    WHERE A.id = $this->courseid";
			
			$result = mysql_query($sql);
			
			if (! $result) {
				logError($sql . " - " . mysql_error());
			}
			
			$percentagepass = 100;
			$memberid = getLoggedOnMemberID();
			
			while (($member = mysql_fetch_assoc($result))) {
				$percentagepass = $member['percentagepass'];
			}
			
			$passed = ($percentagepass > $percentage) ? 0 : 1;
			
			foreach ($questionarray as $k => $v) {
				$questionid = $v['questionid'];
				$passed = $v['passed'] ? 1 : 0;
				
				if ($passed == 1) {
?>
<h4><img src="images/thumbs_up.gif" /> <?php echo $v['questionnumber']; ?>. <?php echo $v['question']; ?></h4>
<?php 
				} else {
?>
<h4><img src="images/unanswered.png" /> <?php echo $v['questionnumber']; ?>. <?php echo $v['question']; ?></h4>
<?php 
				}
				
				foreach ($v['answers'] as $k1 => $v1) {
?>
<h5><div style='padding-left:50px'><?php echo $v1; ?></div></h5>
<?php
				}
			}
			
			$sql = "SELECT B.id FROM {$_SESSION['DB_PREFIX']}coursemember A
				    INNER JOIN {$_SESSION['DB_PREFIX']}courseattempt B
				    ON B.coursememberid = A.id
				    WHERE A.courseid = $this->courseid
				    AND A.memberid = $memberid";
			
			$result = mysql_query($sql);
			$courseattemptid = 0;
			
			if (! $result) {
				logError($sql . " - " . mysql_error());
			}
			
			while (($member = mysql_fetch_assoc($result))) {
				$courseattemptid = $member['id'];
			}
			
			
			if ($percentagepass > $percentage) {
?>
				<br>
				<br>
				<br>
				<h1>Failed, percentage <?php echo $percentage; ?> % is less than what is needed (<?php echo $percentagepass; ?> %) </h1>
<?php
				
			} else {
?>
				<br>
				<br>
				<br>
				<h1>Passed, percentage <?php echo $percentage; ?> % is what is needed (<?php echo $percentagepass; ?> %) </h1>
				<br>
				<br>
				<p><a target="_new" href="nhscertificate.php?id=<?php echo $courseattemptid; ?>">View certificate</a>
<?php
				require_once("system-footer.php");
			}
		}
		
		public function commit() {
			$sql = "SELECT C.id, C.type, C.coursepageid, B.pagenumber, C.questionnumber, C.question
					FROM {$_SESSION['DB_PREFIX']}course A
				    INNER JOIN {$_SESSION['DB_PREFIX']}coursepage B
				    ON A.id = B.courseid
				    INNER JOIN {$_SESSION['DB_PREFIX']}coursequestion C
				    ON B.id = C.coursepageid
				    WHERE A.id = $this->courseid
				    AND C.showresults = 1
				    ORDER BY C.id";
			
			$result = mysql_query($sql);
			
			if (! $result) {
				logError($sql . " - " . mysql_error());
			}
			
			$questioncount = 0;
			$questioncorrect = 0;
			$questionarray = array();
			
			while (($member = mysql_fetch_assoc($result))) {
				$questioncount++;
				
				if ($member['type'] == "O") {
					$answerArray = $this->checkRadioAnswer($member['id']);
					
				} else if ($member['type'] == "M") {
					$answerArray = $this->checkCheckAnswer($member['id']);
				}
				
				$passed = $answerArray['passed'];
				
				if ($passed) {
					$questioncorrect++;
				}
				
				$questionarray[] = array(
						"questionid"		=> $member['id'],
						"passed"			=> $passed,
						"pagenumber"		=> $member['pagenumber'],
						"question"			=> $member['question'],
						"questionnumber"	=> $member['questionnumber']
					);
			}
			
			$percentage = (100 / $questioncount) * $questioncorrect;
			
			$sql = "SELECT percentagepass
					FROM {$_SESSION['DB_PREFIX']}course A
				    WHERE A.id = $this->courseid";
			
			$result = mysql_query($sql);
			
			if (! $result) {
				logError($sql . " - " . mysql_error());
			}
			
			$percentagepass = 100;
			$memberid = getLoggedOnMemberID();
			
			while (($member = mysql_fetch_assoc($result))) {
				$percentagepass = $member['percentagepass'];
			}
			
			$sql = "INSERT INTO {$_SESSION['DB_PREFIX']}coursemember
					(
					courseid, memberid
					)
					VALUES
					(
					$this->courseid, $memberid
					)";
					
			if (! mysql_query($sql)) {
				logError($sql . " - " . mysql_error());
			}
			
			$coursememberid = mysql_insert_id();
			$passed = ($percentagepass > $percentage) ? 0 : 1;
			
			$sql = "INSERT INTO {$_SESSION['DB_PREFIX']}courseattempt
					(
					coursememberid, passed, percentagepass, metacreateddate
					)
					VALUES
					(
					$coursememberid, $passed, $percentage, NOW()
					)";
					
			if (! mysql_query($sql)) {
				logError($sql . " - " . mysql_error());
			}
			
			$attemptid = mysql_insert_id();
			
			foreach ($questionarray as $k => $v) {
				$questionid = $v['questionid'];
				$passed = $v['passed'] ? 1 : 0;
				
				$sql = "INSERT INTO {$_SESSION['DB_PREFIX']}courseattemptanswer
						(
						attemptid, questionid, correct, metacreateddate
						)
						VALUES
						(
						$attemptid, $questionid, $passed, NOW()
						)";
					
				if (! mysql_query($sql)) {
					logError($sql . " - " . mysql_error());
				}
			}
			
			mysql_query("COMMIT");
			
			header("location: " . $_SERVER['HTTP_REFERER'] . "?results=true");
		}
		
		public function checkRadioAnswer($questionid) {
			$returnArray = array();
			$arrayAnswers = array();
			$answer = "COURSE_" . $this->courseid . "_group_radio" . $questionid;
			
			if (! isset($_SESSION[$answer])) {
				return false;
			}
			
			$answer = $_SESSION[$answer];
			
			$sql = "SELECT A.*
					FROM {$_SESSION['DB_PREFIX']}courseanswer A
				    WHERE A.questionid = $questionid
				    AND A.answer = '$answer' 
				    AND A.correct = 1";
			
			$result = mysql_query($sql);
			
			if (! $result) {
				logError($sql . " - " . mysql_error());
			}
			
			$correct = false;
			
			while (($member = mysql_fetch_assoc($result))) {
				$correct = true;
			}	
			
			$arrayAnswers[] = $answer;
			
			$returnArray = array(
					"passed"	=> $correct,
					"answers"	=> $arrayAnswers
				);
			
			return $returnArray;
		}
		
		public function checkCheckAnswer($questionid) {
			$returnArray = array();
			$arrayAnswers = array();
			
			$sql = "SELECT A.*
					FROM {$_SESSION['DB_PREFIX']}courseanswer A
				    WHERE A.questionid = $questionid";
			
			$result = mysql_query($sql);
			
			if (! $result) {
				logError($sql . " - " . mysql_error());
			}
			
			$correct = true;
			
			while (($member = mysql_fetch_assoc($result))) {
				foreach($_SESSION["COURSE_" . $this->courseid . "_group$questionid"] as $k => $v) {
					if ($member['answer'] == $v) {
						$arrayAnswers[] = $v;
					
						if ($member['correct'] == 0) {
							$correct = false;
							break;
						}
						
					} else {
						if ($member['correct'] == 1) {
							$correct = false;
							break;
						}
					}
				}
			}	
			
			$returnArray = array(
					"passed"	=> $correct,
					"answers"	=> $arrayAnswers
				);
			
			return $returnArray;
		}
		
			
		public function valuechecked($questionid, $value, $courseid) {
			if (isset($_SESSION["COURSE_" . $courseid . "_group$questionid"])) {
				foreach($_SESSION["COURSE_" . $courseid . "_group$questionid"] as $k => $v) {
					if ($v == $value) {
						return "checked";
					}
				}
			}
			
			return " ";
		}
		
		public function valueradiochecked($questionid, $value, $courseid) {
			if (isset($_SESSION["COURSE_" . $courseid . "_group_radio$questionid"])) {
				if ($_SESSION["COURSE_" . $courseid . "_group_radio$questionid"] == $value) {
					return "checked";
				}
			}
			
			return " ";
		}
			
		
		public function load() {
			$pagenumber = 1;
			
			require_once("system-header.php");
			
			if (isset($_POST['pagenumber'])) {
				$pagenumber = $_POST['pagenumber'];
			}
?>
			<form id="surveyform" method="POST">
<?php 	
			
			$sql = "SELECT A.richtext AS coursetext, A.title, B.id, B.richtext, B.pagenumber
					FROM {$_SESSION['DB_PREFIX']}course A
				    INNER JOIN {$_SESSION['DB_PREFIX']}coursepage B
				    ON A.id = B.courseid
				    WHERE A.id = $this->courseid
				    AND B.pagenumber = $pagenumber
				    ORDER BY B.pagenumber";
			
			$result = mysql_query($sql);
			
			if (! $result) {
				logError($sql . " - " . mysql_error());
			}
			
			while (($member = mysql_fetch_assoc($result))) {
				$pageid = $member['id'];
				
				echo "<div id='surveypage'><h1>" . $member['title'] . "</h1><br>";
				echo $member['coursetext'];
				echo "<br>";
				echo $member['richtext'];
				echo "<br>";
				
				$sql = "SELECT A.*
						FROM {$_SESSION['DB_PREFIX']}coursequestion A
					    WHERE A.coursepageid = $pageid
					    ORDER BY A.questionnumber";
				
				$questionresult = mysql_query($sql);
				
				if (! $questionresult) {
					logError($sql . " - " . mysql_error());
				}
				
				while (($questionmember = mysql_fetch_assoc($questionresult))) {
					$questionid = $questionmember['id'];
					$type = $questionmember['type'];
					
					echo $questionmember['questiontext'];
					echo "<h4>" . $questionmember['questionnumber'] . ". " . $questionmember['question'] . "</h4>";
					
					$sql = "SELECT A.*
							FROM {$_SESSION['DB_PREFIX']}courseanswer A
						    WHERE A.questionid = $questionid
						    ORDER BY A.id";
					
					$answerresult = mysql_query($sql);
					
					if (! $answerresult) {
						logError($sql . " - " . mysql_error());
					}
					
					while (($answermember = mysql_fetch_assoc($answerresult))) {
						$answerid = $answermember['id'];
						
						if ($type == "O") {
?>
							<div class="inputcontainer">
								<input group="<?php echo $questionid; ?>" <?php echo $this->valueradiochecked($questionid, $answermember['answer'], $this->courseid); ?> type="radio" name="group_radio<?php echo $questionid; ?>" id="group_radio<?php echo $questionid; ?>" value="<?php echo $answermember['answer']; ?>"><span><?php echo $answermember['answer']; ?></span></input><br>
							</div>

<?php 
						} else if ($type == "M") {
?>
							<div class="inputcontainer">
								<input <?php echo $this->valuechecked($questionid, $answermember['answer'], $this->courseid); ?>  type="checkbox" name="group<?php echo $questionid; ?>[]" id="group<?php echo $questionid; ?>" value="<?php echo $answermember['answer']; ?>"> <?php echo $answermember['answer']; ?></input><br>
							</div>
<?php 
						}
					}
					
					echo "<br /><br />";
				}
			}
			
			echo "</div>";
			
			if ($pagenumber > 1) {
?>
		  		<span class="wrapper"><a class='link1 rgap2' href="javascript: prevpage()"><em><b>Prev</b></em></a></span>
<?php 				
			}
			
			$sql = "SELECT A.*
					FROM {$_SESSION['DB_PREFIX']}coursepage A
				    WHERE A.courseid = $this->courseid
				    AND A.pagenumber > $pagenumber";
			
			$nextresult = mysql_query($sql);
			$nextpage = false;
			
			if (! $nextresult) {
				logError($sql . " - " . mysql_error());
			}
			
			while (($nextmember = mysql_fetch_assoc($nextresult))) {
				$nextpage = true;
			}
					
			if ($nextpage) {
?>
		  		<span class="wrapper"><a class='link1' href="javascript: nextpage();"><em><b>Next</b></em></a></span>
<?php 				
				
			} else {
?>
		  		<span class="wrapper"><a class='link1' href="javascript: finish()"><em><b>Finish</b></em></a></span>
<?php 				
			}
			
?>
			<br>
			<br>
			<div id="errorline"></div>
			<input type="hidden" id="pagenumber" name="pagenumber" value="<?php echo $pagenumber; ?>" />
			<input type="hidden" id="commit" name="commit" value="false" />
		</form>
		<br>
		<br>
		<script>
			$(document).ready(
					function() {
						
					}
				);
			function prevpage() {
				$("#pagenumber").val("<?php echo ($pagenumber - 1); ?>");
				$("#surveyform").submit();
			}
			
			function finish() {
				$("#commit").val("true");
				$("#surveyform").submit();
			}

			function verify() {
				var answers = [];
				var verified = true;
<?php 
				$sql = "SELECT A.*
						FROM {$_SESSION['DB_PREFIX']}coursequestion A
					    WHERE A.coursepageid = $pageid
					    ORDER BY A.questionnumber";
				
				$questionresult = mysql_query($sql);
				
				if (! $questionresult) {
					logError($sql . " - " . mysql_error());
				}
				
				while (($questionmember = mysql_fetch_assoc($questionresult))) {
					$questionid = $questionmember['id'];
					
					if ($type == "O") {
?>
				answers.push($("input[name=group_radio<?php echo $questionid;?>]:checked").val());
				
				callAjax(
						"verifyanswer.php", 
						{ 
							answers: answers,
							questionid : <?php echo $questionid; ?>
						},
						function(data) {
							if (! data.correct) {
								var i;
								
								verified = false;
								
								for (i = 0; i < data.correctanswers.length; i++) {
									var div = $("input[name=group_radio<?php echo $questionid;?>][value=" + data.correctanswers[i] + "]").parent();
									
									div.css("color", "red");
									div.css("font-weight", "bold");
								}

								showError("Incorrect selection");
							}
						},
						false,
						function() {
						}
					);		
				
<?php 
					} else if ($type == "M") {
?>
				$('input[name="group_radio<?php echo $questionmember['id'];?>"]:checked').each(function() {
						answers.push(this.value);
					});
				
				callAjax(
						"verifyanswer.php", 
						{ 
							answers: answers,
							questionid : <?php echo $questionid; ?>
						},
						function(data) {
							if (! data.correct) {
								var i;
								
								verified = false;
								
								for (i = 0; i < data.correctanswers.length; i++) {
									var div = $("input[name=group<?php echo $questionid;?>][value=" + data.correctanswers[i] + "]").parent();
									
									div.css("color", "red");
									div.css("font-weight", "bold");
								}

								$("#errorline").text("Incorrect selection");
							}
						},
						false,
						function() {
						}
					);		
<?php 
					}
?>

				answers = [];		
<?php 
				}
?>
				return verified;
			}

			function showError() {
				$("#errorline").text("Incorrect selection");
				$("#errorline").show();
			}
			
			function nextpage() {
				if (verify()) {
					$("#pagenumber").val("<?php echo ($pagenumber + 1); ?>");
					$("#surveyform").submit();
				}
			}
		</script>
<?php 	
			require_once("system-footer.php");
		}
	}
?>