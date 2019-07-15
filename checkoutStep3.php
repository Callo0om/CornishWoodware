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

include "header2.php";

$postError = '';

if(isset($_POST['itemList'])){
	$itemText = $_POST['itemList'];
}else{
	$postError = 'item';
}

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
	$eMail = '';
	$postError = 'form';
}

if(isset($_POST['subTotal'])){
	$subTotal = $_POST['subTotal'];
}else{
	$postError = 'form';
}

if(isset($_POST['deliveryCost'])){
	$deliveryCost = $_POST['deliveryCost'];
}else{
	$postError = 'form';
}

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

if($postError === 'form'){
	echo "<p>Sorry, there has been an error with the address or delivery data.</p>";
	echo "<p>Redirecting to the first step in 3 seconds.</p>";
	header( "refresh:3; url=checkoutStep1.php?nameTitle=".$nameTitle."&nameFirst=".$nameFirst."&nameLast=".$nameLast."&phone=".$phone."&eMail=".$eMail."&address1=".$address1."&address2=".$address2."&address3=".$address3."&city=".$city."&state=".$state."&zip=".$zip."&country=".$country."&itemList=".$itemText."" );
}else if($postError === 'item'){
	echo "<p>Sorry, there has been an error. Please try again.</p>";
	echo "<p>Redirecting to the catalogue in 3 seconds.</p>";
	header( "refresh:3; url=catalogue.php" ); 
}else{
	
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
	
	$serialName = serialize(array($nameTitle, $nameFirst, $nameLast));
	$serialAddress = serialize($address);
	
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
	$date = new DateTime();
	if((floor((strtotime(date('Y-m-d H:i:s', $_SESSION['reservation'])) - strtotime(date('Y-m-d H:i:s', $date->getTimestamp()))) / 60) * -1) >= 20){
		header( "refresh:0; url=timeout.php?time=".$_SESSION['reservation']."&itemList=".$_POST['itemList']."" ); 
	};
	echo "<a href='timeout.php?time=".$_SESSION['reservation']."&itemList=".$itemData."&home=true'><img src='images/header.jpg' class='headerLogo2' alt='CornishWoodware Logo' /></a>";
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
			<span class="checkoutSteps"> Step 1: (Address) </span><span class="checkoutSteps"> Step 2: (Delivery) </span><span class="checkoutStepsActive"> Step 3: (Payment) </span>
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
			echo  explode('-',$country)[1] . "<br />";
		?>
		
		</p>
		
		<?php
		
		echo "<p class='checkoutProductListLeft'>";
		echo "Sub-Total: £" . number_format((float)$subTotal/100, 2, '.', '') . "<br />";
		
		$reduction = 0;					
		if(isset($_POST['promo'])){
			$promoQuery = "SELECT * FROM offers WHERE Code=?";
			$promoStmt = $mysqli->stmt_init();
			$promoStmt = $mysqli->prepare($promoQuery);
							
			if ($promoStmt){
				$promoStmt->bind_param('s', $_POST['promo']);
				$promoStmt->execute();
				$promoResult = get_result($promoStmt);
				$promoRow = array_shift($promoResult);

					if(explode(']', explode('[',$promoRow['Offer'])[1])[0] === 'reduceBy'){
						$reduction = ($subTotal / 100) * explode(')', explode('(',$promoRow['Offer'])[1])[0];
						echo "Promo: <u>-£" . number_format((float)($reduction)/100, 2, '.', '') . "</u><br />";
						$subTotal = $subTotal - $reduction;
					}
					
				}
			}
				
			echo "Delivery: £" . number_format((float)$deliveryCost/100, 2, '.', '') . "<br /><br />";
			echo "Total: £" . number_format((float)($deliveryCost + $subTotal)/100, 2, '.', '');
		echo "</p>";
		
		echo "<div class='checkoutProductListRight'>";

		?>
		
		<br />
		
		<div class='checkoutProductListRight'>
			<div id="paypal-button-container"></div>
				<script src="https://www.paypalobjects.com/api/checkout.js"></script>
				<script>
				// Render the PayPal button
				paypal.Button.render({
				// Set your environment
				env: 'sandbox', // sandbox | production

				// Specify the style of the button
				style: {
				   // horizontal | vertical
				  size:   'responsive',    // medium | large | responsive
				  shape:  'pill',      // pill | rect
				  color:  'gold',       // gold | blue | silver | white | black
				  label: 'pay',
				  fundingicons: 'true'
				},

				// Specify allowed and disallowed funding sources
				//
				// Options:
				// - paypal.FUNDING.CARD
				// - paypal.FUNDING.CREDIT
				// - paypal.FUNDING.ELV
				funding: {
				  allowed: [
					paypal.FUNDING.CARD
					
				  ],
				  disallowed: [
					paypal.FUNDING.CREDIT
				  ]
				},

				// Enable Pay Now checkout flow (optional)
				commit: true,

				// PayPal Client IDs - replace with your own
				// Create a PayPal app: https://developer.paypal.com/developer/applications/create
				client: {
				  sandbox: 'AY60Xnksvoq-C3REo1_XQYJpitD6lUR8mEYDFONAzdjOnPpqFP-vTbrwI4661kr-zCNbEo7BrYwBuprz',
				  production: 'ATmQ3R2ettIdQa_VV8DekaYMhx4Uhnz3WdYgLedU8L3urJTWcO_DOOL0IeC4leVqxZYsmv3vWu26yd-A'
				},

				<?php
				
				$subTotal = 0;
				$total = 0;
				
				foreach($itemList as $item){
					$paypalQuery = "SELECT * FROM products WHERE Name=?";
					$paypalStmt = $mysqli->stmt_init();
					$paypalStmt = $mysqli->prepare($paypalQuery);
								
					if ($paypalStmt){
						$paypalStmt->bind_param('s', $item[0]);
						$paypalStmt->execute();
						$paypalResult = get_result($paypalStmt);
						$paypalRow = array_shift($paypalResult);
											
						$itemPrice = $paypalRow['Price'];
						if($paypalRow['Offer'] !== '0'){
							$dealType = explode(']',explode('[',$paypalRow['Offer'])[1])[0];
							$offerPrice = explode(')',explode('(',$paypalRow['Offer'])[1])[0];
							if($dealType === 'reduceTo'){
								$itemPrice = $offerPrice;
								$subTotal += $itemPrice * $item[1];
							}else if($dealType === 'reduceBy'){
								$itemPrice = ($paypalRow['Price'] / 100) * (100 - $offerPrice);
								$subTotal += $itemPrice * $item[1];
							}else if($dealType === '2for'){
								if($item[1] % 2 > 0){
									$itemPrice = $offerPrice / 2;
									$tempSubTotal = (($itemPrice * ($item[1] - 1)) + $paypalRow['Price']);
														
									$itemPrice = floor($tempSubTotal / $item[1]);
									$subTotal += ($itemPrice * $item[1]);
											
								}else{
									$itemPrice = $offerPrice / 2;
									$subTotal += $itemPrice * $item[1];
								}
							}else if($dealType === 'bundle'){
								$subTotal += ($offerPrice * $item[1]);
							}
						}else{
							$subTotal += $itemPrice * $item[1];
						}
					}
				}
				$total = $subTotal + $deliveryCost;
				
				?>
				
				// Set up a payment
				payment: function(data, actions) {
				  return actions.payment.create({
					transactions: [{
					  amount: {
						total: '<?php echo number_format((float)($total - $reduction)/100, 2, '.', ''); ?>',
						currency: 'GBP',
						details: {
						  subtotal: '<?php echo number_format((float)($subTotal - $reduction)/100, 2, '.', ''); ?>',
						  tax: '0.00',
						  shipping: '<?php echo number_format((float)$deliveryCost/100, 2, '.', ''); ?>',
						  handling_fee: '0.00'
						}
					  },
					  description: 'The payment transaction description.',
					  custom: '90048630024435',
					  //invoice_number: '12345', Insert a unique invoice number
					  payment_options: {
						allowed_payment_method: 'INSTANT_FUNDING_SOURCE'
					  },
					  soft_descriptor: 'ECHI5786786',
					  item_list: {
						items: [
						<?php
						
						$newItemList = "";
						
							foreach($itemList as $item){
								$paypalQuery2 = "SELECT * FROM products WHERE Name=?";
								$paypalStmt2 = $mysqli->stmt_init();
								$paypalStmt2 = $mysqli->prepare($paypalQuery2);
											
								if ($paypalStmt2){
									$paypalStmt2->bind_param('s', $item[0]);
									$paypalStmt2->execute();
									$paypalResult2 = get_result($paypalStmt2);
									$paypalRow2 = array_shift($paypalResult2);
									
									$itemPrice = $paypalRow2['Price'];
									if($paypalRow2['Offer'] !== '0'){
										$dealType = explode(']',explode('[',$paypalRow2['Offer'])[1])[0];
										$offerPrice = explode(')',explode('(',$paypalRow2['Offer'])[1])[0];
										if($dealType === 'reduceTo'){
											$itemPrice = $offerPrice;
										}else if($dealType === 'reduceBy'){
											$itemPrice = ($paypalRow2['Price'] / 100) * (100 - $offerPrice);
										}else if($dealType === '2for'){
											$itemPrice = $paypalRow2['Price'];
										}
									}
									
									$description = $paypalRow2['Type'];
									if($paypalRow2['Offer'] !== '0'){
										$dealType = explode(']',explode('[',$paypalRow2['Offer'])[1])[0];
										if($dealType === '2for'){
											$offerPrice = explode(')',explode('(',$paypalRow2['Offer'])[1])[0];
													
													//fix this
													//its replacing subTotal with just this item
											if($item[1] % 2 > 0){
												$itemPrice = $offerPrice / 2;
												$tempSubTotal = ($itemPrice * ($item[1] - 1)) + $paypalRow2['Price'];
														
												$itemPrice = floor($tempSubTotal / $item[1]);
												$tempSubTotal = $itemPrice * $item[1];
													
											}else{
												$itemPrice = $offerPrice / 2;
												$tempSubTotal = $itemPrice * $item[1];
											}
										}else if($dealType === 'bundle'){
											$itemPrice = $offerPrice;
											$description = "Bundle offer: ";
											foreach(explode(',', explode('}',explode('{',$paypalRow2['Offer'])[1])[0]) as $bundleItem){
												$description .= $bundleItem . " + ";
											}
											$description = substr($description, 0, -3);
										}
									}
									
									echo "{";
									echo "name: '".$paypalRow2['Name']."',";
									echo "description: '".$description."',";
									echo "quantity: '".$item[1]."',";
									echo "price: '".number_format((float)$itemPrice/100, 2, '.', '')."',";
									echo "currency: 'GBP'";
									echo "},";
									$newItemList .= $paypalRow2['Name'] . "|" . $item[1] . "|" . $itemPrice . ",";
								}
							}
							if(isset($_POST['promo'])){
								echo "{";
								echo "name: 'Promotion',";
								echo "description: '".$_POST['promo']."',";
								echo "quantity: '1',";
								echo "price: '".number_format((float)($reduction * -1)/100, 2, '.', '')."',";
								echo "currency: 'GBP'";
								echo "},";
								$newItemList .= "Promotion|1|" . ($reduction * -1) . ",";
							}
									
							$newItemList = substr($newItemList, 0, -1);
						?>
						]
						,
						shipping_address: {
						recipient_name: '<?php echo $nameTitle . " " . $nameFirst . " " . $nameLast; ?>',
						  line1: '<?php echo $address1; ?>',
						  <?php
						  if($address2 != ''){
							  if($address3 != ''){
								$ppAddress = $address2 . ", " . $address3;
							  }else{
								$ppAddress = $address2;
							  }
						  }else{
							  if($address3 != ''){
								  $ppAddress = $address3;
							  }else{
								  $ppAddress = '';
							  }
						  }
						  ?>
						  line2: '<?php echo $ppAddress; ?>',
						  city: '<?php echo $city; ?>',
						  country_code: '<?php echo str_replace(' ', '', explode('-',$country)[0]); ?>',
						  postal_code: '<?php echo $zip; ?>',
						  phone: '<?php echo $phone; ?>',
						  state: '<?php echo $state; ?>'
						}
					  }
					}],
					note_to_payer: 'Contact us for any questions on your order.'
				  });
				},

				onAuthorize: function (data, actions) {

					return actions.payment.execute().then(function(payment) {
						console.log(payment.payer.payer_info);
						
						if (payment.failure_reason === 'UNABLE_TO_COMPLETE_TRANSACTION' || payment.error === 'INVALID_PAYMENT_METHOD' || payment.error === 'PAYER_CANNOT_PAY' || payment.error === 'CANNOT_PAY_THIS_PAYEE' ||  payment.error === 'REDIRECT_REQUIRED' || payment.error === 'PAYEE_FILTER_RESTRICTIONS') {
							return actions.restart();
						}
						
						if (payment.error === 'INSTRUMENT_DECLINED') {
							return actions.restart();
						}
						
						var form = document.createElement("form");
						
						var name = document.createElement("input");
						var phone = document.createElement("input");
						var eMail = document.createElement("input");	
						var address = document.createElement("input");
						var itemList = document.createElement("input");
						var ignoreSession = document.createElement("input");
						var timeValue = document.createElement("input");
						var subTotal = document.createElement("input");
						var delivery = document.createElement("input");
						var paymentTime = document.createElement("input");

						form.method = "POST";
						form.action = "PPSuccess.php";

						name.value = '<?php echo $serialName; ?>';
						name.name = "name";
						name.type = "hidden";
						form.appendChild(name);
						
						phone.value = "<?php echo $phone; ?>";
						phone.name = "phone";
						phone.type = "hidden";
						form.appendChild(phone);
						
						eMail.value = "<?php echo $eMail; ?>";
						eMail.name = "eMail";
						eMail.type = "hidden";
						form.appendChild(eMail);
						
						
						address.value = '<?php echo $serialAddress; ?>';
						address.name = "address";
						address.type = "hidden";
						form.appendChild(address);


						itemList.value = "<?php echo $newItemList; ?>";
						itemList.name = "itemList";
						itemList.type = "hidden";
						form.appendChild(itemList);

						ignoreSession.value = "true";
						ignoreSession.name = "ignoreSession";
						ignoreSession.type = "hidden";
						form.appendChild(ignoreSession);

						timeValue.value = "<?php echo $_SESSION['reservation']; ?>";
						timeValue.name = "timeValue";
						timeValue.type = "hidden";
						form.appendChild(timeValue);

						subTotal.value = "<?php echo $subTotal - $reduction; ?>";
						subTotal.name = "subTotal";
						subTotal.type = "hidden";
						form.appendChild(subTotal);

						delivery.value = "<?php echo $deliveryCost; ?>";
						delivery.name = "delivery";
						delivery.type = "hidden";
						form.appendChild(delivery);

						paymentTime.value = payment.create_time;
						paymentTime.name = "paymentTime";
						paymentTime.type = "hidden";
						form.appendChild(paymentTime);

						document.body.appendChild(form);

						form.submit();
						
					});
				}
				
				}, '#paypal-button-container');
				</script>
			</div>
			
			<?php
			if(isset($_POST['promo'])){
				$promo = $_POST['promo'];
			}else{
				$promo = '';
			}
				
			echo "<a href='checkoutStep1.php?nameTitle=".$nameTitle."&nameFirst=".$nameFirst."&nameLast=".$nameLast."&phone=".$phone."&eMail=".$eMail."&address1=".$address1."&address2=".$address2."&address3=".$address3."&city=".$city."&state=".$state."&zip=".$zip."&country=".$country."&itemList=".$itemText."&promo=".$promo."'><sub>Return to step 1</sub></a>";
					
		echo "</div>";
		
		
		?>
		
	</div>

</body>

</html>