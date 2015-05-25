<?php
ob_start("ob_gzhandler");
session_start();
require_once('../includes/main.inc');

if ($config['login']) {
  if (($_SESSION['login']['right'] >= 3) && !empty($_GET['edit'])) {
    $sql = sql_connect($config['db']);
    if (isset($_POST['remove']) && !empty($_POST['key']) && ($_POST['key'] == md5(strip_tags(trim(rawurldecode($_GET['edit']))) . floor(intval(date('b')))))) {
      $result = sql_query('DELETE FROM darwin_users WHERE username=\'' . addslashes(stripslashes(strip_tags(rawurldecode($_GET['edit'])))) . '\';', $sql);
      header('Location: ' . $config['server'] . $plugin['darwin']['url'] . '/author');
      exit;
    }elseif (isset($_POST['edit']) && !empty($_POST['key']) && ($_POST['key'] == md5(strip_tags(trim(rawurldecode($_GET['edit']))) . floor(intval(date('b'))))) && !empty($_POST['realname']) && (strlen(stripslashes(strip_tags(trim($_POST['realname'])))) > 5) && !empty($_POST['email']) && (strlen(strip_tags(trim($_POST['email']))) > 6)) {
      $result = sql_query('UPDATE darwin_users SET real_name=\'' . addslashes(stripslashes(strip_tags(trim($_POST['realname'])))) . '\', email=\'' . addslashes(stripslashes(strip_tags(trim($_POST['email'])))) . '\', address=' . ((isset($_POST['address']) && !fullyempty($_POST['address']))?'\'' . addslashes(stripslashes(strip_tags(trim($_POST['address'])))) . '\'':'NULL') . ', institution=' . (!empty($_POST['institution'])?'\'' . addslashes(stripslashes(strip_tags(trim($_POST['institution'])))) . '\'':'NULL') . ', updated=NOW() WHERE username=\'' . addslashes(stripslashes(strip_tags(rawurldecode($_GET['edit'])))) . '\';', $sql);
      if (!strlen($r = sql_last_error($sql))) {
        header('Location: ' . $config['server'] . $plugin['darwin']['url'] . '/author/' . rawurlencode(strip_tags(trim(rawurldecode($_GET['edit'])))));
        exit;
      }
    }
    head('darwin');
?>
      <div class="items">
        <h1><?php print $plugin['darwin']['name']; ?><small><?php print $plugin['darwin']['description']; ?></small></h1><br />
<?php
    $result = sql_query('SELECT username, real_name, email, institution, address FROM darwin_users WHERE username=\'' . addslashes(stripslashes(strip_tags(rawurldecode($_GET['edit'])))) . '\' ;', $sql);
    if (!strlen($r = sql_last_error($sql)) && (sql_num_rows($result) == 1)) {
      $row = sql_fetch_row($result); ?>
        <form method="post" action="<?php print $config['server'] . $plugin['darwin']['url'] . '/author/edit/' . rawurlencode(stripslashes(strip_tags($row[0]))); ?>">
        <div>
          <h2><?php print $row[1]; ?></h2><br />
          <div>
            <label for="realname"><strong><?php print _("Full Name"); ?></strong></label>
            <input name="realname" id="realname" type="text" maxlength="128" title="<?php print _("The full, unabbreviated name"); ?>"<?php print (!empty($_POST['realname'])?' value="' . stripslashes(strip_tags(trim($_POST['realname']))) . '"':((!isset($_POST['realname']) && isset($row[1]))?' value="' . $row[1] . '"':'')); ?> />
            <br />
          </div>
          <div>
            <label for="email"><strong><?php print _("Email/Mobile"); ?></strong></label>
            <input name="email" id="email" type="text" maxlength="128" title="<?php print _("E-mail address or Mobile number for further contact"); ?>"<?php print (!empty($_POST['email'])?' value="' . stripslashes(strip_tags(trim($_POST['email']))) . '"':((!isset($_POST['email']) && isset($row[2]))?' value="' . $row[2] . '"':'')); ?> />
            <br />
          </div>
          <div>
            <label for="institution"><?php print _("Institution"); ?></label>
            <select name="institution" id="institution" title="<?php print _("The full, unabbreviated name of the contact"); ?>"><option value=""></option><?php
      $result = sql_query('SELECT institutioncode, name FROM darwin_institution ORDER BY name;', $sql);
      if ((!strlen($r = sql_last_error($sql))) && (sql_num_rows($result) > 0)) {
        while ($row2 = sql_fetch_row($result)) {
          print "<option value=\"$row2[0]\"" . (!empty($_POST['institution'])?(($row2[0] == $_POST['institution'])?' selected="selected"':''):((!isset($_POST['institution']) && isset($row[3]) && ($row2[0] == $row[3]))?' selected="selected"':'')) . ">$row2[1]</option>";
        }
      }
?></select>
            <br />
          </div>
          <div>
            <label for="address"><?php print _("Address"); ?></label>
            <textarea name="address" id="address" rows="4" cols="30" title="<?php print _("The full address of the user"); ?>"><?php print ((isset($_POST['address']) && !fullyempty($_POST['address']))?stripslashes(strip_tags(trim($_POST['address']))):((!isset($_POST['address']) && isset($row[4]))? $row[4]:'')); ?></textarea>
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
    if (!empty($_POST['darwin']) && ($_POST['darwin'] == md5('add' . floor(intval(date('b'))))) && !empty($_POST['username']) && (strlen(stripslashes(strip_tags(trim($_POST['username'])))) > 2) && !empty($_POST['realname']) && (strlen(stripslashes(strip_tags(trim($_POST['realname'])))) > 5) && !empty($_POST['email']) && (strlen(strip_tags(trim($_POST['email']))) > 6)) {
      $result = sql_query('INSERT INTO darwin_users (username, real_name, email, address, institution) VALUES (\'' . addslashes(strtolower(stripslashes(strip_tags(trim($_POST['username']))))) . '\',\'' . addslashes(stripslashes(strip_tags(trim($_POST['realname'])))) . '\',\'' . addslashes(stripslashes(strip_tags(trim($_POST['email'])))) . '\',' . ((isset($_POST['address']) && !fullyempty($_POST['address']))?'\'' . addslashes(stripslashes(strip_tags(trim($_POST['address'])))) . '\'':'NULL') . ',' . (!empty($_POST['institution'])?'\'' . addslashes(stripslashes(strip_tags(trim($_POST['institution'])))) . '\'':'NULL') . ');', $sql);
      if (!strlen($r = sql_last_error($sql))) {
        header('Location: ' . $config['server'] . $plugin['darwin']['url'] . '/author/' . rawurlencode(strtolower(stripslashes(strip_tags(trim($_POST['username']))))));
        exit;
      }
    }
    head('darwin');
?>
      <div class="items">
        <h1><?php print $plugin['darwin']['name']; ?><small><?php print $plugin['darwin']['description']; ?></small></h1><br />
        <form method="post" action="<?php print $config['server'] . $plugin['darwin']['url']; ?>/author/add">
        <div>
          <h2><?php print _("New author"); ?><small><a href="<?php print $config['server'] . $plugin['darwin']['url']; ?>/help/author" title="<?php print _("Help"); ?>"><?php print _("Help"); ?></a></small></h2><br /><?php print _("You can specify the author name and the full address."); ?><br /><br />
          <div>
            <label for="username"><strong><?php print _("Username"); ?></strong></label>
            <input name="username" id="username" type="text" maxlength="32" title="<?php print _("Acronym identifying the user"); ?>"<?php print (!empty($_POST['username'])?' value="' . stripslashes(strip_tags(trim($_POST['username']))) . '"':''); ?> />
            <br />
          </div>
          <div>
            <label for="realname"><strong><?php print _("Full Name"); ?></strong></label>
            <input name="realname" id="realname" type="text" maxlength="128" title="<?php print _("The full, unabbreviated name"); ?>"<?php print (!empty($_POST['realname'])?' value="' . stripslashes(strip_tags(trim($_POST['realname']))) . '"':''); ?> />
            <br />
          </div>
          <div>
            <label for="email"><strong><?php print _("Email/Mobile"); ?></strong></label>
            <input name="email" id="email" type="text" maxlength="128" title="<?php print _("E-mail address or Mobile number for further contact"); ?>"<?php print (!empty($_POST['email'])?' value="' . stripslashes(strip_tags(trim($_POST['email']))) . '"':''); ?> />
            <br />
          </div>
          <div>
            <label for="institution"><?php print _("Institution"); ?></label>
            <select name="institution" id="institution" title="<?php print _("The full, unabbreviated name of the contact"); ?>"><option value=""></option><?php
      $result = sql_query('SELECT institutioncode, name FROM darwin_institution ORDER BY name;', $sql);
      if ((!strlen($r = sql_last_error($sql))) && (sql_num_rows($result) > 0)) {
        while ($row2 = sql_fetch_row($result)) {
          print "<option value=\"$row2[0]\"" . (!empty($_POST['institution'])?(($row2[0] == $_POST['institution'])?' selected="selected"':''):'') . ">$row2[1]</option>";
        }
      }
?></select>
            <br />
          </div>
          <div>
            <label for="address"><?php print _("Address"); ?></label>
            <textarea name="address" id="address" rows="4" cols="30" title="<?php print _("The full address of the user"); ?>"><?php print ((isset($_POST['address']) && !fullyempty($_POST['address']))?stripslashes(strip_tags(trim($_POST['address']))):''); ?></textarea>
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
  }elseif (!empty($_GET['author'])) {
    head('darwin');
?>
      <div class="items">
        <h1><?php print $plugin['darwin']['name']; ?><small><?php print $plugin['darwin']['description']; ?></small></h1><br />
<?php
    $sql = sql_connect($config['db']);
    $result = sql_query('SELECT username, real_name, email, institution, address, updated FROM darwin_users WHERE username=\'' . addslashes(stripslashes(strip_tags(rawurldecode($_GET['author'])))) . '\';', $sql);
    if ((!strlen($r = sql_last_error($sql))) && (sql_num_rows($result) == 1)) {
      $row = sql_fetch_row($result);
?>
        <div>
          <h2><?php print $row[1] . (($_SESSION['login']['right'] >= 3)?'<small><a href="' . $config['server'] . $plugin['darwin']['url'] . '/author/edit/' . rawurlencode(stripslashes(strip_tags($row[0]))) . '" title="' . _("Edit") . '">' . _("edit") . '</a></small>':''); ?></h2>
<?php
      print '          <h3>' . ("Details") . "</h3>\n";
      print '          <div class="details"><div class="title">' . _("Name") . '</div><div class="label">' . $row[1] . "</div></div>\n";
      print '          <div class="details"><div class="title">' . _("Email/Mobile") . '</div><div class="label">' . $row[2] . "</div></div>\n";
      if (isset($row[3])) {
        $result = sql_query('SELECT name FROM darwin_institution WHERE institutioncode=\'' . addslashes($row[3]) . '\';', $sql);
        if ((!strlen($r = sql_last_error($sql))) && (sql_num_rows($result) == 1)) {
          $row2 = sql_fetch_row($result);
          print '          <div class="details"><div class="title">' . _("Institution") . '</div><div class="label">' . $row2[0] . "</div></div>\n";
        }
      }
      print '          <div class="details"><div class="title">' . _("Update") . '</div><div class="label">' . gmdate(_("d-m-Y"), strtotime($row[5])) . "</div></div>\n";
      print '          <h3>' . _("Address") . "</h3>\n";
      print '          <div class="details">' . preg_replace('/\[([^\|\]]*)\|([^\|\]]*)\]/', '<a href="\1">\2</a><br />', htmlentities($row[4], ENT_COMPAT, 'ISO-8859-1')) . "</div>\n";
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
        <div><h2><?php print _("Add Author"); ?><small><a href="<?php print $config['server'] . $plugin['darwin']['url']; ?>/author/add" title="<?php print _("Add a new author/collector"); ?>"><?php print _("Add an author..."); ?></a></small></h2><br /><?php print _("You may add a new author or collector before add a new collection entry."); ?><br /></div>
<?php }
?>
        <form method="post" action="<?php print $config['server'] . $plugin['darwin']['url']; ?>/author/search">
        <div>
          <h2><?php print _("Search"); ?></h2><br /><?php print _("Retrive an author. You may provide a reference, or a full name."); ?><br /><br />
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
      $result = sql_query('SELECT username, real_name, email FROM darwin_users WHERE (username' . sql_reg(addslashes($search)) .' OR real_name' . sql_reg(addslashes($search)) .' OR email' . sql_reg(addslashes($search)) .');', $sql);
      if ((!strlen($r = sql_last_error($sql))) && (sql_num_rows($result) > 0)) {
        print "        <div>\n          <h2>" . _("Results (current authors)") . "</h2>\n";
        while ($row = sql_fetch_row($result)) {
          print '          <div class="result"><span class="ref"><a href="' . $config['server'] . $plugin['darwin']['url'] . '/author/' . rawurlencode($row[0]) . '">' . $row[0] . '</a></span><span class="desc">' . $row[1] . '</span><span class="detail">' . $row[2] . "</span></div>\n";
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