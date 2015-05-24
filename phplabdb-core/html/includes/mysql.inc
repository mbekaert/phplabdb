<?php
 function sql_connect($database) {
  global $sqlhost,$sqllogin,$sqlpassword;
  if($result=mysql_connect($sqlhost, $sqllogin, $sqlpassword)) {
   if(mysql_select_db($database,$result)) return $result;
  };
 };

 function sql_query($query,$link) {
  if($result=mysql_query($query,$link)) return $result;
 };

 function sql_reg($name) {
  return " LIKE \"%$name%\"";
 };

 function  sql_last_error($link) {
  return mysql_error();
 };
 
 function sql_num_rows ($result) {
  if($row=mysql_num_rows($result)) return $row;
 };

 function sql_fetch_row ($result) {
  if($row=mysql_fetch_row($result)) return $row;
 };
?>
