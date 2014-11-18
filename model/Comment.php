<?php

/*
 * This file is the comment object.
 * It provides all functionalities related to 
 * accessing the database to create/modify/delete comments
 */
class Comment {
    // Inserts a comment into the database
    public static function addComment($album_name, $album_owner, $comment, $username) {
        $db = Db::instance();
        $query = sprintf("INSERT INTO %s (`%s`, `%s`, `%s`, `%s`) values (\"%s\", '%s', '%s', '%s')",
                         "comment",
                         "album_name",
                         "album_owner",
                         "text",
                         "username",
                         mysql_real_escape_string($album_name),
                         mysql_real_escape_string($album_owner),
                         mysql_real_escape_string($comment),
                         mysql_real_escape_string($username)
                        );

        $db->execute($query);

        $last_id = mysql_insert_id();

        return $last_id;
    }

    // Returns the comments for the given album
    public function getComments($album_name, $album_owner) {
        $query = sprintf("SELECT * from %s where `album_name`= \"%s\" and `album_owner`='%s' ORDER BY `created` DESC",
                         "comment",
                         $album_name,
                         $album_owner
                        );

        $db = Db::instance();
        $result = $db->lookup($query);
        $comments = array();

        while ($row = mysql_fetch_array($result)) {
            array_push($comments, $row);
        }
        return $comments;
    }
    
    // Returns the comment for a given id
    public function getComment($id) {
        $query = sprintf("SELECT * from %s where `id`='%s' ORDER BY `created` DESC",
                         "comment",
                         $id
                        );

        $db = Db::instance();
        $result = $db->lookup($query);

        if(!mysql_num_rows($result)) {
            return null;
        } else {
            $row = mysql_fetch_assoc($result);
            return $row;
        }
    }
    
    // Deletes the comment for a given id
    public static function deleteComment($id) {
        $query = sprintf("DELETE FROM %s where `id`= %d",
                         "comment",
                         $id
                        );

        $deleteEventQuery = sprintf("DELETE FROM %s where `data`= %d",
                                    "event",
                                    $id
                                   );


        $db = Db::instance();
        $db->execute($query);
        $db->execute($deleteEventQuery);
    }
}