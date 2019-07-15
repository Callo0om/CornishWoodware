<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

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
echo "<title>Cornish Woodware</title></head>";

echo "<body>";
?> 

<div class="menuButtonContainer" onclick="openMenu(this)">
<p>Navigation:</p>
  <div class="menuButtonBar1"></div>
  <div class="menuButtonBar2"></div>
  <div class="menuButtonBar3"></div>
</div>
<?php
	if ($logged == true) {
		$title = "<a href='includes/logout.php?returnPage=index.php' style='color: Black;'>Log out</a>";
	}

	include "nav.php";

?>

<br class="pageBreak" />

<div class="homepageImage">
	<img src="images/homeImages/homeBanner_600.jpg" srcset="images/homeImages/homeBanner_600.jpg 600w, images/homeImages/homeBanner_1000.jpg 1000w, images/homeImages/homeBanner_1400.jpg 1400w" sizes="(max-width: 550px) 100vw, 50vw" alt="Main Banner">
	<a>
		Crafting memories&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
		<p>
			Quality hand made products from Cornwall
		&nbsp;&nbsp;&nbsp;
		</p>
	</a>
	
</div>

<div class="homepageColourBar">

	<br />
	<br />
	<br />
	
	<?php
	
	if ($logged == true) {
		include "includes/orders.php";
	}
		
	?>
	
	<br />
	<br />
	<br />
	
	<div class="homepageImage2">
		
		<img src="images/homeImages/home4_600.jpg" srcset="images/homeImages/home4_600.jpg 600w, images/homeImages/home4_1000.jpg 1000w, images/homeImages/home4_1400.jpg 1400w" sizes="(max-width: 550px) 100vw, 50vw" alt="big Picture 4">
		
	</div>

	<div class="homepageColourBar2">
		We build unique high-quality items from our base near Launceston in Cornwall. Please browse the <a href="catalogue.php">Calalogue</a> to view our items.
	</div>

	<br />
	<br />
	<br />

	<div class="homepageImage2">
		
		<img src="images/homeImages/home1_600.jpg" srcset="images/homeImages/home1_600.jpg 600w, images/homeImages/home1_1000.jpg 1000w, images/homeImages/home1_1400.jpg 1400w" sizes="(max-width: 550px) 100vw, 50vw" alt="big Picture 1">
		
	</div>

	<div class="homepageColourBar2">
		We aim to make completely unique items, and the exact item is pictured on this site, so the item you see is the item you will receive. We clearly mark any items which aren't unique.
	</div>

	<br />
	<br />
	<br />

	<div class="homepageImage2">
		<img src="images/homeImages/home2_600.jpg" srcset="images/homeImages/home2_600.jpg 600w, images/homeImages/home2_1000.jpg 1000w, images/homeImages/home2_1400.jpg 1400w" sizes="(max-width: 550px) 100vw, 50vw" alt="big Picture 2">
		
	</div>

	<div class="homepageColourBar2">
		We use <a href="https://www.paypal.com/uk/webapps/mpp/paypal-popup" title="How PayPal Works" onclick="javascript:window.open('https://www.paypal.com/uk/webapps/mpp/paypal-popup','WIPaypal','toolbar=no, location=no, directories=no, status=no, menubar=no, scrollbars=yes, resizable=yes, width=1060, height=700'); return false;"><img src="https://www.paypalobjects.com/webstatic/mktg/Logo/pp-logo-100px.png" alt="PayPal Logo" class="paypalImage" /></a> for all payments so you can be assured that your data is safe.
	</div>

	<br />
	<br />
	<br />

	<div class="homepageImage2">
		<img src="images/homeImages/home3_600.jpg" srcset="images/homeImages/home3_600.jpg 600w, images/homeImages/home3_1000.jpg 1000w, images/homeImages/home3_1400.jpg 1400w" sizes="(max-width: 550px) 100vw, 50vw" alt="big Picture 3">
	</div>

	<div class="homepageColourBar2">
		Feel free to send us any queries you may have over at the <a href='contact.php'>Contact page</a>, We aim to reply to messages by the end of the next working day.
	</div>
	
	<br />
	<br />
	<br />
	
</div>

<?php
include "footer.php";
?>

</body>
</html>
