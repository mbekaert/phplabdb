<?php
 function sql_connect($database) {
  global $sqlhost,$sqllogin,$sqlpassword;
//  if($result=pg_connect("host=$sqlhost port=5432 dbname=$database user=$sqllogin password=$sqlpassword")) return $result;
  if($result=pg_connect("dbname=$database")) return $result;
 };

 function sql_query($query,$link) {
  if($result=pg_query($link,$query)) return $result;
 };

 function sql_reg($name) {
  return "~*'$name'";
 };

 function  sql_last_error($link) {
  return pg_last_error($link);
 };
 
 function sql_num_rows ($result) {
  if($row=pg_num_rows($result)) return $row;
 };

 function sql_fetch_row ($result) {
  if($row=pg_fetch_row($result)) return $row;
 };
?>
