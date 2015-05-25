-- phplabdb.v2.devel
--
-- Copyright 2006 M.Bekaert
--
-- PostgreSQL database: phplabdb.v2 // oligoml
-- --------------------------------------------------------

ALTER TABLE ONLY oligoml_pair DROP CONSTRAINT oligoml_pair_reverse;
ALTER TABLE ONLY oligoml_pair DROP CONSTRAINT oligoml_pair_forward;
ALTER TABLE ONLY oligoml_pair DROP CONSTRAINT oligoml_pair_author;
ALTER TABLE ONLY oligoml_oligo DROP CONSTRAINT oligoml_oligo_author;
ALTER TABLE ONLY oligoml_pair DROP CONSTRAINT oligoml_pair_pkey;
ALTER TABLE ONLY oligoml_oligo DROP CONSTRAINT oligoml_oligo_pkey;
DROP TABLE oligoml_pair;
DROP TABLE oligoml_oligo;


--
-- Name: oligoml_oligo; Type: TABLE
--

CREATE TABLE oligoml_oligo (
    prefix integer NOT NULL,
    id integer NOT NULL,
    "release" integer DEFAULT 1 NOT NULL,
    name character varying(32) NOT NULL,
    "sequence" character varying(128) NOT NULL,
    modification text,
    box character varying(64) NOT NULL,
    freezer character varying(64),
    rank character varying(64),
    comments text,
    "type" integer,
    design integer,
    program character varying(64),
    version double precision,
    design_comments text,
    reference text,
    updated timestamp without time zone DEFAULT now() NOT NULL,
    author character varying(32) NOT NULL
);


--
-- Name: oligoml_pair; Type: TABLE
--

CREATE TABLE oligoml_pair (
    prefix integer NOT NULL,
    id integer NOT NULL,
    "release" integer DEFAULT 1 NOT NULL,
    forward_prefix integer NOT NULL,
    forward_id integer NOT NULL,
    reverse_prefix integer NOT NULL,
    reverse_id integer NOT NULL,
    speciesid integer,
    species character varying(128) NOT NULL,
    geneid integer,
    locus character varying(64),
    amplicon integer,
    sequenceid character varying(64),
    "location" character varying(64),
    pcr text,
    buffer text,
    next_pair_prefix integer,
    next_pair_id integer,
    comments text,
    reference text,
    updated timestamp without time zone DEFAULT now() NOT NULL,
    author character varying(32) NOT NULL
);


--
-- Name: oligoml_oligo_pkey; Type: CONSTRAINT
--

ALTER TABLE ONLY oligoml_oligo
    ADD CONSTRAINT oligoml_oligo_pkey PRIMARY KEY (prefix, id);


--
-- Name: oligoml_pair_pkey; Type: CONSTRAINT
--

ALTER TABLE ONLY oligoml_pair
    ADD CONSTRAINT oligoml_pair_pkey PRIMARY KEY (prefix, id);


--
-- Name: oligoml_oligo_author; Type: FK CONSTRAINT
--

ALTER TABLE ONLY oligoml_oligo
    ADD CONSTRAINT oligoml_oligo_author FOREIGN KEY (author) REFERENCES users(username);


--
-- Name: oligoml_pair_author; Type: FK CONSTRAINT
--

ALTER TABLE ONLY oligoml_pair
    ADD CONSTRAINT oligoml_pair_author FOREIGN KEY (author) REFERENCES users(username);


--
-- Name: oligoml_pair_forward; Type: FK CONSTRAINT
--

ALTER TABLE ONLY oligoml_pair
    ADD CONSTRAINT oligoml_pair_forward FOREIGN KEY (forward_prefix, forward_id) REFERENCES oligoml_oligo (prefix, id);


--
-- Name: oligoml_pair_reverse; Type: FK CONSTRAINT
--

ALTER TABLE ONLY oligoml_pair
    ADD CONSTRAINT oligoml_pair_reverse FOREIGN KEY (reverse_prefix, reverse_id) REFERENCES oligoml_oligo (prefix, id);


--
-- Name: oligoml_oligo; Type: ACL
--

GRANT ALL ON TABLE oligoml_oligo TO www;


--
-- Name: oligoml_pair; Type: ACL
--

GRANT ALL ON TABLE oligoml_pair TO www;


--
-- Done
--
