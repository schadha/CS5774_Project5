<?php
require_once '../global.php';

if (User::userExists("email", $_POST['email'])) {
	echo mail($_POST['email'], "CollabTunes Password Reset", "Password is: ", "From: dsingh5270@gmail.com");
}

?>