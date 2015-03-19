<?php
	require_once("crud.php");
	
	class PaypalCrud extends Crud {
		public function postScriptEvent() {
?>
<?php
		}
	}
	
	$crud = new PaypalCrud();
	$crud->title = "Paypal Transactions";
	$crud->allowEdit = false;
	$crud->dialogwidth = 400;
	$crud->table = "{$_SESSION['DB_PREFIX']}purchases";
	$crud->sql = "SELECT A.* FROM {$_SESSION['DB_PREFIX']}purchases A
				  ORDER BY A.id DESC";
	
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
				'name'       => 'transactionid',
				'length' 	 => 25,
				'label' 	 => 'Transaction ID'
			),
			array(
				'name'       => 'productid',
				'length' 	 => 15,
				'label' 	 => 'Product'
			),
			array(
				'name'       => 'productname',
				'length' 	 => 25,
				'label' 	 => 'Product Name'
			),
			array(
				'name'       => 'quantity',
				'length' 	 => 12,
				'align'		 => 'right',
				'label' 	 => 'Quantity'
			),
			array(
				'name'       => 'amount',
				'length' 	 => 14,
				'align'		 => 'right',
				'label' 	 => 'Amount (GBP)'
			),
			array(
				'name'       => 'fname',
				'length' 	 => 20,
				'label' 	 => 'First Name'
			),
			array(
				'name'       => 'lname',
				'length' 	 => 20,
				'label' 	 => 'Last Name'
			),
			array(
				'name'       => 'address',
				'length' 	 => 30,
				'label' 	 => 'Address'
			),
			array(
				'name'       => 'city',
				'length' 	 => 20,
				'label' 	 => 'City'
			),
			array(
				'name'       => 'county',
				'length' 	 => 20,
				'label' 	 => 'County'
			),
			array(
				'name'       => 'postcode',
				'length' 	 => 8,
				'label' 	 => 'Post Code'
			),
			array(
				'name'       => 'country',
				'length' 	 => 20,
				'label' 	 => 'Country'
			),
			array(
				'name'       => 'status',
				'length' 	 => 10,
				'label' 	 => 'Status'
			),
			array(
				'name'       => 'posteddate',
				'length' 	 => 20,
				'label' 	 => 'Posted Date'
			)
		);
		
	$crud->run();
	
?>
