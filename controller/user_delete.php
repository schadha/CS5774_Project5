<?php

require_once '../global.php';
$uploads_dir = '../uploads';

$delete = $_POST['delete'];
$admin = $_POST['admin'];
//Gets all the albums associated with the user
$albums = Album::getAlbums("album_owner", $delete);

//Deletes the user's albums, files and artwork from the server
foreach ($albums as $a) {
	Album::deleteAlbum($a["album_name"], $delete);
	$artwork_path = $uploads_dir . "/" . $delete . "_" . $a['album_name'] . ".jpg";
	unlink($artwork_path);
}

//Deletes the user from the database
$curUser = User::deleteUser("username", $delete);

//Logs off user on success
if ($curUser == null) {
	$_SESSION['error'] = "Error in deleteing user!";
} else {
	if ($admin == 0) {
		require_once './user_logoff.php';
	}
}

?>