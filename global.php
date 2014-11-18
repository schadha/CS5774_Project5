<?php
set_include_path(dirname(__FILE__));

require_once 'config.php';

require_once 'views/viewHelpers.php';

// Helper method to activate the nav bar tabs based on whatever is clicked.
function echoActiveClassIfRequestMatches($requestUri)
{
    $current_file_name = basename($_SERVER['REQUEST_URI'], ".php");

    if ($current_file_name == $requestUri)
        echo 'class="active"';
}

//Auto loads all the model classes.
function __autoload($class_name) {
	require_once 'model/'.$class_name.'.php';
}