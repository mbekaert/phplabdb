-- phplabdb.v2.devel
--
-- Copyright 2006-2007 M.Bekaert
--
-- MySQL database: phplabdb.v2
-- --------------------------------------------------------

-- 
-- Drop tables
-- 

SET FOREIGN_KEY_CHECKS = 0;
DROP TABLE IF EXISTS reference;
DROP TABLE IF EXISTS users;
SET FOREIGN_KEY_CHECKS = 1;

-- 
-- Table structure for table `reference`
-- 

CREATE TABLE reference (
  id int(11) NOT NULL,
  url text,
  doi varchar(128) default NULL,
  title text,
  comments text,
  PRIMARY KEY  (id)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 PACK_KEYS=1;


-- 
-- Table structure for table `users`
-- 

CREATE TABLE users (
  username varchar(32) NOT NULL,
  real_name varchar(128) NOT NULL,
  `password` varchar(32) NOT NULL,
  email varchar(128) NOT NULL,
  taxon text,
  code char(1) NOT NULL default '!',
  rights int(11) NOT NULL default '0',
  activated int(11) NOT NULL default '0',
  active varchar(32) default NULL,
  added timestamp NOT NULL default CURRENT_TIMESTAMP,
  PRIMARY KEY  (username),
  UNIQUE KEY email (email)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 PACK_KEYS=1;


-- 
-- Dumping data for table `users`
-- 

INSERT INTO users VALUES ('michael', 'Michael', MD5('123'), 'michael.bekaert@ucd.ie', 'Bats', '@', 9, 2, NULL, CURRENT_TIMESTAMP);

