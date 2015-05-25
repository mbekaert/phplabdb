<?php
ob_start("ob_gzhandler");
session_start();
require_once('includes/main.inc');

if ($config['login']) {
  $sql = sql_connect($config['db']);
  if (!empty($_POST['passwd']) && ($_POST['passwd'] == md5($_SESSION['login']['username'] . floor(intval(date('b'))))) && !empty($_POST['passwordold']) && strlen(strip_tags(trim($_POST['passwordold']))) > 6 && !empty($_POST['password']) && strlen(strip_tags(trim($_POST['password']))) > 6 && !empty($_POST['password2']) && md5(strip_tags(trim($_POST['password']))) == md5(strip_tags(trim($_POST['password2']))) && strip_tags(trim($_POST['password'])) != strip_tags(trim($_POST['username']))) {
    $result = sql_query('UPDATE users SET password=\'' . md5(strip_tags(trim($_POST['password']))) . '\' WHERE username=\'' . addslashes($_SESSION['login']['username']) . '\' AND password=\'' . md5(strip_tags(trim($_POST['passwordold']))) . '\';', $sql);
    if (!strlen($r = sql_last_error($sql))) {
      header('Location: ' . $config['server'] . '/my');
      exit;
    }else {
      $error = 'Invalid password!';
    }
  }elseif (isset($_POST['remove']) && !empty($_POST['key']) && ($_SESSION['login']['right'] != 9) && ($_POST['key'] == md5($_SESSION['login']['username'] . floor(intval(date('b')))))) {
    $result = sql_query('UPDATE users SET rights=7, code=\'?\', activated=0, active=NULL WHERE username=\'' . addslashes($_SESSION['login']['username']) . '\';', $sql);
    header('Location: ' . $config['server'] . '/logout');
    exit;
  }elseif (isset($_POST['edit']) && !empty($_POST['key']) && ($_POST['key'] == md5($_SESSION['login']['username'] . floor(intval(date('b'))))) && !empty($_POST['name']) && strlen(strip_tags(trim($_POST['name']))) > 5 && !empty($_POST['email']) && strlen(strip_tags(trim($_POST['email']))) > 6 && stristr(strip_tags(trim($_POST['email'])), '@') && stristr(strip_tags(trim($_POST['email'])), '.') && !empty($_POST['organism']) && strlen(strip_tags(trim($_POST['organism']))) > 2) {
    $result = sql_query('UPDATE users SET real_name=\'' . addslashes(strip_tags(trim($_POST['name']))) . '\', email=\'' . addslashes(strip_tags(trim($_POST['email']))) . '\', taxon=\'' . addslashes(strip_tags(trim($_POST['organism']))) . '\' WHERE username=\'' . addslashes($_SESSION['login']['username']) . '\';', $sql);
    if (!strlen($r = sql_last_error($sql))) {
      header('Location: ' . $config['server'] . '/my');
      exit;
    }else {
      $error = 'SQL error';
    }
  }
  head('my');
?>
        <div class="items">
          <h1><?php print $_SESSION['login']['username']; ?><small><?php print _("Your profile"); ?></small></h1><br />
<?php
  $result = sql_query('SELECT username, real_name, email, taxon FROM users WHERE (username=\'' . addslashes($_SESSION['login']['username']) . '\');', $sql);
  if (!strlen($r = sql_last_error($sql)) && (sql_num_rows($result) == 1)) {
    $row = sql_fetch_row($result); ?>
          <form method="post" action="<?php print $config['server']; ?>/profile">
          <div>
            <h2><?php print $_SESSION['login']['username']; ?></h2>
            <?php print _("You can edit your current profile (including your name, email and password) if order to keep it up-to-date. You can also completely remove you profile and 'unregister' yourself."); ?><br /><br />
<?php print (isset($error)?"            <strong>" . $error . "</strong><br /><br />\n":''); ?>
            <div>
              <label for="name"><strong><?php print _("Real name"); ?></strong></label>
              <input name="name" id="name" type="text" maxlength="128"<?php print (!empty($_POST['name'])?' value="' . strip_tags(trim($_POST['name'])) . '"':(isset($row[1])?' value="' . $row[1] . '"':'')); ?> title="<?php print _("User real name"); ?>" />
              <br />
            </div>
            <div>
              <label for="email"><strong><?php print _("E-mail"); ?></strong></label>
              <input name="email" id="email" type="text" maxlength="128"<?php print (!empty($_POST['email'])?' value="' . strip_tags(trim($_POST['email'])) . '"':(isset($row[2])?' value="' . $row[2] . '"':'')); ?> title="<?php print _("Email address"); ?>" />
              <br />
            </div>
            <div>
              <label for="organism"><strong><?php print _("Taxon"); ?></strong></label>
              <input name="organism" id="organism" type="text" maxlength="128"<?php print (!empty($_POST['organism'])?' value="' . strip_tags(trim($_POST['organism'])) . '"':(isset($row[3])?' value="' . $row[3] . '"':'')); ?> title="<?php print _("Family or specie names"); ?>" />
              <br />
            </div>
            <br />
            <input type="hidden" name="key" value="<?php print md5($_SESSION['login']['username'] . floor(intval(date('b')))); ?>" />
            <input type="reset" value="<?php print _("Clear"); ?>" />&nbsp;<input type="submit" name="edit" value="<?php print _("Edit"); ?>" /><?php print (($_SESSION['login']['right'] != 9)?'&nbsp;<input type="submit" name="remove" value="' . _("Remove") . '" />':''); ?>
          </div>
          </form>
          <form method="post" action="<?php print $config['server']; ?>/profile">
          <div>
            <h2><?php print _("Password"); ?></h2>
            <?php print _("Set up a new password."); ?><br /><br />
            <div>
              <label for="passwordold"><?php print _("Current password"); ?></label>
              <input name="passwordold" id="passwordold" type="password" maxlength="32" title="<?php print _("Your current password"); ?>" />
              <br />
            </div>
            <div>
              <label for="password"><?php print _("New password"); ?></label>
              <input name="password" id="password" type="password" maxlength="32" title="<?php print _("Your new password (more than 6 characters)"); ?>" />
              <br />
            </div>
            <div>
              <label for="password2"><?php print _("Repeat new password"); ?></label>
              <input name="password2" id="password2" type="password" maxlength="32" title="<?php print _("Your new password for verification"); ?>" />
              <br />
            </div>
            <br />
            <input type="hidden" name="passwd" value="<?php print md5($_SESSION['login']['username'] . floor(intval(date('b')))); ?>" />
            <input type="reset" value="<?php print _("Clear"); ?>" />&nbsp;<input type="submit" value="<?php print _("Update"); ?>" />
          </div>
          </form>
<?php }
?>
          <br />
        </div>
<?php
  foot();
}else {
  header('Location: ' . $config['server']);
}
