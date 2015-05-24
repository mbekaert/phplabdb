-- phpLabDB StrainDB plugin 
--
-- Copyright 2003-2004 M.Bekaert
-- http://phplabdb.sourceforge.net
--
-- PostgreSQL database: db_straindb
---------------------------------------------------------

--
-- Database: db_straindb
--

CREATE DATABASE db_straindb WITH OWNER apache;
\connect db_straindb

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

--
-- Table structure for table 'strain'
--

ALTER TABLE ONLY strain DROP CONSTRAINT strain_pkey;
DROP TABLE strain;
CREATE TABLE strain (
    barcode integer NOT NULL,
    name character varying(100) NOT NULL,
    released date NOT NULL,
    box integer NOT NULL,
    rank integer NOT NULL,
    species integer NOT NULL,
    strain_origin character varying(100),
    ploidy text,
    plasmid text,
    phenotype text,
    genotype text,
    medium text,
    pmid text,
    notes text
) WITHOUT OIDS;
ALTER TABLE ONLY strain
    ADD CONSTRAINT strain_pkey PRIMARY KEY (barcode);

--
-- Table structure for table 'species'
--

ALTER TABLE ONLY species DROP CONSTRAINT species_taxon_key;
ALTER TABLE ONLY species DROP CONSTRAINT species_name_key;
ALTER TABLE ONLY species DROP CONSTRAINT species_pkey;
DROP TABLE species;
CREATE TABLE species (
    id integer NOT NULL,
    name character varying(100) NOT NULL,
    taxon integer
);
ALTER TABLE ONLY species
    ADD CONSTRAINT species_pkey PRIMARY KEY (id);
ALTER TABLE ONLY species
    ADD CONSTRAINT species_name_key UNIQUE (name);
ALTER TABLE ONLY species
    ADD CONSTRAINT species_taxon_key UNIQUE (taxon);

--
-- data for table `species`
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

