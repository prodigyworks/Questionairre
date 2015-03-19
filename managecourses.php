<?php
	require_once("crud.php");
	
	class CourseCrud extends Crud {
		public function postScriptEvent() {
?>
<?php
		}
	}
	
	$crud = new CourseCrud();
	$crud->title = "Courses";
	$crud->dialogwidth = 900;
	$crud->table = "{$_SESSION['DB_PREFIX']}course";
	$crud->sql = "SELECT A.*, B.fullname FROM {$_SESSION['DB_PREFIX']}course A
				  LEFT OUTER JOIN {$_SESSION['DB_PREFIX']}members B
				  ON B.member_id = A.registeredby
				  ORDER BY A.title";
	
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
				'name'       => 'title',
				'length' 	 => 60,
				'label' 	 => 'Title'
			),
			array(
				'name'       => 'richtext',
				'length' 	 => 120,
				'showInView' => false,
				'type'		 => 'TEXTAREA',
				'label' 	 => 'Course Text'
			),
			array(
				'name'       => 'percentagepass',
				'length' 	 => 12,
				'align'		 => 'right',
				'label' 	 => 'Percentage Pass'
			),
			array(
				'name'       => 'registeredby',
				'type'       => 'DATACOMBO',
				'length' 	 => 30,
				'label' 	 => 'User',
				'table'		 => 'members',
				'table_id'	 => 'member_id',
				'alias'		 => 'fullname',
				'table_name' => 'fullname'
			)
		);
		
	$crud->subapplications = array(
			array(
				'title'		  => 'Pages',
				'imageurl'	  => 'images/minimize.gif',
				'application' => 'managecoursepages.php'
			)
		);
		
	$crud->run();
	
?>
