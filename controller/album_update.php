<?php
require_once '../global.php';
$uploads_dir = '../uploads';

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

?>