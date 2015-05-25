<?php
 ob_start("ob_gzhandler");
 session_start();
 include_once '../../includes/login.inc';
 if (!($_SESSION['status'] & pow(2,30))) {
  header('Location: ' . $base_url);
  exit;
 };
 $lev=error_reporting(8); 
 $status=$_SESSION['status'];
 header_start();
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.01//EN">
<html>
  <head>
    <title>
      ..:: phpLabDB::SeedDB ::..
    </title>
    <meta http-equiv="Content-Type" content="text/html">
    <link rel="stylesheet" type="text/css" media="print" href="<?php print $base_url; ?>css/print.css">
    <link rel="stylesheet" type="text/css" media="screen" href="<?php print $base_url; ?>css/screen.css">
    <style type="text/css">
.seed { background: url('../images/seeds.png') no-repeat right top; }
    </style>
  </head>
  <body>
  <body>
    <div id="header">
      <div id="header-logo">
        <?php print "<a href=\"$organisation[1]\"><img src=\"$organisation[2]\" alt=\"$organisation[0]\"></a>"; ?> 
      </div>
      <div id="header-items">
        <span class="header-icon"><?php print '<a href="' . $base_url .'about/lang.php"><img src="' . $base_url . 'images/header-langs.png" alt="">' . _("Language") . '</a> <a href="' . $base_url . 'logout.php"><img src="' . $base_url  . 'images/header-logout.png" alt="">' . _("Logout") . '</a>'; ?></span>
      </div>
    </div>
    <div id="nav">
    </div>
    <div id="side-left">
      <div id="side-nav-label">
        <?php print _("Navigation"); ?>:
      </div>
      <ul id="side-nav">
        <li>
          <?php print '<a href="' . $base_url . '">' . _("Home") . '</a>'; ?> 
        </li>
        <li>
          <?php print '<strong><a href="' . $base_url . 'database.php">' . _("Databases") . '</a></strong>'; ?> 
          <ul>
<?php
 foreach($plugin_title as $key => $value) {
  if ($key=='seeddb') {
   print "          <li>\n            <strong><a href=\"" . $base_url . $plugin_dir[$key] . "\">$value</a></strong>\n          </li>\n";
   $plugin=$plugin_dir[$key];
  } else {
   print "          <li>\n            <a href=\"" . $base_url . $plugin_dir[$key] . "\">$value</a>\n          </li>\n";
  };
 };
?>
          </ul>
        </li>
<?php
 if (isset($mods_name)) {
  foreach($mods_title as $key => $value) {
   print "        <li>\n          <a href=\"" . $base_url . $mods_dir[$key] . "\">$value</a>\n        </li>\n";
  };
 };
 if ($status & pow(2,30)) {
?>
        <li>
          <?php print '<a href="' . $base_url . 'admin/">' . _("Administration") . '</a>'; ?> 
        </li>
<?php }; ?>
        <li>
          <?php print '<a href="' . $base_url . 'about/">' . _("About") . '</a>'; ?> 
        </li>
      </ul>
    </div>
    <div id="middle-three">
      <div class="corner-tr">
        &nbsp;
      </div>
      <div class="corner-tl">
        &nbsp;
      </div>
      <div id="content" class="seed">
        <div id="page-main">
<?php  textdomain('seeddb'); ?>
          <h1>
            SeedDB plug-in
          </h1>
<?php
  $dbconn=sql_connect($plugin_db['seeddb']);
  if (isset($_POST['tips']) && isset($_POST['barcode'])) {
   $result=sql_query("UPDATE config SET value=" . (($_POST['tips']=='on')?"'true'":'NULL') . " WHERE id='tips';",$dbconn);
   $result=sql_query("UPDATE config SET value=" . (($_POST['barcode']=='on')?"'true'":'NULL') . " WHERE id='barcode';",$dbconn);
  };
  $result=sql_query("SELECT value FROM config WHERE id='tips';",$dbconn);
  if(!(strlen($r=sql_last_error($dbconn)))&&(sql_num_rows($result)==1)) {
   $row=sql_fetch_row($result);
   if(isset($row[0])) $tips=true;
  };
  $result=sql_query("SELECT value FROM config WHERE id='barcode';",$dbconn);
  if(!(strlen($r=sql_last_error($dbconn)))&&(sql_num_rows($result)==1)) {
   $row=sql_fetch_row($result);
   if(isset($row[0])) $barcode=true;
  };
?>
          <h3>
            <?php print _("Tips/Help"); ?> 
          </h3>
          <form action="<?php print $base_url . $plugin; ?>admin/tips.php" method="post">
            <table id="list-database" width="100%" summary="">
              <tr class="odd">
                <td class="column-1">
                  &nbsp;1. <?php print _("Tips visible?"); ?>&nbsp;
                </td>
                <td class="column-2">
                  <?php print '<input type="radio" name="tips" value="on"' . ((isset($tips))?' checked="checked"':'') . '>&nbsp;' . _("Yes") . ' &nbsp; <input type="radio" name="tips" value="off"' . ((isset($tips))?'':' checked="checked"') . '>&nbsp;' . _("No"); ?> 
                </td>
                <td class="column-3">
                  <input type="submit" name="submit" value="<?php print _("Update"); ?>">
                </td>
              </tr>
              <tr class="even">
                <td class="column-1">
                  &nbsp;2. <?php print _("Show barcode?"); ?>&nbsp;
                </td>
                <td class="column-2">
                  <?php print '<input type="radio" name="barcode" value="on"' . ((isset($barcode))?' checked="checked"':'') . '>&nbsp;' . _("Yes") . ' &nbsp; <input type="radio" name="barcode" value="off"' . ((isset($barcode))?'':' checked="checked"') . '>&nbsp;' . _("No"); ?> 
                </td>
                <td class="column-3">
                  <input type="submit" name="submit" value="<?php print _("Update"); ?>">
                </td>
              </tr>
            </table>
          </form>
<?php  textdomain('phplabdb'); ?>
        </div>
      </div>
      <div class="corner-br">
        &nbsp;
      </div>
      <div class="corner-bl">
        &nbsp;
      </div>
    </div>
    <div id="footer">
      - <?php print "<a href=\"$organisation[1]\">$organisation[0]</a> " . _("powered by"); ?> <a href="http://sourceforge.net/projects/phplabdb/">phpLabDB</a> -<br>
       &nbsp;<br>
    </div>
  </body>
</html>
