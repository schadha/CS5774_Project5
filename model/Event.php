<?php

/*
 * This file is the event object.
 * It provides all functionalities related to 
 * access the database to create/modify/delete events
 */
class Event {

    const DB_TABLE = 'event';

    protected $event_type;
    protected $username;
    protected $data;
    protected $album_name;
    protected $when_happened;

    // Create an event object
    public function __construct($args = array()) {
        $defaultArgs = array(
            '$event_type' => '',
            'username' => '',
            'data' => '',
            'album_name' => '',
            'when_happened' => null
        );

        $args += $defaultArgs;

        $this->event_type = $args['event_type'];     
        $this->username = $args['username'];
        $this->data = $args['data'];
        $this->album_name = $args['album_name'];
        $this->when_happened = $args['when_happened'];
    }

    //Saves or updates the event information to the database
    public function save() {
        $db = Db::instance();
        if (self::doesEventExist($this->username, $this->event_type, $this->data) != null) {
            $query = sprintf("update %s set `%s` = '%s', `%s` = '%s', `%s` = '%s', `%s` = '%s' where `%s` = '%s' and `%s` = '%s'",
                             self::DB_TABLE,
                             'event_type',
                             mysql_real_escape_string($this->event_type),
                             'username',
                             mysql_real_escape_string($this->username),
                             'data',
                             mysql_real_escape_string($this->data),
                             'album_name',
                             mysql_real_escape_string($this->album_name),
                             'event_type',
                             mysql_real_escape_string($this->event_type),
                             'data',
                             mysql_real_escape_string($this->data)
                            );
        } else {
            $query = sprintf("insert into %s (`%s`, `%s`, `%s`, `%s`) values ('%s', '%s', '%s', '%s')",
                             self::DB_TABLE,
                             'event_type',
                             'username',
                             'data',
                             'album_name',
                             mysql_real_escape_string($this->event_type),
                             mysql_real_escape_string($this->username),
                             mysql_real_escape_string($this->data),
                             mysql_real_escape_string($this->album_name)
                            );
        }
        $db->execute($query);
    }

    //Deletes the event from the database
    public static function deleteEvent($username, $event_type, $data) {
        if (self::doesEventExist($username, $event_type, $data) != null) {
            $query = sprintf("Delete from %s where `username` = '%s' and `event_type` = '%s' and `data` = '%s' ",
                             self::DB_TABLE,
                             mysql_real_escape_string($username),
                             mysql_real_escape_string($event_type),
                             mysql_real_escape_string($data)
                            );

            $db = Db::instance();
            $db->execute($query);
            return true;
        } else {
            return false;
        }
    }
    
    // Checks if the event exists in the database and if it does, returns it
    public static function doesEventExist($username, $event_type, $data)
    {
        $db = Db::instance();
        $query = sprintf("SELECT * from %s WHERE `username` = '%s' and `event_type` = '%s' and `data` = '%s'",
                         self::DB_TABLE,
                         mysql_real_escape_string($username),
                         mysql_real_escape_string($event_type),
                         mysql_real_escape_string($data)
                        );

        $result = $db->lookup($query);
        if(!mysql_num_rows($result)) {
            return null;
        } else {
            $row = mysql_fetch_assoc($result);
            $event = new Event($row);
            return $event;
        }
    }

    //Returns event information, if it exists
    public static function eventInfo($username, $event_type, $data) {
        $event = self::doesEventExist($username, $event_type, $data);

        if ($event) {
            $information = array(
                "username" => $event->username,
                "event_type" => $event->event_type,
                "data" => $event->data,
                "album_name" => $event->album_name,
                "when_happened" => $event->when_happened
            );
            return $information;
        } else {
            return null;
        }
    }

    // Get an array of all events or events specified by column/value
    public function getEvents($column = null, $value = null) {
        if($column == null && $value == null) {
            $query = sprintf(" SELECT * FROM `%s`
                ORDER BY `when_happened` DESC ",
                             self::DB_TABLE
                            );
        } else {
            $query = sprintf(" SELECT * FROM `%s`
                WHERE `%s` = '%s'
                ORDER BY `when_happened` DESC ",
                             self::DB_TABLE,
                             $column,
                             $value
                            );
        }

        $events = array();

        $db = Db::instance();
        $result = $db->lookup($query);
        if(!mysql_num_rows($result))
            return $events;
        else {
            while($row = mysql_fetch_assoc($result)) {
                $events[] = self::eventInfo($row['username'], $row['event_type'], $row['data']);
            }
            return ($events);
        }
    }
}