<?php
 ob_start("ob_gzhandler");
 session_start();
 include_once '../../includes/login.inc';
 if (!($_SESSION['status'] & pow(2,$plugin_level['oligodb']))) {
  header('Location: ' . $base_url);
  exit;
 };
 $lev=error_reporting(8);
 $status=$_SESSION['status'];
 $name=((empty($_POST['nom']))?$_GET['name']:$_POST['name']);
 $action=((isset($_POST['action']))?$_POST['action']:'');
 header_start();
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.01//EN">
<html>
  <head>
    <title>
      ..:: phpLabDB::OligoDB ::..
    </title>
    <meta http-equiv="Content-Type" content="text/html">
    <link rel="stylesheet" type="text/css" media="print" href="<?php print $base_url; ?>css/print.css">
    <link rel="stylesheet" type="text/css" media="screen" href="<?php print $base_url; ?>css/screen.css">
    <style type="text/css">
.oligo { background: url('../images/oligo.png') no-repeat right top; }
    </style>
    <script type="text/javascript">
    //<![CDATA[
function confirmation(){
 if (confirm('<?php textdomain('oligodb'); print _("Remove this association?"); textdomain('phplabdb'); ?>')){
  return true;
 } else {
  return false;
 };
};
    //]]>
    </script>
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
        <li>
          <?php print '<strong><a href="' . $base_url . 'database.php">' . _("Databases") . '</a></strong>'; ?> 
          <ul>
<?php
 foreach($plugin_title as $key => $value) {
  if ($key=='oligodb') {
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
      <div id="content" class="oligo">
        <div id="page-main">
<?php  textdomain('oligodb'); ?>
          <h1>
            OligoDB plug-in
          </h1>
          <h3>
            <?php print _("Association modification"); ?> 
          </h3>
<?php
 if (!(empty($name))) {
  $dbconn=sql_connect($plugin_db['oligodb']);
  $result=sql_query("SELECT name1, name2, notes FROM couple WHERE (name1='" . rawurldecode($name[0]) . "') AND (name2='" . rawurldecode($name[1]) . "');",$dbconn);
  if(strlen($r=sql_last_error($dbconn))) {
   print "          <p>\n            <a href=\"" . $base_url . $plugin . "admin/\"><img src=\"" . $base_url . "images/oops.png\" alt=\"\"></a>&nbsp;<strong>Oops</strong>\n          </p>\n          <p>\n            $r\n          </p>\n";
  } else {
   if(sql_num_rows($result)==1) {
    $row=sql_fetch_row($result);
    if ($action==_("Update")) {
     $note=addslashes(trim(stripslashes($_POST['note'])));
     $result=sql_query("UPDATE couple SET notes='$note' WHERE (name1='" . addslashes($row[0]) . "' AND name2='" . addslashes($row[1]) . "');",$dbconn);
     if(strlen($r=sql_last_error($dbconn))) {
      print "          <p>\n            <a href=\"" . $base_url . $plugin . "admin/modif_couple.php?name[0]=$name[0]&amp;name[1]=$name[1]\"><img src=\"" . $base_url . "images/oops.png\" alt=\"\"></a>&nbsp;<strong>Oops</strong>\n          </p>\n          <p>\n            $r\n          </p>\n";
     } else {
      print "          <p>\n            <a href=\"" . $base_url . $plugin . "admin/\"><img src=\"" . $base_url . 'images/ok.png" alt=""></a>&nbsp;<strong>' . _("Modification confirmed") . "</strong>\n          </p>\n";
     };
    } elseif ($action==_("Remove")) {
     $result=sql_query("DELETE FROM couple WHERE (name1='" . addslashes($row[0]) . "' AND name2='" . addslashes($row[1]) . "');",$dbconn);
     if(strlen($r=sql_last_error($dbconn))) {
      print "          <p>\n            <a href=\"" . $base_url . $plugin . "admin/modif_couple.php?name[0]=$name[0]&amp;name[1]=$name[1]\"><img src=\"" . $base_url . "images/oops.png\" alt=\"\"></a>&nbsp;<strong>Oops</strong>\n          </p>\n          <p>\n            $r\n          </p>\n";
     } else {
      print "          <p>\n            <a href=\"" . $base_url . $plugin . "admin/\"><img src=\"" . $base_url . 'images/ok.png" alt=""></a>&nbsp;<strong>' . _("Suppression confirmed") . "</strong>\n          </p>\n";
     };
    } else {
?>
          <form method="post" action="<?php print $base_url . $plugin; ?>admin/modif_couple.php">
            <table summary="">
              <tr>
                <td>
                  &nbsp;<?php print _("Name"); ?>&nbsp;
                </td>
                <td>
                  &nbsp;<strong><input type="hidden" value="<?php print addslashes($row[0]); ?>" name="name[0]"><input type="hidden" value="<?php print addslashes($row[1]); ?>" name="name[1]"><acronym title="<?php print _("oligonucleotide association (read only)"); ?>"><?php print $row[0] . " / " . $row[1]; ?></acronym></strong>&nbsp;
                </td>
              </tr>
              <tr>
                <td>
                  &nbsp;<label for="note"><?php print _("Comment"); ?></label>&nbsp;
                </td>
                <td>
                &nbsp;<textarea name="note" id="note" rows="2" cols="50" title="<?php print _("Comment"); ?>"><?php print $row[2]; ?></textarea>&nbsp;
                </td>
              </tr>
            </table>
            <div>
              <input type="submit" name="action" value="<?php print _("Update"); ?>">
            </div>
          </form>
          <form method="post" action="<?php print $base_url . $plugin; ?>admin/modif_couple.php" onsubmit="return confirmation();">
            <div>
              <input type="hidden" value="<?php print addslashes($row[0]); ?>" name="nom[0]"><input type="hidden" value="<?php print addslashes($row[1]); ?>" name="nom[1]"><input type="submit" name="action" value="<?php print _("Remove"); ?>">
            </div>
          </form>
<?php
    };
   } else {
    print "          <p>\n            <a href=\"" . $base_url . $plugin . "admin/\"><img src=\"" . $base_url . 'images/no.png" alt=""></a>&nbsp;<strong>' . _("Association unknown!") . "</strong>\n          </p>\n";
   };
  };
 };
 textdomain('phplabdb');
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
