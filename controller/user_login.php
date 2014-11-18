<?php

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