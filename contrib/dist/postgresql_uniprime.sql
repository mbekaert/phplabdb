-- phplabdb.v2.devel
--
-- Copyright 2006-2007 M.Bekaert
--
-- PostgreSQL database: phplabdb.v2 // uniprime
-- --------------------------------------------------------



ALTER TABLE ONLY uniprime_sequence DROP CONSTRAINT uniprime_sequence_sequence_type;
ALTER TABLE ONLY uniprime_sequence DROP CONSTRAINT uniprime_sequence_organism;
ALTER TABLE ONLY uniprime_sequence DROP CONSTRAINT uniprime_sequence_locus;
ALTER TABLE ONLY uniprime_sequence DROP CONSTRAINT uniprime_sequence_author;
ALTER TABLE ONLY uniprime_primer DROP CONSTRAINT uniprime_primer_locus;
ALTER TABLE ONLY uniprime_primer DROP CONSTRAINT uniprime_primer_author;
ALTER TABLE ONLY uniprime_primer DROP CONSTRAINT uniprime_primer_alignment;
ALTER TABLE ONLY uniprime_mrna DROP CONSTRAINT uniprime_mrna_sequence;
ALTER TABLE ONLY uniprime_mrna DROP CONSTRAINT uniprime_mrna_locus;
ALTER TABLE ONLY uniprime_mrna DROP CONSTRAINT uniprime_mrna_author;
ALTER TABLE ONLY uniprime_locus DROP CONSTRAINT uniprime_locus_status;
ALTER TABLE ONLY uniprime_locus DROP CONSTRAINT uniprime_locus_locus_type;
ALTER TABLE ONLY uniprime_locus DROP CONSTRAINT uniprime_locus_evidence;
ALTER TABLE ONLY uniprime_locus DROP CONSTRAINT uniprime_locus_class;
ALTER TABLE ONLY uniprime_locus DROP CONSTRAINT uniprime_locus_author;
ALTER TABLE ONLY uniprime_alignment DROP CONSTRAINT uniprime_alignment_locus;
ALTER TABLE ONLY uniprime_alignment DROP CONSTRAINT uniprime_alignment_author;
ALTER TABLE ONLY uniprime_status DROP CONSTRAINT uniprime_status_pkey;
ALTER TABLE ONLY uniprime_sequence_type DROP CONSTRAINT uniprime_sequence_type_pkey;
ALTER TABLE ONLY uniprime_sequence DROP CONSTRAINT uniprime_sequence_pkey;
ALTER TABLE ONLY uniprime_primer DROP CONSTRAINT uniprime_primer_pkey;
ALTER TABLE ONLY uniprime_mrna DROP CONSTRAINT uniprime_mrna_pkey;
ALTER TABLE ONLY uniprime_molecule DROP CONSTRAINT uniprime_molecule_unique;
ALTER TABLE ONLY uniprime_molecule DROP CONSTRAINT uniprime_molecule_pkey;
ALTER TABLE ONLY uniprime_locus DROP CONSTRAINT uniprime_locus_unique;
ALTER TABLE ONLY uniprime_locus_type DROP CONSTRAINT uniprime_locus_type_pkey;
ALTER TABLE ONLY uniprime_locus DROP CONSTRAINT uniprime_locus_pkey;
ALTER TABLE ONLY uniprime_evidence DROP CONSTRAINT uniprime_evidence_pkey;
ALTER TABLE ONLY uniprime_class DROP CONSTRAINT uniprime_class_pkey;
ALTER TABLE ONLY uniprime_alignment DROP CONSTRAINT uniprime_alignment_pkey;
DROP TABLE uniprime_status;
DROP TABLE uniprime_sequence_type;
DROP TABLE uniprime_sequence;
DROP TABLE uniprime_primer;
DROP TABLE uniprime_mrna;
DROP TABLE uniprime_molecule;
DROP TABLE uniprime_locus_type;
DROP TABLE uniprime_locus;
DROP TABLE uniprime_evidence;
DROP TABLE uniprime_class;
DROP TABLE uniprime_alignment;


--
-- Name: uniprime_alignment; Type: TABLE;
--

CREATE TABLE uniprime_alignment (
    prefix integer NOT NULL,
    id integer NOT NULL,
    locus_prefix integer NOT NULL,
    locus_id integer NOT NULL,
    sequences text NOT NULL,
    alignment text NOT NULL,
    consensus text NOT NULL,
    structure text,
    program character varying(64) NOT NULL,
    comments text,
    updated timestamp without time zone DEFAULT now() NOT NULL,
    author character varying(32) NOT NULL
);


--
-- Name: uniprime_class; Type: TABLE;
--

CREATE TABLE uniprime_class (
    id integer NOT NULL,
    "class" character varying(128) NOT NULL,
    description text
);


--
-- Name: uniprime_evidence; Type: TABLE;
--

CREATE TABLE uniprime_evidence (
    id character varying(8) NOT NULL,
    evidence character varying(128) NOT NULL,
    description text
);


--
-- Name: uniprime_locus; Type: TABLE;
--

CREATE TABLE uniprime_locus (
    prefix integer NOT NULL,
    id integer NOT NULL,
    name character varying(128) NOT NULL,
    alias character varying(128),
    locus_type integer DEFAULT 0 NOT NULL,
    pathway text,
    phenotype text,
    functions text,
    evidence character varying(8) NOT NULL,
    "class" integer DEFAULT 0,
    sources text,
    status integer DEFAULT 0,
    comments text,
    updated timestamp without time zone DEFAULT now() NOT NULL,
    author character varying(32) NOT NULL
);


--
-- Name: uniprime_locus_type; Type: TABLE;
--

CREATE TABLE uniprime_locus_type (
    id integer NOT NULL,
    locus_type character varying(128) NOT NULL,
    description text
);


--
-- Name: uniprime_molecule; Type: TABLE;
--

CREATE TABLE uniprime_molecule (
    id character varying(8) NOT NULL,
    molecule character varying(128) NOT NULL
);


--
-- Name: uniprime_mrna; Type: TABLE;
--

CREATE TABLE uniprime_mrna (
    prefix integer NOT NULL,
    id integer NOT NULL,
    locus_prefix integer NOT NULL,
    locus_id integer NOT NULL,
    sequence_prefix integer NOT NULL,
    sequence_id integer NOT NULL,
    mrna_type integer DEFAULT 1 NOT NULL,
    "location" text,
    mrna text NOT NULL,
    comments text,
    updated timestamp without time zone DEFAULT now() NOT NULL,
    author character varying(32) NOT NULL
);


--
-- Name: uniprime_primer; Type: TABLE;
--

CREATE TABLE uniprime_primer (
    prefix integer NOT NULL,
    id integer NOT NULL,
    locus_prefix integer NOT NULL,
    locus_id integer NOT NULL,
    alignment_prefix integer NOT NULL,
    alignment_id integer NOT NULL,
    penality double precision,
    left_seq character varying(128) NOT NULL,
    left_data text NOT NULL,
    left_name character varying(32),
    right_seq character varying(128) NOT NULL,
    right_data text NOT NULL,
    right_name character varying(32),
    "location" text,
    pcr text,
    comments text,
    updated timestamp without time zone DEFAULT now() NOT NULL,
    author character varying(32) NOT NULL
);


--
-- Name: uniprime_sequence; Type: TABLE;
--

CREATE TABLE uniprime_sequence (
    prefix integer NOT NULL,
    id integer NOT NULL,
    locus_prefix integer NOT NULL,
    locus_id integer NOT NULL,
    name character varying(128),
    alias character varying(128),
    "location" text,
    translation integer DEFAULT 0 NOT NULL,
    molecule character varying(8),
    circular boolean DEFAULT false NOT NULL,
    chromosome character varying(32),
    isolate character varying(128),
    organelle character varying(64),
    map character varying(128),
    accession character varying(32),
    hgnc integer,
    geneid integer,
    organism character varying(128),
    go text,
    sequence_type integer DEFAULT 0 NOT NULL,
    primer_prefix integer,
    primer_id integer,
    structure text,
    stop integer,
    "start" integer,
    strand integer,
    "sequence" text,
    features text,
    evalue double precision,
    sources text,
    comments text,
    updated timestamp without time zone DEFAULT now() NOT NULL,
    author character varying(32) NOT NULL
);


--
-- Name: uniprime_sequence_type; Type: TABLE;
--

CREATE TABLE uniprime_sequence_type (
    id integer NOT NULL,
    sequence_type character varying(128) NOT NULL,
    description text
);


--
-- Name: uniprime_status; Type: TABLE;
--

CREATE TABLE uniprime_status (
    id integer NOT NULL,
    status character varying(128) NOT NULL,
    description text
);


--
-- Data for Name: uniprime_class;
--

INSERT INTO uniprime_class VALUES (0, 'Unknown', NULL);
INSERT INTO uniprime_class VALUES (1, 'Vision gene (nonsyndromic)', NULL);
INSERT INTO uniprime_class VALUES (2, 'Vision gene', NULL);
INSERT INTO uniprime_class VALUES (3, 'Auditory gene (nonsyndromic)', NULL);
INSERT INTO uniprime_class VALUES (4, 'Auditory gene', NULL);
INSERT INTO uniprime_class VALUES (5, 'Olfactory gene (nonsyndromic)', NULL);
INSERT INTO uniprime_class VALUES (6, 'Olfactory gene', NULL);


--
-- Data for Name: uniprime_evidence;
--

INSERT INTO uniprime_evidence VALUES ('NR', 'Unknown', NULL);
INSERT INTO uniprime_evidence VALUES ('TAS', 'Traceable Author Statement', NULL);
INSERT INTO uniprime_evidence VALUES ('NAS', 'Non-traceable Author Statement', NULL);
INSERT INTO uniprime_evidence VALUES ('IC', 'Inferred by Curator', NULL);
INSERT INTO uniprime_evidence VALUES ('IDA', 'Inferred from Direct Assay', NULL);
INSERT INTO uniprime_evidence VALUES ('IEA', 'Inferred from Electronic Annotation', NULL);
INSERT INTO uniprime_evidence VALUES ('IEP', 'Inferred from Expression Pattern', NULL);
INSERT INTO uniprime_evidence VALUES ('IGI', 'Inferred from Genetic Interaction', NULL);
INSERT INTO uniprime_evidence VALUES ('IMP', 'Inferred from Mutant Phenotype', NULL);
INSERT INTO uniprime_evidence VALUES ('IPI', 'Inferred from Physical Interaction', NULL);
INSERT INTO uniprime_evidence VALUES ('ISS', 'Inferred from Sequence or Structural Similarity', NULL);
INSERT INTO uniprime_evidence VALUES ('RCA', 'Inferred from Reviewed Computational Analysis', NULL);
INSERT INTO uniprime_evidence VALUES ('ND', 'No biological Data available', NULL);


--
-- Data for Name: uniprime_locus_type;
--

INSERT INTO uniprime_locus_type VALUES (0, 'Unknown', NULL);
INSERT INTO uniprime_locus_type VALUES (1, 'Gene with protein product, function known', 'For protein-coding genes the protein may be predicted, but there is homology to proteins of known function, not just proteins of known motifs');
-- INSERT INTO uniprime_locus_type VALUES (1, 'Gene with protein product, function known or inferred', 'For protein-coding genes the protein may be predicted, but there is homology to proteins of known function, not just proteins of known motifs');
INSERT INTO uniprime_locus_type VALUES (2, 'Gene with protein product, function unknown', 'Genes for which there is a protein product, which may even have a defined motif, but its function is not known');
-- INSERT INTO uniprime_locus_type VALUES (3, 'Gene with protein product, demonstrates somatic rearrangement', 'To be used for such ''genes'' as IGHG1, IGHG2, which define a combination of exons giving rise to a particular class of protein product. See also: for the set of exons that defines one mRNA, depending on the rearrangement');
INSERT INTO uniprime_locus_type VALUES (4, 'Gene with no protein product', 'For RNA-coding genes that do not fall into other specific categories to be used for such RNAs as the RNA component of enzymes, regulatory RNAs, etc');
-- INSERT INTO uniprime_locus_type VALUES (5, 'Phenotype only', 'For mapped phenotypes');
INSERT INTO uniprime_locus_type VALUES (6, 'Pseudogene', 'Genes are classified as pseudogenes if there is not evidence of transcription, even if there is a predicted coding sequence');
-- INSERT INTO uniprime_locus_type VALUES (7, 'Non-human orthologue', 'Entry describes the approved name and symbol for a gene before the human orthologue has been identified. This reserves the symbol so that it can not be used for a non-orthologous gene and is therefore valuable to maintain compatibility of symbols between orthologues in different species');
INSERT INTO uniprime_locus_type VALUES (8, 'RNA, micro', 'RNAs explicitly designated as micro RNA');
INSERT INTO uniprime_locus_type VALUES (9, 'RNA, ribosomal', 'RNAs that are structural components of ribosomes');
INSERT INTO uniprime_locus_type VALUES (10, 'RNA, small nuclear', 'RNAs explicitly designated as small nuclear');
INSERT INTO uniprime_locus_type VALUES (11, 'RNA, small nucleolar', 'RNAs explicitly designated as small nucleolar');
INSERT INTO uniprime_locus_type VALUES (12, 'RNA, small cytoplasmic', 'RNAs explicitly designated as small cytoplasmic');
INSERT INTO uniprime_locus_type VALUES (13, 'RNA, transfer', 'RNAs explicitly designated as transfer RNAs');
-- INSERT INTO locus_type VALUES (14, 'Duplicon', 'A duplicated piece of DNA, containing a gene that is approximately 97% similar to the original functional gene. Usually the duplicon gene is not functional');
-- INSERT INTO uniprime_locus_type VALUES (15, 'Region', 'Extents of genomic sequence that contain one or more genes. For genes that undergo genomic rearrangement, the region category should be used only for more than one of these. Also applied to non-gene areas that do not fall into other types, such as regulatory elements or repetitive elements');
-- INSERT INTO uniprime_locus_type VALUES (16, 'Model, ab initio', 'A model that is generated only from first principles, and is not guided by EST evidence. It does not predict a protein with significant similarity to other known proteins');
-- INSERT INTO uniprime_locus_type VALUES (17, 'Model, ab initio, with EST support', 'A model that is generated from first principles, and is guided by EST evidence. It does not predict a protein with significant similarity to other known proteins');
-- INSERT INTO uniprime_locus_type VALUES (18, 'Model, ab initio, with EST support and protein similarity', 'A model that is generated from first principles, and is guided by EST evidence. It does predict a protein with significant similarity to other known proteins');
-- INSERT INTO uniprime_locus_type VALUES (19, 'Model, ab initio, with protein similarity', 'A model that is generated from first principles, and is not guided by EST evidence. It does predict a protein with significant similarity to other known proteins');
-- INSERT INTO uniprime_locus_type VALUES (20, 'Model, supported by EST alignments', 'A model that is generated from EST alignments, but not mRNA alignments');
-- INSERT INTO uniprime_locus_type VALUES (21, 'Model, supported by mRNA alignments', 'A model that is generated from mRNA alignments, but splice junctions or extensions are not supporte by ESTs');
-- INSERT INTO uniprime_locus_type VALUES (22, 'Model, supported by mRNA and EST alignments', 'A model that is generated from mRNA alignments, and splice junctions or entensions are supported by ESTs');


--
-- Data for Name: uniprime_molecule;
--

INSERT INTO uniprime_molecule VALUES ('DNA', 'DNA');
INSERT INTO uniprime_molecule VALUES ('RNA', 'RNA');
INSERT INTO uniprime_molecule VALUES ('NA', 'NA');
INSERT INTO uniprime_molecule VALUES ('tRNA', 'tRNA (transfer RNA)');
INSERT INTO uniprime_molecule VALUES ('rRNA', 'rRNA (ribosomal RNA)');
INSERT INTO uniprime_molecule VALUES ('mRNA', 'mRNA (messenger RNA)');
INSERT INTO uniprime_molecule VALUES ('uRNA', 'uRNA (small nuclear RNA)');
INSERT INTO uniprime_molecule VALUES ('snRNA', 'snRNA');
INSERT INTO uniprime_molecule VALUES ('snoRNA', 'snoRNA');
INSERT INTO uniprime_molecule VALUES ('ss-DNA', 'ss-DNA (single-stranded)');
INSERT INTO uniprime_molecule VALUES ('ss-RNA', 'ss-RNA (single-stranded)');
INSERT INTO uniprime_molecule VALUES ('ss-NA', 'ss-NA (single-stranded)');
INSERT INTO uniprime_molecule VALUES ('ds-DNA', 'ds-DNA (double-stranded)');
INSERT INTO uniprime_molecule VALUES ('ds-RNA', 'ds-RNA (double-stranded)');
INSERT INTO uniprime_molecule VALUES ('ds-NA', 'ds-NA (double-stranded)');
INSERT INTO uniprime_molecule VALUES ('ms-DNA', 'ms-DNA (mixed-stranded)');
INSERT INTO uniprime_molecule VALUES ('ms-RNA', 'ms-RNA (mixed-stranded)');
INSERT INTO uniprime_molecule VALUES ('ms-NA', 'ms-NA (mixed-stranded)');


--
-- Data for Name: uniprime_sequence_type;
--

INSERT INTO uniprime_sequence_type VALUES (0, 'Unknown', NULL);
INSERT INTO uniprime_sequence_type VALUES (1, 'Prototype', 'Manual entry');
INSERT INTO uniprime_sequence_type VALUES (2, 'Blasted', NULL);
INSERT INTO uniprime_sequence_type VALUES (3, 'vPCR', 'Virtual PCR');
INSERT INTO uniprime_sequence_type VALUES (4, 'Generated', NULL);


--
-- Data for Name: uniprime_status;
--

INSERT INTO uniprime_status VALUES (0, 'Unknown', 'The Recode record is not evaluated');
INSERT INTO uniprime_status VALUES (1, 'Preliminary', NULL);
INSERT INTO uniprime_status VALUES (2, 'Orthologs found', NULL);
INSERT INTO uniprime_status VALUES (3, 'Multi-alignment', NULL);
INSERT INTO uniprime_status VALUES (4, 'Primers available', NULL);
INSERT INTO uniprime_status VALUES (5, 'Primers ordered', NULL);
INSERT INTO uniprime_status VALUES (6, 'Amplification in progress', NULL);
INSERT INTO uniprime_status VALUES (7, 'Validated', NULL);
INSERT INTO uniprime_status VALUES (8, 'Pending', NULL);
INSERT INTO uniprime_status VALUES (9, 'Not validated', NULL);
INSERT INTO uniprime_status VALUES (10, 'Discarded', NULL);
INSERT INTO uniprime_status VALUES (11, 'Erroneous', NULL);
INSERT INTO uniprime_status VALUES (12, 'Not selected', NULL);


--
-- Name: uniprime_alignment_pkey; Type: CONSTRAINT;
--

ALTER TABLE ONLY uniprime_alignment
    ADD CONSTRAINT uniprime_alignment_pkey PRIMARY KEY (prefix, id);


--
-- Name: uniprime_class_pkey; Type: CONSTRAINT;
--

ALTER TABLE ONLY uniprime_class
    ADD CONSTRAINT uniprime_class_pkey PRIMARY KEY (id);


--
-- Name: uniprime_evidence_pkey; Type: CONSTRAINT;
--

ALTER TABLE ONLY uniprime_evidence
    ADD CONSTRAINT uniprime_evidence_pkey PRIMARY KEY (id);


--
-- Name: uniprime_locus_pkey; Type: CONSTRAINT;
--

ALTER TABLE ONLY uniprime_locus
    ADD CONSTRAINT uniprime_locus_pkey PRIMARY KEY (prefix, id);


--
-- Name: uniprime_locus_type_pkey; Type: CONSTRAINT;
--

ALTER TABLE ONLY uniprime_locus_type
    ADD CONSTRAINT uniprime_locus_type_pkey PRIMARY KEY (id);


--
-- Name: uniprime_locus_unique; Type: CONSTRAINT;
--

ALTER TABLE ONLY uniprime_locus
    ADD CONSTRAINT uniprime_locus_unique UNIQUE (name);


--
-- Name: uniprime_molecule_pkey; Type: CONSTRAINT;
--

ALTER TABLE ONLY uniprime_molecule
    ADD CONSTRAINT uniprime_molecule_pkey PRIMARY KEY (id);


--
-- Name: uniprime_molecule_unique; Type: CONSTRAINT;
--

ALTER TABLE ONLY uniprime_molecule
    ADD CONSTRAINT uniprime_molecule_unique UNIQUE (molecule);


--
-- Name: uniprime_mrna_pkey; Type: CONSTRAINT;
--

ALTER TABLE ONLY uniprime_mrna
    ADD CONSTRAINT uniprime_mrna_pkey PRIMARY KEY (prefix, id);


--
-- Name: uniprime_primer_pkey; Type: CONSTRAINT;
--

ALTER TABLE ONLY uniprime_primer
    ADD CONSTRAINT uniprime_primer_pkey PRIMARY KEY (prefix, id);


--
-- Name: uniprime_sequence_pkey; Type: CONSTRAINT;
--

ALTER TABLE ONLY uniprime_sequence
    ADD CONSTRAINT uniprime_sequence_pkey PRIMARY KEY (prefix, id);


--
-- Name: uniprime_sequence_type_pkey; Type: CONSTRAINT;
--

ALTER TABLE ONLY uniprime_sequence_type
    ADD CONSTRAINT uniprime_sequence_type_pkey PRIMARY KEY (id);


--
-- Name: uniprime_status_pkey; Type: CONSTRAINT;
--

ALTER TABLE ONLY uniprime_status
    ADD CONSTRAINT uniprime_status_pkey PRIMARY KEY (id);


--
-- Name: uniprime_alignment_author; Type: FK CONSTRAINT;
--

ALTER TABLE ONLY uniprime_alignment
    ADD CONSTRAINT uniprime_alignment_author FOREIGN KEY (author) REFERENCES users(username);


--
-- Name: uniprime_alignment_locus; Type: FK CONSTRAINT;
--

ALTER TABLE ONLY uniprime_alignment
    ADD CONSTRAINT uniprime_alignment_locus FOREIGN KEY (locus_prefix, locus_id) REFERENCES uniprime_locus(prefix, id);


--
-- Name: uniprime_locus_author; Type: FK CONSTRAINT;
--

ALTER TABLE ONLY uniprime_locus
    ADD CONSTRAINT uniprime_locus_author FOREIGN KEY (author) REFERENCES users(username);


--
-- Name: uniprime_locus_class; Type: FK CONSTRAINT;
--

ALTER TABLE ONLY uniprime_locus
    ADD CONSTRAINT uniprime_locus_class FOREIGN KEY ("class") REFERENCES uniprime_class(id);


--
-- Name: uniprime_locus_evidence; Type: FK CONSTRAINT;
--

ALTER TABLE ONLY uniprime_locus
    ADD CONSTRAINT uniprime_locus_evidence FOREIGN KEY (evidence) REFERENCES uniprime_evidence(id);


--
-- Name: uniprime_locus_locus_type; Type: FK CONSTRAINT;
--

ALTER TABLE ONLY uniprime_locus
    ADD CONSTRAINT uniprime_locus_locus_type FOREIGN KEY (locus_type) REFERENCES uniprime_locus_type(id);


--
-- Name: uniprime_locus_status; Type: FK CONSTRAINT;
--

ALTER TABLE ONLY uniprime_locus
    ADD CONSTRAINT uniprime_locus_status FOREIGN KEY (status) REFERENCES uniprime_status(id);


--
-- Name: uniprime_mrna_author; Type: FK CONSTRAINT;
--

ALTER TABLE ONLY uniprime_mrna
    ADD CONSTRAINT uniprime_mrna_author FOREIGN KEY (author) REFERENCES users(username);


--
-- Name: uniprime_mrna_locus; Type: FK CONSTRAINT;
--

ALTER TABLE ONLY uniprime_mrna
    ADD CONSTRAINT uniprime_mrna_locus FOREIGN KEY (locus_prefix, locus_id) REFERENCES uniprime_locus(prefix, id);


--
-- Name: uniprime_mrna_sequence; Type: FK CONSTRAINT;
--

ALTER TABLE ONLY uniprime_mrna
    ADD CONSTRAINT uniprime_mrna_sequence FOREIGN KEY (sequence_prefix, sequence_id) REFERENCES uniprime_sequence(prefix, id);


--
-- Name: uniprime_primer_alignment; Type: FK CONSTRAINT;
--

ALTER TABLE ONLY uniprime_primer
    ADD CONSTRAINT uniprime_primer_alignment FOREIGN KEY (alignment_prefix, alignment_id) REFERENCES uniprime_alignment(prefix, id);


--
-- Name: uniprime_primer_author; Type: FK CONSTRAINT;
--

ALTER TABLE ONLY uniprime_primer
    ADD CONSTRAINT uniprime_primer_author FOREIGN KEY (author) REFERENCES users(username);


--
-- Name: uniprime_primer_locus; Type: FK CONSTRAINT;
--

ALTER TABLE ONLY uniprime_primer
    ADD CONSTRAINT uniprime_primer_locus FOREIGN KEY (locus_prefix, locus_id) REFERENCES uniprime_locus(prefix, id);


--
-- Name: uniprime_sequence_author; Type: FK CONSTRAINT;
--

ALTER TABLE ONLY uniprime_sequence
    ADD CONSTRAINT uniprime_sequence_author FOREIGN KEY (author) REFERENCES users(username);


--
-- Name: uniprime_sequence_locus; Type: FK CONSTRAINT;
--

ALTER TABLE ONLY uniprime_sequence
    ADD CONSTRAINT uniprime_sequence_locus FOREIGN KEY (locus_prefix, locus_id) REFERENCES uniprime_locus(prefix, id);


--
-- Name: uniprime_sequence_organism; Type: FK CONSTRAINT;
--

ALTER TABLE ONLY uniprime_sequence
    ADD CONSTRAINT uniprime_sequence_organism FOREIGN KEY (organism) REFERENCES tree_taxonomy(scientificname);


--
-- Name: uniprime_sequence_sequence_type; Type: FK CONSTRAINT;
--

ALTER TABLE ONLY uniprime_sequence
    ADD CONSTRAINT uniprime_sequence_sequence_type FOREIGN KEY (sequence_type) REFERENCES uniprime_sequence_type(id);


--
-- Name: uniprime_alignment; Type: ACL;
--

GRANT ALL ON TABLE uniprime_alignment TO www;


--
-- Name: uniprime_class; Type: ACL;
--

GRANT ALL ON TABLE uniprime_class TO www;


--
-- Name: uniprime_evidence; Type: ACL;
--

GRANT ALL ON TABLE uniprime_evidence TO www;


--
-- Name: uniprime_locus; Type: ACL;
--

GRANT ALL ON TABLE uniprime_locus TO www;


--
-- Name: uniprime_locus_type; Type: ACL;
--

GRANT ALL ON TABLE uniprime_locus_type TO www;


--
-- Name: uniprime_molecule; Type: ACL;
--

GRANT ALL ON TABLE uniprime_molecule TO www;


--
-- Name: uniprime_mrna; Type: ACL;
--

GRANT ALL ON TABLE uniprime_mrna TO www;


--
-- Name: uniprime_primer; Type: ACL;
--

GRANT ALL ON TABLE uniprime_primer TO www;


--
-- Name: uniprime_sequence; Type: ACL;
--

GRANT ALL ON TABLE uniprime_sequence TO www;


--
-- Name: uniprime_sequence_type; Type: ACL;
--

GRANT ALL ON TABLE uniprime_sequence_type TO www;


--
-- Name: uniprime_status; Type: ACL;
--

GRANT ALL ON TABLE uniprime_status TO www;


--
-- Done
--
