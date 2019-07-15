<?php

include_once 'db_connect.php';
include_once 'functions.php';
 
sec_session_start(); // Our custom secure way of starting a PHP session.

if (isset($_POST['fName'])) {
	
    $username = $_POST['fName'];
    $password = $_POST['p']; // The hashed password.

    if (login($username, $password, $mysqli) == true) {
        // Login success 
        header('Location: ../catalogue.php?error=0');
    } else {
		echo "FAIL";
        // Login failed 
        header('Location: ../index.php?error=1');
    }
} else {
    // The correct POST variables were not sent to this page. 
    echo 'Invalid Request';
}