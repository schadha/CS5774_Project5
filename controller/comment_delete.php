<?php

require_once '../global.php';

// delete the comment with the associated ID.
$id = $_POST['id'];
Comment::deleteComment(intval($id));

?>