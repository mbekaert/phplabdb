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
          <h1><?php print _("Sequence"); ?><small><?php print _("All about..."); ?></small></h1><br />
<?php
  if (isset($_GET['sequence']) && preg_match('/^S(\d+)\.(\d+)/', $_GET['sequence'], $matches)) {
    $sql = sql_connect($config['db']);
    $result = sql_query('SELECT a.prefix, a.id, a.locus_prefix, a.locus_id, a.name, a.alias, a.location, a.translation, b.name, c.molecule, a.circular, a.chromosome, a.isolate, a.organelle, a.map, a.accession, a.hgnc, a.geneid, a.organism, d.commonname, a.go, a.sequence_type, e.sequence_type, a.primer_prefix, a.primer_id, a.updated, a.sources, a.comments, a.evalue, a.start, a.stop, a.structure, f.code, a.author FROM uniprime_sequence AS a, tree_translation AS b, uniprime_molecule AS c, tree_taxonomy AS d, uniprime_sequence_type AS e, users AS f WHERE a.prefix=' . octdec($matches[1]) . ' AND a.id=' . octdec($matches[2]) . ' AND a.translation=b.reference AND a.molecule=c.id AND a.organism=d.scientificname AND a.sequence_type=e.id AND a.author=f.username;', $sql);
    if ((!strlen($r = sql_last_error($sql))) && (sql_num_rows($result) > 0)) {
      $row_seq = sql_fetch_row($result);
      $result = sql_query('SELECT a.prefix, a.id, a.name, b.class, a.functions FROM uniprime_locus AS a, uniprime_class AS b WHERE a.prefix=' . $row_seq[2] . ' AND a.id=' . $row_seq[3] . ' AND a.class=b.id;', $sql);
      if ((!strlen($r = sql_last_error($sql))) && (sql_num_rows($result) > 0)) {
        $row = sql_fetch_row($result);
?>
          <div>
            <h2><?php print $row[2] . (($_SESSION['login']['right'] >= 3)?'<small><a href="' . $config['server'] . $plugin['uniprime']['url'] . '/locus/edit/L' . decoct($row[0]) . '.' . decoct($row[1]) . '" title="' . _("Edit locus") . '">' . _("edit") . '</a></small>':''); ?></h2>
<?php
        print '            <h3>' . _("Locus") . "</h3>\n";
        print '            <div class="details"><div class="title">' . _("Name:") . '</div><div class="label"><a href="' . $config['server'] . $plugin['uniprime']['url'] . '/locus/L' . decoct($row[0]) . '.' . decoct($row[1]) . '">' . $row[2] . "</a></div></div>\n";
        print '            <div class="details"><div class="title">' . _("Class:") . '</div><div class="label">' . $row[3] . "</div></div>\n";
        if (!empty($row[4])) {
          print '            <div class="details"><div class="title">' . _("Functions:") . '</div><div class="label">' . $row[4] . "</div></div>\n";
        }
?>
          </div>
          <div>
            <h2><?php print $row_seq[4] . (($_SESSION['login']['right'] >= 3)?'<small><a href="' . $config['server'] . $plugin['uniprime']['url'] . '/sequence/' . $matches[0] . '/edit" title="' . _("Edit sequence") . '">' . _("edit") . '</a></small>':''); ?></h2>
<?php
        print '            <h3>' . _("Details") . "</h3>\n";
        print '            <div class="details"><div class="title">' . _("ID:") . '</div><div class="label">S' . decoct($row_seq[0]) . '.' . decoct($row_seq[1]) . "</div></div>\n";
        print '            <div class="details"><div class="title">' . _("Name:") . '</div><div class="label">' . $row_seq[4] . "</div></div>\n";
        print '            <div class="details"><div class="title">' . _("Release:") . '</div><div class="label">' . date(_("m/d/Y"), strtotime($row_seq[25])) . ' (' . $row_seq[32] . $row_seq[33] . ")</div></div>\n";
        if (!empty($row_seq[31])) {
          print '            <div class="semocode"><img src="' . $config['server'] . $plugin['uniprime']['url'] . '/sequence/draw/' . $matches[0] . "\" alt=\"\" /></div>\n";
        }
        if (!empty($row_seq[5])) {
          print '            <div class="details"><div class="title">' . _("Alias:") . '</div><div class="label">' . $row_seq[5] . "</div></div>\n";
        }
        print '            <div class="details"><div class="title">' . _("Sequence type:") . '</div><div class="label">' . $row_seq[22] . '&nbsp;&nbsp;<img src="' . $config['server'] . $plugin['uniprime']['url'] . '/images/sq' . $row_seq[21] . '.png" height="16" width="16" alt="' . $row_seq[22] . "\" /></div></div>\n";
        if (!empty($row_seq[28])) {
          print '            <div class="details"><div class="title">' . _("E-value:") . '</div><div class="label">' . $row_seq[28] . "</div></div>\n";
        }
        if (!empty($row_seq[15])) {
          print '            <div class="details"><div class="title">' . _("Accession:") . '</div><div class="label"><a href="http://www.ncbi.nlm.nih.gov/entrez/viewer.fcgi?val=' . $row_seq[15] . '&amp;from=' . $row_seq[29] . '&amp;to=' . $row_seq[30] . '&amp;dopt=gb">' . $row_seq[15] . "</a></div></div>\n";
        }
        if (!empty($row_seq[6])) {
          print '            <div class="details"><div class="title">' . _("Gene location:") . '</div><div class="label">' . $row_seq[6] . "</div></div>\n";
        }
        if (!empty($row_seq[14])) {
          print '            <div class="details"><div class="title">' . _("Map:") . '</div><div class="label">' . $row_seq[14] . "</div></div>\n";
        }
        if (!empty($row_seq[16])) {
          print '            <div class="details"><div class="title"><acronym title="HUGO Gene Nomenclature Committee">HGNC</acronym>:</div><div class="label"><a href="http://www.gene.ucl.ac.uk/nomenclature/data/get_data.php?hgnc_id=' . $row_seq[16] . '">' . $row_seq[16] . "</a></div></div>\n";
        }
        if (!empty($row_seq[17])) {
          print '            <div class="details"><div class="title"><acronym title="Genbank GeneID">GeneID</acronym>:</div><div class="label"><a href="http://www.ncbi.nlm.nih.gov/entrez/query.fcgi?db=gene&amp;cmd=Retrieve&amp;dopt=full_report&amp;list_uids=' . $row_seq[17] . '">' . $row_seq[17] . "</a></div></div>\n";
        }
        print '            <div class="details"><div class="title">' . _("Organisme:") . '</div><div class="label"><em>' . $row_seq[18] . '</em>' . (isset($row_seq[19])?" ($row_seq[19])":'') . "</div></div>\n";
        if (!empty($row_seq[12])) {
          print '            <div class="details"><div class="title">' . _("Isolate/population:") . '</div><div class="label">' . $row_seq[12] . "</div></div>\n";
        }
        if (!empty($row_seq[11])) {
          print '            <div class="details"><div class="title">' . _("Chromosome:") . '</div><div class="label">' . $row_seq[11] . "</div></div>\n";
        }
        if (!empty($row_seq[13])) {
          print '            <div class="details"><div class="title">' . _("Organelle:") . '</span> <span class="desc">' . $row_seq[13] . "</div></div>\n";
        }
        print '            <div class="details"><div class="title">' . _("Molecule:") . '</div><div class="label">' . $row_seq[9] . "</div></div>\n";
        print '            <div class="details"><div class="title">' . _("Topology:") . '</div><div class="label">' . (($row_seq[10] == 't')?'circular':'linear') . "</div></div>\n";
        if ($row_seq[7] > 0) {
          print '            <div class="details"><div class="title">' . _("Genetic code:") . '</div><div class="label">' . $row_seq[8] . "</div></div>\n";
        }
        if (!empty($row_seq[20])) {
          print '            <div class="details"><div class="title"><acronym title="Gene Ontology terms">GO</acronym>:</div><div class="label">' . $row_seq[20] . "</div></div>\n";
        }
        if (!empty($row_seq[23])) {
          print '            <div class="details"><div class="title">' . _("Primer:") . '</div><div class="label"><a href="' . $config['server'] . $plugin['uniprime']['url'] . '/primer/X' . decoct($row_seq[23]) . '.' . decoct($row_seq[24]) . '">X' . decoct($row_seq[23]) . '.' . decoct($row_seq[24]) . "</a></div></div>\n";
        }
        print '            <div class="details"><div class="title">' . _("Sequence:") . '</div><div class="label"><a href="#" onclick="window.open(\'' . $config['server'] . $plugin['uniprime']['url'] . '/sequence/dna/' . $matches[0] . '\', \'' . _("Sequence") . '\', \'toolbar=no, location=no, directories=no, status=no, scrollbars=yes, resizable=no, copyhistory=no, width=450, height=400, left=300, top=50\');return false;" title="' . _("Extract DNA sequence") . '">' . _("See sequence") . "</a></div></div>\n";
        if (!empty($row_seq[27])) {
          print '            <h3>' . _("Comments") . "</h3>\n" . '            <div class="details">' . $row_seq[27] . "</div>\n";
        }
        if (!empty($row_seq[26])) put_ref($row_seq[26], $sql); ?>
          </div>
<?php
        if ($row_seq[21]<3) {
          $result = sql_query('SELECT prefix, id, mrna_type, location FROM uniprime_mrna WHERE sequence_prefix=' . octdec($matches[1]) . ' AND sequence_id=' . octdec($matches[2]) . ' ORDER BY mrna_type;', $sql);
          if ((!strlen($r = sql_last_error($sql))) && (sql_num_rows($result) > 0)) {
            print "            <div>\n              <h2>" . _("mRNA") . (($_SESSION['login']['right'] >= 3)?'<small><a href="' . $config['server'] . $plugin['uniprime']['url'] . '/mrna/add/' . $matches[0] . '" title="' . _("Add a new mRNA") . '">' . _("add") . '</a></small>':'') . "</h2><br />\n";
            while ($row = sql_fetch_row($result)) {
              if (($_SESSION['specie'] == 'alias') && isset($row[6])) {
                $specie = ucfirst($row[6]);
              }else {
                $specie = ucfirst($row[5]);
              }
              $desc = '<em>' . $specie . '</em>' . (isset($row[3])?' - ' . $row[3]:'') . (!empty($row[4])?' (' . $row[4] . ')':'') . (isset($row[10])?' [' . $row[10] . ']':'');
              print '              <div class="result"><span class="ref"><a href="' . $config['server'] . $plugin['uniprime']['url'] . '/mrna/R' . decoct($row[0]) . '.' . decoct($row[1]) . '" title="R' . decoct($row[0]) . '.' . decoct($row[1]) . '" class="mrna">' . ((sql_num_rows($result) == 1)?'mRNA': _("Transcripts") . ' ' . $row[2]) . '</a></span><span class="desclong">' . ((strlen($row[3]) > 63)?(substr($row[3], 0, 60) . '...'):$row[3]) . "</span></div>\n";
            }
            print "            </div>\n";
          }elseif ($_SESSION['login']['right'] >= 3) {
            print "            <div>\n              <h2>" . _("mRNA") . '<small><a href="' . $config['server'] . $plugin['uniprime']['url'] . '/nrma/add/' . $matches[0] . '/" title="' . _("Add a new mRNA") . '">' . _("add") . "</a></small></h2><br />\n            </div>\n";
          }
        }
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
