<?php

/* This function come from squirrelmail project, thanks guys!! 

/* Detect whether gettext is installed. */
 $gettext_flags = 0;
 if (function_exists('_')) {
  $gettext_flags += 1;
 };
 if (function_exists('bindtextdomain')) {
  $gettext_flags += 2;
 };
 if (function_exists('textdomain')) {
  $gettext_flags += 4;
 };

/* If gettext is fully loaded, cool */
 if ($gettext_flags == 7) {
  $use_gettext = true;
 } elseif ($gettext_flags == 0) {
  /* If we can fake gettext, try that */
  $use_gettext = true;
  include_once 'gettext.inc';
 } else {
  /* Uh-ho.  A weird install */
  if (! $gettext_flags & 1) {
   function _($str) {
    return $str;
   };
  };
  if (! $gettext_flags & 2) {
   function bindtextdomain() {
    return;
   };
  };
  if (! $gettext_flags & 4) {
   function textdomain() {
    return;
   };
  };
 };

?>
