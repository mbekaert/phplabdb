-- phpLabDB PlasmidDB plugin 
--
-- Copyright 2004 M.Bekaert
-- http://phplabdb.sourceforge.net
--
-- PostgreSQL database: db_plasmiddb
---------------------------------------------------------

--
-- Database: db_plasmiddb
--

CREATE DATABASE db_plasmiddb WITH OWNER apache;
\connect db_plasmiddb

--
-- Alter integrity constraints
--

ALTER TABLE ONLY plasmid DROP CONSTRAINT plasmid_fkey;
ALTER TABLE ONLY seq DROP CONSTRAINT seq_fkey;
ALTER TABLE ONLY map DROP CONSTRAINT map_fkey;
ALTER TABLE ONLY alias DROP CONSTRAINT alias_fkey;

--
-- Table structure for table 'config'
--

ALTER TABLE ONLY config DROP CONSTRAINT config_pkey;
DROP TABLE config;
CREATE TABLE config (
    id character varying(20) NOT NULL,
    value character varying(50)
) WITHOUT OIDS;
ALTER TABLE ONLY config
    ADD CONSTRAINT config_pkey PRIMARY KEY (id);

--
-- data for table `config`
--

INSERT INTO config (id, value) VALUES ('barcode', 'true');
INSERT INTO config (id, value) VALUES ('primer', '10');

--
-- Table structure for table 'prototype
--

ALTER TABLE ONLY prototype DROP CONSTRAINT prototype_pkey;
DROP TABLE prototype;
CREATE TABLE prototype (
    name character varying(100) NOT NULL,
    length integer,
    circ boolean DEFAULT 't',
    ascendant character varying(100),
    dna character(1) DEFAULT 'd',
    pmid text,
    selection text,
    replication text,
    plasmid boolean DEFAULT 'f',
    created date,
    notes text
) WITHOUT OIDS;
ALTER TABLE ONLY prototype
    ADD CONSTRAINT prototype_pkey PRIMARY KEY (name);

--
-- Table structure for table 'plasmid'
--

ALTER TABLE ONLY plasmid DROP CONSTRAINT home_pkey;
DROP TABLE plasmid;
CREATE TABLE plasmid (
    barcode integer NOT NULL,
    name character varying(100) NOT NULL,
    released date,
    box integer NOT NULL,
    rank integer NOT NULL,
    conditioning text NOT NULL,
    preparation text,
    conc double precision
) WITHOUT OIDS;
ALTER TABLE ONLY plasmid
    ADD CONSTRAINT plasmid_pkey PRIMARY KEY (barcode);
ALTER TABLE ONLY plasmid
    ADD CONSTRAINT plasmid_fkey FOREIGN KEY (name) REFERENCES prototype (name);

--
-- Table structure for table 'map'
--

ALTER TABLE ONLY map DROP CONSTRAINT map_pkey;
DROP TABLE map;
CREATE TABLE map (
    name character varying(100) NOT NULL,
    markers text,
    enzymes text
) WITHOUT OIDS;
ALTER TABLE ONLY map
    ADD CONSTRAINT map_pkey PRIMARY KEY (name);
ALTER TABLE ONLY map
    ADD CONSTRAINT map_fkey FOREIGN KEY (name) REFERENCES prototype (name);

--
-- Table structure for table 'seq'
--

ALTER TABLE ONLY seq DROP CONSTRAINT seq_pkey;
DROP TABLE seq;
CREATE TABLE seq (
    name character varying(100) NOT NULL,
    seq text NOT NULL
) WITHOUT OIDS;    
ALTER TABLE ONLY seq
    ADD CONSTRAINT seq_pkey PRIMARY KEY (name);
ALTER TABLE ONLY seq
    ADD CONSTRAINT seq_fkey FOREIGN KEY (name) REFERENCES prototype (name);

--
-- Table structure for table 'alias'
--

ALTER TABLE ONLY alias DROP CONSTRAINT alias_pkey;
DROP TABLE alias;
CREATE TABLE alias (
    name character varying(100) NOT NULL,
    alias character varying(100) NOT NULL
) WITHOUT OIDS;
ALTER TABLE ONLY alias
    ADD CONSTRAINT alias_pkey PRIMARY KEY (alias);
ALTER TABLE ONLY alias
    ADD CONSTRAINT alias_fkey FOREIGN KEY (name) REFERENCES prototype (name);

--
-- Table structure for table 'conditioning'
--

ALTER TABLE ONLY conditioning DROP CONSTRAINT conditioning_pkey;
DROP TABLE conditioning;
CREATE TABLE conditioning (
    id integer NOT NULL,
    lang character(2) NOT NULL,
    legend character varying(100) NOT NULL
) WITHOUT OIDS;
ALTER TABLE ONLY conditioning
    ADD CONSTRAINT conditioning_pkey PRIMARY KEY (id, lang);

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

ALTER TABLE ONLY preparation DROP CONSTRAINT preparation_pkey;
DROP TABLE preparation;
CREATE TABLE preparation (
    id integer NOT NULL,
    lang character(2) NOT NULL,
    legend character varying(100) NOT NULL
) WITHOUT OIDS;
ALTER TABLE ONLY preparation
    ADD CONSTRAINT preparation_pkey PRIMARY KEY (id, lang);

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
-- Table structure for table 'selection'
--

ALTER TABLE ONLY selection DROP CONSTRAINT selection_pkey;
DROP TABLE selection;
CREATE TABLE selection (
    organism character varying(100) NOT NULL,
    lang character(2) NOT NULL,
    legend text
) WITHOUT OIDS;
ALTER TABLE ONLY selection
    ADD CONSTRAINT selection_pkey PRIMARY KEY (organism, lang);

--
-- Data for table `selection`
--

INSERT INTO selection (organism,lang,legend) VALUES ('E. coli','en','Ampicilline|Kanamycine|Chloramphenicol|Tetracycline');
INSERT INTO selection (organism,lang,legend) VALUES ('S. cerevisiae','en','Histidine|Leucine|Tryptophane|Uracile');
INSERT INTO selection (organism,lang,legend) VALUES ('M. musculus','en','');
INSERT INTO selection (organism,lang,legend) VALUES ('E. coli','fr','Ampicilline|Kanamycine|Chloramphenicol|Tetracycline');
INSERT INTO selection (organism,lang,legend) VALUES ('S. cerevisiae','fr','Histidine|Leucine|Tryptophane|Uracile');
INSERT INTO selection (organism,lang,legend) VALUES ('M. musculus','fr','');

--
-- Table structure for table 'replication'
--

ALTER TABLE ONLY replication DROP CONSTRAINT replication_pkey;
DROP TABLE replication;
CREATE TABLE replication (
    organism character varying(100) NOT NULL,
    lang character(2) NOT NULL,
    legend text
) WITHOUT OIDS;
ALTER TABLE ONLY replication
    ADD CONSTRAINT replication_pkey PRIMARY KEY (organism, lang);

--
-- Data for table `replication`
--

INSERT INTO replication (organism,lang,legend) VALUES ('E. coli','en','ORI E.coli');
INSERT INTO replication (organism,lang,legend) VALUES ('S. cerevisiae','en','CEN1|CEN2|CEN3|CEN4');
INSERT INTO replication (organism,lang,legend) VALUES ('M. musculus','en','');
INSERT INTO replication (organism,lang,legend) VALUES ('E. coli','fr','ORI E.coli');
INSERT INTO replication (organism,lang,legend) VALUES ('S. cerevisiae','fr','CEN1|CEN2|CEN3|CEN4');
INSERT INTO replication (organism,lang,legend) VALUES ('M. musculus','fr','');
