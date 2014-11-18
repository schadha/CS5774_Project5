<?php
require_once '../global.php';

//Return information about a specified album
if ($_SESSION) {
	if ($curAlbum = Album::publicAlbumInfo($_POST['album_name'], $_POST['album_owner'])) 
	{
		 echo json_encode($curAlbum);
	}
}
?>