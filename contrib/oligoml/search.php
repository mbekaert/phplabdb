<?php
ob_start("ob_gzhandler");
session_start();
require_once('../includes/main.inc');

if ($config['login']) {
  $table = array('O' => 'oligo', 'P' => 'pair');
  if (isset($_GET['query']) && preg_match('/(\w)(\d+)\.(\d+)/', strtoupper($_GET['query']), $matches) && array_key_exists($matches[1], $table)) {
    header('Location: ' . $config['server'] . $plugin['oligoml']['url'] . '/' . $table[$matches[1]] . '/' . $matches[0]);
    exit;
  }
  head('oligoml');
?>
         <div class="items">
           <h1><?php print $plugin['oligoml']['name']; ?><small><?php print $plugin['oligoml']['description']; ?></small></h1><br />
          <form method="post" action="<?php print $config['server'] . $plugin['oligoml']['url']; ?>/search">
          <div>
            <h2><?php print _("Search"); ?><small><a href="<?php print $config['server'] . $plugin['oligoml']['url']; ?>/search" title="<?php print _("Identify a oligonuclotide or a primer set"); ?>"><?php print _("Advanced search"); ?></a></small></h2><br /><?php print _("Retrieve an oligonucleotide, a primer or a primer set. You may provide a reference numbre, a primer name, or a nucleic sequence."); ?><br /><br />
            <div>
              <label for="search"><?php print _("search"); ?></label>
              <input name="search" id="search" type="text" maxlength="32"<?php print (!empty($_POST['search'])?' value="' . stripslashes(strip_tags(trim($_POST['search']))) . '"':''); ?> />
              <br />
            </div>
            <br />
            <input type="hidden" name="oligoml" value="<?php print md5('search' . floor(intval(date('b')))); ?>" />
            <input type="submit" value="<?php print _("Search"); ?>" />
          </div>
          </form>
<?php
  if (!empty($_POST['oligoml']) && ($_POST['oligoml'] == md5('search' . floor(intval(date('b'))))) && !empty($_POST['search'])) {
    $sql = sql_connect($config['db']);
    $search = preg_replace('/[^\w\d\.\-\_\/ \(\)]/', '', stripslashes(strip_tags(trim($_POST['search']))));
    if (preg_match('/(\d+)\.(\d+)/', $search, $matches)) {
      $result = sql_query('SELECT updated, prefix, id, name, sequence FROM oligoml_oligo WHERE (prefix=' . octdec(intval($matches[1])) . ' AND id=' . octdec(intval($matches[2])) . ');', $sql);
    }else {
      $result = sql_query('SELECT updated, prefix, id, name, sequence FROM oligoml_oligo WHERE (name' . sql_reg(addslashes($search)) . ' OR sequence' . sql_reg(addslashes($search)) . ') ORDER BY updated DESC;', $sql);
    }
    if ((!strlen($r = sql_last_error($sql))) && (sql_num_rows($result) > 0)) {
      print "            <div>\n              <h2>" . _("Results (Oligonucleotides)") . "</h2>\n";
      while ($row = sql_fetch_row($result)) {
        print '              <div class="result"><span class="ref"><a href="' . $config['server'] . $plugin['oligoml']['url'] . '/oligo/O' . decoct($row[1]) . '.' . decoct($row[2]) . '">O' . decoct($row[1]) . '.' . decoct($row[2]) . '</a></span><span class="desc">' . $row[3] . '</span><span class="detail">' . $row[4] . '</span><span class="updated">' . date('Y-m-d', strtotime($row[0])) . '</span><span class="tag"><a href="' . $config['server'] . $plugin['oligoml']['url'] . '/tag/O' . decoct($row[1]) . '.' . decoct($row[2]) . '"><img src="' . $config['server'] . "/images/tag.png\" alt=\"tag\" /></a></span></div>\n";
      }
      print "            </div>\n";
    }
    if (isset($matches[1])) {
      $result = sql_query('SELECT updated, prefix, id, forward_prefix, forward_id, reverse_prefix, reverse_id FROM oligoml_pair WHERE (prefix=' . octdec(intval($matches[1])) . ' AND id=' . octdec(intval($matches[2])) . ');', $sql);
    }else {
      $result = sql_query('SELECT updated, prefix, id, forward_prefix, forward_id, reverse_prefix, reverse_id FROM oligoml_pair WHERE (comments' . sql_reg(addslashes($search)) . ' OR species' . sql_reg(addslashes($search)) . ' OR locus' . sql_reg(addslashes($search)) . ' OR "location"' . sql_reg(addslashes($search)) . ') ORDER BY updated DESC;', $sql);
    }
    if ((!strlen($r = sql_last_error($sql))) && (sql_num_rows($result) > 0)) {
      print "            <div>\n              <h2>" . _("Results (Primer sets)") . "</h2>\n";
      while ($row = sql_fetch_row($result)) {
        print '              <div class="result"><span class="ref"><a href="' . $config['server'] . $plugin['oligoml']['url'] . '/pair/P' . decoct($row[1]) . '.' . decoct($row[2]) . '">P' . decoct($row[1]) . '.' . decoct($row[2]) . '</a></span><span class="desc">Forward <a href="' . $config['server'] . $plugin['oligoml']['url'] . '/oligo/O' . decoct($row[3]) . '.' . decoct($row[4]) . '">O' . decoct($row[3]) . '.' . decoct($row[4]) . '</a></span><span class="detail">Reverse <a href="' . $config['server'] . $plugin['oligoml']['url'] . '/oligo/O' . decoct($row[5]) . '.' . decoct($row[6]) . '">O' . decoct($row[5]) . '.' . decoct($row[6]) . '</a></span><span class="updated">' . date('Y-m-d', strtotime($row[0])) . "</span></div>\n";
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