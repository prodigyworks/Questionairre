<?php
	class O_page {
		private $pageid;
		private $questions = array();
		
		public function __construct($id) {
			$this->pageid = $id;
			$this->load();
		}
		
		public function load() {
			$sql = "SELECT A.*, B.question, B.questionnumber, B.questiontext, B.type
					FROM {$_SESSION['DB_PREFIX']}coursepage A
				    INNER JOIN {$_SESSION['DB_PREFIX']}coursequestion B
				    ON A.id = B.coursepageid
				    WHERE A.id = $this->pageid
				    ORDER BY B.questionnumber";
			
			$result = mysql_query($sql);
			
			if (! $result) {
				logError($sql . " - " . mysql_error());
			}
			
			while (($member = mysql_fetch_assoc($result))) {
			}
		}
	}
?>