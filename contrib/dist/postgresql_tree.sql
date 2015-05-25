-- phplabdb.v2.devel
--
-- Copyright 2006-2007 M.Bekaert
--
-- PostgreSQL database: phplabdb.v2 // tree
-- --------------------------------------------------------

ALTER TABLE ONLY tree_taxonomy DROP CONSTRAINT tree_taxonomy_nomenclaturalcode;
ALTER TABLE ONLY tree_taxonomy DROP CONSTRAINT tree_taxonomy_author;
ALTER TABLE ONLY tree_taxonomy DROP CONSTRAINT tree_taxonomy_unique;
ALTER TABLE ONLY tree_taxonomy DROP CONSTRAINT tree_taxonomy_pkey;
ALTER TABLE ONLY tree_nomenclaturalcode DROP CONSTRAINT tree_nomenclaturalcode_pkey;
ALTER TABLE ONLY tree_translation DROP CONSTRAINT tree_translation_unique;
ALTER TABLE ONLY tree_translation DROP CONSTRAINT tree_translation_pkey;
ALTER TABLE ONLY tree_division DROP CONSTRAINT tree_division_pkey;
ALTER TABLE ONLY tree_division DROP CONSTRAINT tree_division_unique;
DROP TABLE tree_taxonomy;
DROP TABLE tree_nomenclaturalcode;
DROP TABLE tree_translation;
DROP TABLE tree_division;


--
-- Name: tree_division; Type: TABLE
--

CREATE TABLE tree_division (
    reference character varying(8) NOT NULL,
    name character varying(128) NOT NULL
);


--
-- Name: tree_translation; Type: TABLE
--

CREATE TABLE tree_translation (
    reference integer NOT NULL,
    name character varying(128) NOT NULL
) WITHOUT OIDS;


--
-- Name: tree_taxonomy; Type: TABLE
--

CREATE TABLE tree_taxonomy (
    scientificname character varying(128) NOT NULL,
    alias character varying(128),
    taxon text,
    division character varying(16) DEFAULT 'UNA'::character varying NOT NULL,
    taxonid integer,
    nomenclaturalcode character varying(8),
    tkingdom character varying(128),
    tphylum character varying(128),
    tclass character varying(128),
    torder character varying(128),
    tfamily character varying(128),
    ttribe character varying(128),
    tgenus character varying(128) NOT NULL,
    tspecies character varying(128) NOT NULL,
    tsubspecies character varying(128),
    commonname character varying(128),
    abbrivation character varying(16),
    comments text,
    updated timestamp without time zone DEFAULT CURRENT_TIMESTAMP NOT NULL,
    author character varying(32) NOT NULL
);


--
-- Name: tree_nomenclaturalcode; Type: TABLE
--

CREATE TABLE tree_nomenclaturalcode (
    name character varying(32) NOT NULL,
    description text NOT NULL
);


--
-- Data for Name: tree_nomenclaturalcode; Type: TABLE DATA
--

INSERT INTO tree_nomenclaturalcode VALUES ('ICBN', 'International Code of Botanical Nomenclature');
INSERT INTO tree_nomenclaturalcode VALUES ('ICZN', 'International Code of Zoological Nomenclature');
INSERT INTO tree_nomenclaturalcode VALUES ('BC', 'Bacteriological Code');
INSERT INTO tree_nomenclaturalcode VALUES ('ICNCP', 'International Code of Nomenclature for Cultivated Plants');
INSERT INTO tree_nomenclaturalcode VALUES ('ICTV', 'International Code of Virus Classification and Nomenclature');
INSERT INTO tree_nomenclaturalcode VALUES ('BioCode', 'Proposed international rules for the scientific names of organisms');


--
-- Data for Name: tree_translation; Type: TABLE DATA
--

INSERT INTO tree_translation VALUES (0, 'Unknown');
INSERT INTO tree_translation VALUES (1, 'Standard');
INSERT INTO tree_translation VALUES (2, 'Vertebrate Mitochondrial');
INSERT INTO tree_translation VALUES (3, 'Yeast Mitochondrial');
INSERT INTO tree_translation VALUES (4, 'Mold, Protozoan, Coelenterate Mito. and Myco/Spiroplasma');
INSERT INTO tree_translation VALUES (5, 'Invertebrate Mitochondrial');
INSERT INTO tree_translation VALUES (6, 'Ciliate Nuclear, Dasycladacean Nuclear, Hexamita Nuclear');
INSERT INTO tree_translation VALUES (9, 'Echinoderm Mitochondrial');
INSERT INTO tree_translation VALUES (10, 'Euploid Nuclear');
INSERT INTO tree_translation VALUES (11, 'Bacterial');
INSERT INTO tree_translation VALUES (12, 'Alternative Yeast Nuclear');
INSERT INTO tree_translation VALUES (13, 'Ascidian Mitochondrial');
INSERT INTO tree_translation VALUES (14, 'Flatworm Mitochondrial');
INSERT INTO tree_translation VALUES (15, 'Blepharisma Macronuclear');
INSERT INTO tree_translation VALUES (16, 'Chlorophycean Mitochondrial');
INSERT INTO tree_translation VALUES (21, 'Trematode Mitochondrial');


--
-- Data for Name: tree_division; Type: TABLE DATA
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
-- Name: tree_division_pkey; Type: CONSTRAINT
--

ALTER TABLE ONLY tree_division
    ADD CONSTRAINT tree_division_pkey PRIMARY KEY (reference);


--
-- Name: tree_division_unique; Type: CONSTRAINT
--
ALTER TABLE ONLY tree_division
    ADD CONSTRAINT tree_division_unique UNIQUE (name);


--
-- Name: tree_nomenclaturalcode_pkey; Type: CONSTRAINT
--

ALTER TABLE ONLY tree_nomenclaturalcode
    ADD CONSTRAINT tree_nomenclaturalcode_pkey PRIMARY KEY (name);


--
-- Name: tree_translation_pkey; Type: CONSTRAINT
--

ALTER TABLE ONLY tree_translation
    ADD CONSTRAINT tree_translation_pkey PRIMARY KEY (reference);


--
-- Name: tree_translation_unique; Type: CONSTRAINT
--

ALTER TABLE ONLY tree_translation
    ADD CONSTRAINT tree_translation_unique UNIQUE (name);


--
-- Name: tree_taxonomy_pkey; Type: CONSTRAINT
--

ALTER TABLE ONLY tree_taxonomy
    ADD CONSTRAINT tree_taxonomy_pkey PRIMARY KEY (scientificname);


--
-- Name: tree_taxonomy_unique; Type: CONSTRAINT
--

ALTER TABLE ONLY tree_taxonomy
    ADD CONSTRAINT tree_taxonomy_unique UNIQUE (abbrivation);


--
-- Name: tree_taxonomy_author; Type: CONSTRAINT
--

ALTER TABLE ONLY tree_taxonomy
    ADD CONSTRAINT tree_taxonomy_author FOREIGN KEY (author) REFERENCES users(username);


--
-- Name: tree_taxonomy_nomenclaturalcode; Type: CONSTRAINT
--

ALTER TABLE ONLY tree_taxonomy
    ADD CONSTRAINT tree_taxonomy_nomenclaturalcode FOREIGN KEY (nomenclaturalcode) REFERENCES tree_nomenclaturalcode(name);


--
-- Name: tree_taxonomy; Type: ACL
--

GRANT ALL ON TABLE tree_taxonomy TO www;


--
-- Name: tree_translation; Type: ACL
--

GRANT ALL ON TABLE tree_translation TO www;


--
-- Name: tree_nomenclaturalcode; Type: ACL
--

GRANT ALL ON TABLE tree_nomenclaturalcode TO www;


--
-- Name: tree_division; Type: ACL
--

GRANT ALL ON TABLE tree_division TO www;


--
-- Done
--
