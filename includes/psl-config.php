<?php
/**
 * These are the database login details
 */
$debug = FALSE; 
if($debug === TRUE){
	define("HOST", "localhost");
	define("USER", "root");
	define("PASSWORD", "");
	define("DATABASE", "data");
}else{
	define("HOST", "db768743597.hosting-data.io");
	define("USER", "dbo768743597");
	define("PASSWORD", "Shaddow1@");
	define("DATABASE", "db768743597");
}



define("SECURE", FALSE);    // FOR DEVELOPMENT ONLY!!!! CHANGE TO TRUE WHEN ON SSL

?>