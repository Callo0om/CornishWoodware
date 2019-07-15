<!DOCTYPE html>
<html lang="en">

<?php
include "head.html";
echo "<title>HiC Message sent</title></head>";

echo "<body>";
include "header.php";
include "nav.php";

?>

<br class="pageBreak" />


<div class="pageTitleImage">
	<img src="images/mailImages/mail1.jpg" alt="big Picture 1" />
	
		<?php
		if (isset($_GET['error'])) {
			if($_GET['error'] === 'mail'){
				echo "<a style='color: red;'>Message<br /> not sent!";
				$text = "Sorry, there has been an error on the server. Please <a href='/contact.php'>Click here</a> to return and try again.";
			}else if($_GET['error'] === 'form'){
				echo "<a style='color: red;'>Message<br /> not sent!";
				$text = "Sorry, there seems to be a problem with the form data. Please <a href='/contact.php'>Click here</a> to return and try again.";
			}
		}else{
			echo "<a style='color: green;'>Message<br /> sent!";
			$text = "Thank you for the message, we will get back to you as soon as possible.";
		}
		?>
	</a>
	
</div>

<div class="homepageColourBar">
<br />
	<?php
	echo $text;
	?>
	<br /><br />
	<a href="catalogue.php">Return to catalogue.</a>
</div>

</body>
</html>