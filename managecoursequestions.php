<?php
	require_once("crud.php");
	
	class coursequestionCrud extends Crud {
		public function postScriptEvent() {
?>
<?php
		}
	}
	
	$pageid = $_GET['id'];
	
	$crud = new coursequestionCrud();
	$crud->title = "Course Page Questions";
	$crud->dialogwidth = 950;
	$crud->table = "{$_SESSION['DB_PREFIX']}coursequestion";
	$crud->sql = "SELECT A.*, B.pagenumber, C.title FROM {$_SESSION['DB_PREFIX']}coursequestion A
				  INNER JOIN {$_SESSION['DB_PREFIX']}coursepage B
				  ON B.id = A.coursepageid
				  INNER JOIN {$_SESSION['DB_PREFIX']}course C
				  ON C.id = B.courseid
				  WHERE A.coursepageid = $pageid
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
				'name'       => 'coursepageid',
				'length' 	 => 12,
				'editable'	 => false,
				'showInView' => false,
				'default'	 => $pageid,
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
				'label' 	 => 'Question Number'
			),
			array(
				'name'       => 'questiontext',
				'length' 	 => 120,
				'showInView' => false,
				'type'		 => 'TEXTAREA',
				'label' 	 => 'Pre Question Text'
			),
			array(
				'name'       => 'question',
				'length' 	 => 120,
				'type'		 => 'BASICTEXTAREA',
				'label' 	 => 'Question'
			),
			array(
				'name'       => 'showresults',
				'length' 	 => 15,
				'align'		 => 'center',
				'type'		 => 'CHECKBOX',
				'label' 	 => 'Show Results'
			),
			array(
				'name'       => 'required',
				'length' 	 => 15,
				'align'		 => 'center',
				'type'		 => 'CHECKBOX',
				'label' 	 => 'Required'
			),
			array(
				'name'       => 'type',
				'length' 	 => 30,
				'label' 	 => 'Type',
				'type'       => 'COMBO',
				'options'    => array(
						array(
							'value'		=> 'M',
							'text'		=> 'Multiple Choice'
						),
						array(
							'value'		=> 'F',
							'text'		=> 'Free Entry'
						),
						array(
							'value'		=> 'O',
							'text'		=> 'One Of Many'
						)
					)
			)
		);
		
	$crud->subapplications = array(
			array(
				'title'		  => 'Answers',
				'imageurl'	  => 'images/minimize.gif',
				'application' => 'managecourseanswers.php'
			)
		);
		
	$crud->run();
	
?>
