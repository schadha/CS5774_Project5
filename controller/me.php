<?php
require_once '../global.php';

//Returns information about logged in user
if ($_SESSION) {
	if ($curUser = User::publicUserInfo("username", $_SESSION['username'])) 
	{
		 echo json_encode($curUser);
	}
}
?>