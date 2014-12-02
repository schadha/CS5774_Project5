<?php

/* This file is the controller
 * for all comment related actions.
 */

$method = $_SERVER['QUERY_STRING'];

if (strcmp($method, "method=login") == 0) {
    login();
} else if (strcmp($method, "method=getAlbums") == 0) {
    getAlbums();
} else if (strcmp($method, "method=createAlbum") == 0) {
    createAlbum();
}  else if (strcmp($method, "method=getTracks") == 0) {
    getTracks();
} else if (strcmp($method, "method=getCollabs") == 0) {
    getCollabs();
} else if (strcmp($method, "method=collaborate") == 0) {
    collaborate();
} else if (strcmp($method, "method=uncollaborate") == 0) {
    uncollaborate();
}   else {
    echo 'Unkown method!';
}

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

function getAlbums() {
    require_once '../global.php';

    $user = $_POST['album_owner'];

    $albums = Album::getAlbums("album_owner", $user);
    echo json_encode($albums);
}

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
    
    //Create a new album
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

function getTracks() {
    require_once '../global.php';

    $album = $_POST['album_name'];
    $user = $_POST['album_owner'];

    $tracks = Track::getTracks($album, $user);
    echo json_encode($tracks);
}

function getCollabs() {
    require_once '../global.php';

    $user = $_POST['user'];

    // Get all the collaborators for the specified user.
    $collabs = Collaborator::getCollaborators($user, 0);

    echo json_encode($collabs);
}

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