<footer>

<div id="fb-root"></div>
<script>(function(d, s, id) {
  var js, fjs = d.getElementsByTagName(s)[0];
  if (d.getElementById(id)) return;
  js = d.createElement(s); js.id = id;
  js.src = 'https://connect.facebook.net/en_GB/sdk.js#xfbml=1&version=v3.2';
  fjs.parentNode.insertBefore(js, fjs);
}(document, 'script', 'facebook-jssdk'));</script>

	<div class="footerContainer">
		
		<div class="footerFlexContainer">
		
			<div class="footerSpan">
				<div> - <a href="privacy.php">Privacy Policy</a> - </div>
				<div> - <a href="delivery.php">Delivery</a> - </div>
				<div> - <a href="returns.php">Returns</a> - </div>
				<div> - <a href="terms.php">Terms of Use</a> - </div>
			</div>

			<div class="footerSpan">
				<div> - <a href="catalogue.php">Catalogue</a> - </div>
				<div> - <a href="cart.php">Cart</a> - </div>
				<div> - <a href="deals.php">Deals</a> - </div>
			</div>
			
			<div class="footerSpan">
				<img src="images/footerLogo.jpg" class="footerLogo" alt="Footer Logo" />
				<div> &nbsp; </div>
				<div> - <a href="contact.php">Contact Us</a> - </div><br />
			</div>
			

		</div>
		
		<div class="footerCopyright">
				- &#169; Copyright <?php echo date("Y"); ?> Cornish Woodware. All Rights Reserved. - &nbsp; &nbsp; &nbsp; &nbsp; 
				- Facebook: <div class="fb-like" data-href="https://www.facebook.com/HandmadeCornwall/" data-layout="button" data-action="like" data-size="large" data-show-faces="false" data-share="true"></div> -
				<label for="showAdminPopUp" class="adminText"> - Admin Login - </label>
				<input type="checkbox" id="showAdminPopUp" value="button" />
				
				<aside class="adminLoginBox">
					<form action="includes/process_login.php" method="POST" name="login_form">
					<input type="text" name="fName"><br />
					<input type="password" name="fPass"><br />
					<input type="button" value="Login" onclick="formhash(this.form, this.form.fPass);" />
					</form>
				</aside>
		</div>
		
		

		
		
	</div>

</footer>
<br />
<br />