<?php

/* This file is the controller
 * for all comment related actions.
 */

$method = $_SERVER['QUERY_STRING'];

if (strcmp($method, "method=login") == 0) {
    login();
} else if (strcmp($method, "method=getAlbums") == 0) {
    getAlbums();
} else if (strcmp($method, "method=getTracks") == 0) {
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