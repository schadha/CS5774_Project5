<?php

//download specified track
require_once '../global.php';
set_time_limit(60);
$file_name = $_GET['p'];
$file = "../uploads/".$file_name;

if (file_exists($file)) {
    header('Pragma: public');
    header('Expires: 0');
    header('Content-Description: File Transfer');
    header('Content-Disposition: attachment; filename='.basename($file));
    header('Cache-Control: public');
    header('Content-Length: ' . filesize($file));
	readfile($file);
    exit();
} else {
	die("File Not valid");
}

?>