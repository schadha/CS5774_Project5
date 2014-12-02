<?php

/**
 * This file is the controller for all extension related actions.
 * Slightly different methods because our main website's $_SESSION is not being used.
 */

$method = $_SERVER['QUERY_STRING']; //Get the method query string


//If else tree for deciding what method to call based on the query
if (strcmp($method, "method=login") == 0) {
    login();
} else if (strcmp($method, "method=getAlbums") == 0) {
    getAlbums();
} else if (strcmp($method, "method=createAlbum") == 0) {
    createAlbum();
}  else if (strcmp($method, "method=getTracks") == 0) {
    getTracks();
} else if (strcmp($method, "method=createTrack") == 0) {
    createTrack();
} else if (strcmp($method, "method=getCollabs") == 0) {
    getCollabs();
} else if (strcmp($method, "method=collaborate") == 0) {
    collaborate();
} else if (strcmp($method, "method=uncollaborate") == 0) {
    uncollaborate();
}   else {
    echo 'Unkown method!';
}

//Login the user. Differs from main website login because session is separate
function login() {
    require_once '../global.php';

    $username = $_POST['username'];
    $password = $_POST['password'];

    // get user with this username from database
    $user = User::userExists("username", $username);

    if($user == null) {
        echo "Username doesn't exist.";
        exit();
    } else if(User::validateUser($username, $password) == false) { // does inputted password match password in db?
        echo "Invalid Password";
        exit();
    }

}

//Fetch albums for the user
function getAlbums() {
    require_once '../global.php';

    $user = $_POST['album_owner'];

    $albums = Album::getAlbums("album_owner", $user);
    echo json_encode($albums);
}

//Create an album for the user
function createAlbum() {
    require_once '../global.php';

    $properties = $_POST;
    $error = array();

    if (!file_exists("$uploads_dir")) {
        mkdir("$uploads_dir", 0777, true);
    }

    //Album artwork handling
    $artwork_path = $uploads_dir . "/" . $properties['username'] . "_" . $properties['album_name'] . ".jpg";

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
    
    $properties['album_owner'] = $properties['username'];
    
    //Create a new album event
    if (Album::albumExist($properties['album_name'], $properties['username']) != null) {
        $error["Error"] = "Album with that name already exists for you!";
    } else {
        $properties['album_image'] = $artwork_path;
        $newAlbum = new Album($properties);
        $newAlbum->save();

        $eventProperties = [
            'event_type' => 'add_album',
            'username' => $properties['username'],
            'data' => $properties['album_name'],
            'album_name' => $properties['album_name']
        ];
        $e = new Event($eventProperties);
        $e->save();
    }
    echo json_encode($error);
}

//Fetch the tracks for an album
function getTracks() {
    require_once '../global.php';

    $album = $_POST['album_name'];
    $user = $_POST['album_owner'];

    $tracks = Track::getTracks($album, $user);
    echo json_encode($tracks);
}

//Create a new track for a given album
function createTrack() {
	require_once '../global.php';
	//Tracks are stored in uploads file, store path in database
	$track_name = $_POST['track_name'];
	$track_album = $_POST['track_album'];
	$album_owner = $_POST['album_owner'];
	$track_owner = $_POST['album_owner'];

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

//Get the collaboration requests for a user
function getCollabs() {
    require_once '../global.php';

    $user = $_POST['user'];

    // Get all the collaborators for the specified user.
    $collabs = Collaborator::getCollaborators($user, 0);

    echo json_encode($collabs);
}

// Send a collaboration acceptance
function collaborate() {
    require_once '../global.php';

    $user = $_POST['username'];
    $collabWith = $_POST['collaborator'];

    // Send a collaboration request to $collabWith
    $result = Collaborator::collab_request($user, $collabWith);

    if ($result == 1) {
        // Save both sides of the collaboration as an event (bidirectional)
        $eventProperties = [
            'event_type' => 'add_collaborator1',
            'username' => $collabWith,
            'data' => $user
        ];

        $e = new Event($eventProperties);
        $e->save();

        $eventProperties = [
            'event_type' => 'add_collaborator2',
            'username' => $user,
            'data' => $collabWith
        ];

        $e = new Event($eventProperties);
        $e->save();
    }
}

// Send a collaboration denial
function uncollaborate() {
    require_once '../global.php';

    $user = $_POST['username'];
    $cancel_request = $_POST['collaborator'];

    // Remove the user as a collaborator
    Collaborator::removeCollaborator($user, $cancel_request);

    // Remove the events associated with collaboration A->B and B->A
    Event::deleteEvent($user, 'add_collaborator1', $cancel_request);
    Event::deleteEvent($user, 'add_collaborator2', $cancel_request);

    Event::deleteEvent($cancel_request, 'add_collaborator1', $user);
    Event::deleteEvent($cancel_request, 'add_collaborator2', $user);
}

?>