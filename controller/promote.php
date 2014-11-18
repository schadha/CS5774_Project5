<?php
require_once '../global.php';

// Add moderator privelages to the user.
$promoteUser = $_POST['promote'];
User::promoteUser($promoteUser);

?>