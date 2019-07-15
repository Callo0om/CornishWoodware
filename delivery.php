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
echo "<title>CW Delivery Policy</title></head>";
?>

<body>

<?php
if (isset($_GET['itemSize'])) {
    if($_GET['itemSize'] === "small"){
		$size = 1;
	}else{
		$size = 2;
	}
}
else
{
	$size = 1;
}
?>

<?php
include "header.php";
include "nav.php";
?>

<br class="pageBreak" />

<div class="pageContent" id="deliveryContent" style="padding: 0.5vw;">

<h1 style='text-align: center;'>Delivery Policy</h1>

	<form name="sizeChoice">

	<div class="topText">
		Item Size
	
		<input type="radio" id="small" name="choice" value="Small" <?php if($size === 1){echo "checked";} ?> onchange="javascript:window.location.replace('delivery.php?itemSize=small')">
		<label for="small">Small</label>
		<input type="radio" id="large" name="choice" value="Large" <?php if($size === 2){echo "checked";} ?> onchange="javascript:window.location.replace('delivery.php?itemSize=large')">
		<label for="large">Large</label>
	
			<table>

			<tr>
				<td>
				</td>
				<td>
					<b>Orders under £30:</b>
				</td>
				<td>
					<b>Orders £30 - £70:</b>
				</td>
				<td>
					<b>Orders over £70:</b>
				</td>
			</tr>

		<?php

			$deliveriesQuery = "SELECT * FROM delivery";
			$deliveriesStmt = $mysqli->stmt_init();
			$deliveriesStmt = $mysqli->prepare($deliveriesQuery);
														
			if ($deliveriesStmt){
				$deliveriesStmt->execute();
				$deliveriesResult = get_result($deliveriesStmt);
				
				if($size == 1){
					$sizeString = "Small";
				}else{
					$sizeString = "Large";
				}
			
				foreach($deliveriesResult as $deliveryCountry){
					echo "<tr>";
						echo "<td>";
							echo "<b>".$deliveryCountry['Country']."</b>";
						echo "</td>";
						
						echo "<td>";
							if(explode('-',$deliveryCountry[$sizeString])[1][0] === '0')
							{
								echo "Unavailable";
							}
							else
							{
								echo "£" . number_format(((float)explode('-',$deliveryCountry[$sizeString])[0]/100) * 1.2, 2, '.', '');
							}
						echo "</td>";
						
						echo "<td>";
							if(explode('-',$deliveryCountry[$sizeString])[1][1] === '0')
							{
								echo "Unavailable";
							}
							else
							{
								echo "£" . number_format(((float)explode('-',$deliveryCountry[$sizeString])[0]/100), 2, '.', '');
							}
						echo "</td>";
						
						echo "<td>";
							if(explode('-',$deliveryCountry[$sizeString])[1][2] === '0')
							{
								echo "Unavailable";
							}
							else
							{
								echo "£" . number_format(((float)explode('-',$deliveryCountry[$sizeString])[0]/100) * 0.8, 2, '.', '');
							}
						echo "</td>";
					echo "</tr>";	
				}
			}
			
			
		?>
			
		</table>
	
	</div>
	
		<div class="topText" id="deliveryText">

<h2>1. General Information</h2>

<p>All orders are subject to product availability. If an item is not in stock, or if there is a problem with the item, at the time you place your order, we will notify you and refund you the total amount of your order, using the original method of payment. </p>

<h2>2. Delivery Location</h2>

<p>Items offered on our website are available for delivery to addresses detailed above. [We also accept orders from international customers who are shipping to addresses detailed above only.] Any shipments outside of those above may not be available at this time, you will be prompted at checkout to contact us for a custom delivery quote.</p>

 

<h2>3. Delivery Time</h2>

<p>An estimated delivery time will be provided to you once your order is placed. Delivery times are estimates and commence from the date of shipping, rather than the date of order. Delivery times are to be used as a guide only and are subject to the acceptance and approval of your order.</p>

<p>Unless there are exceptional circumstances, we make every effort to fulfill your order within 5 business days of the date of your order. Business day mean Monday to Friday, except holidays.</p>

<p>Please note we do not ship on weekends.</p>

 

<p>Date of delivery may vary due to carrier shipping practices, delivery location, method of delivery, and the items ordered. Products may also be delivered in separate shipments.</p>

 

<h2>4. Delivery Instructions</h2>

<p>You can provide special delivery instructions on the check-out page of our website.</p>

 

<h2>5. Shipping Costs</h2>

<p>Shipping costs are based on the size of your order and the location. To find out how much your order will cost, simple add the items you would like to purchase to your cart, and proceed to the checkout page. Once at the checkout screen, shipping charges will be displayed.</p>

<p>Additional shipping charges may apply to remote areas or for large or heavy items. You will be advised of any charges on the checkout page.</p>

 

<p>Sales tax may be charged according to the province or territory to which the item is shipped.</p>

 

<h2>6. Damaged Items in Transport </h2>

<p>If there is any damage to the packaging on delivery, contact me immediately using the <a href="contact.php">Contact page</a>.</p>

 

<h2>7. Questions</h2>

<p>If you have any questions about the delivery and shipment or your order, please contact me on the <a href="contact.php">Contact page</a>.</p>
	</div>
		
</form>



</div>

<?php
include "footer.php";
?>

</body>

</html>