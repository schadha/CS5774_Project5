CREATE DATABASE  IF NOT EXISTS `collabtunes` /*!40100 DEFAULT CHARACTER SET utf8 */;
USE `collabtunes`;
-- MySQL dump 10.13  Distrib 5.6.17, for Win32 (x86)
--
-- Host: localhost    Database: collabtunes
-- ------------------------------------------------------
-- Server version	5.6.20

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

--
-- Table structure for table `album`
--

DROP TABLE IF EXISTS `album`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `album` (
  `album_summary` varchar(500) NOT NULL,
  `album_owner` varchar(100) NOT NULL,
  `album_genre` varchar(100) NOT NULL,
  `album_name` varchar(500) NOT NULL,
  `album_image` varchar(200) NOT NULL,
  PRIMARY KEY (`album_owner`,`album_name`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `album`
--

LOCK TABLES `album` WRITE;
/*!40000 ALTER TABLE `album` DISABLE KEYS */;
INSERT INTO `album` VALUES ('Test Album','schadha','Rap','Test Album Sanchit','../uploads/schadha_Test Album Sanchit.jpg'),('test','schadha','Rap2','test2','../uploads/schadha_test2.jpg');
/*!40000 ALTER TABLE `album` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `collaborators`
--

DROP TABLE IF EXISTS `collaborators`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `collaborators` (
  `friend_one` varchar(100) NOT NULL,
  `friend_two` varchar(100) NOT NULL,
  `status` int(11) NOT NULL,
  `modified` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `sent_by` varchar(100) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=21 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `collaborators`
--

LOCK TABLES `collaborators` WRITE;
/*!40000 ALTER TABLE `collaborators` DISABLE KEYS */;
/*!40000 ALTER TABLE `collaborators` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `comment`
--

DROP TABLE IF EXISTS `comment`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `comment` (
  `username` varchar(100) NOT NULL,
  `album_name` varchar(500) NOT NULL,
  `text` text NOT NULL,
  `album_owner` varchar(100) NOT NULL,
  `created` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `id` int(11) NOT NULL AUTO_INCREMENT,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=30 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `comment`
--

LOCK TABLES `comment` WRITE;
/*!40000 ALTER TABLE `comment` DISABLE KEYS */;
INSERT INTO `comment` VALUES ('schadha','Test Album Sanchit','Test','schadha','2014-11-09 15:56:30',9),('schadha','Test Album Sanchit','Test3','schadha','2014-11-09 16:00:30',11),('schadha','Test Album Sanchit','1','schadha','2014-11-11 02:34:54',14),('schadha','Test Album Sanchit','2','schadha','2014-11-11 02:36:26',15),('schadha','Test Album Sanchit','3','schadha','2014-11-11 02:36:28',16),('schadha','Test Album Sanchit','4','schadha','2014-11-11 02:36:29',17),('schadha','Test Album Sanchit','5','schadha','2014-11-11 02:36:30',18),('schadha','Test Album Sanchit','6','schadha','2014-11-11 02:36:32',19),('schadha','Test Album Sanchit','7','schadha','2014-11-11 02:36:33',20),('schadha','Test Album Sanchit','8','schadha','2014-11-11 02:36:35',21),('schadha','Test Album Sanchit','9','schadha','2014-11-11 02:36:38',22);
/*!40000 ALTER TABLE `comment` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `event`
--

DROP TABLE IF EXISTS `event`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `event` (
  `event_type` varchar(32) DEFAULT NULL,
  `username` varchar(128) DEFAULT NULL,
  `data` varchar(128) DEFAULT NULL,
  `album_name` varchar(128) DEFAULT NULL,
  `when_happened` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `event`
--

LOCK TABLES `event` WRITE;
/*!40000 ALTER TABLE `event` DISABLE KEYS */;
INSERT INTO `event` VALUES ('add_album','schadha','Test Album Sanchit','Test Album Sanchit','2014-11-09 13:53:02'),('add_comment','schadha','11','Test Album Sanchit','2014-11-09 16:00:30'),('add_comment','schadha','13','Test Album Divit','2014-11-09 16:01:03'),('add_comment','schadha','14','Test Album Sanchit','2014-11-11 02:34:54'),('add_comment','schadha','15','Test Album Sanchit','2014-11-11 02:36:26'),('add_comment','schadha','16','Test Album Sanchit','2014-11-11 02:36:28'),('add_comment','schadha','17','Test Album Sanchit','2014-11-11 02:36:29'),('add_comment','schadha','18','Test Album Sanchit','2014-11-11 02:36:30'),('add_comment','schadha','19','Test Album Sanchit','2014-11-11 02:36:32'),('add_comment','schadha','20','Test Album Sanchit','2014-11-11 02:36:33'),('add_comment','schadha','21','Test Album Sanchit','2014-11-11 02:36:35'),('add_comment','schadha','22','Test Album Sanchit','2014-11-11 02:36:38'),('add_track','schadha','Testing','Test Album Sanchit,schadha','2014-11-11 02:37:01'),('change_genre','schadha','Rap,Pop','','2014-11-11 05:09:01'),('change_genre','schadha','Pop,Rap','','2014-11-11 05:09:07'),('change_genre','schadha','Rap,Rock','','2014-11-30 05:29:20'),('change_genre','schadha','Rock,Rap','','2014-12-02 07:30:09');
/*!40000 ALTER TABLE `event` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `track`
--

DROP TABLE IF EXISTS `track`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `track` (
  `track_name` varchar(200) NOT NULL,
  `track_path` varchar(500) NOT NULL,
  `track_owner` varchar(100) NOT NULL,
  `track_album` varchar(100) NOT NULL,
  `album_owner` varchar(100) NOT NULL DEFAULT ''
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `track`
--

LOCK TABLES `track` WRITE;
/*!40000 ALTER TABLE `track` DISABLE KEYS */;
/*!40000 ALTER TABLE `track` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `user`
--

DROP TABLE IF EXISTS `user`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `user` (
  `username` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `first_name` varchar(100) NOT NULL,
  `last_name` varchar(100) NOT NULL,
  `user_type` int(11) DEFAULT NULL,
  `favorite_genre` varchar(100) NOT NULL DEFAULT '',
  `twitter` varchar(15) DEFAULT NULL,
  PRIMARY KEY (`username`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `user`
--

LOCK TABLES `user` WRITE;
/*!40000 ALTER TABLE `user` DISABLE KEYS */;
INSERT INTO `user` VALUES ('divit52','test@test2','$2y$10$VA.zXifmbbkoT3YGjnpAuuuf2hXW63p39Uz16/UNv7ko11CN.iHAi','Divit','Singh',2,'Rap','sudosingh'),('kluther','kluther@vt.edu','$2y$10$GiziXotSkaExlsdwhFzbeutMybZveI3QgfuQaFjxCvHH20UzekhS2','Kurt','Luther',0,'Pop','kurtluther'),('schadha','schadha@vt.edu','$2y$10$XamgzJzAqSrP4KcHm8PvuuRVx0/LI5Hc9N/eEpciyYLqtHpMZrRCi','Sanchit','Chadha',2,'Rap','l3thalbloo'),('test','test@test','$2y$10$3akenjcvVVqwsIdF6HPkaebnHTPKPEQHqR8YmlzIzhkOFoUeMP/0.','test','test',1,'Rap','kanyewest');
/*!40000 ALTER TABLE `user` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2014-12-02  5:20:14
