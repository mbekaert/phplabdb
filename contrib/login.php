<?php
ob_start("ob_gzhandler");
session_start();
require_once('includes/main.inc');
mt_srand(time());

if (($config['secure'] == true) && !empty($_POST['login']) && ($_POST['login'] == md5('login' . floor(intval(date('b'))))) && !empty($_POST['username']) && (strip_tags(trim($_POST['username'])) == $_SERVER['SSL_CLIENT_S_DN_CN']) && !empty($_POST['password'])) {
  $password = md5(strip_tags(trim($_POST['password'])));
  $sql = sql_connect($config['db']);
  $result = sql_query("SELECT username, email, code, rights FROM users WHERE (username='" . $_SERVER['SSL_CLIENT_S_DN_CN'] . "' AND password='$password' AND email='" . $_SERVER['SSL_CLIENT_S_DN_Email'] . "' AND activated=2 AND rights > 7);", $sql);
  if (!strlen($r = sql_last_error($sql)) && (sql_num_rows($result) == 1)) {
    $row = sql_fetch_row($result);
    $_SESSION['login']['user_id'] = md5(uniqid(mt_rand(), true));
    $_SESSION['login']['ip'] = $_SERVER['REMOTE_ADDR'];
    $_SESSION['login']['username'] = $row[0];
    $_SESSION['login']['code'] = $row[2];
    $_SESSION['login']['email'] = $row[1];
    $_SESSION['login']['right'] = intval($row[3]);
    setcookie('user_id', $_SESSION['login']['user_id'], 0, dirname($_SERVER['REQUEST_URI']));
  }else {
    $error = _("Your username or password is not correct!");
  }
}elseif (!empty($_POST['login']) && ($_POST['login'] == md5('login' . floor(intval(date('b'))))) && !empty($_POST['username']) && !empty($_POST['password'])) {
  $login = strip_tags(trim($_POST['username']));
  $password = md5(strip_tags(trim($_POST['password'])));
  $sql = sql_connect($config['db']);
  $result = sql_query("SELECT username, email, code, rights FROM users WHERE (username='$login' AND password='$password' AND".((isset($config['unsafe']) && ($config['secure'] == false))?'':' activated=1 AND rights<7 AND').' rights>0);', $sql);
  if (!strlen($r = sql_last_error($sql)) && (sql_num_rows($result) == 1)) {
    $row = sql_fetch_row($result);
    $_SESSION['login']['user_id'] = md5(uniqid(mt_rand(), true));
    $_SESSION['login']['ip'] = $_SERVER['REMOTE_ADDR'];
    $_SESSION['login']['username'] = $row[0];
    $_SESSION['login']['code'] = $row[2];
    $_SESSION['login']['email'] = $row[1];
    $_SESSION['login']['right'] = intval($row[3]);
    setcookie('user_id', $_SESSION['login']['user_id'], mktime(1, 0, 0, date("m"), date("d") + 1, date("Y")), dirname($_SERVER['REQUEST_URI']));
  }else {
    $error = _("Your username or password is not correct!");
  }
}
if (!isset($row)) {
  unset($_SESSION['login']);
  session_unset();
  session_destroy();
  setcookie('user_id', '', 0, dirname($_SERVER['REQUEST_URI']));
  setcookie(session_name(), '', 0, '/');
  head('login');
?>
        <div class="items">
          <h1><?php print _("Login"); ?><small><?php print _("Request full access"); ?></small></h1><br />
          <form method="post" action="<?php print $config['server']; ?>/login">
          <div>
            <?php print _("If you have forgotten your username or password, you can") . ' <a href="' . $config['server'] . '/lost">' . _("request") . '</a> ' . _("to have your username e-mailed to you and to reset your password."); ?><br /><br />
<?php print (isset($error)?"            <strong>" . $error . "</strong><br /><br />\n":''); ?>
            <div>
              <label for="username"><?php print _("Username"); ?></label>
              <input name="username" id="username" type="text" maxlength="32" title="<?php print _("login/username"); ?>" />
              <br />
            </div>
            <div>
              <label for="password"><?php print _("Password"); ?></label>
              <input name="password" id="password" type="password" maxlength="32" title="<?php print _("Your login password"); ?>" />
              <br />
            </div>
            <br />
            <input type="hidden" name="login" value="<?php print md5('login' . floor(intval(date('b')))); ?>" />
            <input type="reset" value="<?php print _("Clear"); ?>" />&nbsp;<input type="submit" value="<?php print _("Submit"); ?>" />
          </div>
          </form>
        </div>
<?php
  foot();
}else {
  header('Location: ' . $config['server'] . '/my');
  exit;
}
?>