<?php
/*
 * This class is the database wrapper to communicate with mysql.
 * It is left short on purpose so I can use this for all the classes.
 */
class Db {
	private static $_instance = null;
	private $conn;

	//Establish connection to the database
	private function __construct() {
		$host     = DB_HOST;
		$database = DB_DATABASE;
		$username = DB_USER;
		$password = DB_PASS;

		$conn = mysql_connect($host, $username, $password)
			or die ('Error: Could not connect to MySql database');

		mysql_select_db($database);
	}

	//Returns an instance, creates one if it doesn't exist
	public static function instance() {
		if (self::$_instance === null) {
			self::$_instance = new Db();
		}
		return self::$_instance;
	}

	//Insert and Update Operations
	public function execute($query) {
		$ex = mysql_query($query);
		if(!$ex)
			die ('Query failed:' . mysql_error());
	}

	//Looks up queries 
	public function lookup($query) {
		$result = mysql_query($query);
		if(!$result)
			die('Invalid query: ' . $query);
		return ($result);		
	}
}