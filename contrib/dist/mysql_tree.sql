-- phplabdb.v2.devel
--
-- Copyright 2006-2007 M.Bekaert
--
-- MySQL database: phplabdb.v2 // tree
-- --------------------------------------------------------

-- 
-- Drop tables
-- 

SET FOREIGN_KEY_CHECKS = 0;
DROP TABLE IF EXISTS tree_taxonomy;
DROP TABLE IF EXISTS tree_division;
DROP TABLE IF EXISTS tree_translation;
DROP TABLE IF EXISTS tree_nomenclaturalcode;
SET FOREIGN_KEY_CHECKS = 1;


-- 
-- Table structure for table `tree_division`
-- 

CREATE TABLE tree_division (
  reference varchar(8) NOT NULL,
  name varchar(32) NOT NULL,
  PRIMARY KEY (reference),
  UNIQUE KEY name (name)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 PACK_KEYS=1;


-- 
-- Dumping data for table `tree_division`
-- 

INSERT INTO tree_division VALUES ('BCT', 'bacteria');
INSERT INTO tree_division VALUES ('ENV', 'environmental sampling');
INSERT INTO tree_division VALUES ('INV', 'invertebrate');
INSERT INTO tree_division VALUES ('MAM', 'mammalia');
INSERT INTO tree_division VALUES ('PHG', 'bacteriophage');
INSERT INTO tree_division VALUES ('PLN', 'plant, fungy, and alga');
INSERT INTO tree_division VALUES ('PRI', 'primate');
INSERT INTO tree_division VALUES ('ROD', 'rodent');
INSERT INTO tree_division VALUES ('SYN', 'synthetic');
INSERT INTO tree_division VALUES ('VRL', 'virus');
INSERT INTO tree_division VALUES ('VRT', 'vertebrate');


-- 
-- Table structure for table `tree_translation`
-- 

CREATE TABLE tree_translation (
  reference int(11) NOT NULL,
  name varchar(128) NOT NULL,
  PRIMARY KEY (reference),
  UNIQUE KEY name (name)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 PACK_KEYS=1;


-- 
-- Dumping data for table `tree_translation`
--

INSERT INTO translation VALUES (0, 'Unknown');
INSERT INTO translation VALUES (1, 'Standard');
INSERT INTO translation VALUES (2, 'Vertebrate Mitochondrial');
INSERT INTO translation VALUES (3, 'Yeast Mitochondrial');
INSERT INTO translation VALUES (4, 'Mold, Protozoan, Coelenterate Mito. and Myco/Spiroplasma');
INSERT INTO translation VALUES (5, 'Invertebrate Mitochondrial');
INSERT INTO translation VALUES (6, 'Ciliate Nuclear, Dasycladacean Nuclear, Hexamita Nuclear');
INSERT INTO translation VALUES (9, 'Echinoderm Mitochondrial');
INSERT INTO translation VALUES (10, 'Euploid Nuclear');
INSERT INTO translation VALUES (11, 'Bacterial');
INSERT INTO translation VALUES (12, 'Alternative Yeast Nuclear');
INSERT INTO translation VALUES (13, 'Ascidian Mitochondrial');
INSERT INTO translation VALUES (14, 'Flatworm Mitochondrial');
INSERT INTO translation VALUES (15, 'Blepharisma Macronuclear');
INSERT INTO translation VALUES (16, 'Chlorophycean Mitochondrial');
INSERT INTO translation VALUES (21, 'Trematode Mitochondrial');


-- 
-- Table structure for table `tree_nomenclaturalcode`
-- 

CREATE TABLE tree_nomenclaturalcode (
  name varchar(32) NOT NULL,
  description text NOT NULL,
  PRIMARY KEY (name)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 PACK_KEYS=1;


-- 
-- Dumping data for table `tree_nomenclaturalcode`
-- 

INSERT INTO tree_nomenclaturalcode VALUES ('BC', 'Bacteriological Code');
INSERT INTO tree_nomenclaturalcode VALUES ('BioCode', 'Proposed international rules for the scientific names of organisms');
INSERT INTO tree_nomenclaturalcode VALUES ('ICBN', 'International Code of Botanical Nomenclature');
INSERT INTO tree_nomenclaturalcode VALUES ('ICNCP', 'International Code of Nomenclature for Cultivated Plants');
INSERT INTO tree_nomenclaturalcode VALUES ('ICTV', 'International Code of Virus Classification and Nomenclature');
INSERT INTO tree_nomenclaturalcode VALUES ('ICZN', 'International Code of Zoological Nomenclature');


-- 
-- Table structure for table `tree_taxonomy`
-- 

CREATE TABLE tree_taxonomy (
  scientificname varchar(128) NOT NULL,
  alias varchar(128) default NULL,
  taxon text,
  division varchar(16) NOT NULL default 'UNA',
  taxonid int(11) default NULL,
  nomenclaturalcode varchar(8) default NULL,
  tkingdom varchar(128) default NULL,
  tphylum varchar(128) default NULL,
  tclass varchar(128) default NULL,
  torder varchar(128) default NULL,
  tfamily varchar(128) default NULL,
  ttribe varchar(128) default NULL,
  tgenus varchar(128) NOT NULL,
  tspecies varchar(128) NOT NULL,
  tsubspecies varchar(128) default NULL,
  commonname varchar(128) default NULL,
  abbrivation varchar(16) default NULL,
  comments text,
  updated timestamp NOT NULL default CURRENT_TIMESTAMP,
  author varchar(32) NOT NULL,
  PRIMARY KEY (scientificname),
  UNIQUE KEY abbrivation (abbrivation)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 PACK_KEYS=1;


-- 
-- Constraints for table `tree_taxonomy`
-- 
ALTER TABLE `tree_taxonomy`
  ADD CONSTRAINT tree_taxonomy_author FOREIGN KEY (author) REFERENCES users (username),
  ADD CONSTRAINT tree_taxonomy_nomenclaturalcode FOREIGN KEY (nomenclaturalcode) REFERENCES tree_nomenclaturalcode (name);

