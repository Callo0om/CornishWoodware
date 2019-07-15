<?php
include_once 'db_connect.php';

if(isset($_POST['orderID'])){
	$orderID = $_POST['orderID'];
}else{
	header("Location: ../index.php");
}

if(isset($_POST['tracking'])){
	$tracking = $_POST['tracking'];
}else{
	header("Location: ../index.php");
}

if(isset($_POST['service'])){
	$service = $_POST['service'];
}else{
	header("Location: ../index.php");
}

$trackingString = $tracking."|".$service;

$trackingQuery = "UPDATE orders SET Tracking=? WHERE ID=?";
$trackingStmt = $mysqli->stmt_init();
$trackingStmt = $mysqli->prepare($trackingQuery);
													
if ($trackingStmt){		
	$trackingStmt->bind_param('ss', $trackingString, $orderID);
	$trackingStmt->execute();
}

header("Location: ../index.php");

?>