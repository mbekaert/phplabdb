<?php
ob_start("ob_gzhandler");
session_start();
require_once('../includes/main.inc');

if ($config['login']) {
  if (isset($_GET['vpcr']) && preg_match('/^X(\d+)\.(\d+)/', $_GET['vpcr'], $matches)) {
    $sql = sql_connect($config['db']);
    $result = sql_query('SELECT b.mrna FROM uniprime_sequence AS a, uniprime_mrna as b, uniprime_primer AS c WHERE c.prefix=' . octdec($matches[1]) . ' AND c.id=' . octdec($matches[2]) . ' AND a.locus_prefix=c.locus_prefix AND a.locus_id=c.locus_id AND b.sequence_prefix=a.prefix AND b.sequence_id=a.id AND a.sequence_type=1 ORDER BY b.prefix, b.id;', $sql);
    if ((!strlen($r = sql_last_error($sql))) && (sql_num_rows($result) >= 1)) {
      header('Powered by: ' . $config['powered']);
      header('Content-Type: chemical/seq-na-fasta');
      header('Content-Disposition: attachment; filename="'.$matches[0].'.fasta"');
      $row = sql_fetch_row($result);
      print ">Reference_mRNA\n".bzdecompress(base64_decode($row[0]))."\n\n";
      $result = sql_query('SELECT a.organism, a.sequence FROM uniprime_sequence AS a, tree_taxonomy as b WHERE a.primer_prefix=' . octdec($matches[1]) . ' AND a.primer_id=' . octdec($matches[2]) . ' AND a.sequence_type>=3 AND a.organism=b.scientificname;', $sql);
      if ((!strlen($r = sql_last_error($sql))) && (sql_num_rows($result) > 0)) {
        while ($row = sql_fetch_row($result)) {
          print '>'.strtr($row[0],' ','_')."\n".bzdecompress(base64_decode($row[1]))."\n\n";
        }
      }
    }
    exit;
  }
  head('uniprime');
?>
        <div class="items">
          <h1><?php print _("Primers"); ?><small><?php print _("All about..."); ?></small></h1><br />
<?php
  if (isset($_GET['primer']) && preg_match('/^X(\d+)\.(\d+)/', $_GET['primer'], $matches)) {
    $sql = sql_connect($config['db']);
    $result = sql_query('SELECT a.locus_prefix, a.locus_id, a.alignment_prefix, a.alignment_id, a.penality, a.left_seq, a.left_data, a.left_name, a.right_seq, a.right_data, a.right_name, a.location, a.pcr, a.author, a.updated, a.comments, b.name, c.updated, c.sequences, c.structure, d.code, e.class, b.functions FROM uniprime_primer AS a, uniprime_locus AS b, uniprime_alignment AS c, users AS d, uniprime_class AS e WHERE a.prefix=' . octdec($matches[1]) . ' AND a.id=' . octdec($matches[2]) . ' AND b.prefix=a.locus_prefix AND b.id=a.locus_id  AND c.prefix=a.alignment_prefix AND c.id=a.alignment_id AND a.author=d.username AND b.class=e.id;', $sql);
    if ((!strlen($r = sql_last_error($sql))) && (sql_num_rows($result) == 1)) {
      $row = sql_fetch_row($result);
?>
          <div>
            <h2><?php print $row[16] . (($_SESSION['login']['right'] >= 3)?'<small><a href="' . $config['server'] . $plugin['uniprime']['url'] . '/locus/edit/L' . decoct($row[0]) . '.' . decoct($row[1]) . '" title="' . _("Edit locus") . '">' . _("edit") . '</a></small>':''); ?></h2>
<?php
      print '            <h3>' . _("Locus") . "</h3>\n";
      print '            <div class="details"><div class="title">' . _("Name:") . '</div><div class="label"><a href="' . $config['server'] . $plugin['uniprime']['url'] . '/locus/L' . decoct($row[0]) . '.' . decoct($row[1]) . '">' . $row[16] . "</a></div></div>\n";
      print '            <div class="details"><div class="title">' . _("Class:") . '</div><div class="label">' . $row[21] . "</div></div>\n";
      if (!empty($row[22])) {
        print '            <div class="details"><div class="title">' . _("Functions:") . '</div><div class="label">' . $row[22] . "</div></div>\n";
      }
?>
          </div>
          <div>
            <h2><?php print _("Alignment") . ' ' . date(_("m/d/Y"), strtotime($row[17])) . (($_SESSION['login']['right'] >= 3)?'<small><a href="' . $config['server'] . $plugin['uniprime']['url'] . '/alignment/edit/A' . decoct($row[2]) . '.' . decoct($row[3]) . '" title="' . _("Edit sequence") . '">' . _("edit") . '</a></small>':''); ?></h2>
<?php
      print '            <h3>' . _("Alignment") . "</h3>\n";
      print '            <div class="details"><div class="title">' . _("Release:") . '</div><div class="label"><a href="' . $config['server'] . $plugin['uniprime']['url'] . '/alignment/A' . decoct($row[2]) . '.' . decoct($row[3]) . '">' . date(_("m/d/Y"), strtotime($row[17])) . "</a></div></div>\n"; ?>
          </div>
          <div>
            <h2><?php print _("Primers") . (($_SESSION['login']['right'] >= 3)?'<small><a href="' . $config['server'] . $plugin['uniprime']['url'] . '/primer/edit/' . $matches[0] . '" title="' . _("Edit primers") . '">' . _("edit") . '</a></small>':''); ?></h2>
<?php
      print '            <h3>' . _("Details") . "</h3>\n";
      $data1 = explode('|', $row[6]);
      $data2 = explode('|', $row[9]);
      if (!empty($row[19])) {
        print '            <div class="semocode"><a title="' . _("XML export of the primer set") . '" href="' . $config['server'] . $plugin['uniprime']['url'] . '/primer/xml/' . $matches[0] . '"><img src="' . $config['server'] . $plugin['uniprime']['url'] . '/primer/draw/' . $matches[0] . "\" alt=\"\" /></a></div>\n";
      }
      print '            <div class="details"><div class="title">' . _("ID:") . '</div><div class="label">' . $matches[0] . ' ('.octdec($matches[2]).")</div><br /></div>\n";
      print '            <div class="details"><div class="title">' . _("Location:") . '</div><div class="label">' . $row[11] . "</div><br /></div>\n";
      print '            <div class="details"><div class="title">' . _("Release:") . '</div><div class="label">' . date(_("m/d/Y"), strtotime($row[14])) . ' (' . $row[20] . $row[13] . ")</div><br /></div>\n";
      print '            <div class="details"><div class="title">' . _("Primer penality:") . '</div><div class="label">' . $row[4] . "</div><br /></div>\n";
      if (!empty($row[7])) print '            <div class="details"><div class="title">' . _("Forward:") . '</div><div class="label">' . $row[7] . "</div><br /></div>\n";
      print '            <div class="details"><div class="title">' . ((!empty($row[7]))?'&nbsp;': _("Forward:")) . '</div><div class="label">' . $row[5] . "<br />\n";
      print '            <span class="title">&nbsp;</span>' . $data1[1] . ' nt&nbsp;&nbsp;&nbsp;GC: ' . $data1[3] . '%&nbsp;&nbsp;&nbsp;Penality: ' . $data1[4] . '' . "<br />\n";
      print '            <span class="title">&nbsp;</span>TM: ' . $data1[2] . "&deg;C<br />\n";
      print '            <span class="title">&nbsp;</span>' . _("Position in consensus:") . ' ' . $data1[0] . "<br />\n";
      print '            <span class="title">&nbsp;</span>' . _("Position in alignment:") . ' ' . $data1[5] . "</div><br /></div>\n";
      if (!empty($row[10])) print '            <div class="details"><div class="title">' . _("Reverse:") . '</div><div class="label">' . $row[10] . "</div><br /></div>\n";
      print '            <div class="details"><div class="title">' . ((!empty($row[10]))?'&nbsp;': _("Reverse:")) . '</div><div class="label">' . $row[8] . "<br />\n";
      print '            <span class="title">&nbsp;</span>' . $data2[1] . ' nt&nbsp;&nbsp;&nbsp;GC: ' . $data2[3] . '%&nbsp;&nbsp;&nbsp;Penality: ' . $data2[4] . '' . "<br />\n";
      print '            <span class="title">&nbsp;</span>TM: ' . $data2[2] . "&deg;C<br />\n";
      print '            <span class="title">&nbsp;</span>' . _("Position in consensus:") . ' ' . $data2[0] . "<br />\n";
      print '            <span class="title">&nbsp;</span>' . _("Position in alignment:") . ' ' . $data2[5] . "</div><br /></div>\n";
      if (!empty($row[12])) print '            <h3>' . _("PCR Conditions") . "</h3>\n" . '            <div class="details">' . $row[12] . "<br /></div>\n";
      if (!empty($row[15])) print '            <h3>' . _("Comments") . "</h3>\n" . '            <div class="details">' . $row[15] . "<br /></div>\n"; ?>
          </div>
<?php
      $result = sql_query('SELECT a.prefix, a.id, a.name, a.map, a.accession, a.organism, c.commonname, a.sequence_type, b.sequence_type, a.updated, a.organelle FROM uniprime_sequence AS a, uniprime_sequence_type AS b, tree_taxonomy as c WHERE a.primer_prefix=' . octdec($matches[1]) . ' AND a.primer_id=' . octdec($matches[2]) . ' AND a.sequence_type>=3 AND a.sequence_type=b.id AND a.organism=c.scientificname;', $sql);
      if ((!strlen($r = sql_last_error($sql))) && (sql_num_rows($result) > 0)) {
        print "            <div>\n              <h2>" . _("Sequences") . '<small><a href="' . $config['server'] . $plugin['uniprime']['url'] . '/vpcr/' . $matches[0] . '" title="' . _("Export FASTA") . '">' . _("export") . "</a></small></h2><br />\n";
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