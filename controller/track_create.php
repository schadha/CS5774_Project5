<?php

require_once '../global.php';

//Tracks are stored in uploads file, store path in database
$uploads_dir = '../uploads';
$track_name = $_POST['track_name'];
$track_album = $_POST['track_album'];
$album_owner = $_POST['album_owner'];
$track_owner = $_SESSION['username'];

$error = array();

if (!file_exists("$uploads_dir")) {
	mkdir("$uploads_dir", 0777, true);
}

$mod_track_name = str_replace(" ", "_", $track_name);
$mod_track_album = str_replace(" ", "_", $track_album);

$track_path = $uploads_dir . "/" . $track_owner . "_" . $mod_track_album . "_" . $mod_track_name . ".mp3";

$path_parts = pathinfo($_FILES["track_data"]["name"]);
$extension = $path_parts['extension'];

// Check extension of uploaded track. Must be an mp3/m4a file.
if ($extension == "mp3" || $extension == "m4a") {
	move_uploaded_file($_FILES['track_data']['tmp_name'], "$track_path");
} else {
	$error["Error"] = 'Upload only mp3/m4a files!';
	echo json_encode($error);
	return;
}

if (Track::trackExist($track_name, $track_album, $album_owner, $track_owner, $album_owner) != null) {
	$error["Error"] = 'Track with that name already exists for you under this album!';
	echo json_encode($error);
	return;
} else {
	$properties = array(
		"track_name" => $track_name,
		"track_album" => $track_album,
		"track_owner" => $track_owner,
		"track_path" => $track_path,
        "album_owner" => $album_owner
		);
	$newTrack = new Track($properties);
	$newTrack->save();
    
    $eventProperties = [
        'event_type' => 'add_track',
        'username' => $track_owner,
        'data' => $track_name,
        'album_name' => "$track_album,$album_owner"
    ];
    $e = new Event($eventProperties);
    $e->save();
    
	echo json_encode($error);
}

?>