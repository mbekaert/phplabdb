<?php
ob_start("ob_gzhandler");
session_start();
require_once('includes/main.inc');
mt_srand(time());

head('login');
if (!empty($_POST['lost']) && ($_POST['lost'] == md5('lost' . floor(intval(date('b')))))) {
  if (!empty($_POST['email']) && stristr(strip_tags(trim($_POST['email'])), '@') && stristr(strip_tags(trim($_POST['email'])), '.')) {
    $sql = sql_connect($config['db']);
    $activated = uniqid(mt_rand(), true);
    if ($config['secure'] == true) {
      $result = sql_query('SELECT username, real_name, email FROM users WHERE (email=\'' . addslashes(strip_tags(trim($_POST['email']))) . '\' AND activated > 0 AND rights > 0);', $sql);
    }else {
      $result = sql_query('SELECT username, real_name, email FROM users WHERE (email=\'' . addslashes(strip_tags(trim($_POST['email']))) . '\' AND activated = 1 AND rights > 0 AND rights < 7);', $sql);
    }
    if (!strlen($r = sql_last_error($sql)) && (sql_num_rows($result) == 1)) {
      $row = sql_fetch_row($result);
      $salt = array('a', 'b', 'c', 'd', 'e', 'f', 'g', 'h', 'j', 'k', 'm', 'n', 'p', 'q', 'r', 's', 't', 'u', 'v', 'w', 'x', 'y', 'z', 'A', 'B', 'C', 'E', 'F', 'G', 'H', 'J', 'K', 'L', 'M', 'N', 'P', 'Q', 'R', 'S', 'T', 'U', 'V', 'W', 'X', 'Y', 'Z', '2', '3', '4', '5', '6', '7', '8', '9', '#', '_', '$', '*', '!', '=', '-', '+', '@');
      $random_password = '';
      for ($i = 0; $i < 10; $i++) $random_password .= $salt[mt_rand(0, count($salt)-1)];
      $result = sql_query('UPDATE users SET password=\'' . md5($random_password) . '\' WHERE (email=\'' . addslashes($row[2]) . '\');', $sql);
      if (!strlen($r = sql_last_error($sql))) {
        $subject = $config['powered'] . ': Password Reset';
        $message = 'Hello ' . $row[1] . ",\n\nYou requested a reset of your password.\n\nUsername: " . $row[0] . "\nNew Password: " . $random_password . "\nSite: " . $config['server'] . "\n\nOnce you have logged in using this temporary password, please go to change\npassword and create your own password.\n\n** This is an automated response, please do not reply! **\n\n--\n" . $config['powered'] . "\n" . $config['server'] . "\n";
        $headers = 'From: ' . $config['powered'] . ' <nobody@>';
        $mailsend = @mail($row[2], $subject, $message, $headers);
        $notice = '           <p>' . _("Your password has been sent! Please check your email!.") . "</p>\n";
      }
    }else {
      $error = _("Your email address is not correct or unknown!");
    }
  }else {
    $error = _("Your email address is not correct or unknown!");
  }
}
?>
         <div class="items">
           <h1><?php print _("Lost Password"); ?><small><?php print _("Recover a Forgotten Password"); ?></small></h1><br />
<?php if (isset($notice)) {
  print $notice;
}else {
?>
          <form method="post" action="<?php print $config['server']; ?>/lost">
            <div><?php print _("If you have forgotten your username or password, you can request to have your username emailed to you and to reset your password."); ?><br /><br />
<?php print (isset($error)?'            <strong>' . $error . "</strong><br /><br />\n":''); ?>
            <div>
                <label for="email"><strong><?php print _("E-mail"); ?></strong></label>
                <input name="email" id="email" type="text" maxlength="128" title="<?php print _("Your email address"); ?>" />
                <br />
              </div>
              <br />
              <strong><?php print _("Notice:"); ?> </strong><?php print _("When you fill in your registered email address, you will be sent instructions on how to reset your password."); ?><br />
              <br />
              <input type="hidden" name="lost" value="<?php print md5('lost' . floor(intval(date('b')))); ?>" />
              <input type="reset" value="<?php print _("Clear"); ?>" />&nbsp;<input type="submit" value="<?php print _("Submit"); ?>" />
            </div>
          </form>
<?php
}
?>
         </div>
<?php
foot();
?>