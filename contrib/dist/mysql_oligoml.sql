-- phplabdb.v2.devel
--
-- Copyright 2006 M.Bekaert
--
-- MySQL database: phplabdb.v2 // oligoml
-- --------------------------------------------------------

-- 
-- Drop tables
-- 

SET FOREIGN_KEY_CHECKS = 0;
DROP TABLE IF EXISTS oligoml_pair;
DROP TABLE IF EXISTS oligoml_oligo;
SET FOREIGN_KEY_CHECKS = 1;

-- 
-- Table structure for table `oligoml_oligo`
-- 

CREATE TABLE oligoml_oligo (
  prefix int(11) NOT NULL,
  id int(11) NOT NULL,
  release int(11) NOT NULL default '1',
  name varchar(32) NOT NULL,
  sequence varchar(128) NOT NULL,
  modification text,
  box varchar(64) NOT NULL ,
  freezer varchar(64) default NULL,
  rank varchar(64) default NULL,
  comments text,
  `type` int(11) default NULL,
  design int(11) default NULL,
  program varchar(64) default NULL,
  version double default NULL,
  design_comments text,
  reference text,
  updated timestamp NOT NULL default CURRENT_TIMESTAMP,
  author varchar(32) NOT NULL,
  PRIMARY KEY (prefix,id)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 PACK_KEYS=1;


-- 
-- Table structure for table `oligoml_pair`
-- 

CREATE TABLE oligoml_pair (
  prefix int(11) NOT NULL,
  id int(11) NOT NULL,
  release int(11) NOT NULL default '1',
  forward_prefix int(11) NOT NULL,
  forward_id int(11) NOT NULL,
  reverse_prefix int(11) NOT NULL,
  reverse_id int(11) NOT NULL,
  speciesid int(11) default NULL,
  species varchar(128) NOT NULL,
  geneid int(11) default NULL,
  locus varchar(64) default NULL,
  amplicon int(11) default NULL,
  sequenceid varchar(64) default NULL,
  location varchar(64) default NULL,
  pcr text,
  buffer text,
  next_pair_prefix int(11) default NULL,
  next_pair_id int(11) default NULL,
  comments text,
  reference text,
  updated timestamp NOT NULL default CURRENT_TIMESTAMP,
  author varchar(32) NOT NULL,
  PRIMARY KEY  (prefix,id)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 PACK_KEYS=1;


-- 
-- Constraints for table `oligoml_oligo`
-- 
ALTER TABLE `oligoml_oligo`
  ADD CONSTRAINT oligoml_oligo_author FOREIGN KEY (author) REFERENCES users (username);


-- 
-- Constraints for table `oligoml_pair`
-- 
ALTER TABLE `oligoml_pair`
  ADD CONSTRAINT oligoml_pair_author FOREIGN KEY (author) REFERENCES users (username),
  ADD CONSTRAINT oligoml_pair_forward FOREIGN KEY (forward_prefix, forward_id) REFERENCES oligoml_oligo (prefix, id),
  ADD CONSTRAINT oligoml_pair_reverse FOREIGN KEY (reverse_prefix, reverse_id) REFERENCES oligoml_oligo (prefix, id);





INSERT INTO oligoml_oligo VALUES (47, 1, 1, 'GJA3.5end.f', 'ACAGCCGTGCTTCCCTTGCAGG', NULL, 'Mic.1', 'Frank (res 4)', '11', NULL, NULL, 2, 'UniPrime', 1.1000000000000001, NULL, NULL, '2006-12-13 13:09:15.206927', 'michael');
INSERT INTO oligoml_oligo VALUES (47, 2, 1, 'GJA3.3end.r', 'TTGTAAGGCTGTCTGGAGAGAC', NULL, 'Mic.1', 'Frank (res 4)', '12', NULL, NULL, 2, 'UniPrime', 1.1000000000000001, NULL, NULL, '2006-12-13 13:09:15.217385', 'michael');
INSERT INTO oligoml_oligo VALUES (47, 3, 1, 'GJA8.intron1.f', 'GGCCGCTCAGATTTTGCCTTC', NULL, 'Mic.1', 'Frank (res 4)', '13', NULL, NULL, 2, 'UniPrime', 1.1000000000000001, NULL, NULL, '2006-12-13 13:09:15.224396', 'michael');
INSERT INTO oligoml_oligo VALUES (47, 4, 1, 'BBS7.52.f', 'TCTCCATCCTAAAAGATGTGC', NULL, 'Mic.1', 'Frank (res 4)', '15', NULL, NULL, 2, 'UniPrime', 1.1000000000000001, NULL, NULL, '2006-12-13 13:09:15.231359', 'michael');
INSERT INTO oligoml_oligo VALUES (47, 5, 1, 'BBS7.52.r', 'GCAGGTTGCTTTTTGTATTC', NULL, 'Mic.1', 'Frank (res 4)', '16', NULL, NULL, 2, 'UniPrime', 1.1000000000000001, NULL, NULL, '2006-12-13 13:09:15.239096', 'michael');
INSERT INTO oligoml_oligo VALUES (47, 6, 1, 'CRX.2.f', 'AAGGCTCGTCCTGCCAAGAG', NULL, 'Mic.1', 'Frank (res 4)', '17', NULL, NULL, 2, 'UniPrime', 1.1000000000000001, NULL, NULL, '2006-12-13 13:09:15.244292', 'michael');
INSERT INTO oligoml_oligo VALUES (47, 7, 1, 'CRX.2.r', 'CGTCTTCTACAAGATCTGAAACTTCC', NULL, 'Mic.1', 'Frank (res 4)', '18', NULL, NULL, 2, 'UniPrime', 1.1000000000000001, NULL, NULL, '2006-12-13 13:09:15.249955', 'michael');
INSERT INTO oligoml_oligo VALUES (47, 8, 1, 'CRYAB.54.f', 'GAACATTCTGGGACATTCCTG', NULL, 'Mic.1', 'Frank (res 4)', '19', NULL, NULL, 2, 'UniPrime', 1.1000000000000001, NULL, NULL, '2006-12-13 13:09:15.257558', 'michael');
INSERT INTO oligoml_oligo VALUES (47, 9, 1, 'CRYAB.54.r', 'ATCCGGTATTTCCTGTGGAAC', NULL, 'Mic.1', 'Frank (res 4)', '20', NULL, NULL, 2, 'UniPrime', 1.1000000000000001, NULL, NULL, '2006-12-13 13:09:15.262882', 'michael');
INSERT INTO oligoml_oligo VALUES (47, 10, 1, 'KERA.51.f', 'TGAGAAGCCATTTGAGAATGC', NULL, 'Mic.1', 'Frank (res 4)', '21', NULL, NULL, 2, 'UniPrime', 1.1000000000000001, NULL, NULL, '2006-12-13 13:09:15.268849', 'michael');
INSERT INTO oligoml_oligo VALUES (47, 11, 1, 'KERA.51.r', 'TGATCAAGGTGAAGGTGCTG', NULL, 'Mic.1', 'Frank (res 4)', '22', NULL, NULL, 2, 'UniPrime', 1.1000000000000001, NULL, NULL, '2006-12-13 13:09:15.275704', 'michael');
INSERT INTO oligoml_oligo VALUES (47, 12, 1, 'NR2E3.10.f', 'AGTGGTCTCTGCCTCTGGAC', NULL, 'Mic.1', 'Frank (res 4)', '23', NULL, NULL, 2, 'UniPrime', 1.1000000000000001, NULL, NULL, '2006-12-13 13:09:15.2808', 'michael');
INSERT INTO oligoml_oligo VALUES (47, 13, 1, 'NR2E3.10.r', 'AGAGAAGCAGCAGCAGTGAG', NULL, 'Mic.1', 'Frank (res 4)', '24', NULL, NULL, 2, 'UniPrime', 1.1000000000000001, NULL, NULL, '2006-12-13 13:09:15.287813', 'michael');
INSERT INTO oligoml_oligo VALUES (47, 14, 1, 'OPN1SW.12.f', 'CTTTGAGCGCTACATTGTCATC', NULL, 'Mic.1', 'Frank (res 4)', '25', NULL, NULL, 2, 'UniPrime', 1.1000000000000001, NULL, NULL, '2006-12-13 13:09:15.293577', 'michael');
INSERT INTO oligoml_oligo VALUES (47, 15, 1, 'OPN1SW.12.r', 'GAAGCAGATGAGGGAGAGAGG', NULL, 'Mic.1', 'Frank (res 4)', '26', NULL, NULL, 2, 'UniPrime', 1.1000000000000001, NULL, NULL, '2006-12-13 13:09:15.299191', 'michael');
INSERT INTO oligoml_oligo VALUES (47, 16, 1, 'PDE6C.17.f', 'TTTCTCTTTCTAGCCTATGATGGAC', NULL, 'Mic.1', 'Frank (res 4)', '27', NULL, NULL, 2, 'UniPrime', 1.1000000000000001, NULL, NULL, '2006-12-13 13:09:15.30617', 'michael');
INSERT INTO oligoml_oligo VALUES (47, 17, 1, 'PDE6C.17.r', 'CTGCTTTTTCACCTCCTCTTC', NULL, 'Mic.1', 'Frank (res 4)', '28', NULL, NULL, 2, 'UniPrime', 1.1000000000000001, NULL, NULL, '2006-12-13 13:09:15.311398', 'michael');
INSERT INTO oligoml_oligo VALUES (47, 18, 1, 'PDE6G.1.f', 'ACGACATCCCTGGAATGGAAGG', NULL, 'Mic.1', 'Frank (res 4)', '29', NULL, NULL, 2, 'UniPrime', 1.1000000000000001, NULL, NULL, '2006-12-13 13:09:15.31745', 'michael');
INSERT INTO oligoml_oligo VALUES (47, 19, 1, 'PDE6G.1.r', 'ATGCCATAATGGGCCAGCTC', NULL, 'Mic.1', 'Frank (res 4)', '30', NULL, NULL, 2, 'UniPrime', 1.1000000000000001, NULL, NULL, '2006-12-13 13:09:15.324467', 'michael');
INSERT INTO oligoml_oligo VALUES (47, 20, 1, 'PRPF3.35.f', 'CTTTCCCTTCTCCAGAGTTCG', NULL, 'Mic.1', 'Frank (res 4)', '31', NULL, NULL, 2, 'UniPrime', 1.1000000000000001, NULL, NULL, '2006-12-13 13:09:15.329591', 'michael');
INSERT INTO oligoml_oligo VALUES (47, 21, 1, 'PRPF3.35.r', 'CCCACAGCTCTCCCACTTAATC', NULL, 'Mic.1', 'Frank (res 4)', '32', NULL, NULL, 2, 'UniPrime', 1.1000000000000001, NULL, NULL, '2006-12-13 13:09:15.336039', 'michael');
INSERT INTO oligoml_oligo VALUES (47, 22, 1, 'PRPF8.39.f', 'ATCGTAGATTCACCCTCTGGTG', NULL, 'Mic.1', 'Frank (res 4)', '33', NULL, NULL, 2, 'UniPrime', 1.1000000000000001, NULL, NULL, '2006-12-13 13:09:15.341138', 'michael');
INSERT INTO oligoml_oligo VALUES (47, 23, 1, 'PRPF8.39.r', 'CCGGGAGACATTCCACTTATAG', NULL, 'Mic.1', 'Frank (res 4)', '34', NULL, NULL, 2, 'UniPrime', 1.1000000000000001, NULL, NULL, '2006-12-13 13:09:15.344924', 'michael');
INSERT INTO oligoml_oligo VALUES (47, 24, 1, 'RDH5.20.f', 'GCAGTGCTGTGGTTGCTCAG', NULL, 'Mic.1', 'Frank (res 4)', '35', NULL, NULL, 2, 'UniPrime', 1.1000000000000001, NULL, NULL, '2006-12-13 13:09:15.349162', 'michael');
INSERT INTO oligoml_oligo VALUES (47, 25, 1, 'RDH5.20.r', 'ACACCAGCTTTATTCACCAGAC', NULL, 'Mic.1', 'Frank (res 4)', '36', NULL, NULL, 2, 'UniPrime', 1.1000000000000001, NULL, NULL, '2006-12-13 13:09:15.353246', 'michael');
INSERT INTO oligoml_oligo VALUES (47, 26, 1, 'TYR.21.f', 'TTTATAATAGGACCTGCCAGTGC', NULL, 'Mic.1', 'Frank (res 4)', '37', NULL, NULL, 2, 'UniPrime', 1.1000000000000001, NULL, NULL, '2006-12-13 13:09:15.357304', 'michael');
INSERT INTO oligoml_oligo VALUES (47, 27, 1, 'TYR.21.r', 'TCTTACCTGCCAAGAGGAGAAG', NULL, 'Mic.1', 'Frank (res 4)', '38', NULL, NULL, 2, 'UniPrime', 1.1000000000000001, NULL, NULL, '2006-12-13 13:09:15.360943', 'michael');
INSERT INTO oligoml_oligo VALUES (47, 28, 1, 'UNC119.22.f', 'CCCCTGAGGAGAATATCTACAAG', NULL, 'Mic.1', 'Frank (res 4)', '39', NULL, NULL, 2, 'UniPrime', 1.1000000000000001, NULL, NULL, '2006-12-13 13:09:15.364693', 'michael');
INSERT INTO oligoml_oligo VALUES (47, 29, 1, 'UNC119.22.r', 'CGTGAACTGGTAGCGGACAAAG', NULL, 'Mic.1', 'Frank (res 4)', '40', NULL, NULL, 2, 'UniPrime', 1.1000000000000001, NULL, NULL, '2006-12-13 13:09:15.368638', 'michael');
INSERT INTO oligoml_oligo VALUES (47, 30, 1, 'VMD2.30.f', 'ATTTCCTTCGTGCTGGGTGAG', NULL, 'Mic.1', 'Frank (res 4)', '41', NULL, NULL, 2, 'UniPrime', 1.1000000000000001, NULL, NULL, '2006-12-13 13:09:15.372755', 'michael');
INSERT INTO oligoml_oligo VALUES (47, 31, 1, 'VMD2.30.r', 'AGGTTGGCGTAGCGGATGAG', NULL, 'Mic.1', 'Frank (res 4)', '42', NULL, NULL, 2, 'UniPrime', 1.1000000000000001, NULL, NULL, '2006-12-13 13:09:15.3766', 'michael');
INSERT INTO oligoml_oligo VALUES (47, 32, 1, 'OPN1LM.5end.f', 'GCATTACAGGGGCATGGCCCAG', NULL, 'Mic.1', 'Frank (res 4)', '43', NULL, NULL, 2, 'UniPrime', 1.1000000000000001, NULL, NULL, '2006-12-13 13:09:15.380302', 'michael');
INSERT INTO oligoml_oligo VALUES (47, 33, 1, 'OPN1LM.3end.r', 'CTCAGGGGCCCCTCTCTGTGAC', NULL, 'Mic.1', 'Frank (res 4)', '44', NULL, NULL, 2, 'UniPrime', 1.1000000000000001, NULL, NULL, '2006-12-13 13:09:15.384188', 'michael');
INSERT INTO oligoml_oligo VALUES (47, 34, 1, 'ABCA4.2.f', 'ATGAATGTGAGCGGGGTATG', NULL, 'Mic.1', 'Frank (res 4)', '1', NULL, NULL, 2, 'UniPrime', 1.1000000000000001, NULL, NULL, '2006-12-13 13:09:15.387906', 'michael');
INSERT INTO oligoml_oligo VALUES (47, 35, 1, 'ABCA4.2.r', 'GCTGCTCCTTGGTCAGGTTC', NULL, 'Mic.1', 'Frank (res 4)', '2', NULL, NULL, 2, 'UniPrime', 1.1000000000000001, NULL, NULL, '2006-12-13 13:09:15.391499', 'michael');
INSERT INTO oligoml_oligo VALUES (47, 36, 1, 'RLBP1.3.f', 'TCTCTCTGTCACTGCAGGATTC', NULL, 'Mic.1', 'Frank (res 4)', '3', NULL, NULL, 2, 'UniPrime', 1.1000000000000001, NULL, NULL, '2006-12-13 13:09:15.39512', 'michael');
INSERT INTO oligoml_oligo VALUES (47, 37, 1, 'RLBP1.3.r', 'AGGATGTTCTCATCAATCTCCTG', NULL, 'Mic.1', 'Frank (res 4)', '4', NULL, NULL, 2, 'UniPrime', 1.1000000000000001, NULL, NULL, '2006-12-13 13:09:15.399107', 'michael');
INSERT INTO oligoml_oligo VALUES (47, 38, 1, 'GJA3.7.f', 'TGACCGTGCTGTTCATCTTC', NULL, 'Mic.1', 'Frank (res 4)', '5', NULL, NULL, 2, 'UniPrime', 1.1000000000000001, NULL, NULL, '2006-12-13 13:09:15.402872', 'michael');
INSERT INTO oligoml_oligo VALUES (47, 39, 1, 'GJA3.7.r', 'CCTGCTTGAGCTTCTTCCAG', NULL, 'Mic.1', 'Frank (res 4)', '6', NULL, NULL, 2, 'UniPrime', 1.1000000000000001, NULL, NULL, '2006-12-13 13:09:15.40653', 'michael');
INSERT INTO oligoml_oligo VALUES (47, 40, 1, 'GJA8.1.f', 'AATGAGCACTCCACCGTCATC', NULL, 'Mic.1', 'Frank (res 4)', '7', NULL, NULL, 2, 'UniPrime', 1.1000000000000001, NULL, NULL, '2006-12-13 13:09:15.410159', 'michael');
INSERT INTO oligoml_oligo VALUES (47, 41, 1, 'GJA8.1.r', 'GACACGAAGCAGTCCACCAC', NULL, 'Mic.1', 'Frank (res 4)', '8', NULL, NULL, 2, 'UniPrime', 1.1000000000000001, NULL, NULL, '2006-12-13 13:09:15.413857', 'michael');
INSERT INTO oligoml_oligo VALUES (47, 42, 1, 'EFEMP1.21.f', 'GCATTGCAAAACTCTGTATGG', NULL, 'Mic.1', 'Frank (res 4)', '9', NULL, NULL, 2, 'UniPrime', 1.1000000000000001, NULL, NULL, '2006-12-13 13:09:15.417793', 'michael');
INSERT INTO oligoml_oligo VALUES (47, 43, 1, 'EFEMP1.21.r', 'TACCTTCACAGTTGAGCCTGTC', NULL, 'Mic.1', 'Frank (res 4)', '10', NULL, NULL, 2, 'UniPrime', 1.1000000000000001, NULL, NULL, '2006-12-13 13:09:15.421553', 'michael');

INSERT INTO oligoml_pair VALUES (47, 1, 1, 47, 1, 47, 2, NULL, 'Mammalia', NULL, 'GJA3', NULL, NULL, '5'' UTR - 3'' UTR', 'TouchDown from 60&deg; to 50&deg;, 30x 60&deg;', NULL, NULL, NULL, NULL, NULL, '2006-12-13 13:09:15.421553', 'michael');
INSERT INTO oligoml_pair VALUES (47, 2, 1, 47, 3, 47, 41, NULL, 'Mammalia', NULL, 'GJA8', NULL, NULL, 'exon 1 - exon 2', 'TouchDown from 60&deg; to 50&deg;, 30x 60&deg;', NULL, NULL, NULL, NULL, NULL, '2006-12-13 13:09:15.421553', 'michael');
INSERT INTO oligoml_pair VALUES (47, 3, 1, 47, 4, 47, 5, NULL, 'Mammalia', NULL, 'BBS7', 500, NULL, 'intron 16', 'TouchDown from 60&deg; to 50&deg;, 30x 60&deg;', NULL, NULL, NULL, NULL, NULL, '2006-12-13 13:09:15.421553', 'michael');
INSERT INTO oligoml_pair VALUES (47, 4, 1, 47, 6, 47, 7, NULL, 'Mammalia', NULL, 'CRX', NULL, NULL, NULL, 'TouchDown from 60&deg; to 50&deg;, 30x 60&deg;', NULL, NULL, NULL, NULL, NULL, '2006-12-13 13:09:15.421553', 'michael');
INSERT INTO oligoml_pair VALUES (47, 5, 1, 47, 8, 47, 9, NULL, 'Mammalia', NULL, 'CRYAB', NULL, NULL, NULL, 'TouchDown from 60&deg; to 50&deg;, 30x 60&deg;', NULL, NULL, NULL, NULL, NULL, '2006-12-13 13:09:15.421553', 'michael');
INSERT INTO oligoml_pair VALUES (47, 6, 1, 47, 10, 47, 11, NULL, 'Mammalia', NULL, 'KERA', NULL, NULL, NULL, 'TouchDown from 60&deg; to 50&deg;, 30x 60&deg;', NULL, NULL, NULL, NULL, NULL, '2006-12-13 13:09:15.421553', 'michael');
INSERT INTO oligoml_pair VALUES (47, 7, 1, 47, 12, 47, 13, NULL, 'Mammalia', NULL, 'NR2E3', 615, NULL, 'exon 6 - exon 7', 'TouchDown from 60&deg; to 50&deg;, 30x 60&deg;', NULL, NULL, NULL, NULL, NULL, '2006-12-13 13:09:15.421553', 'michael');
INSERT INTO oligoml_pair VALUES (47, 8, 1, 47, 14, 47, 15, NULL, 'Mammalia', NULL, 'OPN1SW', NULL, NULL, NULL, 'TouchDown from 60&deg; to 50&deg;, 30x 60&deg;', NULL, NULL, NULL, NULL, NULL, '2006-12-13 13:09:15.421553', 'michael');
INSERT INTO oligoml_pair VALUES (47, 9, 1, 47, 16, 47, 17, NULL, 'Mammalia', NULL, 'PDE6C', NULL, NULL, NULL, 'TouchDown from 60&deg; to 50&deg;, 30x 60&deg;', NULL, NULL, NULL, NULL, NULL, '2006-12-13 13:09:15.421553', 'michael');
INSERT INTO oligoml_pair VALUES (47, 10, 1, 47, 18, 47, 19, NULL, 'Mammalia', NULL, 'PDE6G', NULL, NULL, NULL, 'TouchDown from 60&deg; to 50&deg;, 30x 60&deg;', NULL, NULL, NULL, NULL, NULL, '2006-12-13 13:09:15.421553', 'michael');
INSERT INTO oligoml_pair VALUES (47, 11, 1, 47, 20, 47, 21, NULL, 'Mammalia', NULL, 'PRPF3', NULL, NULL, NULL, 'TouchDown from 60&deg; to 50&deg;, 30x 60&deg;', NULL, NULL, NULL, NULL, NULL, '2006-12-13 13:09:15.421553', 'michael');
INSERT INTO oligoml_pair VALUES (47, 12, 1, 47, 22, 47, 23, NULL, 'Mammalia', NULL, 'PRPF8', NULL, NULL, NULL, 'TouchDown from 60&deg; to 50&deg;, 30x 60&deg;', NULL, NULL, NULL, NULL, NULL, '2006-12-13 13:09:15.421553', 'michael');
INSERT INTO oligoml_pair VALUES (47, 13, 1, 47, 24, 47, 25, NULL, 'Mammalia', NULL, 'RDH5', 490, NULL, 'exon 2 - exon 3', 'TouchDown from 60&deg; to 50&deg;, 30x 60&deg;', NULL, NULL, NULL, NULL, NULL, '2006-12-13 13:09:15.421553', 'michael');
INSERT INTO oligoml_pair VALUES (47, 14, 1, 47, 26, 47, 27, NULL, 'Mammalia', NULL, 'TYR', NULL, NULL, NULL, 'TouchDown from 60&deg; to 50&deg;, 30x 60&deg;', NULL, NULL, NULL, NULL, NULL, '2006-12-13 13:09:15.421553', 'michael');
INSERT INTO oligoml_pair VALUES (47, 15, 1, 47, 28, 47, 29, NULL, 'Mammalia', NULL, 'UNC119', NULL, NULL, NULL, 'TouchDown from 60&deg; to 50&deg;, 30x 60&deg;', NULL, NULL, NULL, NULL, NULL, '2006-12-13 13:09:15.421553', 'michael');
INSERT INTO oligoml_pair VALUES (47, 16, 1, 47, 30, 47, 30, NULL, 'Mammalia', NULL, 'VMD2', NULL, NULL, NULL, 'TouchDown from 60&deg; to 50&deg;, 30x 60&deg;', NULL, NULL, NULL, NULL, NULL, '2006-12-13 13:09:15.421553', 'michael');
INSERT INTO oligoml_pair VALUES (47, 17, 1, 47, 32, 47, 33, NULL, 'Mammalia', NULL, 'OPN1LM', NULL, NULL, NULL, 'TouchDown from 60&deg; to 50&deg;, 30x 60&deg;', NULL, NULL, NULL, NULL, NULL, '2006-12-13 13:09:15.421553', 'michael');
INSERT INTO oligoml_pair VALUES (47, 18, 1, 47, 34, 47, 35, NULL, 'Mammalia', NULL, 'ABCA4', 600, NULL, 'intron 33 - exon 35', 'TouchDown from 60&deg; to 50&deg;, 30x 60&deg;', NULL, NULL, NULL, NULL, NULL, '2006-12-13 13:09:15.421553', 'michael');
INSERT INTO oligoml_pair VALUES (47, 19, 1, 47, 36, 47, 37, NULL, 'Mammalia', NULL, 'RLBP1', NULL, NULL, NULL, 'TouchDown from 60&deg; to 50&deg;, 30x 60&deg;', NULL, NULL, NULL, NULL, NULL, '2006-12-13 13:09:15.421553', 'michael');
INSERT INTO oligoml_pair VALUES (47, 20, 1, 47, 31, 47, 39, NULL, 'Mammalia', NULL, 'GJA3', 620, NULL, 'exon 1', 'TouchDown from 60&deg; to 50&deg;, 30x 60&deg;', NULL, NULL, NULL, NULL, NULL, '2006-12-13 13:09:15.421553', 'michael');
INSERT INTO oligoml_pair VALUES (47, 21, 1, 47, 40, 47, 41, NULL, 'Mammalia', NULL, 'GJA8', 550, NULL, 'exon 2', 'TouchDown from 60&deg; to 50&deg;, 30x 60&deg;', NULL, NULL, NULL, NULL, NULL, '2006-12-13 13:09:15.421553', 'michael');
INSERT INTO oligoml_pair VALUES (47, 22, 1, 47, 42, 47, 43, NULL, 'Mammalia', NULL, 'EFEMP1', 640, NULL, 'intron 6 - exon 7', 'TouchDown from 60&deg; to 50&deg;, 30x 60&deg;', NULL, NULL, NULL, NULL, NULL, '2006-12-13 13:09:15.421553', 'michael');

