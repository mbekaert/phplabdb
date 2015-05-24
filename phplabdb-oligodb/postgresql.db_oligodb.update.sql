-- phpLabDB OligoDB plugin 
--
-- Copyright 2003-2005 M.Bekaert
-- http://phplabdb.sourceforge.net
--
-- PostgreSQL database: db_oligodb
---------------------------------------------------------

--
-- Database: db_oligodb / Update
--

\connect db_oligodb

--
-- Table structure for table 'oligo'
--

ALTER TABLE oligo ADD COLUMN freezer character varying(10) DEFAULT NULL;
ALTER TABLE ONLY oligo DROP CONSTRAINT oligo_unique;
ALTER TABLE ONLY oligo
    ADD CONSTRAINT oligo_unique UNIQUE (freezer, box, rank);

--
-- Table structure for table 'couple'
--

ALTER TABLE ONLY couple1_fk DROP CONSTRAINT couple_pkey;
ALTER TABLE ONLY couple2_fk DROP CONSTRAINT couple_pkey;

