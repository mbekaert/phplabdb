<?php
ob_start("ob_gzhandler");
session_start();
require_once('../includes/main.inc');

function add_oligo($alignment, $id) {
  global $sql;

  foreach (explode('>', $alignment) as $seq) {
    if (!empty($seq)) {
      $i = 0;
      $s = '';
      foreach (explode("\n", $seq) as $line) {
        if ($i++ == 0) {
          $name = trim($line);
        }else {
          $s .= trim($line);
        }
      }
      $sequence[] = array('name' => $name, 'sequence' => $s);
    }
    unset($seq);
  }
  $alignment = '';
  $length = strlen($sequence[0]['sequence']);
  $result = sql_query('SELECT prefix, id, LENGTH(left_seq), left_data, LENGTH(right_seq), right_data FROM uniprime_primer WHERE alignment_prefix=' . octdec($id[1]) . ' AND alignment_id=' . octdec($id[2]) . ' ORDER BY penality ASC;', $sql);
  if ((!strlen($r = sql_last_error($sql))) && (sql_num_rows($result) > 0)) {
    while ($row = sql_fetch_row($result)) {
      list(, , , , , $left) = explode('|', $row[3]);
      list(, , , , , $right) = explode('|', $row[5]);
      $primer[$left - 1] = array($row[2], decoct($row[0]) . '.' . decoct($row[1]));
      $primer[$right - $row[4] ] = array($row[4], decoct($row[0]) . '.' . decoct($row[1]));
    }
    ksort($primer);
  }
  $i = 0;
  while ($i < $length) {
    if (isset($primer)) {
      if (isset($pos)) unset($pos);
      if (isset($pos_next)) {
        $pos = $pos_next;
        unset($pos_next);
      }
      foreach($primer as $key => $oligo) {
        if (($i <= $key) && ($i + 60 >= $key)) {
          for ($j = 0; $j < $oligo[0]; $j++) $pos[($key - $i + $j)] = (isset($pos[($key - $i + $j)])?$pos[($key - $i + $j)] . ' ':'') . $oligo[1];
        }
      }
      if (isset($pos)) {
        $last = '';
        $pos[] = '';
        foreach($pos as $key => $var) {
          if ($key >= 60) {
            $pos_next[$key-60] = $var;
          }
          if ($var != $last) {
            if ($key >= 60) {
              unset($pos[$key]);
              $pos[60] = '</span>';
            }else {
              $pos[$key] = (!empty($last)?'</span>':'') . (!empty($var)?'<span class="align' . ceil(strlen($var) / 6) . '" title="' . $var . '">':'');
            }
          }else {
            unset($pos[$key]);
          }
          $last = $var;
        }
      }
    }
    foreach($sequence as $specie) {
      $seq = substr($specie['sequence'], $i, 60);
      if (isset($pos)) {
        krsort($pos);
        foreach($pos as $key => $str) {
          $seq = substr($seq, 0, $key) . $str . substr($seq, $key);
        }
      }
      $alignment .= sprintf("%s\t%s\n", $specie['name'], $seq);
    }
    $alignment .= "\n";
    $i += 60;
  }
  return $alignment;
}

if ($config['login']) {
  if (!isset($_SESSION['specie'])) {
    @set_pref('specie', 'name');
  }
  head('uniprime');
?>
        <div class="items">
          <h1><?php print _("Alignment"); ?><small><?php print _("All about..."); ?></small></h1><br />
<?php
  if (isset($_GET['alignment']) && preg_match('/^A(\d+)\.(\d+)/', $_GET['alignment'], $matches)) {
    $sql = sql_connect($config['db']);
    $result = sql_query('SELECT b.prefix, b.id, b.name, d.class, b.functions, a.sequences, a.alignment, a.consensus, a.program, a.author, c.code, a.updated, a.comments FROM uniprime_alignment AS a, uniprime_locus AS b, users AS c, uniprime_class AS d WHERE a.prefix=' . octdec($matches[1]) . ' AND a.id=' . octdec($matches[2]) . ' AND a.locus_prefix=b.prefix AND a.locus_id=b.id AND a.author=c.username AND b.class=d.id;', $sql);
    if ((!strlen($r = sql_last_error($sql))) && (sql_num_rows($result) == 1)) {
      $row = sql_fetch_row($result);
?>
          <div>
            <h2><?php print $row[2] . (($_SESSION['login']['right'] >= 3)?'<small><a href="' . $config['server'] . $plugin['uniprime']['url'] . '/locus/edit/L' . decoct($row[0]) . '.' . decoct($row[1]) . '" title="' . _("Edit locus") . '">' . _("edit") . '</a></small>':''); ?></h2>
<?php
      print '            <h3>' . _("Locus") . "</h3>\n";
      print '            <div class="details"><div class="title">' . _("Name:") . '</div><div class="label"><a href="' . $config['server'] . $plugin['uniprime']['url'] . '/locus/L' . decoct($row[0]) . '.' . decoct($row[1]) . '">' . $row[2] . "</a></div><br /></div>\n";
      print '            <div class="details"><div class="title">' . _("Class:") . '</div><div class="label">' . $row[3] . "</div><br /></div>\n";
      if (!empty($row[4])) {
        print '            <div class="details"><div class="title">' . _("Functions:") . '</div><div class="label">' . $row[4] . "</div><br /></div>\n";
      }
?>
          </div>
          <div>
            <h2><?php print _("Alignment") . ' ' . date(_("m/d/Y"), strtotime($row[11])) . (($_SESSION['login']['right'] >= 3)?'<small><a href="' . $config['server'] . $plugin['uniprime']['url'] . '/alignment/edit/' . $matches[0] . '" title="' . _("Edit alignment") . '">' . _("edit") . '</a></small>':''); ?></h2>
<?php
      print '            <h3>' . _("Details") . "</h3>\n";
      print '            <div class="details"><div class="title">' . _("ID:") . '</div><div class="label">A' . $matches[0] . ' ('.octdec($matches[2]).")</div><br /></div>\n";
      print '            <div class="details"><div class="title">' . _("Program:") . '</div><div class="label">' . $row[8] . "</div><br /></div>\n";
      print '            <div class="details"><div class="title">' . _("Release:") . '</div><div class="label">' . date(_("m/d/Y"), strtotime($row[11])) . ' (' . $row[10] . $row[9] . ")</div><br /></div>\n";
      if (!empty($row[12])) {
        print '            <h3>' . _("Comments") . "</h3>\n" . '            <div class="details">' . $row[12] . "<br /></div>\n";
      }
?>
          </div>
          <div>
            <h2><?php print _("Sequences"); ?></h2>
<?php
      // sequences
      foreach(explode(' ', $row[5]) as $seqs) {
        if (preg_match('/^(\d+)\.(\d+)/', $seqs, $seq)) {
          $result_seq = sql_query('SELECT a.name, a.map, a.accession, a.organism, c.commonname, a.sequence_type, b.sequence_type, a.updated, a.organelle FROM uniprime_sequence AS a, uniprime_sequence_type AS b, tree_taxonomy AS c WHERE a.prefix=' . octdec($seq[1]) . ' AND a.id=' . octdec($seq[2]) . ' AND a.sequence_type=b.id AND a.organism=c.scientificname;', $sql);
          if ((!strlen($r = sql_last_error($sql))) && (sql_num_rows($result_seq) > 0)) {
            while ($row_seq = sql_fetch_row($result_seq)) {
              if ((get_pref('specie') == 'alias') && isset($row_seq[4])) {
                $specie = ucfirst($row_seq[4]);
              }else {
                $specie = ucfirst($row_seq[3]);
              }
              $desc = '<em>' . $specie . '</em>' . (isset($row_seq[1])?' - ' . $row_seq[1]:'') . (!empty($row_seq[2])?' (' . $row_seq[2] . ')':'') . (isset($row_seq[8])?' [' . $row_seq[8] . ']':'');
              print '              <div class="result"><span class="ref"><a href="' . $config['server'] . $plugin['uniprime']['url'] . '/sequence/S' . $seqs . '" title="S' . $seqs . '" class="sq' . $row_seq[5] . '">' . $row_seq[0] . '</a></span><span class="desc">' . $row_seq[6] . '</span><span class="detail">' . ((strlen($desc) > 43)?(substr($desc, 0, 40) . '...'):$desc) . '</span><span class="updated">' . date(_("m/d/Y"), strtotime($row_seq[7])) . "</span></div>\n";
            }
          }
        }
      }
?>
          </div>
          <div>
            <h2><?php print _("Alignment"); ?></h2>
            <p><pre>
<?php print add_oligo(bzdecompress(base64_decode($row[6])), $matches); ?>
            <pre></p>
          </div>
<?php
      // primers
      $result = sql_query('SELECT prefix, id, left_name, right_name, location, updated FROM uniprime_primer WHERE alignment_prefix=' . octdec($matches[1]) . ' AND alignment_id=' . octdec($matches[2]) . ' ORDER BY penality;', $sql);
      if ((!strlen($r = sql_last_error($sql))) && (sql_num_rows($result) > 0)) {
        print "            <div>\n              <h2>" . _("Primers") . (($_SESSION['login']['right'] >= 3)?'<small><a href="' . $config['server'] . $plugin['uniprime']['url'] . '/primer/add/' . $matches[0] . '" title="' . _("Add a new primer") . '">' . _("add") . '</a></small>':'') . "</h2><br />\n";
        $i = 1;
        while ($row = sql_fetch_row($result)) {
          print '              <div class="result"><span class="ref"><a href="' . $config['server'] . $plugin['uniprime']['url'] . '/primer/X' . decoct($row[0]) . '.' . decoct($row[1]) . '" title="X' . decoct($row[0]) . '.' . decoct($row[1]) . '" class="primer">' . _("Primer") . ((sql_num_rows($result) == 1)?'':' ' . $i++) . '</a></span><span class="desc">' . substr($row[4], 0, strpos($row[4], '(')) . '</span><span class="detail">' . ((isset($row[2]) && isset($row[3]))?($row[2] . ' / ' . $row[3]):'') . '</span><span class="updated">' . date(_("m/d/Y"), strtotime($row[5])) . "</span></div>\n";
        }
        print "            </div>\n";
      }elseif ($_SESSION['login']['right'] >= 3) {
        print "            <div>\n              <h2>" . _("Primers") . '<small><a href="' . $config['server'] . $plugin['uniprime']['url'] . '/primer/add/' . $matches[0] . '" title="' . _("Add a new primer") . '">' . _("add") . "</a></small></h2><br />\n            </div>\n";
      }
    }
  }
?>
          <br />
        </div>
<?php
  foot();
}else {
  header('Location: ' . $config['server']);
}
?>