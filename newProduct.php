<?php
include_once 'includes/db_connect.php';
include_once 'includes/functions.php';

if (empty($_POST['newName'])){
	header( "refresh:3; url=index.php" ); 
	echo "Name missing, redirecting in 3 seconds.";
}
else if(empty($_POST['newDesc'])){
	header( "refresh:3; url=index.php" ); 
	echo "Description missing, redirecting in 3 seconds.";
}
else if(empty($_POST['newPrice'])){
	header( "refresh:3; url=index.php" ); 
	echo "Price missing, redirecting in 3 seconds.";
}
else if(empty($_POST['newQuantity'])){
	header( "refresh:3; url=index.php" ); 
	echo "Quantity missing, redirecting in 3 seconds.";
}
else if(empty($_POST['newWidth'])){
	header( "refresh:3; url=index.php" ); 
	echo "Width missing, redirecting in 3 seconds.";
}
else if(empty($_POST['newHeight'])){
	header( "refresh:3; url=index.php" ); 
	echo "Height missing, redirecting in 3 seconds.";
}
else
{
	
	$name = filter_input(INPUT_POST, 'newName', FILTER_SANITIZE_STRING);
	$name = trim($name);
	$desc = filter_input(INPUT_POST, 'newDesc', FILTER_SANITIZE_STRING);
	$desc = trim($desc);
	$price = filter_input(INPUT_POST, 'newPrice', FILTER_SANITIZE_NUMBER_FLOAT);
	$quantity = filter_input(INPUT_POST, 'newQuantity', FILTER_SANITIZE_NUMBER_INT);
	$images = $_FILES['images'];
	$numOfPics = count($images["name"]);
	$width = filter_input(INPUT_POST, 'newWidth', FILTER_SANITIZE_NUMBER_FLOAT);
	$height = filter_input(INPUT_POST, 'newHeight', FILTER_SANITIZE_NUMBER_FLOAT);
	$itemSize = $width . "|" . $height;

	//$numOfPics = filter_input(INPUT_POST, 'newNumOfPics', FILTER_SANITIZE_NUMBER_INT);
	$material = filter_input(INPUT_POST, 'materialList', FILTER_SANITIZE_STRING);
	$finish = filter_input(INPUT_POST, 'finishList', FILTER_SANITIZE_STRING);
	if(isset($_POST['newDemo'])){
		$demo = 1;
	}else{
		$demo = 0;
	}
	if($_POST['newSize'] === 'Small'){
		$size = 1;
	}else{
		$size = 2;
	}
	$type = filter_input(INPUT_POST, 'type', FILTER_SANITIZE_STRING);
	
	$offer = filter_input(INPUT_POST, 'offer', FILTER_SANITIZE_STRING);
	if(!empty($_POST['newOfferPrice'])){
		$offerPrice = filter_input(INPUT_POST, 'newOfferPrice', FILTER_SANITIZE_STRING);
	}else{
		$offerPrice = '';
	}
	
	if($offer == 'None'){
		$offerString = '0';
	}else{
		$offerString = '['.$offer.']('.$offerPrice.'){}';
	}
	
	$material = rtrim($material,',');
	$finish = rtrim($finish,',');
	$finish = str_replace("Tung Oil","Tung Oil[allergen]",$finish);
	
	$errorCode	= array(0);
	$error[0] = "File is not an image.";
	$error[1] = "File already exists.";
	$error[2] = "File size too large.";
	$error[3] = "File format not JPG, JPEG, PNG or GIF.";
	$error[4] = "There is already a directory with this name on the server. Try a different name.";
	
	$target_dir = "images/products/" . $name;
	$CSVSrc = "";
	if (!is_dir($target_dir))
	{
		
		mkdir($target_dir, 0705);
		
		mkdir($target_dir."/_600", 0705);
		mkdir($target_dir."/_1000", 0705);
		mkdir($target_dir."/_1400", 0705);
		$imageCheckFail = 0;
		
		if($numOfPics == 0){
			$errorCode[0] = performImageChecks($target_dir, -1, $src[0]);
			$CSVSrc = $src[0] . ",";
		}
		else
		{
			for($i = 0; $i < $numOfPics; $i++)
			{
				$errorCode[$i] = performImageChecks($target_dir, $i, $src[$i]);
				if($errorCode[$i] != 0)
				{
					$imageCheckFail = $errorCode[$i];
					continue;
				}
			}
		}
	}
	else
	{
		$imageCheckFail = 4;
	}

	if($imageCheckFail != 0)
	{
		$uploadFailed = true;
		if($imageCheckFail != 4)
		{
			if (is_dir($target_dir)) {
				emptyDir($target_dir);
				rmdir($target_dir);
			}
		}
		header( "refresh:3; url=index.php" ); 
		echo "<p style='background: red;'>File not uploaded. " . $error[$imageCheckFail-1] . "Redirecting in 3 seconds.</p>";
	}
	else
	{

		$uploadFailed = false;

		for($i = 0; $i < $numOfPics; $i++)
		{
			$smallSrc = $target_dir . "/_600/" . basename($src[$i]);
			$medSrc = $target_dir . "/_1000/" . basename($src[$i]);
			$bigSrc = $target_dir . "/_1400/" . basename($src[$i]);
			
			// || !move_uploaded_file($images["tmp_name"][$i], $medSrc) || !move_uploaded_file($images["tmp_name"][$i], $bigSrc)
			if (!move_uploaded_file($images["tmp_name"][$i], $bigSrc)) {
				echo "Error uploading picture ".$i.". The product was not added.";
				if (is_dir($target_dir)) {
					emptyDir($target_dir);
					rmdir($target_dir);
				}
				$uploadFailed = true;
			}else{
				$src_img = ImageCreateFromJpeg($target_dir . "/_1400/" . basename($src[$i]));
				$src_imggw = imagesx($src_img);
				$src_imggh = imagesy($src_img);
				
				$ratio = $src_imggw / $src_imggh;

				$med_imagex = 1000;
				$med_imagey = $med_imagex/$ratio;
				
				$small_imagex = 600;
				$small_imagey = $small_imagex/$ratio;
				
				$med_image = imagecreatetruecolor($med_imagex, $med_imagey);
				imagecopyresampled($med_image, $src_img, 0, 0, 0, 0, $med_imagex, $med_imagey, $src_imggw, $src_imggh);
				imagejpeg($med_image, $target_dir . "/_1000/" . basename($src[$i]), 100);
				imagedestroy($med_image);
				
				$small_image = imagecreatetruecolor($small_imagex, $small_imagey);
				imagecopyresampled($small_image, $src_img, 0, 0, 0, 0, $small_imagex, $small_imagey, $src_imggw, $src_imggh);
				imagejpeg($small_image, $target_dir . "/_600/" . basename($src[$i]), 100);
				imagedestroy($small_image);
				
				imagedestroy($src_img);
			}
		}
	}
	if($uploadFailed === false)
	{
		
		$CSVSrc = $target_dir.'/thumb.jpg,';

		for($i = 0; $i < $numOfPics; $i++)
		{
			$CSVSrc .= $src[$i] . ",";
		}
		$CSVSrc = substr_replace($CSVSrc ,"", -1);
		
		$src_img = ImageCreateFromJpeg($target_dir . "/_1400/" . basename($src[0]));
		$src_imggw = imagesx($src_img);
		$src_imggh = imagesy($src_img);

		$small_imagex = 622;
		$small_imagey = 350;
		$small_image = imagecreatetruecolor($small_imagex, $small_imagey);
		imagecopyresampled($small_image, $src_img, 0, 0, 0, 0, $small_imagex, $small_imagey, $src_imggw, $src_imggh);
		
		//imagefilter($small_image, IMG_FILTER_GRAYSCALE); 
		imagejpeg($small_image, $target_dir.'/thumb.jpg', 100);

		imagedestroy($src_img);
		imagedestroy($small_image);
		
		$prep_stmt = "SELECT ID FROM products WHERE name = ? LIMIT 1";
		$stmt = $mysqli->prepare($prep_stmt);
	
		if ($stmt) {
				
			$stmt->bind_param('s', $name);
			$stmt->execute();
			$stmt->store_result();
				
			if ($stmt->num_rows == 1) {
				if (is_dir($target_dir)) {
					emptyDir($target_dir);
					rmdir($target_dir);
				}
				header( "refresh:3; url=index.php" ); 
				echo "<p style='background: red;'>A product with this name already exists, redirecting in 3 seconds. The product was not added.</p>";
				$stmt->close();
			}else{
				if ($insert_stmt = $mysqli->prepare("INSERT INTO products (Name, Src, Description, Quantity, Price, Materials, Finish, Remaining, Demo, Size, Type, Offer, ItemSize) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)")) {
					$insert_stmt->bind_param('sssidssiiisss', $name, $CSVSrc, $desc, $quantity, $price, $material, $finish, $quantity, $demo, $size, $type, $offerString, $itemSize);
					if (! $insert_stmt->execute()) {
						if (is_dir($target_dir)) {
							emptyDir($target_dir);
							rmdir($target_dir);
						}
						header( "refresh:3; url=index.php" ); 
						echo "<p style='background: red;'>Error updating database. Redirecting in 3 seconds. The product was not added.</p>";
					}
					else
					{
						header( "refresh:3; url=catalogue.php" );
						echo "<p style='background: Green;'>Product uploaded successfully. Redirecting in 3 seconds.</p>";
					}
				}
			}
		}
	}
}



function performImageChecks($target_dir, $picNumber, &$src){
	
	if($picNumber == -1){
		$src = "images/error.png";
	}else{
		$src = $target_dir . "/" . basename($_FILES['images']['name'][$picNumber]);	
	
		$imageFileType = strtolower(pathinfo($src,PATHINFO_EXTENSION));
		// Check if image file is a actual image or fake image
		if(isset($_POST["submit"])) {
			$check = getimagesize($_FILES['images']['tmp_name'][$picNumber]);
			if($check === false) {
				return 1;
			}
		}
		
		// Check if file already exists
		if (file_exists($src)) {
			return 2;
		}
		
		// Check file size
		if ($_FILES['images']['size'][$picNumber] > 10000000) {
			return 3;
		}
		
		// Allow certain file formats
		if($imageFileType != "jpg" && $imageFileType != "png" && $imageFileType != "jpeg" && $imageFileType != "gif" ) {
			return 4;
		}
	}
	
	return 0;
	
}
?>