<?php
ob_start("ob_gzhandler");
session_start();
require_once('../includes/main.inc');

if ($config['login']) {
  if (!empty($_POST['tree']) && ($_POST['tree'] == md5('search' . floor(intval(date('b'))))) && !empty($_POST['search'])) {
    $query = preg_replace('/&amp;(#x?[0-9a-f]+);/', '&\1', htmlentities(html_entity_decode(trim($_POST['search'])), ENT_QUOTES, 'ISO8859-1'));
  }
  head('tree');
?>
        <div class="items">
          <h1><?php print $plugin['tree']['name']; ?><small><?php print $plugin['tree']['description']; ?></small></h1><br />
          <form method="post" action="<?php print $config['server'] . $plugin['tree']['url']; ?>/search">
          <div>
            <h2><?php print _("Search"); ?><small><a href="<?php print $config['server'] . $plugin['tree']['url']; ?>/search" title="<?php print _("Identify an organism"); ?>"><?php print _("Advanced search"); ?></a></small></h2><br /><?php print _("Retrieve an organism. You may provide a reference numbre, a latin or an english name."); ?><br /><br />
            <div>
              <label for="search"><?php print _("search"); ?></label>
              <input name="search" id="search" type="text" maxlength="32"<?php print (!empty($query)?' value="' . $query . '"':''); ?> />
              <br />
            </div>
            <br />
            <input type="hidden" name="tree" value="<?php print md5('search' . floor(intval(date('b')))); ?>" />
            <input type="submit" value="<?php print _("Search"); ?>" />
          </div>
          </form>
<?php
  if (!empty($query)) {
    $query = addslashes($query);
    $sql = sql_connect($config['db']);
    $result = sql_query('SELECT scientificname, commonname, abbrivation FROM tree_taxonomy WHERE scientificname' . sql_reg($query) . ' OR commonname' . sql_reg($query) . ' OR alias' . sql_reg($query) . ' OR abbrivation' . sql_reg($query) . ' OR comments' . sql_reg($query) . ' ORDER BY scientificname;', $sql);
    if ((!strlen($r = sql_last_error($sql))) && (sql_num_rows($result) > 0)) {
      print "            <div>\n              <h2>" . _("Organism") . "</h2>\n";
      while ($row = sql_fetch_row($result)) {
        print '              <div class="result"><span class="ref"><em><a href="' . $config['server'] . $plugin['tree']['url'] . '/species/' . rawurlencode(str_replace(' ', '_', $row[0])) . '" title="' . ucfirst($row[0]) . '">' . ucfirst($row[0]) . '</a></em></span><span class="desc">' . (isset($row[1]) ? ucfirst($row[1]) : '') . '</span><span class="detail">' . (isset($row[2]) ? ucfirst($row[2]) : '') . "</span></div>\n";
      }
      print "            </div>\n          <br />\n";
    }
  }
?>
        </div>
<?php
  foot();
}else {
  header('Location: ' . $config['server']);
}
?>