<?php
include_once 'includes/db_connect.php';
include_once 'includes/functions.php';
 
sec_session_start();
?>

<!DOCTYPE html>
<html lang="en">

<?php

include "head.html";
echo "<title>CW Confirmation</title></head>";

$postError = '';

if(isset($_POST['itemList'])){
	$itemText = $_POST['itemList'];
}else{
	$postError = 'item';
}

	$itemNames = (explode(',',$itemText));
				
	$itemList = [];
	foreach ($itemNames as $item){
					
		$found = false;
		foreach($itemList as &$list){
			if($list[0] === explode('|',$item)[0]){
				$found = true;
				$temp = $list[1] + 1;
				$list[1] = $temp;
			}
		}
					
		if($found === false){
			array_push($itemList, [explode('|',$item)[0], 1]);
		}
	}

$justItemNames = "";
foreach($itemNames as $item){

	if(explode('|',$item)[0] !== 'Promotion'){
		if ($result = $mysqli->query("SELECT * FROM products WHERE Name='".explode('|',$item)[0]."'")) {
			$row = mysqli_fetch_array($result, MYSQLI_NUM);
			
			if($row[12] != '0'){
				if(explode(']',explode('[',$row[12])[1])[0] == 'bundle'){
					for($i=0; $i<intval(explode('|',$item)[1]); $i++){
						foreach(explode(',', explode('}',explode('{',$row[12])[1])[0]) as $bundleItem){
							$justItemNames .= $bundleItem . ",";
						}
					}
				}else{
					for($i=0; $i<intval(explode('|',$item)[1]); $i++){
						$justItemNames .= explode('|',$item)[0] . ",";
					}
				}
			}else{
				for($i=0; $i<intval(explode('|',$item)[1]); $i++){
					$justItemNames .= explode('|',$item)[0] . ",";
				}
			}
		}
	}
}
$justItemNames = substr($justItemNames, 0, -1);

echo "<body onload='removeItem(\"user\", \"".$justItemNames."\")'>";
include "header.php"; 
include "nav.php";

if(isset($_POST['name'])){
	$name = unserialize($_POST['name']);
}else{
	$postError = 'form';
}

if(isset($_POST['address'])){
	$address = unserialize($_POST['address']);
}else{
	$postError = 'form';
}

if(isset($_POST['phone'])){
	$phone = $_POST['phone'];
}else{
	$postError = 'form';
}

if(isset($_POST['eMail']) && $_POST['eMail'] != ""){
	$eMail = $_POST['eMail'];
}else{
	$postError = 'form';
}

if(isset($_POST['subTotal'])){
	$subTotal = $_POST['subTotal'];
}else{
	$postError = 'form';
}

if(isset($_POST['delivery'])){
	$delivery = $_POST['delivery'];
}else{
	$postError = 'form';
}

if(isset($_POST['paymentTime']) && $_POST['paymentTime'] != ""){
	$paymentTime = $_POST['paymentTime'];
}else{
	$postError = 'form';
}

$tempID = uniqid();
$ID1 = strtoupper(substr($tempID, 0, 4));
$ID2 = strtoupper(substr($tempID, -4));
$ID = $ID1 . '-' . $ID2;

$nameTitle = $name[0];
$nameFirst = $name[1];
$nameLast = $name[2];

$address1 = $address[0];
$i = 0;
if(count($address) > 6){
	$i = 2;
	$address2 = $address[1];
	$address3 = $address[2];
}else if(count($address) > 5){
	$i = 1;
	$address2 = $address[1];
	$address3 = '';
}else{
	$address2 = '';
	$address3 = '';
}
$city = $address[1 + $i];
$state = $address[2 + $i];
$zip = $address[3 + $i];
$country = $address[4 + $i];

if($postError !== ''){
	echo "<p>Sorry, there seems to have been an error with this page.</p>";
	echo "<p>Please use the <a href='contact.php?orderNo=".$ID."'>Contact page</a> to send us a message and we will check that all is well with your order.</p>";
}else{
	
	
	


$addressString = '';
foreach($address as $addressLine){
	$addressString .= $addressLine . ",";
}
$addressString = substr($addressString, 0, -1);



$Name = $nameTitle . "," . $nameFirst . "," . $nameLast;
$Address = $addressString;
$DateTime = $paymentTime;

if ($insert_stmt = $mysqli->prepare("INSERT INTO orders (ID, Name, Address, Phone, ItemNames, DateTime, SubTotal, Delivery) VALUES (?, ?, ?, ?, ?, ?, ?, ?)")) {
	$insert_stmt->bind_param('ssssssss', $ID, $Name, $Address, $phone, $itemText, $DateTime, $subTotal, $delivery);
	if (! $insert_stmt->execute()) {
		echo $insert_stmt->error;
	}
}
	
}

foreach($itemList as $item){
	if($item[0] !== 'Promotion'){
		
		$itemQuery = "SELECT * FROM products WHERE Name='".$item[0]."'";
		$itemStmt = $mysqli->stmt_init();
		$itemStmt = $mysqli->prepare($itemQuery);
													
		if ($itemStmt){
			$itemStmt->execute();
			$itemResult = get_result($itemStmt);
			$itemRow = array_shift($itemResult);
				
			if($itemRow['Offer'] !== '0'){
				if(explode(']',explode('[',$itemRow['Offer'])[1])[0] === 'bundle'){
					foreach(explode(',', explode('}',explode('{',$itemRow['Offer'])[1])[0]) as $bundleItems){
						if ($bundleResult = $mysqli->query("SELECT * FROM products WHERE Name='".$bundleItems."'")) {
							$bundleRow = mysqli_fetch_array($bundleResult, MYSQLI_NUM);
									
							$newReservedText = '0';
							if(count(explode(',',$bundleRow[13])) > 0){
								$oldReservedText = explode(',',$bundleRow[13]);
							}else{
								$oldReservedText = $bundleRow[13];
							}
							foreach($oldReservedText as $oldText){
								if(!explode('|',$oldText)[1] == $_SESSION['reservation']){
									if($newReservedText == '0'){
										$newReservedText = $oldText;
									}else{
										$newReservedText = $newReservedText . ',' . $oldText;
									}
								}
							}
							
							$bundleUpdateQuery = "UPDATE products SET Reserved=? WHERE Name=?";
							$bundleUpdateStmt = $mysqli->stmt_init();
							$bundleUpdateStmt = $mysqli->prepare($bundleUpdateQuery);
							if ($bundleUpdateStmt){
								$bundleUpdateStmt->bind_param('ss', $newReservedText, $bundleItems);
								$bundleUpdateStmt->execute();
							}
							
							//if ($result2 = $mysqli->query("UPDATE products SET Reserved='".$newReservedText."' WHERE Name='".$bundleItems."'")) {
							//}
							
						}
					}
				}else{
					$newReservedText = '0';
					if(count(explode(',',$itemRow['Reserved'])) > 0){
						$oldReservedText = explode(',',$itemRow['Reserved']);
					}else{
						$oldReservedText = $itemRow['Reserved'];
					}
					foreach($oldReservedText as $oldText){
						if(!explode('|',$oldText)[1] == $_SESSION['reservation']){
							if($newReservedText == '0'){
								$newReservedText = $oldText;
							}else{
								$newReservedText = $newReservedText . ',' . $oldText;
							}
						}
					}
					
					$updateQuery = "UPDATE products SET Reserved=? WHERE Name=?";
					$updateStmt = $mysqli->stmt_init();
					$updateStmt = $mysqli->prepare($updateQuery);
					if ($updateStmt){
						$updateStmt->bind_param('ss', $newReservedText, $item[0]);
						$updateStmt->execute();
					}
							
					//if ($result3 = $mysqli->query("UPDATE products SET Reserved='".$newReservedText."' WHERE Name='".$item[0]."'")) {
					//}
				}
			}else{
				$newReservedText = '0';
				if(count(explode(',',$itemRow['Reserved'])) > 0){
					$oldReservedText = explode(',',$itemRow['Reserved']);
				}else{
					$oldReservedText = $itemRow['Reserved'];
				}
				foreach($oldReservedText as $oldText){
					if(!explode('|',$oldText)[1] == $_SESSION['reservation']){
						if($newReservedText == '0'){
							$newReservedText = $oldText;
						}else{
							$newReservedText = $newReservedText . ',' . $oldText;
						}
					}
				}
				
				$updateQuery = "UPDATE products SET Reserved=? WHERE Name=?";
				$updateStmt = $mysqli->stmt_init();
				$updateStmt = $mysqli->prepare($updateQuery);
				if ($updateStmt){
					$updateStmt->bind_param('ss', $newReservedText, $item[0]);
					$updateStmt->execute();
				}
				
				//if ($result2 = $mysqli->query("UPDATE products SET Reserved='".$newReservedText."' WHERE Name='".$item[0]."'")) {
				//}
			}			
		}
	}
}

//make email confirmation
$to = $eMail;

$subject = 'Your order details. Cornish Woodware.';

$itemsHTML = '';
foreach($itemList as $item){
	$itemsHTML .= "<a>".$item[0]."</a><br />";
}

$addressHTML = '';
foreach($address as $addressLine){
	$addressHTML .= "<a>".$addressLine."</a><br />";
}

$message = "<head>
<style>
.eMailPageElement h1{
	font-family: FoglihtenNo07, Times;
	font-size: 2em;
	line-height: 1em;
}
.eMailPageElement a{
	font-family: FoglihtenNo07, Times;
	font-size: 1.5em;
	line-height: 1.25em;
}
.eMailPageElement{
	position: relative;
	width: 90%;
	left: 5%;
	height: auto;
	background: #e7e3d4;
	border: 0;
	border-bottom: 1.5vw solid #b23850;
	font-size: 2em;
	line-height: 1em;
	padding: 5%;
	text-align: center;
	font-family: Timeless, arial;
	box-shadow: 0.3vw 0.3vw 2.2vw -0.45vw rgba(0,0,0,0.75);
	border-radius: 0 0 2.5vw 2.5vw;
}
</style>
</head>
<body>
<br />
<div class='eMailPageElement'>
	<img src='http://www.cornishWoodware.co.uk/images/favicon.png' />
	<h1>Thank you for your order!</h1>
	Your order of:
	<br />
	".$itemsHTML."
	is on it's way.
	<br />
	<br />
	<a>Sub-Total: &#163;".number_format((float)$subTotal/100, 2, '.', '')."</a>
	<br />
	<a>Delivery: &#163;".number_format((float)$delivery/100, 2, '.', '')."</a>
	<br />
	<a>Total: &#163;".number_format((float)($subTotal + $delivery)/100, 2, '.', '')."</a>
	<br />
	<br />
	Paid via Paypal.
	<br />
	<br />
	Delivery address:
	<br />
	".$addressHTML."
</div>
</body>";

$headers  = 'MIME-Version: 1.0' . "\r\n";
$headers .= 'Content-type: text/html; charset=iso-8859-1' . "\r\n";
$headers .= 'To: '.$name[0] . " " . $name[1] . " " . $name[2] .' <'.$eMail.'>' . "\r\n";
$headers .= 'From: Orders <CornishWoodware.co.uk>' . "\r\n";

mail($to, $subject, $message, $headers);

unset($_SESSION['item']);
unset($_SESSION['reservation']);


?>

<br class="pageBreak" />

<div class="pageTitleImage">
	<img src="images/ppsuccess.jpg" alt="big Picture 1" />
	<a>Thank <br />you!</a>
</div>

<div class="homepageColourBar">
<br />

	Your order has been authorized by PayPal and is being processed. <br />
	You will shortly recieve an eMail to confirm your order.<br /><br />
	
	You ordered:<br />
	<?php
	
	foreach($itemList as $item)
	{
		if ($result = $mysqli->query("SELECT * FROM products WHERE Name='".$item[0]."'")) {
			$row = mysqli_fetch_array($result, MYSQLI_NUM);
			$srcs = explode(',', $row[2]);
			
			if($srcs[0] === ''){
				if($item[0] === 'Promotion'){
					$temp = 'images/promo.png';
				}else{
					$temp = 'images/bundle.png';
				}
			}else{
				$temp = $srcs[0];
			}
			
			echo "<img src='".$temp."' alt='Picture of ".$item[0]."' class='ppSuccessImage' />";
			echo "<a class='ppSuccessText'> " . $item[1] . " x " . $item[0] . "</a>";
			echo "<br />";
		
		}
	}
	
	
	
	?>

</div>

</body>
</html>