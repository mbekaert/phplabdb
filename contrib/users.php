<?php
ob_start("ob_gzhandler");
session_start();
require_once('includes/main.inc');

if ($config['login'] && ($_SESSION['login']['right'] == 9)) {
  if (!empty($_GET['user'])) {
    $sql = sql_connect($config['db']);
    if (isset($_POST['remove']) && !empty($_POST['key']) && (strip_tags(trim($_GET['user'])) != $_SESSION['login']['username']) && ($_POST['key'] == md5(strip_tags(trim($_GET['user'])) . floor(intval(date('b')))))) {
      $result = sql_query('DELETE FROM users WHERE username=\'' . addslashes(strip_tags(trim(rawurldecode($_GET['user'])))) . '\';', $sql);
      header('Location: ' . $config['server'] . '/users');
      exit;
    }elseif (isset($_POST['edit']) && !empty($_POST['key']) && ($_POST['key'] == md5(strip_tags(trim($_GET['user'])) . floor(intval(date('b'))))) && !empty($_POST['name']) && strlen(strip_tags(trim($_POST['name']))) > 5 && !empty($_POST['email']) && strlen(strip_tags(trim($_POST['email']))) > 6 && stristr(strip_tags(trim($_POST['email'])), '@') && stristr(strip_tags(trim($_POST['email'])), '.') && ((strip_tags(trim($_GET['user'])) == $_SESSION['login']['username']) || (!empty($_POST['rights']) && (intval($_POST['rights']) >= 0))) && !empty($_POST['organism']) && strlen(strip_tags(trim($_POST['organism']))) > 2) {
      $code = array(0 => '!', 1 => '~', 2 => '=', 3 => '$', 4 => '£', 5 => '+', 6 => '*', 7 => '?', 8 => '#', 9 => '@');
      $result = sql_query('UPDATE users SET real_name=\'' . addslashes(strip_tags(trim($_POST['name']))) . '\', email=\'' . addslashes(strip_tags(trim($_POST['email']))) . '\'' . (((strip_tags(trim($_GET['user'])) != $_SESSION['login']['username'])) ? ', rights=\'' . intval($_POST['rights']) . '\', code=\'' . $code[intval($_POST['rights'])] . '\', activated=' . ((intval($_POST['rights']) > 7)?2:((intval($_POST['rights']) > 0)?1:0)) : '') . ', taxon=\'' . addslashes(strip_tags(trim($_POST['organism']))) . '\' WHERE username=\'' . addslashes(strip_tags(trim(rawurldecode($_GET['user'])))) . '\';', $sql);
      if (!strlen($r = sql_last_error($sql))) {
        header('Location: ' . $config['server'] . '/users');
        exit;
      }else {
        $error = 'SQL error';
      }
    }
    $result = sql_query('SELECT username, real_name, email, rights, taxon FROM users WHERE (username=\'' . addslashes(strip_tags(trim(rawurldecode($_GET['user'])))) . '\');', $sql);
    if (!strlen($r = sql_last_error($sql)) && (sql_num_rows($result) == 1)) {
      $row = sql_fetch_row($result);
      head('admin');
?>
        <div class="items">
          <h1><?php print $row[0]; ?><small><?php print _("Users manager"); ?></small></h1><br />
          <form method="post" action="<?php print $config['server'] . '/users/' . rawurlencode($row[0]); ?>">
          <div>
          <?php print _("You can edit your any user (except the username and the password!) if order to keep it up-to-date. You can also completely remove the profile."); ?><br /><br />
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
<?php if (strip_tags(trim($_GET['user'])) != $_SESSION['login']['username']) {
?>
            <div>
              <label for="rights"><strong><?php print _("Rights"); ?></strong></label>
              <select name="rights" id="rights" title="<?php print _("User rights"); ?>"><option<?php print ((!isset($_POST['rights']) || ($_POST['rights'] == 0))?' selected="selected"':((!isset($row[3]) || ($row[3] == 0))?' selected="selected"':'')); ?> value="0"><?php print _("not activated (disable)"); ?></option><option<?php print ((isset($_POST['rights']) && ($_POST['rights'] == 1))?' selected="selected"':((isset($row[3]) && ($row[3] == 1))?' selected="selected"':'')); ?> value="1"><?php print _("guest"); ?></option><option<?php print ((isset($_POST['rights']) && ($_POST['rights'] == 2))?' selected="selected"':((isset($row[3]) && ($row[3] == 2))?' selected="selected"':'')); ?> value="2"><?php print _("user"); ?></option><option<?php print ((isset($_POST['rights']) && ($_POST['rights'] == 3))?' selected="selected"':((isset($row[3]) && ($row[3] == 3))?' selected="selected"':'')); ?> value="3"><?php print _("user with power"); ?></option><option<?php print ((isset($_POST['rights']) && ($_POST['rights'] == 5))?' selected="selected"':((isset($row[3]) && ($row[3] == 5))?' selected="selected"':'')); ?> value="5"><?php print _("robot"); ?></option><option<?php print ((isset($_POST['rights']) && ($_POST['rights'] == 7))?' selected="selected"':((isset($row[3]) && ($row[3] == 7))?' selected="selected"':'')); ?> value="7"><?php print _("deleted (disable)"); ?></option><option<?php print ((isset($_POST['rights']) && ($_POST['rights'] == 8))?' selected="selected"':((isset($row[3]) && ($row[3] == 8))?' selected="selected"':'')); ?> value="8"><?php print _("curator [certificate]"); ?></option><option<?php print ((isset($_POST['rights']) && ($_POST['rights'] == 9))?' selected="selected"':((isset($row[3]) && ($row[3] == 9))?' selected="selected"':'')); ?> value="9"><?php print _("adminstrator [certificate]"); ?></option></select>
              <br />
            </div>
<?php }
?>
            <div>
              <label for="organism"><?php print _("Taxon"); ?></label>
              <input name="organism" id="organism" type="text" maxlength="128"<?php print (!empty($_POST['organism'])?' value="' . strip_tags(trim($_POST['organism'])) . '"':(isset($row[4])?' value="' . $row[4] . '"':'')); ?> title="<?php print _("Family or specie names"); ?>" />
              <br />
            </div>
            <br />
            <input type="hidden" name="key" value="<?php print md5(strip_tags(trim($_GET['user'])) . floor(intval(date('b')))); ?>" />
            <input type="reset" value="<?php print _("Clear"); ?>" />&nbsp;<input type="submit" name="edit" value="<?php print _("Edit"); ?>" /><?php print ((strip_tags(trim($_GET['user'])) != $_SESSION['login']['username'])?'&nbsp;<input type="submit" name="remove" value="' . _("Remove") . '" />':''); ?>
          </div>
          </form>
<?php
    }
  }elseif (!empty($config['organisation']['voucher']) && !empty($_POST['voucher']) && ($_POST['voucher'] == md5('voucher' . floor(intval(date('b'))))) && !empty($_POST['email']) && strlen(strip_tags(trim($_POST['email']))) > 6 && stristr(strip_tags(trim($_POST['email'])), '@') && stristr(strip_tags(trim($_POST['email'])), '.')) {
    $subject = $config['powered'] . ': Invitation to register';
    $message = "Hello,\n\n Here is an invitation to create an account in " . $config['server'] . " by clicking the link below\n\n" . $config['server'] . '/register?email=' . strip_tags(trim($_POST['email'])) . '&voucher=' . sha1($config['organisation']['voucher'] . strip_tags(trim($_POST['email'])) . $config['server']) . "\n\n** This is an automated response, please do not reply! **\n\n--\n" . $config['powered'] . "\n" . $config['server'] . "\n";
    $headers = 'From: ' . $config['powered'] . ' <' . $_SERVER['SERVER_ADMIN'] . '>';
    $mailsend = @mail(strip_tags(trim($_POST['email'])), $subject, $message, $headers);
    header('Location: ' . $config['server'] . '/users');
    exit;
  }else {
    head('admin')
?>
        <div class="items">
          <h1><?php print _("Users"); ?><small><?php print _("Users manager"); ?></small></h1><br />
          <form method="post" action="<?php print $config['server']; ?>/users">
          <div>
            <h2><?php print _("Search"); ?></h2><?php print _("You can edit your any user (except the username and the password!) if order to keep it up-to-date. You can also completely remove you profile."); ?><br /><br />
<?php print (isset($error)?"            <strong>" . $error . "</strong><br /><br />\n":''); ?>
            <div>
              <label for="user"><?php print _("Username"); ?></label>
              <input name="user" id="user" type="text" maxlength="32"<?php print (!empty($_POST['user'])?' value="' . strip_tags(trim($_POST['user'])) . '"':''); ?> title="<?php print _("Username"); ?>" />
              <br />
            </div>
            <br />
            <input type="hidden" name="search" value="<?php print md5('user' . floor(intval(date('b')))); ?>" />
            <input type="submit" value="<?php print _("Search"); ?>" />
          </div>
          </form>
<?php
    if (!empty($_POST['search']) && ($_POST['search'] == md5('user' . floor(intval(date('b'))))) && !empty($_POST['user'])) {
      $sql = sql_connect($config['db']);
      $result = sql_query('SELECT username, real_name, code, email, added FROM users WHERE (username' . sql_reg(addslashes(strip_tags(trim($_POST['user'])))) . ' OR real_name' . sql_reg(addslashes(strip_tags(trim($_POST['user'])))) . ');', $sql);
      if (!strlen($r = sql_last_error($sql)) && (sql_num_rows($result) > 0)) {
        print "          <div>\n            <h2>Results</h2>\n";
        while ($row = sql_fetch_row($result)) {
          print '             <div class="result"><span class="ref"><a href="' . $config['server'] . '/users/' . rawurlencode($row[0]) . '">' . $row[2] . $row[0] . '</a></span><span class="desc">' . $row[1] . '</span><span class="detail">' . $row[3] . '</span><span class="updated">' . date(_("d-m-Y"), strtotime($row[4])) . "</span></div>\n";
        }
        print "          </div>\n";
      }
    }
    if (!empty($config['organisation']['voucher'])) {
?>
          <form method="post" action="<?php print $config['server']; ?>/users">
          <div>
            <h2><?php print _("Voucher"); ?></h2><?php print _("Send a voucher to a new guest."); ?><br /><br />
            <div>
              <label for="email"><?php print _("E-mail"); ?></label>
              <input name="email" id="email" type="text" maxlength="128" title="<?php print _("Email address"); ?>" />
              <br />
            </div>
            <br />
            <input type="hidden" name="voucher" value="<?php print md5('voucher' . floor(intval(date('b')))); ?>" />
            <input type="submit" value="<?php print _("Send"); ?>" />
          </div>
          </form>
<?php }
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