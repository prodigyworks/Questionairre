<?php
	require_once("crud.php");
	
	class CoursePageCrud extends Crud {
		public function postScriptEvent() {
?>
<?php
		}
	}
	
	$courseid = $_GET['id'];
	
	$crud = new CoursePageCrud();
	$crud->title = "Course Pages";
	$crud->dialogwidth = 900;
	$crud->table = "{$_SESSION['DB_PREFIX']}coursepage";
	$crud->sql = "SELECT A.*, B.title FROM {$_SESSION['DB_PREFIX']}coursepage A
				  INNER JOIN {$_SESSION['DB_PREFIX']}course B
				  ON B.id = A.courseid
				  WHERE A.courseid = $courseid
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
				'name'       => 'pagenumber',
				'length' 	 => 12,
				'align'		 => 'right',
				'label' 	 => 'Page Number'
			),
			array(
				'name'       => 'courseid',
				'length' 	 => 12,
				'editable'	 => false,
				'showInView' => false,
				'default'	 => $courseid,
				'label' 	 => 'Title'
			),
			array(
				'name'       => 'title',
				'length' 	 => 60,
				'editable'	 => false,
				'bind'		 => false,
				'label' 	 => 'Title'
			),
			array(
				'name'       => 'richtext',
				'length' 	 => 120,
				'type'		 => 'TEXTAREA',
				'label' 	 => 'Page Text'
			)
		);
		
	$crud->subapplications = array(
			array(
				'title'		  => 'Questions',
				'imageurl'	  => 'images/minimize.gif',
				'application' => 'managecoursequestions.php'
			)
		);
		
	$crud->run();
	
?>
