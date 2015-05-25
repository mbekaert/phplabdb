<?php
 ob_start("ob_gzhandler");
 session_start();
 include_once '../includes/login.inc';
 if (!($_SESSION['status'] & pow(2,30))) {
  header('Location: ' . $base_url);
  exit;
 };
 $lev=error_reporting(8);
 if ((empty($_GET['membre'])) && (!(empty($_POST['membre'])))) {
  if (!(empty($_POST['login'])) && !(empty($_POST['pwd1'])) && ($_POST['pwd1']==$_POST['pwd2']) && !(empty($_POST['email'])) ) {
   $status2=0;
   if (isset($_POST['level'])) {
    foreach ($_POST['level'] as $key => $value) {
     $status2+=pow(2,intval($value));
    };
   };
   if (rawurldecode($_POST['membre'])== 'admin') {
    $status2=(pow(2,31)-1);
    $newlogin='admin';
   } else {
    $newlogin=stripslashes($_POST['login']);
   };
   $dbconn=sql_connect('db_phplabdb');
   $result=sql_query("UPDATE membres SET login='$newlogin', password='" . addslashes(htmlentities($_POST['pwd1'])) . "', email='" . addslashes(stripslashes($_POST['email'])) . "', status=$status2 WHERE login='" . rawurldecode($_POST['membre']) . "';",$dbconn);
   if(strlen ($r=sql_last_error($dbconn))) {
    $msg="         <p>\n           <a href=\"" . $base_url . "admin/users.php\"><img src=\"" . $base_url . "images/oops.png\" alt=\"\"></a>&nbsp;<strong>Oops</strong>\n         </p>\n         <p>\n           $r\n         </p>\n";
   } else {
    $msg=0;
   };
  } else {
   $msg=1;
  };
 } elseif ((empty($_POST['membre'])) && (!(empty($_GET['membre'])))) {
  if (!(empty($_GET['remove']))) {
   if(!($_GET['membre']== 'admin')) {
    $dbconn=sql_connect ('db_phplabdb');
    $result=sql_query("DELETE FROM membres WHERE login='" . rawurldecode($_GET['membre']) . "';",$dbconn);
    if(strlen ($r=sql_last_error($dbconn))) {
     $msg="         <p>\n           <a href=\"" . $base_url . "admin/users.php\"><img src=\"" . $base_url . "images/oops.png\" alt=\"\"></a>&nbsp;<strong>Oops</strong>\n         </p>\n         <p>\n           $r\n         </p>\n";
    } else {
     $msg=2;
    };
   } else {
    $msg=3;
   };
  } else {
   $dbconn=sql_connect ('db_phplabdb');
   $result=sql_query("SELECT login, email, status, password FROM membres WHERE login='" . rawurldecode($_GET['membre']) . "';",$dbconn);
   if(strlen ($r=sql_last_error($dbconn))) {
    $msg="         <p>\n           <a href=\"" . $base_url . "admin/users.php\"><img src=\"" . $base_url . "images/oops.png\" alt=\"\"></a>&nbsp;<strong>Oops</strong>\n         </p>\n         <p>\n           $r\n         </p>\n";
   } else {;
    $nombre_membre = sql_num_rows($result);
    if ( $nombre_membre == 1) {
     $row = sql_fetch_row($result);
    } else {
     header('Location: ' . $base_url . 'admin/users.php');
     exit;
    };
   };
  };
 } else {
  header('Location: ' . $base_url . 'admin/users.php');
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
              <?php print '<strong><a href="' . $base_url . 'admin/users.php">' . _("Users") . '</a></strong>'; ?> 
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
           <?php print _("Users Management"); ?> 
         </h1>
<?php 
 if ( (empty($_POST['membre'])) && (!(empty($_GET['membre']))) && (empty($_GET['remove']))) {
?>
        <p>
          <?php print _("Inform all following information:"); ?> 
        </p>
        <form action="<?php print $base_url; ?>admin/users_modif.php" method="post">
          <table summary="">
            <tr>
              <td>
                <label for="login"><?php print _("Username"); ?></label>
              </td>
              <td>
                <input type="text" size="20" maxlength="20" name="login" id="login" value="<?php print $row[0]; ?>"> 
              </td>
            </tr>
            <tr>
              <td>
                <label for="pwd1"><?php print _("Password"); ?></label>
              </td>
              <td>
                <input type="password" size="20" maxlength="20" name="pwd1" id="pwd1" value="<?php print $row[3]; ?>"> 
              </td>
            </tr>
            <tr>
              <td>
                <label for="pwd2"><?php print _("Password 2"); ?></label>
              </td>
              <td>
                <input type="password" size="20" maxlength="20" name="pwd2" id="pwd2" value="<?php print $row[3]; ?>"> 
              </td>
            </tr>
            <tr>
              <td>
                <label for="email"><?php print _("Email"); ?></label>
              </td>
              <td>
                <input type="text" size="20" maxlength="50" name="email" id="email" value="<?php print $row[1]; ?>" accept="text/plain"> 
              </td>
            </tr>
            <tr>
              <td valign="top">
                <?php print _("Power level"); ?> 
              </td>
              <td>
<?php
  if (isset($plugin_name)) {
   foreach($plugin_name as $key => $value) {
    print "                <input type=\"checkbox\" name=\"level[$key]\" id=\"$key\" value=\"" . $plugin_level[$key] . '"' . (($row[2] & pow(2,$plugin_level[$key]))?' checked="checked" ':'') . ">&nbsp;<label for=\"$key\">$value</label><br>\n";
   };
  } else {
   print '                <em>' . _("No plug-in") . "</em>\n"; 
  };
?>
              </td>
            </tr>
            <tr>
              <td colspan="2">
                <input type="hidden" name="membre" value="<?php print rawurlencode($_GET['membre']); ?>"><input name="clear" type="reset" value="<?php print _("Clear"); ?>"> &nbsp; <input type="submit" name="Submit" value="<?php print _("Update"); ?>"> 
              </td>
            </tr>
          </table>
        </form>
<?php
 } else {
  if (!(is_int($msg))) {
   print $msg;
  } else {
   switch ($msg) {
    case 0:
     print "        <p>\n          <a href=\"" . $base_url . "admin/users.php\"><img src=\"" . $base_url . 'images/ok.png" alt=""></a>&nbsp;<strong>' . _("Update successfull") ."</strong>\n        </p>\n";
     break;
    case 1:
     print "        <p>\n          <a href=\"" . $base_url . "admin/users_modif.php?member=" . rawurlencode($_POST['membre']) . "\"><img src=\"" . $base_url . 'images/no.png" alt=""></a>&nbsp;<strong>' . _("Invalid informations") ."</strong>\n        </p>\n";
     break;
    case 2:
     print "        <p>\n          <a href=\"" . $base_url . "admin/users.php\"><img src=\"" . $base_url . 'images/ok.png" alt=""></a>&nbsp;<strong>' . _("User deleted successfull") ."</strong>\n        </p>\n";
     break;
    case 3:
     print "        <p>\n          <a href=\"" . $base_url . "admin/users.php\"><img src=\"" . $base_url . 'images/no.png" alt=""></a>&nbsp;<strong>' . _("Removing 'admin' account deny!") ."</strong>\n        </p>\n";
     break;
   };
  };
 };
?>        
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
