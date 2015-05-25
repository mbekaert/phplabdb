<?php
ob_start("ob_gzhandler");
session_start();
require_once('../includes/main.inc');

if ($config['login']) {
  head('uniprime'); ?>
        <div class="items">
          <h1><?php print _("mRNA"); ?><small><?php print _("All about..."); ?></small></h1><br />
<?php
  if (isset($_GET['mrna']) && preg_match('/^R(\d+)\.(\d+)/', $_GET['mrna'], $matches)) {
    $sql = sql_connect($config['db']);
    $result = sql_query('SELECT b.prefix, b.id, b.name, f.class, b.functions, c.organism, e.commonname, a.sequence_prefix, a.sequence_id, a.mrna_type, a.location, a.author, a.updated, a.comments, c.name, d.code FROM uniprime_mrna AS a, uniprime_locus AS b, uniprime_sequence AS c, users AS d, tree_taxonomy AS e, uniprime_class as f WHERE a.prefix=' . octdec($matches[1]) . ' AND a.id=' . octdec($matches[2]) . ' AND a.locus_prefix=b.prefix AND a.locus_id=b.id AND a.sequence_prefix=c.prefix AND a.sequence_id=c.id AND a.author=d.username AND c.organism=e.scientificname AND b.class=f.id;', $sql);
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
            <h2><?php print $row[14] . (($_SESSION['login']['right'] >= 3)?'<small><a href="' . $config['server'] . $plugin['uniprime']['url'] . '/sequence/edit/S' . decoct($row[7]) . '.' . decoct($row[8]) . '" title="' . _("Edit sequence") . '">' . _("edit") . '</a></small>':''); ?></h2>
<?php
      print '            <h3>' . _("Sequence") . "</h3>\n";
      print '            <div class="details"><div class="title">' . _("Name:") . '</div><div class="label"><a href="' . $config['server'] . $plugin['uniprime']['url'] . '/sequence/S' . decoct($row[7]) . '.' . decoct($row[8]) . '">' . $row[14] . "</a></div></div>\n";
      print '            <div class="details"><div class="title">' . _("Organism:") . '</div><div class="label"><em>' . $row[5] . '</em>' . (isset($row[6])?" ($row[6])":'') . "</div></div>\n"; ?>
          </div>
          <div>
            <h2><?php print _("mRNA") . (($_SESSION['login']['right'] >= 3)?'<small><a href="' . $config['server'] . $plugin['uniprime']['url'] . '/mrna/edit/' . $matches[0] . '" title="' . _("Edit mRNA") . '">' . _("edit") . '</a></small>':''); ?></h2>
<?php
      print '            <h3>' . _("Details") . "</h3>\n";
      if (!empty($row[10])) {
        print '            <div class="semocode"><img src="' . $config['server'] . $plugin['uniprime']['url'] . '/mrna/draw/' . $matches[0] . "\" alt=\"\" /></div>\n";
      }
      print '            <div class="details"><div class="title">' . _("ID:") . '</div><div class="label">' . $matches[0] . "</div><br /></div>\n";
      print '            <div class="details"><div class="title">' . _("mRNA:") . '</div><div class="label">' . _("Transcript") . ' ' . $row[9] . "</div><br /></div>\n";
      print '            <div class="details"><div class="title">' . _("Release:") . '</div><div class="label">' . date(_("m/d/Y"), strtotime($row[12])) . ' (' . $row[15] . $row[11] . ")</div><br /></div>\n";
      print '            <div class="details"><div class="title">' . _("Location:") . '</div><div class="label">' . preg_replace('/,/', ', ', $row[10]) . "</div><br /></div>\n";
      print '            <div class="details"><div class="title">' . _("Sequence:") . '</div><div class="label"><a href="#" onclick="window.open(\'' . $config['server'] . $plugin['uniprime']['url'] . '/mrna/dna/' . $matches[0] . '\', \'' . _("Sequence") . '\', \'toolbar=no, location=no, directories=no, status=no, scrollbars=yes, resizable=no, copyhistory=no, width=450, height=400, left=300, top=50\');return false;" title="' . _("Extract DNA sequence") . '">' . _("See sequence") . "</a></div><br /></div>\n";
      if (!empty($row[13])) {
        print '            <h3>' . _("Comments") . "</h3>\n" . '            <div class="details">' . $row[13] . "<br /></div>\n";
      }
?>
          </div>
<?php
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