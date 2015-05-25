<?php
ob_start("ob_gzhandler");
session_start();
require_once('../includes/main.inc');

if ($config['login']) {
  $table = array('B' => 'bioject', 'D' => 'sample');
  if (isset($_GET['query']) && preg_match('/(\w)(\d+)\.(\d+)/', strtoupper($_GET['query']), $matches) && array_key_exists($matches[1], $table)) {
    header('Location: ' . $config['server'] . $plugin['darwin']['url'] . '/' . $table[$matches[1]] . '/' . $matches[0]);
    exit;
  }
  head('darwin');
?>
        <div class="items">
          <h1><?php print $plugin['darwin']['name']; ?><small><?php print $plugin['darwin']['description']; ?></small></h1><br />
          <form method="post" action="<?php print $config['server'] . $plugin['darwin']['url']; ?>/search">
          <div>
            <h2><?php print _("Search"); ?><small><a href="<?php print $config['server'] . $plugin['darwin']['url']; ?>/search" title="<?php print _("Identify a sample or a collection entry"); ?>"><?php print _("Advanced search"); ?></a></small></h2><br /><?php print _("Retrieve a sample, an collection entry... You may provide a reference numbre, a keyword, or a name."); ?><br /><br />
            <div>
              <label for="search"><?php print _("search"); ?></label>
              <input name="search" id="search" type="text" maxlength="32"<?php print (!empty($_POST['search'])?' value="' . stripslashes(strip_tags(trim($_POST['search']))) . '"':''); ?> />
              <br />
            </div>
            <br />
            <input type="hidden" name="darwin" value="<?php print md5('search' . floor(intval(date('b')))); ?>" />
            <input type="submit" value="<?php print _("Search"); ?>" />
          </div>
          </form>
<?php
  if (!empty($_POST['darwin']) && ($_POST['darwin'] == md5('search' . floor(intval(date('b'))))) && !empty($_POST['search'])) {
    $sql = sql_connect($config['db']);
    $search = preg_replace('/[^\w\d\.\-\_\/ \(\)]/', '', stripslashes(strip_tags(trim($_POST['search']))));
    if (preg_match('/(\w+)-(\w+)-([\w\d\.]+)/', $search, $matches)) {
      $result = sql_query('SELECT prefix, id, institutioncode, collectioncode, catalognumber, scientificname FROM darwin_bioject WHERE (institutioncode=\'' . addslashes($matches[1]) . '\' AND collectioncode=\'' . addslashes($matches[2]) . '\' AND catalognumber=\'' . addslashes($matches[3]) . '\');', $sql);
    }else {
      $result = sql_query('SELECT prefix, id, institutioncode, collectioncode, catalognumber, scientificname FROM darwin_bioject WHERE (geolocation' . sql_reg(addslashes($search)) . ' OR event' . sql_reg(addslashes($search)) . ' OR catalognumber' . sql_reg(addslashes($search)) . ' OR comments' . sql_reg(addslashes($search)) . ');', $sql);
    }
    if ((!strlen($r = sql_last_error($sql))) && (sql_num_rows($result) > 0)) {
      print "          <div>\n            <h2>" . _("Results (Collection entry)") . "</h2>\n";
      while ($row = sql_fetch_row($result)) {
        print '            <div class="result"><span class="ref"><a href="' . $config['server'] . $plugin['darwin']['url'] . '/bioject/B' . decoct($row[0]) . '.' . decoct($row[1]) . '">' . $row[2] . '-' . $row[3] . '-' . $row[4] . '</a></span><span class="desc"><em>' . $row[5] . "</em></span></div>\n";
      }
      print "          </div>\n";
    }
    if (preg_match('/(\w+)-(\w+)-([\w\d\.]+)/', $search, $matches)) {
      $result = sql_query('SELECT a.prefix, a.id, b.institutioncode, b.collectioncode, b.catalognumber, a.subcatalognumber, a.basisofrecord, a.partname FROM darwin_sample AS a, darwin_bioject AS b WHERE (b.institutioncode=\'' . addslashes($matches[1]) . '\' AND b.collectioncode=\'' . addslashes($matches[2]) . '\' AND b.catalognumber=\'' . addslashes($matches[3]) . '\') AND b.prefix=a.bioject_prefix AND b.id=a.bioject_id;', $sql);
    }else {
      $result = sql_query('SELECT a.prefix, a.id, b.institutioncode, b.collectioncode, b.catalognumber, a.subcatalognumber, a.basisofrecord, a.partname FROM darwin_sample AS a, darwin_bioject AS b WHERE (a.comments' . sql_reg(addslashes($search)) . ' OR a.partname' . sql_reg(addslashes($search)) . ' OR disposition' . sql_reg(addslashes($search)) . 'OR b.geolocation' . sql_reg(addslashes($search)) . ' OR b.event' . sql_reg(addslashes($search)) . ' OR b.catalognumber' . sql_reg(addslashes($search)) . ') AND b.prefix=a.bioject_prefix AND b.id=a.bioject_id;', $sql);
    }
    if ((!strlen($r = sql_last_error($sql))) && (sql_num_rows($result) > 0)) {
      print "          <div>\n            <h2>" . _("Results (Sample)") . "</h2>\n";
      while ($row = sql_fetch_row($result)) {
        print '            <div class="result"><span class="ref"><a href="' . $config['server'] . $plugin['darwin']['url'] . '/sample/D' . decoct($row[0]) . '.' . decoct($row[1]) . '">' . ucfirst($row[6]) . '</a></span><span class="desc">' . $row[2] . '-' . $row[3] . '-' . $row[4] . '-' . $row[5] . '</span><span class="detail">' . ucfirst($row[7]) . "</span></div>\n";
      }
      print "          </div>\n";
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