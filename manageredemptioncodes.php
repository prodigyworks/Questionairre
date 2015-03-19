<?php
	require_once("crud.php");
	
	$crud = new Crud();
	$crud->dialogwidth = 350;
	$crud->title = "Redemption Codes";
	$crud->table = "{$_SESSION['DB_PREFIX']}redemptioncode";
	$crud->sql = "SELECT A.*  " .
				 "FROM  {$_SESSION['DB_PREFIX']}redemptioncode A " .
				 "ORDER BY A.code";
	$crud->columns = array(
			array(
				'name'       => 'id',
				'viewname'   => 'uniqueid',
				'length' 	 => 6,
				'showInView' => false,
				'bind' 	 	 => false,
				'editable' 	 => false,
				'pk'		 => true,
				'label' 	 => 'ID'
			),
			array(
				'name'       => 'code',
				'length' 	 => 20,
				'label' 	 => 'Code'
			),
			array(
				'name'       => 'redeemed',
				'length' 	 => 20,
				'label' 	 => 'Redeemed',
				'type'       => 'COMBO',
				'options'    => array(
						array(
							'value'		=> "Y",
							'text'		=> "Yes"
						),
						array(
							'value'		=> "N",
							'text'		=> "No"
						)
					)
			)
		);
		
	$crud->run();
?>
