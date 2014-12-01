<?php

/* This file is the controller
 * for all comment related actions.
 */

$method = $_SERVER['QUERY_STRING'];

if (strcmp($method, "method=delete") == 0) {
	deleteComment();
} else if (strcmp($method, "method=add") == 0) {
	addComment();
} else {
	echo 'Unkown method!';
}

function deleteComment() {
	require_once '../global.php';
	// delete the comment with the associated ID.
	$id = $_POST['id'];
	Comment::deleteComment(intval($id));
}

function addComment() {
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
}

?>