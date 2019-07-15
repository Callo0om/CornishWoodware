<?php

include_once 'functions.php';

sec_session_start();

$productName = $_GET['name'];

if(isset($_SESSION['item'])){
	
	$itemNames = (explode(',',$_SESSION['item']));
	$found = false;
	$newItemNames = '';
	
	foreach($itemNames as $item){
		if($item === $productName && $found === false){
			$found = true;
		}else{
			$newItemNames .= $item . ',';
		}
	}
	
	$_SESSION['item'] = $newItemNames;	
	$_SESSION['item'] = substr($_SESSION['item'], 0, -1);

	if(trim($_SESSION['item']) == "" )
	{
		unset($_SESSION['item']);	
	}
	
	$_SESSION['item'] = str_replace(",,",",",$_SESSION['item']);
	$_SESSION['item'] = str_replace(",,,",",",$_SESSION['item']);
	$_SESSION['item'] = trim($_SESSION['item'],",");
	
	
}

?>