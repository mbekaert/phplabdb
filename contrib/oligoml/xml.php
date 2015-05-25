<?php
ob_start("ob_gzhandler");
session_start();
require_once('../includes/main.inc');

function get_oligo($oligo, $sql) {
  global $config, $plugin;
  $result = sql_query('SELECT prefix, id, release, name, sequence, modification, box, freezer, rank, comments, design, program, version, design_comments, reference, updated, author FROM oligoml_oligo WHERE (prefix=' . octdec(intval($oligo[2])) . ' AND id=' . octdec(intval($oligo[3])) . ');', $sql);
  if ((!strlen($r = sql_last_error($sql))) && (sql_num_rows($result) == 1)) {
    $row = sql_fetch_row($result);
    print "\t<oligo id=\"O" . decoct($row[0]) . '.' . decoct($row[1]) . '" ref="' . $config['server'] . '/O' . decoct($row[0]) . '.' . decoct($row[1]) . "\">\n";
    print "\t\t<name>" . $row[3] . "</name>\n";
    print "\t\t<sequence>" . $row[4] . "</sequence>\n";
    if (!empty($row[5])) print "\t\t<modification>" . $row[5] . "</modification>\n";
    print "\t\t<location>\n";
    print "\t\t\t<box>" . $row[6] . "</box>\n";
    if (!empty($row[7])) print "\t\t\t<freezer>" . $row[7] . "</freezer>\n";
    if (!empty($row[8])) print "\t\t\t<rank>" . $row[8] . "</rank>\n";
    print "\t\t</location>\n";
    if (!empty($row[9])) print "\t\t<comments><![CDATA[" . htmlentities($row[9], ENT_COMPAT, 'ISO-8859-15') . "]]></comments>\n";
    print "\t\t<revision version=\"" . $row[2] . "\">\n";
    print "\t\t\t<author>" . $row[16] . "</author>\n";
    print "\t\t\t<date>" . gmdate("Y-m-d\TH:i:s+00:00", strtotime($row[15])) . "</date>\n";
    print "\t\t</revision>\n";
    if (!empty($row[10]) && ($row[10] == 1)) {
      print "\t\t<reference class=\"manual\" />\n";
    }elseif (!empty($row[10]) && ($row[10] == 2)) {
      print "\t\t<reference class=\"software\">\n";
      if (!empty($row[11])) print "\t\t\t<program" . (!empty($row[12]) ? (' version="' . $row[12] . '"') : '') . '>' . $row[11] . "</program>\n";
      if (!empty($row[13])) print "\t\t\t<comments><![CDATA[" . htmlentities($row[13], ENT_COMPAT, 'ISO-8859-15') . "]]></comments>\n";
      print "\t\t</reference>\n";
    }
    if (!empty($row[14])) put_ref($row[14], $sql);
    print "\t</oligo>\n";
  }
}

if ($config['login'] && !empty($_GET['xml']) && preg_match('/([OP])(\d+)\.(\d+)/', rawurldecode($_GET['xml']), $matches)) {
  $sql = sql_connect($config['db']);
  header('Content-Type: application/xml; charset=ISO-8859-15');
  print '<!DOCTYPE oligoml PUBLIC "-//OLIGOML//DTD OLIGOML 0.4/EN" "' . $config['server'] . '/dtd/oligoml.dtd">' . "\n";
  print "<oligoml version=\"0.4\">\n";
  if ($matches[1] == 'O') {
    get_oligo($matches, $sql);
  }elseif ($matches[1] == 'P') {
    $result = sql_query('SELECT prefix, id, release, forward_prefix, forward_id, reverse_prefix, reverse_id, speciesid, species, geneid, locus, amplicon, sequenceid, location, pcr, buffer, comments, reference, updated, author FROM oligoml_pair WHERE (prefix=' . octdec(intval($matches[2])) . ' AND id=' . octdec(intval($matches[3])) . ');', $sql);
    if ((!strlen($r = sql_last_error($sql))) && (sql_num_rows($result) == 1)) {
      $row = sql_fetch_row($result);
      get_oligo(array(2 => decoct($row[3]), 3 => decoct($row[4])), $sql);
      get_oligo(array(2 => decoct($row[5]), 3 => decoct($row[6])), $sql);
      print "\t<pair id=\"P" . decoct($row[0]) . '.' . decoct($row[1]) . '" ref="' . $config['server'] . '/P' . decoct($row[0]) . '.' . decoct($row[1]) . "\">\n";
      print "\t\t<forward>" . decoct($row[3]) . '.' . decoct($row[4]) . "</forward>\n";
      print "\t\t<reverse>" . decoct($row[5]) . '.' . decoct($row[6]) . "</reverse>\n";
      print "\t\t<specificity>\n";
      print "\t\t\t<species" . (!empty($row[7]) ? (' id="' . $row[7] . '"') : '') . (!empty($row[8]) ? ('>' . $row[8] . '</species>') : ' />') . "\n";
      print "\t\t\t<target" . (!empty($row[9]) ? (' id="' . $row[9] . '"') : '') . (!empty($row[10]) ? ('>' . $row[10] . '</target>') : ' />') . "\n";
      print "\t\t\t<length" . (!empty($row[12]) ? (' id="' . $row[12] . '"') : '') . (!empty($row[11]) ? ('>' . $row[11] . '</length>') : ' />') . "\n";
      if (!empty($row[13])) print "\t\t\t<location>" . $row[13] . "</location>\n";
      print "\t\t</specificity>\n";
      if (!empty($row[14]) || !empty($row[15])) {
        print "\t\t<condition>\n";
        if (!empty($row[14])) print "\t\t\t<pcr>" . $row[14] . "</pcr>\n";
        if (!empty($row[15])) print "\t\t\t<buffer>" . $row[15] . "</buffer>\n";
        print "\t\t</condition>\n";
      }
      if (!empty($row[16])) print "\t\t<comments><![CDATA[" . htmlentities($row[16], ENT_COMPAT, 'ISO-8859-15') . "]]></comments>\n";
      print "\t\t<revision version=\"" . $row[2] . "\">\n";
      print "\t\t\t<author>" . $row[19] . "</author>\n";
      print "\t\t\t<date>" . gmdate("Y-m-d\TH:i:s+00:00", strtotime($row[18])) . "</date>\n";
      print "\t\t</revision>\n";
      if (!empty($row[17])) put_ref($row[17], $sql);
      print "\t</pair>\n";
    }
  }
  print "</oligoml>\n";
}
?>