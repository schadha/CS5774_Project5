<?php
require_once '../global.php';

//delete path from uploads file and also from database
$uploads_dir = '../uploads';
$track_name = $_POST['track_name'];
$track_album = $_POST['track_album'];
$album_owner = $_POST['album_owner'];

$track_owner = Track::getTrackByName($track_name)['track_owner'];

if (!file_exists("$uploads_dir")) {
	mkdir("$uploads_dir", 0777, true);
}

$mod_track_name = str_replace(" ", "_", $track_name);
$mod_track_album = str_replace(" ", "_", $track_album);

$track_path = $uploads_dir . "/" . $track_owner . "_" . $mod_track_album . "_" . $mod_track_name . ".mp3";
unlink($track_path);

$result = array();
if (!Track::deleteTrack($track_name, $track_album, $track_owner, $album_owner)) {
	$result["Error"] = "Track doesn't exist!";
	echo json_encode($result);
	return;
} else {
	$_SESSION['success'] = "Track deleted!";
	echo json_encode($result);
}

?>