<?php

include_once 'functions.php';

sec_session_start();

$productName = $_GET['name'];

if(trim($_SESSION['item']) == "" )
{
	$_SESSION['item'] = $productName;
}else{
	$_SESSION['item'] .= ",".$productName;
}



?>