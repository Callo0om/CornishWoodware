<?php
include_once 'includes/db_connect.php';
include_once 'includes/functions.php';
 
sec_session_start();
 
if (login_check($mysqli) == true) {
    $logged = true;
} else {
    $logged = false;
}

?>
<!DOCTYPE html>
<html lang="en">

<?php
include "head.html";
echo "<title>CW Shopping Cart</title></head>";

echo "<body>";

if (isset($_GET['error'])) {
    echo '<p class="error">Error Logging In!</p>';
}

include "header.php";


if (isset($_GET['postageCountry'])){
	$_SESSION['postageCountry'] = $_GET['postageCountry'];
}

if (isset($_SESSION['postageCountry'])){
	$postageCountry = $_SESSION['postageCountry'];
}else{
	$postageCountry = 'UK-Mainland';
}
?> 

<?php
if ($logged == true) {
	$title = "<a href='includes/logout.php?returnPage=deals.php' style='color: Black;'>Log out</a>";
	include "nav.php";
} else {
	$title = "My Cart <img src='images/cartImages/cartImage_300.png' srcset='images/cartImages/cartImage_300.png 300w, images/cartImages/cartImage_600.png 600w' sizes='(max-width: 550px) 100vw, 50vw' class='cartMainImage' alt='big Picture'>";
	include "nav.php";
}
?>

<br class="pageBreak" />



<div class="cartPageContent">
	<div class='cartItemsList'>
		
		<?php
		
		$updatedItemList = '';
		
		function displayCartItem($item, $offer)
		{
			$runningPrice = 0;
			global $mysqli;
			global $postageCountry;
			global $higestDeliverySize;
			
			$query = "SELECT * FROM products WHERE Name=?";
			$stmt = $mysqli->stmt_init();
			$stmt = $mysqli->prepare($query);
			
			if ($stmt){
				$stmt->bind_param('s', $item[0]);
				$stmt->execute();
				$result = get_result($stmt);
				$row = array_shift($result);

					echo "<div class='cartItemContainer'>";
						if($offer !== '0'){
							if($offer === 'bundle'){
								echo "<span><a class='itemTitle' style='color: #09859b'> -Collection- " . $row['Name'] . " </a>";
								$bundlePrice = explode(')',explode('(',$row['Offer'])[1])[0];
								$priceText = "Bundle price: <u>£" . number_format((float)$bundlePrice/100, 2, '.', '') . "</u>";
							}else{
								echo "<span><a href='productPage.php?productName=" . $row['Name'] . "' class='itemTitle' style='color: #228d20'> -Special- " . $row['Name'] . " </a>";
								$dealType = explode(']',explode('[',$row['Offer'])[1])[0];
								$offerPrice = explode(')',explode('(',$row['Offer'])[1])[0];
								if($dealType === '2for'){
									if($item[1] % 2 > 0){
										$runningPrice += ($offerPrice * (($item[1] - 1) / 2)) + $row['Price'];
									}else{
										$runningPrice += $offerPrice * ($item[1] / 2);
									}
									$priceText = "Price each: <u>£" . number_format((float)$row['Price']/100, 2, '.', '') . "</u> Or <u>2 for £" . number_format((float)$offerPrice/100, 2, '.', '') . "</u>";
								}else if($dealType === 'reduceBy'){
									$newPrice = ($row['Price'] / 100) * (100 - $offerPrice);
									$priceText = "Price each: <strike>£" . number_format((float)$row['Price']/100, 2, '.', '') . "</strike> <u>£" . number_format((float)$newPrice/100, 2, '.', '') . "</u>";
									$runningPrice += ($newPrice * $item[1]);
								}else if($dealType === 'reduceTo'){
									$priceText = "Price each: <strike>£" . number_format((float)$row['Price']/100, 2, '.', '') . "</strike> <u>£" . number_format((float)$offerPrice/100, 2, '.', '') . "</u>";
									$runningPrice += ($offerPrice * $item[1]);
								}
							}
						}else{
							echo "<span><a href='productPage.php?productName=" . $row['Name'] . "' class='itemTitle'>" . $row['Name'] . " </a>";
							$runningPrice += ($row['Price'] * $item[1]);
							$priceText = "Price each: <u>£" . number_format((float)$row['Price']/100, 2, '.', '') . "</u>";
						}
						$price = number_format((float)$row['Price']/100, 2, '.', '');
						
						
						echo "</span>";
						
						if($offer === 'bundle'){
							$bundleItems = explode(',', explode('}',explode('{',$row['Offer'])[1])[0]);
							$bundleUnavailable = false;

							$lowestBundleItemRemaining = 999;
							echo "<div class='cartTable'>";
							foreach($bundleItems as $bundleItem){									
								$bundleQuery = "SELECT * FROM products WHERE Name=?";
								$bundleStmt = $mysqli->stmt_init();
								$bundleStmt = $mysqli->prepare($bundleQuery);
								
								if ($bundleStmt){
									$bundleStmt->bind_param('s', $bundleItem);
									$bundleStmt->execute();
									$bundleResult = get_result($bundleStmt);
									$bundleRow = array_shift($bundleResult);
									$bundleSrcs = explode(',', $bundleRow['Src']);
									
									$adjustedRemaining = $bundleRow['Remaining'] - intval(explode('|',$bundleRow['Reserved'])[0]);
																	
									if($adjustedRemaining < 1){
										$bundleUnavailable = true;
									}else{
										if($adjustedRemaining < $lowestBundleItemRemaining){
											$lowestBundleItemRemaining = $adjustedRemaining;
										}
									}
										
										
										
									global $itemList;
									foreach($itemList as $bundleCheckItem){
										if($bundleRow['Name'] === $bundleCheckItem[0]){
											if($item[1] + $bundleCheckItem[1] > $adjustedRemaining){
												$bundleUnavailable = true;
											}
										}else{
											if($item[1] > $adjustedRemaining){
												$bundleUnavailable = true;
											}
										}
									}
														
									echo "<div class='bundleTableCell'>";
										if($adjustedRemaining < 1){
											echo "<img src='images/outOfStock.png' alt='Error' class='bundleImageThumb'>";
											$runningPrice = 0.0;
											$bundleUnavailable = true;
										}else{
											echo "<a href='productPage.php?productName=" . $bundleRow['Name'] . "&returnPage=deals.php'><img src='" . $bundleSrcs[0] . "' alt='Product: " . $bundleRow['Name'] . "' class='bundleImageThumb' name='anchor".$bundleRow['Name']."'></a>";
										}
													
										if($bundleUnavailable === true){
											echo "<div class='cartOutOfStock'>One or more items out of stock.</div>";
										}
													
									echo "</div>";
												
								}
								
							}
							
							if($bundleUnavailable !== true){
								$runningPrice += ($bundlePrice * $item[1]);
							}
							
							echo "</div>";
							
							
										
						}else{
							echo "<a href='productPage.php?productName=" . $row['Name'] . "&returnPage=cart.php'><img src='".explode(',',$row['Src'])[0]."' class='cartItemImage' alt='Product: " . $row['Name'] . "' /></a>";
						}					
						
						if(isset($bundleUnavailable) === true && $bundleUnavailable === true){
							$higestDeliverySize = 1;
						}
	
							$quantityQuery = "SELECT * FROM products WHERE Name=?";
							$quantityStmt = $mysqli->stmt_init();
							$quantityStmt = $mysqli->prepare($quantityQuery);
								
							if ($quantityStmt){
								$quantityStmt->bind_param('s', $item[0]);
								$quantityStmt->execute();
								$quantityResult = get_result($quantityStmt);
								$quantityRow = array_shift($quantityResult);
								
								global $updatedItemList;
								
								$totalReserved = 0;
								foreach(explode(',',$quantityRow['Reserved']) as $reserved){
									$totalReserved = $totalReserved + intval(explode('|',$reserved)[0]);
								}
								$remaining = $quantityRow['Remaining'] - $totalReserved;
								echo $priceText." <br class='breaks' /><br /> ";
								echo "<hr class='rules' />";
								if($quantityRow['Offer'] != '0'){
									$dealType = explode(']',explode('[',$quantityRow['Offer'])[1])[0];
								}else{
									$dealType = '0';
								}
								
								
									if($item[1] > $remaining && $dealType != 'bundle'){
										if($quantityRow['Quantity'] > 1){
											$runningPrice = ($runningPrice / $item[1]) * $remaining;
											echo "<hr>";
											echo "<br />So sorry, somebody is currently buying one-or-more of these items, if they complete the purchase the availability will be adjusted. <br /><br />";
											echo "<hr>";
											echo "<div class='buyNowFlexContainer'>";
											echo "<a>Quanity:</a> &nbsp;&nbsp;";
											echo "<input type='text' readonly class='cartQuantityBox' value=' " . $item[1] . " ' name='quantityBox' style='text-decoration: line-through; margin-right: 0.5vw; background: #979797; color: #595959;' />";
											echo "<input type='text' readonly class='cartQuantityBox' value=' ".$remaining." ' name='quantityBox' style='margin-left: 0;' />";
											for($i = 0; $i < $remaining; $i++){
												$updatedItemList .= "," . $item[0];
											}
										}else{
											$runningPrice = 0.0;
											echo "<hr>";
											echo "<br />So sorry, somebody is currently buying this item, if they complete the purchase it will be removed from the site or if not then it will become available again. <br /><br />";
											echo "<hr>";
											echo "<div class='buyNowFlexContainer'>";
											echo "<a>Quanity:</a> &nbsp;&nbsp;";
											echo "<input type=\"text\" readonly class='cartQuantityBox' value=\" " . $item[1] . " \" name=\"quantityBox\" style=\" text-decoration: line-through; background: #979797; color: #595959; margin-right: 0; \" />";
											echo "<input type=\"text\" readonly class='cartQuantityBox' value=\" 0 \" name=\"quantityBox\" style=\" margin-left: 0; \" />";
										}
									}else{
										echo "<div class='buyNowFlexContainer'>";
										echo "<a>Quanity:</a> &nbsp;&nbsp;";
										echo "<input type=\"text\" readonly class='cartQuantityBox' value=\" " . $item[1] . " \" name=\"quantityBox\" />";
										for($i = 0; $i < $item[1]; $i++){
											$updatedItemList .= "," . $item[0];
										}
									}
									echo "<div class='dealsPlusMinusButtonsContainer'>";
										if($dealType != 'bundle'){
											if($item[1] < $remaining){
												echo "<input type=\"button\" alt=\"Plus Button\" class='dealsPlusMinusButtons' value='+' onclick=\"addToCart('".$row['Name']."','".$item[1]."','".$remaining."')\" />";
											}else{
												echo "<input type='submit' disabled alt='Plus Button' value=' &#8679 ' style='text-decoration: line-through; background: #979797; color: #595959;' class='dealsPlusMinusButtons' />";
											}
										}else{
											$lowestBundleItemRemaining = 999;
											foreach($bundleItems as $bundleItem){
												
												$bundleQuery2 = "SELECT * FROM products WHERE Name=?";
												$bundleStmt2 = $mysqli->stmt_init();
												$bundleStmt2 = $mysqli->prepare($bundleQuery2);
													
												if ($bundleStmt2){
													$bundleStmt2->bind_param('s', $bundleItem);
													$bundleStmt2->execute();
													$bundleResult2 = get_result($bundleStmt2);
													$bundleRow2 = array_shift($bundleResult2);
												
													$adjustedRemaining = $bundleRow2['Remaining'] - intval(explode('|',$bundleRow2['Reserved'])[0]);
																					
													if($adjustedRemaining < 1){
														$bundleUnavailable = true;
													}else{
														if($adjustedRemaining < $lowestBundleItemRemaining){
															$lowestBundleItemRemaining = $adjustedRemaining;
														}
													}
												}
											}
											if($item[1] < $lowestBundleItemRemaining){
												echo "<input type=\"button\" alt=\"Plus Button\" class='dealsPlusMinusButtons' value='+' onclick=\"addToCart('".$row['Name']."','".$item[1]."','".$lowestBundleItemRemaining."')\" />";
											}else{
												echo "<input type='submit' disabled alt='Plus Button' value=' &#8679 ' style='text-decoration: line-through; background: #979797; color: #595959;' class='dealsPlusMinusButtons' />";
											}
										}
										echo "<input type=\"button\" alt=\"Minus Button\"class='dealsPlusMinusButtons' value='-' onclick=\"removeFromCart('".$row['Name']."')\" />";
									echo "</div>";
								}
							echo "</div>";
							
							echo "<br class='breaks' />";
							echo "<hr class='rules' />";
							echo "Item Total: £" . number_format((float)$runningPrice/100, 2, '.', '');

							if($row['Size'] > $higestDeliverySize){
								$higestDeliverySize = $row['Size'];
							}
								
					echo "</div>";
					echo "<br />";
				return $runningPrice;
			}
		}

		
		$totalPrice = 0.0;
		
		$higestDeliverySize = 0;
		
		if (isset($_SESSION['item']))
		{
			
			if(trim(! $_SESSION['item']) === "" )
			{
				
				$itemNames = (explode(',',$_SESSION['item']));

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

				echo "<div class=\"itemsScroller\">";
					
				foreach($itemList as $item)
				{
					
					$itemTypeQuery = "SELECT * FROM products WHERE Name=?";
					$itemTypeStmt = $mysqli->stmt_init();
					$itemTypeStmt = $mysqli->prepare($itemTypeQuery);
													
					if ($itemTypeStmt){
						$itemTypeStmt->bind_param('s', $item[0]);
						$itemTypeStmt->execute();
						$itemTypeResult = get_result($itemTypeStmt);
						$itemTypeRow = array_shift($itemTypeResult);
								
						if($itemTypeRow['Offer'] != '0'){
							if(explode(']',explode('[',$itemTypeRow['Offer'])[1])[0] === 'bundle'){
								$totalPrice += displayCartItem($item, 'bundle');
							}else{
								$totalPrice += displayCartItem($item, 'offer');
							}
						}else{
							$totalPrice += displayCartItem($item, '0');
						}
					}
				}
					
				echo "</div>";
				
				
				echo "<br class='totalBreak' />";
								
				echo "<div class='cartTotalContainerFlex'>";
				echo "<fieldset class='cartTotalContainer'>";
				echo "<span class=\"itemTitle\"><a class='cartTotalContainerTitles'>Cart Total</a></span><hr><br class='breaks' />";
				
					$postageQuery = "SELECT * FROM delivery WHERE Country=?";
					$postageStmt = $mysqli->stmt_init();
					$postageStmt = $mysqli->prepare($postageQuery);
											
					if ($postageStmt){		
						$postageStmt->bind_param('s', $postageCountry);
						$postageStmt->execute();
						$postageResult = get_result($postageStmt);
						$postageRow = array_shift($postageResult);
						
						$deliverySizeAsStrings = array("Country", "Small", "Large", "Code");
						if($totalPrice < 3000){
							if(explode('-', $postageRow[$deliverySizeAsStrings[$higestDeliverySize]])[1][0] === '0'){
								$deliveryCost = 99999;
							}else{
								$deliveryCost = explode('-', $postageRow[$deliverySizeAsStrings[$higestDeliverySize]])[0] * 1.2;
							}
						}else if($totalPrice >= 3000 && $totalPrice < 7000){
							if(explode('-', $postageRow[$deliverySizeAsStrings[$higestDeliverySize]])[1][1] === '0'){
								$deliveryCost = 99999;
							}else{
								$deliveryCost = explode('-', $postageRow[$deliverySizeAsStrings[$higestDeliverySize]])[0] * 1;
							}
						}else{
							if(explode('-', $postageRow[$deliverySizeAsStrings[$higestDeliverySize]])[1][2] === '0'){
								$deliveryCost = 99999;
							}else{
								$deliveryCost = explode('-', $postageRow[$deliverySizeAsStrings[$higestDeliverySize]])[0] * 0.8;
							}
						}
				
					}else{
						$deliveryCost = 0;
					}
					
					if($deliveryCost === 99999){
						echo "Delivery to " . $postageCountry . " unavailable. Please see <a href='delivery.php'>Delivery</a> page for more information.<br /><br class='breaks' /><hr class='rules' />";
					}else{
						echo "Sub-Total: <u>£" . number_format((float)$totalPrice/100, 2, '.', '') . "</u><br />";
						
						if(isset($_POST['promo'])){
							
							$promoQuery = "SELECT * FROM offers WHERE Code LIKE ?";
							$promoStmt = $mysqli->stmt_init();
							$promoStmt = $mysqli->prepare($promoQuery);
													
							if ($promoStmt){		
								$promoStmt->bind_param('s', $_POST['promo']);
								$promoStmt->execute();
								$promoResult = get_result($promoStmt);
								if($promoRow = array_shift($promoResult)){
									if(strcmp($promoRow['Code'], $_POST['promo']) === 0){
										$reduction = ($totalPrice / 100) * explode(')', explode('(',$promoRow['Offer'])[1])[0];
										echo "Promo: <u>-£" . number_format((float)($reduction)/100, 2, '.', '') . "</u><br />";
										$totalPrice = $totalPrice - $reduction;
									}else{
										unset($_POST['promo']);
									}
								}else{
									unset($_POST['promo']);
								}
							}
						}
						
						echo "Delivery to: " . $postageCountry . " <u>£" . number_format((float)$deliveryCost/100, 2, '.', '') . "</u><br class='breaks' />";
						echo "<hr class='rules' />";
									
						echo "<br class='breaks' /><u class='totalText'>Total: £" . number_format((float)($totalPrice + $deliveryCost)/100, 2, '.', '') . "</u>";
						echo "</fieldset>";
						echo "<fieldset class='cartTotalContainer'>";
						echo "<span class=\"itemTitle\"><a class='cartTotalContainerTitles'>Continue</a></span><hr>";
						//<legend>Continue</legend>";
						echo "<br class='breaks' />";
						echo "<form action='checkoutStep1.php' method='post'>";
		
							if(isset($_POST['promo'])){
								echo "<input type='hidden' name='promo' value='".$_POST['promo']."' />";
							}
							echo "<input type='hidden' name='itemList' value='".ltrim($updatedItemList,',')."' />";
							echo "<input type='submit' value='Checkout' class='cartCheckoutButton' />";
						echo "</form>";

					}
				echo "<sub>Powered by:</sub><br />";
				echo "<img src='https://www.paypalobjects.com/webstatic/en_US/i/buttons/PP_logo_h_200x51.png' alt='PayPal' class='cartPayPal' />";
				echo "</fieldset>";
				echo "<br class='totalBreak' />";
				echo "</div>";
				
				echo "<div class='cartTotalContainerFlex'>";
					echo "<fieldset class='cartTotalContainer'>";
					echo "<span class=\"itemTitle\"><a class='cartTotalContainerTitles'>Promo Code</a></span><hr><br class='breaks' />";
					
					if(isset($_POST['promo'])){
						echo "Promo code " . $_POST['promo'] . " Applied.";
					}else{
						echo "<form action='cart.php' method='POST'>";
							echo "<input type='text' name='promo' placeholder='Code' required />";
							echo "<br />";
							echo "<input type='submit' value='Apply Code' />";
						echo "</form>";
					}
				
					echo "</fieldset>";
				echo "</div>";
				
			}
			else
			{
				echo "<fieldset class='cartItemContainer'>No items currently in cart</fieldset>";
			}
			
		}
		else
		{
			echo "<fieldset class='cartItemContainer'>No items currently in cart</fieldset>";
		}

		?>
		
	</div>

	<div class='cartRightList'>
		
		<fieldset class='cartRightContainer' style='text-align: left;'>
		<span class="itemTitle"><a style="text-decoration: none;">Delivery</a></span>
		<img src="images/deliveryIcon.png" class="cartIcons" alt="Delivery Icon" />
		<p style="margin: 0; padding: 0; line-height: 2vw;">
		Deliver to:
		
		<select id='coutrySelect' onchange="javascript:window.location.replace('cart.php?postageCountry='+this.options[this.selectedIndex].value+'')">
		<?php
		
		$countriesQuery = "SELECT Country FROM delivery";
		$countriesStmt = $mysqli->stmt_init();
		$countriesStmt = $mysqli->prepare($countriesQuery);
													
		if ($countriesStmt){
			$countriesStmt->execute();
			$countriesResult = get_result($countriesStmt);
			$countriesRow = array_shift($countriesResult);
		
			foreach($countriesResult as $country)
			{
				if($postageCountry === $country['Country']){
					echo "<option selected>".$country['Country']."</option>";
				}else{
					echo "<option>".$country['Country']."</option>";
				}
			}
		}else{
			echo "Error loading deliver details, plaese use <a href='contact.php'>Contact page</a> to resolve issue.";
		}
		echo "</select><br class='cartDeliveryBreak' /> ";
		?>
		
			See the <a href="delivery.php"> Delivery</a> policy. 
		</p>
		</fieldset><br /><br />
		
		<fieldset class='cartRightContainer' style='text-align: left;'>
		<span class="itemTitle"><a style="text-decoration: none;">Returns</a></span>
		<img src="images/returnIcon.png" class="cartIcons" alt="Returns Icon" />
		<p style="margin: 0; padding: 0;">
			See the <a href="returns.php"> Returns</a> policy. 
		</p>
		<hr class="rules" />
		</fieldset><br /><br />
		
		<fieldset class='cartRightContainer' style='text-align: left;'>
		<span class="itemTitle"><a style="text-decoration: none;">Privacy</a></span>
		<img src="images/privacyIcon.png" class="cartIcons" alt="Privacy Icon" />
		<p style="margin: 0; padding: 0;">
			See the <a href="privacy.php"> Privacy</a> policy. 
		</p>
		<hr class="rules" />
		</fieldset><br /><br />
		
		<fieldset class='cartRightContainer' style='text-align: left;'>
		<span class="itemTitle"><a style="text-decoration: none;">Terms</a></span>
		<img src="images/termsIcon.png" class="cartIcons" alt="Terms Icon" />
		<p style="margin: 0; padding: 0;">
			See the <a href="terms.php"> Terms of Use</a> page. 
		</p>
		<hr class="rules" />
		</fieldset>
		
	</div>
</div>

<?php
include "footer.php";
?>

</body>
</html>