<?php
require_once '../global.php';

$curUser = User::publicUserInfo("username", $_SESSION['username']);
$oldFavoriteGenre = $curUser['favorite_genre'];
$newFavoriteGenre = $_POST['favorite_genre'];

//Edit the user information
if (User::editUser($_SESSION['username'], $_POST)) {
	$_SESSION['success'] = "Updated Profile!";
    
    
    if ($oldFavoriteGenre != $newFavoriteGenre) {
        $eventProperties = [
            'event_type' => 'change_genre',
            'username' => $curUser['username'],
            'data' => "$oldFavoriteGenre,$newFavoriteGenre"
        ];
        
        $e = new Event($eventProperties);
        $e->save();
    }
}

?>