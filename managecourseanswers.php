<?php
	require_once("crud.php");
	
	class CourseAnswerCrud extends Crud {
		public function postScriptEvent() {
?>
<?php
		}
	}
	
	$questionid = $_GET['id'];
	
	$crud = new CourseAnswerCrud();
	$crud->title = "Course Page Answers";
	$crud->dialogwidth = 700;
	$crud->table = "{$_SESSION['DB_PREFIX']}courseanswer";
	$crud->sql = "SELECT A.*, AA.questionnumber, B.pagenumber, C.title FROM {$_SESSION['DB_PREFIX']}courseanswer A
				  INNER JOIN {$_SESSION['DB_PREFIX']}coursequestion AA
				  ON AA.id = A.questionid
				  INNER JOIN {$_SESSION['DB_PREFIX']}coursepage B
				  ON B.id = AA.coursepageid
				  INNER JOIN {$_SESSION['DB_PREFIX']}course C
				  ON C.id = B.courseid
				  WHERE A.questionid = $questionid
				  ORDER BY A.id";
	
	$crud->columns = array(
			array(
				'name'       => 'id',
				'length' 	 => 6,
				'showInView' => false,
				'bind' 	 	 => false,
				'editable' 	 => false,
				'pk'		 => true,
				'label' 	 => 'ID'
			),
			array(
				'name'       => 'questionid',
				'length' 	 => 12,
				'editable'	 => false,
				'showInView' => false,
				'default'	 => $questionid,
				'label' 	 => 'Question'
			),
			array(
				'name'       => 'title',
				'length' 	 => 40,
				'editable'	 => false,
				'bind'		 => false,
				'label' 	 => 'Title'
			),
			array(
				'name'       => 'pagenumber',
				'length' 	 => 12,
				'align'		 => 'right',
				'editable'	 => false,
				'bind'		 => false,
				'label' 	 => 'Page Number'
			),
			array(
				'name'       => 'questionnumber',
				'length' 	 => 19,
				'align'		 => 'right',
				'editable'	 => false,
				'bind'		 => false,
				'label' 	 => 'Question Number'
			),
			array(
				'name'       => 'correct',
				'length' 	 => 12,
				'align'		 => 'center',
				'label' 	 => 'Correct',
				'type'       => 'CHECKBOX'
			),
			array(
				'name'       => 'answer',
				'length' 	 => 120,
				'type'		 => 'BASICTEXTAREA',
				'label' 	 => 'Answer'
			)
		);
		
	$crud->run();
	
?>
