<?php
echo "<div class='orderContainerDiv'>";

echo "Orders:";

echo "<ul class='ordersul'>";

$where = "orders";

$headingsQuery = "SELECT column_name FROM information_schema.columns WHERE table_name=?";
$headingsStmt = $mysqli->stmt_init();
$headingsStmt = $mysqli->prepare($headingsQuery);
												
if ($headingsStmt){
	$headingsStmt->bind_param('s', $where);
	$headingsStmt->execute();
	$headingsResult = get_result($headingsStmt);
		
	foreach($headingsResult as $headingsRow){
		
		echo "<li class='ordersli'>" . $headingsRow['column_name'] . "</li>";
		
	}
}
echo "</ul>";

$tableQuery = "SELECT * FROM orders ORDER BY DateTime Desc";
$tableStmt = $mysqli->stmt_init();
$tableStmt = $mysqli->prepare($tableQuery);
												
if ($tableStmt){
	$tableStmt->execute();
	$tableResult = get_result($tableStmt);
		
	foreach($tableResult as $tableRow){

		$sent = boolval($tableRow['Sent']);
		
		echo "<ul class='ordersul'>";
		
		$count = 0;
		foreach ($tableRow as $data){
			$string = "";
			foreach(explode(',',$data) as $dataEntry){
				if($count === 0){
					if($sent === true){
						$string .= $dataEntry . "<br /><form action='includes/printInvoice.php' method='POST' target='_blank'><input type='hidden' name='orderID' value='".$tableRow['ID']."' /><input type='submit' value='Print Invoice' /></form>";
						$dateSent = $tableRow['DateTime'];
					}else{
						$string .= $dataEntry . "<br /><form action='includes/printInvoice.php' method='POST' target='_blank'><input type='hidden' name='orderID' value='".$tableRow['ID']."' /><input type='submit' value='Print Invoice' /></form>";
						$string .= "<form action='includes/markSent.php' method='POST'><input type='hidden' name='orderID' value='".$tableRow['ID']."' /><input type='submit' value='Mark as Sent' /></form>";
					}
				}else if($count === 1){
					if($sent === true){
						if($tableRow['Tracking'] != '0'){
							$string .= "Sent:<br />" . date('d M y', strtotime($dateSent)) . "<br />" . date('G:i:s', strtotime($dateSent)) . "<br /><br />Tracking:<br /><b>" . explode('|',$tableRow['ItemNames'])[1] . "</b><br /><b><u>" . explode('|',$tableRow['ItemNames'])[0] . "</u></b>";
						}else{
							$string .= "Sent:<br />" . date('d M y', strtotime($dateSent)) . "<br />" . date('G:i:s', strtotime($dateSent)) . "<br /><br /><form action='includes/addTracking.php' method='POST'><input type='hidden' name='orderID' value='".$tableRow['ID']."' /><input type='text' name='service' placeholder='Service' required /><input type='text' name='tracking' placeholder='Tracking' required /><input type='submit' value='Add Tracking' /></form>";
						}
					}else{
						$string .= $dataEntry . "<br />";
					}
				}else if($count === 3 || $count === 5){
					if($count === 5){
						$temp = explode('|', $dataEntry);
						$string .= "&#8226;" . $temp[0] . "<br /> £" . number_format((float)$temp[1]/100, 2, '.', '') . " * " . $temp[2] . "<br />";
					}else{
						$string .= "&#8226;" . $dataEntry . "<br />";
					}
				}else{
					$string .= $dataEntry . "<br />";
				}
			}
			echo "<li class='ordersli'>" . $string . "</li>";
			$count++;
		}
		
		echo "</ul>";
	}
}

echo "</div>";

?>