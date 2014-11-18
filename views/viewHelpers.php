<?php

session_start();

// return session errors, if any
$errorMessage = '';
if(isset($_SESSION['error'])) {
    $errorMessage = $_SESSION['error'];
    unset($_SESSION['error']);
}

$successMessage = '';
if(isset($_SESSION['success'])) {
    $successMessage = $_SESSION['success'];
    unset($_SESSION['success']);
}

// Helper method to parse the event_type and create a text based event using  hyperlinks and event information
function renderEvent($event=null, $user) {
    if($event == null)
        echo '';
    
    if ($event['username'] == $user) {
        $username = "<a href=".SERVER_PATH.$event['username']."> You </a>";
    } else {
        $username = "<a href=".SERVER_PATH.$event['username'].">".$event['username']."</a>";
    }

    $eventType = $event['event_type'];
    switch($eventType) {
        // add comment
        case 'add_comment':
            $commentData = Comment::getComment($event['data']);
            $comment = $commentData['text'];
            $album_owner = $commentData['album_owner'];
            //$user/you added the comment: $data[0] to the album $data[1]
            echo $username .' added the comment "'. $comment .'" to the album "'. "<a href=".SERVER_PATH.$album_owner.'/'.str_replace(" ", "%20", $event['album_name']).">" .$event['album_name']."</a>" .'" - '. date("M j, g:i a", strtotime($event['when_happened']));
            break;

        // add album
        case 'add_album':
            //$user/you added the album: $data
            echo $username .' added the album "'. "<a href=".SERVER_PATH.$event['username'].'/'.str_replace(" ", "%20", $event['album_name']).">".$event['album_name']."</a>" .'" - '. date("M j, g:i a", strtotime($event['when_happened']));
            break;

        //add track
        case 'add_track':
            $dataArray = explode(",", $event['album_name']);
            
            //$user/you added the track: $data[0] to the album $data[1]
            echo $username . ' added the track "'. $event['data'] .'" to the album "'. "<a href=".SERVER_PATH.$dataArray[1].'/'.str_replace(" ", "%20", $dataArray[0]).">".$dataArray[0]."</a>" .'" by '. "<a href=".SERVER_PATH.$dataArray[1].">".$dataArray[1]."</a>" . ' - '. date("M j, g:i a", strtotime($event['when_happened']));
            break;

        //added a collaborator
        case 'add_collaborator1':
            //$username added $data/you as a collaborator
            echo $username .' added '. "<a href=".SERVER_PATH.$event['data'].">".$event['data']."</a>" .' as a collaborator - ' . date("M j, g:i a", strtotime($event['when_happened']));
            break;
        
        //added as a collaborator
        case 'add_collaborator2':
            //You/$user are/is collaborating with $username
            if (strpos($username, 'You') !== false) {
                echo $username .' are collaborating with '. "<a href=".SERVER_PATH.$event['data'].">".$event['data']."</a>" . ' - '  . date("M j, g:i a", strtotime($event['when_happened']));
            } else {
                echo $username .' is collaborating with '. "<a href=".SERVER_PATH.$event['data'].">".$event['data']."</a>" . ' - ' . date("M j, g:i a", strtotime($event['when_happened']));
            }
            break;
        
        // change genre
        case 'change_genre':
            $dataArray = explode(",", $event['data']);
            //$user/you changed the favorite genre from "$data[0]" to "$data[1]"
            if (strpos($username, 'You') !== false) {
                echo $username .' changed your favorite genre from "'. $dataArray[0] .'" to "' . $dataArray[1] . '" - '. date("M j, g:i a", strtotime($event['when_happened']));
            } else {
                echo $username .' changed their favorite genre from "'. $dataArray[0] .'" to "' . $dataArray[1] . '" - '. date("M j, g:i a", strtotime($event['when_happened']));
            }
            break;
    }
}
