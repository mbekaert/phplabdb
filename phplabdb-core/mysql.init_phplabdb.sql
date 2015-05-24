-- phpLabDB core 
--
-- Copyright 2003-2005 M.Bekaert
-- http://phplabdb.sourceforge.net
--
-- MySQL database: db_phplabdb
---------------------------------------------------------

--
-- CREATE USER phplabdb
--

USE mysql;

INSERT INTO user (Host,User,Password,Reload_priv) VALUES ('localhost','phplabdb',PASSWORD('wlclj7rwa77a'),'Y');

CREATE DATABASE db_phplabdb;

GRANT ALL ON db_phplabdb.* TO phplabdb IDENTIFIED BY 'wlclj7rwa77a';

FLUSH PRIVILEGES;
