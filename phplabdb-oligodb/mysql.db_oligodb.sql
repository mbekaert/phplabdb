-- phpLabDB OligoDB plugin 
--
-- Copyright 2003 M.Bekaert
-- http://phplabdb.sourceforge.net
--
-- MySQL database: db_oligodb
---------------------------------------------------------

--
-- Database: db_oligodb
--

USE db_oligodb;


--
-- Table structure for table 'config'
--

DROP TABLE IF EXISTS config;
CREATE TABLE config (
  id varchar(20) NOT NULL,
  value varchar(50) default NULL,
  PRIMARY KEY (id)
) TYPE=MyISAM;


--
-- Data for table `config`
--

INSERT INTO config (id, value) VALUES ('pair', 'true');
INSERT INTO config (id, value) VALUES ('barcode', 'true');
INSERT INTO config (id, value) VALUES ('note', 'true');


--
-- Table structure for table 'couple'
--

DROP TABLE IF EXISTS couple;
CREATE TABLE couple (
  name1 varchar(20) NOT NULL,
  name2 varchar(20) NOT NULL,
  notes text,
  PRIMARY KEY (name1,name2)
) TYPE=MyISAM;


--
-- Table structure for table 'oligo'
--

DROP TABLE IF EXISTS oligo;
CREATE TABLE oligo (
  name varchar(20) NOT NULL,
  oligo text NOT NULL,
  synthesis date NOT NULL,
  box int(10) unsigned default NULL,
  rank int(10) unsigned default NULL,
  freezer varchar(10) default NULL,
  barcode int(10) unsigned NOT NULL,
  notes text default NULL,
  PRIMARY KEY (barcode),
  UNIQUE KEY oligo_unique (freezer,box,rank)
) TYPE=MyISAM;

