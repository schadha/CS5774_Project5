<?php

require_once '../global.php';

$album_owner = $_POST['album_owner'];
$album_name = urldecode($_POST['album_name']);
$currentCommenter = $_SESSION['username'];
$comment = $_POST['comment'];

// Add a comment to the database.
$id = Comment::addComment($album_name, $album_owner, $comment, $currentCommenter);

// Create an event related to the comment.
$eventProperties = [
    'event_type' => 'add_comment',
    'username' => $currentCommenter,
    'data' => $id,
    'album_name' => $album_name
];

$e = new Event($eventProperties);
$e->save();

?>