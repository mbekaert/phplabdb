<?php
ob_start("ob_gzhandler");
session_start();
require_once('includes/main.inc');

$sql = sql_connect($config['db']);
$i=0;
/*
$result=sql_query('SELECT DISTINCT a.prefix, a.id, a.name FROM uniprime_locus AS a, uniprime_primer AS b WHERE b.locus_prefix=a.prefix AND b.locus_id=a.id AND a.status=4 ORDER BY a.id;',$sql);
while ($row = sql_fetch_row($result)) {
  $i++;
  print '>'.decoct($row[0]).'.'.$row[1].' ('.$row[2].")<br />\n";
  if(isset($_GET['update'])) { sql_query('UPDATE uniprime_locus SET status=4 WHERE status=3 AND prefix='.$row[0].' AND id='.$row[1].';',$sql); }
}
  print '#'.$i."<br />\n";
*/
$result=sql_query('SELECT prefix, id, name FROM uniprime_locus WHERE status='.(!empty($_GET['status'])?intval($_GET['status']):4).' ORDER BY id;',$sql);
while ($row = sql_fetch_row($result)) {
  $i++;
  print '>'.decoct($row[0]).'.'.$row[1].' ('.$row[2].")<br />\n";
}
  print '#'.$i."<br />\n";

?>
