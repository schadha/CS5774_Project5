<?php

/* 
 * This class serves as the User connection to the database.
 * It connects all interaction between the user and the product.
 */
class User {

	const DB_TABLE = 'user';

	protected $email;
	protected $username;
	protected $password;
	protected $first_name;
	protected $last_name;
    protected $favorite_genre;

	public function __construct($args = array()) {
		$defaultArgs = array(
			'email' => null,
			'username' => '',
			'password' => '',
			'first_name' => null,
			'last_name' => null,
			'user_type'=>null,
            'favorite_genre' => ''
			);

		$args += $defaultArgs;

		$this->email = $args['email'];     
		$this->username = $args['username'];
		$this->password = password_hash($args['password'], PASSWORD_DEFAULT);
		$this->first_name = $args['first_name'];
		$this->last_name = $args['last_name'];
		$this->user_type = $args['user_type'];
        $this->favorite_genre = $args['favorite_genre'];
	}

	//Creates a new user in the database
	public function save() {
		$db = Db::instance();

		if (($curUser = self::doesUserExist("username", $this->username)) != null) {
			$query = sprintf("update %s (%s = '%s', %s = '%s', %s = '%s', %s = '%s', `%s` = '%s', `%s` = '%s') where `%s` = '%s'",
				self::DB_TABLE,
				'email',
				$this->email,
				'password',
				$this->password,
				'first_name',
				$this->first_name,
				'last_name',
				$this->last_name,
				'user_type',
				$this->user_type,
                'favorite_genre',
                $this->favorite_genre,
				'username',
				$this->username
				);
		} else {
			$query = sprintf("insert into %s (`%s`, `%s`, `%s`, `%s`, `%s`,`%s`,`%s`) values ('%s', '%s', '%s', '%s', '%s', '%s', '%s')",
				self::DB_TABLE,
				'email',
				'username',
				'password',
				'first_name',
				'last_name',
				'user_type',
                'favorite_genre',
				$this->email,
				$this->username,
				$this->password,
				$this->first_name,
				$this->last_name,
				$this->user_type,
                $this->favorite_genre
				);
		}
		$db->execute($query);
	}

	//Local method to check existence, used to access protected variables
	public function doesUserExist($propertyname, $username) {
		return self::userExists("username", $username);
	}

	//Method to check if user has special permissions (moderator/admin/regular user)
	public static function isSpecial($username) {
		$specialUser = self::userExists("username", $username);
		return $specialUser->user_type;
	}

	//Method to check for user existence
	public static function userExists($propertyname, $username)
	{
		$query = sprintf("SELECT * from %s WHERE `%s` = '%s' ",
			self::DB_TABLE,
			$propertyname,
			$username
			);

		$db = Db::instance();
		$result = $db->lookup($query);
		if(!mysql_num_rows($result)) {
			return null;
		} else {
			$row = mysql_fetch_assoc($result);
			$curUser = new User($row);
			return $curUser;
		}	
	}

	//Returns all public information (e.g. not password) about user
	public static function publicUserInfo($propertyname, $username) {
		$user = self::userExists($propertyname, $username);

		if ($user) {
			$information = array(
				"email" => $user->email,
				"username" => $user->username,
				"first_name" => $user->first_name,
				"last_name" => $user->last_name,
                "user_type" => $user->user_type,
                "favorite_genre" => $user->favorite_genre
				);
			return $information;
		} else {
			return null;
		}
	}

	//Validates user for logging in
	public static function validateUser($username, $password) {
		$query = sprintf("select password from %s WHERE `username` = '%s'",
			self::DB_TABLE,
			$username
			);
		$db = Db::instance();
		$result = $db->lookup($query);
		if (mysql_num_rows($result) == 0) {
			return false;
		} else {
			$hash = mysql_fetch_array($result);
			if (password_verify($password, $hash[0])) {
				return true;
			}
			return false;
		}
	}

	//Deletes user from database
	public static function deleteUser($propertyname, $username) {
		if (self::userExists($propertyname, $username) != null) {
			$query = sprintf("Delete from %s where `username` = '%s' ",
				self::DB_TABLE,
				$username
				);

			$query_deleteComments = sprintf("Delete from comment where `username` = '%s'",
				mysql_real_escape_string($username)
				);

			$query_deleteEvents1 = sprintf("Delete from event where `username` = '%s'",
				mysql_real_escape_string($username)
				);

			$query_deleteEvents2 = sprintf("Delete from event where `data` = '%s' and `event_type` = 'add_collaborator2'",
				mysql_real_escape_string($username)
				);

			$query_deleteCollaborators = sprintf("Delete from collaborators where `friend_one` = '%s' or `friend_two` = '%s'",
				mysql_real_escape_string($username),
				mysql_real_escape_string($username)
				);

			$db = Db::instance();
			$db->execute($query);
			$db->execute($query_deleteComments);
			$db->execute($query_deleteEvents1);
			$db->execute($query_deleteEvents2);
			$db->execute($query_deleteCollaborators);
			return true;
		} else {
			return null;
		}
	}

	//Edits the properties of the user
	public static function editUser($username, $updated = array()) {
		if (($curUser = self::userExists("username", $username)) != null) {
			$fields = array();
			$values = array();
			foreach ($updated as $key => $value) {
				if (strcmp($key, "email") == 0 && self::userExists("email", $value) == null) {
					array_push($fields, $key);
					array_push($values, $value);
				} else if (strcmp($key, "password") == 0) {
					array_push($fields, $key);
					array_push($values, password_hash($value, PASSWORD_DEFAULT));
				} else if (strcmp($key, "email") != 0) {
					array_push($fields, $key);
					array_push($values, $value);
				}
			}

			$query = "update " . self::DB_TABLE . ' set ';
			for ($i = 0; $i < sizeof($fields); $i++) {
				$query .= $fields[$i] . " = " . "'" . $values[$i] . "'";
				if ($i != sizeof($fields) - 1) {
					$query .= ", ";
				}
			}
			$query .= "where `username` = '$username'";
			$db = Db::instance();
			$db->execute($query);
			return true;
		}
		return false;
	}

	//Promotes a regular user to a moderator
	public static function promoteUser($username) {
		if (self::userExists("username", $username)) {
			$query = sprintf("update user set `%s`='%s' where `%s`='%s'",
				'user_type',
				'1',
				'username',
				$username
			);
			$db = Db::instance();
			$db->execute($query);
		}
	}

	//Demotes a moderator to a regular user
	public static function demoteUser($username) {
		if (self::userExists("username", $username)) {
			$query = sprintf("update user set `%s`='%s' where `%s`='%s'",
				'user_type',
				'0',
				'username',
				$username
			);
			$db = Db::instance();
			$db->execute($query);
		}
	}
}