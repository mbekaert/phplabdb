-- phpLabDB OligoDB plugin 
--
-- Copyright 2003-2005 M.Bekaert
-- http://phplabdb.sourceforge.net
--
-- PostgreSQL database: db_oligodb
---------------------------------------------------------

--
-- Database: db_oligodb
--

CREATE DATABASE db_oligodb WITH OWNER apache;
\connect db_oligodb

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

INSERT INTO config (id, value) VALUES ('pair', 'true');
INSERT INTO config (id, value) VALUES ('barcode', 'true');
INSERT INTO config (id, value) VALUES ('note', 'true');

--
-- Table structure for table 'oligo'
--

ALTER TABLE ONLY oligo DROP CONSTRAINT oligo_pkey;
ALTER TABLE ONLY oligo DROP CONSTRAINT oligo_name;
ALTER TABLE ONLY oligo DROP CONSTRAINT oligo_unique;
DROP TABLE oligo;
CREATE TABLE oligo (
    name character varying(20) NOT NULL,
    oligo text NOT NULL,
    synthesis date NOT NULL,
    box integer,
    rank integer,
    freezer character varying(10) DEFAULT NULL,		
    barcode integer NOT NULL,
    notes text
) WITHOUT OIDS;
ALTER TABLE ONLY oligo
    ADD CONSTRAINT oligo_pkey PRIMARY KEY (barcode);
ALTER TABLE ONLY oligo
    ADD CONSTRAINT oligo_name UNIQUE (name);
ALTER TABLE ONLY oligo
    ADD CONSTRAINT oligo_unique UNIQUE (freezer, box, rank);

		
--
-- Table structure for table 'couple'
--

ALTER TABLE ONLY couple_pkey DROP CONSTRAINT couple_pkey;
DROP TABLE couple;
CREATE TABLE couple (
    name1 character varying(20) NOT NULL,
    name2 character varying(20) NOT NULL,
    notes text
) WITHOUT OIDS;
ALTER TABLE ONLY couple
    ADD CONSTRAINT couple_pkey PRIMARY KEY (name1, name2);

