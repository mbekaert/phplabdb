-- phplabdb.v2.devel
--
-- Copyright 2006-2007 M.Bekaert
--
-- PostgreSQL database: phplabdb.v2
-- --------------------------------------------------------

ALTER TABLE ONLY users DROP CONSTRAINT users_unique;
ALTER TABLE ONLY users DROP CONSTRAINT users_pkey;
ALTER TABLE ONLY reference DROP CONSTRAINT reference_pkey;
DROP TABLE users;
DROP TABLE reference;


--
-- Name: reference; Type: TABLE
--

CREATE TABLE reference (
    id integer NOT NULL,
    url text,
    title text,
    doi character varying(128),
    comments text
);


--
-- Name: users; Type: TABLE
--

CREATE TABLE users (
    username character varying(32) NOT NULL,
    real_name character varying(128) NOT NULL,
    "password" character varying(32) NOT NULL,
    email character varying(128) NOT NULL,
    taxon text,
    code character varying(1) DEFAULT '!'::character varying NOT NULL,
    rights integer DEFAULT 0 NOT NULL,
    activated integer DEFAULT 0 NOT NULL,
    active character varying(32),
    added timestamp without time zone DEFAULT CURRENT_TIMESTAMP NOT NULL
);


--
-- Name: reference_pkey; Type: CONSTRAINT
--

ALTER TABLE ONLY reference
    ADD CONSTRAINT reference_pkey PRIMARY KEY (id);


--
-- Name: users_pkey; Type: CONSTRAINT
--

ALTER TABLE ONLY users
    ADD CONSTRAINT users_pkey PRIMARY KEY (username);


--
-- Name: users_unique; Type: CONSTRAINT
--

ALTER TABLE ONLY users
    ADD CONSTRAINT users_unique UNIQUE (email);


--
-- Name: reference; Type: ACL
--

GRANT ALL ON TABLE reference TO www;


--
-- Name: users; Type: ACL
--

GRANT ALL ON TABLE users TO www;

--
-- Done
--
