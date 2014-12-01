<?php

/* This file is the controller
 * for all comment related actions.
 */

$method = $_SERVER['QUERY_STRING'];

if (strcmp($method, "method=login") == 0) {
    login();
} else if (strcmp($method, "method=getAlbums") == 0) {
    getAlbums();
} else if (strcmp($method, "method=getCollabs") == 0) {
    getCollabs();
} else {
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
    } else {
        // does inputted password match password in db?
        if(User::validateUser($username, $password) == false) {
            echo "Invalid Password";
            exit();
        } else {
            $_SESSION['username'] = $username;
            exit();
        }
    }
}

function getAlbums() {
    require_once '../global.php';

    $user = $_POST['album_owner'];

    $albums = Album::getAlbums("album_owner", $user);
    echo json_encode($albums);
}

function getCollabs() {
    require_once '../global.php';

    $user = $_POST['user'];

    // Get all the collaborators for the specified user.
    $collabs = Collaborator::getCollaborators($user, 0);

    echo json_encode($collabs);
}

?>