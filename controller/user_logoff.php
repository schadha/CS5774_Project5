<?php

// Destros the sessiopn and resets it.
require_once '../global.php';
session_destroy();
$_SESSION = array();
header('Location: '.SERVER_PATH);