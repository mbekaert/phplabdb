-- phpLabDB SeedDB plugin 
--
-- Copyright 2003-2004 M.Bekaert
-- http://phplabdb.sourceforge.net
--
-- PostgreSQL database: db_seeddb
---------------------------------------------------------

--
-- Database: db_seeddb
--

CREATE DATABASE db_seeddb WITH OWNER apache;
\connect db_seeddb

--
-- Alter integrity constraint
--

ALTER TABLE ONLY seeds DROP CONSTRAINT seeds_fkey;

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

INSERT INTO config (id, value) VALUES ('tips', 'true');
INSERT INTO config (id, value) VALUES ('barcode', 'true');

--
-- Table structure for table 'prospection'
--

ALTER TABLE ONLY prospection DROP CONSTRAINT prospection_pkey;
DROP TABLE prospection;
CREATE TABLE prospection (
    barcode integer NOT NULL,
    date date NOT NULL,
    prospection character varying(50) NOT NULL,
    species integer NOT NULL,
    vernacular character varying(50),
    country character varying(50) NOT NULL,
    locality character varying(50),
    latitude character varying(20),
    longitude character varying(20),
    altitude character varying(20),
    ethnos character varying(50),
    nature integer,
    form integer,
    size integer,
    distribution integer,
    weather integer,
    precocity integer,
    note text
);
ALTER TABLE ONLY prospection
    ADD CONSTRAINT prospection_pkey PRIMARY KEY (barcode);

--
-- Table structure for table 'seeds'
--

ALTER TABLE ONLY seeds DROP CONSTRAINT seeds_ref_key;
ALTER TABLE ONLY seeds DROP CONSTRAINT seeds_pkey;
DROP TABLE seeds;
CREATE TABLE seeds (
    barcode integer NOT NULL,
    ref character varying(50),
    date date NOT NULL,
    species integer NOT NULL,
    stock integer,
    father character varying(50),
    fbarcode integer,
    mother character varying(50),
    mbarcode integer,
    crosstype integer,
    note text
);
ALTER TABLE ONLY seeds
    ADD CONSTRAINT seeds_pkey PRIMARY KEY (barcode);
ALTER TABLE ONLY seeds
    ADD CONSTRAINT seeds_ref_key UNIQUE (ref);

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

--
-- Table structure for table 'crosstype'
--

ALTER TABLE ONLY crosstype DROP CONSTRAINT crosstype_pkey;
DROP TABLE crosstype;
CREATE TABLE crosstype (
    id integer NOT NULL,
    lang character(2) NOT NULL,
    legend character varying(50) NOT NULL
) WITHOUT OIDS;
ALTER TABLE ONLY crosstype
    ADD CONSTRAINT crosstype_pkey PRIMARY KEY (id, lang);

--
-- data for table `crosstype`
--

INSERT INTO crosstype (id, lang, legend) VALUES (1, 'en', 'Self/sib pollination');
INSERT INTO crosstype (id, lang, legend) VALUES (2, 'en', 'Cross');
INSERT INTO crosstype (id, lang, legend) VALUES (3, 'en', 'Androgenese');
INSERT INTO crosstype (id, lang, legend) VALUES (1, 'fr', 'Autofécondation');
INSERT INTO crosstype (id, lang, legend) VALUES (2, 'fr', 'Fécondation');
INSERT INTO crosstype (id, lang, legend) VALUES (3, 'fr', 'Androgenèse');

--
-- Table structure for table 'form'
--

ALTER TABLE ONLY form DROP CONSTRAINT form_pkey;
DROP TABLE form;
CREATE TABLE form (
    id integer NOT NULL,
    lang character(2) NOT NULL,
    legend character varying(50) NOT NULL
);
ALTER TABLE ONLY form
    ADD CONSTRAINT form_pkey PRIMARY KEY (id, lang);

--
-- data for table `form`
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
-- Table structure for table 'size'
--

ALTER TABLE ONLY size DROP CONSTRAINT size_pkey;
DROP TABLE size;
CREATE TABLE size (
    id integer NOT NULL,
    lang character(2) NOT NULL,
    legend character varying(50) NOT NULL
);
ALTER TABLE ONLY size
    ADD CONSTRAINT size_pkey PRIMARY KEY (id, lang);

--
-- data for table `size`
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
-- Table structure for table 'distribution'
--

ALTER TABLE ONLY  DROP CONSTRAINT distribution_pkey;
DROP TABLE distribution;
CREATE TABLE distribution (
    id integer NOT NULL,
    lang character(2) NOT NULL,
    legend character varying(50) NOT NULL
) WITHOUT OIDS;
ALTER TABLE ONLY distribution
    ADD CONSTRAINT distribution_pkey PRIMARY KEY (id, lang);

--
-- data for table `distribution`
--

INSERT INTO distribution (id, lang, legend) VALUES (1, 'en', 'Individual scattered plants');
INSERT INTO distribution (id, lang, legend) VALUES (2, 'en', 'Many slusters of plants');
INSERT INTO distribution (id, lang, legend) VALUES (3, 'en', 'Dense and uniform vevegation');
INSERT INTO distribution (id, lang, legend) VALUES (1, 'fr', 'Pieds individuels dispersés');
INSERT INTO distribution (id, lang, legend) VALUES (2, 'fr', 'Nombreuses touffes');
INSERT INTO distribution (id, lang, legend) VALUES (3, 'fr', 'Végétation dense et uniforme');

--
-- Table structure for table 'nature'
--

ALTER TABLE ONLY  DROP CONSTRAINT nature_pkey;
DROP TABLE nature;
CREATE TABLE nature (
    id integer NOT NULL,
    lang character(2) NOT NULL,
    legend character varying(50) NOT NULL
) WITHOUT OIDS;
ALTER TABLE ONLY nature
    ADD CONSTRAINT nature_pkey PRIMARY KEY (id, lang);

--
-- data for table `nature`
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

ALTER TABLE ONLY  DROP CONSTRAINT precocity_pkey;
DROP TABLE precocity;
CREATE TABLE precocity (
    id integer NOT NULL,
    lang character(2) NOT NULL,
    legend character varying(50) NOT NULL
) WITHOUT OIDS;
ALTER TABLE ONLY precocity
    ADD CONSTRAINT precocity_pkey PRIMARY KEY (id, lang);

--
-- data for table `precocity`
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
-- Table structure for table 'weather'
--

ALTER TABLE ONLY  DROP CONSTRAINT weather_pkey;
DROP TABLE weather;
CREATE TABLE weather (
    id integer NOT NULL,
    lang character(2) NOT NULL,
    legend character varying(50) NOT NULL
) WITHOUT OIDS;
ALTER TABLE ONLY weather
    ADD CONSTRAINT weather_pkey PRIMARY KEY (id, lang);

--
-- data for table `weather`
--

INSERT INTO weather (id, lang, legend) VALUES (1, 'en', 'Rain');
INSERT INTO weather (id, lang, legend) VALUES (2, 'en', 'Irrigated');
INSERT INTO weather (id, lang, legend) VALUES (1, 'fr', 'Pluviale');
INSERT INTO weather (id, lang, legend) VALUES (2, 'fr', 'Irriguée');
INSERT INTO weather (id, lang, legend) VALUES (3, 'fr', 'Fluviale');
INSERT INTO weather (id, lang, legend) VALUES (3, 'en', 'River side');
