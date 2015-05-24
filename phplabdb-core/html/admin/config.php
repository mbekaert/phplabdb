<?php
 ob_start("ob_gzhandler");
 session_start();
 include_once '../includes/login.inc';
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
              <?php print '<strong><a href="' . $base_url . 'admin/config.php">' . _("System config") . '</a></strong>'; ?> 
            </li>
            <li>
              <?php print '<a href="' . $base_url . 'admin/users.php">' . _("Users") . '</a>'; ?> 
            </li>
            <li>
              <?php print '<a href="' . $base_url . 'admin/db.php">' . _("Databases") . '</a>'; ?> 
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
           <?php print _("System configuration"); ?> 
         </h1>
<?php if (isset($_GET['para'])) {
  $sql=sql_connect('db_phplabdb');
  $result=sql_query('SELECT ' . $_GET['para'] . ' FROM organisation;',$sql);
  if (sql_num_rows($result)==1) {
   $row=sql_fetch_row($result);
?>
          <h3>
            <?php print _("Modification"); ?> 
          </h3>
          <form action="<?php print $base_url; ?>admin/config.php" method="post">
            <table summary="">
              <tr>
                <td>
                  <label for="zulu"><?php print _($_GET['para']); ?>&nbsp;</label>
                </td>
                <td>
                  <input type="text" size="40" maxlength="500" name="zulu" value="<?php print $row[0]; ?>">
                </td>
              </tr>
              <tr>
                <td>
                </td>
                <td>
                  <input type="hidden" name="para" value="<?php print $_GET['para']; ?>"><input type="reset" name="clear" value="<?php print _("Clear"); ?>"> &nbsp; <input type="submit" name="submit" value="<?php print _("Update"); ?>">
                </td>
              </tr>
            </table>
          </form>
<?php 
  };
 } elseif (isset($_POST['para'])) {
  $sql=sql_connect ('db_phplabdb');
  $result=sql_query('UPDATE organisation SET ' . $_POST['para'] . "='" . trim($_POST['zulu']) . "';",$sql);
   print "          <p>\n            <a href=\"" . $base_url . "admin/config.php\"><img src=\"" . $base_url . 'images/ok.png" alt=""></a>&nbsp;<strong>' . _("Update successfull") . "</strong>\n          </p>\n";
 } else { ?> 
          <table id="list-database" width="100%" summary="">
            <tr>
              <th colspan="2">
                <?php print _("Read-only parameters"); ?> 
              </th>
            </tr>
            <tr>
              <td>
                <strong>Version</strong>
              </td>
              <td>
                <?php print $version; ?> 
              </td>
            </tr>
            <tr>
              <td>
                <strong>Webmaster address</strong>
              </td>
              <td>
                <?php print $_SERVER['SERVER_ADMIN']; ?> 
              </td>
            </tr>
            <tr>
              <td>
                <strong>Web Server</strong>
              </td>
              <td>
                <?php print $_SERVER['SERVER_SOFTWARE']; ?> 
              </td>
            </tr>
            <tr>
              <td>
                <strong>Base_URL</strong>
              </td>
              <td>
                <?php print $base_url; ?> 
              </td>
            </tr>
            <tr>
              <td>
                <strong>SQLhost</strong>
              </td>
              <td>
                <?php print $sqlhost; ?> 
              </td>
            </tr>
            <tr>
              <td>
                <strong>SQLlogin</strong>
              </td>
              <td>
                <?php print $sqllogin; ?> 
              </td>
            </tr>
            <tr>
              <td>
                <strong>SQLpassword</strong>
              </td>
              <td>
                <acronym title="<?php print $sqlpassword; ?>" class="password">***********</acronym>
              </td>
            </tr>
            <tr>
              <td>
                <strong>SQLserver</strong>
              </td>
              <td>
                <?php print $sqlserver; ?> 
              </td>
            </tr>
            <tr>
              <td>
                <strong>SQLdump</strong>
              </td>
              <td>
                <small><?php print $sqldump; ?></small>
              </td>
            </tr>
            <tr>
              <td>
                <strong>SQLrestaure</strong>
              </td>
              <td>
                <small><?php print $sqlrestaure; ?></small>
              </td>
            </tr>
          </table>
          <br>
          <table id="list-database" width="100%" summary="">
            <tr>
              <th colspan="3">
                <?php print _("Configurable parameters"); ?> 
              </th>
            </tr>
            <tr>
              <td class="column-1">
                <strong><?php print _("Organisation Name"); ?></strong>
              </td>
              <td class="column-2">
                <?php print $organisation[0]; ?> 
              </td>
              <td class="column-3">
                <?php print '<a href="' . $base_url . 'admin/config.php?para=name">' . _("Modify") . '</a>'; ?> 
              </td>
            </tr>
            <tr>
              <td class="column-1">
                <strong><?php print _("Organisation URL"); ?></strong>
              </td>
              <td class="column-2">
                <?php print $organisation[1]; ?> 
              </td>
              <td class="column-3">
                <?php print '<a href="' . $base_url . 'admin/config.php?para=url">' . _("Modify") . '</a>'; ?> 
              </td>
            </tr>
            <tr>
              <td class="column-1">
                <strong><?php print _("Organisation Logo"); ?></strong>
              </td>
              <td class="column-2">
                <?php print $organisation[2]; ?> 
              </td>
              <td class="column-3">
                <?php print '<a href="' . $base_url . 'admin/config.php?para=logo">' . _("Modify") . '</a>'; ?> 
              </td>
            </tr>
            <tr>
              <td class="column-1">
                <strong><?php print _("Welcome message"); ?></strong>
              </td>
              <td class="column-2">
                <?php print ((empty($organisation[3]))?'<em>none</em>':'...'); ?> 
              </td>
              <td class="column-3">
                <?php print '<a href="' . $base_url . 'admin/config.php?para=welcome">' . _("Modify") . '</a>'; ?> 
              </td>
            </tr>
          </table>
<?php }; ?>
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
