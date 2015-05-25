<?php
ob_start("ob_gzhandler");
session_start();
require_once('includes/main.inc');

if ($config['login']) {
  head('my');
?>
        <div class="items">
          <h1><?php print $_SESSION['login']['username']; ?><small><?php print _("Your space..."); ?></small></h1><br />
          <div>
            <div><h2>Profile<small><a href="<?php print $config['server']; ?>/profile" title="<?php print _("Your profile"); ?>"><?php print _("Edit your profile"); ?></a></small></h2><br /><?php print _("You can edit your current profile (including your name, email and password) in order to keep it up-to-date. You can also completely remove you profile and 'unregister' yourself."); ?><br /></div>
<?php
  $sql = sql_connect($config['db']);
  if (isset($plugin)) {
    foreach($plugin as $request) {
      if (isset($request['search'])) {
        $result = sql_query('SELECT a.updated, a.prefix, a.id, ' . $request['search'] . ' (a.author=\'' . $_SESSION['login']['username'] . '\' AND a.updated>' . (($config['sqlserver'] == 'mysql')?'DATE_SUB(CURDATE(),INTERVAL 30 DAY)':'(CURRENT_TIMESTAMP - interval \'30 days\')') . ') ORDER BY a.updated DESC LIMIT 10;', $sql);
        if (!strlen($r = sql_last_error($sql)) && (sql_num_rows($result) > 0)) {
          print "            <div>\n              <h2>" . _("Your recent activities") . "</h2>\n";
          list($ref) = explode('|', $request['code']);
          while ($row = sql_fetch_row($result)) {
            print '              <div class="result"><span class="ref"><a href="' . $config['server'] . '/' . $ref . decoct($row[1]) . '.' . decoct($row[2]) . '">' . $ref . decoct($row[1]) . '.' . decoct($row[2]) . '</a></span><span class="desc">' . $row[3] . '</span><span class="detail">' . $row[4] . '</span><span class="updated">' . date('Y-m-d', strtotime($row[0])) . "</span></div>\n";
          }
          print "            </div>\n";
        }
      }
    }
  }
?>
          </div>
          <br />
        </div>
<?php
  if ($_SESSION['login']['right'] == 9) {
?>
        <div class="items">
          <h1><?php print _("Administration"); ?><small><?php print _("Users and Website managenment"); ?></small></h1><br />
          <form method="post" action="<?php print $config['server']; ?>/users">
          <div>
            <h2><?php print _("Identify a user"); ?><small><a href="<?php print $config['server']; ?>/users" title="<?php print _("Users manager"); ?>"><?php print _("Users manager"); ?></a></small></h2><br /><?php print _("You can edit your any user (except the username and the password!) in order to keep it up-to-date. You can also completely remove you profile."); ?><br /><br />
            <div>
              <label for="user"><?php print _("Username"); ?></label>
              <input name="user" id="user" type="text" maxlength="32" title="<?php print _("Username"); ?>" />
              <br />
            </div>
            <br />
            <input type="hidden" name="search" value="<?php print md5('user' . floor(intval(date('b')))); ?>" />
            <input type="submit" value="<?php print _("Search"); ?>" />
          </div>
          </form>
<?php
    $result = sql_query('SELECT username, real_name, code, email, added FROM users WHERE added>' . (($config['sqlserver'] == 'mysql')?'DATE_SUB(CURDATE(),INTERVAL 30 DAY)':'(CURRENT_TIMESTAMP - interval \'30 days\')') . ' ORDER BY added DESC;', $sql);
    if (!strlen($r = sql_last_error($sql)) && (sql_num_rows($result) > 0)) {
      print "            <div>\n              <h2>" . _("Recently added") . "</h2>\n";
      while ($row = sql_fetch_row($result)) {
        print '              <div class="result"><span class="ref"><a href="' . $config['server'] . '/users/' . rawurlencode($row[0]) . '">' . $row[2] . $row[0] . '</a></span><span class="desc">' . $row[1] . '</span><span class="detail">' . $row[3] . '</span><span class="updated">' . date('Y-m-d', strtotime($row[4])) . "</span></div>\n";
      }
      print "            </div>\n";
    }
?>
          <div><h2>Website<small><a href="<?php print $config['server']; ?>/website" title="<?php print _("Website options"); ?>"><?php print _("Edit options"); ?></a></small></h2><br /><?php print _("You can customize the website (including an organisation name, and logo) in order to keep it up-to-date."); ?><br /></div>
          <br />
        </div>
<?php
  }

  foot();
}else {
  header('Location: ' . $config['server']);
}
?>