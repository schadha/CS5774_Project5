<?php

/*
 * This file is the album object.
 * It provides all functionalities related to 
 * access the database to create/modify/delete albums
 */
class Album {

	const DB_TABLE = 'album';

	protected $album_name;
	protected $album_summary;
	protected $album_owner;
	protected $album_genre;
	protected $album_image;

	public function __construct($args = array()) {
		$defaultArgs = array(
            'album_name' => null,
            'album_owner' => '',
            'album_summary' => '',
            'album_genre' => null,
            'album_image' =>null
            );

		$args += $defaultArgs;
       
        $this->album_name = $args['album_name'];     
        $this->album_owner = $args['album_owner'];
        $this->album_summary = $args['album_summary'];
        $this->album_genre = $args['album_genre'];
        $this->album_image = $args['album_image'];
	}

	//Saves or updates the album information to the database
	public function save() {
		$db = Db::instance();
		if (self::doesAlbumExist($this->album_name, $this->album_owner) != null) {
			$query = sprintf("update %s set `%s` = '%s', `%s` = '%s', `%s` = '%s' where `%s` = '%s' and `%s` = '%s'",
				self::DB_TABLE,
				'album_summary',
				mysql_real_escape_string($this->album_summary),
				'album_genre',
				mysql_real_escape_string($this->album_genre),
				'album_image',
				mysql_real_escape_string($this->album_image),
				'album_name',
				mysql_real_escape_string($this->album_name),
				'album_owner',
				mysql_real_escape_string($this->album_owner)
				);
		} else {
			$query = sprintf("insert into %s (`%s`, `%s`, `%s`, `%s`, `%s`) values ('%s', '%s', '%s', '%s', '%s')",
				self::DB_TABLE,
				'album_name',
				'album_owner',
				'album_summary',
				'album_genre',
				'album_image',
				mysql_real_escape_string($this->album_name),
				mysql_real_escape_string($this->album_owner),
				mysql_real_escape_string($this->album_summary),
				mysql_real_escape_string($this->album_genre),
				mysql_real_escape_string($this->album_image)
				);
		}
		$db->execute($query);
	}

	//Local method to check if the album exists
	public function doesAlbumExist($album_name, $album_owner) {
		return self::albumExist($album_name, $album_owner);
	}

	//Checks to see if the specified album exists and returns the album
	public static function albumExist($album_name, $album_owner)
	{
		$db = Db::instance();
		$query = sprintf("SELECT * from %s WHERE `album_name` = '%s' and `album_owner` = '%s'",
			self::DB_TABLE,
			mysql_real_escape_string($album_name),
			mysql_real_escape_string($album_owner)
			);

		$result = $db->lookup($query);
		if(!mysql_num_rows($result)) {
			return null;
		} else {
			$row = mysql_fetch_assoc($result);
			$album = new Album($row);
			return $album;
		}
	}

	//Returns public information regarding the album
	public static function getInfoByAlbum($album) {
		return self::publicAlbumInfo($album->album_name, $album->album_owner);
	}

	//Returns album information, if it exists
	public static function publicAlbumInfo($album_name, $album_owner) {
		$album = self::albumExist($album_name, $album_owner);

		if ($album) {
			$information = array(
				"album_name" => $album->album_name,
				"album_owner" => $album->album_owner,
				"album_summary" => $album->album_summary,
				"album_genre" => $album->album_genre,
				"album_image" => $album->album_image
				);
			return $information;
		} else {
			return null;
		}
	}

	//Deletes the album and its tracks from the database
	public static function deleteAlbum($album_name, $album_owner) {
		if (self::doesAlbumExist($album_name, $album_owner) != null) {
			$query_deleteAlbum = sprintf("Delete from %s where `album_name` = '%s' and `album_owner` = '%s' ",
				self::DB_TABLE,
				mysql_real_escape_string($album_name),
				mysql_real_escape_string($album_owner)
				);
			
			$query_deleteTracks = sprintf("Delete from track where `track_owner` = '%s'",
				mysql_real_escape_string($album_owner)
			);

			$tracks = Track::getTracksByOwner($album_owner);
			if ($tracks) {
				foreach ($tracks as $t) {
					unlink($t['track_path']);
				}
			}
            
            $query_deleteComments = sprintf("Delete from comment where `album_name` = '%s'",
				mysql_real_escape_string($album_name)
			);
            
            $query_deleteEvents = sprintf("Delete from event where `album_name` = '%s,%s'",
				mysql_real_escape_string($album_name),
                mysql_real_escape_string($album_owner)
			);
            //delete comments
            //delete events
			
			$db = Db::instance();
			$db->execute($query_deleteAlbum);
			$db->execute($query_deleteTracks);
            $db->execute($query_deleteComments);
            $db->execute($query_deleteEvents);
            
			return true;
		} else {
			return null;
		}
	}

	//Modifies the album object
	public static function editAlbum($orig_album, $album_name, $album_owner, $updated = array()) {
		if (self::doesAlbumExist($album_name, $album_owner) != null) {
			$fields = array();
			$values = array();
			foreach ($updated as $key => $value) {
				if (strcmp($key, "album_name") == 0 && self::albumExist($album_name, $album_owner) == null) {
					array_push($fields, $key);
					array_push($values, $value);
				} else if (strcmp($key, "orig_album") != 0 && strcmp($key, "album_name") != 0) {
					array_push($fields, $key);
					array_push($values, $value);
				} else {
					echo "Album name already exists under " . $album_owner . "!";
					return;
				}
			}

			$query = "update " . self::DB_TABLE . ' set ';
			for ($i = 0; $i < sizeof($fields); $i++) {
				$query .= $fields[$i] . " = " . "'" . $values[$i] . "'";
				if ($i != sizeof($fields) - 1) {
					$query .= ", ";
				}
			}
			$query .= "where `album_name` = '$orig_album'";
			$db = Db::instance();
			$db->execute($query);
			return;
		}

	}
    
    // get an array of albums created by the owner
    public function getAlbums($column = null, $value = null) {
        if($column == null && $value == null) {
            $query = sprintf(" SELECT * FROM `%s`
                ORDER BY album_name ASC ",
                self::DB_TABLE
            );
        } else {
            $query = sprintf(" SELECT * FROM `%s`
                WHERE `%s` = '%s'
                ORDER BY `album_name` ASC ",
                self::DB_TABLE,
                $column,
                $value
            );
        }

        $db = Db::instance();
        $result = $db->lookup($query);
        if(!mysql_num_rows($result))
            return null;
        else {
            $albums = array();
            while($row = mysql_fetch_assoc($result)) {
                $albums[] = self::publicAlbumInfo($row['album_name'], $row['album_owner']);
            }
            return ($albums);
        }
    }
    
    // get an array of all distinct genres in the database
    public function getGenres() {
        $query = sprintf(" SELECT DISTINCT album_genre FROM `%s` ORDER BY album_genre ASC",
            self::DB_TABLE
        );
        
        $db = Db::instance();
        $result = $db->lookup($query);
        if(!mysql_num_rows($result))
            return null;
        else {
            $genres = array();
            while($row = mysql_fetch_assoc($result)) {
                array_push($genres, $row['album_genre']);
            }
            return ($genres);
        }
    }
}