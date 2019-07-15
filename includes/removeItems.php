<?php 
include_once 'db_connect.php';
include_once 'functions.php';
sec_session_start();

$removalQuery = "SELECT * FROM products WHERE Name=?";
$removalStmt = $mysqli->stmt_init();
$removalStmt = $mysqli->prepare($removalQuery);
													
if ($removalStmt){
	$removalStmt->bind_param('s', $_POST['productName']);
	$removalStmt->execute();
	$removalResult = get_result($removalStmt);
	
	foreach($removalResult as $removalRow){

		if($removalRow['Remaining'] == 1){
			
			$deleteQuery = "DELETE FROM products WHERE Name=?";
			$deleteStmt = $mysqli->stmt_init();
			$deleteStmt = $mysqli->prepare($deleteQuery);
																
			if ($deleteStmt){
				$deleteStmt->bind_param('s', $_POST['productName']);
				$deleteStmt->execute();
			}
			
		}else{
			
			$newRemaining = intval($removalRow['Remaining']) - 1;
			$reduceQuery = "UPDATE products SET Remaining=? WHERE Name=?";
			$reduceStmt = $mysqli->stmt_init();
			$reduceStmt = $mysqli->prepare($reduceQuery);
																
			if ($reduceStmt){
				$reduceStmt->bind_param('ss', $newRemaining, $_POST['productName']);
				$reduceStmt->execute();
			}			
		}
	}
}

header("Location: /catalogue.php");
die();

?>