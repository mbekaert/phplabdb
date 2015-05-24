-- phpLabDB core 
--
-- Copyright 2003-2005 M.Bekaert
-- http://phplabdb.sourceforge.net
--
-- MySQL database: db_phplabdb
---------------------------------------------------------

--
-- Database: db_phplabdb
--

USE db_phplabdb;


--
-- Table structure for table 'membres'
--

DROP TABLE IF EXISTS membres;
CREATE TABLE membres (
  login varchar(20) NOT NULL,
  password varchar(20) NOT NULL,
  email varchar(100) NOT NULL,
  status int(10) unsigned NOT NULL,
  id varchar(32) default NULL,
  ip varchar(50) default NULL,
  PRIMARY KEY  (login),
  UNIQUE KEY email (email,id)
) TYPE=MyISAM;


--
-- Data for table 'membres'
--

LOCK TABLES membres WRITE;
INSERT INTO membres VALUES ('admin','wlclj7rwa77a','root@localhost',2147483647,NULL,NULL);
UNLOCK TABLES;


--
-- Table structure for table 'organisation'
--

DROP TABLE IF EXISTS organisation;
CREATE TABLE organisation (
  name varchar(50) NOT NULL,
  url varchar(100) NOT NULL,
  logo varchar(100) NOT NULL,
  welcome text
) TYPE=MyISAM;


--
-- Data for table 'organisation'
--

LOCK TABLES organisation WRITE;
INSERT INTO organisation VALUES ('phpLabDB','http://phplabdb.sourceforge.net/','/images/header-phplabdb_logo.png','Welcome to phpLabDB');
UNLOCK TABLES;

