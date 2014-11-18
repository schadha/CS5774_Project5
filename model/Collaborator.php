<?php

/*
 * This file is the Collaborator object.
 * It provides all functionalities related to 
 * accessing the database to create/modify/delete collaborator relationships.
 */
class Collaborator {

    //Collaborate with another user
    public static function collab_request($collab1, $collab2) {
        //Need to look up twice because it is bidirectional and collaborator
        //might be either in friend_one or friend_two location
        $getInfo1 = sprintf("SELECT * from %s where `%s`='%s' and `%s` = '%s'",
                            "collaborators",
                            "friend_one",
                            $collab1,
                            "friend_two",
                            $collab2
                           );

        $getInfo2 = sprintf("SELECT * from %s where `%s`='%s' and `%s` = '%s'",
                            "collaborators",
                            "friend_one",
                            $collab2,
                            "friend_two",
                            $collab1
                           );

        $db = Db::instance();
        $result = $db->lookup($getInfo1);
        $result2 = $db->lookup($getInfo2);

        if (!mysql_num_rows($result) && !mysql_num_rows($result2)) {
            //Need to insert twice because it is bidirectional and collaborator
            //might be either in friend_one or friend_two location
            $query = sprintf("INSERT INTO %s (`%s`, `%s`, `%s`, `%s`) values('%s', '%s', '%s', '%s')",
                             'collaborators',
                             'friend_one',
                             'friend_two',
                             'status',
                             'sent_by',
                             $collab1,
                             $collab2,
                             0,
                             $collab1
                            );

            $query2 = sprintf("INSERT INTO %s (`%s`, `%s`, `%s`, `%s`) values('%s', '%s', '%s', '%s')",
                              'collaborators',
                              'friend_one',
                              'friend_two',
                              'status',
                              'sent_by',
                              $collab2,
                              $collab1,
                              0,
                              $collab1
                             );

            $db->execute($query);
            $db->execute($query2);

            return 0;
        } else {
            $query = sprintf("UPDATE %s SET `%s`='%s' WHERE `%s`='%s' and `%s`='%s'",
                             'collaborators',
                             'status',
                             1,
                             "friend_one",
                             $collab1,
                             "friend_two",
                             $collab2
                            );
            $query2 = sprintf("UPDATE %s SET `%s`='%s' WHERE `%s`='%s' and `%s`='%s'",
                              'collaborators',
                              'status',
                              1,
                              "friend_one",
                              $collab2,
                              "friend_two",
                              $collab1
                             );

            $db->execute($query);
            $db->execute($query2);

            return 1;
        }
    }

    //Checks to see if two people are collaborators with each other
    public static function isCollaborator($collab1, $collab2) {
        $query = sprintf("select * from %s where `%s`='%s' and `%s`='%s'",
                         'collaborators',
                         'friend_one',
                         $collab1,
                         'friend_two',
                         $collab2
                        );

        $db = Db::instance();
        $result = $db->lookup($query);

        if (!mysql_num_rows($result)) {
            return null;
        } else {
            $row = mysql_fetch_assoc($result);
            return $row;
        }
    }

    //Removes Collaboration with someone
    public static function removeCollaborator($collab1, $collab2) {
        $query = sprintf("delete from %s where `%s`='%s' and `%s`='%s'",
                         'collaborators',
                         'friend_one',
                         $collab1,
                         'friend_two',
                         $collab2
                        );

        $query2 = sprintf("delete from %s where `%s`='%s' and `%s`='%s'",
                          'collaborators',
                          'friend_one',
                          $collab2,
                          'friend_two',
                          $collab1
                         );

        $db = Db::instance();
        $result = $db->execute($query);
        $result2 = $db->execute($query2);
    }

    //Get all collaborators for a user
    public static function getCollaborators($username, $status) {
        $query = sprintf("select * from %s where `%s` = '%s' and `%s`='%s' order by `modified` desc",
                         'collaborators',
                         'friend_one',
                         $username,
                         'status',
                         0
                        );

        $query2 = sprintf("select * from %s where `%s` = '%s' and `%s`='%s' order by `modified` desc",
                          'collaborators',
                          'friend_one',
                          $username,
                          'status',
                          1
                         );

        $db = Db::instance();
        $result = $db->lookup($query);
        $result2 = $db->lookup($query2);

        $collabs = array();

        if ($status == 0) { // Pending requests
            while (($row = mysql_fetch_array($result)) != null) {
                array_push($collabs, $row);
            }
        } else if ($status == 1) { // Accepted collaboration requests
            while (($row2 = mysql_fetch_array($result2)) != null) {
                array_push($collabs, $row2);
            }
        } else { //All collaboration requests
            while (($row = mysql_fetch_array($result)) != null) {
                array_push($collabs, $row);
            }
            while (($row2 = mysql_fetch_array($result2)) != null) {
                array_push($collabs, $row2);
            }
        }   
        return $collabs;
    }
}