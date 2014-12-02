<?php

/* This file is the controller for 
* all user related actions.
*/
$method = $_SERVER['QUERY_STRING'];

if (strcmp($method, "method=delete") == 0) {
    userDelete();
} else if (strcmp($method, "method=login") == 0) {
    userLogin();
} else if (strcmp($method, "method=logoff") == 0) {
    userLogoff();
} else if (strcmp($method, "method=register") == 0) {
    userRegister();
} else if (strcmp($method, "method=update") == 0) {
    userUpdate();
} else if (strcmp($method, "method=whoami") == 0) {
    whoAmI();
} else if (strcmp($method, "method=collaborate") == 0) {
    collaborate();
} else if (strcmp($method, "method=uncollaborate") == 0) {
    uncollaborate();
} else if (strcmp($method, "method=promote") == 0) {
    promote();
} else if (strcmp($method, "method=demote") == 0) {
    demote();
} else if (strcmp($method, "method=collabs") == 0) {
    collabs();
} else if (strcmp($method, "method=home") == 0) {
    home();
} else if (strstr($method, "method=home") != false) {
    home();
} else if (strcmp($method, "method=community") == 0) {
    community();
} else if (strstr($method, "method=community") != false) {
    community();
} else {
    echo 'Unkown method!';
}

function whoAmI() {
    require_once '../global.php';
    //Returns information about logged in user
    if ($_SESSION) {
        if ($curUser = User::publicUserInfo("username", $_SESSION['username'])) 
        {
            echo json_encode($curUser);
        }
    }
}

function userDelete() {
    require_once '../global.php';
    $delete = $_POST['delete'];
    $admin = $_POST['admin'];
    //Gets all the albums associated with the user
    $albums = Album::getAlbums("album_owner", $delete);

    //Deletes the user's albums, files and artwork from the server
    foreach ($albums as $a) {
        Album::deleteAlbum($a["album_name"], $delete);
        $artwork_path = $uploads_dir . "/" . $delete . "_" . $a['album_name'] . ".jpg";
        unlink($artwork_path);
    }

    echo $admin;
    // //Deletes the user from the database
    $curUser = User::deleteUser("username", $delete);

    //Logs off user on success
    if ($admin == 0) {
        userLogoff();
    }
}

function userLogin() {
    require_once '../global.php';

    $username = $_POST['username'];
    $password = $_POST['password'];

    // get user with this username from database
    $user = User::userExists("username", $username);

    if($user == null) {
        echo "Username doesn't exist.";
        exit();
    } else {
        // does inputted password match password in db?
        if(User::validateUser($username, $password) == false) {
            echo "Invalid Password";
            exit();
        } else {
            $_SESSION['username'] = $username;
            exit();
        }
    }
}

function userLogoff() {
    require_once '../global.php';

    session_destroy();
    $_SESSION = array();
    header('Location: '.SERVER_PATH);
}

function userRegister() {
    require_once '../global.php';

    //registers the user for creation of account
    $fName = $_POST['firstname'];
    $lName = $_POST['lastname'];
    $email = $_POST['email'];
    $username = $_POST['username'];
    $password1 = $_POST['password1'];
    $password2 = $_POST['password2'];
    $user_type = $_POST['user_type'];
    $favorite_genre = $_POST['favorite_genre'];
    $twitterName = $_POST['twitter'];

    // do the passwords match?
    if($password1 != $password2) {
        echo "Passwords do not match!";
        exit();
    } else { //does email exist?
        if (User::userExists("email", $email)) {
            echo "Email already exists!";
            exit();
        } else if (User::userExists("username", $username)) { //does username exist?
            echo "Username already exists!";
            exit();
        } else { //Create account
            $info = array(
                'email' => $email, 
                'username'=> $username, 
                'first_name'=> $fName, 
                'last_name'=> $lName, 
                'password'=> $password1,
                'user_type'=>$user_type,
                'favorite_genre' => $favorite_genre,
                'twitter' => $twitterName
            );

            $newUser = new User($info);
            $newUser->save();
            $_SESSION['success'] = "User Created!";
            exit();
        }
    }
}

function userUpdate() {
    require_once '../global.php';

    $curUser = User::publicUserInfo("username", $_SESSION['username']);
    $oldFavoriteGenre = $curUser['favorite_genre'];
    $newFavoriteGenre = $_POST['favorite_genre'];

    //Edit the user information
    if (User::editUser($_SESSION['username'], $_POST)) {
        $_SESSION['success'] = "Updated Profile!";


        if ($oldFavoriteGenre != $newFavoriteGenre) {
            $eventProperties = [
                'event_type' => 'change_genre',
                'username' => $curUser['username'],
                'data' => "$oldFavoriteGenre,$newFavoriteGenre"
            ];

            $e = new Event($eventProperties);
            $e->save();
        }
    }
}

function collaborate() {
    require_once '../global.php';

    $collabWith = $_POST['collaborator'];

    // Send a collaboration request to $collabWith
    $result = Collaborator::collab_request($_SESSION['username'], $collabWith);

    if ($result == 1) {
        // Save both sides of the collaboration as an event (bidirectional)
        $eventProperties = [
            'event_type' => 'add_collaborator1',
            'username' => $collabWith,
            'data' => $_SESSION['username']
        ];

        $e = new Event($eventProperties);
        $e->save();

        $eventProperties = [
            'event_type' => 'add_collaborator2',
            'username' => $_SESSION['username'],
            'data' => $collabWith
        ];

        $e = new Event($eventProperties);
        $e->save();
    }
}

function uncollaborate() {
    require_once '../global.php';

    $cancel_request = $_POST['collaborator'];

    // Remove the user as a collaborator
    Collaborator::removeCollaborator($_SESSION['username'], $cancel_request);

    // Remove the events associated with collaboration A->B and B->A
    Event::deleteEvent($_SESSION['username'], 'add_collaborator1', $cancel_request);
    Event::deleteEvent($_SESSION['username'], 'add_collaborator2', $cancel_request);

    Event::deleteEvent($cancel_request, 'add_collaborator1', $_SESSION['username']);
    Event::deleteEvent($cancel_request, 'add_collaborator2', $_SESSION['username']);
}

function promote() {
    require_once '../global.php';

    // Add moderator privelages to the user.
    $promoteUser = $_POST['promote'];
    User::promoteUser($promoteUser);
}

function demote() {
    require_once '../global.php';

    // Remove moderator privelages for a user.
    $demoteUser = $_POST['demote'];
    User::demoteUser($demoteUser);
}

function collabs() {
    require_once '../global.php';

    $pageName = 'Collaborators';

    // Get all the collaborators for the specified user.
    $collabs = Collaborator::getCollaborators($_SESSION['username'], 2);

    if(isset($_SESSION['username'])) {
        $tweets = twitterData();
    }


    require_once '../views/header.html';

    require_once '../views/collabs.html';
}

function twitterData() {
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
            $handle = explode(":", $twitterNames[$i]);
            if (sizeof($handle) == 2) {
                $twitter = $handle[1];
                $username = $handle[0];
                $content = $connection->get('https://api.twitter.com/1.1/statuses/user_timeline.json?screen_name=' . $twitter . '&count=3');
                for ($j = 0; $j < sizeof($content); $j++) {
                    array_push($tweets, $username . ': ' . $content[$j]->text . ' ' . $content[$j]->created_at);

                }
            }
        }
    }
    return $tweets;
}

function home() {
    require_once '../global.php';

    // choose template depending on whether logged in or not
    if(isset($_SESSION['username'])) {
        $user = $_GET['u'];

        if($user == '') {
            header("Location: ".SERVER_PATH.$_SESSION['username']);
            exit;
        }

        // if user exists then show their profile, else 404
        if (($curUser = User::userExists("username", $user))) {
            $pageName = 'CollabTunes - '.$user;

            $albums = Album::getAlbums("album_owner", $user);
            $curUser = User::publicUserInfo("username", $user);

            $displayButton = "add";
            $result = Collaborator::isCollaborator($_SESSION['username'], $user);
            $sentBy = $result['sent_by'];
            $isCollab = $result['status'];
            if ($isCollab != null) {
                if ($isCollab == 0 && strcmp($sentBy, $_SESSION['username']) == 0) {
                    $displayButton = "sent";
                } else if ($isCollab == 0) {
                    $displayButton = "waiting";
                }else {
                    $displayButton = "accepted";
                }
            }

            $twitterName = $curUser['twitter'];
            $tweets = array();

            if (strlen($twitterName) != null) {
              require_once './twitteroauth.php';

                $connection = new TwitterOAuth("mJY9r8kDCUtMJUNiGwfZ2gLxK", "oLzWiKDik54rc1rPYgY2eBmxc0l15U3H6JK0xkBxIlIxp8QTiY", "911592974-xPcUDBEkeBV79vAhTYeVCXXAwnOQSjZhN2ayvwKs", "HGcHF1HyGLZLPJR3gmjMS70jGitmh5UPpdJ3VqCq0cXVN");
                $content = $connection->get('https://api.twitter.com/1.1/statuses/user_timeline.json?screen_name=' . $twitterName);

                for ($i = 0; $i < sizeof($content); $i++) {
                    array_push($tweets, $content[$i]->text . ' ' . $content[$i]->created_at);
                } 
            }
            
            require_once '../views/header.html';
            require_once '../views/profile.html';
        } else {
            $pageName = 'CollabTunes - 404';
            require_once '../views/header.html';
            require_once '../views/404.html';
        }
    } else {
        $pageName = 'CollabTunes';
        require_once '../views/header.html';
        require_once '../views/index.html';
    }
}

function community() {
    require_once '../global.php';

    $pageName = 'CollabTunes - Community';
    $genres = Album::getGenres();

    //Gets all albums for the specified genre
    if (isset($_GET["g"])) {
        parse_str($_SERVER['QUERY_STRING']);
        $selectedGenre = $g;
        $albums = Album::getAlbums("album_genre", $selectedGenre);
    }
    else {
        $albums = Album::getAlbums();
    }

    require_once '../views/header.html';

    if(isset($_SESSION['username'])) {
        $tweets = twitterData();
        require_once '../views/featured_logged_in.html';
    } else {
        require_once '../views/featured.html';
    }
}


?>