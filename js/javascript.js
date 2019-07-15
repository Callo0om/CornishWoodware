
function removeItem(auth, name){
	if(auth === "admin"){
		if (confirm("Are you sure you wish to remove " + name)) {
			$.post('removeProduct.php', {'name' : name}, function(response){
				
				alert(response);
				
			});
		} else {

		}	
	}else{
		$.post('removeProduct.php', {'name' : name}, function(response){

		});
	}
	

}

function setProductPicture(src){
	
	var newImgSmall = src.substr(0, src.lastIndexOf("/")) + "/_600/" + src.substring(src.lastIndexOf('/')+1);
	var newImgMed = src.substr(0, src.lastIndexOf("/")) + "/_1000/" + src.substring(src.lastIndexOf('/')+1);
	var newImgBig = src.substr(0, src.lastIndexOf("/")) + "/_1400/" + src.substring(src.lastIndexOf('/')+1);

	document.getElementById('productMainImageContainerImages').innerHTML = "<img src='"+newImgSmall+"' srcset='"+newImgSmall+" 600w,"+newImgMed+" 1000w,"+newImgBig+" 1400w' alt='Main Image Buffer (for crossfade effect)' class='productMainImage' id='productMainImage' />";

}

function enlargeImageOpen(){
	
	var oldSrc = document.getElementById("productMainImage").src;
	
	var temp = oldSrc.substr(0, document.getElementById("productMainImage").src.lastIndexOf("/")).split('/');
    temp.pop();
    var oldDir = temp.join('/');

	var oldImgSmall = oldDir + "/_600/" + oldSrc.substring(oldSrc.lastIndexOf('/')+1);
	var oldImgMed = oldDir + "/_1000/" + oldSrc.substring(oldSrc.lastIndexOf('/')+1);
	var oldImgBig = oldDir + "/_1400/" + oldSrc.substring(oldSrc.lastIndexOf('/')+1);
	
	document.getElementById('enlargeImage').innerHTML = "<img src='"+oldImgSmall+"' srcset='"+oldImgSmall+" 600w,"+oldImgMed+" 1000w,"+oldImgBig+" 1400w' id='enlargeImageImg' class='enlargeImageImg'><p> Click anywhere to return </p>";
	
	document.getElementById("enlargeImage").style.opacity = "1";
	document.getElementById("enlargeImage").style.visibility = "visible";
}

function enlargeImageClose(){
	document.getElementById("enlargeImage").style.opacity = "0";
	document.getElementById("enlargeImage").style.visibility = "hidden";
}

function openAllergens(allergen){
	overlay = createDiv("100%", "100%", "0", "0", document.body, "allergenInfoOverlay");
	overlay.style.backgroundColor = "white";
	overlay.style.border = "0";
	overlay.onclick = function(){document.body.removeChild(overlay);};
	back = createDiv("50vw", "25vw", "25vw", "5vw", document.getElementById("allergenInfoOverlay"), "allergenInfoBack");
	title = createDiv("40vw", "5vw", "5vw", "1vw", document.getElementById("allergenInfoBack"), "allergenInfoTitle");
	title.innerHTML = "<h1 style=\"font-size: 2.5vw; line-height: 5vw;\">Allergen Information</h1>";
	information = createDiv("40vw", "15vw", "5vw", "7vw", document.getElementById("allergenInfoBack"), "allergenInfoInfo");
	
	if(allergen == "Tung Oil"){
		info = "Tung oil is made by pressing the nut or seed of the Tung tree(Vernicia fordii). It has been around for thousands of years and was originally used in China. It is a durable and waterproof finish.<br /> There is some debate as to whether Tung oil can cause an allergic reaction, but if you have an allergy to nuts or seeds then I recommend choosing a different product, just in case.";
	}
	else{
		info = "Error";
	}
	information.innerHTML = "<div style=\"font-size: 1.5vw; width: 36vw; font-size: 1.2vw; font-family: Timeless, arial; padding: 2vw;\">"+info+"</div>";
	closeText = createDiv("50vw", "5vw", "25vw", "31vw", document.getElementById("allergenInfoOverlay"), "allergenInfoBack");
	closeText.innerHTML = "<div style=\"font-size: 2.5vw; line-height: 5vw; text-align: center; width: 100%; height: 100%; font-family: FoglihtenNo07, arial;\">Click anywhere to close.</div>"
}

function createDiv(width, height, left, top, parentElement, id){
	var newDiv = document.createElement("div");
	newDiv.id = id;
	newDiv.style.backgroundColor = '#808080';
	newDiv.style.border = '0.1vw solid black';
	newDiv.style.position = "absolute";
	newDiv.style.zIndex = "101";
	newDiv.style.width = width;
	newDiv.style.height = height;
	newDiv.style.left = left;
	newDiv.style.top = top;
	newDiv.style.fontSize = "2vw";
	parentElement.appendChild(newDiv);
	return newDiv;
}

function addToCart(productName, quantitySelected, quantityRemaining){
	if(parseInt(quantitySelected) < parseInt(quantityRemaining))
	{
		var xmlhttp = new XMLHttpRequest();
		xmlhttp.onreadystatechange = function() {
			if (this.readyState == 4 && this.status == 200) {
				location.reload();
			}
		};
		xmlhttp.open("GET", "includes/addToSession.php?name=" + productName, true);
		xmlhttp.send();
	}
}

function removeFromCart(productName){
	
	var xmlhttp = new XMLHttpRequest();
    xmlhttp.onreadystatechange = function() {
        if (this.readyState == 4 && this.status == 200) {
            location.reload();
        }
    };
    xmlhttp.open("GET", "includes/removeFromSession.php?name=" + productName, true);
    xmlhttp.send();
	
}

function setDropDown(selectElement, buttonElement, listField){
	
	if(selectElement.value !== ""){
		var string = selectElement.value;
		var substring = "*";
		if(string.indexOf(substring) !== -1){
			
			listField.value = listField.value
			var newValue = selectElement.value.replace("* ","");
			listField.value = listField.value.replace(selectElement.options[selectElement.selectedIndex].text.replace("* ", "") + ",","");
			
			selectElement.options[selectElement.selectedIndex].style.backgroundColor = "#c4dbf6";
			selectElement.options[selectElement.selectedIndex].text = newValue;
			buttonElement.value = "Add";
		}else{
			listField.value += selectElement.options[selectElement.selectedIndex].text + ",";
			selectElement.options[selectElement.selectedIndex].style.backgroundColor = "#00d20f";
			selectElement.options[selectElement.selectedIndex].text = "* " + selectElement.value;
			buttonElement.value = "Remove";
		}
	}
}

function dropDownSelection(selectElement, buttonElement){
	var string = selectElement.value;
	var substring = "*";
	if(string.indexOf(substring) !== -1){
		buttonElement.value = "Remove";
	}else{
		buttonElement.value = "Add";
	}
}

var scrolling = false;
var scrollingID;

document.onmouseup = function(){
	scrolling = false;
	
}

document.onmousemove = function(){
	if(scrolling === true){
		document.getElementById(scrollingID).top = '100px';
	}
}

function scrollBarDown(scrollBar, scrollDiv){
	scrollingID = scrollBar.id;
	scrolling = true;
	
}

function sortingFunction(pageNum){
	if(!pageNum){
		pageNum = 1;
	}
	var sortBy = document.getElementById('sortBy').options[document.getElementById('sortBy').selectedIndex].value;
	var filterBy = document.getElementById('filterBy').options[document.getElementById('filterBy').selectedIndex].value;
	var showResults = document.getElementById('showResults').options[document.getElementById('showResults').selectedIndex].value;
	
	window.location.replace('catalogue.php?sortBy='+sortBy+'&filterBy='+filterBy+'&showResults='+showResults+'&pageNumber='+pageNum);
}

function openMenu(x) {
  x.classList.toggle("change");
  document.getElementById('navBar').classList.toggle("change");

}

function addbundleItem(itemlist, quantity){

	var container = document.getElementById('bundleItemContainer');
	
	var selectionBoxes = '';
	for(var i = 0; i<quantity; i++){
		selectionBoxes += "<label for='item'"+i+">Item "+(i+1)+": &nbsp;&nbsp;</label><select name='item"+i+"'>";
		
		for(var j=0; j<itemlist.length; j++){
			selectionBoxes += "<option value='"+itemlist[j]+"'>"+itemlist[j]+"</option>";
		}
		
		selectionBoxes += "</select>";
	}
	
	container.innerHTML = selectionBoxes;
	
	
}

var itemWidth;
var itemHeight;
var itemType;

function sizeScaleInit(width, height, type){
	if(width > 0 && height > 0){
		itemWidth = width;
		itemHeight = height;
		itemType = type;

		var readyStateCheckInterval = setInterval(function() {
			if (document.readyState === "complete") {
				clearInterval(readyStateCheckInterval);
				document.getElementById('sizeTextBackup').style.display = "none";
				document.getElementById('choppingBoardImgDiv').style.display = "block";
				document.getElementById('choppingBoardScale').style.display = "block";
				if(itemType == "Chopping Boards"){
					document.getElementById('bread').style.display = "block";
					document.getElementById('choppingBoardImgSlotDiv').style.display = "block";
				}
				sizeScale()
			}
		}, 10);
	}
}



function sizeScale(){
		
		var imageWidthPerCm = ((parseInt(window.getComputedStyle(document.getElementById('choppingBoardScale')).width) / 100) * 16.48148148148148) / 10;
		var imageHeightPerCm = ((parseInt(window.getComputedStyle(document.getElementById('choppingBoardScale')).height) / 100) * 20.47058823529412) / 10;
			
		document.getElementById('choppingBoardImgDiv').style.width = (imageWidthPerCm * itemWidth) + "px";
		document.getElementById('choppingBoardImgDiv').style.height = (imageHeightPerCm * itemHeight) + "px";
		
		var leftOffset = ((parseInt(window.getComputedStyle(document.getElementById('choppingBoardScale')).width) / 100) * 13.88888888888889);
		var topOffset = ((parseInt(window.getComputedStyle(document.getElementById('choppingBoardScale')).height) / 100) * 14.35294117647059);
		
		document.getElementById('choppingBoardImgDiv').style.left = parseInt(document.getElementById('choppingBoardScale').getBoundingClientRect().left + leftOffset) + "px";
		document.getElementById('choppingBoardImgDiv').style.top = parseInt((document.getElementById('choppingBoardScale').getBoundingClientRect().top + window.pageYOffset) + topOffset)+ "px";

		if(itemType == "Chopping Boards"){
			
			document.getElementById('choppingBoardImgSlotDiv').style.width = (imageWidthPerCm * 2) + "px";
			document.getElementById('choppingBoardImgSlotDiv').style.height = (imageHeightPerCm * (itemHeight / 2)) + "px";
			document.getElementById('choppingBoardImgSlotDiv').style.left = (imageWidthPerCm * 2) + "px";
			document.getElementById('choppingBoardImgSlotDiv').style.top = ((imageHeightPerCm * itemHeight) / 2) - ((imageHeightPerCm * (itemHeight / 2)) / 2) + "px";
		
			document.getElementById('bread').style.width = (imageWidthPerCm * 26) + "px";
			document.getElementById('bread').style.height = (imageHeightPerCm * 15) + "px";
			
			document.getElementById('bread').style.left = parseInt(document.getElementById('choppingBoardScale').getBoundingClientRect().left + leftOffset) + (imageWidthPerCm * 5) + "px";
			
			if(itemWidth > 31 && itemHeight > 15){
				document.getElementById('bread').style.top = (parseInt((document.getElementById('choppingBoardScale').getBoundingClientRect().top + window.pageYOffset) + topOffset)) + (imageHeightPerCm * 4) + "px";
			}else{
				document.getElementById('bread').style.top = (parseInt((document.getElementById('choppingBoardScale').getBoundingClientRect().top + window.pageYOffset) + topOffset)) + (imageHeightPerCm * itemHeight) + (imageHeightPerCm * 2) + "px";
			}
			
		}

}

var sizeOpen = false, materialsOpen = false;

function popUpText(textvalue){
	
	var sizeString = "", materialString = "";
	
	if(textvalue == 'size'){
		if(sizeOpen == false){
			sizeString = "Please note: All sizes are approximate. If you need an exact size let us know on the <a href='contact.php'>Contact Page</a>.<br />All Sizes are to the longest/widest point.<br />Image overlay is for reference only.<br /><br />";
			sizeOpen = true;
			materialString = '';
			materialsOpen = false;
		}else{
			sizeString = '';
			sizeOpen = false;
		}
		
	}else if(textvalue == 'materials'){
		if(materialsOpen == false){
			materialString = "Please note: We cannot guarantee correct identification of wood types. But all pieces are thoroughly checked to ensure quality.<br /><br />";
			materialsOpen = true;
			sizeString = '';
			sizeOpen = false;
		}else{
			materialString = '';
			materialsOpen = false;
		}
		
	}
	
	document.getElementById('sizePopUpDiv').innerHTML = sizeString;
	document.getElementById('materialsPopUpDiv').innerHTML = materialString;
	sizeScale();
}













