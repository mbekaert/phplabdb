<?php
ob_start("ob_gzhandler");
session_start();
require_once('../includes/main.inc');
require_once('../includes/dna.inc');

function get_oligo($oligo, $sql) {
  global $config, $plugin;
  $result = sql_query('SELECT a.prefix, a.id, a.release, a.name, a.sequence, a.modification, a.box, a.freezer, a.rank, a.comments, a.design, a.program, a.version, a.design_comments, a.reference, a.updated, a.author, b.code FROM oligoml_oligo AS a, users AS b WHERE (a.prefix=' . octdec(intval($oligo[1])) . ' AND a.id=' . octdec(intval($oligo[2])) . ' AND a.author=b.username);', $sql);
  if ((!strlen($r = sql_last_error($sql))) && (sql_num_rows($result) == 1)) {
    $row = sql_fetch_row($result);
?>
          <div>
            <h2><?php print $row[3] . (($_SESSION['login']['right'] >= 3)?'<small><a href="' . $config['server'] . $plugin['oligoml']['url'] . '/edit/O' . decoct($row[0]) . '.' . decoct($row[1]) . '" title="' . _("edit") . '">' . _("edit") . '</a></small>':''); ?></h2>
<?php
    print '            <div class="semocode"><img src="' . $config['server'] . $plugin['oligoml']['url'] . '/semacode/O' . decoct($row[0]) . '.' . decoct($row[1]) . '" width="100" height="100" alt="'.EAN8('O',$row[0],$row[1])."\" /></div>\n";
    print '            <h3>' . ("Details") . "</h3>\n";
    print '            <div class="details"><div class="title">' . _("ID:") . '</div><div class="label"><a href="' . $config['server'] . $plugin['oligoml']['url'] . '/xml/O' . decoct($row[0]) . '.' . decoct($row[1]) . '" title="' . _("Export xml format") . '">O' . decoct($row[0]) . '.' . decoct($row[1]) . "</a></div></div>\n";
    print '            <div class="details"><div class="title">' . _("Name:") . '</div><div class="label">' . $row[3] . "</div></div>\n";
    print '            <div class="details"><div class="title">' . ("Revision:") . '</div><div class="label">' . $row[2] . ' (<em>' . gmdate(_("d-m-Y"), strtotime($row[15])) . "</em>)</div></div>\n";
    print '            <div class="details"><div class="title">' . ("Author:") . '</div><div class="label">' . $row[17] . $row[16] . "</div></div>\n";
    print '            <h3>' . ("Sequence") . "</h3>\n";
    print '            <div class="details"><div class="title">' . _("Sequence:") . '</div><div class="label">' . wordwrap($row[4], 3, ' ', 1) . "</div></div>\n";
    if (!empty($row[5])) print '            <div class="details"><div class="title">' . _("Modification") . '</div><div class="label">' . $row[5] . "</div></div>\n";
    print '            <div class="details"><div class="title">' . _("GC%:") . '</div><div class="label">' . CG($row[4]) . "</div></div>\n";
    print '            <div class="details"><div class="title">' . _("TM:") . '</div><div class="label">' . round(Tm($row[4]), 0) . "&deg;C</div></div>\n";
    print '            <div class="details"><div class="title">' . _("Mw:") . '</div><div class="label">' . round(Mw($row[4]), 2) . " g/mol</div></div>\n";
    print '            <h3>' . ("Location") . "</h3>\n";
    if (!empty($row[7])) print '            <div class="details"><div class="title">' . _("Freezer") . '</div><div class="label">' . $row[7] . "</div></div>\n";
    if (!empty($row[6])) print '            <div class="details"><div class="title">' . _("box:") . '</div><div class="label">' . $row[6] . (!empty($row[8])?' (' . $row[8] . ')':'') . "</div></div>\n";
    if (!empty($row[9])) print '            <h3>' . _("Comments") . "</h3>\n" . '            <div class="details">' . htmlentities($row[9], ENT_COMPAT, 'ISO-8859-15') . "</div>\n";
    if (!empty($row[10])) {
      print '            <h3>' . ("Design") . "</h3>\n";
      if (!empty($row[10]) && ($row[10] == 1)) {
        print '            <div class="details"><div class="title">' . _("Design:") . '</div><div class="label">' . _("Manual") . "</div></div>\n";
      }elseif (!empty($row[10]) && ($row[10] == 2)) {
        print '            <div class="details"><div class="title">' . _("Design:") . '</div><div class="label">' . _("Software") . "</div></div>\n";
        if (!empty($row[11])) print '            <div class="details"><div class="title">' . _("Program:") . '</div><div class="label">' . $row[11] . (!empty($row[12]) ? ' / ' . $row[12] : '') . "</div></div>\n";
        if (!empty($row[13])) print '            <div class="details"><div class="title">' . _("comments:") . '</div><div class="label">' . preg_replace('/\[([^\|\]]*)\|([^\|\]]*)\]/', '<a href="\1">\2</a>', str_replace("\n", '<br />', htmlentities($row[13], ENT_COMPAT, 'ISO-8859-1'))) . "</div></div>\n";
      }
    }
    if (!empty($row[14])) put_ref($row[14], $sql);

    print "          </div>\n";
    $result = sql_query('SELECT updated, prefix, id, forward_prefix, forward_id, reverse_prefix, reverse_id FROM oligoml_pair WHERE ((forward_prefix=' . octdec(intval($oligo[1])) . ' AND forward_id=' . octdec(intval($oligo[2])) . ') OR (reverse_prefix=' . octdec(intval($oligo[1])) . ' AND reverse_id=' . octdec(intval($oligo[2])) . '));', $sql);
    if ((!strlen($r = sql_last_error($sql))) && (sql_num_rows($result) > 0)) {
      print "            <div>\n              <h2>" . _("Primer sets") . "</h2>\n";
      while ($row = sql_fetch_row($result)) {
        print '              <div class="result"><span class="ref"><a href="' . $config['server'] . $plugin['oligoml']['url'] . '/pair/P' . decoct($row[1]) . '.' . decoct($row[2]) . '">P' . decoct($row[1]) . '.' . decoct($row[2]) . '</a></span><span class="desc">Forward <a href="' . $config['server'] . $plugin['oligoml']['url'] . '/oligo/O' . decoct($row[3]) . '.' . decoct($row[4]) . '">O' . decoct($row[3]) . '.' . decoct($row[4]) . '</a></span><span class="detail">Reverse <a href="' . $config['server'] . $plugin['oligoml']['url'] . '/oligo/O' . decoct($row[5]) . '.' . decoct($row[6]) . '">O' . decoct($row[5]) . '.' . decoct($row[6]) . '</a></span><span class="updated">' . date('Y-m-d', strtotime($row[0])) . "</span></div>\n";
      }
      print "            </div>\n";
    }
  }
}

if ($config['login']) {
  head('oligoml');
?>
        <div class="items">
          <h1><?php print _("Details"); ?><small><?php print _("All about..."); ?></small></h1><br />
<?php
  if (!empty($_GET['pair']) || !empty($_GET['oligo'])) {
    $sql = sql_connect($config['db']);
    if (!empty($_GET['oligo']) && preg_match('/O(\d+)\.(\d+)/', rawurldecode($_GET['oligo']), $matches)) {
      get_oligo($matches, $sql);
    }elseif (!empty($_GET['pair']) && preg_match('/P(\d+)\.(\d+)/', rawurldecode($_GET['pair']), $matches)) {
      $result = sql_query('SELECT a.prefix, a.id, a.release, a.forward_prefix, a.forward_id, a.reverse_prefix, a.reverse_id, a.speciesid, a.species, a.geneid, a.locus, a.amplicon, a.sequenceid, a.location, a.pcr, a.buffer, a.comments, a.reference, a.updated, a.author, b.code FROM oligoml_pair AS a, users AS b WHERE (a.prefix=' . octdec(intval($matches[1])) . ' AND a.id=' . octdec(intval($matches[2])) . ' AND a.author=b.username);', $sql);
      if ((!strlen($r = sql_last_error($sql))) && (sql_num_rows($result) == 1)) {
        $row = sql_fetch_row($result); ?>
          <div>
            <h2>O<?php print decoct($row[3]) . '.' . decoct($row[4]) . ' / O' . decoct($row[5]) . '.' . decoct($row[6]) . (($_SESSION['login']['right'] >= 3)?'<small><a href="' . $config['server'] . $plugin['oligoml']['url'] . '/edit/P' . decoct($row[0]) . '.' . decoct($row[1]) . '" title="' . _("edit") . '">' . _("edit") . '</a></small>':''); ?></h2>
<?php
        print '            <h3>' . _("Details") . "</h3>\n";
        print '            <div class="details"><div class="title">' . _("ID:") . '</div><div class="label"><a href="' . $config['server'] . $plugin['oligoml']['url'] . '/xml/P' . decoct($row[0]) . '.' . decoct($row[1]) . '" title="' . _("Export xml format") . '">P' . decoct($row[0]) . '.' . decoct($row[1]) . "</a></div></div>\n";
        print '            <div class="details"><div class="title">' . _("Revision:") . '</div><div class="label">' . $row[2] . ' (<em>' . gmdate(_("d-m-Y"), strtotime($row[18])) . "</em>)</div></div>\n";
        print '            <div class="details"><div class="title">' . _("Author:") . '</div><div class="label">' . $row[20] . $row[19] . "</div></div>\n";
        print '            <div class="details"><div class="title">' . _("Forward:") . '</div><div class="label">' . decoct($row[3]) . '.' . decoct($row[4]) . "</div></div>\n";
        print '            <div class="details"><div class="title">' . _("Reverse:") . '</div><div class="label">' . decoct($row[5]) . '.' . decoct($row[6]) . "</div></div>\n";
        print '            <h3>' . ("Specificsity") . "</h3>\n";
        print '            <div class="details"><div class="title">' . _("Specie:") . '</div><div class="label">' . (!empty($row[8]) ? $row[8] : '') . (!empty($row[7]) ? (' (id: ' . $row[7] . ')') : '') . "</div></div>\n";
        print '            <div class="details"><div class="title">' . _("Target:") . '</div><div class="label">' . (!empty($row[10]) ? $row[10] : '') . (!empty($row[9]) ? (' (id: ' . $row[9] . ')') : '') . "</div></div>\n";
        print '            <div class="details"><div class="title">' . _("Length:") . '</div><div class="label">' . (!empty($row[11]) ? $row[11] . ' bp' : '') . (!empty($row[12]) ? (' (id: ' . $row[12] . ')') : '') . "</div></div>\n";
        if (!empty($row[13])) print '<div class="details"><div class="title">' . _("Location:") . '</div><div class="label">' . $row[13] . "</div></div>\n";
        if (!empty($row[14]) || !empty($row[15])) {
          print '            <h3>' . ("Conditions") . "</h3>\n";
          if (!empty($row[14])) print '            <div class="details"><div class="title">' . _("pcr:") . '</div><div class="label">' . $row[14] . "</div></div>\n";
          if (!empty($row[15])) print '            <div class="details"><div class="title">' . _("buffer:") . '</div><div class="label">' . $row[15] . "</div></div>\n";
        }
        if (!empty($row[16])) print '            <h3>' . _("Comments") . "</h3>\n" . '            <div class="details">' . preg_replace('/\[([^\|\]]*)\|([^\|\]]*)\]/', '<a href="\1">\2</a>', str_replace("\n", '<br />', htmlentities($row[16], ENT_COMPAT, 'ISO-8859-1'))) . "</div>\n";
        if (!empty($row[17])) put_ref($row[17], $sql);
        print "          </div>\n          <br />\n        </div>\n        <div class=\"items\">\n";
        get_oligo(array(1 => decoct($row[3]), 2 => decoct($row[4])), $sql);
        print "          <br />\n        </div>\n        <div class=\"items\">\n";
        get_oligo(array(1 => decoct($row[5]), 2 => decoct($row[6])), $sql);
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