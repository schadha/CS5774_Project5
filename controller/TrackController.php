<?php

/* This file is the controller
* for all track related actions.
*/

$method = $_SERVER['QUERY_STRING'];

if (strcmp($method, "method=create") == 0) {
	trackCreate();
} else if (strcmp($method, "method=delete") == 0) {
	trackDelete();
} else if (strcmp($method, "method=edit") == 0) {
	trackEdit();
} else if (strstr($method, "method=download") != false) {
	trackDownload();
} else {
	echo 'Unkown method!';
}

function trackCreate() {
	require_once '../global.php';
	//Tracks are stored in uploads file, store path in database
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
}

function trackDelete() {
	require_once '../global.php';
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
}

function trackDownload() {
	require_once '../global.php';
	//download specified track
	set_time_limit(60);
	$file_name = $_GET['p'];
	$file = "../uploads/".$file_name;

	if (file_exists($file)) {
	    header('Pragma: public');
	    header('Expires: 0');
	    header('Content-Description: File Transfer');
	    header('Content-Disposition: attachment; filename='.basename($file));
	    header('Cache-Control: public');
	    header('Content-Length: ' . filesize($file));
		readfile($file);
	    exit();
	} else {
		die("File Not valid");
	}
}

function trackEdit() {
	require_once '../global.php';
	//Edit the track to the new name
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
}


?>