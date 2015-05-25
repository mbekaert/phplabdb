<?php
ob_start("ob_gzhandler");
session_start();
require_once('../includes/main.inc');

if ($config['login']) {
  $table = array('L' => 'locus', 'S' => 'sequence', 'A' => 'alignment', 'R' => 'mrna', 'X' => 'primer');
  if (!isset($_SESSION['limit'])) {
    @set_pref('limit', false);
  }
  if (isset($_GET['query']) && preg_match('/(\w)(\d+)\.(\d+)/', strtoupper($_GET['query']), $matches) && array_key_exists($matches[1], $table)) {
    header('Location: ' . $config['server'] . $plugin['uniprime']['url'] . '/' . $table[$matches[1]] . '/' . $matches[0]);
    exit;
  }elseif (!empty($_POST['uniprime']) && ($_POST['uniprime'] == md5('search' . floor(intval(date('b'))))) && !empty($_POST['search'])) {
    $query = preg_replace('/&amp;(#x?[0-9a-f]+);/', '&\1', htmlentities(html_entity_decode(trim($_POST['search'])), ENT_QUOTES, 'ISO8859-1'));
  }
  head('uniprime');
?>
        <div class="items">
          <h1><?php print $plugin['uniprime']['name']; ?><small><?php print $plugin['uniprime']['description']; ?></small></h1><br />
          <form method="post" action="<?php print $config['server'] . $plugin['uniprime']['url']; ?>/search">
          <div>
            <h2><?php print _("Search"); ?><small><a href="<?php print $config['server'] . $plugin['uniprime']['url']; ?>/search" title="<?php print _("Identify a locus or a sequence"); ?>"><?php print _("Advanced search"); ?></a></small></h2><br /><?php print _("Retrieve a locus, a sequence, an alignment or a primer set. You may provide a reference numbre, a primer name, or a keywork."); ?><br /><br />
            <div>
              <label for="search"><?php print _("search"); ?></label>
              <input name="search" id="search" type="text" maxlength="32"<?php print (!empty($query)?' value="' . $query . '"':''); ?> />
              <br />
            </div>
            <br />
            <input type="hidden" name="uniprime" value="<?php print md5('search' . floor(intval(date('b')))); ?>" />
            <input type="submit" value="<?php print _("Search"); ?>" />
          </div>
          </form>
<?php
  if (!empty($query)) {
    $query = addslashes($query);
    $sql = sql_connect($config['db']);
    preg_match('/^(\w)(\d+)\.(\d+)/', $query, $matches);
    // locus
    $result = sql_query('SELECT DISTINCT a.prefix, a.id, a.name, a.functions, a.class, a.status, b.status FROM uniprime_locus AS a,  uniprime_status AS b WHERE a.status=b.id' . ((isset($matches[1]) && ($matches[1] == 'L'))?' AND a.prefix=' . octdec(intval($matches[2])) . ' AND a.id=' . octdec(intval($matches[3])):(get_pref('limit')?(' AND a.status>0 AND a.status<=' . get_pref('limit')):'') . ' AND (a.name' . sql_reg($query) . ' OR a.alias' . sql_reg($query) . ' OR a.functions' . sql_reg($query) . ' OR a.comments' . sql_reg($query) . ')') . ' ORDER BY a.name;', $sql);
    if ((!strlen($r = sql_last_error($sql))) && (sql_num_rows($result) > 0)) {
      print "            <div>\n              <h2>" . _("Results (Locus)") . "</h2>\n";
      while ($row = sql_fetch_row($result)) {
        print '              <div class="result"><span class="ref"><a href="' . $config['server'] . $plugin['uniprime']['url'] . '/locus/L' . decoct($row[0]) . '.' . decoct($row[1]) . '" title="L' . decoct($row[0]) . '.' . decoct($row[1]) . '" class="cl' . $row[4] . '">' . $row[2] . '</a></span><span class="desclong">' . ((strlen($row[3]) > 63)?(substr($row[3], 0, 60) . '...'):$row[3]) . '</span><span class="updated"><img src="' . $config['server'] . $plugin['uniprime']['url'] . '/images/st' . $row[5] . '.png" height="16" width="16" alt="' . $row[6] . "\" /></span></div>\n";
      }
      print "            </div>\n";
    }
    // sequences
    $result = sql_query('SELECT DISTINCT a.prefix, a.id, a.name, a.map, a.accession, a.organism, c.commonname, a.sequence_type, b.sequence_type, a.updated, a.organelle FROM  uniprime_sequence AS a,  uniprime_sequence_type AS b, tree_taxonomy as c WHERE a.sequence_type=b.id AND a.organism=c.scientificname AND ' . ((isset($matches[1]) && ($matches[1] == 'S'))?'a.prefix=' . octdec(intval($matches[2])) . ' AND a.id=' . octdec(intval($matches[3])):('(a.name' . sql_reg($query) . ' OR a.alias' . sql_reg($query) . ' OR a.comments' . sql_reg($query) . ')')) . ' ORDER BY a.updated;', $sql);
    if ((!strlen($r = sql_last_error($sql))) && (sql_num_rows($result) > 0)) {
      print "            <div>\n              <h2>" . _("Results (Sequences)") . "</h2>\n";
      while ($row = sql_fetch_row($result)) {
        if (($_SESSION['specie'] == 'alias') && isset($row[6])) {
          $specie = ucfirst($row[6]);
        }else {
          $specie = ucfirst($row[5]);
        }
        $desc = '<em>' . $specie . '</em>' . (isset($row[3])?' - ' . $row[3]:'') . (!empty($row[4])?' (' . $row[4] . ')':'') . (isset($row[10])?' [' . $row[10] . ']':'');
        print '              <div class="result"><span class="ref"><a href="' . $config['server'] . $plugin['uniprime']['url'] . '/sequence/S' . decoct($row[0]) . '.' . decoct($row[1]) . '" title="S' . decoct($row[0]) . '.' . decoct($row[1]) . '" class="sq' . $row[7] . '">' . $row[2] . '</a></span><span class="desc">' . $row[8] . '</span><span class="detail">' . ((strlen($desc) > 63)?(substr($desc, 0, 60) . '...'):$desc) . '</span><span class="updated">' . date(_("m/d/Y"), strtotime($row[9])) . "</span></div>\n";
      }
      print "            </div>\n";
    }
    // primers
    $result = sql_query('SELECT a.prefix, a.id, b.name, a.left_name, a.right_name, a.location, a.updated FROM  uniprime_primer AS a,  uniprime_locus AS b WHERE ' . ((isset($matches[1]) && ($matches[1] == 'X'))?'a.prefix=' . octdec(intval($matches[2])) . ' AND a.id=' . octdec(intval($matches[3])):('(a.left_name' . sql_reg($query) . ' OR a.right_name' . sql_reg($query) . ' OR a.comments' . sql_reg($query) . ')')) . ' AND b.prefix=a.locus_prefix AND b.id=a.locus_id;', $sql);
    if ((!strlen($r = sql_last_error($sql))) && (sql_num_rows($result) > 0)) {
      print "            <div>\n              <h2>" . _("Results (Primers)") . "</h2>\n";
      $i = 1;
      while ($row = sql_fetch_row($result)) {
        print '              <div class="result"><span class="ref"><a href="' . $config['server'] . $plugin['uniprime']['url'] . '/primer/X' . decoct($row[0]) . '.' . decoct($row[1]) . '" title="X' . decoct($row[0]) . '.' . decoct($row[1]) . '" class="primer">' . $row[2] . '</a></span><span class="desc">' . substr($row[5], 0, strpos($row[5], '(')) . '</span><span class="detail">' . ((isset($row[3]) && isset($row[4]))?($row[3] . ' / ' . $row[4]):'') . '</span><span class="updated">' . date(_("m/d/Y"), strtotime($row[6])) . "</span></div>\n";
      }
      print "            </div>\n";
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