<?php

/*
 * This class represents a track that the user 
 * uploads to their profile. It serves as the link
 * between database and track information.
 */
class Track {

	const DB_TABLE = 'track';

	protected $track_name;
	protected $track_path;
	protected $track_album;
	protected $track_owner;
    protected $album_owner;

	public function __construct($args = array()) {
		$defaultArgs = array(
            'track_name' => '',
            'track_path' => '',
            'track_album' => null,
            'track_owner' => null,
            '$album_owner' => null
            );

		$args += $defaultArgs;
       	
        $this->track_name = $args['track_name'];
        $this->track_path = $args['track_path'];
        $this->track_album = $args['track_album'];
        $this->track_owner = $args['track_owner'];
        $this->album_owner = $args['album_owner'];
	}

	//Saves/modifies track information 
	public function save($newTrackName = null, $newTrackPath = null) {
		$db = Db::instance();

		if (self::doesTrackExist($this->track_name, $this->track_album, $this->track_owner, $this->album_owner) != null) {
			if ($newTrackName) {
				$query = sprintf("update %s set `%s` = '%s', `%s` = '%s' where `%s` = '%s' and `%s` = '%s' and `%s` = '%s' and `%s` = '%s'",
				self::DB_TABLE,
				'track_name',
				mysql_real_escape_string($newTrackName),
				'track_path',
				mysql_real_escape_string($newTrackPath),
				'track_album',
				mysql_real_escape_string($this->track_album),
				'track_owner',
				mysql_real_escape_string($this->track_owner),
				'track_name',
				mysql_real_escape_string($this->track_name),
                'album_owner',
                mysql_real_escape_string($this->album_owner)
				);
				$db->execute($query);
			}
		} else {
			$query = sprintf("insert into %s (`%s`, `%s`, `%s`, `%s`, `%s`) values ('%s', '%s', '%s', '%s', '%s')",
				self::DB_TABLE,
				'track_name',
				'track_album',
				'track_owner',
				'track_path',
                'album_owner',
				mysql_real_escape_string($this->track_name),
				mysql_real_escape_string($this->track_album),
				mysql_real_escape_string($this->track_owner),
				mysql_real_escape_string($this->track_path),
                mysql_real_escape_string($this->album_owner)
				);
				$db->execute($query);
		}
	}

	//Local method to check existence.. used so I can reference protected variables
	public function doesTrackExist($track_name, $track_album, $track_owner, $album_owner) {
		return self::trackExist($track_name, $track_album, $track_owner, $album_owner);
	}

	//Checks to see if the track exists
	public static function trackExist($track_name, $track_album, $track_owner, $album_owner)
	{
		$db = Db::instance();

		$query = sprintf("SELECT * from %s WHERE `track_name` = '%s' and `track_album` = '%s' and `track_owner` = '%s' and `album_owner` = '%s'",
			self::DB_TABLE,
			mysql_real_escape_string($track_name),
			mysql_real_escape_string($track_album),
			mysql_real_escape_string($track_owner),
            mysql_real_escape_string($album_owner)
			);

		$result = $db->lookup($query);
		if(!mysql_num_rows($result)) {
			return null;
		} else {
			$row = mysql_fetch_assoc($result);
			$track = new Track($row);
			return $track;
		}	
	}

	//Returns all public information about the track
	public static function publicTrackInfo($track_name, $track_album, $track_owner, $album_owner) {
		$track = self::trackExist($track_name, $track_album, $track_owner, $album_owner);

		if ($track) {
			$information = array(
				"track_name" => $track->track_name,
				"track_album" => $track->track_album,
				"track_owner" => $track->track_owner,
				"track_path" => $track->track_path,
                "album_owner" => $track->album_owner
				);
			return $information;
		} else {
			return null;
		}
	}

	//Deletes the track and associated event from the album
	public static function deleteTrack($track_name, $track_album, $track_owner, $album_owner) {
		if (self::doesTrackExist($track_name, $track_album, $track_owner, $album_owner) != null) {
			$query = sprintf("Delete from %s where `track_name` = '%s' and `track_album` = '%s' and `track_owner` = '%s' and `album_owner` = '%s'",
				self::DB_TABLE,
				mysql_real_escape_string($track_name),
				mysql_real_escape_string($track_album),
				mysql_real_escape_string($track_owner),
                mysql_real_escape_string($album_owner)
				);
            
            $deleteEventQuery = sprintf("Delete from event where `data` = '%s' and `album_name` = '%s'",
				mysql_real_escape_string($track_name),
				mysql_real_escape_string("$track_album,$album_owner")
				);
            
			$db = Db::instance();
			$db->execute($query);
            $db->execute($deleteEventQuery);
            
			return true;
		} else {
			return null;
		}
	}
    
    // get an array of tracks for the album that belongs to a specified album owner
    public static function getTracks($track_album, $album_owner) {
    	$db = Db::instance();

        $query = sprintf(" SELECT * FROM `%s`
                WHERE `%s` = '%s'
                and `%s` = '%s'
                ORDER BY `track_name` ASC ",
                self::DB_TABLE,
                "track_album",
                mysql_real_escape_string($track_album),
                "album_owner",
                mysql_real_escape_string($album_owner)
            );
        
        $result = $db->lookup($query);
        if(!mysql_num_rows($result))
            return null;
        else {
            $tracks = array();
            while($row = mysql_fetch_assoc($result)) {
                $tracks[] = self::publicTrackInfo($row['track_name'], $row['track_album'], $row['track_owner'], $row['album_owner']);
            }
            return ($tracks);
        }
    }
    
    // get an array of tracks for the album that belongs to a specified owner
    public static function getTracksByOwner($track_owner) {
    	$db = Db::instance();

        $query = sprintf(" SELECT * FROM `%s`
                WHERE `%s` = '%s'
                ORDER BY `track_name` ASC ",
                self::DB_TABLE,
                "track_owner",
                mysql_real_escape_string($track_owner)
            );
        
        $result = $db->lookup($query);
        if(!mysql_num_rows($result))
            return null;
        else {
            $tracks = array();
            while($row = mysql_fetch_assoc($result)) {
                $tracks[] = self::publicTrackInfo($row['track_name'], $row['track_album'], $row['track_owner'], $row['album_owner']);
            }
            return ($tracks);
        }
    }
    
    // get an array of tracks for the album that belongs to a specified owner
    public static function getTrackByName($track_name) {
    	$db = Db::instance();

        $query = sprintf(" SELECT * FROM `%s`
                WHERE `%s` = '%s'
                ORDER BY `track_name` ASC ",
                self::DB_TABLE,
                "track_name",
                mysql_real_escape_string($track_name)
            );
        
        $result = $db->lookup($query);
        if(!mysql_num_rows($result))
            return null;
        else {
            $row = mysql_fetch_assoc($result);
            return $row;
        }
    }
}