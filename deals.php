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
echo "<title>CW Bundle deals</title></head>";

echo "<body>";

if (isset($_GET['error'])) {
    echo '<p class="error">Error Logging In!</p>';
}
?> 

<?php	
include "header.php"; 

if ($logged == true) {
	$title = "<a href='includes/logout.php?returnPage=deals.php' style='color: Black;'>Log out</a>";
	include "nav.php";
} else {
	$title = "Bundle Offers";
	include "nav.php";
}

?>

<br class="pageBreak" />

<div class="homepageImage" style='height: 10vw;'>
	<img src="images/bundleImages/bundle1_600.jpg" srcset="images/bundleImages/bundle1_600.jpg 600w, images/bundleImages/bundle1_1000.jpg 1000w, images/bundleImages/bundle1_1400.jpg 1400w" sizes="(max-width: 550px) 100vw, 50vw" alt="big Picture 1">
</div>

<div class="homepageColourBar">

	<?php
	
	if ($logged == true) {
		echo "<br /><br />";
		echo "<div class='newProductPopup'>";
			echo "<form action='newBundle.php' method='post' class='uploadForm' enctype='multipart/form-data'>";
				echo "<fieldset>";
				
				$allItems = array();
				
				$allItemsQuery = "SELECT * FROM products";
				$allItemsStmt = $mysqli->stmt_init();
				$allItemsStmt = $mysqli->prepare($allItemsQuery);
															
				if ($allItemsStmt){
					$allItemsStmt->execute();
					$allItemsResult = get_result($allItemsStmt);
					
					foreach($allItemsResult as $allItemsRow){
						if($allItemsRow['Offer'] != '0'){
							if(explode(']',explode('[',$allItemsRow['Offer'])[1])[0] !== 'bundle'){
								array_push($allItems, $allItemsRow['Name']);
							}
						}else{
							array_push($allItems, $allItemsRow['Name']);
						}
					}
				}
					
				echo "<legend style='font-size: 1.5vw;'>Add New Bundle</legend>";
					
					echo "<label for='bundleName'>Bundle Name: &nbsp;&nbsp;</label>";
					echo "<input type='text' maxlength='63' name='bundleName'> <br />";
					
					echo "<label for='itemsQuantity'>Quantity: &nbsp;&nbsp;</label>";
					echo "<select name='itemsQuantity' onchange='javascript:addbundleItem(".json_encode($allItems).",this.options[this.selectedIndex].value);'>";
						echo "<option value='0' selected>None</option>";
						echo "<option value='2'>2 Items</option>";
						echo "<option value='3'>3 Items</option>";
						echo "<option value='4'>4 Items</option>";
						echo "<option value='5'>5 Items</option>";
					echo "</select>";
					
					echo "<div id='bundleItemContainer'>";
					echo "</div>";
					
					echo "<label for='bundlePrice'>Price (p): &nbsp;&nbsp;</label>";
					echo "<input type='number' min='0.00' max='100000.00' step='0.01' name='bundlePrice'> <br />";
				
					echo "<input type='submit' value='Upload New Bundle' name='submit' class='submit'>";
				
				echo "</fieldset>";

			echo "</form>";
		echo "</div>";
	};
		
	?>

	<div class="productListContainer">

		<?php
		$anyResults = false;
		
		$dealsQuery = "SELECT * FROM products WHERE NOT Offer='0'";
		$dealsStmt = $mysqli->stmt_init();
		$dealsStmt = $mysqli->prepare($dealsQuery);
													
		if ($dealsStmt){
			$dealsStmt->execute();
			$dealsResult = get_result($dealsStmt);
		
			foreach($dealsResult as $dealRow){

				$dealType = explode(']',explode('[',$dealRow['Offer'])[1])[0];
				
				if($dealType === 'bundle'){
				
					$items = explode(',', explode('}',explode('{',$dealRow['Offer'])[1])[0]);
					$price = explode(')',explode('(',$dealRow['Offer'])[1])[0];
					
					$bundleUnavailable = false;
					
					$lowestBundleItemRemaining = 999;
					
					foreach($items as $item){
						
						$availabilityQuery = "SELECT * FROM products WHERE Name='".$item."'";
						$availabilityStmt = $mysqli->stmt_init();
						$availabilityStmt = $mysqli->prepare($availabilityQuery);
																	
						if ($availabilityStmt){
							$availabilityStmt->execute();
							$availabilityResult = get_result($availabilityStmt);
							$availabilityRow = array_shift($availabilityResult);
							
							if($availabilityRow['Remaining'] < 1){
								$bundleUnavailable = true;
							}else{
								if($availabilityRow['Remaining'] < $lowestBundleItemRemaining){
									$lowestBundleItemRemaining = $availabilityRow['Remaining'];
								}
							}
						}
					}
					
					if($bundleUnavailable === false){
						
						$anyResults = true;
							
						echo "<div class='productContainerDeal'>";
							
							echo "<a class='productContainerTextLarger'>" . $dealRow['Name'] . "</a>";
							
							echo "<div class='bundleTable'>";
									
								

								foreach($items as $item){
									
									$imagesQuery = "SELECT * FROM products WHERE Name='".$item."'";
									$imagesStmt = $mysqli->stmt_init();
									$imagesStmt = $mysqli->prepare($imagesQuery);
																				
									if ($imagesStmt){
										$imagesStmt->execute();
										$imagesResult = get_result($imagesStmt);
										$imagesRow = array_shift($imagesResult);
										$imageSrc = explode(',', $imagesRow['Src']);
											
										echo "<div class='bundleTableCell'>";
											if($imagesRow['Remaining'] < 1){
												echo "<img src='images/outOfStock.png' alt='Error' class='bundleImageThumb'>";
											}else{
												echo "<a href='productPage.php?productName=" . $imagesRow['Name'] . "&returnPage=deals.php'><img src='" . $imageSrc[0] . "' alt='Product: " . $imagesRow['Name'] . "' class='bundleImageThumb' name='anchor" . $imagesRow['Name'] . "'></a>";
											}
												
										echo "</div>";
											
									}
								}
														
								echo "</div>";
								
								echo "<hr>";

								$bundleTotalPrice = 0;
								echo "<table>";
									foreach($items as $item){
										
										$detailsQuery = "SELECT * FROM products WHERE Name='".$item."'";
										$detailsStmt = $mysqli->stmt_init();
										$detailsStmt = $mysqli->prepare($detailsQuery);
																					
										if ($detailsStmt){
											$detailsStmt->execute();
											$detailsResult = get_result($detailsStmt);
											$detailsRow = array_shift($detailsResult);

											echo "<tr><td style='text-align: right;'>";
												echo $item . " : ";
												echo "</td><td>";
												echo "£" . number_format((float)$detailsRow['Price']/100, 2, '.', '');
												$bundleTotalPrice += $detailsRow['Price'];
											echo "</td></tr>";
										}
									}
								echo "<td class='dealsTotal' style='text-align: right;'>Total : </td><td class='dealsTotal'><u><strike>£" . number_format((float)$bundleTotalPrice/100, 2, '.', '') . "</strike></u></td>";
								echo "</table>";
								
								echo "<hr>";
								
								echo "<br />";
								echo "Get <a class='dealsTextLarge'>all</a> of this for <a class='dealsTextLarge'><u>£" . number_format((float)$price/100, 2, '.', '') . "</u></a>";
								
								
								echo "<br />";
								echo "<br />";
								
								echo "<div class='buyNowFlexContainer' style='justify-content: center;'>";
								
								$quantitySelected = 0;
								
								if(isset($_SESSION['item'])){
									$itemNames = (explode(',',$_SESSION['item']));
									$itemList = [];
									foreach ($itemNames as $item){
										if($item === $dealRow['Name']){
											$temp = $quantitySelected + 1;
											$quantitySelected = $temp;
										}
									}
								}
										
								if(isset($_SESSION['item'])){
									if($quantitySelected > 0){
										
										echo "<form action='cart.php'>";
											echo "<input type='submit' value='".$quantitySelected." in Cart' class='buyNowButton' />";
										echo "</form>";
										echo "<div class='dealsPlusMinusButtonsContainer'>";
											if($quantitySelected < $lowestBundleItemRemaining){
												echo "<input type='submit' alt='Plus Button' value='&#8679' class='dealsPlusMinusButtons' onclick=\"addToCart('".$dealRow['Name']."','".$quantitySelected."','".$lowestBundleItemRemaining."')\" />";
											}else{
												echo "<input type='submit' disabled alt='Plus Button' value=' &#8679 ' style='text-decoration: line-through; background: #979797; color: #595959;' class='dealsPlusMinusButtons' onclick=\"addToCart('".$dealRow['Name']."','".$quantitySelected."','".$lowestBundleItemRemaining."')\" />";
											}
											
											if($quantitySelected > 0){
												echo "<input type='submit' alt='Minus Button' value='&#8681' class='dealsPlusMinusButtons' onclick=\"removeFromCart('".$dealRow['Name']."')\" />";
											}else{
												echo "<input type='submit' disabled alt='Minus Button' value=' &#8681 ' style='text-decoration: line-through; background: #979797; color: #595959;' class='dealsPlusMinusButtons' onclick=\"removeFromCart('".$dealRow['Name']."','".$quantitySelected."','".$lowestBundleItemRemaining."')\" />";
											}
										echo "</div>";

									}else{
										echo "&nbsp;&nbsp;&nbsp;&nbsp;<input type=\"button\" value=\"Add to Cart\" class='productButton' onclick=\"addToCart('".$dealRow['Name']."','0','".$lowestBundleItemRemaining."')\" />";
									}
									
								}else{
									echo "&nbsp;&nbsp;&nbsp;&nbsp;<input type=\"button\" value=\"Add to Cart\" class='productButton' onclick=\"addToCart('".$dealRow['Name']."','0','".$lowestBundleItemRemaining."')\" />";
								}
								
								echo "</div>";
								
						if ($logged == true) {
							echo "<br />";
							echo "<br />";
							echo "<input type='button' value='Admin Remove'style='background: red;' class='dealRemoveButton' onClick='removeItem(\"admin\", \"".$dealRow['Name']."\"); location.reload();' />";
						}
								
						echo "</div>";
							
					}

				}
			}

			$dealsStmt->close();
		}
		
		if($anyResults === false){
			echo "Sorry, there don't seem to be any bundles available right now. Check back another time for new offers.";
		}
		?>

	</div>
	
</div>

<br />





<?php
include "footer.php";
?>

</body>
</html>
