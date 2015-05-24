<?php
//phpLabDB
 $base_url = '/'; // for http://localhost/

//phpLabDB version
 $version = '1.6.2';
 
//SQL configuration
 include_once '/var/www/sql.inc';

//postgreSQL
 $sqlserver = 'postgresql';
 $sqldump = '/usr/bin/pg_dump';
 $sqlrestaure = '/usr/bin/pg_restore';

//MySQL
// $sqlserver = 'mysql';
// $sqldump = '/usr/bin/mysqldump'; //Linux
// $sqlrestaure = '/usr/bin/mysql'; //Linux
// $sqldump = '"C:\Program Files\EasyPHP\mysql\bin\mysqldump.exe"'; // Window/EasyPHP
// $sqlrestaure = '"C:\Program Files\EasyPHP\mysql\bin\mysql.exe"'; // Window/EasyPHP
 
//Plugins & Mods
// ex.: include_once 'oligodb.inc';
// ex.: include_once 'plasmiddb.inc';
// ex.: include_once 'seeddb.inc';
// ex.: include_once 'straindb.inc';			
?>
