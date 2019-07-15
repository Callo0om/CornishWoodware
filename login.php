<?php
if ( !empty( $_POST['fName'] ) ) { 
	$username = $_POST['fName'];

	if ( !empty( $_POST['fPass'] ) ) { 
		$password = $_POST['fPass'];
		
		include_once 'includes/psl-config.php';
		
		$mysqli = new mysqli(HOST, USER, PASSWORD, DATABASE);
	}
	else
	{
		echo "No password supplied";
	}
}
else 
{
	echo "No username supplied";
}
?>
