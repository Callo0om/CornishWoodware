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
if(isset($_GET["productName"])){
	echo "<title>HiC ".$_GET["productName"]."</title></head>";
	
	$trimmedProductName = '';
	$tooltip = false;
		
	if(strlen($_GET["productName"]) > 21){
		$trimmedProductName = substr($_GET["productName"], 0, 18) . "...";
		$tooltip = true;
	}else{
		$trimmedProductName = substr($_GET["productName"], 0, 21);
	}
	
	if($tooltip === true){
		$title = "<a class='tooltip'>" . $trimmedProductName . "<span class='tooltiptext'>".$_GET["productName"]."</span></a>";
	}else{
		$title = $trimmedProductName;
	}
	
}else{
	echo "<title>HiC Product page</title></head>";
	$title = "Product Details";
}

echo "<body onresize='sizeScale()' onscroll='sizeScale()'>";
echo "<div class='choppingBoardImgDiv' id='choppingBoardImgDiv' style='display: none;' />";
	echo "<img src='images/choppingBoardImg/TL.png' class='TL' />";
	echo "<img src='images/choppingBoardImg/TR.png' class='TR' />";
	echo "<img src='images/choppingBoardImg/BL.png' class='BL' />";
	echo "<img src='images/choppingBoardImg/BR.png' class='BR' />";
	
	echo "<img src='images/choppingBoardImg/L.png' class='L' />";
	echo "<img src='images/choppingBoardImg/T.png' class='T' />";
	echo "<img src='images/choppingBoardImg/R.png' class='R' />";
	echo "<img src='images/choppingBoardImg/B.png' class='B' />";
	
	echo "<img src='images/choppingBoardImg/back.png' class='back' />";
		
	echo "<div class='choppingBoardImgSlotDiv' id='choppingBoardImgSlotDiv' style='display: none;'>";
		echo "<img src='images/choppingBoardImg/SlotT.png' class='SlotT' />";
		echo "<img src='images/choppingBoardImg/SlotM.png' class='SlotM' />";
		echo "<img src='images/choppingBoardImg/SlotB.png' class='SlotB' />";
	echo "</div>";
echo "</div>";
echo "<img src='../images/bread.png' class='bread' id='bread' style='display: none;' />";
include "header.php";


include "nav.php";

if (isset($_GET['postageCountry'])){
	$_SESSION['postageCountry'] = $_GET['postageCountry'];
}

if (isset($_SESSION['postageCountry'])){
	$postageCountry = $_SESSION['postageCountry'];
}else{
	$postageCountry = 'UK-Mainland';
}

if(isset($_GET["buyNowQuantity"])){
	$buyNowQuantity = $_GET["buyNowQuantity"];
}else{
	$buyNowQuantity = 1;
}
	
	echo "<div class='productPageContainer'>";

	echo "<div class='productTitle'>";
	
	echo "<br class='pageBreak' />";
	if(isset($_GET["productName"])){
		
		if(isset($_GET["returnPage"])){
			echo "<a href='".$_GET["returnPage"]."?anchor=anchor".str_replace(' ', '%20', $_GET["productName"])."' class='button'> Return to catalogue</a>&emsp;";
		}else{
			echo "<a href='catalogue.php?anchor=anchor".str_replace(' ', '%20', $_GET["productName"])."' class='button'> Return to catalogue</a>&emsp;";
		}
	}else{
		if(isset($_GET["returnPage"])){
			echo "<a href='".$_GET["returnPage"]."' class='button'> Return to catalogue</a>&emsp;";
		}else{
			echo "<a href='catalogue.php' class='button'> Return to catalogue</a>&emsp;";
		}
	}

	echo "</div>";
	
	echo "<div class='productPageContentLeft'>";
	
	$productQuery = "SELECT * FROM products WHERE Name=?";
	$productStmt = $mysqli->stmt_init();
	$productStmt = $mysqli->prepare($productQuery);
													
	if ($productStmt){		
		$productStmt->bind_param('s', $_GET["productName"]);
		$productStmt->execute();
		$productResult = get_result($productStmt);
		$productRow = array_shift($productResult);

		$imageSrcs = explode(',', $productRow['Src']);
		$imageSrcs = array_splice($imageSrcs, 1);	
	}

	$imgSmall = str_replace(' ', '%20', dirname($imageSrcs[0]) . "/_600/" . basename($imageSrcs[0]));
	$imgMed = str_replace(' ', '%20', dirname($imageSrcs[0]) . "/_1000/" . basename($imageSrcs[0]));
	$imgBig = str_replace(' ', '%20', dirname($imageSrcs[0]) . "/_1400/" . basename($imageSrcs[0]));
	
	echo "<div class='productMainImageContainer' id='productMainImageContainer'>";
	echo "<div id='productMainImageContainerImages'>";
		echo "<img src=\"".$imgSmall."\" srcset='".$imgSmall." 600w,".$imgMed." 1000w,".$imgBig." 1400w' sizes='(max-width: 550px) 100vw, 50vw' alt='Main Image' class='productMainImage' id='productMainImage' />";
	echo "</div>";
	echo "<img src='images/enlarge.png' alt='enlarge' class='enlarge' onClick=\"enlargeImageOpen()\">";
	
	echo "<hr>";
	
	echo "<div class=\"productPageThumbnails\">";
	foreach ($imageSrcs as &$Src) {
		echo "<img src='". str_replace(' ', '%20', dirname($Src)) . "/_600/" . basename($Src) ."' alt='Product thumbnail' class='productThumbImage' onClick=\"setProductPicture('".$Src."')\">";
	}
	echo "</div>";
	
	echo "<div class='productPriceTag'>";
	$actualPrice = $productRow['Price'];
	if($productRow['Offer'] === '0'){
		echo "<a class='productPriceTagXLarge'>£" . number_format((float)$productRow['Price']/100, 2, '.', '') . "</a>";
	}else{
		$dealType = explode(']',explode('[',$productRow['Offer'])[1])[0];
		$offerPrice = explode(')',explode('(',$productRow['Offer'])[1])[0];
		if($dealType === 'reduceTo'){
			$actualPrice = $offerPrice;
			echo "<strike><a class='productPriceTagSmall'>£" . number_format((float)$productRow['Price']/100, 2, '.', '') . "<br /></a></strike><a class='productPriceTagLarge'>£" . number_format((float)$offerPrice/100, 2, '.', '') . "</a>";
		}else if($dealType === 'reduceBy'){
			$actualPrice = ($productRow['Price'] / 100) * (100 - $offerPrice);
			echo "<strike><a class='productPriceTagSmall'>£" . number_format((float)$productRow['Price']/100, 2, '.', '') . "<br /></a></strike><a class='productPriceTagLarge'>£" . number_format((float)($productRow['Price'] / 100) * (100 - $offerPrice)/100, 2, '.', '') . "</a>";
		}else if($dealType === '2for'){
			$actualPrice = $productRow['Price'];
			echo "<a class='productPriceTagSmall'><strike>£" . number_format((float)$productRow['Price']/100, 2, '.', '') . "</strike> <span class='productPriceTagMed'>2</span> For<br /></a><a class='productPriceTagLarge'>£" . number_format((float)$offerPrice / 100, 2, '.', '') . "</a>";
		}
	}
	echo "</div>";
	
	echo "</div>";
	
	
	
	
	echo "</div><div class='productPageContentRight'>";
	
	echo "<div class=\"productRightContent\">";
	
	$reserved = false;
	if($productRow['Reserved'] != '0'){
		$updateReservation = false;
		$newReservedText = '0';
		if(count(explode(',',$productRow['Reserved'])) > 0){
			$oldReservedText = explode(',',$productRow['Reserved']);
		}else{
			$oldReservedText = $productRow['Reserved'];
		}
		$date = new DateTime();
		foreach($oldReservedText as $oldText){
			if((floor((strtotime(date('Y-m-d H:i:s', explode('|',$oldText)[1])) - strtotime(date('Y-m-d H:i:s', $date->getTimestamp()))) / 60) * -1) >= 20){
				$updateReservation = true;
			}else{
				if($newReservedText == '0'){
					$newReservedText = $oldText;
				}else{
					$newReservedText = $newReservedText . ',' . $oldText;
				}
			}
		}
		
		$totalReserved = 0;
		foreach(explode(',',$newReservedText) as $reserved){
			$totalReserved = $totalReserved + intval(explode('|',$reserved)[0]);
		}
		
		if($updateReservation == true){
			
			$reservationQuery = "UPDATE products SET Reserved=? WHERE Name=?";
			$reservationStmt = $mysqli->stmt_init();
			$reservationStmt = $mysqli->prepare($reservationQuery);
													
			if ($reservationStmt){		
				$reservationStmt->bind_param('ss', $newReservedText, $productRow['Name']);
				$reservationStmt->execute();
			}
		}
	}else{
		$totalReserved = 0;
	}
	
	$available = $productRow['Remaining'] - $totalReserved;
	
	//quantity
	echo "<fieldset><legend>Availability</legend>";

	if($available < 1){
		echo "<hr>";
		echo "So sorry, somebody is currently buying this item, if they complete the purchase it will be removed or if not then it will become available again.";
		$reserved = true;
		echo "<hr>";
	}else{
		if($productRow['Quantity'] == 1){
		echo "This is a one-off item, when it's gone it's gone forever.";
	}else{
		if($available == 2){
			echo $available . " Left. Once they're gone they're gone forever.";
		}else if($available == 1){
			echo "The last one, when it's gone it's gone forever.";
		}else{
			echo $available . " Available. Once they're gone they're gone.";
		}
	}
	}
	echo "</fieldset><br />";
	
	if($reserved !== true){
		
		
		
		//buy now
		echo "<fieldset><legend>Buy Now</legend>";
		
		echo "<div class='buyNowFlexContainer'>";

			if($available == 1){
				echo "<form action='checkoutStep1.php' method='post'>";
					echo "<input type='hidden' name='itemList' value='".$productRow['Name']."' />";
					echo "<input type='submit' value='Buy me Now!' />";
				echo "</form>";
			}else{
				//echo "<input type=\"text\" readonly class='dealQuantityBox' value=\"".$buyNowQuantity."\" \" />";

				$buyNowString = '';
				for($i = 0; $i < $buyNowQuantity; $i++){
					$buyNowString .= $productRow['Name'] . ',';
				}
				$buyNowString = rtrim($buyNowString,",");

				echo "<form action='checkoutStep1.php' method='post'>";
					echo "<input type='hidden' name='itemList' value='".$buyNowString."' />";
					echo "<input type='submit' value='Buy ".$buyNowQuantity." Now' class='buyNowButton' />";
				echo "</form>";
				
				echo "<div class='dealsPlusMinusButtonsContainer'>";
					if($buyNowQuantity < $available){
						echo "<form action='productPage.php' method='GET'>";
							echo "<input type='hidden' name='productName' value='".$productRow['Name']."' />";
							echo "<input type='hidden' name='returnPage' value='".$_GET["returnPage"]."' />";
							echo "<input type='hidden' name='buyNowQuantity' value='".($buyNowQuantity + 1)."' />";
							echo "<input type='submit' value='&#8679;' class='dealsPlusMinusButtons' />";
						echo "</form>";
					}else{
						echo "<input type='submit' alt='Plus Button' disabled value=' &#8679; ' style='text-decoration: line-through; background: #979797; color: #595959;' class='dealsPlusMinusButtons' />";
					}
					
					if($buyNowQuantity > 1){
						echo "<form action='productPage.php' method='GET'>";
							echo "<input type='hidden' name='productName' value='".$productRow['Name']."' />";
							echo "<input type='hidden' name='returnPage' value='".$_GET["returnPage"]."' />";
							echo "<input type='hidden' name='buyNowQuantity' value='".($buyNowQuantity - 1)."' />";
							echo "<input type='submit' value='&#8681;' class='dealsPlusMinusButtons' />";
						echo "</form>";
					}else{
						echo "<input type='submit' alt='Minus Button' disabled value=' &#8681; ' style='text-decoration: line-through; background: #979797; color: #595959;' class='dealsPlusMinusButtons' />";
					}
				echo "</div>";
				
			}
			
		echo "</div>";
		
		echo "</fieldset>";
		echo "<br />";
		echo "<fieldset><legend>Add to <a href='cart.php'>Cart</a></legend>";

		echo "<div class='buyNowFlexContainer'>";
		
			$quantitySelected = 0;
			if (isset($_SESSION['item'])){
				$quantitySelected = substr_count($_SESSION['item'], $productRow['Name']);
			}
					echo "<form action='cart.php'>";
						echo "<input type='submit' value='".$quantitySelected." in Cart' class='buyNowButton' />";
					echo "</form>";

					echo "<div class='dealsPlusMinusButtonsContainer'>";
						if($quantitySelected < $available){
							echo "<input type='submit' value='&#8679;' class='dealsPlusMinusButtons' onclick=\"addToCart('".$productRow['Name']."','".$quantitySelected."','".$available."')\" />";
						}else{
							echo "<input type='submit' disabled value=' &#8679; ' style='text-decoration: line-through; background: #979797; color: #595959;' class='dealsPlusMinusButtons' />";
						}
						
						if($quantitySelected > 0){
							echo "<input type='submit' value='&#8681;' class='dealsPlusMinusButtons' onclick=\"removeFromCart('".$productRow['Name']."')\" />";
						}else{
							echo "<input type='submit' disabled value=' &#8681; ' style='text-decoration: line-through; background: #979797; color: #595959;' class='dealsPlusMinusButtons' onclick=\"removeFromCart('".$productRow['Name']."','".$quantitySelected."','".$available."')\" />";
						}
					echo "</div>";
			
		
		echo "</div>";
		
		echo "</fieldset><br />";
		
	}
	//description
	echo "<fieldset><legend>Description</legend>".$productRow['Description']."</fieldset><br />";
	
	//size
	echo "<fieldset><legend>Size<a class='popUpStar' onclick='popUpText(\"size\")'>&nbsp;*&nbsp;</a></legend>";
	$sizeX = explode('|', $productRow['ItemSize'])[0];
	$sizeY = explode('|', $productRow['ItemSize'])[1];
		echo "<div id='sizePopUpDiv' class='PopUpDiv'></div>";
		echo "<img src='images/choppingBoardScale.png' class='choppingBoardScale' id='choppingBoardScale' style='display: none;' />";
		
		
		?>
		<script>
			sizeScaleInit(<?php echo $sizeX; ?>, <?php echo $sizeY; ?>, "<?php echo $productRow['Type']; ?>");
		</script>
		<?php
		
		if($sizeX === '0' || $sizeY === '0'){
			$backupText = "Very Small";
		}else{
			$backupText = $sizeX . "cm x " . $sizeY . "cm";
		}
		
		echo "<div id='sizeTextBackup'>" . $backupText . "</div>";
		echo "<br /><br />";
	echo "</fieldset><br />";
	
	//materials
	echo "<table><tr style=\"vertical-align: top;\"><td>";
	echo "<fieldset><legend>Materials<a class='popUpStar' onclick='popUpText(\"materials\")'>&nbsp;*&nbsp;</a></legend>";
	echo "<div id='materialsPopUpDiv' class='PopUpDiv'></div>";
	$materials = explode(',', $productRow['Materials']);
	foreach($materials as $mat){
		echo "&#8226;" . $mat . "<br />";	
	}
	echo "</fieldset>";
	echo "</td><td>";
	//finishes
	echo "<fieldset><legend>Finishes</legend>";
	$finish = explode(',', $productRow['Finish']);
	foreach($finish as $fin){
		
		if (strpos($fin, 'allergen') !== false) {
			echo "&#8226;" . explode('[', $fin)[0] . "<sup style=\"text-decoration: underline; cursor: pointer;\" onclick='popUpText(\"".explode('[', $fin)[0]."\")'>[Allergen]</sup> <br />";
		}else{
			echo "&#8226;" . $fin . "<br />";
		}
		
	}
	echo "</fieldset>";
	echo "</td></tr></table><br />";

	echo "<hr><br />";
		
	echo "<div class='paypalImageDiv'>";
		echo "<sub>Powered by:</sub><br />";
		echo "<img src='https://www.paypalobjects.com/webstatic/en_US/i/buttons/PP_logo_h_200x51.png' alt='PayPal' />";
	echo "</div>";
		
	
	
	echo "</div>";
?>
	
</div></div>

<div class="enlargeImage" id="enlargeImage" onclick="enlargeImageClose()">
	<img src="<?php echo $imgSmall ?>" srcset="<?php echo $imgSmall." 600w,".$imgMed." 1000w,".$imgBig." 1400w" ?>" sizes="(max-width: 550px) 100vw, 50vw" id="enlargeImageImg" class="enlargeImageImg" alt="Enlarged Image">
	<p> Click anywhere to return </p>
</div>