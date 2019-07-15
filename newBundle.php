<?php
include_once 'includes/db_connect.php';
include_once 'includes/functions.php';

$bundleName = filter_input(INPUT_POST, 'bundleName', FILTER_SANITIZE_STRING);
$bundleName = trim($bundleName);

$quantityOfItems = filter_input(INPUT_POST, 'itemsQuantity', FILTER_SANITIZE_STRING);

$items = array();
for($i=0; $i<$quantityOfItems; $i++){
	array_push($items, filter_input(INPUT_POST, 'item'.$i, FILTER_SANITIZE_STRING));
}

$bundleItems = '';
foreach($items as $item){
	$bundleItems .= $item . ',';
}
$bundleItems = substr($bundleItems, 0, -1);

$bundlePrice = filter_input(INPUT_POST, 'bundlePrice', FILTER_SANITIZE_NUMBER_FLOAT);

$bundleString = '[bundle]('.$bundlePrice.'){'.$bundleItems.'}';


	$prep_stmt = "SELECT ID FROM products WHERE name = ? LIMIT 1";
		$stmt = $mysqli->prepare($prep_stmt);
	
		if ($stmt) {
				
			$stmt->bind_param('s', $bundleName);
			$stmt->execute();
			$stmt->store_result();
				
			if ($stmt->num_rows == 1) {
				header( "refresh:3; url=deals.php" ); 
				echo "<p style='background: red;'>A product with this name already exists, redirecting in 3 seconds. The product was not added.</p>";
				$stmt->close();
			}else{
				if ($insert_stmt = $mysqli->prepare("INSERT INTO products (Name, Quantity, Price, Offer) VALUES (?, ?, ?, ?)")) {
					$Quantity = 0;
					$Price = 1;
					$insert_stmt->bind_param('siis', $bundleName, $Quantity, $Price, $bundleString);
					if (! $insert_stmt->execute()) {
						header( "refresh:3; url=deals.php" ); 
						//echo "<p style='background: red;'>Error updating database. Redirecting in 3 seconds. The product was not added.</p>";
						echo $insert_stmt->error;
					}else{
						header( "refresh:3; url=deals.php" );
						echo "<p style='background: Green;'>Bundle uploaded successfully. Redirecting in 3 seconds.</p>";
					}
				}
			}
		}

?>