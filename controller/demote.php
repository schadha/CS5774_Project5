<?php
require_once '../global.php';

// Remove moderator privelages for a user.
$demoteUser = $_POST['demote'];
User::demoteUser($demoteUser);
?>