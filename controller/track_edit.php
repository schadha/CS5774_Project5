<?php

require_once '../global.php';

//Edit the track to the new name
$uploads_dir = '../uploads';
$old_track_name = $_POST['old_track_name'];
$new_track_name = $_POST['track_name'];
$track_album = $_POST['track_album'];
$album_owner = $_POST['album_owner'];

$track_owner = Track::getTrackByName($old_track_name)['track_owner'];

$mod_old_track_name = str_replace(" ", "_", $old_track_name);
$mod_new_track_name = str_replace(" ", "_", $new_track_name);
$mod_track_album = str_replace(" ", "_", $track_album);

$old_track_path = $uploads_dir . "/" . $track_owner . "_" . $mod_track_album . "_" . $mod_old_track_name . ".mp3";
$new_track_path = $uploads_dir . "/" . $track_owner . "_" . $mod_track_album . "_" . $mod_new_track_name . ".mp3";

rename($old_track_path, $new_track_path);

// Check if track with that name exists. If not, then update the track name
$error = array();
if (Track::trackExist($new_track_name, $track_album, $track_owner, $album_owner)) {
	$error["Error"] = 'Track with that name exists!';
	echo json_encode($error);
	return;
} else {

	$properties = array(
		"track_name" => $old_track_name,
		"track_album" => $track_album,
		"track_owner" => $track_owner,
        "album_owner" => $album_owner
		);
	$track = new Track($properties);
	$track->save($new_track_name, $new_track_path);
	echo json_encode($error);
}

?>