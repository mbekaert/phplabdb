<?php
 ob_start("ob_gzhandler");
 session_start();
 include_once '../includes/login.inc';
 if (!($_SESSION['status'] & pow(2,30))) {
  header('Location: ' . $base_url);
  exit;
 };
 $status=$_SESSION['status'];
 header_start();
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.01//EN">
<html>
  <head>
    <title>
      ..:: phpLabDB ::..
    </title>
    <meta http-equiv="Content-Type" content="text/html">
    <link rel="stylesheet" type="text/css" media="print" href="<?php print $base_url; ?>css/print.css">
    <link rel="stylesheet" type="text/css" media="screen" href="<?php print $base_url; ?>css/screen.css">
  </head>
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
<?php if (isset($plugin_name)) {?>
        <li>
          <?php print '<a href="' . $base_url . 'database.php">' . _("Databases") . '</a>'; ?> 
        </li>
<?php };
  if (isset($mods_name)) {
   foreach($mods_title as $key => $value) {
    print "        <li>\n          <a href=\"" . $base_url . $mods_dir[$key] . "\">$value</a>\n        </li>\n";
   };
  };
?>
        <li>
          <?php print '<strong><a href="' . $base_url . 'admin/">' . _("Administration") . '</strong></a>'; ?> 
          <ul>
            <li>
              <?php print '<a href="' . $base_url . 'admin/config.php">' . _("System config") . '</a>'; ?> 
            </li>
            <li>
              <?php print '<a href="' . $base_url . 'admin/users.php">' . _("Users") . '</a>'; ?> 
            </li>
            <li>
              <?php print '<a href="' . $base_url . 'admin/db.php">' . _("Databases") . '</a>'; ?> 
            </li>
            <li>
              <?php print '<strong><a href="' . $base_url . 'admin/plugin.php">' . _("Plug-ins") . '</a></strong>'; ?> 
            </li>
            <li>
              <?php print '<a href="' . $base_url . 'admin/mods.php">' . _("Mods") . '</a>'; ?> 
            </li>
          </ul>
        </li>
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
      <div id="content">
        <div id="page-main">
          <h1>
            <?php print _("Plug-ins Management"); ?> 
          </h1>
          <table id="list-database" width="100%" summary="">
            <tr>
              <th>
                <?php print _("Name"); ?> 
              </th>
              <th>
                <?php print _("Description"); ?> 
              </th>
              <th>
                <?php print _("Action"); ?> 
              </th>
            </tr>
<?php
 if (isset($plugin_name)) {
  $lang=((isset($_COOKIE['lang']))?$_COOKIE['lang']:'en_US');
  $i=0;
  foreach($plugin_name as $key => $value) {
   print "            <tr class=\"" . (($i++%2==1)?'odd':'even') . "\">\n              <td class=\"column-1\">\n                $value\n              </td>\n              <td class=\"column-2\">\n                " . $plugin_desc[$key][$lang] . "\n              </td>\n              <td class=\"column-3\">\n                " . (($plugin_status[$key]==true)?('<a href="' . $base_url . $plugin_dir[$key] . 'admin/">' . _("Configuration") . '</a>'):'-') . "\n              </td>\n            </tr>\n";
  };
 } else {
  print "            <tr>\n              <td colspan=\"3\">\n                <em>" . _("No plug-in") . "</em>\n              </td>\n            </tr>\n"; 
 };
?>
          </table>
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

