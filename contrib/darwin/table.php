<?php
ob_start("ob_gzhandler");
session_start();
require_once('../includes/main.inc');

function get_names($table, $sql) {
  global $config;
  if ($config['sqlserver'] == 'postgresql') {
    $result = sql_query('SELECT column_name, character_maximum_length, is_nullable FROM information_schema.columns WHERE table_name=\'darwin_' . addslashes($table) . '\' ORDER BY ordinal_position;', $sql);
  }elseif ($config['sqlserver'] == 'mysql') {
    $result = sql_query('SHOW COLUMNS FROM darwin_' . addslashes($table) . ';', $sql);
  }
  if ((!strlen($r = sql_last_error($sql))) && (sql_num_rows($result) > 0)) {
    while ($row = sql_fetch_row($result)) {
      $names[] = array($row[0], (!empty($row[1])?intval($row[1]):0), ((isset($row[2]) && ($row[2] == 'YES'))?false:true));
    }
    return $names;
  }
  return false;
}

$table = array('country', 'weather', 'sex', 'protocol', 'preservationmethod', 'lifestage', 'identificationqualifier', 'habitatcategory', 'disposition', 'density', 'datum', 'continentocean', 'conditionelement', 'basisofrecord');
$sql = sql_connect($config['db']);
if ($config['login'] && ($_SESSION['login']['right'] >= 7) && !empty($_GET['table']) && in_array($_GET['table'], $table) && (($names = get_names($_GET['table'], $sql)) !== false)) {
  if (!empty($_GET['edit'])) {
    $sql = sql_connect($config['db']);
    if (isset($_POST['remove']) && !empty($_POST['key']) && ($_POST['key'] == md5(strip_tags(trim(rawurldecode($_GET['edit']))) . floor(intval(date('b')))))) {
      $result = sql_query('DELETE FROM darwin_' . addslashes($_GET['table']) . ' WHERE ' . $names[0][0] . '=\'' . addslashes(stripslashes(strip_tags(rawurldecode($_GET['edit'])))) . '\' ;', $sql);
      if (!strlen($r = sql_last_error($sql))) {
        header('Location: ' . $config['server'] . $plugin['darwin']['url'] . '/table/' . rawurlencode(rawurldecode($_GET['table'])));
        exit;
      }else {
        $error = $r;
      }
    }elseif (isset($_POST['edit']) && !empty($_POST['key']) && ($_POST['key'] == md5(strip_tags(trim(rawurldecode($_GET['edit']))) . floor(intval(date('b'))))) && !empty($_POST[$names[0][0]])) {
      $ok = true;
      foreach($names as $value) {
        if (empty($_POST[$value[0]]) && !empty($value[2])) $ok = false;
        $values[] = $value[0] . '=' . (!empty($_POST[$value[0]])?'\'' . addslashes(stripslashes(strip_tags(trim($_POST[$value[0]])))) . '\'':'NULL');
      }
      if ($ok) {
        $result = sql_query('UPDATE darwin_' . addslashes($_GET['table']) . ' SET ' . implode(', ', $values) . ' WHERE ' . $names[0][0] . '=\'' . addslashes(stripslashes(strip_tags(rawurldecode($_GET['edit'])))) . '\' ;', $sql);
        if (!strlen($r = sql_last_error($sql))) {
          header('Location: ' . $config['server'] . $plugin['darwin']['url'] . '/table/' . rawurlencode(rawurldecode($_GET['table'])));
          exit;
        }
      }
    }
    head('darwin');
?>
      <div class="items">
        <h1><?php print $plugin['darwin']['name']; ?><small><?php print $plugin['darwin']['description']; ?></small></h1><br />
<?php
    $result = sql_query('SELECT * FROM darwin_' . addslashes($_GET['table']) . ' WHERE ' . $names[0][0] . '=\'' . addslashes(stripslashes(strip_tags(rawurldecode($_GET['edit'])))) . '\' ;', $sql);
    if (!strlen($r = sql_last_error($sql)) && (sql_num_rows($result) == 1)) {
      $row = sql_fetch_row($result);
?>
          <form method="post" action="<?php print $config['server'] . $plugin['darwin']['url'] . '/table/' . rawurlencode(rawurldecode($_GET['table'])) . '/edit/' . rawurlencode(rawurldecode($_GET['edit'])); ?>">
          <div>
            <h2><?php print $row[0]; ?></h2><br />
<?php print (isset($error)?'            <strong>' . $error . "</strong><br /><br />\n":''); ?>
<?php foreach($names as $key => $value) {
?>
             <div>
               <label for="<?php print $value[0]; ?>"><?php print (!empty($value[2])?'<strong>' . ucfirst($value[0]) . '</strong>':ucfirst($value[0])); ?></label>
               <input name="<?php print $value[0]; ?>" id="<?php print $value[0]; ?>" type="text"<?php print (!empty($value[1])?' maxlength="' . $value[1] . '"':'') . (!empty($_POST[$value[0]])?' value="' . stripslashes(strip_tags(trim($_POST[$value[0]]))) . '"':(!isset($_POST[$value[0]]) && isset($row[$key])?' value="' . $row[$key] . '"':'')) . ((!empty($value[1]) && ($value[1] <= 16))?' class="half"':'') ; ?> />
             <br />
           </div>
<?php }
?>
            <br />
            <input type="hidden" name="key" value="<?php print md5(strip_tags(trim(rawurldecode($_GET['edit']))) . floor(intval(date('b')))); ?>" />
            <input type="reset" value="<?php print _("Clear"); ?>" />&nbsp;<input type="submit"  name="edit" value="<?php print _("Edit"); ?>" />&nbsp;<input type="submit" name="remove" value="<?php print _("Remove"); ?>" onclick="return confirm('<?php print _("Are you sure you want to delete?"); ?>')"/>
          </div>
          </form>
          <br />
        </div>
<?php
    }
  }elseif (isset($_GET['add'])) {
    if (!empty($_POST['darwin']) && ($_POST['darwin'] == md5('add' . floor(intval(date('b'))))) && !empty($_POST[$names[0][0]])) {
      $ok = true;
      foreach($names as $value) {
        if (empty($_POST[$value[0]]) && !empty($value[2])) $ok = false;
        $values[] = (!empty($_POST[$value[0]])?'\'' . addslashes(stripslashes(strip_tags(trim($_POST[$value[0]])))) . '\'':'NULL');
      }
      if ($ok) {
        $result = sql_query('INSERT INTO darwin_' . addslashes($_GET['table']) . ' VALUES (' . implode(', ', $values) . ');', $sql);
        if (!strlen($r = sql_last_error($sql))) {
          header('Location: ' . $config['server'] . $plugin['darwin']['url'] . '/table/' . rawurlencode(rawurldecode($_GET['table'])));
          exit;
        }
      }
    }
    head('darwin');
?>
      <div class="items">
        <h1><?php print $plugin['darwin']['name']; ?><small><?php print $plugin['darwin']['description']; ?></small></h1><br />
        <form method="post" action="<?php print $config['server'] . $plugin['darwin']['url'] . '/table/' . rawurlencode(rawurldecode($_GET['table'])); ?>/add">
        <div>
          <h2><?php print _("New entry"); ?></h2><br /><br />
<?php foreach($names as $value) {
?>
          <div>
            <label for="<?php print $value[0]; ?>"><?php print (!empty($value[2])?'<strong>' . ucfirst($value[0]) . '</strong>':ucfirst($value[0])); ?></label>
            <input name="<?php print $value[0]; ?>" id="<?php print $value[0]; ?>" type="text"<?php print (!empty($value[1])?' maxlength="' . $value[1] . '"':'') . (!empty($_POST[$value[0]])?' value="' . stripslashes(strip_tags(trim($_POST[$value[0]]))) . '"':'') . ((!empty($value[1]) && ($value[1] <= 16))?' class="half"':'') ; ?> />
            <br />
          </div>
<?php }
?>
          <br />
          <input type="hidden" name="darwin" value="<?php print md5('add' . floor(intval(date('b')))); ?>" />
          <input type="reset" value="<?php print _("Clear"); ?>" />&nbsp;<input type="submit" value="<?php print _("Add"); ?>" />
        </div>
        </form>
        <br />
      </div>
<?php
  }else {
    head('darwin');
?>
      <div class="items">
        <h1><?php print $plugin['darwin']['name']; ?><small><?php print $plugin['darwin']['description']; ?></small></h1><br />
        <div><h2><?php print _("Add an entry"); ?><small><a href="<?php print $config['server'] . $plugin['darwin']['url'] . '/table/' . rawurlencode(rawurldecode($_GET['table'])); ?>/add" title="<?php print _("Add a new entry"); ?>"><?php print _("Add an entry..."); ?></a></small></h2><br /><?php print _("You may add a new entry in the table") . ' \'' . ucfirst($_GET['table']); ?>'.<br /></div>
        <div><h2><?php print _("Currently in the table"); ?></h2><br />
<?php
    $result = sql_query('SELECT ' . $names[0][0] . ',' . $names[1][0] . ' FROM darwin_' . addslashes($_GET['table']) . ' ORDER BY ' . $names[0][0] . ';', $sql);
    if ((!strlen($r = sql_last_error($sql))) && (sql_num_rows($result) > 0)) {
      while ($row = sql_fetch_row($result)) {
        print '          <div class="result"><span class="ref"><a href="' . $config['server'] . $plugin['darwin']['url'] . '/table/' . rawurlencode(rawurldecode($_GET['table'])) . '/edit/' . rawurlencode($row[0]) . '">' . $row[0] . '</a></span><span class="detail">' . $row[1] . "</span></div>\n";
      }
    }
?>
        </div>
      <br />
      </div>
<?php
  }
  foot();
}else {
  header('Location: ' . $config['server']);
}
?>