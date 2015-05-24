-- phpLabDB PlasmidDB plugin
--
-- Copyright 2004 M.Bekaert
-- http://phplabdb.sourceforge.net
--
-- MySQL database: db_plasmiddb
---------------------------------------------------------

--
-- Database: db_plasmiddb
--

USE db_plasmiddb;


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

INSERT INTO config (id, value) VALUES ('barcode', 'true');
INSERT INTO config (id, value) VALUES ('primer', '10');


--
-- Table structure for table 'prototype'
--

DROP TABLE IF EXISTS prototype;
CREATE TABLE prototype (
  name varchar(100) NOT NULL,
  length int(10) unsigned default NULL,
  circ enum('f','t') default 't',
  ascendant varchar(100) default NULL,
  dna char(1) default 'd',
  pmid text,
  selection text,
  replication text,
  plasmid enum('f','t') default 'f',
  created date default NULL,
  notes text,
  PRIMARY KEY (name)
) TYPE=MyISAM;


--
-- Table structure for table 'plasmid'
--

DROP TABLE IF EXISTS plasmid;
CREATE TABLE plasmid (
  barcode int(10) unsigned NOT NULL,
  name varchar(100) NOT NULL,
  released date default NULL,
  box int(10) unsigned NOT NULL,
  rank int(10) unsigned  NOT NULL,
  conditioning text NOT NULL,
  preparation text default NULL,
  conc double default NULL,
  PRIMARY KEY (barcode)
) TYPE=MyISAM;


--
-- Table structure for table 'map'
--

DROP TABLE IF EXISTS map;
CREATE TABLE map (
  name varchar(100) NOT NULL,
  markers text,
  enzymes text,
  PRIMARY KEY (name)
) TYPE=MyISAM;


--
-- Table structure for table 'seq'
--

DROP TABLE IF EXISTS seq;
CREATE TABLE seq (
  name varchar(100) NOT NULL,
  seq text NOT NULL,
  PRIMARY KEY (name)
) TYPE=MyISAM;


--
-- Table for table 'alias'
--

DROP TABLE IF EXISTS alias;
CREATE TABLE alias (
  name varchar(100) NOT NULL,
  alias varchar(100) NOT NULL,
  PRIMARY KEY (alias)
) TYPE=MyISAM;


--
-- Table for table 'conditioning'
--

DROP TABLE IF EXISTS conditioning;
CREATE TABLE conditioning (
  id int(10) unsigned NOT NULL,
  lang char(2) NOT NULL,
  legend varchar(100) NOT NULL,
  PRIMARY KEY (id,lang)
) TYPE=MyISAM;


--
-- Data for table `conditioning`
--

INSERT INTO conditioning (id,lang,legend) VALUES (1,'en','DNA');
INSERT INTO conditioning (id,lang,legend) VALUES (2,'en','RNA');
INSERT INTO conditioning (id,lang,legend) VALUES (3,'en','E. coli DH5 alpha');
INSERT INTO conditioning (id,lang,legend) VALUES (4,'en','S. cerevisiae FY 1679-18B');
INSERT INTO conditioning (id,lang,legend) VALUES (1,'fr','ADN');
INSERT INTO conditioning (id,lang,legend) VALUES (2,'fr','ARN');
INSERT INTO conditioning (id,lang,legend) VALUES (3,'fr','E. coli DH5 alpha');
INSERT INTO conditioning (id,lang,legend) VALUES (4,'fr','S. cerevisiae FY 1679-18B');


--
-- Table structure for table 'preparation'
--

DROP TABLE IF EXISTS preparation;
CREATE TABLE preparation (
  id int(10) unsigned NOT NULL,
  lang char(2) NOT NULL,
  legend varchar(100) NOT NULL,
  PRIMARY KEY (id,lang)
) TYPE=MyISAM;


--
-- Data for table `preparation`
--

INSERT INTO preparation (id,lang,legend) VALUES (1,'en','Miniprep');
INSERT INTO preparation (id,lang,legend) VALUES (2,'en','Midiprep');
INSERT INTO preparation (id,lang,legend) VALUES (3,'en','Maxiprep');
INSERT INTO preparation (id,lang,legend) VALUES (4,'en','Transformation LiAC');
INSERT INTO preparation (id,lang,legend) VALUES (5,'en','Transformation Litris');
INSERT INTO preparation (id,lang,legend) VALUES (6,'en','Transformation LiAC/DTT');
INSERT INTO preparation (id,lang,legend) VALUES (7,'en','Electroporation');
INSERT INTO preparation (id,lang,legend) VALUES (1,'fr','Miniprep');
INSERT INTO preparation (id,lang,legend) VALUES (2,'fr','Midiprep');
INSERT INTO preparation (id,lang,legend) VALUES (3,'fr','Maxiprep');
INSERT INTO preparation (id,lang,legend) VALUES (4,'fr','Transformation LiAC');
INSERT INTO preparation (id,lang,legend) VALUES (5,'fr','Transformation Litris');
INSERT INTO preparation (id,lang,legend) VALUES (6,'fr','Transformation LiAC/DTT');
INSERT INTO preparation (id,lang,legend) VALUES (7,'fr','Electroporation');


--
-- Table structure for table 'replication'
--

DROP TABLE IF EXISTS replication;
CREATE TABLE replication (
  organism varchar(100) NOT NULL,
  lang char(2) NOT NULL,
  legend text NOT NULL,
  PRIMARY KEY (organism,lang)
) TYPE=MyISAM;


--
-- Data for table `replication`
--

INSERT INTO replication (organism,lang,legend) VALUES ('E. coli','en','ORI E.coli');
INSERT INTO replication (organism,lang,legend) VALUES ('S. cerevisiae','en','CEN1|CEN2|CEN3|CEN4');
INSERT INTO replication (organism,lang,legend) VALUES ('M. musculus','en','');
INSERT INTO replication (organism,lang,legend) VALUES ('E. coli','fr','ORI E.coli');
INSERT INTO replication (organism,lang,legend) VALUES ('S. cerevisiae','fr','CEN1|CEN2|CEN3|CEN4');
INSERT INTO replication (organism,lang,legend) VALUES ('M. musculus','fr','');


--
-- Table structure for table 'selection'
--

DROP TABLE IF EXISTS selection;
CREATE TABLE selection (
  organism varchar(100) NOT NULL,
  lang char(2) NOT NULL,
  legend text NOT NULL,
  PRIMARY KEY (organism,lang)
) TYPE=MyISAM;


--
-- Data for table `selection`
--

INSERT INTO selection (organism,lang,legend) VALUES ('E. coli','en','Ampicilline|Kanamycine|Chloramphenicol|Tetracycline');
INSERT INTO selection (organism,lang,legend) VALUES ('S. cerevisiae','en','Histidine|Leucine|Tryptophane|Uracile');
INSERT INTO selection (organism,lang,legend) VALUES ('M. musculus','en','');
INSERT INTO selection (organism,lang,legend) VALUES ('E. coli','fr','Ampicilline|Kanamycine|Chloramphenicol|Tetracycline');
INSERT INTO selection (organism,lang,legend) VALUES ('S. cerevisiae','fr','Histidine|Leucine|Tryptophane|Uracile');
INSERT INTO selection (organism,lang,legend) VALUES ('M. musculus','fr','');

