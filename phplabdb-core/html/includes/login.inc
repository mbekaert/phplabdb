<?php
 global $base_url, $version, $sqlhost, $sqllogin, $sqlpassword, $sqlserver, $sqldump, $sqlrestaure, $plugin_name, $plugin_title, $plugin_version, $plugin_dir, $plugin_db, $plugin_level, $plugin_status, $plugin_desc;
 include_once 'i18n.inc';
 include_once 'config.inc';
 if ($sqlserver=='postgresql') {
  include_once 'postgresql.inc';
 } elseif ($sqlserver=='mysql') {
  include_once 'mysql.inc';
 } else {
  mes_error("SQL server type '$sqlserver' unknown on this server");
 };
 bindtextdomain('phplabdb', $_SERVER['DOCUMENT_ROOT'] . $base_url . 'locale');
 textdomain('phplabdb');

 function mes_error($message) {
  print "<!DOCTYPE HTML PUBLIC \"-//IETF//DTD HTML 2.0//EN\">\n<HTML><HEAD>\n<TITLE>phpLabDB Error</TITLE>\n</HEAD><BODY>\n<H1>Error</H1>\n$message.<P>\n<HR>\n<ADDRESS>phpLabDB/$version Server at <A HREF=\"mailto:" . $_SERVER['SERVER_ADMIN'] . "\">" . $_SERVER['SERVER_NAME'] .  "</A> Port " . $_SERVER['SERVER_PORT'] . "</ADDRESS>\n</BODY></HTML>";
  exit();
 };

 function checkid() {
  global $base_url;
  $lang=((isset($_COOKIE['lang']))?$_COOKIE['lang']:'en_US');
  if (isset($_COOKIE['user_id'])) {
   if (!isset($_SESSION['status'])) {
    $sql=sql_connect('db_phplabdb');
    $result=sql_query("SELECT status FROM membres WHERE id='" . $_COOKIE['user_id'] . "' AND ip='" . $_SERVER['REMOTE_ADDR'] . "';",$sql);
    if (sql_num_rows($result)==1) {
     $row=sql_fetch_row($result);
     session_cache_expire(31104000);
     $_SESSION['status']=$row[0];
    } else {
     session_unset();
     session_destroy();
     setcookie('user_id','',0,'/');
     setcookie(session_name(),'',0,'/');
     header("Location: " . $base_url . "nologin.php");
     exit;
    };
   };
  } else {
   session_unset();
   session_destroy();
   setcookie(session_name(),'',0,'/');
   header("Location: " . $base_url . "nologin.php");
   exit;
  };
  return $_SESSION['status'];
 };

 function header_start() {
  global $organisation;
  $lang=((isset($_COOKIE['lang']))?$_COOKIE['lang']:'en_US');
  if ( !ini_get('safe_mode') && getenv( 'LC_ALL' ) != $lang ) {
   putenv( "LC_ALL=$lang" );
   putenv( "LANG=$lang" );
   putenv( "LANGUAGE=$lang" );
  };
  setlocale(LC_ALL, $lang);
  $sql=sql_connect ('db_phplabdb');
  $result=sql_query("SELECT name, url, logo, welcome FROM organisation;",$sql);
  if (sql_num_rows($result)==1) {
   $organisation=sql_fetch_row($result);
  } else {
   $organisation=array('phpLabDB','http://phplabdb.sourceforge.net',$base_url.'images/header-phplabdb_logo.png','');
  };
  header('Cache-Control: no-store, no-cache');
  header('Expires: ' . gmdate("D, d M Y H:i:s") . ' GMT');
 };
?>