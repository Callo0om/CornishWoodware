<?php
include_once 'db_connect.php';

if(isset($_POST['orderID'])){
	$orderID = $_POST['orderID'];
}else{
	header("Location: ../index.php");
}

date_default_timezone_set('Europe/London');

$query = "UPDATE orders SET Sent='1', DateTimeSent=? WHERE ID=?";
$stmt = $mysqli->stmt_init();
$stmt = $mysqli->prepare($query);
			
if ($stmt){
	$stmt->bind_param('ss', date(DATE_RFC3339), $orderID);
	$stmt->execute();
}

header("Location: ../index.php");

?>