<?php
include_once 'db_connect.php';
include_once 'functions.php';

if(isset($_POST['orderID'])){
	$orderID = $_POST['orderID'];
}else{
	header("Location: ../index.php");
}

$invoiceQuery = "SELECT * FROM orders WHERE ID=?";
$invoiceStmt = $mysqli->stmt_init();
$invoiceStmt = $mysqli->prepare($invoiceQuery);
			
if ($invoiceStmt){
	$invoiceStmt->bind_param('s', $orderID);
	$invoiceStmt->execute();
	$invoiceResult = get_result($invoiceStmt);
		
	foreach($invoiceResult as $invoiceRow){
		$name = explode(',',$invoiceRow['Name']);
		$nameString = "";
		foreach($name as $nameLine){
			$nameString .= $nameLine . " ";
		}
		$address = explode(',',$invoiceRow['Address']);
		$addressString = "";
		foreach($address as $addressLine){
			$addressString .= $addressLine . "<br />";
		}
		$addressString = substr($addressString, 0, -6);
		$phone = $invoiceRow['Phone'];
		$dateInvoiced = $invoiceRow['DateTime'];
		$totalPrice = intval($invoiceRow['SubTotal']) + intval($invoiceRow['Delivery']);
		$itemText = $invoiceRow['ItemNames'];
		$delivery = $invoiceRow['Delivery'];
	}
}

?>

<!doctype html>
<html>
	<head>
		<meta charset="utf-8">
		<title>Invoice</title>
		<link rel="stylesheet" href="../styles/invoiceStyle.css">
		<link rel="license" href="http://www.opensource.org/licenses/mit-license/">
		<script src="../js/invoiceJavascript.js"></script>
	</head>
	<body>
		<header>
			<h1>Invoice</h1>
			<address contenteditable>
				<p>Cornish Woodware</p>
				<br />
				<p>Callum Mellows</p>
				<p>4 Beacon View<br />Lewannick<br />Launceston<br />Cornwall</p>
				<p>07805 135452</p>
			</address>
			<span><img alt="" src="logo.png"><input type="file" accept="image/*"></span>
		</header>
		<article>
			<h1>Recipient</h1>
			<address contenteditable>
			<p><?php echo $nameString ?></p>
			<br />
			<p><?php echo $addressString ?></p>
			<p><?php echo $phone ?></p>
				
			</address>
			<table class="meta">
				<tr>
					<th><span contenteditable>Invoice #</span></th>
					<td><span contenteditable><?php echo $orderID ?></span></td>
				</tr>
				<tr>
					<th><span contenteditable>Date Ordered</span></th>
					<td><span contenteditable><?php echo date('d M y', strtotime($dateInvoiced)) ?></span></td>
				</tr>
				<tr>
					<th><span contenteditable>Date Shipped</span></th>
					<td><span contenteditable><?php date_default_timezone_set('Europe/London'); echo date("d M y", time()); ?></span></td>
				</tr>
				<tr>
					<th><span contenteditable>Amount Due</span></th>
					<td><span id="prefix" contenteditable>£</span><span><?php echo $totalPrice ?></span></td>
				</tr>
			</table>
			<table class="inventory">
				<thead>
					<tr>
						<th><span contenteditable>Item</span></th>
						<th><span contenteditable>Type</span></th>
						<th><span contenteditable>Rate</span></th>
						<th><span contenteditable>Quantity</span></th>
						<th><span contenteditable>Price</span></th>
					</tr>
				</thead>
				<tbody>
					<?php
					foreach(explode(',',$itemText) as $item){
						
						echo "<tr>";
						$itemArray = explode('|',$item);
						echo "<td><a class='cut'>-</a><span contenteditable>".$itemArray[0]."</span></td>";
						echo "<td><span contenteditable>Product</span></td>";
						echo "<td><span data-prefix>£</span><span contenteditable>".number_format((float)$itemArray[2]/100, 2, '.', '')."</span></td>";
						echo "<td><span contenteditable>".$itemArray[1]."</span></td>";
						echo "<td><span data-prefix>£</span><span>600.00</span></td>";

						echo "</tr>";
					}
					?>
					
					<tr>
						<td><a class='cut'>-</a><span contenteditable>Delivery</span></td>
						<td><span contenteditable>Service</span></td>
						<td><span data-prefix>£</span><span contenteditable><?php  echo number_format((float)$delivery/100, 2, '.', '') ?></span></td>
						<td><span contenteditable>1</span></td>
						<td><span data-prefix>£</span><span>600.00</span></td>
					</tr>
					
				</tbody>
			</table>
			<a class="add">+</a>
			<table class="balance">
				<tr>
					<th><span contenteditable>Total</span></th>
					<td><span data-prefix>£</span><span>600.00</span></td>
				</tr>
				<tr>
					<th><span contenteditable>Amount Paid</span></th>
					<td><span data-prefix>£</span><span contenteditable><?php  echo number_format((float)$totalPrice/100, 2, '.', '') ?></span></td>
				</tr>
				<tr>
					<th><span contenteditable>Balance Due</span></th>
					<td><span data-prefix>£</span><span>600.00</span></td>
				</tr>
			</table>
		</article>
		<aside>
			<h1><span contenteditable>Thank you for your order</span></h1>
			<div contenteditable>
				<p style="text-align: center; font-style: italic;">Cornish Woodware</p>
			</div>
		</aside>
	</body>
</html>