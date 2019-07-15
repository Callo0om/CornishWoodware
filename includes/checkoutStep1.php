<!DOCTYPE html>
<html lang="en">

<?php
include "head.html";
echo "<title>CW Checkout</title></head>";

echo "<body>";
echo "NKNNK";
//include "header2.php";

include_once 'includes/db_connect.php';
include_once 'includes/functions.php';
 
sec_session_start();

if(isset($_POST['itemList']) || isset($_GET['itemList'])){
	
	if(!isset($_SESSION['reservation'])){
		
		if(isset($_POST['itemList'])){
			$itemNames = (explode(',',$_POST['itemList']));
		}else{
			$itemNames = (explode(',',$_GET['itemList']));
		}
						
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
				if ($result = $mysqli->query("SELECT * FROM products WHERE Name='".$item[0]."'")) {
					$row = mysqli_fetch_array($result, MYSQLI_NUM);
					
					$notEnoughItemsLeft = false;
					if($row[12] === '0'){
						$totalReserved = 0;
						foreach(explode(',',$row[13]) as $reserved){
							$totalReserved = $totalReserved + intval(explode('|',$reserved)[0]);
						}
						if($item[1] > ($row[8] - $totalReserved)){
							$notEnoughItemsLeft = true;
						}
					}else{
						if(explode(']',explode('[',$row[12])[1])[0] === 'bundle'){
							
							$bundleContent = explode('}',explode('{',$row[12])[1])[0];
							foreach(explode(',',$bundleContent) as $bundleItem){
								if ($bundleResult = $mysqli->query("SELECT * FROM products WHERE Name='".$bundleItem."'")) {
									$bundleRow = mysqli_fetch_array($bundleResult, MYSQLI_NUM);
									
									$totalReserved = 0;
									foreach(explode(',',$bundleRow[13]) as $reserved){
										$totalReserved = $totalReserved + intval(explode('|',$reserved)[0]);
									}
									if($item[1] > ($bundleRow[8] - $totalReserved)){
										$notEnoughItemsLeft = true;
									}
								}
							}
							
						}else{
							$totalReserved = 0;
							foreach(explode(',',$row[13]) as $reserved){
								$totalReserved = $totalReserved + intval(explode('|',$reserved)[0]);
							}
							if($item[1] > ($row[8] - $totalReserved)){
								$notEnoughItemsLeft = true;
							}
						}
					}
				}
			}
			
			if($notEnoughItemsLeft == true){
				header( "refresh:3; url=cart.php" ); 
				echo "<p>Sorry, there has been an error.</p>";
				echo "<p>Redirecting to your cart in 3 seconds.</p>";
			}
			
			foreach($itemList as $item){
				if ($result = $mysqli->query("SELECT * FROM products WHERE Name='".$item[0]."'")) {
					$row = mysqli_fetch_array($result, MYSQLI_NUM);
					$reservedText = $row[13];
					$date = new DateTime();
					
					if($reservedText == '0'){
						$reservedText = $item[1] . "|" . $date->getTimestamp();
					}else{
						$reservedText = $reservedText . "," . $item[1] . "|" . $date->getTimestamp();
					}
					
					if ($result2 = $mysqli->query("UPDATE products SET Reserved='".$reservedText."' WHERE Name='".$item[0]."'")) {
						$_SESSION['reservation'] = $date->getTimestamp();
					}
				}
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
	echo "<a href='timeout.php?time=".$_SESSION['reservation']."&itemList=".$itemData."&home=true'><img src='images/favicon.png' class='headerLogo2' alt='Logo' /></a>";
}else{
	header( "refresh:3; url=catalogue.php" ); 
	
	echo "<p>Sorry, there has been an error. Please try again.</p>";
	echo "<p>Redirecting to the catalogue in 3 seconds.</p>";
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
<span class="checkoutStepsActive"> Step 1: (Address) </span><span class="checkoutSteps"> Step 2: (Delivery) </span><span class="checkoutSteps"> Step 3: (Payment) </span>
</a>

<hr style='width: 100%'>

	<form class="checkoutForm" action="checkoutStep2.php" method="POST">
		<table class="checkoutTable">
		<tr>
		<td><label for="nameTitle">Title</label></td>
		<td><select name="nameTitle">	
			<?php 
				$optionList = array("Mr","Mrs","Miss");
				
				forEach($optionList as $option){
					echo "<option value='" . $option . "' ";
					if(isset($_GET['nameTitle'])){if($_GET['nameTitle'] == $option){echo "selected";} }; 
					echo ">" . $option . "</option>";
				}
			?>
		</select></td>
		</tr>
		<tr>
		<td><label for="nameFirst">First Name <a class='checkoutStar'>*</a></label></td>
		<td><input type="text" name="nameFirst" required value="<?php if(isset($_GET['nameFirst'])){echo $_GET['nameFirst'];} ?>"  /></td>
		</tr>
		<tr>
		<td><label for="nameLast">Last Name <a class='checkoutStar'>*</a></label></td>
		<td><input type="text" name="nameLast" required value="<?php if(isset($_GET['nameLast'])){echo $_GET['nameLast'];} ?>"  /></td>
		</tr>
		<tr>
		<td><label for="phone">Phone Number <a class='checkoutStar'>*</a></label></td>
		<td><input type="text" name="phone" required value="<?php if(isset($_GET['phone'])){echo $_GET['phone'];} ?>"  /></td>
		</tr>
		<tr>
		<td><label for="eMail">eMail Address <a class='checkoutStar'>*</a></label></td>
		<td><input type="text" name="eMail" required value="<?php if(isset($_GET['eMail'])){echo $_GET['eMail'];} ?>"  /></td>
		</tr>
		<tr>
		<td></td>
		<td></td>
		</tr>
		<tr>
		<td><label for="address1">Address Line 1 <a class='checkoutStar'>*</a></label></td>
		<td><input type="text" name="address1" required value="<?php if(isset($_GET['address1'])){echo $_GET['address1'];} ?>"  /></td>
		</tr>
		<tr>
		<td><label for="address2">Address Line 2</label></td>
		<td><input type="text" name="address2" value="<?php if(isset($_GET['address2'])){echo $_GET['address2'];} ?>" /></td>
		</tr>
		<tr>
		<td><label for="address3">Address Line 3</label></td>
		<td><input type="text" name="address3" value="<?php if(isset($_GET['address3'])){echo $_GET['address3'];} ?>" /></td>
		</tr>
		<tr>
		<td><label for="city">Town / City <a class='checkoutStar'>*</a></label></td>
		<td><input type="text" name="city" required value="<?php if(isset($_GET['city'])){echo $_GET['city'];} ?>"  /></td>
		</tr>
		<tr>
		<td><label for="state">County / State / Region <a class='checkoutStar'>*</a></label></td>
		<td><input type="text" name="state" required value="<?php if(isset($_GET['state'])){echo $_GET['state'];} ?>"  /></td>
		</tr>
		<tr>
		<td><label for="zip">Zip / Postal Code <a class='checkoutStar'>*</a></label></td>
		<td><input type="text" name="zip" required value="<?php if(isset($_GET['zip'])){echo $_GET['zip'];} ?>"  /></td>
		</tr>
		<tr>
		<td><label for="country">Country <a class='checkoutStar'>*</a></label></td>
		<td>
		<select name="country">
		<?php 
			$optionList = array(
				"AF-Afghanistan","AF-Afghanistan","AX-Åland Islands","AL-Albania","DZ-Algeria","AS-American Samoa","AD-Andorra","AO-Angola","AI-Anguilla","AQ-Antarctica","AG-Antigua and Barbuda","AR-Argentina","AM-Armenia","AW-Aruba","AU-Australia",
				"AT-Austria","AZ-Azerbaijan","BS-Bahamas","BH-Bahrain","BD-Bangladesh","BB-Barbados","BY-Belarus","BE-Belgium","BZ-Belize","BJ-Benin","BM-Bermuda","BT-Bhutan","BO-Bolivia, Plurinational State of","BQ-Bonaire, Sint Eustatius and Saba",
				"BA-Bosnia and Herzegovina","BW-Botswana","BV-Bouvet Island","BR-Brazil","IO-British Indian Ocean Territory","BN-Brunei Darussalam","BG-Bulgaria","BF-Burkina Faso","BI-Burundi","KH-Cambodia","CM-Cameroon","CA-Canada",
				"CV-Cape Verde","KY-Cayman Islands","CF-Central African Republic","TD-Chad","CL-Chile","CN-China","CX-Christmas Island","CC-Cocos (Keeling) Islands","CO-Colombia","KM-Comoros","CG-Congo","CD-Congo, the Democratic Republic of the",
				"CK-Cook Islands","CR-Costa Rica","CI-Côte d'Ivoire","HR-Croatia","CU-Cuba","CW-Curaçao","CY-Cyprus","CZ-Czech Republic","DK-Denmark","DJ-Djibouti","DM-Dominica","DO-Dominican Republic","EC-Ecuador","EG-Egypt","SV-El Salvador",
				"GQ-Equatorial Guinea","ER-Eritrea","EE-Estonia","ET-Ethiopia","FK-Falkland Islands (Malvinas)","FO-Faroe Islands","FJ-Fiji","FI-Finland","FR-France","GF-French Guiana","PF-French Polynesia","TF-French Southern Territories",
				"GA-Gabon","GM-Gambia","GE-Georgia","DE-Germany","GH-Ghana","GI-Gibraltar","GR-Greece","GL-Greenland","GD-Grenada","GP-Guadeloupe","GU-Guam","GT-Guatemala","GG-Guernsey","GN-Guinea","GW-Guinea-Bissau","GY-Guyana","HT-Haiti",
				"HM-Heard Island and McDonald Islands","VA-Holy See (Vatican City State)","HN-Honduras","HK-Hong Kong","HU-Hungary","IS-Iceland","IN-India","ID-Indonesia","IR-Iran, Islamic Republic of","IQ-Iraq","IE-Ireland (Republic of)","IM-Isle of Man",
				"IL-Israel","IT-Italy","JM-Jamaica","JP-Japan","JE-Jersey","JO-Jordan","KZ-Kazakhstan","KE-Kenya","KI-Kiribati","KP-Korea, Democratic People's Republic of","KR-Korea, Republic of","KW-Kuwait","KG-Kyrgyzstan","LA-Lao People's Democratic Republic",
				"LV-Latvia","LB-Lebanon","LS-Lesotho","LR-Liberia","LY-Libya","LI-Liechtenstein","LT-Lithuania","LU-Luxembourg","MO-Macao","MK-Macedonia, the former Yugoslav Republic of","MG-Madagascar","MW-Malawi","MY-Malaysia","MV-Maldives",
				"ML-Mali","MT-Malta","MH-Marshall Islands","MQ-Martinique","MR-Mauritania","MU-Mauritius","YT-Mayotte","MX-Mexico","FM-Micronesia, Federated States of","MD-Moldova, Republic of","MC-Monaco","MN-Mongolia","ME-Montenegro",
				"MS-Montserrat","MA-Morocco","MZ-Mozambique","MM-Myanmar","NA-Namibia","NR-Nauru","NP-Nepal","NL-Netherlands","NC-New Caledonia","NZ-New Zealand","NI-Nicaragua","NE-Niger","NG-Nigeria","NU-Niue","NF-Norfolk Island","MP-Northern Mariana Islands",
				"NO-Norway","OM-Oman","PK-Pakistan","PW-Palau","PS-Palestinian Territory, Occupied","PA-Panama","PG-Papua New Guinea","PY-Paraguay","PE-Peru","PH-Philippines","PN-Pitcairn","PL-Poland","PT-Portugal","PR-Puerto Rico","QA-Qatar",
				"RE-Réunion","RO-Romania","RU-Russian Federation","RW-Rwanda","BL-Saint Barthélemy","SH-Saint Helena, Ascension and Tristan da Cunha","KN-Saint Kitts and Nevis","LC-Saint Lucia","MF-Saint Martin (French part)","PM-Saint Pierre and Miquelon",
				"VC-Saint Vincent and the Grenadines","WS-Samoa","SM-San Marino","ST-Sao Tome and Principe","SA-Saudi Arabia","SN-Senegal","RS-Serbia","SC-Seychelles","SL-Sierra Leone","SG-Singapore","SX-Sint Maarten (Dutch part)","SK-Slovakia",
				"SI-Slovenia","SB-Solomon Islands","SO-Somalia","ZA-South Africa","GS-South Georgia and the South Sandwich Islands","SS-South Sudan","ES-Spain","LK-Sri Lanka","SD-Sudan","SR-Suriname","SJ-Svalbard and Jan Mayen","SZ-Swaziland",
				"SE-Sweden","CH-Switzerland","SY-Syrian Arab Republic","TW-Taiwan, Province of China","TJ-Tajikistan","TZ-Tanzania, United Republic of","TH-Thailand","TL-Timor-Leste","TG-Togo","TK-Tokelau","TO-Tonga","TT-Trinidad and Tobago",
				"TN-Tunisia","TR-Turkey","TM-Turkmenistan","TC-Turks and Caicos Islands","TV-Tuvalu","UG-Uganda","UA-Ukraine","AE-United Arab Emirates","GB-United Kingdom","US-United States","UM-United States Minor Outlying Islands",
				"UY-Uruguay","UZ-Uzbekistan","VU-Vanuatu","VE-Venezuela, Bolivarian Republic of","VN-Viet Nam","VG-Virgin Islands, British","VI-Virgin Islands, U.S.","WF-Wallis and Futuna","EH-Western Sahara","YE-Yemen","ZM-Zambia","ZW-Zimbabwe"			
			);
				
			forEach($optionList as $option){
				echo "<option value='" . $option . "' ";
				if(isset($_GET['country'])){if($_GET['country'] == $option){echo "selected";}}else{if($option == "GB-United Kingdom"){echo "selected";}}; 
				//echo ">" . explode('-',$option)[1] . "</option>";
				echo ">" . explode('-',$option)[1] . "</option>";
			}
		?>

		</select>
		</td>
		</tr>
		<tr>
		<td></td>
		<td></td>
		</tr>
		<tr>
		<td><input type="hidden" name="itemList" value="<?php if(isset($_POST['itemList'])){echo $_POST['itemList'];}else if(isset($_GET['itemList'])){echo $_GET['itemList'];} ?>" /></td>
		<td><input type="reset" class="checkoutButton" /><input type="submit" value="Continue" class="checkoutButton" /></td>
		</tr>
		</table>
	</form>
	
</div>

</body>

</html>