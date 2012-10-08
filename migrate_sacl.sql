-- phpMyAdmin SQL Dump
-- version 3.3.9.2
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: Oct 02, 2012 at 05:48 PM
-- Server version: 5.5.9
-- PHP Version: 5.3.6

SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Database: `sacl`
--

-- --------------------------------------------------------

--
-- Table structure for table `access`
--

CREATE TABLE `access` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `type` varchar(255) NOT NULL,
  `type_id` int(11) NOT NULL,
  `feature_id` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `type` (`type`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=6 ;

--
-- Dumping data for table `access`
--

INSERT INTO `access` VALUES(1, 'users', 1, 0);

-- --------------------------------------------------------

--
-- Table structure for table `features`
--

CREATE TABLE `features` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `site_url` varchar(255) NOT NULL,
  `title` varchar(255) NOT NULL,
  `access` int(11) NOT NULL,
  `icon` varchar(255) NOT NULL,
  `dashboard` tinyint(1) NOT NULL,
  `description` varchar(255) NOT NULL,
  `status` varchar(100) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 COMMENT='0:all, 1:public, 2:private, 3:controlled' AUTO_INCREMENT=58 ;

--
-- Dumping data for table `features`
--

INSERT INTO `features` VALUES(5, 'main', '', 2, '', 0, '', '');
INSERT INTO `features` VALUES(2, 'login', '', 1, '', 0, '', '');
INSERT INTO `features` VALUES(3, 'sacl/logout', '', 1, '', 0, '', '');
INSERT INTO `features` VALUES(4, 'welcome', '', 1, '', 0, '', '');
INSERT INTO `features` VALUES(6, 'main/dashboard', '', 2, '', 0, '', '');
INSERT INTO `features` VALUES(11, 'sacl/all_feature', 'Edit Feature', 3, 'pencil.png', 1, 'Edit Feature', 'on');
INSERT INTO `features` VALUES(0, 'ALL', 'All', 3, '', 0, '', '');
INSERT INTO `features` VALUES(10, 'sacl/new_feature', 'New Feature', 3, 'magic.png', 1, 'Add new feature', 'on');
INSERT INTO `features` VALUES(12, 'sacl/add_control', 'Manage ACL', 3, 'keychain.png', 1, 'Manage access card for features', 'on');
INSERT INTO `features` VALUES(57, 'sacl', '', 3, '', 0, '', 'indev');
INSERT INTO `features` VALUES(17, 'sacl/users', 'Manage User', 3, 'folder_user.png', 1, 'Manage user details', 'on');
INSERT INTO `features` VALUES(56, 'main/error_404', '', 1, '', 0, '', 'indev');
INSERT INTO `features` VALUES(55, 'sacl/edit_self', 'Edit Self', 2, 'pencil_yellow.png', 1, 'Edit self details', 'on');
INSERT INTO `features` VALUES(20, 'user/retrieve', 'Retrieve Password', 1, '', 0, 'Kembalikan password untuk login', 'indev');
INSERT INTO `features` VALUES(21, 'sacl/spoof', 'User Spoof', 3, 'identification_card.png', 1, 'Login sebagai user lain', 'on');
INSERT INTO `features` VALUES(22, 'sacl/unspoof', 'Logout Spoof', 1, '', 0, 'Logout from spoofing user', 'indev');
INSERT INTO `features` VALUES(23, 'sacl/gospoof', 'GoSpoof', 1, '', 0, 'redirect spoof link', 'indev');
INSERT INTO `features` VALUES(26, 'sacl/tags', 'Manage Tags', 3, 'tag.png', 1, 'Urus tag yang ada', 'on');
INSERT INTO `features` VALUES(48, 'sacl/edit_staff', '', 3, '', 0, '', 'indev');
INSERT INTO `features` VALUES(49, 'sandbox', 'SandBox', 3, 'tools.png', 0, 'Development Area', 'indev');
INSERT INTO `features` VALUES(54, 'sacl/new_user', 'New User', 3, 'user.png', 1, 'tambah user baru', 'on');

-- --------------------------------------------------------

--
-- Table structure for table `spoof_log`
--

CREATE TABLE `spoof_log` (
  `key` varchar(255) NOT NULL,
  `username` varchar(255) NOT NULL,
  `user_id` int(11) NOT NULL,
  `spoof_id` int(11) NOT NULL,
  `timestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `access` tinyint(1) NOT NULL DEFAULT '1',
  PRIMARY KEY (`key`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Dumping data for table `spoof_log`
--

INSERT INTO `spoof_log` VALUES('d3511a419ac59577642522461ac4c68a3cd377c4', 'root', 1, 1, '2012-10-01 17:21:12', 0);
INSERT INTO `spoof_log` VALUES('04957bb3e9d88f701743c28c7816b5ee432dab58', 'root', 1, 2, '2012-10-02 11:29:19', 0);
INSERT INTO `spoof_log` VALUES('bea37842fc07853baba0eaeeea5777745aff935b', 'root', 1, 3, '2012-10-02 11:30:31', 0);
INSERT INTO `spoof_log` VALUES('17aa5760a5f1d89b17bae097f5e07e0209dcb4bd', 'root', 1, 2, '2012-10-02 11:38:16', 0);
INSERT INTO `spoof_log` VALUES('9d24b241ef204ede7cec6ec96638286e064417ae', 'root', 1, 3, '2012-10-02 12:38:07', 0);
INSERT INTO `spoof_log` VALUES('a1d312546b5c917ed8d323cf2acd2af18684154c', 'root', 1, 3, '2012-10-02 12:50:53', 0);
INSERT INTO `spoof_log` VALUES('46d48c4e2be6fa4acbe154bf909b8f3c0c49b56d', 'root', 1, 3, '2012-10-02 14:08:34', 0);
INSERT INTO `spoof_log` VALUES('fe8f4fbf7294eb822906e8e7763acda987f75eca', 'root', 1, 3, '2012-10-02 15:09:30', 0);
INSERT INTO `spoof_log` VALUES('fc28a7adbf2a02194fdad98e35403c752045c175', 'root', 1, 3, '2012-10-02 15:09:45', 0);
INSERT INTO `spoof_log` VALUES('eca163e6a347d54c36280584e46d44253ece1893', 'root', 1, 3, '2012-10-02 15:17:55', 0);
INSERT INTO `spoof_log` VALUES('2457cd995545a1c9077091104a1fd1edca2e86ce', 'root', 1, 3, '2012-10-02 15:20:45', 0);
INSERT INTO `spoof_log` VALUES('0da751fbc2aa86701995ee7acbe2ddb57d2be0cb', 'root', 1, 3, '2012-10-02 15:21:10', 0);
INSERT INTO `spoof_log` VALUES('e5c317a8d9f7f94650b0fda3a652f83d8237146b', 'root', 1, 2, '2012-10-02 15:32:43', 0);
INSERT INTO `spoof_log` VALUES('13d94627e40ecc3e8a68d8b6831be728241fb1ea', 'root', 1, 3, '2012-10-02 15:35:14', 0);
INSERT INTO `spoof_log` VALUES('84c7b4c9ba7bda0ff9eec50e8c8105773723304c', 'root', 1, 2, '2012-10-02 16:11:52', 0);
INSERT INTO `spoof_log` VALUES('41db7cb27bfe06e9ac990108bb30d4735c53cf86', 'root', 1, 3, '2012-10-02 16:50:34', 0);
INSERT INTO `spoof_log` VALUES('6f3b9490a11374570ca1dc16c225cfc88b099cbc', 'root', 1, 3, '2012-10-02 16:52:21', 0);

-- --------------------------------------------------------

--
-- Table structure for table `tags`
--

CREATE TABLE `tags` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `key` varchar(255) NOT NULL,
  `value` varchar(255) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=3 ;

--
-- Dumping data for table `tags`
--

INSERT INTO `tags` VALUES(1, 'sacl', 'developer');
INSERT INTO `tags` VALUES(2, 'sacl', 'member');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `username` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `fullname` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `tags` text NOT NULL,
  PRIMARY KEY (`id`),
  FULLTEXT KEY `tags` (`tags`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=4 ;

--
-- Dumping data for table `users`
--

INSERT INTO `users` VALUES(1, 'root', '5LcMeHcT1C7S5PbI1N65e3d5e62c9e13ce848ef6feac81bff09==', 'developer', 'root@gmail.com', 'a:1:{i:0;s:1:"1";}');
INSERT INTO `users` VALUES(3, 'user', '5LcMeHcT1C7S5PbI1N65e3d5e62c9e13ce848ef6feac81bff09==', '', 'user@gmail.com', 'a:1:{i:0;s:1:"2";}');

-- --------------------------------------------------------

--
-- Table structure for table `users_data`
--

CREATE TABLE `users_data` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `hp` varchar(255) NOT NULL,
  `address` text NOT NULL,
  `ic` varchar(255) NOT NULL,
  `waris1` text NOT NULL,
  `ic_waris1` varchar(255) NOT NULL,
  `address_waris1` text NOT NULL,
  `hp_waris1` varchar(255) NOT NULL,
  `waris2` text NOT NULL,
  `ic_waris2` varchar(255) NOT NULL,
  `address_waris2` text NOT NULL,
  `hp_waris2` varchar(255) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

--
-- Dumping data for table `users_data`
--

