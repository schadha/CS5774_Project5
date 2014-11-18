<?php 

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

?>