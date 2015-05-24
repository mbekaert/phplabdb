<?php
 global $gettext_php_loaded, $gettext_php_translateStrings, $gettext_php_loaded_language, $gettext_php_short_circuit;

 function gettext_php_load_strings() {
  global $gettext_php_translateStrings, $gettext_php_domain, $gettext_php_dir, $gettext_php_loaded, $gettext_php_loaded_language, $gettext_php_short_circuit;
  $gettext_php_translateStrings = array();
  $gettext_php_short_circuit = false;  /* initialization */

  $filename = $gettext_php_dir[$gettext_php_domain];
  if (substr($filename, -1) != '/') $filename .= '/';
  $language=((isset($_COOKIE['lang']))?$_COOKIE['lang']:'en_US');
  $filename .= $language . '/LC_MESSAGES/' . $gettext_php_domain . '.po';

  $file = @fopen($filename, 'r');
  if ($file == false) {
   /* Uh-ho -- we can't load the file.  Just fake it.  */
   $gettext_php_loaded = true;
   $gettext_php_loaded_language = $language;
   $gettext_php_short_circuit = true;  
   return;
  };
   
  $key = '';
  $SkipRead = false;
  while (! feof($file)) {
   if (! $SkipRead) {
    $line = trim(fgets($file, 4096));
   } else {
    $SkipRead = false;
   };
   if (ereg('^msgid "(.*)"$', $line, $match)) {
    if ($match[1] == '') {
     $key = '';
     $line = trim(fgets($file, 4096));
     while (ereg('^[ ]*"(.*)"[ ]*$', $line, $match)) {
      $key .= $match[1];
      $line = trim(fgets($file, 4096));
     };
     $SkipRead = true;
    } else {
     $key = $match[1];
    };
   } elseif (ereg('^msgstr "(.*)"$', $line, $match)) {
    if ($match[1] == '') {
     $gettext_php_translateStrings[$key] = '';
     $line = trim(fgets($file, 4096));
     while (ereg('^[ ]*"(.*)"[ ]*$', $line, $match)) {
      $gettext_php_translateStrings[$key] .= $match[1];
      $line = trim(fgets($file, 4096));
     };
     $SkipRead = true;
    } else {
     $gettext_php_translateStrings[$key] = $match[1];
    };
    $gettext_php_translateStrings[$key] = stripslashes($gettext_php_translateStrings[$key]);
    if ($gettext_php_translateStrings[$key] == '') $gettext_php_translateStrings[$key] = $key;
    $key = '';
   };
  };
  fclose($file);
  $gettext_php_loaded = true;
  $gettext_php_loaded_language = $language;
 };

/* Alternative php gettext function (short form) */
 function _($str) {
  global $gettext_php_loaded, $gettext_php_translateStrings, $gettext_php_loaded_language, $gettext_php_short_circuit;

  if (! $gettext_php_loaded || $gettext_php_loaded_language != ((isset($_COOKIE['lang']))?$_COOKIE['lang']:'en_US')) {
   gettext_php_load_strings();
  };
    
  /* Try finding the exact string */
  if (isset($gettext_php_translateStrings[$str])) {
   return $gettext_php_translateStrings[$str];
  };
    
  /* See if we should short-circuit */
  if ($gettext_php_short_circuit) {
   $gettext_php_translateStrings[$str] = $str;
    return $str;
  };
 };

/* Alternative php bindtextdomain function */
 function bindtextdomain($name, $dir) {
  global $gettext_php_domain, $gettext_php_dir, $gettext_php_loaded;

  if ($gettext_php_domain != $name) {
   $gettext_php_domain = $name;
   $gettext_php_loaded = false;
  };
  if (!isset($gettext_php_dir[$name]) || $gettext_php_dir[$name]!= $dir) {
   $gettext_php_dir[$name] = $dir;
   $gettext_php_loaded = false;
  };
  return $dir;
 };

/* Alternative php textdomain function */
 function textdomain($name = false) {
  global $gettext_php_domain, $gettext_php_loaded;
  if ($name != false && $gettext_php_domain != $name) {
   $gettext_php_domain = $name;
   $gettext_php_loaded = false;
  };
  return $gettext_php_domain;
 };
?>
