<?php
include_once 'includes/db_connect.php';
include_once 'includes/functions.php';
 
sec_session_start();
?>

<!DOCTYPE html>
<html lang="en">

<?php

include "head.html";

?>
<title>HiC Timeout</title></head>
<body>

<?php

include "nav.php";

$redirecting = false;
if(isset($_GET['home'])){
	if($_GET['home'] == 'true'){
		$redirecting = true;
	}
}

if(isset($_GET['time'])){
	if(isset($_GET['itemList'])){
		if(isset($_SESSION['reservation'])){
		
			$itemNames = (explode(',',$_GET['itemList']));
			
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
				
				$itemQuery = "SELECT * FROM products WHERE Name=?";
				$itemStmt = $mysqli->stmt_init();
				$itemStmt = $mysqli->prepare($itemQuery);
													
				if ($itemStmt){		
					$itemStmt->bind_param('s', $item[0]);
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
										if(!explode('|',$oldText)[1] == $_GET['time']){
											if($newReservedText == '0'){
												$newReservedText = $oldText;
											}else{
												$newReservedText = $newReservedText . ',' . $oldText;
											}
										}
									}
									if ($result2 = $mysqli->query("UPDATE products SET Reserved='".$newReservedText."' WHERE Name='".$bundleItems."'")) {
										unset($_SESSION['item']);
										unset($_SESSION['reservation']);
									}
									
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
								if(!explode('|',$oldText)[1] == $_GET['time']){
									if($newReservedText == '0'){
										$newReservedText = $oldText;
									}else{
										$newReservedText = $newReservedText . ',' . $oldText;
									}
								}
							}
							if ($result3 = $mysqli->query("UPDATE products SET Reserved='".$newReservedText."' WHERE Name='".$item[0]."'")) {
								unset($_SESSION['item']);
								unset($_SESSION['reservation']);
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
							if(!explode('|',$oldText)[1] == $_GET['time']){
								if($newReservedText == '0'){
									$newReservedText = $oldText;
								}else{
									$newReservedText = $newReservedText . ',' . $oldText;
								}
							}
						}
						if ($result2 = $mysqli->query("UPDATE products SET Reserved='".$newReservedText."' WHERE Name='".$item[0]."'")) {
							unset($_SESSION['item']);
							unset($_SESSION['reservation']);
						}
					}
				}
			}
		}
	}
}
?>

<br class="pageBreak" />

<?php

if($redirecting == true){

	echo "<div style='line-height: normal;'>";
	echo "Just closing up your checkout, won't be a minute";
	echo "</div>";
	echo "<script>window.location='index.php';</script>";

}else{

	echo "<div class='pageTitleImage'>";
		echo "<img src='images/ppfail.jpg' alt='big Picture 1' />";
		echo "<a>Timeout</a>";
	echo "</div>";

	echo "<div class='homepageColourBar'>";
	echo "<br />";

		echo "So sorry, it seems you have exeeded the time allowed to checkout.<br />";
		echo "If you wish to continue, you will need to begin the checkout again.<br /><br />";
		
		echo "if you feel the time limit wasn't enough to checkout, please let me know at the <a href='contact.php'>Contact page</a> so that I can change it.<br /><br />";
		
		echo "Or please <a href='cart.php'>Click here</a> to return to your cart.";

	echo "</div>";

	}
?>

</body>
</html>