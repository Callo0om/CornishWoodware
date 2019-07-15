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
echo "<title>CW Catalogue</title></head>";

if(isset($_GET['anchor']))
{
	echo "<body onLoad='load(\"".$_GET['anchor']."\")'>";
}
else
{
	echo "<body>";
}

if(isset($_GET['sortBy'])){
	$_SESSION['sortBy'] = $_GET['sortBy'];
}
if(isset($_GET['filterBy'])){
	$_SESSION['filterBy'] = $_GET['filterBy'];
}
if(isset($_GET['showResults'])){
	$_SESSION['showResults'] = $_GET['showResults'];
}
if(isset($_GET['pageNumber'])){
	$_SESSION['pageNumber'] = $_GET['pageNumber'];
}

if(isset($_SESSION['sortBy'])){
	if(($_SESSION['sortBy']) === 'Type'){
		$sortBy = 'Type';
		$sortBy2 = 'Type';
		$sortDir = 'ASC';
	}else if(($_SESSION['sortBy']) === 'Price(Asc)'){
		$sortBy = 'Price';
		$sortBy2 = 'Price(Asc)';
		$sortDir = 'ASC';
	}else if(($_SESSION['sortBy']) === 'Price(Dec)'){
		$sortBy = 'Price';
		$sortBy2 = 'Price(Dec)';
		$sortDir = 'DESC';
	}else if(($_SESSION['sortBy']) === 'Materials'){
		$sortBy = 'Materials';
		$sortBy2 = 'Materials';
		$sortDir = 'ASC';
	}
}else{
	$sortBy = 'Type';
	$sortBy2 = 'Type';
	$sortDir = 'ASC';
}

if(isset($_SESSION['filterBy'])){
	$filterBy = $_SESSION['filterBy'];
}else{
	$filterBy = 'All';
}

$resultNumberOptions = array(10, 20);
if(isset($_SESSION['showResults'])){
	if($_SESSION['showResults'] === 'All'){
		$showResults = 'All';
	}else{
		$showResults = intval($_SESSION['showResults']);
	}
}else{
	$showResults = intval($resultNumberOptions[1]);
}


if(isset($_SESSION['pageNumber'])){
	$pageNumber = intval($_SESSION['pageNumber']);
}else{
	$pageNumber = 1;
}

include "header.php"; 

if ($logged == true) {
	$title = "<a href='includes/logout.php?returnPage=catalogue.php' style='color: Black;'>Log out</a>";
	include "nav.php";
} else {
	$title = "Catalogue";
	include "nav.php";
}
?>

<br class="pageBreak" />

<div class="homepageImage" style='height: 5vw;'>
	<img src="images/catalogueImages/catalogue1_600.jpg" srcset="images/catalogueImages/catalogue1_600.jpg 600w, images/catalogueImages/catalogue1_1000.jpg 1000w, images/catalogueImages/catalogue1_1400.jpg 1400w" sizes="(max-width: 550px) 100vw, 50vw" alt="big Picture 1">
</div>

<div class="homepageColourBar">

	<fieldset class="sortingBox" id="sortingForm">
	Items marked with a * are unique so if anything takes your fancy, make sure to get it quickly to avaid disappointment.
	<br />
	<hr>
	Sort by:
		<select id="sortBy" class="sortingTextBoxes" onchange="sortingFunction();">
		
			<option <?php if($sortBy2 === 'Type'){ echo "selected"; } ?> >Type</option>
			<option <?php if($sortBy2 === 'Price(Asc)'){ echo "selected"; } ?> >Price(Asc)</option>
			<option <?php if($sortBy2 === 'Price(Dec)'){ echo "selected"; } ?> >Price(Dec)</option>
			<option <?php if($sortBy2 === 'Materials'){ echo "selected"; }; ?> >Materials</option>
		</select>

	Filter by:
		<select id="filterBy" class="sortingTextBoxes" onchange="sortingFunction();">
		<option selected>All</option>
			<?php
			$filtersQuery = "SELECT DISTINCT Type FROM products";
			$filtersStmt = $mysqli->stmt_init();
			$filtersStmt = $mysqli->prepare($filtersQuery);
			
			if ($filtersStmt){
				$filtersStmt->execute();
				$filtersResult = get_result($filtersStmt);
				
				foreach($filtersResult as $filter)
				{
					if($filterBy === $filter['Type']){
						echo "<option selected>".$filter['Type']."</option>";
					}else{
						echo "<option>".$filter['Type']."</option>";
					}
				}
			}else{
				echo "Error loading filter details, plaese use <a href='contact.php'>Contact page</a> to contact us to resolve issue.";
			}
			?>
		</select>
		
	Results per page:
		<select id="showResults" class="sortingTextBoxes" onchange="sortingFunction();">
			<option <?php if($showResults === $resultNumberOptions[0]){ echo "selected"; } ?> ><?php echo $resultNumberOptions[0]?></option>
			<option <?php if($showResults === $resultNumberOptions[1]){ echo "selected"; } ?> ><?php echo $resultNumberOptions[1]?></option>
			<option <?php if($showResults === 'All'){ echo "selected"; } ?> >All</option>
		</select>
	
	</fieldset>
	
	<?php
	
	if ($logged == true) {
		echo "<br /><br />";
		include "adminCatalogue.html";
	}
		
	?>
	
	
	
	<div class="productListContainer">

		<?php
		$catalogueStmt = $mysqli->stmt_init();
		$currentType = "";
		$unknown = 'unknown';

		if($showResults > 0){
			if(!isset($_SESSION['filterBy']) || $_SESSION['filterBy'] === 'All'){
				$catalogueStmt = $mysqli->prepare("SELECT * FROM products WHERE NOT Type=? ORDER BY ".$sortBy." ".$sortDir." LIMIT ? OFFSET ?");
				$catalogueStmt->bind_param('sii', $unknown, intval($showResults), intval(($pageNumber - 1) * intval($showResults)));
			}else{
				$catalogueStmt = $mysqli->prepare("SELECT * FROM products WHERE Type=? ORDER BY ".$sortBy." ".$sortDir." LIMIT ? OFFSET ?");
				$catalogueStmt->bind_param('sii', $_SESSION['filterBy'], intval($showResults), intval(($pageNumber - 1) * intval($showResults)));
			}
		}else{
			$catalogueStmt = $mysqli->prepare("SELECT * FROM products WHERE NOT Type=? ORDER BY ".$sortBy." ".$sortDir."");
			$catalogueStmt->bind_param('s', $unknown);
		}

		if ($catalogueStmt){
			$catalogueStmt->execute();
			$catalogueResult = get_result($catalogueStmt);
			
			foreach($catalogueResult as $catalogueRow) {	

				if($catalogueRow['Offer'] === '0'){
					$display = true;
				}else if (explode(']',explode('[',$catalogueRow['Offer'])[1])[0] !== 'bundle'){
					$display = true;
				}else{
					$display = false;
				}
				
				if($display === true){
				
					if($sortBy === 'Type' && $filterBy === 'All'){
						if($currentType !== $catalogueRow['Type']){
							$currentType = $catalogueRow['Type'];

							echo "<div class='productContainerCatalogue' style='padding: 0; margin: 0; text-align: center; width: 100%;'>";
							echo "<p class='productTypeLabel'>".$currentType."</p>";
							echo "</div>";
						}
					}
					
					
					if(!isset($_SESSION['filterBy']) || $_SESSION['filterBy'] === 'All' || $_SESSION['filterBy'] === $catalogueRow['Type']){
							
						echo "<div class='productContainerCatalogue'>";
							
							if($catalogueRow['Quantity'] == 1){
								echo "<a class='productContainerStar'><sub>*</sub> </a>";
								$maxStringLength = 15;
							}else{
								$maxStringLength = 18;
							}
							echo "<a class='productContainerTextLarger' href='productPage.php?productName=" . str_replace(' ', '%20', $catalogueRow['Name']) . "&returnPage=catalogue.php'>";
								if(strlen($catalogueRow['Name']) > $maxStringLength){
									echo substr($catalogueRow['Name'], 0, $maxStringLength-3) . "...";
								}else{
									echo substr($catalogueRow['Name'], 0, $maxStringLength);
								}
							echo "</a>";
							
						$srcs = explode(',', $catalogueRow['Src']);
						echo "<a class='productImageThumb' href='productPage.php?productName=" . str_replace(' ', '%20', $catalogueRow['Name']) . "&returnPage=catalogue.php'>";
						echo "<img src='" . str_replace(' ', '%20', $srcs[0]) . "' alt='Product: " . $catalogueRow['Name'] . "' id='anchor".str_replace(' ', '%20', $catalogueRow['Name'])."'>";
						echo "</a>";
							
						echo "<br />";
							
						if($catalogueRow['Offer'] !== '0'){
							$dealType = explode(']',explode('[',$catalogueRow['Offer'])[1])[0];
							$offerPrice = explode(')',explode('(',$catalogueRow['Offer'])[1])[0];
						
							if($dealType === '2for'){
								echo "<a class='productContainerTextLarge'><u>£" . number_format((float)$catalogueRow['Price']/100, 2, '.', '') . "</u></a>  &nbsp;or&nbsp;  <a class='productContainerTextLarge'>2</a> for <a class='productContainerTextLarge'><u>£" . number_format((float)$offerPrice/100, 2, '.', '') . "</u></a>";
							}else if($dealType === 'reduceBy'){
								echo "<a class='productContainerTextLarge'>" . $offerPrice . "%Off!</a>  Now <a class='productContainerTextLarge'><u>£" . number_format((float)(($catalogueRow['Price'] / 100) * (100 - $offerPrice))/100, 2, '.', '') . "</u></a>";
							}else if($dealType === 'reduceTo'){
								echo "Was <a class='productContainerTextLarge'><strike>£" . number_format((float)$catalogueRow['Price']/100, 2, '.', '') . "</strike></a> Now <a class='productContainerTextLarge'><u>£" . number_format((float)$offerPrice/100, 2, '.', '') . "</u></a>";
							}else{
								echo "<a class='productContainerTextLarge'><u>£" . number_format((float)$catalogueRow['Price']/100, 2, '.', '') . "</u></a>";
							}
						}else{
							echo "<a class='productContainerTextLarge'><u>£" . number_format((float)$catalogueRow['Price']/100, 2, '.', '') . "</u></a>";
						}
							
						if ($logged == true) {
							echo "<br />";
							echo "<input type='button' value='Admin Remove' class='catalogueRemoveButton' onClick='removeItem(\"admin\", \"".$catalogueRow['Name']."\"); location.reload();' />";
						}
								
						echo "</div>";
					}
					
				}

			}
			
			if(!isset($_SESSION['filterBy']) || $_SESSION['filterBy'] === 'All'){
				$query = "select COUNT(*) AS total_things FROM products WHERE NOT Type='Unknown'";
			}else{
				$query = "select COUNT(*) AS total_things FROM products WHERE Type='".$_SESSION['filterBy']."'";
			}
			
			$result2 = $mysqli->query($query);
			$row2 = mysqli_fetch_array($result2, MYSQLI_NUM);
			
			if($showResults > 0){
				$total = ceil($row2[0] / $showResults);
			}else{
				$total = 1;
			}
			
			
			echo "<div class='productContainerCatalogue' style='padding: 0; margin: 0; text-align: center; width: 100%;'>";
				echo "<p class='cataloguePageNumbers'>Page: ";
					for($i = 1; $i <= $total; $i++){
						if($i === $pageNumber){
							echo "&nbsp;&nbsp;<a><b>".$i."</b></a>";
						}else{
							echo "&nbsp;&nbsp;<a href='javascript:sortingFunction(".$i.")'>".$i." </a>";
						}
					}
				echo "</p>";
			echo "</div>";

			$catalogueStmt->close();
		}
		?>

	</div>

</div>

<br />
<br />

<div class="homepageImage">
	<img src="images/catalogueImages/catalogue2_600.jpg" srcset="images/catalogueImages/catalogue2_600.jpg 600w, images/catalogueImages/catalogue2_1000.jpg 1000w, images/catalogueImages/catalogue2_1400.jpg 1400w" sizes="(max-width: 550px) 100vw, 50vw" alt="big Picture 3">
</div>

<div class="homepageColourBar">
	All items marked by a ‘*’ are unique one-off's, otherwise we were able to make more than one item from the same piece. This means that the item you receive will have the same materials, finish and pattern but may have slight aesthetic differences such as grain pattern or knots.<br /><br />
	Please feel free to use the <a href='contact.php'>contact page</a> if you would like anything made to order, or if you have any queries at all, and we will be happy to help.

</div>

<div style='opacity: 0;'>
<a href='productPage.php'>a</a>
</div>


<?php
include "footer.php";
?>

<script>

function load(anchor){

window.location.hash = "#"+anchor;
// to top right away
if ( window.location.hash ) scroll(0,0);
// void some browsers issue
setTimeout( function() { scroll(0,0); }, 1);

var sel = 'img[id="' + anchor + '"]';
var currentOffset = $( sel ).offset().top;

  if(window.location.hash) {
	  

        $('html, body').animate({
            scrollTop: currentOffset + 'px'
        }, 500, 'swing');
    }  
};


</script>

</body>
</html>
