<header>

<nav>
	<div class="navBar" id="navBar">
	
		<div class="navBarFlex" id="navBarFlex">
		
			<div id="button1">
				<a href="index.php">Home</a>
			</div>
			<div id="button2">
				<a href="catalogue.php">Catalogue</a>
			</div>
			<div id="button3">
				<a href="deals.php">Bundles</a>
			</div>
			<div id="button4">
				<a href="cart.php">Cart</a>
				<span class="cartNumber">
				<?php
				if (isset($_SESSION['item'])){
					if(trim(! $_SESSION['item']) == "" )
					{
						echo count(explode(',',$_SESSION['item']));
					}
					else
					{
						echo "0";
					}
					
				}else{
					echo "0";
				}
				?>
				</span>
			</div>
			<div id="button5">
				<a href="contact.php">Contact Us</a>
			</div>
		</div>
		
		<?php if(isset($title)){echo "<p class='headerText2' id='headerText'>" . $title;}else{echo "<p class='headerText' id='headerText'>Cornish Woodware";}; ?></p>
		<sub class="headerSubText"><?php if(!isset($title)){echo "<i>-Hand Made in Cornwall-</i>";} ?></sub>
		<img src='images/header.jpg' class='headerLogo' alt='Cornish Woodware Logo' />
		
		<?php if(isset($title)){echo "<div class='offerText'>Summer is here, so enjoy <u>5% off</u> everything. <br />Just enter code 'SUMMER5' at the checkout.</div>";}else{echo "<div class='offerText' style='background: #e7e3d4;'>Summer is here, so enjoy <u>5% off</u> everything. <br />Just enter code 'SUMMER5' at the checkout.</div>";}; ?>
		
	</div>
	
</nav>

</header>