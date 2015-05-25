-- phplabdb.v2.devel
--
-- Copyright 2006 M.Bekaert
--
-- MySQL database: phplabdb.v2 // uniprime
-- --------------------------------------------------------

-- 
-- Structure for table users
-- 

DROP TABLE IF EXISTS users;
CREATE TABLE users (
  username varchar(32) NOT NULL,
  real_name varchar(128) NOT NULL,
  password varchar(32) NOT NULL,
  email varchar(128) NOT NULL,
  taxon text,
  code varchar(1) NOT NULL default '!',
  rights int(11) NOT NULL default '0',
  activated int(11) NOT NULL default '0',
  active varchar(32) default NULL,
  added timestamp NOT NULL default CURRENT_TIMESTAMP,
  PRIMARY KEY  (username),
  UNIQUE KEY email (email)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 PACK_KEYS=1;


--
-- Structure for table comments
--

DROP TABLE IF EXISTS comments;
CREATE TABLE comments (
  prefix int(11) NOT NULL,
  id int(11) NOT NULL,
  name varchar(32) NOT NULL,
  comments text NOT NULL,
  author varchar(32) NOT NULL,
  updated timestamp NOT NULL default CURRENT_TIMESTAMP,
  status int(11) default NULL,
  PRIMARY KEY  (prefix,id,name)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 PACK_KEYS=1;


--
-- Structure for table class
--

DROP TABLE IF EXISTS class;
CREATE TABLE class (
  id int(11) NOT NULL,
  class varchar(128) NOT NULL,
  description text default NULL,
  lang varchar(8) NOT NULL default 'en',
  PRIMARY KEY  (id,lang)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 PACK_KEYS=1;


--
-- Data for table division
--

INSERT INTO class VALUES (0, 'Unknown', NULL, 'en');
INSERT INTO class VALUES (1, 'Vision gene (nonsyndromic)', NULL, 'en');
INSERT INTO class VALUES (2, 'Vision gene', NULL, 'en');
INSERT INTO class VALUES (3, 'Auditory gene (nonsyndromic)', NULL, 'en');
INSERT INTO class VALUES (4, 'Auditory gene', NULL, 'en');
INSERT INTO class VALUES (5, 'Olfactory gene (nonsyndromic)', NULL, 'en');
INSERT INTO class VALUES (6, 'Olfactory gene', NULL, 'en');


-- 
-- Structure for table division
-- 

DROP TABLE IF EXISTS division;
CREATE TABLE division (
  id varchar(8) NOT NULL,
  division varchar(128) NOT NULL,
  PRIMARY KEY  (id),
  UNIQUE KEY division (division)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 PACK_KEYS=1;


--
-- Data for table division
--

INSERT INTO division VALUES ('PRI', 'primate sequences');
INSERT INTO division VALUES ('ROD', 'rodent sequences');
INSERT INTO division VALUES ('MAM', 'other mammalian sequences');
INSERT INTO division VALUES ('VRT', 'other vertebrate sequences');
INSERT INTO division VALUES ('INV', 'invertebrate sequences');
INSERT INTO division VALUES ('PLN', 'plant, fungal, and algal sequences');
INSERT INTO division VALUES ('BCT', 'bacterial sequences');
INSERT INTO division VALUES ('VRL', 'viral sequences');
INSERT INTO division VALUES ('PHG', 'bacteriophage sequences');
INSERT INTO division VALUES ('SYN', 'synthetic sequences');
INSERT INTO division VALUES ('UNA', 'unannotated sequences');
INSERT INTO division VALUES ('EST', 'EST sequences (expressed sequence tags)');
INSERT INTO division VALUES ('PAT', 'patent sequences');
INSERT INTO division VALUES ('STS', 'STS sequences (sequence tagged sites)');
INSERT INTO division VALUES ('GSS', 'GSS sequences (genome survey sequences)');
INSERT INTO division VALUES ('HTG', 'HTG sequences (high-throughput genomic sequences)');
INSERT INTO division VALUES ('HTC', 'unfinished high-throughput cDNA sequencing');
INSERT INTO division VALUES ('ENV', 'environmental sampling sequences');
INSERT INTO division VALUES ('CON', 'contig');


-- 
-- Structure for table evidence
-- 

DROP TABLE IF EXISTS evidence;
CREATE TABLE evidence (
  id varchar(8) NOT NULL,
  evidence varchar(128) NOT NULL,
  description text default NULL,
  lang varchar(8) NOT NULL default 'en',
  PRIMARY KEY  (id,lang)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 PACK_KEYS=1;


--
-- Data for table evidence
--

INSERT INTO evidence VALUES ('NR', 'Unknown', NULL, 'en');
INSERT INTO evidence VALUES ('TAS', 'Traceable Author Statement', NULL, 'en');
INSERT INTO evidence VALUES ('NAS', 'Non-traceable Author Statement', NULL, 'en');
INSERT INTO evidence VALUES ('IC', 'Inferred by Curator', NULL, 'en');
INSERT INTO evidence VALUES ('IDA', 'Inferred from Direct Assay', NULL, 'en');
INSERT INTO evidence VALUES ('IEA', 'Inferred from Electronic Annotation', NULL, 'en');
INSERT INTO evidence VALUES ('IEP', 'Inferred from Expression Pattern', NULL, 'en');
INSERT INTO evidence VALUES ('IGI', 'Inferred from Genetic Interaction', NULL, 'en');
INSERT INTO evidence VALUES ('IMP', 'Inferred from Mutant Phenotype', NULL, 'en');
INSERT INTO evidence VALUES ('IPI', 'Inferred from Physical Interaction', NULL, 'en');
INSERT INTO evidence VALUES ('ISS', 'Inferred from Sequence or Structural Similarity', NULL, 'en');
INSERT INTO evidence VALUES ('RCA', 'Inferred from Reviewed Computational Analysis', NULL, 'en');
INSERT INTO evidence VALUES ('ND', 'No biological Data available', NULL, 'en');


-- 
-- Structure for table locus_type
-- 

DROP TABLE IF EXISTS locus_type;
CREATE TABLE locus_type (
  id int(11) NOT NULL,
  locus_type varchar(128) NOT NULL,
  description text default NULL,
  lang varchar(8) NOT NULL default 'en',
  PRIMARY KEY  (id,lang)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 PACK_KEYS=1;


--
-- Data for table locus_type
--

INSERT INTO locus_type VALUES (0, 'Unknown', NULL, 'en');
INSERT INTO locus_type VALUES (1, 'Gene with protein product, function known', 'For protein-coding genes the protein may be predicted, but there is homology to proteins of known function, not just proteins of known motifs', 'en');
-- INSERT INTO locus_type VALUES (1, 'Gene with protein product, function known or inferred', 'For protein-coding genes the protein may be predicted, but there is homology to proteins of known function, not just proteins of known motifs', 'en');
INSERT INTO locus_type VALUES (2, 'Gene with protein product, function unknown', 'Genes for which there is a protein product, which may even have a defined motif, but its function is not known', 'en');
-- INSERT INTO locus_type VALUES (3, 'Gene with protein product, demonstrates somatic rearrangement', 'To be used for such ''genes'' as IGHG1, IGHG2, which define a combination of exons giving rise to a particular class of protein product. See also: for the set of exons that defines one mRNA, depending on the rearrangement', 'en');
INSERT INTO locus_type VALUES (4, 'Gene with no protein product', 'For RNA-coding genes that do not fall into other specific categories to be used for such RNAs as the RNA component of enzymes, regulatory RNAs, etc', 'en');
-- INSERT INTO locus_type VALUES (5, 'Phenotype only', 'For mapped phenotypes', 'en');
INSERT INTO locus_type VALUES (6, 'Pseudogene', 'Genes are classified as pseudogenes if there is not evidence of transcription, even if there is a predicted coding sequence', 'en');
-- INSERT INTO locus_type VALUES (7, 'Non-human orthologue', 'Entry describes the approved name and symbol for a gene before the human orthologue has been identified. This reserves the symbol so that it can not be used for a non-orthologous gene and is therefore valuable to maintain compatibility of symbols between orthologues in different species', 'en');
INSERT INTO locus_type VALUES (8, 'RNA, micro', 'RNAs explicitly designated as micro RNA', 'en');
INSERT INTO locus_type VALUES (9, 'RNA, ribosomal', 'RNAs that are structural components of ribosomes', 'en');
INSERT INTO locus_type VALUES (10, 'RNA, small nuclear', 'RNAs explicitly designated as small nuclear', 'en');
INSERT INTO locus_type VALUES (11, 'RNA, small nucleolar', 'RNAs explicitly designated as small nucleolar', 'en');
INSERT INTO locus_type VALUES (12, 'RNA, small cytoplasmic', 'RNAs explicitly designated as small cytoplasmic', 'en');
INSERT INTO locus_type VALUES (13, 'RNA, transfer', 'RNAs explicitly designated as transfer RNAs', 'en');
-- INSERT INTO locus_type VALUES (14, 'Duplicon', 'A duplicated piece of DNA, containing a gene that is approximately 97% similar to the original functional gene. Usually the duplicon gene is not functional', 'en');
-- INSERT INTO locus_type VALUES (15, 'Region', 'Extents of genomic sequence that contain one or more genes. For genes that undergo genomic rearrangement, the region category should be used only for more than one of these. Also applied to non-gene areas that do not fall into other types, such as regulatory elements or repetitive elements', 'en');
-- INSERT INTO locus_type VALUES (16, 'Model, ab initio', 'A model that is generated only from first principles, and is not guided by EST evidence. It does not predict a protein with significant similarity to other known proteins', 'en');
-- INSERT INTO locus_type VALUES (17, 'Model, ab initio, with EST support', 'A model that is generated from first principles, and is guided by EST evidence. It does not predict a protein with significant similarity to other known proteins', 'en');
-- INSERT INTO locus_type VALUES (18, 'Model, ab initio, with EST support and protein similarity', 'A model that is generated from first principles, and is guided by EST evidence. It does predict a protein with significant similarity to other known proteins', 'en');
-- INSERT INTO locus_type VALUES (19, 'Model, ab initio, with protein similarity', 'A model that is generated from first principles, and is not guided by EST evidence. It does predict a protein with significant similarity to other known proteins', 'en');
-- INSERT INTO locus_type VALUES (20, 'Model, supported by EST alignments', 'A model that is generated from EST alignments, but not mRNA alignments', 'en');
-- INSERT INTO locus_type VALUES (21, 'Model, supported by mRNA alignments', 'A model that is generated from mRNA alignments, but splice junctions or extensions are not supporte by ESTs', 'en');
-- INSERT INTO locus_type VALUES (22, 'Model, supported by mRNA and EST alignments', 'A model that is generated from mRNA alignments, and splice junctions or entensions are supported by ESTs', 'en');


-- 
-- Structure for table  molecule
-- 

DROP TABLE IF EXISTS molecule;
CREATE TABLE molecule (
  id varchar(8) NOT NULL,
  molecule varchar(128) NOT NULL,
  PRIMARY KEY  (id),
  UNIQUE KEY molecule (molecule)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 PACK_KEYS=1;


--
-- Data for table molecule
--

INSERT INTO molecule VALUES ('DNA', 'DNA');
INSERT INTO molecule VALUES ('RNA', 'RNA');
INSERT INTO molecule VALUES ('NA', 'NA');
INSERT INTO molecule VALUES ('tRNA', 'tRNA (transfer RNA)');
INSERT INTO molecule VALUES ('rRNA', 'rRNA (ribosomal RNA)');
INSERT INTO molecule VALUES ('mRNA', 'mRNA (messenger RNA)');
INSERT INTO molecule VALUES ('uRNA', 'uRNA (small nuclear RNA)');
INSERT INTO molecule VALUES ('snRNA', 'snRNA');
INSERT INTO molecule VALUES ('snoRNA', 'snoRNA');
INSERT INTO molecule VALUES ('ss-DNA', 'ss-DNA (single-stranded)');
INSERT INTO molecule VALUES ('ss-RNA', 'ss-RNA (single-stranded)');
INSERT INTO molecule VALUES ('ss-NA', 'ss-NA (single-stranded)');
INSERT INTO molecule VALUES ('ds-DNA', 'ds-DNA (double-stranded)');
INSERT INTO molecule VALUES ('ds-RNA', 'ds-RNA (double-stranded)');
INSERT INTO molecule VALUES ('ds-NA', 'ds-NA (double-stranded)');
INSERT INTO molecule VALUES ('ms-DNA', 'ms-DNA (mixed-stranded)');
INSERT INTO molecule VALUES ('ms-RNA', 'ms-RNA (mixed-stranded)');
INSERT INTO molecule VALUES ('ms-NA', 'ms-NA (mixed-stranded)');

-- 
-- Structure for table sequence_type
-- 

DROP TABLE IF EXISTS sequence_type;
CREATE TABLE sequence_type (
  id int(11) NOT NULL,
  sequence_type varchar(128) NOT NULL,
  description text default NULL,
  lang varchar(8) NOT NULL default 'en',
  PRIMARY KEY  (id,lang)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 PACK_KEYS=1;

    
--
-- Data for table sequence_type
--

INSERT INTO sequence_type VALUES (0, 'Unknown', NULL, 'en');
INSERT INTO sequence_type VALUES (1, 'Prototype', 'Manual entry', 'en');
INSERT INTO sequence_type VALUES (2, 'Blasted', NULL, 'en');
INSERT INTO sequence_type VALUES (3, 'Generated', NULL, 'en');


-- 
-- Structure for table status
-- 

DROP TABLE IF EXISTS status;
CREATE TABLE status (
  id int(11) NOT NULL,
  status varchar(128) NOT NULL,
  description text default NULL,
  lang varchar(8) NOT NULL default 'en',
  PRIMARY KEY  (id,lang)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 PACK_KEYS=1;


--
-- Data for table status
--

INSERT INTO status VALUES (0, 'Unknown', 'The Recode record is not evaluated', 'en');
INSERT INTO status VALUES (1, 'Preliminary', NULL, 'en');
INSERT INTO status VALUES (2, 'Orthologs found', NULL, 'en');
INSERT INTO status VALUES (3, 'Multi-alignment', NULL, 'en');
INSERT INTO status VALUES (4, 'Primers available', NULL, 'en');
INSERT INTO status VALUES (5, 'Primers ordered', NULL, 'en');
INSERT INTO status VALUES (6, 'Amplification in progress', NULL, 'en');
INSERT INTO status VALUES (7, 'Validated', NULL, 'en');
INSERT INTO status VALUES (8, 'Pending', NULL, 'en');
INSERT INTO status VALUES (9, 'Not validated', NULL, 'en');
INSERT INTO status VALUES (10, 'Discarded', NULL, 'en');
INSERT INTO status VALUES (11, 'Erroneous', NULL, 'en');
INSERT INTO status VALUES (12, 'Not selected', NULL, 'en');


-- 
-- Structure de la table translation
-- 

DROP TABLE IF EXISTS translation;
CREATE TABLE translation (
  id int(11) NOT NULL,
  translation varchar(128) NOT NULL,
  PRIMARY KEY  (id),
  UNIQUE KEY translation (translation)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 PACK_KEYS=1;


--
-- Data for table translation
--

INSERT INTO translation VALUES (0, 'Unknown');
INSERT INTO translation VALUES (1, 'Standard');
INSERT INTO translation VALUES (2, 'Vertebrate Mitochondrial');
INSERT INTO translation VALUES (3, 'Yeast Mitochondrial');
INSERT INTO translation VALUES (4, 'Mold, Protozoan, Coelenterate Mito. and Myco/Spiroplasma');
INSERT INTO translation VALUES (5, 'Invertebrate Mitochondrial');
INSERT INTO translation VALUES (6, 'Ciliate Nuclear, Dasycladacean Nuclear, Hexamita Nuclear');
INSERT INTO translation VALUES (9, 'Echinoderm Mitochondrial');
INSERT INTO translation VALUES (10, 'Euploid Nuclear');
INSERT INTO translation VALUES (11, 'Bacterial');
INSERT INTO translation VALUES (12, 'Alternative Yeast Nuclear');
INSERT INTO translation VALUES (13, 'Ascidian Mitochondrial');
INSERT INTO translation VALUES (14, 'Flatworm Mitochondrial');
INSERT INTO translation VALUES (15, 'Blepharisma Macronuclear');
INSERT INTO translation VALUES (16, 'Chlorophycean Mitochondrial');
INSERT INTO translation VALUES (21, 'Trematode Mitochondrial');

-- --------------------------------------------------------

-- 
-- Structure for table locus
-- 

DROP TABLE IF EXISTS locus;
CREATE TABLE locus (
  prefix int(11) NOT NULL ,
  id int(11) NOT NULL,
  name varchar(128) NOT NULL,
  alias varchar(128) default NULL,
  locus_type int(11) NOT NULL default '0',
  pathway text default NULL,
  phenotype text default NULL,
  functions text default NULL,
  evidence varchar(8) NOT NULL,
  class int(11) NOT NULL default '0',
  sources text default NULL,
  status int(11) NOT NULL default '0',
  comments text default NULL,
  updated timestamp NOT NULL default CURRENT_TIMESTAMP,
  author varchar(32) NOT NULL,
  PRIMARY KEY  (prefix,id),
  UNIQUE KEY name (name)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 PACK_KEYS=1;


-- 
-- Structure for table organism
-- 

DROP TABLE IF EXISTS organism;
CREATE TABLE organism (
  id int(11) NOT NULL,
  name varchar(128) NOT NULL,
  alias varchar(128) default NULL,
  abbr varchar(8) default NULL,
  taxon text default NULL,
  division varchar(8) NOT NULL default 'UNA',
  taxonid int(11) default NULL,
  comments text default NULL,
  updated timestamp NOT NULL default CURRENT_TIMESTAMP,
  author varchar(32) NOT NULL,
  PRIMARY KEY  (id),
  UNIQUE KEY name (name),
  UNIQUE KEY abbr (abbr)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 PACK_KEYS=1;


-- 
-- Structure for table sequence
-- 

DROP TABLE IF EXISTS sequence;
CREATE TABLE sequence (
  prefix int(11) NOT NULL,
  id int(11) NOT NULL,
  locus_prefix int(11) NOT NULL,
  locus_id int(11) NOT NULL,
  name varchar(128) default NULL,
  alias varchar(128) default NULL,
  location text default NULL,
  translation int(11) NOT NULL default '0',
  molecule varchar(8) default NULL,
  circular enum('t','f') NOT NULL default 'f',
  chromosome varchar(32) default NULL,
  isolate varchar(128) default NULL,
  organelle varchar(64) default NULL,
  map varchar(128) default NULL,
  accession varchar(32) default NULL,
  hgnc int(11) default NULL,
  geneid int(11) default NULL,
  organism int(11) NOT NULL,
  go text default NULL,
  sequence_type int(11) NOT NULL default '0',
  prime_prefixr int(11) default NULL,
  primer_id int(11) default NULL,
  structure text default NULL,
  stop int(11) default NULL,
  start int(11) default NULL,
  strand int(11) default NULL,
  sequence longtext default NULL,
  features text default NULL,
  evalue double default NULL,
  sources text default NULL,
  comments text default NULL,
  updated timestamp NOT NULL default CURRENT_TIMESTAMP,
  author varchar(32) NOT NULL,
  PRIMARY KEY  (prefix,id)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 PACK_KEYS=1;


-- 
-- Structure for table alignment
-- 

DROP TABLE IF EXISTS alignment;
CREATE TABLE alignment (
  prefix int(11) NOT NULL ,
  id int(11) NOT NULL,
  locus_prefix int(11) NOT NULL,
  locus_id int(11) NOT NULL,
  sequences text NOT NULL,
  alignment longtext NOT NULL,
  consensus longtext NOT NULL,
  structure text default NULL,
  program varchar(64) NOT NULL,
  comments text default NULL,
  updated timestamp NOT NULL default CURRENT_TIMESTAMP,
  author varchar(32) NOT NULL,
  PRIMARY KEY  (prefix,id)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 PACK_KEYS=1;


-- 
-- Structure for table mrna
-- 

DROP TABLE IF EXISTS mrna;
CREATE TABLE mrna (
  prefix int(11) NOT NULL ,
  id int(11) NOT NULL,
  locus_prefix int(11) NOT NULL,
  locus_id int(11) NOT NULL,
  sequence_prefix int(11) NOT NULL,
  sequence_id int(11) NOT NULL,
  mrna_type int(11) NOT NULL default '1',
  location text default NULL,
  mrna longtext NOT NULL,
  comments text default NULL,
  updated timestamp NOT NULL default CURRENT_TIMESTAMP,
  author varchar(32) NOT NULL,
  PRIMARY KEY  (prefix,id)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 PACK_KEYS=1;


-- 
-- Structure for table primer
-- 

DROP TABLE IF EXISTS primer;
CREATE TABLE primer (
  prefix int(11) NOT NULL ,
  id int(11) NOT NULL,
  locus_prefix int(11) NOT NULL,
  locus_id int(11) NOT NULL,
  alignment_prefix int(11) NOT NULL,
  alignment_id int(11) NOT NULL,
  penality double default NULL,
  left_seq varchar(128) NOT NULL,
  left_data text NOT NULL,
  left_name varchar(32) default NULL,
  right_seq varchar(128) NOT NULL,
  right_data text NOT NULL,
  right_name varchar(32) default NULL,
  location text default NULL,
  pcr text default NULL,
  comments text default NULL,
  updated timestamp NOT NULL default CURRENT_TIMESTAMP,
  author varchar(32) NOT NULL,
  PRIMARY KEY  (prefix,id)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 PACK_KEYS=1;


-- --------------------------------------------------------

OPTIMIZE TABLE alignment, class, comments, division, evidence, locus, locus_type, molecule, mrna, organism, primer, sequence, sequence_type, status, translation, users;