<?php
require_once '../global.php';

// choose template depending on whether logged in or not
if(isset($_SESSION['username'])) {
	$uri = explode("/", $_SERVER['REQUEST_URI']);
	$album =  urldecode($uri[sizeof($uri)-1]);

	// If album exists, then show its page, if not then 404
	if (($curAlbum = Album::albumExist($album, $uri[sizeof($uri)-2]))) {
		
		$curAlbum =  Album::getInfoByAlbum($curAlbum);
        $comments = Comment::getComments($curAlbum['album_name'], $curAlbum['album_owner']); //Get the album's comments
		$pageName = 'CollabTunes - ' . $uri[sizeof($uri)-3];
		$tracks = Track::getTracks($album, $uri[sizeof($uri)-2]);
		require_once '../views/header.html';
		require_once '../views/album.html';
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