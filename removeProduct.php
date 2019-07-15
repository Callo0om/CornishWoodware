<?php
include_once 'includes/db_connect.php';
include_once 'includes/functions.php';

if (empty($_POST['name'])){
	echo "Form data missing.";
	header( "refresh:3; url=catalogue.php" );
}
else
{
	$name = filter_input(INPUT_POST, 'name', FILTER_SANITIZE_STRING);
	$itemNames = (explode(',',$name));
				
	$itemList = [];
	foreach ($itemNames as $item){
					
		$found = false;
		foreach($itemList as &$list){
			if($list[0] === $item){
				$found = true;
				$temp = $list[1] + 1;
				$list[1] = $temp;
			}
		}
					
		if($found === false){
			array_push($itemList, [$item, 1]);
		}
	}
	
	foreach($itemList as $item){
		$prep_stmt = "SELECT * FROM products WHERE name=? LIMIT 1";
		$stmt = $mysqli->prepare($prep_stmt);
		
		if ($stmt) {
			$stmt->bind_param('s', $item[0]);
			$stmt->execute();
				
			$result = get_result($stmt);
			$row = array_shift($result);
			if ($row){
				
				if($row['Remaining'] > intval($item[1])){
					if ($update_stmt = $mysqli->prepare("UPDATE products SET Remaining = Remaining - ".$item[1]." WHERE name=?")) {
						$update_stmt->bind_param('s', $item[0]);
						if (! $update_stmt->execute()) {
							echo "Error updating database. No action taken";
							header( "refresh:3; url=index.php" ); 
							$stmt->close();
							$update_stmt->close();
						}
					}
				}else{
					$dir = dirname(explode(",",$row['Src'])[0]);
					if ($update_stmt = $mysqli->prepare("DELETE FROM products WHERE name=?")) {
						$update_stmt->bind_param('s', $item[0]);
						if (! $update_stmt->execute()) {
							echo "Error updating database. No action taken";
							header( "refresh:3; url=index.php" ); 
							$stmt->close();
							$update_stmt->close();
						}else{
							if(file_exists($dir)){
								emptyDir($dir);
								rmdir($dir);
							}
							echo "Product reomved successfully.";
							header( "refresh:3; url=index.php" ); 
							$stmt->close();
							$update_stmt->close();
						}
					}
				}
			}else{
				echo "Product not found in database, no action taken.";
				$stmt->close();
			}
		}
	}
}

?>