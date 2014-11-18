<?php
require_once '../global.php';

$pageName = 'Collaborators';

// Get all the collaborators for the specified user.
$collabs = Collaborator::getCollaborators($_SESSION['username'], 2);

require_once '../views/header.html';

require_once '../views/collabs.html';