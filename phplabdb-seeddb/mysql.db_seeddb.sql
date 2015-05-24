-- phpLabDB SeedDB plugin
--
-- Copyright 2003-2004 M.Bekaert
-- http://phplabdb.sourceforge.net
--
-- MySQL database: db_seeddb
---------------------------------------------------------

--
-- Current Database: db_seeddb
--

USE db_seeddb;


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
-- Data for table 'config'
--

INSERT INTO config (id, value) VALUES ('tips', 'true');
INSERT INTO config (id, value) VALUES ('barcode', 'true');


--
-- Table structure for table 'prospection'
--

DROP TABLE IF EXISTS prospection;
CREATE TABLE prospection (
  barcode int(10) unsigned NOT NULL,
  date date NOT NULL,
  prospection varchar(50) NOT NULL,
  species int(10) unsigned NOT NULL,
  vernacular varchar(50) default NULL,
  country varchar(50) NOT NULL,
  locality varchar(50) default NULL,
  latitude varchar(20) default NULL,
  longitude varchar(20) default NULL,
  altitude varchar(20) default NULL,
  ethnos varchar(50) default NULL,
  nature int(10) unsigned default NULL,
  form int(10) unsigned default NULL,
  size int(10) unsigned default NULL,
  distribution int(10) unsigned default NULL,
  weather int(10) unsigned default NULL,
  precocity int(10) unsigned default NULL,
  note text,
  PRIMARY KEY (barcode)
) TYPE=MyISAM;


--
-- Table structure for table 'seeds'
--

DROP TABLE IF EXISTS seeds;
CREATE TABLE seeds (
  barcode int(10) unsigned NOT NULL,
  ref varchar(50) default NULL,
  date date NOT NULL,
  species int(10) unsigned NOT NULL,
  stock int(10) unsigned default NULL,
  father varchar(50) default NULL,
  fbarcode int(10) unsigned default NULL,
  mother varchar(50) default NULL,
  mbarcode int(10) unsigned default NULL,
  crosstype int(10) unsigned default NULL,
  note text,
  PRIMARY KEY (barcode),
  UNIQUE KEY ref (ref)
) TYPE=MyISAM;


--
-- Table structure for table 'species'
--

DROP TABLE IF EXISTS species;
CREATE TABLE species (
  id int(10) unsigned NOT NULL,
  name varchar(100) NOT NULL,
  taxon int(10) unsigned default NULL,
  PRIMARY KEY (id),
  UNIQUE KEY legend (name)
) TYPE=MyISAM;


--
-- Data for table 'species'
--

INSERT INTO species (id, name, taxon) VALUES (0,'Hydrib',NULL);
INSERT INTO species (id, name, taxon) VALUES (1,'Anopheles gambiae',7165);
INSERT INTO species (id, name, taxon) VALUES (2,'Arabidopsis thaliana',3701);
INSERT INTO species (id, name, taxon) VALUES (3,'Avena sativa',4498);
INSERT INTO species (id, name, taxon) VALUES (4,'Bos taurus',9913);
INSERT INTO species (id, name, taxon) VALUES (5,'Caenorhabditis elegans',6239);
INSERT INTO species (id, name, taxon) VALUES (6,'Chlamydomonas reinhardtii',3055);
INSERT INTO species (id, name, taxon) VALUES (7,'Danio rerio',7955);
INSERT INTO species (id, name, taxon) VALUES (8,'Dictyostelium discoideum',44689);
INSERT INTO species (id, name, taxon) VALUES (9,'Drosophila melanogaster',7227);
INSERT INTO species (id, name, taxon) VALUES (10,'Encephalitozoon cuniculi',6035);
INSERT INTO species (id, name, taxon) VALUES (11,'Escherichia coli',562);
INSERT INTO species (id, name, taxon) VALUES (12,'Glycine max',3847);
INSERT INTO species (id, name, taxon) VALUES (13,'Guillardia theta',55529);
INSERT INTO species (id, name, taxon) VALUES (14,'Hepatitis C virus',11103);
INSERT INTO species (id, name, taxon) VALUES (15,'Homo sapiens',9606);
INSERT INTO species (id, name, taxon) VALUES (16,'Hordeum vulgare',4513);
INSERT INTO species (id, name, taxon) VALUES (17,'Leishmania major',5664);
INSERT INTO species (id, name, taxon) VALUES (18,'Lycopersicon esculentum',4081);
INSERT INTO species (id, name, taxon) VALUES (19,'Mus musculus',10090);
INSERT INTO species (id, name, taxon) VALUES (20,'Mycoplasma pneumoniae',2104);
INSERT INTO species (id, name, taxon) VALUES (21,'Oryza sativa',4530);
INSERT INTO species (id, name, taxon) VALUES (22,'Pennisetum glaucum',4543);
INSERT INTO species (id, name, taxon) VALUES (23,'Plasmodium falciparum',5833);
INSERT INTO species (id, name, taxon) VALUES (24,'Pneumocystis carinii',4754);
INSERT INTO species (id, name, taxon) VALUES (25,'Rattus norvegicus',10116);
INSERT INTO species (id, name, taxon) VALUES (26,'Saccharomyces cerevisiae',4932);
INSERT INTO species (id, name, taxon) VALUES (27,'Schizosaccharomyces pombe',4896);
INSERT INTO species (id, name, taxon) VALUES (28,'Takifugu rubripes',31033);
INSERT INTO species (id, name, taxon) VALUES (29,'Triticum aestivum',4565);
INSERT INTO species (id, name, taxon) VALUES (30,'Xenopus laevis',8355);
INSERT INTO species (id, name, taxon) VALUES (31,'Zea mays',4577);


--
-- Table structure for table 'crosstype'
--

DROP TABLE IF EXISTS crosstype;
CREATE TABLE crosstype (
  id int(10) unsigned NOT NULL,
  lang char(2) NOT NULL,
  legend varchar(50) NOT NULL,
  PRIMARY KEY (id,lang)
) TYPE=MyISAM;


--
-- Data for table 'crosstype'
--

INSERT INTO crosstype (id, lang, legend) VALUES (1, 'en', 'Self/sib pollination');
INSERT INTO crosstype (id, lang, legend) VALUES (2, 'en', 'Cross');
INSERT INTO crosstype (id, lang, legend) VALUES (3, 'en', 'Androgenese');
INSERT INTO crosstype (id, lang, legend) VALUES (1, 'fr', 'Autofécondation');
INSERT INTO crosstype (id, lang, legend) VALUES (2, 'fr', 'Fécondation');
INSERT INTO crosstype (id, lang, legend) VALUES (3, 'fr', 'Androgenèse');


--
-- Table structure for table 'distribution'
--

DROP TABLE IF EXISTS distribution;
CREATE TABLE distribution (
  id int(10) unsigned NOT NULL,
  lang char(2) NOT NULL,
  legend varchar(50) NOT NULL,
  PRIMARY KEY (id,lang)
) TYPE=MyISAM;


--
-- Data for table 'distribution'
--

INSERT INTO distribution (id, lang, legend) VALUES (1, 'en', 'Individual scattered plants');
INSERT INTO distribution (id, lang, legend) VALUES (2, 'en', 'Many slusters of plants');
INSERT INTO distribution (id, lang, legend) VALUES (3, 'en', 'Dense and uniform vevegation');
INSERT INTO distribution (id, lang, legend) VALUES (1, 'fr', 'Pieds individuels dispersés');
INSERT INTO distribution (id, lang, legend) VALUES (2, 'fr', 'Nombreuses touffes');
INSERT INTO distribution (id, lang, legend) VALUES (3, 'fr', 'Végétation dense et uniforme');


--
-- Table structure for table 'form'
--

DROP TABLE IF EXISTS form;
CREATE TABLE form (
  id int(10) unsigned NOT NULL,
  lang char(2) NOT NULL,
  legend varchar(50) NOT NULL,
  PRIMARY KEY (id,lang)
) TYPE=MyISAM;


--
-- Data for table 'form'
--

INSERT INTO form (id, lang, legend) VALUES (1, 'en', 'Wild botanical form');
INSERT INTO form (id, lang, legend) VALUES (2, 'en', 'Primitive cultivar');
INSERT INTO form (id, lang, legend) VALUES (3, 'en', 'In-bred line');
INSERT INTO form (id, lang, legend) VALUES (4, 'en', 'Advanced Cultivar');
INSERT INTO form (id, lang, legend) VALUES (5, 'en', 'Hybrid form');
INSERT INTO form (id, lang, legend) VALUES (6, 'en', 'Botanic Garden Sample');
INSERT INTO form (id, lang, legend) VALUES (1, 'fr', 'Formes botaniques sauvages');
INSERT INTO form (id, lang, legend) VALUES (2, 'fr', 'Cultivar primitif');
INSERT INTO form (id, lang, legend) VALUES (3, 'fr', 'Lignée');
INSERT INTO form (id, lang, legend) VALUES (4, 'fr', 'Cultivar amelioré');
INSERT INTO form (id, lang, legend) VALUES (5, 'fr', 'Formes hybrides');
INSERT INTO form (id, lang, legend) VALUES (6, 'fr', 'Echantillon de jardin botanique');


--
-- Table structure for table 'nature'
--

DROP TABLE IF EXISTS nature;
CREATE TABLE nature (
  id int(10) unsigned NOT NULL,
  lang char(2) NOT NULL,
  legend varchar(50) NOT NULL,
  PRIMARY KEY  (id,lang)
) TYPE=MyISAM;


--
-- Data for table 'nature'
--

INSERT INTO nature (id, lang, legend) VALUES (1, 'en', 'Crest');
INSERT INTO nature (id, lang, legend) VALUES (2, 'en', 'Estuary');
INSERT INTO nature (id, lang, legend) VALUES (3, 'en', 'Valley slope');
INSERT INTO nature (id, lang, legend) VALUES (4, 'en', 'Mountainside');
INSERT INTO nature (id, lang, legend) VALUES (5, 'en', 'Plain');
INSERT INTO nature (id, lang, legend) VALUES (6, 'en', 'Plateau');
INSERT INTO nature (id, lang, legend) VALUES (7, 'en', 'Summit');
INSERT INTO nature (id, lang, legend) VALUES (8, 'en', 'Terrace');
INSERT INTO nature (id, lang, legend) VALUES (9, 'en', 'Valley bottom');
INSERT INTO nature (id, lang, legend) VALUES (10, 'en', 'Valley side');
INSERT INTO nature (id, lang, legend) VALUES (1, 'fr', 'Crête');
INSERT INTO nature (id, lang, legend) VALUES (2, 'fr', 'Estuaire');
INSERT INTO nature (id, lang, legend) VALUES (3, 'fr', 'Pente de vallée');
INSERT INTO nature (id, lang, legend) VALUES (4, 'fr', 'Montagne');
INSERT INTO nature (id, lang, legend) VALUES (5, 'fr', 'Plaine');
INSERT INTO nature (id, lang, legend) VALUES (6, 'fr', 'Plateau');
INSERT INTO nature (id, lang, legend) VALUES (7, 'fr', 'Sommet');
INSERT INTO nature (id, lang, legend) VALUES (8, 'fr', 'Terrasse');
INSERT INTO nature (id, lang, legend) VALUES (9, 'fr', 'Fond de vallée');
INSERT INTO nature (id, lang, legend) VALUES (10, 'fr', 'Côté de vallée');


--
-- Table structure for table 'precocity'
--

DROP TABLE IF EXISTS precocity;
CREATE TABLE precocity (
  id int(10) unsigned NOT NULL,
  lang char(2) NOT NULL,
  legend varchar(50) NOT NULL,
  PRIMARY KEY (id,lang)
) TYPE=MyISAM;


--
-- Data for table 'precocity'
--

INSERT INTO precocity (id, lang, legend) VALUES (1, 'en', 'Early');
INSERT INTO precocity (id, lang, legend) VALUES (2, 'en', 'Medium-early');
INSERT INTO precocity (id, lang, legend) VALUES (3, 'en', 'Medium-late');
INSERT INTO precocity (id, lang, legend) VALUES (4, 'en', 'Late');
INSERT INTO precocity (id, lang, legend) VALUES (1, 'fr', 'Précoce');
INSERT INTO precocity (id, lang, legend) VALUES (2, 'fr', 'Semi-précoce');
INSERT INTO precocity (id, lang, legend) VALUES (3, 'fr', 'Semi-tardif');
INSERT INTO precocity (id, lang, legend) VALUES (4, 'fr', 'Tardif');


--
-- Table structure for table 'size'
--

DROP TABLE IF EXISTS size;
CREATE TABLE size (
  id int(10) unsigned NOT NULL,
  lang char(2) NOT NULL,
  legend varchar(50) NOT NULL,
  PRIMARY KEY (id,lang)
) TYPE=MyISAM;


--
-- Data for table 'size'
--

INSERT INTO size (id, lang, legend) VALUES (1, 'en', 'From 10 to 50 plants');
INSERT INTO size (id, lang, legend) VALUES (2, 'en', 'From 50 to 100 plants');
INSERT INTO size (id, lang, legend) VALUES (3, 'en', 'From 100 to 500 plants');
INSERT INTO size (id, lang, legend) VALUES (4, 'en', 'From 500 to 1000 plants');
INSERT INTO size (id, lang, legend) VALUES (5, 'en', 'Above 1000 plants');
INSERT INTO size (id, lang, legend) VALUES (1, 'fr', 'De 10 à 50 pieds');
INSERT INTO size (id, lang, legend) VALUES (2, 'fr', 'De 50 à 100 pieds');
INSERT INTO size (id, lang, legend) VALUES (3, 'fr', 'De 100 à 500 pieds');
INSERT INTO size (id, lang, legend) VALUES (4, 'fr', 'De 500 à 1000 pieds');
INSERT INTO size (id, lang, legend) VALUES (5, 'fr', 'Supérieur à 1000 pieds');


--
-- Table structure for table 'weather'
--

DROP TABLE IF EXISTS weather;
CREATE TABLE weather (
  id int(10) unsigned NOT NULL,
  lang char(2) NOT NULL,
  legend varchar(50) NOT NULL,
  PRIMARY KEY (id,lang)
) TYPE=MyISAM;


--
-- Data for table 'weather'
--

INSERT INTO weather (id, lang, legend) VALUES (1, 'en', 'Rain');
INSERT INTO weather (id, lang, legend) VALUES (2, 'en', 'Irrigated');
INSERT INTO weather (id, lang, legend) VALUES (1, 'fr', 'Pluviale');
INSERT INTO weather (id, lang, legend) VALUES (2, 'fr', 'Irriguée');
INSERT INTO weather (id, lang, legend) VALUES (3, 'fr', 'Fluviale');
INSERT INTO weather (id, lang, legend) VALUES (3, 'en', 'River side');

