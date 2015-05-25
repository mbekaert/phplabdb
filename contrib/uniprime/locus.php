<?php
ob_start("ob_gzhandler");
session_start();
require_once('../includes/main.inc');

if ($config['login']) {
  if (!isset($_SESSION['specie'])) {
    @set_pref('specie', 'name');
  }
  head('uniprime');
?>
        <div class="items">
          <h1><?php print _("Locus"); ?><small><?php print _("All about..."); ?></small></h1><br />
<?php
  if (isset($_GET['locus']) && preg_match('/^L(\d+)\.(\d+)/', $_GET['locus'], $matches)) {
    $sql = sql_connect($config['db']);
    $result = sql_query('SELECT a.prefix, a.id, a.name, a.alias, a.pathway, a.phenotype, a.functions, a.comments, a.sources, a.evidence, b.evidence, a.class, c.class, a.status, d.status, a.locus_type, e.locus_type, a.author, f.code, a.updated FROM uniprime_locus AS a, uniprime_evidence AS b, uniprime_class AS c, uniprime_status AS d, uniprime_locus_type AS e, users AS f WHERE a.prefix=' . octdec($matches[1]) . ' AND a.id=' . octdec($matches[2]) . ' AND a.evidence=b.id AND a.class=c.id AND a.status=d.id AND a.locus_type=e.id AND a.author=f.username;', $sql);
    if ((!strlen($r = sql_last_error($sql))) && (sql_num_rows($result) > 0)) {
      $row = sql_fetch_row($result);
?>
          <div>
            <h2><?php print $row[2] . (($_SESSION['login']['right'] >= 3)?'<small><a href="' . $config['server'] . $plugin['uniprime']['url'] . '/locus/edit/' . $matches[0] . '" title="' . _("Edit locus") . '">' . _("edit") . '</a></small>':''); ?></h2>
<?php
      print '            <h3>' . _("Details") . "</h3>\n";
      print '            <div class="details"><div class="title">' . _("ID:") . '</div><div class="label">L' . decoct($row[0]) . '.' . decoct($row[1]) . " ($row[1])</div></div>\n";
      print '            <div class="details"><div class="title">' . _("Name:") . '</div><div class="label">' . $row[2] . "</div></div>\n";
      if (!empty($row[3])) {
        print '            <span class="title">' . _("Alias:") . '</div><div class="label">' . $row[3] . "</div></div>\n";
      }
      print '            <div class="details"><div class="title">' . _("Class:") . '</div><div class="label">' . $row[12] . '&nbsp;&nbsp;<img src="' . $config['server'] . $plugin['uniprime']['url'] . '/images/cl' . $row[11] . '.png" height="16" width="16" alt="' . $row[12] . "\" /></div></div>\n";
      print '            <div class="details"><div class="title">' . _("Locus type:") . '</div><div class="label">' . $row[16] . "</div></div>\n";
      print '            <div class="details"><div class="title">' . _("Evidence:") . '</div><div class="label">' . $row[10] . "</div></div>\n";
      print '            <div class="details"><div class="title">' . _("Status:") . '</div><div class="label">' . $row[14] . '&nbsp;&nbsp;<img src="' . $config['server'] . $plugin['uniprime']['url'] . '/images/st' . $row[13] . '.png" height="16" width="16" alt="' . $row[14] . "\" /></div></div>\n";
      print '            <div class="details"><div class="title">' . _("Release:") . '</div><div class="label">' . date(_("m/d/Y"), strtotime($row[19])) . ' (' . $row[18] . $row[17] . ")</div></div>\n";
      print '            <h3>' . ("Function") . "</h3>\n";
      if (!empty($row[6])) {
        print '            <div class="details"><div class="title">' . _("Functions:") . '</div><div class="label">' . $row[6] . "</div></div>\n";
      }
      if (!empty($row[4])) {
        print '            <div class="details"><div class="title">' . _("pathway:") . '</div><div class="label">' . str_replace('|', '<br /><span class="title">&nbsp;</span>', $row[4]) . "</div></div>\n";
      }
      if (!empty($row[5])) {
        print '            <div class="details"><div class="title">' . _("Phenotypes:") . '</div><div class="label">' . str_replace('|', '<br /><span class="title">&nbsp;</span>', $row[5]) . "</div></div>\n";
      }
      if (!empty($row[7])) {
        print '            <h3>' . _("Comments") . "</h3>\n" . '            <div class="details">' . $row[7] . "</div>\n";
      }
      if (!empty($row[8])) put_ref($row[8], $sql);
?>
          </div>
<?php
      // sequences
      $result = sql_query('SELECT a.prefix, a.id, a.name, a.map, a.accession, a.organism, c.commonname, a.sequence_type, b.sequence_type, a.updated, a.organelle FROM uniprime_sequence AS a, uniprime_sequence_type AS b, tree_taxonomy as c WHERE a.locus_prefix=' . octdec($matches[1]) . ' AND a.locus_id=' . octdec($matches[2]) . ' AND a.sequence_type<3 AND a.sequence_type=b.id AND a.organism=c.scientificname;', $sql);
      if ((!strlen($r = sql_last_error($sql))) && (sql_num_rows($result) > 0)) {
        print "            <div>\n              <h2>" . _("Sequences") . (($_SESSION['login']['right'] >= 3)?'<small><a href="' . $config['server'] . $plugin['uniprime']['url'] . '/sequence/add/' . $matches[0] . '" title="' . _("Add a new sequence") . '">' . _("add") . '</a></small>':'') . "</h2><br />\n";
        while ($row = sql_fetch_row($result)) {
          if (($_SESSION['specie'] == 'alias') && isset($row[6])) {
            $specie = ucfirst($row[6]);
          }else {
            $specie = ucfirst($row[5]);
          }
          $desc = '<em>' . $specie . '</em>' . (isset($row[3])?' - ' . $row[3]:'') . (!empty($row[4])?' (' . $row[4] . ')':'') . (isset($row[10])?' [' . $row[10] . ']':'');
          print '              <div class="result"><span class="ref"><a href="' . $config['server'] . $plugin['uniprime']['url'] . '/sequence/S' . decoct($row[0]) . '.' . decoct($row[1]) . '" title="S' . decoct($row[0]) . '.' . decoct($row[1]) . '" class="sq' . $row[7] . '">' . $row[2] . '</a></span><span class="desc">' . $row[8] . '</span><span class="detail">' . ((strlen($desc) > 43)?(substr($desc, 0, 40) . '...'):$desc) . '</span><span class="updated">' . date(_("m/d/Y"), strtotime($row[9])) . "</span></div>\n";
        }
        print "            </div>\n";
      }elseif ($_SESSION['login']['right'] >= 3) {
        print "            <div>\n              <h2>" . _("Sequences") . '<small><a href="' . $config['server'] . $plugin['uniprime']['url'] . '/sequence/add/' . $matches[0] . '" title="' . _("Add a new sequence") . '">' . _("add") . "</a></small></h2><br />\n            </div>\n";
      }
      // alignments
      $result = sql_query('SELECT prefix, id, sequences, program, updated FROM uniprime_alignment WHERE locus_prefix=' . octdec($matches[1]) . ' AND locus_id=' . octdec($matches[2]) . ' ORDER BY updated;', $sql);
      if ((!strlen($r = sql_last_error($sql))) && (sql_num_rows($result) > 0)) {
        print "            <div>\n              <h2>" . _("Alignments") . (($_SESSION['login']['right'] >= 3)?'<small><a href="' . $config['server'] . $plugin['uniprime']['url'] . '/alignment/add/' . $matches[0] . '" title="' . _("Add a new aligment") . '">' . _("add") . '</a></small>':'') . "</h2><br />\n";
        $i = 1;
        while ($row = sql_fetch_row($result)) {
          $specie = array();
          foreach(explode(' ', $row[2]) as $seqs) {
            if (preg_match('/^(\d+)\.(\d+)/', $seqs, $seq)) {
              $result_seq = sql_query('SELECT a.organism, b.commonname FROM uniprime_sequence AS a, tree_taxonomy AS b WHERE a.prefix=' . octdec($seq[1]) . ' AND a.id=' . octdec($seq[2]) . ' AND a.organism=b.scientificname;', $sql);
              if ((!strlen($r = sql_last_error($sql))) && (sql_num_rows($result_seq) == 1)) {
                $row_seq = sql_fetch_row($result_seq);
                if ((get_pref('specie') == 'alias') && isset($row_seq[1])) {
                  $specie[] = ucfirst($row_seq[1]);
                }else {
                  $specie[] = ucfirst($row_seq[0]);
                }
              }
            }
          }
          if (isset($specie)) $desc = implode(', ', $specie);
          print '              <div class="result"><span class="ref"><a href="' . $config['server'] . $plugin['uniprime']['url'] . '/alignment/A' . decoct($row[0]) . '.' . decoct($row[1]) . '" title="A' . decoct($row[0]) . '.' . decoct($row[1]) . '" class="align">' . _("Alignment") . ((sql_num_rows($result) == 1)?'':' ' . $i++) . '</a></span><span class="desc">' . $row[3] . '</span><span class="detail">' . ((strlen($desc) > 43)?(substr($desc, 0, 40) . '...'):$desc) . '</span><span class="updated">' . date(_("m/d/Y"), strtotime($row[4])) . "</span></div>\n";
        }
        print "            </div>\n";
      }elseif ($_SESSION['login']['right'] >= 3) {
        print "            <div>\n              <h2>" . _("Alignments") . '<small><a href="' . $config['server'] . $plugin['uniprime']['url'] . '/alignment/add/' . $matches[0] . '" title="' . _("Add a new alignment") . '">' . _("add") . "</a></small></h2><br />\n            </div>\n";
      }
      // primers
      $result = sql_query('SELECT prefix, id, left_name, right_name, location, updated FROM uniprime_primer WHERE locus_prefix=' . octdec($matches[1]) . ' AND locus_id=' . octdec($matches[2]) . ' ORDER BY penality;', $sql);
      if ((!strlen($r = sql_last_error($sql))) && (sql_num_rows($result) > 0)) {
        print "            <div>\n              <h2>" . _("Primers") . "</h2>\n";
        $i = 1;
        while ($row = sql_fetch_row($result)) {
          print '              <div class="result"><span class="ref"><a href="' . $config['server'] . $plugin['uniprime']['url'] . '/primer/X' . decoct($row[0]) . '.' . decoct($row[1]) . '" title="X' . decoct($row[0]) . '.' . decoct($row[1]) . '" class="primer">' . _("Primer") . ((sql_num_rows($result) == 1)?'':' ' . $i++) . '</a></span><span class="desc">' . substr($row[4], 0, strpos($row[4], '(')) . '</span><span class="detail">' . ((isset($row[2]) && isset($row[3]))?($row[2] . ' / ' . $row[3]):'') . '</span><span class="updated">' . date(_("m/d/Y"), strtotime($row[5])) . "</span></div>\n";
        }
        print "            </div>\n";
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