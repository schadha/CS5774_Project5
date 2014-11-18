<?php
require_once '../global.php';

$pageName = 'CollabTunes - Community';
$genres = Album::getGenres();

//Gets all albums for the specified genre
if ($_GET) {
	$selectedGenre = $_GET['g'];
	$albums = Album::getAlbums("album_genre", $selectedGenre);
}
else {
	$albums = Album::getAlbums();
}

require_once '../views/header.html';

if(isset($_SESSION['username'])) {
	require_once '../views/featured_logged_in.html';
} else {
	require_once '../views/featured.html';
}