<?php

require_once '../global.php';
$uploads_dir = '../uploads';

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

?>