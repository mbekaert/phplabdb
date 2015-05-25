<?php
ob_start("ob_gzhandler");
session_start();
require_once('../includes/main.inc');

if ($config['login']) {
  if (!isset($_SESSION['filter'])) {
    @set_pref('filter', false);
  }
  if (!isset($_SESSION['limit'])) {
    @set_pref('limit', false);
  }
  head('uniprime');
?>
        <div class="items">
          <h1><?php print $plugin['uniprime']['name']; ?><small><?php print $plugin['uniprime']['description']; ?></small></h1><br />
          <div>
            <h2><?php print _("Browse") . (($_SESSION['login']['right'] >= 3)?'<small><a href="' . $config['server'] . $plugin['uniprime']['url'] . '/add" title="' . _("Add a new locus") . '">' . _("Add a new locus") . '</a></small>':''); ?></h2><br />
<?php
  $sql = sql_connect($config['db']);
  $result = sql_query('SELECT a.prefix, a.id, a.name, a.functions, a.class, a.status, b.status FROM uniprime_locus AS a, uniprime_status AS b WHERE a.status=b.id' . (get_pref('limit')?(' AND a.status>0 AND a.status<=' . get_pref('limit')):'') . (get_pref('filter')?(' AND a.class=' . get_pref('filter')):'') . ' ORDER BY a.name ASC;', $sql);
  if ((!strlen($r = sql_last_error($sql))) && (sql_num_rows($result) > 0)) {
    while ($row = sql_fetch_row($result)) {
      print '              <div class="result"><span class="ref"><a href="' . $config['server'] . $plugin['uniprime']['url'] . '/locus/L' . decoct($row[0]) . '.' . decoct($row[1]) . '" title="L' . decoct($row[0]) . '.' . decoct($row[1]) . '" class="cl' . $row[4] . '">' . $row[2] . '</a></span><span class="desclong">' . ((strlen($row[3]) > 63)?(substr($row[3], 0, 60) . '...'):$row[3]) . '</span><span class="tag"><img src="' . $config['server'] . $plugin['uniprime']['url'] . '/images/st' . $row[5] . '.png" height="16" width="16" alt="' . $row[6] . "\" /></span></div>\n";
    }
  }
?>
         </div>
         <br />
       </div>
<?php
  foot();
}else {
  header('Location: ' . $config['server']);
}
?>