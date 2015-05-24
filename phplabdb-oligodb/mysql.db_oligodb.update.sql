-- phpLabDB OligoDB plugin 
--
-- Copyright 2003-2005 M.Bekaert
-- http://phplabdb.sourceforge.net
--
-- MySQL database: db_oligodb
---------------------------------------------------------

--
-- Database: db_oligodb / Update
--

USE db_oligodb;


--
-- Table structure for table 'oligo'
--

ALTER TABLE oligo ADD COLUMN freezer varchar(10) default NULL;

