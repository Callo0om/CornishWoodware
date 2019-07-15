<?php
if(isset($_SESSION['reservation'])){
	if(!isset($_POST['ignoreSession'])){
		header( "refresh:0; url=timeout.php?time=".$_SESSION['reservation']."&itemList=".$_SESSION['item']."&home=true" );
	}
}
?>

<div class="menuButtonContainer" onclick="openMenu(this)">
<p>Navigation:</p>
  <div class="menuButtonBar1"></div>
  <div class="menuButtonBar2"></div>
  <div class="menuButtonBar3"></div>
</div>

<div class="closedMenuCartIcon">
My Cart:<br />
	<a class="fa fa-shopping-cart" href="cart.php" style="font-size: 15vw; text-decoration: none; margin-top: 1vw;">
		<span class="closedMenuCartIconNumber">
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
	</a>
</div>

