<?php
 ob_start("ob_gzhandler");
 session_start();
 include_once '../includes/login.inc';
 if (!isset($_SESSION['status'])) {
  header('Location: ' . $base_url);
  exit;
 };
 include_once '../includes/login.inc'; 
 if(isset($_POST['ok']) && isset($_POST['db']) && ($_FILES['fileup']['size']>10)) {
  if ($sqlserver=='postgresql') {
   $handlesql=popen("$sqlrestaure -c -d " . $_POST['db'] . ' -Fc','w'); //compress
   } elseif ($sqlserver=='mysql') {
   $handlesql=popen("$sqlrestaure -C -B -r -u $sqllogin -p$sqlpassword -h $sqlhost -D " . $_POST['db'],'w');
   } else {
   mes_error(_("SQL server type '$sqlserver' unknown on this server"));
  };
  if (preg_match("/.bz2/", $_FILES['fileup']['name']) && @function_exists('bzcompress')) {
   $handlefile = bzopen($_FILES['fileup']['tmp_name'], 'rb');
   fwrite($handlesql, bzread ($handlefile, $_FILES['fileup']['size']), $_FILES['fileup']['size']);
   bzclose($handlefile);
   } else {
   $handlefile = fopen($_FILES['fileup']['tmp_name'], 'rb');
   fwrite($handlesql, fread ($handlefile, $_FILES['fileup']['size']), $_FILES['fileup']['size']);
   fclose($handlefile);
  };
  pclose($handlesql);
  header('Location: ' . $base_url . 'admin/db.php');
 } elseif (isset($_GET['action']) && isset($_GET['db'])) {
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
              <?php print '<strong><a href="' . $base_url . 'admin/db.php">' . _("Databases") . '</a></strong>'; ?> 
            </li>
            <li>
              <?php print '<a href="' . $base_url . 'admin/plugin.php">' . _("Plug-ins") . '</a>'; ?> 
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
           <?php print _("Database Management"); ?> 
         </h1>
          <p>
            <?php print _("To upload ") . $_GET['db'] . _(" will erase the data"); ?>.<br>
            <?php print _("SQL file. (no more than ") . get_cfg_var(upload_max_filesize); ?>).
          </p>
          <form action="<?php print $base_url; ?>admin/db_upload.php" method="post" enctype="multipart/form-data">
            <table summary="">
              <tr>
                <td>
                  <label for="fileup">&nbsp;<?php print _("SQL file"); ?>&nbsp;</label>
                </td>
                <td>
                  <input type="file" size="30" maxlength="100" name="fileup" id="fileup">
                </td>
              </tr>
              <tr>
                <td>
                  <label for="ok">&nbsp;<?php print _("Are you sure?"); ?>&nbsp;</label>
                </td>
                <td>
                  <input type="checkbox" name="ok" id="ok">
                </td>
              </tr>
              <tr>
                <td>
                </td>
                <td>
                  <input type="hidden" name="db" value="<?php print $_GET['db']; ?>"><input type="submit" name="submit" value="<?php print _("Upload"); ?>">
                </td>
              </tr>
            </table>
          </form>
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
<?php
 } else {
 header('Location: ' . $base_url . 'admin/db.php');
};
?>