<?php
require_once '../global.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['community'])) {
	$twitterNames = array();
	$tweets = array();

	if ($curUser = User::publicUserInfo("username", $_SESSION['username'])) {
        $collabUser = new User($curUser);
        $twitterNames = json_decode($collabUser->getTwitterNamesForCollabs());
    }

	if (sizeof($twitterNames) > 0) {
		require_once './twitteroauth.php';
		$connection = new TwitterOAuth("mJY9r8kDCUtMJUNiGwfZ2gLxK", "oLzWiKDik54rc1rPYgY2eBmxc0l15U3H6JK0xkBxIlIxp8QTiY", "911592974-xPcUDBEkeBV79vAhTYeVCXXAwnOQSjZhN2ayvwKs", "HGcHF1HyGLZLPJR3gmjMS70jGitmh5UPpdJ3VqCq0cXVN");

		for ($i = 0; $i < sizeof($twitterNames); $i++) {
			if ($twitterNames[$i] != null && strlen($twitterNames[$i]) > 0) {
				$content = $connection->get('https://api.twitter.com/1.1/statuses/user_timeline.json?screen_name=' . $twitterNames[$i] . '&count=1');
				array_push($tweets, $content[0]->text . ' ' . $content[0]->created_at);
			}
		}
	}

	echo json_encode($tweets);
}  else if ($_SERVER['REQUEST_METHOD'] === 'POST') {
	$twitterName = "";
	$tweets = array();

	if ($curUser = User::publicUserInfo("username", $_SESSION['username'])) {
        $twitterName = $curUser['twitter'];
    }

	if ($twitterName != null && strlen($twitterName) > 0) {
	 	require_once './twitteroauth.php';

	    $connection = new TwitterOAuth("mJY9r8kDCUtMJUNiGwfZ2gLxK", "oLzWiKDik54rc1rPYgY2eBmxc0l15U3H6JK0xkBxIlIxp8QTiY", "911592974-xPcUDBEkeBV79vAhTYeVCXXAwnOQSjZhN2ayvwKs", "HGcHF1HyGLZLPJR3gmjMS70jGitmh5UPpdJ3VqCq0cXVN");
	    $content = $connection->get('https://api.twitter.com/1.1/statuses/user_timeline.json?screen_name=' . $twitterName);

	    for ($i = 0; $i < sizeof($content); $i++) {
	        array_push($tweets, $content[$i]->text . ' ' . $content[$i]->created_at);
	    } 
	}

	echo json_encode($tweets);
}

?>