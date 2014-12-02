-- phpMyAdmin SQL Dump
-- version 4.2.7.1
-- http://www.phpmyadmin.net
--
-- Host: 127.0.0.1
-- Generation Time: Dec 02, 2014 at 08:23 AM
-- Server version: 5.6.20
-- PHP Version: 5.5.15

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Database: `collabtunes`
--

-- --------------------------------------------------------

--
-- Table structure for table `album`
--

CREATE TABLE IF NOT EXISTS `album` (
  `album_summary` varchar(500) NOT NULL,
  `album_owner` varchar(100) NOT NULL,
  `album_genre` varchar(100) NOT NULL,
  `album_name` varchar(500) NOT NULL,
  `album_image` varchar(200) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `album`
--

INSERT INTO `album` (`album_summary`, `album_owner`, `album_genre`, `album_name`, `album_image`) VALUES
('test', 'divit52', 'test', 'check', '../uploads/divit52_check.jpg'),
('test 2', 'divit52', 'testte', 'test', '../uploads/divit52_test.jpg'),
('Test Album', 'schadha', 'Rap', 'Test Album Sanchit', '../uploads/schadha_Test Album Sanchit.jpg'),
('test', 'schadha', 'Rap2', 'test2', '../uploads/schadha_test2.jpg');

-- --------------------------------------------------------

--
-- Table structure for table `collaborators`
--

CREATE TABLE IF NOT EXISTS `collaborators` (
  `friend_one` varchar(100) NOT NULL,
  `friend_two` varchar(100) NOT NULL,
  `status` int(11) NOT NULL,
  `modified` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
`id` int(11) NOT NULL,
  `sent_by` varchar(100) DEFAULT NULL
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=19 ;

--
-- Dumping data for table `collaborators`
--

INSERT INTO `collaborators` (`friend_one`, `friend_two`, `status`, `modified`, `id`, `sent_by`) VALUES
('schadha', 'divit52', 1, '2014-11-11 05:01:26', 11, 'schadha'),
('divit52', 'schadha', 1, '2014-11-11 05:01:26', 12, 'schadha'),
('kluther', 'schadha', 1, '2014-11-11 05:03:40', 13, 'kluther'),
('schadha', 'kluther', 1, '2014-11-11 05:03:40', 14, 'kluther'),
('test23', 'divit52', 1, '2014-12-02 05:02:19', 17, 'test23'),
('divit52', 'test23', 1, '2014-12-02 05:02:19', 18, 'test23');

-- --------------------------------------------------------

--
-- Table structure for table `comment`
--

CREATE TABLE IF NOT EXISTS `comment` (
  `username` varchar(100) NOT NULL,
  `album_name` varchar(500) NOT NULL,
  `text` text NOT NULL,
  `album_owner` varchar(100) NOT NULL,
  `created` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
`id` int(11) NOT NULL
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=30 ;

--
-- Dumping data for table `comment`
--

INSERT INTO `comment` (`username`, `album_name`, `text`, `album_owner`, `created`, `id`) VALUES
('schadha', 'Test Album Sanchit', 'Test', 'schadha', '2014-11-09 15:56:30', 9),
('schadha', 'Test Album Sanchit', 'Test3', 'schadha', '2014-11-09 16:00:30', 11),
('schadha', 'Test Album Sanchit', '1', 'schadha', '2014-11-11 02:34:54', 14),
('schadha', 'Test Album Sanchit', '2', 'schadha', '2014-11-11 02:36:26', 15),
('schadha', 'Test Album Sanchit', '3', 'schadha', '2014-11-11 02:36:28', 16),
('schadha', 'Test Album Sanchit', '4', 'schadha', '2014-11-11 02:36:29', 17),
('schadha', 'Test Album Sanchit', '5', 'schadha', '2014-11-11 02:36:30', 18),
('schadha', 'Test Album Sanchit', '6', 'schadha', '2014-11-11 02:36:32', 19),
('schadha', 'Test Album Sanchit', '7', 'schadha', '2014-11-11 02:36:33', 20),
('schadha', 'Test Album Sanchit', '8', 'schadha', '2014-11-11 02:36:35', 21),
('schadha', 'Test Album Sanchit', '9', 'schadha', '2014-11-11 02:36:38', 22),
('schadha', 'Test Album Sanchit', '10', 'schadha', '2014-11-11 05:02:07', 23),
('divit52', 'test', 'test', 'divit52', '2014-12-01 21:56:12', 29);

-- --------------------------------------------------------

--
-- Table structure for table `event`
--

CREATE TABLE IF NOT EXISTS `event` (
  `event_type` varchar(32) DEFAULT NULL,
  `username` varchar(128) DEFAULT NULL,
  `data` varchar(128) DEFAULT NULL,
  `album_name` varchar(128) DEFAULT NULL,
  `when_happened` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `event`
--

INSERT INTO `event` (`event_type`, `username`, `data`, `album_name`, `when_happened`) VALUES
('add_album', 'divit52', 'Test Album Divit', 'Test Album Divit', '2014-11-09 13:51:38'),
('add_album', 'schadha', 'Test Album Sanchit', 'Test Album Sanchit', '2014-11-09 13:53:02'),
('add_album', 'divit52', 'test2', 'test2', '2014-11-30 07:06:18'),
('add_comment', 'schadha', '11', 'Test Album Sanchit', '2014-11-09 16:00:30'),
('add_comment', 'schadha', '13', 'Test Album Divit', '2014-11-09 16:01:03'),
('add_comment', 'schadha', '14', 'Test Album Sanchit', '2014-11-11 02:34:54'),
('add_comment', 'schadha', '15', 'Test Album Sanchit', '2014-11-11 02:36:26'),
('add_comment', 'schadha', '16', 'Test Album Sanchit', '2014-11-11 02:36:28'),
('add_comment', 'schadha', '17', 'Test Album Sanchit', '2014-11-11 02:36:29'),
('add_comment', 'schadha', '18', 'Test Album Sanchit', '2014-11-11 02:36:30'),
('add_comment', 'schadha', '19', 'Test Album Sanchit', '2014-11-11 02:36:32'),
('add_comment', 'schadha', '20', 'Test Album Sanchit', '2014-11-11 02:36:33'),
('add_comment', 'schadha', '21', 'Test Album Sanchit', '2014-11-11 02:36:35'),
('add_comment', 'schadha', '22', 'Test Album Sanchit', '2014-11-11 02:36:38'),
('add_track', 'schadha', 'Testing', 'Test Album Sanchit,schadha', '2014-11-11 02:37:01'),
('add_collaborator1', 'schadha', 'divit52', '', '2014-11-11 05:01:26'),
('add_collaborator2', 'divit52', 'schadha', '', '2014-11-11 05:01:26'),
('add_comment', 'schadha', '23', 'Test Album Sanchit', '2014-11-11 05:02:07'),
('add_track', 'schadha', 'Power', 'Test Album Sanchit,schadha', '2014-11-11 05:02:18'),
('add_collaborator1', 'kluther', 'schadha', '', '2014-11-11 05:03:40'),
('add_collaborator2', 'schadha', 'kluther', '', '2014-11-11 05:03:40'),
('change_genre', 'schadha', 'Rap,Pop', '', '2014-11-11 05:09:01'),
('change_genre', 'schadha', 'Pop,Rap', '', '2014-11-11 05:09:07'),
('add_comment', 'divit52', '24', 'Test Album Divit', '2014-11-30 05:20:55'),
('add_comment', 'divit52', '25', 'Test Album Divit', '2014-11-30 05:20:58'),
('add_comment', 'divit52', '26', 'Test Album Divit', '2014-11-30 05:25:31'),
('change_genre', 'schadha', 'Rap,Rock', '', '2014-11-30 05:29:20'),
('add_album', 'divit52', 'test', 'test', '2014-11-30 06:33:38'),
('add_track', 'divit52', 'test', 'check,divit52', '2014-12-02 03:01:22'),
('add_album', 'divit52', 'test2', 'test2', '2014-11-30 07:03:05'),
('add_album', 'divit52', 'test3', 'test3', '2014-11-30 07:04:20'),
('add_collaborator1', 'divit52', 'test', '', '2014-11-30 07:16:50'),
('add_comment', 'divit52', '29', 'test', '2014-12-01 21:56:12'),
('add_album', 'divit52', 'check', 'check', '2014-12-02 03:01:06'),
('add_collaborator1', 'test23', 'divit52', '', '2014-12-02 05:02:19'),
('add_collaborator2', 'divit52', 'test23', '', '2014-12-02 05:02:19');

-- --------------------------------------------------------

--
-- Table structure for table `track`
--

CREATE TABLE IF NOT EXISTS `track` (
  `track_name` varchar(200) NOT NULL,
  `track_path` varchar(500) NOT NULL,
  `track_owner` varchar(100) NOT NULL,
  `track_album` varchar(100) NOT NULL,
  `album_owner` varchar(100) NOT NULL DEFAULT ''
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `track`
--

INSERT INTO `track` (`track_name`, `track_path`, `track_owner`, `track_album`, `album_owner`) VALUES
('Power', '../uploads/schadha_Test_Album_Sanchit_Power.mp3', 'schadha', 'Test Album Sanchit', 'schadha'),
('test', '../uploads/divit52_Test_Album_Sanchit_test.mp3', 'divit52', 'Test Album Sanchit', 'schadha'),
('test', '../uploads/divit52_test_test.mp3', 'divit52', 'test', 'divit52'),
('test', '../uploads/divit52_check_test.mp3', 'divit52', 'check', 'divit52');

-- --------------------------------------------------------

--
-- Table structure for table `user`
--

CREATE TABLE IF NOT EXISTS `user` (
  `username` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `first_name` varchar(100) NOT NULL,
  `last_name` varchar(100) NOT NULL,
  `user_type` int(11) DEFAULT NULL,
  `favorite_genre` varchar(100) NOT NULL DEFAULT '',
  `twitter` varchar(15) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `user`
--

INSERT INTO `user` (`username`, `email`, `password`, `first_name`, `last_name`, `user_type`, `favorite_genre`, `twitter`) VALUES
('divit52', 'test@test2', '$2y$10$VA.zXifmbbkoT3YGjnpAuuuf2hXW63p39Uz16/UNv7ko11CN.iHAi', 'Divit', 'Singh', 2, 'Rap', 'sudosingh'),
('kluther', 'kluther@vt.edu', '$2y$10$GiziXotSkaExlsdwhFzbeutMybZveI3QgfuQaFjxCvHH20UzekhS2', 'Kurt', 'Luther', 0, 'Pop', NULL),
('schadha', 'schadha@vt.edu', '$2y$10$XamgzJzAqSrP4KcHm8PvuuRVx0/LI5Hc9N/eEpciyYLqtHpMZrRCi', 'Sanchit', 'Chadha', 2, 'Rock', NULL),
('test', 'test@test', '$2y$10$3akenjcvVVqwsIdF6HPkaebnHTPKPEQHqR8YmlzIzhkOFoUeMP/0.', 'test', 'test', 0, 'Rap', 'sudosingh'),
('test2', 'test@test212', '$2y$10$gF8idOMBxEw.C7aKckR53eMLYcajOa/qCLDtyM2xJn9tTiDRFLkzq', 'tet', 'ta', 0, 'Pop', NULL),
('test21', 'test@test21', '$2y$10$5Fy95C/GV7Ciqe.zkc80v.1v52CwXhhsV065MQMW0ow59.TJvpAI2', 'test', 'test', 0, 'Rap', NULL),
('test23', 'test@test2123', '$2y$10$9hRMauFbPR9VUb80bXlYmezGrN6rfpcDt/fiMoP9e434Vr82HoOG6', 'tet', 'ta', 0, 'Pop', 'l3thalbloo'),
('test231', 'test@test2123', '$2y$10$9hRMauFbPR9VUb80bXlYmezGrN6rfpcDt/fiMoP9e434Vr82HoOG6', 'tet', 'ta', 0, 'Pop', 'sudosingh'),
('test3', 'test@test3', '$2y$10$qEpPgqkcxdNoLdUeCflxceB/15kLX7ZlIwmA0eZ5Cn29YR0gWNpR.', 'test', 'test', 0, 'Rap', NULL),
('twitter', 'asdf@asdf', '$2y$10$kKNG7XbG2FXC7DkKCYHyMej0ZN6MEwYCcC/QStgwLkIfmHtZB8Tu.', 'test', 'test', 0, 'Rap', 'l3thalbloo');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `album`
--
ALTER TABLE `album`
 ADD PRIMARY KEY (`album_owner`,`album_name`);

--
-- Indexes for table `collaborators`
--
ALTER TABLE `collaborators`
 ADD PRIMARY KEY (`id`);

--
-- Indexes for table `comment`
--
ALTER TABLE `comment`
 ADD PRIMARY KEY (`id`);

--
-- Indexes for table `user`
--
ALTER TABLE `user`
 ADD PRIMARY KEY (`username`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `collaborators`
--
ALTER TABLE `collaborators`
MODIFY `id` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=19;
--
-- AUTO_INCREMENT for table `comment`
--
ALTER TABLE `comment`
MODIFY `id` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=30;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
