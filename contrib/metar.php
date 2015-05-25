<?php
ob_start("ob_gzhandler");
session_start();
require_once('includes/main.inc');

if ($config['login']) {
  head('metar');
?>
        <div class="items">
          <h1>METAR<small><?php print _("aviation routine weather report") ?></small></h1><br />
          <form method="post" action="<?php print $config['server']; ?>/metar.php">
          <div>
            <h2><?php print _("Search"); ?></h2><br /><?php print _("Retrieve a METAR entry."); ?><br /><br />
            <div>
              <label for="search"><?php print _("search"); ?></label>
              <input name="search" id="search" type="text" maxlength="32"<?php print (!empty($_POST['search'])?' value="' . stripslashes(strip_tags(trim($_POST['search']))) . '"':''); ?> />
              <br />
            </div>
            <br />
            <input type="submit" value="<?php print _("Search"); ?>" />
          </div>
          </form>
<?php
  if (!empty($_POST['search']) && ($sql = @sql_connect('metar'))) {
    $search = preg_replace('/[^\w\d\.\-\_\/ \(\)]/', '', stripslashes(strip_tags(trim($_POST['search']))));
    $result = sql_query('SELECT icao, report, metar FROM metar WHERE (icao' . sql_reg(addslashes($search)) . ' OR report' . sql_reg(addslashes($search)) . ') ORDER BY report DESC LIMIT 100;', $sql);
    if ((!strlen($r = sql_last_error($sql))) && (sql_num_rows($result) > 0)) {
      print "          <div>\n            <h2>" . _("Results") . "</h2>\n";
      while ($row = sql_fetch_row($result)) {
        print '            <div class="result"><span class="ref">' . $row[0] . '</span><span class="desc">' . $row[1] . '</span><span class="details"><small>' . $row[2] . "</small></span></div>\n";
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