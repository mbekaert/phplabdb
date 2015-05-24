-- phpLabDB core 
--
-- Copyright 2003-2005 M.Bekaert
-- http://phplabdb.sourceforge.net
--
-- PostgreSQL database: db_phplabdb
---------------------------------------------------------

--
-- Database: db_phplabdb
--

\connect db_phplabdb

--
-- Table structure for table 'membres'
--


ALTER TABLE ONLY membres DROP CONSTRAINT membres_pkey;
ALTER TABLE ONLY membres DROP CONSTRAINT membres_unique;
DROP TABLE  membres;
CREATE TABLE membres (
    login character varying(20) NOT NULL,
    password character varying(20) NOT NULL,
    email character varying(100) NOT NULL,
    status integer NOT NULL,
    id character varying(32),
    ip character varying(20)
) WITHOUT OIDS;
ALTER TABLE ONLY membres
    ADD CONSTRAINT membres_pkey PRIMARY KEY (login);
ALTER TABLE ONLY membres
    ADD CONSTRAINT membres_unique UNIQUE (email,id);


--
-- Data for table 'membres'
--

INSERT INTO membres VALUES ('admin','wlclj7rwa77a','root@localhost',2147483647,NULL,NULL);


--
-- Table structure for table 'organisation'
--

DROP TABLE organisation;
CREATE TABLE organisation (
  name character varying(50) NOT NULL,
  url character varying(100) NOT NULL,
  logo character varying(100) NOT NULL,
  welcome text
) WITHOUT OIDS;


--
-- Data for table 'organisation'
--

INSERT INTO organisation VALUES ('phpLabDB','http://phplabdb.sourceforge.net/','/images/header-phplabdb_logo.png','Welcome to phpLabDB');

