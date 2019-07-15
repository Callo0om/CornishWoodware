<?php
include_once 'includes/functions.php';
sec_session_start();
?>

<!DOCTYPE html>
<html lang="en">

<?php
include "head.html";
echo "<title>CW Returns policy</title></head>";
?>

<body>

<?php
include "header.php"; 
 include "nav.php";
?>

<br class="pageBreak" />

<div class="pageContent" id="returnContent" style="padding: 0.5vw;">

<h1>Returns Policy</h1>

<p>Our policy lasts 30 days. If 30 days have gone by since your purchase, unfortunately we can’t offer you a refund or exchange.</p>

<p>To be eligible for a return, your item must be unused and in the same condition that you received it. It must also be in the original packaging.</p>

<p>To complete your return, we require a returns label which will be sent via email after a return case is opened by contacting us on the <a href="contact.php">Contact</a> page.</p>

<p>Please do not send your purchase back to the manufacturer.</p>

<h4>Partial refunds</h4>
<ul>
	<li>Any item not in its original condition, is damaged or missing parts for reasons not due to our error.</li>
	<li>Any item that is returned more than 30 days after delivery</li>
</ul>

<h4>Refunds</h4>
<p>Once your return is received and inspected, we will send you an email to notify you that we have received your returned item. We will also notify you of the approval or rejection of your refund.</p>
<p>If you are approved, then your refund will be processed via PayPal within 7 days.</p>

<h4>Late or missing refunds</h4>
<p>If you haven’t received a refund yet, first check your PayPal account again.</p>
<p>Then contact PayPal, it may take some time before your refund is officially posted.</p>
<p>If you’ve done this and you still have not received your refund yet, please contact us on the <a href="contact.php">Contact</a> page.</p>

<h4>Sale items</h4>
<p>Only regular priced items may be refunded, unfortunately sale items cannot be refunded.</p>

<h4>Exchanges</h4>
<p>I only replace items if they are defective or damaged.  If you need to exchange it for an equivalent item, please contact me on the <a href="contact.php">Contact</a> page and I will send you a return label.</p>
<p>Depending on where you live, the time it may take for your exchanged product to reach you, may vary.</p>

<h4>Shipping</h4>
<p>To return your product, you should contact me on the <a href="contact.php">Contact</a> page.</p>

<p>You will be responsible for paying for your own shipping costs for returning your item. Shipping costs are non-refundable. If you receive a refund, the cost of return shipping will be deducted from your refund.</p>

<p>If you are shipping an item over £50, you should consider using a trackable shipping service or purchasing shipping insurance. We cannot guarantee that we will receive your returned item.</p>

		
</div>

<?php
include "footer.php";
?>

</body>

</html>