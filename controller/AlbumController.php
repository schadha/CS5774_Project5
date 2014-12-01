<?php
/* This file is the controller for all album 
 * related actions.
 */

$method = $_SERVER['QUERY_STRING'];

if (strcmp($method, "method=delete") == 0) {
	deleteAlbum();
} else if (strcmp($method, "method=create") == 0) {
	createAlbum();
} else if (strcmp($method, "method=get") == 0) {
	getAlbum();
} else if (strcmp($method, "method=update") == 0) {
	updateAlbum();
} else if (strcmp($method, "method=featured") == 0) {
	getFeaturedAlbums();
} else {
	echo 'Unkown method!';
}

function getFeaturedAlbums() {
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
}

function createAlbum() {
	require_once '../global.php';

	$properties = $_POST;
	$error = array();

	if (!file_exists("$uploads_dir")) {
		mkdir("$uploads_dir", 0777, true);
	}

	//Album artwork handling
	$artwork_path = $uploads_dir . "/" . $_SESSION['username'] . "_" . $properties['album_name'] . ".jpg";

	$path_parts = pathinfo($_FILES["album_image"]["name"]);
	$extension = $path_parts['extension'];

	// Check extension of uploaded image. Must be an jpg/png file.
	if ($extension == "jpg" || $extension == "png") {
		move_uploaded_file($_FILES['album_image']['tmp_name'], "$artwork_path");
	} else {
		$error["Error"] = 'Upload only jpg/png files!';
		echo json_encode($error);
		return;
	}

	$properties['album_owner'] = $_SESSION['username'];

	//Create a new album
	if (Album::albumExist($properties['album_name'], $_SESSION['username']) != null) {
		$error["Error"] = "Album with that name already exists for you!";
	} else {
		$properties['album_image'] = $artwork_path;
		$newAlbum = new Album($properties);
		$newAlbum->save();
	    
	    $eventProperties = [
	        'event_type' => 'add_album',
	        'username' => $properties['album_owner'],
	        'data' => $properties['album_name'],
	        'album_name' => $properties['album_name']
	    ];
	    $e = new Event($eventProperties);
	    $e->save();
	    
		$_SESSION['success'] = "Album Created!";
	}
	echo json_encode($error);
}

function deleteAlbum() {
	require_once '../global.php';
	$artwork_path = $uploads_dir . "/" . $_SESSION['username'] . "_" . $_POST['album_name'] . ".jpg";
	//Delete the album
	if (Album::deleteAlbum($_POST['album_name'], $_POST['album_owner']) !== null) {
		$_SESSION['success'] = "Album deleted";
		unlink($artwork_path);
	    
	} else {
		echo "Album not found!";
	}
}

function getAlbum() {
	require_once '../global.php';
	//Return information about a specified album
	if ($_SESSION) {
		if ($curAlbum = Album::publicAlbumInfo($_POST['album_name'], $_POST['album_owner'])) 
		{
			 echo json_encode($curAlbum);
		}
	}
}

function updateAlbum() {
	require_once '../global.php';

	//Updates information about an album
	$properties = $_POST;

	$error = array();
	$artwork_path = $uploads_dir . "/" . $_SESSION['username'] . "_" . $_POST['album_name'] . ".jpg";

	// Check if a file has been uploaded
	if ($_FILES['album_image']['size'] > 0) {
		$path_parts = pathinfo($_FILES["album_image"]["name"]);
		$extension = $path_parts['extension'];

		// Check file extension so only an jpg/png file can be uploaded.
		if ($extension == "jpg" || $extension == "png") {
			move_uploaded_file($_FILES['album_image']['tmp_name'], "$artwork_path");
		} else {
			$error["Error"] = 'Upload only jpg/png files!';
			echo json_encode($error);
			return;
		}
	}

	// Update the album.
	$properties["album_owner"] = $_SESSION['username'];
	$properties["album_image"] = $artwork_path;

	$updatedAlbum = new Album($properties);
	$updatedAlbum->save();
	$_SESSION['success'] = "Album updated!";
	echo json_encode($error);
}

?>