<?php
include_once 'includes/db_connect.php';
include_once 'includes/functions.php';
 
sec_session_start();

?>

<!DOCTYPE html>
<html lang="en">

<?php
include "head.html";
echo "<title>CW Checkout</title></head>";

echo "<body>";

$postError = '';

$nameTitle = $_POST['nameTitle'];
$country = $_POST['country'];

if(isset($_POST['itemList'])){
	$itemText = $_POST['itemList'];
}else{
	$itemText = '';
	$postError = 'item';
}

if(isset($_POST['nameFirst']) && $_POST['nameFirst'] != ""){
	$nameFirst = $_POST['nameFirst'];
}else{
	$nameFirst = '';
	$postError = 'form';
}

if(isset($_POST['nameLast']) && $_POST['nameLast'] != ""){
	$nameLast = $_POST['nameLast'];
}else{
	$nameLast = '';
	$postError = 'form';
}

if(isset($_POST['phone']) && $_POST['phone'] != ""){
	$phone = $_POST['phone'];
}else{
	$phone = '';
	$postError = 'form';
}

if(isset($_POST['eMail']) && $_POST['eMail'] != ""){
	$eMail = $_POST['eMail'];
}else{
	$eMail = '';
	$postError = 'form';
}

if(isset($_POST['address1']) && $_POST['address1'] != ""){
	$address1 = $_POST['address1'];
}else{
	$address1 = '';
	$postError = 'form';
}

if(isset($_POST['address2']) && $_POST['address2'] != ""){
	$address2 = $_POST['address2'];
}else{
	$address2 = '';
}

if(isset($_POST['address3']) && $_POST['address3'] != ""){
	$address3 = $_POST['address3'];
}else{
	$address3 = '';
}

if(isset($_POST['city']) && $_POST['city'] != ""){
	$city = $_POST['city'];
}else{
	$city = '';
	$postError = 'form';
}

if(isset($_POST['state']) && $_POST['state'] != ""){
	$state = $_POST['state'];
}else{
	$state = '';
	$postError = 'form';
}

if(isset($_POST['zip']) && $_POST['zip'] != ""){
	$zip = $_POST['zip'];
}else{
	$zip = '';
	$postError = 'form';
}

		
if($postError === 'form'){
	header( "refresh:3; url=checkoutStep1.php?nameTitle=".$nameTitle."&nameFirst=".$nameFirst."&nameLast=".$nameLast."&phone=".$phone."&eMail=".$eMail."&address1=".$address1."&address2=".$address2."&address3=".$address3."&city=".$city."&state=".$state."&zip=".$zip."&country=".$country."&itemList=".$itemText."" );
	echo "<p>Sorry, there has been an error with the address data.</p>";
	echo "<p>Redirecting to the first step in 3 seconds.</p>";
}else if($postError === 'item'){
	header( "refresh:3; url=catalogue.php" ); 
	echo "<p>Sorry, there has been an error. Please try again.</p>";
	echo "<p>Redirecting to the catalogue in 3 seconds.</p>";
}else{
	
	
	$itemNames = (explode(',',$itemText));
				
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
	
	//timeout
	if(isset($_POST['itemList'])){
		$itemData = $_POST['itemList'];
	}else{
		$itemData = $_GET['itemList'];
	}
	$date = new DateTime();
	if((floor((strtotime(date('Y-m-d H:i:s', $_SESSION['reservation'])) - strtotime(date('Y-m-d H:i:s', $date->getTimestamp()))) / 60) * -1) >= 20){
		header( "refresh:0; url=timeout.php?time=".$_SESSION['reservation']."&itemList=".$itemData."" ); 
	};
	echo "<a href='timeout.php?time=".$_SESSION['reservation']."&itemList=".$itemData."&home=true'><img src='images/header.jpg' class='headerLogo2' alt='Logo' /></a>";
}

?>

<br />

<div class="checkoutImage">
	<a>
		Checkout:
	</a>
	
</div>

	<div class="cartPageContent">

		<a class="checkoutStepsWrapper">
			<span class="checkoutSteps"> Step 1: (Address) </span><span class="checkoutStepsActive"> Step 2: (Delivery) </span><span class="checkoutSteps"> Step 3: (Payment) </span>
		</a>

		<hr style='width: 100%'>
		
		<p class='checkoutProductListLeft'>
		Delivery of items:
		<br />
		<?php
		
		$biggestItem = 1;
		$subTotal = 0;
		
		foreach($itemList as $item){
			
			$itemQuery = "SELECT * FROM products WHERE Name=?";
			$itemStmt = $mysqli->stmt_init();
			$itemStmt = $mysqli->prepare($itemQuery);
								
			if ($itemStmt){
				$itemStmt->bind_param('s', $item[0]);
				$itemStmt->execute();
				$itemResult = get_result($itemStmt);
				$itemRow = array_shift($itemResult);
									
				$thumbSrc = explode(',',$itemRow['Src'])[0];
				$thumbClass = "checkoutProductThumb";
				if($itemRow['Offer'] != '0'){
					if(explode(']',explode('[',$itemRow['Offer'])[1])[0] === 'bundle'){
						$thumbSrc = "images/bundle.png";
						$thumbClass = "checkoutProductThumb2";
					}
				}
			
				if($itemRow['Offer'] != '0'){
					if(explode(']',explode('[',$itemRow['Offer'])[1])[0] == 'bundle'){
						echo "<img src='images/blank.png' alt='Offer thumbnail' class='checkoutProductThumbOffer' />";
						
					}else{
						echo "<img src='images/special.png' alt='Offer thumbnail' class='checkoutProductThumbOffer' />";
					}
				}else{
					echo "<img src='images/blank.png' alt='Offer thumbnail' class='checkoutProductThumbOffer' />";
				}
				
				echo "<img src='".$thumbSrc."' alt='Thumbnail for ".$item[0]."' class='".$thumbClass."' />";
				echo $item[1] . " x <a href='productpage.php?productName=".$item[0]."'>" . $item[0] . "</a><br />";
			
				if($itemRow['Size'] == 2){
					$biggestItem = 2;
				}
							
				if($itemRow['Offer'] !== '0'){
					if(explode(']',explode('[',$itemRow['Offer'])[1])[0] === 'bundle'){
						$bundlePrice = explode(')',explode('(',$itemRow['Offer'])[1])[0];
						$subTotal += ($bundlePrice * $item[1]);
					}else{
						$dealType = explode(']',explode('[',$itemRow['Offer'])[1])[0];
						$offerPrice = explode(')',explode('(',$itemRow['Offer'])[1])[0];
						if($dealType === '2for'){
							if($item[1] % 2 > 0){
								$subTotal += ($offerPrice * (($item[1] - 1) / 2)) + $itemRow['Price'];
							}else{
								$subTotal += $offerPrice * ($item[1] / 2);
							}
						}else if($dealType === 'reduceBy'){
							$newPrice = ($itemRow['Price'] / 100) * (100 - $offerPrice);
							$subTotal += ($newPrice * $item[1]);
						}else if($dealType === 'reduceTo'){
							$subTotal += ($offerPrice * $item[1]);
						}
					}
				}else{
					$subTotal += ($itemRow['Price'] * $item[1]);
				}
			}
		}
		
		?>
		</p>
		<p class='checkoutProductListRight'>
		<?php
			echo "<u>" . $nameTitle . " " . $nameFirst . " " . $nameLast . "</u><br />";
			echo $address1 . "<br />";
			if($address2 !== ''){
				echo $address2 . "<br />";
			}
			if($address3 !== ''){
				echo $address3 . "<br />";
			}
			echo $city . "<br />";
			echo $state . "<br />";
			echo $zip . "<br />";
			echo explode('-',$country)[1] . "<br />";
		?>
		
		</p>
		
		<br />
		
		<?php

		$deliveryCostRow = 0;
		$deliveryText = "";
		
		$canDeliverTo = false;
		if ($result = $mysqli->query("SELECT * FROM delivery")) {
			while($row = mysqli_fetch_array($result, MYSQLI_NUM)){
				$codes = explode(',',$row[3]);
				
				foreach ($codes as $code){
					if($code == explode('-',$_POST['country'])[0]){
						$deliveryCostRow = intval($row[$biggestItem]);
						$canDeliverTo = true;
						if($code === 'GB'){
							
							if ($result = $mysqli->query("SELECT * FROM delivery WHERE Country='UK-Highlands'")) {
								$row = mysqli_fetch_array($result, MYSQLI_NUM);
								$highlandsPrice = intval($row[$biggestItem]);
							}
							
							$prefix = substr($zip, 0, 2);
							$number = intval(substr($zip, 2, 4));
							$numberSingle = intval(substr($zip, 2, 3));
							if(strcasecmp($prefix,'GY') == 0 || strcasecmp($prefix,'JE') == 0 || strcasecmp($prefix,'IM') == 0 || strcasecmp($prefix,'BT') == 0 || strcasecmp($prefix,'KW') == 0 || strcasecmp($prefix,'IV') == 0){
								$deliveryCostRow = $highlandsPrice;
								$deliveryText = "Postcode detected as being in a remote location, and so the delivery cost has been set acordingly. Please see the <a href='delivery.php'>Delivery page</a> for more information.";
							}else if(strcasecmp($prefix,'TR') == 0 ){
								if($number >= 21 && $number <= 25){
									$deliveryCostRow = $highlandsPrice;
									$deliveryText = "Postcode detected as being in a remote location, and so the delivery cost has been set acordingly. Please see the <a href='delivery.php'>Delivery page</a> for more information.";
								}
							}else if(strcasecmp($prefix,'HS') == 0 ){
								if($numberSingle >= 1 && $number <= 9){
									$deliveryCostRow = $highlandsPrice;
									$deliveryText = "Postcode detected as being in a remote location, and so the delivery cost has been set acordingly. Please see the <a href='delivery.php'>Delivery page</a> for more information.";
								}
							}else if(strcasecmp($prefix,'PA') == 0 ){
								if($number >= 20 && $number <= 38 || $number >= 41 && $number <= 49 || $number >= 60 && $number <= 80){
									$deliveryCostRow = $highlandsPrice;
									$deliveryText = "Postcode detected as being in a remote location, and so the delivery cost has been set acordingly. Please see the <a href='delivery.php'>Delivery page</a> for more information.";
								}
							}else if(strcasecmp($prefix,'PH') == 0 ){
								if($number >= 16 && $number <= 26 || $number >= 30 && $number <= 44 || $number == 49 || $number == 50){
									$deliveryCostRow = $highlandsPrice;
									$deliveryText = "Postcode detected as being in a remote location, and so the delivery cost has been set acordingly. Please see the <a href='delivery.php'>Delivery page</a> for more information.";
								}
							}else if(strcasecmp($prefix,'ZE') == 0 ){
								if($numberSingle >= 1 && $number <= 3){
									$deliveryCostRow = $highlandsPrice;
									$deliveryText = "Postcode detected as being in a remote location, and so the delivery cost has been set acordingly. Please see the <a href='delivery.php'>Delivery page</a> for more information.";
								}
							}else if(strcasecmp($prefix,'PO') == 0 ){
								if($number >= 30 && $number <= 41){
									$deliveryCostRow = $highlandsPrice;
									$deliveryText = "Postcode detected as being in a remote location, and so the delivery cost has been set acordingly. Please see the <a href='delivery.php'>Delivery page</a> for more information.";
								}
							}
						}
						
						if($subTotal < 3000){
							if(explode('-', $row[$biggestItem])[1][0] === '0'){
								$deliveryCost = 99999;
								
							}else{
								$deliveryCost = $deliveryCostRow * 1.2;
								
							}
						}else if($subTotal >= 3000 && $subTotal < 7000){
							if(explode('-', $row[$biggestItem])[1][1] === '0'){
								$deliveryCost = 99999;
							}else{
								$deliveryCost = $deliveryCostRow;
							}
						}else{
							if(explode('-', $row[$biggestItem])[1][2] === '0'){
								$deliveryCost = 99999;
							}else{
								$deliveryCost = $deliveryCostRow * 0.8;
							}
						}
						if($deliveryCost === 99999){
							$canDeliverTo = false;
							$deliveryText = "Delivery to " . explode('-',$_POST['country'])[1] . " unavailable for this order. Please see <a href='delivery.php'>Delivery</a> page for more information.";
						}
					}
				}
			}
					
		}
		
		$nameArray = array($nameTitle, $nameFirst, $nameLast);
		$address = array($address1);
			if($address2 !== ''){
				array_push($address, $address2);
			}
			if($address3 !== ''){
				array_push($address, $address3);
			}
			array_push($address, $city);
			array_push($address, $state);
			array_push($address, $zip);
			array_push($address, $country);
			
		if($canDeliverTo !== true){
			$name = $nameTitle . " " . $nameFirst . " " . $nameLast;

			
			
			echo "<p class='checkoutProductListLeft'>";
			if($deliveryText !== ""){
				echo $deliveryText;
			}else{
				echo "So sorry, in order to book delivery to <i>".explode('-',$_POST['country'])[1]."</i> you will need to contact us for a custom delivery quote. <br />";
				echo "Please use the <a href='contact.php?items=".str_replace('{', '%7B', str_replace('}', '%7D', serialize($itemList)))."&name=".$name."&address=".str_replace('{', '%7B', str_replace('}', '%7D', serialize($address)))."'>Contact page</a> to send us a message, and we will get back to you as soon as possible.";
			}
			echo "</p>";
		}else{

			echo "<div class='checkoutProductListLeft'>";
				if($deliveryText !== ""){
					echo $deliveryText . "<br />";
				}
				echo "Sub-Total: <u>£" . number_format((float)$subTotal/100, 2, '.', '') . "</u><br />";
				
				if(isset($_POST['promo'])){
					
					$promoQuery = "SELECT * FROM offers WHERE Code=?";
					$promoStmt = $mysqli->stmt_init();
					$promoStmt = $mysqli->prepare($promoQuery);
								
					if ($promoStmt){
						$promoStmt->bind_param('s', $_POST['promo']);
						$promoStmt->execute();
						$promoResult = get_result($promoStmt);

						if(count($promoResult) > 0){
							
							$promoRow = array_shift($promoResult);

							if(explode(']', explode('[',$promoRow['Offer'])[1])[0] === 'reduceBy'){
								$reduction = ($subTotal / 100) * explode(')', explode('(',$promoRow['Offer'])[1])[0];
								echo "Promo: <u>-£" . number_format((float)($reduction)/100, 2, '.', '') . "</u><br />";
								$subTotal = $subTotal - $reduction;
							}
							
							echo "Delivery: <u>£" . number_format((float)$deliveryCost/100, 2, '.', '') . "</u><br /><br />";
							echo "Total: £" . number_format((float)($deliveryCost + $subTotal)/100, 2, '.', '');
							
						}else{
							unset($_POST['promo']);
						}
						
					}
				}
				
				if(!isset($_POST['promo'])){
					echo "Delivery: <u>£" . number_format((float)$deliveryCost/100, 2, '.', '') . "</u><br />";
					echo "Total: £" . number_format((float)($deliveryCost + $subTotal)/100, 2, '.', '') . "<br /><br />";
					echo "<fieldset class='checkoutPromoBox'>";
						echo "<span class=\"itemTitle\"><a class='cartTotalContainerTitles'>Promo Code</a></span><hr>";
						echo "<form action='checkoutStep2.php' method='POST'>";
							echo "<input type='text' name='promo' placeholder='Promo Code' required />";
							 
							echo "<input type='hidden' name='nameTitle' value='".$_POST['nameTitle']."' />";
							echo "<input type='hidden' name='country' value='".$_POST['country']."' />";
							echo "<input type='hidden' name='itemList' value='".$_POST['itemList']."' />";
							echo "<input type='hidden' name='nameFirst' value='".$_POST['nameFirst']."' />";
							echo "<input type='hidden' name='nameLast' value='".$_POST['nameLast']."' />";
							echo "<input type='hidden' name='phone' value='".$_POST['phone']."' />";
							echo "<input type='hidden' name='eMail' value='".$_POST['eMail']."' />";
							echo "<input type='hidden' name='address1' value='".$_POST['address1']."' />";
							echo "<input type='hidden' name='address2' value='".$_POST['address2']."' />";
							echo "<input type='hidden' name='address3' value='".$_POST['address3']."' />";
							echo "<input type='hidden' name='city' value='".$_POST['city']."' />";
							echo "<input type='hidden' name='state' value='".$_POST['state']."' />";
							echo "<input type='hidden' name='zip' value='".$_POST['zip']."' />";
							
							echo "<input type='submit' value='Apply' />";
						echo "</form>";
					echo "</fieldset>";
					echo "<br />";
				}
				
				
				
			echo "</div>";
		
		}
		
		?>
		
		<?php
		
		if($canDeliverTo === true){
			echo "<div class='checkoutProductListRight'>";
			echo "<br /><br />";
			
				if(isset($_POST['promo'])){
					$promo = $_POST['promo'];
				}else{
					$promo = '';
				}
				
				echo "<form class='checkoutForm' action='checkoutStep3.php' method='POST'>";
					echo "<input type='hidden' name='itemList' value='" . $_POST['itemList']  . "' />";
					echo "<input type='hidden' name='name' value='" .  serialize($nameArray)  . "' />";
					echo "<input type='hidden' name='address' value='" .  serialize($address)  . "' />";
					echo "<input type='hidden' name='phone' value='" . $phone  . "' />";
					echo "<input type='hidden' name='eMail' value='" . $eMail  . "' />";
					echo "<input type='hidden' name='subTotal' value='" .  $subTotal  . "' />";
					echo "<input type='hidden' name='deliveryCost' value='" .  $deliveryCost  . "' />";
					if(isset($_POST['promo'])){
						echo "<input type='hidden' name='promo' value='" .  $promo  . "' />";
					}
					echo "<input type='submit' value='Continue' class='checkoutButton' />";
				echo "</form>";

				echo "<a href='checkoutStep1.php?nameTitle=".$nameTitle."&nameFirst=".$nameFirst."&nameLast=".$nameLast."&phone=".$phone."&eMail=".$eMail."&address1=".$address1."&address2=".$address2."&address3=".$address3."&city=".$city."&state=".$state."&zip=".$zip."&country=".$country."&itemList=".$itemText."&promo=".$promo."'><sub>Return to step 1</sub></a>";
				
			echo "</div>";
		}
		
		?>
		
	</div>

</body>

</html>