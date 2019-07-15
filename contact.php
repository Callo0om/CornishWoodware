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
echo "<title>CW Contact page</title></head>";

echo "<body>";

?> 

<?php
include "header.php";
?>

<?php
if ($logged == true) {
	$title = "<a href='includes/logout.php?returnPage=deals.php' style='color: Black;'>Log out</a>";
	include "nav.php";
} else {
	$title = "Contact Us";
	include "nav.php";
}
?>

<br class="pageBreak" />

<div class="homepageImage" style='height: 10vw;'>
	<img src="images/contactImages/contact1_600.jpg" srcset="images/contactImages/contact1_600.jpg 600w, images/contactImages/contact1_1000.jpg 1000w, images/contactImages/contact1_1400.jpg 1400w" sizes="(max-width: 550px) 100vw, 50vw" alt="big Picture 1">
</div>

<div class="homepageColourBar">

	<fieldset class="sortingBox" id="sortingForm">
	Please feel free to contact us with any queries you may have by using the form below. <br /><br /> We aim to reply to messages within the same day, but please be patient as We personally respond to each and every query.
	</fieldset>
	
	<br />
	<br />

	<div class="homepageImage2" style="height: 12vw;">
		<img src="images/contactImages/contact2_600.jpg" srcset="images/contactImages/contact2_600.jpg 600w, images/contactImages/contact2_1000.jpg 1000w, images/contactImages/contact2_1400.jpg 1400w" sizes="(max-width: 550px) 100vw, 50vw" alt="small Picture 1">
		<a>
			<p>
				Message<br />
			</p>
		</a>
	</div>

	<fieldset class="homepageColourBar2" style="margin: 0;">
		<form method="POST" action="includes/sendMessage.php">
			<fieldset class="contactForm" style="border: 0;">

				<label for="name">Name:<br /><a>(Required)</a></label>
				<input type="text" name="name" id="name" placeholder="Name" required />
					<br />
				<label for="subject">Subject:<br /><a>(Required)</a></label>
				<input type="text" name="subject" id="subject" placeholder="Subject" required />
					<br />
				<label for="eMail">eMail Address:<br /><a>(Required)</a></label>
				<input type="email" name="eMail" id="eMail" placeholder="eMail" required />
					<br />
				<label for="phone">Phone Number</label>
				<input type="text" name="phone" id="phone" placeholder="Phone (Optional)" />
					<br /><br />
				<label for="message">Message:<br /><a>(Required)</a></label>
				<textarea name="message" id="message" placeholder="Message" required >
<?php
if(isset($_GET['items']) && isset($_GET['name']) && isset($_GET['address'])){
$items = unserialize($_GET['items']);
echo "[Add your message here...] \n \n &nbsp&nbsp Ref:: Delivery quote for: \n";
foreach($items as $item){
echo $item[1] . " * " . $item[0] . "\n";
}
$name = $_GET['name'];
$address = unserialize($_GET['address']);
echo "\n &nbsp&nbsp To: \n";
echo $name . "\n";
foreach($address as $addressLine){
echo $addressLine . "\n";
}
}else if(isset($_GET['orderNo'])){
	echo "&nbsp&nbsp Ref:: Order No: " . $_GET['orderNo'] . " \n\n [Add your message here...]";
	
}
?>
</textarea>
					<br /><br />
				<label for="submit"></label>
				<input type="submit" name="submit" id="submit" value="Submit" class="contactSubmit" />

			</fieldset>
		</form>
	</fieldset>
	
	<br /><br />
	
	<div class="homepageImage2" style="height: 12vw;">
		<img src="images/contactImages/contact3_600.jpg" srcset="images/contactImages/contact3_600.jpg 600w, images/contactImages/contact3_1000.jpg 1000w, images/contactImages/contact3_1400.jpg 1400w" sizes="(max-width: 550px) 100vw, 50vw" alt="small Picture 2">
		<a>
			<p>
				eMail<br />
			</p>
		</a>
	</div>
	
	<fieldset class="homepageColourBar2" style="margin: 0;">
	<br />
		<form action="mailto:callo0om@hotmail.co.uk" name="emailForm" method="GET">
			<input type="hidden" value="email" name="type" />
			<input type="submit" value="eMail Us" class="contactEmail" />
		</form>
		<br />
	</fieldset>
	
	<br /><br />
	
	<div class="homepageImage2" style="height: 12vw;">
		<img src="images/contactImages/contact4_600.jpg" srcset="images/contactImages/contact4_600.jpg 600w, images/contactImages/contact4_1000.jpg 1000w, images/contactImages/contact4_1400.jpg 1400w" sizes="(max-width: 550px) 100vw, 50vw" alt="small Picture 3">
		<a>
			<p>
				Facebook<br />
			</p>
		</a>
	</div>
	
	<fieldset class="homepageColourBar2" style="margin: 0; padding-bottom: 2vw;">
	<br />
	<form target="_blank" action="https://www.facebook.com/HandmadeCornwall/">
		<input type="submit" value="Find us on Facebook" class="contactEmail" />
	</form>
	<br />
	</fieldset>
	
	<br />
	<br />
	
</div>

	
		
	




<?php
include "footer.php";
?>

</body>
</html>
