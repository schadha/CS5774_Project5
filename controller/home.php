<?php
require_once '../global.php';

// choose template depending on whether logged in or not
if(isset($_SESSION['username'])) {
	$user = $_GET['u'];

	// if user exists then show their profile, else 404
	if (User::userExists("username", $user)) {
		$pageName = 'CollabTunes - '.$user;
        
		$albums = Album::getAlbums("album_owner", $user);
		$curUser = User::publicUserInfo("username", $user);
        
		$displayButton = "add";
		$result = Collaborator::isCollaborator($_SESSION['username'], $user);
		$sentBy = $result['sent_by'];
		$isCollab = $result['status'];
		if ($isCollab != null) {
			if ($isCollab == 0 && strcmp($sentBy, $_SESSION['username']) == 0) {
				$displayButton = "sent";
			} else if ($isCollab == 0) {
				$displayButton = "waiting";
			}else {
				$displayButton = "accepted";
			}
		}

		require_once '../views/header.html';
    	require_once '../views/profile.html';
	} else {
		$pageName = 'CollabTunes - 404';
		require_once '../views/header.html';
		require_once '../views/404.html';
	}
}
else {
	$pageName = 'CollabTunes';
	require_once '../views/header.html';
    require_once '../views/index.html';
}