<?php
function trim_alter(&$item) {
  $item = trim($item);
}

function getRNA($key) {
  $url = "http://eutils.ncbi.nlm.nih.gov/entrez/eutils/efetch.fcgi?db=nucleotide&id=$key&rettype=gb";
  $sequence = '';
  if ($genbankFile = fopen($url, "r")) {
    $seqdata_flag = false;
    while (!feof($genbankFile)) {
      $buffer = fgets($genbankFile, 4096);
      if (substr($buffer, 0, 6) == 'ORIGIN') {
        $seqdata_flag = true;
      }elseif ((substr($buffer, 0, 2) != '//') && ($seqdata_flag)) {
        $buffer = preg_replace('/\W/', '', $buffer);
        $sequence .= strtoupper(preg_replace('/\d/', '', $buffer));
      }elseif ((substr($buffer, 0, 2) == '//') && ($seqdata_flag)) {
        $seqdata_flag = false;
      }
    }
    fclose($genbankFile);
    if (strlen($sequence) > 10) return $sequence;
  }
  return false;
}

function getgenbank($key, $start, $end) {
  $translate = array('A' => 'T', 'T' => 'A', 'C' => 'G', 'G' => 'C');
  // //
  if ($start > $end) {
    $tmp = $start;
    $start = $end-25;
    $end = $tmp + 25;
  }else {
    $start -= 25;
    $end += 25;
  }
  // //
  $url = "http://eutils.ncbi.nlm.nih.gov/entrez/eutils/efetch.fcgi?db=nucleotide&id=$key&rettype=gb&seq_start=$start&seq_stop=$end";
  if ($genbankFile = fopen($url, "r")) {
    $flag_o = false;
    $flag_s = false;
    $flag_g = false;
    $flag_c = false;
    $flag_m = false;
    $flag_go = false;
    $seqdata_flag = false;
    $ret['translation'] = 1;
    while (!feof($genbankFile)) {
      $buffer = fgets($genbankFile, 4096);
      if (substr($buffer, 0, 5) == 'LOCUS') {
        $ret['molecule'] = trim(substr($buffer, 44, 7));
        $ret['circular'] = (trim(substr($buffer, 55, 8)) == 'circular'?'t':'f');
        $ret['division'] = trim(strtoupper(substr($buffer, 64, 3)));
        $ret['size'] = intval(trim(substr($buffer, 29, 11)));
        $ret['release'] = strtotime(trim(strtoupper(substr($buffer, 68, 11))));
      }elseif (substr($buffer, 0, 7) == 'VERSION') {
        if (preg_match('/([\d\w\_]+.\d+)/', $buffer, $matches)) {
          $ret['reference'] = $matches[1];
        }
      }elseif (substr($buffer, 0, 6) == 'SOURCE') {
        if (preg_match('/\((.*)\)/', $buffer, $matches)) {
          $ret['common'] = $matches[1];
        }
      }elseif (substr($buffer, 0, 10) == '  ORGANISM') {
        $ret['organism'] = trim(substr($buffer, 12));
        $tax_string = '';
        $flag_o = true;
      }elseif ((substr($buffer, 0, 12) == '            ') && ($flag_o)) {
        $tax_string .= substr($buffer, 12);
      }elseif ((substr($buffer, 0, 12) != '            ') && ($flag_o)) {
        $tax_string = trim($tax_string);
        $tax_string = substr($tax_string, 0, (strlen($tax_string)-1));
        $aTaxonomy = preg_split('/;/', $tax_string, -1, PREG_SPLIT_NO_EMPTY);
        array_walk($aTaxonomy, 'trim_alter');
        $ret['phylum'] = implode(' ; ', $aTaxonomy);
        $flag_o = false;
      }elseif (substr($buffer, 0, 21) == '     source          ') {
        $flag_s = true;
      }elseif ((substr($buffer, 0, 21) == '                     ') && ($flag_s)) {
        if (substr($buffer, 21, 16) == '/db_xref="taxon:') $ret['taxonid'] = intval(substr($buffer, 37, -2));
        if (substr($buffer, 21, 13) == '/chromosome="') $ret['chromosome'] = substr($buffer, 34, -2);
        if (substr($buffer, 21, 12) == '/organelle="') $ret['organelle'] = substr($buffer, 33, -2);
      }elseif ((substr($buffer, 0, 21) == '     gene            ') && (preg_match('/ (complement\()?(join\()?(\d+)\..*\.(\d+)(\))*$/', $buffer, $matches)) && ($matches[3] < 50) && (($ret['size'] - $matches[4]) < 50)) {
        $flag_s = false;
        $begin = $matches[3]-1;
        $lenght = $matches[4] - $matches[3] + 1;
        $ret['strand'] = (($matches[1] == 'complement(')?-1:1);
        $ret['start'] = $start + $matches[3]-1;
        $ret['end'] = $start + $matches[4]-1;
        $ret['location'] = $ret['start'] . '..' . $ret['end'];
        if ($ret['strand'] == -1) {
          $ret['location'] = 'complement(' . $ret['location'] . ')';
        }
        $flag_g = true;
      }elseif ((substr($buffer, 0, 21) == '                     ') && ($flag_g) && ($flag_c == false)) {
        if (substr($buffer, 21, 7) == '/gene="') $ret['gene'] = substr($buffer, 28, -2);
        if (substr($buffer, 21, 17) == '/db_xref="GeneID:') $ret['geneid'] = intval(substr($buffer, 38, -2));
        if (substr($buffer, 21, 15) == '/db_xref="HGNC:') $ret['hgnc'] = intval(substr($buffer, 36, -2));
      }elseif ((substr($buffer, 0, 21) == '     mRNA            ') && (isset($ret['gene']))) {
        $flag_g = false;
        $flag_m = 1;
        $rna = trim(substr($buffer, 21));
      }elseif ((substr($buffer, 0, 21) == '                     ') && ($flag_m)) {
        if ($flag_m == 1) {
          if (strpos($buffer, '/') === false) {
            $rna .= trim(substr($buffer, 21));
          }else {
            $flag_m = 2;
          }
        }
        if ((substr($buffer, 21, 7) == '/gene="') && ($ret['gene'] == substr($buffer, 28, -2))) {
          $flag_m = 3;
        }elseif (($flag_m == 3) && (substr($buffer, 21, 16) == '/transcript_id="')) {
          if (($tmp = getRNA(trim(substr($buffer, 37, -2)))) !== false) {
            $rna = preg_replace("/(\d+)/e", "$start+('\\1')-1", $rna);
            $ret['mRNA'][$rna] = $tmp;
            unset($tmp);
          }
        }
      }elseif ((substr($buffer, 0, 21) == '     CDS             ') && (isset($ret['gene']))) {
        $flag_g = false;
        $flag_m = false;
        $flag_c = 1;
        $go = '';
      }elseif ((substr($buffer, 0, 21) == '                     ') && ($flag_c)) {
        if ((substr($buffer, 21, 7) == '/gene="') && ($ret['gene'] == substr($buffer, 28, -2))) {
          $flag_c = 2;
        }else {
          if (substr($buffer, 21, 13) == '/go_process="') {
            $go = trim(substr($buffer, 34));
            $flag_go = 2;
          }elseif ($flag_go == 2) {
            if (strpos($buffer, '/') === false) {
              $go .= trim(substr($buffer, 21));
            }else {
              $go = substr($go, 0, -1);
              $flag_go = 1;
            }
          }
        }
        if (substr($buffer, 21, 14) == '/transl_table=') $ret['translation'] = intval(substr($buffer, 35));
      }elseif ((isset($ret['gene'])) && (substr($buffer, 0, 6) == 'ORIGIN')) {
        $flag_g = false;
        if (($flag_c == 2) && !empty($go)) {
          $ret['go'] = $go;
        }
        $flag_c = false;
        $seqdata_flag = true;
        $ret['sequence'] = '';
      }elseif ((substr($buffer, 0, 2) != '//') && ($seqdata_flag)) {
        $buffer = preg_replace('/\W/', '', $buffer);
        $ret['sequence'] .= strtoupper(preg_replace('/\d/', '', $buffer));
      }elseif ((substr($buffer, 0, 2) == '//') && ($seqdata_flag)) {
        $ret['sequence'] = substr($ret['sequence'], $begin, $lenght);
        if (isset($ret['strand']) && ($ret['strand'] == -1)) {
          $tmp = '';
          for($i = strlen($ret['sequence'])-1; $i >= 0; $i--) {
            $tmp .= $translate[$ret['sequence'] {
              $i}
            ];
          }
          $ret['sequence'] = $tmp;
          unset($tmp);
        }
        $seqdata_flag = false;
      }elseif (substr($buffer, 0, 21) != '                     ') {
        $flag_g = false;
        $flag_m = false;
        if (($flag_c == 2) && !empty($go)) {
          $ret['go'] = $go;
        }
        $flag_c = false;
      }
    }
    fclose($genbankFile);
    return $ret;
  }else {
    return false;
  }
}

function addLocus($entry, $delay, $sql) {
  global $config;
  $msg = '';
  if (isset($delay) || ((($entry['genbank'] = getgenbank($entry['sequence']['accession'], $entry['sequence']['start'], $entry['sequence']['end'])) !== false) && !empty($entry['genbank']['gene']) && !empty($entry['genbank']['location']) && !empty($entry['genbank']['molecule']) && !empty($entry['genbank']['circular']) && !empty($entry['genbank']['reference']) && !empty($entry['genbank']['geneid']) && !empty($entry['genbank']['strand']) && !empty($entry['genbank']['sequence']) && !empty($entry['genbank']['division']) && !empty($entry['genbank']['organism']) && !empty($entry['genbank']['phylum']) && !empty($entry['genbank']['taxonid']))) {
    $result = sql_query('INSERT INTO locus (name,alias,locus_type,evidence,class,status,functions,comments,sources,reviewer) VALUES (\'' . addslashes($entry['locus']['name']) . '\',' . (isset($entry['locus']['alias'])?'\'' . addslashes($entry['locus']['alias']) . '\'':'NULL') . ',' . $entry['locus']['type'] . ',\'' . $entry['locus']['evidence'] . '\',' . $entry['locus']['class'] . ',' . $entry['locus']['status'] . ',' . (isset($entry['locus']['functions'])?'\'' . addslashes($entry['locus']['functions']) . '\'':'NULL') . ',' . (isset($entry['locus']['comments'])?'\'' . addslashes($entry['locus']['comments']) . '\'':'NULL') . ',' . (isset($entry['locus']['references'])?'\'' . addslashes($entry['locus']['references']) . '\'':'NULL') . ',\'' . addslashes($_SERVER['SSL_CLIENT_S_DN_CN']) . '\');', $sql);
    if (!strlen($r = sql_last_error($sql))) {
      $result = sql_query('SELECT id FROM locus WHERE name=\'' . addslashes($entry['locus']['name']) . '\';', $sql);
      if ((!strlen($r = sql_last_error($sql))) && (sql_num_rows($result) == 1)) {
        $row = sql_fetch_row($result);
        $entry['locus']['id'] = intval($row[0]);
        $msg = addSequence($entry, $delay, $sql);
      }else {
        $msg = '        <div class="warning"><p><img src="' . $config['server'] . '/images/warning.png" alt="">&nbsp;' . _("General error: entry unknown") . "</p></div>\n";
      }
    }else {
      $msg = '        <div class="warning"><p><img src="' . $config['server'] . '/images/warning.png" alt="">&nbsp;' . _("Entry invalid, check your data") . "</p></div>\n";
    }
  }else {
    $msg = '        <div class="warning"><p><img src="' . $config['server'] . '/images/warning.png" alt="">&nbsp;' . _("Accession number unknown") . "</p></div>\n";
  }
  return $msg;
}

function addSequence($entry, $delay, $sql) {
  global $config;
  $msg = '';
  if (isset($delay) || isset($entry['genbank']) || ((($entry['genbank'] = getgenbank($entry['sequence']['accession'], $entry['sequence']['start'], $entry['sequence']['end'])) !== false) && !empty($entry['genbank']['gene']) && !empty($entry['genbank']['location']) && !empty($entry['genbank']['molecule']) && !empty($entry['genbank']['circular']) && !empty($entry['genbank']['reference']) && !empty($entry['genbank']['geneid']) && !empty($entry['genbank']['strand']) && !empty($entry['genbank']['sequence']) && !empty($entry['genbank']['division']) && !empty($entry['genbank']['organism']) && !empty($entry['genbank']['phylum']) && !empty($entry['genbank']['taxonid']))) {
    if (!isset($delay)) {
      $result = sql_query('SELECT id FROM organism WHERE taxonid=' . $entry['genbank']['taxonid'] . ';', $sql);
      if (!strlen($r = sql_last_error($sql))) {
        if (sql_num_rows($result) == 1) {
          $row = sql_fetch_row($result);
          $organism = intval($row[0]);
        }else {
          $result = sql_query('INSERT INTO organism (name,alias,taxon,division,taxonid,reviewer) VALUES (\'' . addslashes($entry['genbank']['organism']) . '\',' . (isset($entry['genbank']['common'])?'\'' . addslashes($entry['genbank']['common']) . '\'':'NULL') . ',\'' . addslashes($entry['genbank']['phylum']) . '\',\'' . addslashes($entry['genbank']['division']) . '\',' . $entry['genbank']['taxonid'] . ',\'' . addslashes($config['powered']) . '\');', $sql);
          if (!strlen($r = sql_last_error($sql))) {
            $result = sql_query('SELECT id FROM organism WHERE taxonid=' . $entry['genbank']['taxonid'] . ';', $sql);
            $row = sql_fetch_row($result);
            $organism = intval($row[0]);
          }else {
            $organism = 'NULL';
          }
        }
        if (count($entry['genbank']['mRNA']) > 0) {
          $result = sql_query('INSERT INTO sequence (locus,name,alias,location,isolate,organelle,translation,molecule,circular,chromosome,map,accession,hgnc,geneid,organism,go,sequence_type,stop,start,strand,sequence,sources,comments,structure,reviewer,evalue,primer) VALUES (' . $entry['locus']['id'] . ',\'' . addslashes($entry['genbank']['gene']) . '\',' . (isset($entry['sequence']['alias'])?'\'' . addslashes($entry['sequence']['alias']) . '\'':'NULL') . ',\'' . addslashes($entry['genbank']['location']) . '\',' . (isset($entry['sequence']['isolate'])?'\'' . addslashes($entry['sequence']['isolate']) . '\'':'NULL') . ',' . (isset($entry['genank']['organelle'])?'\'' . addslashes($entry['genank']['organelle']) . '\'':'NULL') . ',' . $entry['genbank']['translation'] . ',\'' . addslashes($entry['genbank']['molecule']) . '\',\'' . $entry['genbank']['circular'] . '\',' . (isset($entry['genbank']['chromosome'])?'\'' . addslashes($entry['genbank']['chromosome']) . '\'':'NULL') . ',' . (isset($entry['sequence']['map'])?'\'' . addslashes($entry['sequence']['map']) . '\'':'NULL') . ',\'' . $entry['genbank']['reference'] . '\',' . (isset($entry['genbank']['hgnc'])?$entry['genbank']['hgnc']:'NULL') . ',' . $entry['genbank']['geneid'] . ',' . $organism . ',' . (isset($entry['genbank']['go'])?'\'' . addslashes($entry['genbank']['go']) . '\'':'NULL') . ',' . $entry['sequence']['type'] . ',' . $entry['genbank']['end'] . ',' . $entry['genbank']['start'] . ',' . $entry['genbank']['strand'] . ',\'' . addslashes(base64_encode(bzcompress($entry['genbank']['sequence']))) . '\',' . (isset($entry['sequence']['references'])?'\'' . addslashes($entry['sequence']['references']) . '\'':'NULL') . ',' . (isset($entry['sequence']['comments'])?'\'' . addslashes($entry['sequence']['comments']) . '\'':'NULL') . ',' . (isset($entry['sequence']['structure'])?'\'' . addslashes($entry['sequence']['structure']) . '\'':'NULL') . ',\'' . addslashes($_SERVER['SSL_CLIENT_S_DN_CN']) . '\',' . (isset($entry['sequence']['evalue'])?$entry['sequence']['evalue']:'NULL') . ',' . (isset($entry['sequence']['primer'])?$entry['sequence']['primer']:'NULL') . ');', $sql);
          if (!strlen($r = sql_last_error($sql))) {
            print '        <div class="info"><p><img src="' . $config['server'] . '/images/info.png" alt="">&nbsp;' . _("Sequence added") . "</p></div>\n";
            $result = sql_query('SELECT id from sequence WHERE locus=' . $entry['locus']['id'] . ' AND name=\'' . addslashes($entry['genbank']['gene']) . '\' AND accession=\'' . $entry['genbank']['reference'] . '\' AND location=\'' . addslashes($entry['genbank']['location']) . '\' AND sequence_type=' . $entry['sequence']['type'] . ';', $sql);
            $row = sql_fetch_row($result);
            $sequence = intval($row[0]);
            $i = 0;
            foreach ($entry['genbank']['mRNA'] as $loc => $mrna) {
              $result = sql_query('INSERT INTO mrna (locus,sequence,mrna_type,location,mrna,reviewer) VALUES (' . $entry['locus']['id'] . ',' . $sequence . ',' . ++$i . ',\'' . addslashes($loc) . '\',\'' . addslashes(base64_encode(bzcompress($mrna))) . '\',\'' . addslashes($_SERVER['SSL_CLIENT_S_DN_CN']) . '\');', $sql);
              if (strlen($r = sql_last_error($sql))) {
                $msg = '        <div class="warning"><p><img src="' . $config['server'] . '/images/warning.png" alt="">&nbsp;' . _("SQL error, check your data") . "</p></div>\n";
              }
            }
          }else {
            $msg = '        <div class="warning"><p><img src="' . $config['server'] . '/images/warning.png" alt="">&nbsp;' . _("SQL error, check your data") . "</p></div>\n";
          }
        }else {
          $msg = '        <div class="warning"><p><img src="' . $config['server'] . '/images/warning.png" alt="">&nbsp;' . _("No mRNA found!, check your data") . "</p></div>\n";
        }
      }else {
        $msg = '        <div class="warning"><p><img src="' . $config['server'] . '/images/warning.png" alt="">&nbsp;' . _("Entry invalid, check your data") . "</p></div>\n";
      }
    }else {
      $result = sql_query('INSERT INTO sequence (locus,alias,isolate,map,accession,sequence_type,stop,start,sources,comments,structure,reviewer,evalue,primer) VALUES (' . $entry['locus']['id'] . ',' . (isset($entry['sequence']['alias'])?'\'' . addslashes($entry['sequence']['alias']) . '\'':'NULL') . ',' . (isset($entry['sequence']['isolate'])?'\'' . addslashes($entry['sequence']['isolate']) . '\'':'NULL') . ',' . (isset($entry['sequence']['map'])?'\'' . addslashes($entry['sequence']['map']) . '\'':'NULL') . ',\'' . $entry['sequence']['accession'] . '\',' . $entry['sequence']['type'] . ',' . $entry['sequence']['end'] . ',' . $entry['sequence']['start'] . ',' . (isset($entry['sequence']['references'])?'\'' . addslashes($entry['sequence']['references']) . '\'':'NULL') . ',' . (isset($entry['sequence']['comments'])?'\'' . addslashes($entry['sequence']['comments']) . '\'':'NULL') . ',' . (isset($entry['sequence']['structure'])?'\'' . addslashes($entry['sequence']['structure']) . '\'':'NULL') . ',\'' . addslashes($_SERVER['SSL_CLIENT_S_DN_CN']) . '\',' . (isset($entry['sequence']['evalue'])?$entry['sequence']['evalue']:'NULL') . ',' . (isset($entry['sequence']['primer'])?$entry['sequence']['primer']:'NULL') . ');', $sql);
      if (strlen($r = sql_last_error($sql))) {
        $msg = '        <div class="warning"><p><img src="' . $config['server'] . '/images/warning.png" alt="">&nbsp;' . _("SQL error, check your data") . "</p></div>\n";
      }
    }
  }else {
    $msg = '        <div class="warning"><p><img src="' . $config['server'] . '/images/warning.png" alt="">&nbsp;' . _("Accession number unknown") . "</p></div>\n";
  }
  return $msg;
}
?>