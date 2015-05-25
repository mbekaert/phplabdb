<?php
ob_start("ob_gzhandler");
session_start();
require_once('../includes/main.inc');

if ($config['login']) {
  if (!isset($_SESSION['specie'])) {
    @set_pref('specie', 'name');
  }
  if (!isset($_SESSION['pairs'])) {
    @set_pref('pair', false);
  }
  if (!isset($_SESSION['filter'])) {
    @set_pref('filter', false);
  }
  if (!isset($_SESSION['limit'])) {
    @set_pref('limit', false);
  }
  if (!isset($_SESSION['primer'])) {
    @set_pref('primer', 5);
  }
  if (isset($_POST['specie']) && isset($_POST['pairs']) && isset($_POST['filter']) && isset($_POST['limit']) && isset($_POST['primer'])) {
    @set_pref('specie', (($_POST['specie'] == 'alias')?'alias':'name'));
    @set_pref('pairs', (($_POST['pairs'] == 'false')?false:true));
    @set_pref('filter', (($_POST['filter'] == 'false')?false:intval($_POST['filter'])));
    @set_pref('limit', (($_POST['limit'] == 'false')?false:intval($_POST['limit'])));
    @set_pref('primer', intval($_POST['primer']));
  }
  head('uniprime');
?>
        <div class="items">
          <h1><?php print $plugin['uniprime']['name']; ?><small><?php print $plugin['uniprime']['description']; ?></small></h1><br />
          <form method="post" action="<?php print $config['server'] . $plugin['uniprime']['url']; ?>/options">
           <div>
            <h2><?php print _("Options"); ?></h2><br /><?php print _("You may modify the output options such the common name, latin name etc."); ?><br /><br />
            <div>
              <label for="specie"><?php print _("Species naming"); ?></label>
              <select name="specie" id="specie" title="<?php print _("Species naming"); ?>"><option value="name"<?php print ((get_pref('specie') == 'name')?' selected="selected"':''); ?>><?php print _("Latin name"); ?></option><option value="alias"<?php print ((get_pref('specie') == 'alias')?' selected="selected"':''); ?>><?php print _("Common name"); ?></option></select>
              <br />
            </div>
            <div>
              <label for="pairs"><?php print _("Show primer pairing"); ?></label>
              <select name="pairs" id="pairs" title="<?php print _("Show primer alignment"); ?>"><option value="false"<?php print ((!get_pref('pairs'))?' selected="selected"':''); ?>><?php print _("No"); ?></option><option value="true"<?php print ((get_pref('pairs'))?' selected="selected"':''); ?>><?php print _("Yes"); ?></option></select>
              <br />
            </div>
<?php
  $sql = sql_connect($config['db']);
  $result = sql_query('SELECT id, class FROM uniprime_class WHERE id>0 ORDER BY id;', $sql);
  if ((!strlen($r = sql_last_error($sql))) && (sql_num_rows($result) > 1)) {
?>
            <div>
              <label for="filter"><?php print _("Filter"); ?></label>
              <select name="filter" id="pairs" title="<?php print _("Browser filter"); ?>"><option value="0"<?php print ((!get_pref('filter'))?' selected="selected"':''); ?>><?php print _("show all"); ?></option><?php
    while ($row = sql_fetch_row($result)) {
      print '<option value="' . $row[0] . '"' . ((get_pref('filter') == $row[0])?' selected="selected"':'') . '>' . $row[1] . '</option>';
    }
?></select>
               <br />
            </div>
<?php }
?>
            <div>
              <label for="limit"><?php print _("Search limit"); ?></label>
              <select name="limit" id="limit" title="<?php print _("Search limit"); ?>"><option value="false"<?php print ((!get_pref('limit'))?' selected="selected"':''); ?>><?php print _("No filter"); ?></option><option value="8"<?php print ((get_pref('limit') == 8)?' selected="selected"':''); ?>><?php print _("Working view"); ?></option></select>
              <br />
            </div>
            <div>
              <label for="primer"><?php print _("Primer max. mismatch"); ?></label>
              <select name="primer" id="primer" title="<?php print _("Maximum of mismatch between a primer and the consensus sequence allowed"); ?>"><?php
  for ($i = 0; $i < 11; $i++) {
    print '<option value="' . $i . '"' . ((get_pref('primer') == $i)?' selected="selected"':'') . '>' . $i . '</option>';
  }
?></select>
              <br />
            </div>
            <br />
            <input type="hidden" name="uniprime" value="<?php print md5('options' . floor(intval(date('b')))); ?>" />
            <input type="submit" value="<?php print _("Validate"); ?>" />
          </div>
          </form>
         </div>
<?php
  foot();
}else {
  header('Location: ' . $config['server']);
}
?>