<?php
ob_start("ob_gzhandler");
session_start();
require_once('../includes/main.inc');

if ($config['login']) {
  if (($_SESSION['login']['right'] >= 3) && !empty($_GET['edit'])) {
    $sql = sql_connect($config['db']);
    if (isset($_POST['remove']) && !empty($_POST['key']) && ($_POST['key'] == md5(strip_tags(trim(rawurldecode($_GET['edit']))) . floor(intval(date('b')))))) {
      $result = sql_query('DELETE FROM darwin_institution WHERE institutioncode=\'' . addslashes(stripslashes(strip_tags(rawurldecode($_GET['edit'])))) . '\';', $sql);
      header('Location: ' . $config['server'] . $plugin['darwin']['url'] . '/institution');
      exit;
    }elseif (isset($_POST['edit']) && !empty($_POST['key']) && ($_POST['key'] == md5(strip_tags(trim(rawurldecode($_GET['edit']))) . floor(intval(date('b'))))) && !empty($_POST['institutioncode']) && (strlen(stripslashes(strip_tags(trim($_POST['institutioncode'])))) > 1) && !empty($_POST['name']) && (strlen(stripslashes(strip_tags(trim($_POST['name'])))) > 2) && !empty($_POST['contact']) && !fullyempty($_POST['address']) && !empty($_POST['country']) && (fullyempty($_POST['latitude']) || ((floatval($_POST['latitude']) >= -90) || (floatval($_POST['latitude']) <= 90)) ) && (fullyempty($_POST['longitude']) || ((floatval($_POST['longitude']) >= -180) || (floatval($_POST['longitude']) <= 180)) )) {
      $result = sql_query('UPDATE darwin_institution SET institutioncode=\'' . addslashes(stripslashes(strip_tags(trim($_POST['institutioncode'])))) . '\', name=\'' . addslashes(stripslashes(strip_tags(trim($_POST['name'])))) . '\', contact=\'' . addslashes(stripslashes(strip_tags(trim($_POST['contact'])))) . '\', address=\'' . addslashes(stripslashes(strip_tags(trim($_POST['address'])))) . '\', country=\'' . addslashes(stripslashes(strip_tags(trim($_POST['country'])))) . '\', latitude=' . ((isset($_POST['latitude']) && !fullyempty($_POST['latitude']))?floatval($_POST['latitude']):'NULL') . ', longitude=' . ((isset($_POST['longitude']) && !fullyempty($_POST['longitude']))?floatval($_POST['longitude']):'NULL') .', updated=NOW() WHERE institutioncode=\'' . addslashes(stripslashes(strip_tags(rawurldecode($_GET['edit'])))) . '\';', $sql);
      if (!strlen($r = sql_last_error($sql))) {
        header('Location: ' . $config['server'] . $plugin['darwin']['url'] . '/institution/' . rawurlencode(strip_tags(trim(rawurldecode($_GET['edit'])))));
        exit;
      }
    }
    head('darwin');
?>
      <div class="items">
        <h1><?php print $plugin['darwin']['name']; ?><small><?php print $plugin['darwin']['description']; ?></small></h1><br />
<?php
    $result = sql_query('SELECT institutioncode, name, contact, address, country, latitude, longitude FROM darwin_institution WHERE institutioncode=\'' . addslashes(stripslashes(strip_tags(rawurldecode($_GET['edit'])))) . '\' ;', $sql);
    if (!strlen($r = sql_last_error($sql)) && (sql_num_rows($result) == 1)) {
      $row = sql_fetch_row($result); ?>
        <form method="post" action="<?php print $config['server'] . $plugin['darwin']['url'] . '/institution/edit/' . rawurlencode(stripslashes(strip_tags($row[0]))); ?>">
        <div>
          <h2><?php print $row[1]; ?></h2><br />
			  <div>
			  <label for="institutioncode"><strong><?php print _("Institution Code"); ?></strong></label>
				  <input name="institutioncode" id="institutioncode" type="text" maxlength="6" title="<?php print _("The code (or acronym) identifying the institution administering the collection in which the organism record is cataloged"); ?>"<?php print (!empty($_POST['institutioncode'])?' value="' . stripslashes(strip_tags(trim($_POST['institutioncode']))) . '"':((!isset($_POST['institutioncode']) && isset($row[0]))?' value="' . $row[0] . '"':'')); ?> class="half" />
				  <br />
				  </div>
				  <div>
				  <label for="name"><strong><?php print _("Institution Name"); ?></strong></label>
				  <input name="name" id="name" type="text" maxlength="128" title="<?php print _("The full, unabbreviated name of the Institutions"); ?>"<?php print (!empty($_POST['name'])?' value="' . stripslashes(strip_tags(trim($_POST['name']))) . '"':((!isset($_POST['name']) && isset($row[1]))?' value="' . $row[1] . '"':'')); ?> />
					  <br />
					  </div>
					  <div>
					  <label for="country"><strong><?php print _("Country"); ?></strong></label>
						  <select name="country" id="country" title="<?php print _("The full, unabbreviated name of the country or major political unit"); ?>"><option value=""></option><?php
						  $result = sql_query('SELECT iana, name FROM darwin_country ORDER BY name;', $sql);
					  if ((!strlen($r = sql_last_error($sql))) && (sql_num_rows($result) > 0)) {
						  while ($row2 = sql_fetch_row($result)) {
							  print "<option value=\"$row2[0]\"" . (!empty($_POST['country'])?(($row2[0] == $_POST['country'])?' selected="selected"':''):((!isset($_POST['country']) && isset($row[4]) && ($row2[0] == $row[4]))?' selected="selected"':'')) . ">$row2[1]</option>";
						  }
					  }
					  ?></select>
						  <br />
						  </div>
						  <div>
						  <label for="latitude"><?php print _("Latitude"); ?></label>
							  <input name="latitude" id="latitude" type="text" maxlength="16" title="<?php print _("The latitude of Institution, expressed in fractional degrees (e.g. 53.308231)"); ?>"<?php print ((isset($_POST['latitude']) && !fullyempty($_POST['latitude']))?' value="' . floatval($_POST['latitude']) . '"':((!isset($_POST['latitude']) && isset($row[5]))?' value="' . $row[5] . '"':'')); ?> class="half" />
							  <br />
							  </div>
							  <div>
							  <label for="longitude"><?php print _("Longitude"); ?></label>
							  <input name="longitude" id="longitude" type="text" maxlength="16" title="<?php print _("The longitude of Institution, expressed in fractional degrees (e.g. -6.225488)"); ?>"<?php print ((isset($_POST['longitude']) && !fullyempty($_POST['longitude']))?' value="' . floatval($_POST['longitude']) . '"':((!isset($_POST['longitude']) && isset($row[6]))?' value="' . $row[6] . '"':'')); ?> class="half" />
								  <br />
								  </div>
								  <div>
								  <label for="contact"><strong><?php print _("Contact"); ?></strong></label>
									  <select name="contact" id="contact" title="<?php print _("The name of the contact in this Institution"); ?>"><option value=""></option><?php
									  $result = sql_query('SELECT username, real_name FROM darwin_users ORDER BY real_name;', $sql);
								  if ((!strlen($r = sql_last_error($sql))) && (sql_num_rows($result) > 0)) {
									  while ($row2 = sql_fetch_row($result)) {
										  print "<option value=\"$row2[0]\"" . ((isset($_POST['contact']) && ($row2[0] == $_POST['contact']))?' selected="selected"':((!isset($_POST['curator']) && isset($row[2]) && ($row2[0] == $row[2]))?' selected="selected"':'')) . ">$row2[1]</option>";
									  }
								  }
								  ?></select>
									  <br />
									  </div>
									  <div>
									  <label for="address"><strong><?php print _("Address"); ?></strong></label>
										  <textarea name="address" id="address" rows="4" cols="30" title="<?php print _("The full address of the Institution"); ?>"><?php print ((isset($_POST['address']) && !fullyempty($_POST['address']))?stripslashes(strip_tags(trim($_POST['address']))):((!isset($_POST['address']) && isset($row[3]))?$row[3]:'')); ?></textarea>
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
    if (!empty($_POST['darwin']) && ($_POST['darwin'] == md5('add' . floor(intval(date('b'))))) && !empty($_POST['institutioncode']) && (strlen(stripslashes(strip_tags(trim($_POST['institutioncode'])))) > 1) && !empty($_POST['name']) && (strlen(stripslashes(strip_tags(trim($_POST['name'])))) > 2) && !empty($_POST['contact']) && !fullyempty($_POST['address']) && !empty($_POST['country']) && (fullyempty($_POST['latitude']) || ((floatval($_POST['latitude']) >= -90) || (floatval($_POST['latitude']) <= 90)) ) && (fullyempty($_POST['longitude']) || ((floatval($_POST['longitude']) >= -180) || (floatval($_POST['longitude']) <= 180)) )) {
      $result = sql_query('INSERT INTO darwin_institution (institutioncode, name, contact, address, country, latitude, longitude) VALUES (\'' . addslashes(stripslashes(strip_tags(trim($_POST['institutioncode'])))) . '\',\'' . addslashes(stripslashes(strip_tags(trim($_POST['name'])))) . '\',\'' . addslashes(stripslashes(strip_tags(trim($_POST['contact'])))) . '\',\'' . addslashes(stripslashes(strip_tags(trim($_POST['address'])))) . '\',\'' . addslashes(stripslashes(strip_tags(trim($_POST['country'])))) . '\',' . ((isset($_POST['latitude']) && !fullyempty($_POST['latitude']))?floatval($_POST['latitude']):'NULL') . ',' . ((isset($_POST['longitude']) && !fullyempty($_POST['longitude']))?floatval($_POST['longitude']):'NULL') . ');', $sql);
      if (!strlen($r = sql_last_error($sql))) {
        header('Location: ' . $config['server'] . $plugin['darwin']['url'] . '/institution/' . rawurlencode(stripslashes(strip_tags(trim($_POST['institutioncode'])))));
        exit;
      }
    }
    head('darwin');
?>
      <div class="items">
        <h1><?php print $plugin['darwin']['name']; ?><small><?php print $plugin['darwin']['description']; ?></small></h1><br />
        <form method="post" action="<?php print $config['server'] . $plugin['darwin']['url']; ?>/institution/add">
        <div>
          <h2><?php print _("New institution"); ?><small><a href="<?php print $config['server'] . $plugin['darwin']['url']; ?>/help/institution" title="<?php print _("Help"); ?>"><?php print _("Help"); ?></a></small></h2><br /><?php print _("You can specify the institution name and the full address."); ?><br /><br />
          <div>
            <label for="institutioncode"><strong><?php print _("Institution Code"); ?></strong></label>
            <input name="institutioncode" id="institutioncode" type="text" maxlength="6" title="<?php print _("The code (or acronym) identifying the institution administering the collection in which the organism record is cataloged"); ?>"<?php print (!empty($_POST['institutioncode'])?' value="' . stripslashes(strip_tags(trim($_POST['institutioncode']))) . '"':''); ?> class="half" />
            <br />
          </div>
          <div>
            <label for="name"><strong><?php print _("Institution Name"); ?></strong></label>
            <input name="name" id="name" type="text" maxlength="128" title="<?php print _("The full, unabbreviated name of the Institutions"); ?>"<?php print (!empty($_POST['name'])?' value="' . stripslashes(strip_tags(trim($_POST['name']))) . '"':''); ?> />
            <br />
          </div>
          <div>
            <label for="country"><strong><?php print _("Country"); ?></strong></label>
            <select name="country" id="country" title="<?php print _("The full, unabbreviated name of the country or major political unit"); ?>"><option value=""></option><?php
      $result = sql_query('SELECT iana, name FROM darwin_country ORDER BY name;', $sql);
      if ((!strlen($r = sql_last_error($sql))) && (sql_num_rows($result) > 0)) {
        while ($row2 = sql_fetch_row($result)) {
          print "<option value=\"$row2[0]\"" . (!empty($_POST['country'])?(($row2[0] == $_POST['country'])?' selected="selected"':''):'') . ">$row2[1]</option>";
        }
      }
?></select>
            <br />
          </div>
           <div>
            <label for="latitude"><?php print _("Latitude"); ?></label>
            <input name="latitude" id="latitude" type="text" maxlength="16" title="<?php print _("The latitude of Institution, expressed in fractional degrees (e.g. 53.308231)"); ?>"<?php print ((isset($_POST['latitude']) && !fullyempty($_POST['latitude']))?' value="' . floatval($_POST['latitude']) . '"':''); ?> class="half" />
            <br />
          </div>
          <div>
            <label for="longitude"><?php print _("Longitude"); ?></label>
            <input name="longitude" id="longitude" type="text" maxlength="16" title="<?php print _("The longitude of Institution, expressed in fractional degrees (e.g. -6.225488)"); ?>"<?php print ((isset($_POST['longitude']) && !fullyempty($_POST['longitude']))?' value="' . floatval($_POST['longitude']) . '"':''); ?> class="half" />
            <br />
          </div>
            <div>
              <label for="contact"><strong><?php print _("Contact"); ?></strong></label>
              <select name="contact" id="contact" title="<?php print _("The name of the contact in this Institution"); ?>"><option value=""></option><?php
      $result = sql_query('SELECT username, real_name FROM darwin_users ORDER BY real_name;', $sql);
      if ((!strlen($r = sql_last_error($sql))) && (sql_num_rows($result) > 0)) {
        while ($row2 = sql_fetch_row($result)) {
          print "<option value=\"$row2[0]\"" . ((isset($_POST['contact']) && ($row2[0] == $_POST['contact']))?' selected="selected"':'') . ">$row2[1]</option>";
        }
      }
?></select>
                <br />
              </div>
          <div>
            <label for="address"><strong><?php print _("Address"); ?></strong></label>
            <textarea name="address" id="address" rows="4" cols="30" title="<?php print _("The full address of the Institution"); ?>"><?php print ((isset($_POST['address']) && !fullyempty($_POST['address']))?stripslashes(strip_tags(trim($_POST['address']))):''); ?></textarea>
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
  }elseif (!empty($_GET['institution'])) {
    head('darwin', false, true);
?>
      <div class="items">
        <h1><?php print $plugin['darwin']['name']; ?><small><?php print $plugin['darwin']['description']; ?></small></h1><br />
<?php
    $sql = sql_connect($config['db']);
    $result = sql_query('SELECT a.institutioncode, a.name, b.real_name, a.address, a.updated, c.name, a.latitude, a.longitude FROM darwin_institution AS a, darwin_users AS b, darwin_country AS c WHERE c.iana=a.country AND b.username=a.contact AND a.institutioncode=\'' . addslashes(stripslashes(strip_tags(rawurldecode($_GET['institution'])))) . '\';', $sql);
    if ((!strlen($r = sql_last_error($sql))) && (sql_num_rows($result) == 1)) {
      $row = sql_fetch_row($result);
?>
        <div>
          <h2><?php print $row[1] . (($_SESSION['login']['right'] >= 3)?'<small><a href="' . $config['server'] . $plugin['darwin']['url'] . '/institution/edit/' . rawurlencode(stripslashes(strip_tags($row[0]))) . '" title="' . _("Edit") . '">' . _("edit") . '</a></small>':''); ?></h2>
<?php if (!empty($row[6]) && !empty($row[7])) { ?>
          <div id="map" style="width: 300px; height: 300px"></div><script type="text/javascript">
//<![CDATA[
 load(<?php print $row[6] . ',' . $row[7] ; ?>);
//]]>
</script>
<?php
      }
      print '          <h3>' . ("Details") . "</h3>\n";
      print '          <div class="details"><div class="title">' . _("Institution Code") . '</div><div class="label">' . $row[0] . "</div></div>\n";
      print '          <div class="details"><div class="title">' . _("Institution Name") . '</div><div class="label">' . $row[1] . "</div></div>\n";
      print '          <div class="details"><div class="title">' . _("Contact") . '</div><div class="label">' . $row[2] . "</div></div>\n";
      print '          <div class="details"><div class="title">' . _("Update") . '</div><div class="label">' . gmdate(_("d-m-Y"), strtotime($row[4])) . "</div></div>\n";
      print '          <h3>' . _("Address") . "</h3>\n";
      if (!empty($row[6])) print '          <div class="details"><div class="title">' . _("Latitude") . '</div><div class="label">' . (($row[6] < 0)?'S':'N') . abs($row[6]) . "</div></div>\n";
      if (!empty($row[7])) print '          <div class="details"><div class="title">' . _("Longitude") . '</div><div class="label">' . (($row[7] < 0)?'W':'E') . abs($row[7]) . "</div></div>\n";
      print '          <div class="details"><div class="title">' . _("Country") . '</div><div class="label">' . $row[5] . "</div></div>\n";
      print '          <div class="details">' . preg_replace('/\[([^\|\]]*)\|([^\|\]]*)\]/', '<a href="\1">\2</a><br />', htmlentities($row[3], ENT_COMPAT, 'ISO-8859-1')) . "</div>\n";
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
        <div><h2><?php print _("Add Institution"); ?><small><a href="<?php print $config['server'] . $plugin['darwin']['url']; ?>/institution/add" title="<?php print _("Add a new institution"); ?>"><?php print _("Add an institution..."); ?></a></small></h2><br /><?php print _("You may add a new institution before add a new collection entry."); ?><br /></div>
<?php }
?>
        <form method="post" action="<?php print $config['server'] . $plugin['darwin']['url']; ?>/institution/search">
        <div>
          <h2><?php print _("Search"); ?></h2><br /><?php print _("Retrive an institution. You may provide a reference, or a full name."); ?><br /><br />
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
      $result = sql_query('SELECT a.institutioncode, a.name, b.name FROM darwin_institution AS a, darwin_country AS b WHERE (a.institutioncode=\'' . addslashes($search) . '\' OR a.name' . sql_reg(addslashes($search)) .' OR b.name' . sql_reg(addslashes($search)) . ') AND a.country=b.iana;', $sql);
      if ((!strlen($r = sql_last_error($sql))) && (sql_num_rows($result) > 0)) {
        print "        <div>\n          <h2>" . _("Results (current institutions)") . "</h2>\n";
        while ($row = sql_fetch_row($result)) {
          print '          <div class="result"><span class="ref"><a href="' . $config['server'] . $plugin['darwin']['url'] . '/institution/' . rawurlencode($row[0]) . '">' . $row[0] . '</a></span><span class="desc">' . $row[1] . '</span><span class="detail">' . $row[2] . "</span></div>\n";
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