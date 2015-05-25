<?php
ob_start("ob_gzhandler");
session_start();
require_once('includes/main.inc');

head('login');
if (isset($_GET['activate'])) {
  $sql = sql_connect($config['db']);
  $result = sql_query('SELECT username FROM users WHERE activated=0 AND active=\'' . md5(strip_tags(trim($_GET['activate']))) . '\';', $sql);
  if (!strlen($r = sql_last_error($sql)) && (sql_num_rows($result) == 1)) {
    $result = sql_query('UPDATE users SET activated=1, rights=1, code=\'~\' WHERE active=\'' . md5(strip_tags(trim($_GET['activate']))) . '\';', $sql);
    if (!strlen($r = sql_last_error($sql))) {
      $activated = true;
    }
  }
}
?>
         <div class="items">
           <h1><?php print _("Register"); ?><small><?php print _("Register to get a full access"); ?></small></h1><br />
<?php
if (isset($activated)) {
  print '           <p>' . _("Your account has been activated.") . "</p>\n";
}else {
  print '           <p>' . _("Your account could not be activated.") . "</p>\n";
}
?>
         </div>
<?php
foot(); ?>