<?php
ob_start("ob_gzhandler");
session_start();
require_once('../includes/main.inc');

if ($config['login']) {
  if (($_SESSION['login']['right'] >= 3) && !empty($_GET['edit'])) {
    $sql = sql_connect($config['db']);
    if (isset($_POST['remove']) && !empty($_POST['key']) && ($_POST['key'] == md5(strip_tags(trim(rawurldecode($_GET['edit']))) . floor(intval(date('b')))))) {
      $result = sql_query('DELETE FROM darwin_collection WHERE collectioncode=\'' . addslashes(stripslashes(strip_tags(rawurldecode($_GET['edit'])))) . '\';', $sql);
      header('Location: ' . $config['server'] . $plugin['darwin']['url'] . '/collection');
      exit;
    }elseif (isset($_POST['edit']) && !empty($_POST['key']) && ($_POST['key'] == md5(strip_tags(trim(rawurldecode($_GET['edit']))) . floor(intval(date('b'))))) && !empty($_POST['collectioncode']) && (strlen(stripslashes(strip_tags(trim($_POST['collectioncode'])))) > 1) && !empty($_POST['name']) && (strlen(stripslashes(strip_tags(trim($_POST['name'])))) > 2) && !empty($_POST['curator']) && !fullyempty($_POST['description']) ) {
      $result = sql_query('UPDATE darwin_collection SET collectioncode=\'' . addslashes(stripslashes(strip_tags(trim($_POST['collectioncode'])))) . '\', name=\'' . addslashes(stripslashes(strip_tags(trim($_POST['name'])))) . '\', curator=\'' . addslashes(stripslashes(strip_tags(trim($_POST['curator'])))) . '\', description=\'' . addslashes(stripslashes(strip_tags(trim($_POST['description'])))) . '\', updated=NOW() WHERE collectioncode=\'' . addslashes(stripslashes(strip_tags(rawurldecode($_GET['edit'])))) . '\';', $sql);
      if (!strlen($r = sql_last_error($sql))) {
        header('Location: ' . $config['server'] . $plugin['darwin']['url'] . '/collection/' . rawurlencode(strip_tags(trim(rawurldecode($_GET['edit'])))));
        exit;
      }
    }
    head('darwin');
?>
      <div class="items">
        <h1><?php print $plugin['darwin']['name']; ?><small><?php print $plugin['darwin']['description']; ?></small></h1><br />
<?php
    $result = sql_query('SELECT collectioncode, name, curator, description FROM darwin_collection WHERE collectioncode=\'' . addslashes(stripslashes(strip_tags(rawurldecode($_GET['edit'])))) . '\' ;', $sql);
    if (!strlen($r = sql_last_error($sql)) && (sql_num_rows($result) == 1)) {
      $row = sql_fetch_row($result); ?>
        <form method="post" action="<?php print $config['server'] . $plugin['darwin']['url'] . '/collection/edit/' . rawurlencode(stripslashes(strip_tags($row[0]))); ?>">
        <div>
          <h2><?php print $row[1]; ?></h2><br />
			  <div>
			  <label for="collectioncode"><strong><?php print _("Collection Code"); ?></strong></label>
				  <input name="collectioncode" id="collectioncode" type="text" maxlength="6" title="<?php print _("The code (or acronym) identifying the collection within the institution in which the organism record is cataloged"); ?>"<?php print (!empty($_POST['collectioncode'])?' value="' . stripslashes(strip_tags(trim($_POST['collectioncode']))) . '"':((!isset($_POST['collectioncode']) && isset($row[0]))?' value="' . $row[0] . '"':'')); ?> class="half" />
				  <br />
				  </div>
				  <div>
				  <label for="name"><strong><?php print _("Collection Name"); ?></strong></label>
				  <input name="name" id="name" type="text" maxlength="128" title="<?php print _("The full, unabbreviated name of the collection"); ?>"<?php print (!empty($_POST['name'])?' value="' . stripslashes(strip_tags(trim($_POST['name']))) . '"':((!isset($_POST['name']) && isset($row[1]))?' value="' . $row[1] . '"':'')); ?> />
					  <br />
					  </div>
					  <div>
					  <label for="curator"><strong><?php print _("Curator"); ?></strong></label>
						  <select name="curator" id="curator" title="<?php print _("The name of the curator of this collection"); ?>"><option value=""></option><?php
						  $result = sql_query('SELECT username, real_name FROM darwin_users ORDER BY real_name;', $sql);
					  if ((!strlen($r = sql_last_error($sql))) && (sql_num_rows($result) > 0)) {
						  while ($row2 = sql_fetch_row($result)) {
							  print "<option value=\"$row2[0]\"" . ((isset($_POST['curator']) && ($row2[0] == $_POST['curator']))?' selected="selected"':((!isset($_POST['curator']) && isset($row[2]) && ($row2[0] == $row[2]))?' selected="selected"':'')) . ">$row2[1]</option>";
						  }
					  }
					  ?></select>
						  <br />
						  </div>
						  <div>
						  <label for="description"><strong><?php print _("Description"); ?></strong></label>
							  <textarea name="description" id="description" rows="4" cols="30" title="<?php print _("Brief description of the project / collection type"); ?>"><?php print ((isset($_POST['description']) && !fullyempty($_POST['description']))?stripslashes(strip_tags(trim($_POST['description']))):((!isset($_POST['description']) && isset($row[3]))?$row[3]:'')); ?></textarea>
								  <br />
								  </div>
          <br />
          <input type="hidden" name="key" value="<?php print md5(strip_tags(trim(rawurldecode($_GET['edit']))) . floor(intval(date('b')))); ?>" />
          <input type="reset" value="<?php print _("Clear"); ?>" />&nbsp;<input type="submit"  name="edit" value="<?php print _("Edit"); ?>" />&nbsp;<input type="submit" name="remove" value="<?php print _("Remove"); ?>" onclick="return confirm('<?php print _("Are you sure you want to delete?"); ?>')"/>
        </div>
        </form>
        <br />
      </div>
<?php
    }
  }elseif (($_SESSION['login']['right'] >= 2) && !empty($_GET['add'])) {
    $sql = sql_connect($config['db']);
    if (!empty($_POST['darwin']) && ($_POST['darwin'] == md5('add' . floor(intval(date('b'))))) && !empty($_POST['collectioncode']) && (strlen(stripslashes(strip_tags(trim($_POST['collectioncode'])))) > 1) && !empty($_POST['name']) && (strlen(stripslashes(strip_tags(trim($_POST['name'])))) > 2) && !empty($_POST['curator']) && !fullyempty($_POST['description']) ) {
      $result = sql_query('INSERT INTO darwin_collection (collectioncode, name, curator, description) VALUES (\'' . addslashes(stripslashes(strip_tags(trim($_POST['collectioncode'])))) . '\',\'' . addslashes(stripslashes(strip_tags(trim($_POST['name'])))) . '\',\'' . addslashes(stripslashes(strip_tags(trim($_POST['curator'])))) . '\',\'' . addslashes(stripslashes(strip_tags(trim($_POST['description'])))) . '\');', $sql);
      if (!strlen($r = sql_last_error($sql))) {
        header('Location: ' . $config['server'] . $plugin['darwin']['url'] . '/collection/' . rawurlencode(stripslashes(strip_tags(trim($_POST['collectioncode'])))));
        exit;
      }
    }
    head('darwin');
?>
      <div class="items">
        <h1><?php print $plugin['darwin']['name']; ?><small><?php print $plugin['darwin']['description']; ?></small></h1><br />
        <form method="post" action="<?php print $config['server'] . $plugin['darwin']['url']; ?>/collection/add">
        <div>
          <h2><?php print _("New collection"); ?><small><a href="<?php print $config['server'] . $plugin['darwin']['url']; ?>/help/collection" title="<?php print _("Help"); ?>"><?php print _("Help"); ?></a></small></h2><br /><?php print _("You can specify the collection name and a short abstract."); ?><br /><br />
          <div>
            <label for="collectioncode"><strong><?php print _("Collection Code"); ?></strong></label>
            <input name="collectioncode" id="collectioncode" type="text" maxlength="6" title="<?php print _("The code (or acronym) identifying the collection within the institution in which the organism record is cataloged"); ?>"<?php print (!empty($_POST['collectioncode'])?' value="' . stripslashes(strip_tags(trim($_POST['collectioncode']))) . '"':''); ?> class="half" />
            <br />
          </div>
          <div>
            <label for="name"><strong><?php print _("Collection Name"); ?></strong></label>
            <input name="name" id="name" type="text" maxlength="128" title="<?php print _("The full, unabbreviated name of the collection"); ?>"<?php print (!empty($_POST['name'])?' value="' . stripslashes(strip_tags(trim($_POST['name']))) . '"':''); ?> />
            <br />
          </div>
            <div>
              <label for="curator"><strong><?php print _("Curator"); ?></strong></label>
              <select name="curator" id="curator" title="<?php print _("The name of the curator of this collection"); ?>"><option value=""></option><?php
      $result = sql_query('SELECT username, real_name FROM darwin_users ORDER BY real_name;', $sql);
      if ((!strlen($r = sql_last_error($sql))) && (sql_num_rows($result) > 0)) {
        while ($row2 = sql_fetch_row($result)) {
          print "<option value=\"$row2[0]\"" . ((isset($_POST['curator']) && ($row2[0] == $_POST['curator']))?' selected="selected"':'') . ">$row2[1]</option>";
        }
      }
?></select>
                <br />
              </div>
          <div>
            <label for="description"><strong><?php print _("Description"); ?></strong></label>
            <textarea name="description" id="description" rows="4" cols="30" title="<?php print _("Brief description of the project / collection type"); ?>"><?php print ((isset($_POST['description']) && !fullyempty($_POST['description']))?stripslashes(strip_tags(trim($_POST['description']))):''); ?></textarea>
            <br />
          </div>
          <br />
          <input type="hidden" name="darwin" value="<?php print md5('add' . floor(intval(date('b')))); ?>" />
          <input type="reset" value="<?php print _("Clear"); ?>" />&nbsp;<input type="submit" value="<?php print _("Add"); ?>" />
        </div>
        </form>
        <br />
      </div>
<?php
  }elseif (!empty($_GET['collection'])) {
    head('darwin');
?>
      <div class="items">
        <h1><?php print $plugin['darwin']['name']; ?><small><?php print $plugin['darwin']['description']; ?></small></h1><br />
<?php
    $sql = sql_connect($config['db']);
    $result = sql_query('SELECT a.collectioncode, a.name, b.real_name, a.description, a.updated FROM darwin_collection AS a, darwin_users AS b WHERE b.username=a.curator AND a.collectioncode=\'' . addslashes(stripslashes(strip_tags(rawurldecode($_GET['collection'])))) . '\';', $sql);
    if ((!strlen($r = sql_last_error($sql))) && (sql_num_rows($result) == 1)) {
      $row = sql_fetch_row($result);
?>
        <div>
          <h2><?php print $row[1] . (($_SESSION['login']['right'] >= 3)?'<small><a href="' . $config['server'] . $plugin['darwin']['url'] . '/collection/edit/' . rawurlencode(stripslashes(strip_tags($row[0]))) . '" title="' . _("Edit") . '">' . _("edit") . '</a></small>':''); ?></h2>
<?php
      print '          <h3>' . ("Details") . "</h3>\n";
      print '          <div class="details"><div class="title">' . _("Collection Code") . '</div><div class="label">' . $row[0] . "</div></div>\n";
      print '          <div class="details"><div class="title">' . _("Collection Name") . '</div><div class="label">' . $row[1] . "</div></div>\n";
      print '          <div class="details"><div class="title">' . _("Curator") . '</div><div class="label">' . $row[2] . "</div></div>\n";
      print '          <div class="details"><div class="title">' . _("Update") . '</div><div class="label">' . gmdate(_("d-m-Y"), strtotime($row[4])) . "</div></div>\n";
      print '          <h3>' . _("Description") . "</h3>\n" . '          <div class="details">' . preg_replace('/\[([^\|\]]*)\|([^\|\]]*)\]/', '<a href="\1">\2</a><br />', htmlentities($row[3], ENT_COMPAT, 'ISO-8859-1')) . "</div>\n";
      print "        </div>\n";
    }
?>
        <br />
      </div>
<?php
  }else {
    head('darwin');
?>
      <div class="items">
        <h1><?php print $plugin['darwin']['name']; ?><small><?php print $plugin['darwin']['description']; ?></small></h1><br />
<?php if (($_SESSION['login']['right'] >= 2) && empty($_POST['search'])) {
      ?>
        <div><h2><?php print _("Add Collection"); ?><small><a href="<?php print $config['server'] . $plugin['darwin']['url']; ?>/collection/add" title="<?php print _("Add a new collection"); ?>"><?php print _("Add a collection..."); ?></a></small></h2><br /><?php print _("You may add a new collection before add a new collection entry."); ?><br /></div>
<?php }
?>
        <form method="post" action="<?php print $config['server'] . $plugin['darwin']['url']; ?>/collection/search">
        <div>
          <h2><?php print _("Search"); ?></h2><br /><?php print _("Retrive a collection. You may provide a reference, or a full name."); ?><br /><br />
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
      $result = sql_query('SELECT collectioncode, name, description FROM darwin_collection WHERE (collectioncode=\'' . addslashes($search) . '\' OR name' . sql_reg(addslashes($search)) . ' OR description' . sql_reg(addslashes($search)) . ');', $sql);
      if ((!strlen($r = sql_last_error($sql))) && (sql_num_rows($result) > 0)) {
        print "        <div>\n          <h2>" . _("Results (current collections)") . "</h2>\n";
        while ($row = sql_fetch_row($result)) {
          print '          <div class="result"><span class="ref"><a href="' . $config['server'] . $plugin['darwin']['url'] . '/collection/' . rawurlencode($row[0]) . '">' . $row[0] . '</a></span><span class="desc">' . $row[1] . '</span><span class="detail">' . substr($row[2],0,20) . "...</span></div>\n";
        }
        print "        </div>\n";
      }
    }
?>
        <br />
      </div>
<?php
  }
  foot();
}else {
  header('Location: ' . $config['server']);
}
?>