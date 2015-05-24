<?php
 ob_start("ob_gzhandler");
 session_start();
 include_once '../includes/login.inc';
 if (!isset($_SESSION['status'])) {
  header('Location: ' . $base_url);
  exit;
 };
 $lev=error_reporting(8);
 textdomain('oligodb');
 $type=((isset($_POST['type']))?$_POST['type']:'');
 $data=((isset($_POST['data']))?stripslashes(rawurldecode($_POST['data'])):'');
 if ($type=='synthesis') $data=date("Y-m-d",strtotime($data));
 $couple=((isset($_POST['couple']))?$_POST['couple']:'');
 $rmq=((isset($_POST['rmq']))?stripslashes(rawurldecode($_POST['rmq'])):''); 
 $dbconn=sql_connect($plugin_db['oligodb']);
 $nb_true=0;
 $msg='';
 if (!(empty($couple))){ //type= data= rmq=? couple=<array>
  $i=0;
  foreach ($couple as $a) {
   if ($a!='') {
    $vrai[$nb_true++]=addslashes(stripslashes(rawurldecode($a)));
   };
  };
  $result=sql_query("SELECT name, oligo FROM oligo WHERE (name='" . implode("' OR name='",$vrai) . "') ORDER BY name;",$dbconn);
  if(strlen ($r=sql_last_error($dbconn))) {
   $msg .= "            <p>\n            <a href=\"" . $base_url . $plugin . "new_couple.php\"><img src=\"" . $base_url .  "images/oops.png\" alt=\"\"></a>&nbsp;<strong>Oops</strong>\n            </p>\n            <p>\n              $r            </p>\n";
  } else {;
   $nombre_membre=sql_num_rows($result);
   if ($nombre_membre!=0) {
    $msg .= "            <p>\n              " . _("You have selected:") . "\n            </p>\n            <table id=\"list-database\" width=\"100%\" summary=\"\">\n              <tr>\n                <th>\n                  " . _("Name") . "\n                </th>\n                <th>\n                  " . _("Sequence") . "\n                </th>\n                <th>\n                  " . _("Select") . "\n                </th>\n              </tr>\n";
    while($row=sql_fetch_row($result)) {
     $msg .= '              <tr class="' . (($i++%2 ==1) ? 'odd' : 'even' ) . "\">\n                <td class=\"column-1\">\n                  &nbsp;$row[0]&nbsp;\n                </td>\n                <td class=\"column-2\">\n                  &nbsp;<small>" . wordwrap(((strlen($row[1])>39)?(substr($row[1],0,39).'...'):($row[1])),3,' ',1) . "</small>&nbsp;\n                </td>\n                <td class=\"column-3\">\n                  &nbsp;<input type=\"checkbox\" name=\"couple[$i]\" value=\"" . rawurlencode($row[0]) . "\" checked=\"checked\">&nbsp;\n                </td>\n              </tr>\n";
    };
    $msg .= "            </table>\n";
    if ($nombre_membre>2) {
     $msg .= "            <p>\n              <a href=\"" . $base_url . $plugin . "new_couple.php\"><img src=\"" . $base_url .  'images/no.png" alt=""></a>&nbsp;<strong>' . _("You have selected more than two oligonuleotides!") . "</strong>\n            </p>\n";
    };
   };
  };
 };
 if ($nb_true!=2) {
  if (empty($data)){
   $msg .="            <p>\n              " . _("Enter the name of the searched oligonucleotide, or a part of its sequence:") . "\n            </p>\n";
  } else {
   $msg .="            <p>\n              " . _("Select two oligonucleotides to be associated") . ".\n            </p>\n";
   $result=sql_query("SELECT name, oligo FROM oligo WHERE ($type" . (($type=='box')?("='". addslashes(htmlentities($data)) ."'"):(sql_reg(addslashes(htmlentities($data))))) . " " . (($nb_true>0)?"AND (name!='" . implode('\' AND name!=\'',$vrai) . "')":'') . ") ORDER BY " . (($type=='box')?'rank':$type) . ";",$dbconn);
   if(strlen($r=sql_last_error($dbconn))) {
    $msg .= "            <p>\n            <a href=\"" . $base_url . $plugin . "new_couple.php\"><img src=\"" . $base_url .  "images/oops.png\" alt=\"\"></a>&nbsp;<strong>Oops</strong>\n            </p>\n            <p>\n              $r            </p>\n";
   } else {
    $nombre_membre=sql_num_rows($result);
    if ($nombre_membre!=0) {
     $msg .= "            <table id=\"list-database\" width=\"100%\" summary=\"\">\n              <tr>\n                <th>\n                  " . _("Name") . "\n                </th>\n                <th>\n                  " . _("Sequence") . "\n                </th>\n                <th>\n                  " . _("Select") . "\n                </th>\n              </tr>\n";    
     while($row=sql_fetch_row($result)) {
      $msg .= '              <tr class="' . (($i++%2 ==1) ? 'odd' : 'even' ) . "\">\n                <td class=\"column-1\">\n                  &nbsp;$row[0]&nbsp;\n                </td>\n                <td class=\"column-2\">\n                  &nbsp;<small>" . wordwrap(((strlen($row[1])>39)?(substr($row[1],0,39).'...'):($row[1])),3,' ',1) . "</small>&nbsp;\n                </td>\n                <td class=\"column-3\">\n                  &nbsp;<input type=\"checkbox\" name=\"couple[$i]\" value=\"" . rawurlencode($row[0]) . "\">&nbsp;\n                </td>\n              </tr>\n";     
     };
     $msg .= "            </table>\n";
    } else {
     $msg .="            <p>\n              <em>" . _("No result") . "</em>\n            </p>\n";
    };
    $msg .="            <p>\n              " . _("Enter the name of the searched oligonucleotide, or a part of its sequence:") . "\n            </p>\n";
   };
  };
  $msg .='            <table>
              <tr>
                <td>
                  <select name="type">
                    <option value="name">
                      ' . _("Name") . '
                    </option>
                    <option value="oligo">
                      ' . _("Sequence") . '
                    </option>
                    <option value="synthesis">
                      ' . _("Date of synthesis") . '
                    </option>
                    <option value="box">
                      ' . _("Box") . '
                    </option>
                    <option value="freezer">
                      ' . _("Freezer") . '
                    </option>										
                    <option value="notes">
                      ' . _("Notes") . '
                    </option>
                    <option value="barcode">
                      ' . _("ID") . '
                    </option>
                  </select>
                </td>
                <td>
                  <input type="text" size="20" maxlength="100" name="data" value="">
                </td>
              </tr>
              <tr>
                <td>
                </td>
                <td>                
                  <input type="submit" name="submit" value="' . _("Next") . '">
                </td>
              </tr>
            </table>';
 } elseif (empty($rmq)){
  $result=sql_query("SELECT notes FROM couple WHERE ((name1='$vrai[0]' AND name2='$vrai[1]') OR (name1='$vrai[1]' AND name2='$vrai[0]'));",$dbconn);
  if(strlen($r=sql_last_error($dbconn))) {
   $msg .= "            <p>\n            <a href=\"" . $base_url . $plugin . "new_couple.php\"><img src=\"" . $base_url .  "images/oops.png\" alt=\"\"></a>&nbsp;<strong>Oops</strong>\n            </p>\n            <p>\n              $r            </p>\n";
  } else {;
   $nombre_membre=sql_num_rows($result);
   if ($nombre_membre!=0) {
    $row=sql_fetch_row($result);
    $msg .= "            <p>\n            <a href=\"" . $base_url . $plugin . "new_couple.php\"><img src=\"" . $base_url .  'images/no.png" alt=""></a>&nbsp;<strong>' . _("Association") . ' '. stripslashes(implode(' / ', $vrai)) . ' ' . _("already exists") . ' ' . (($row[0]=='')?'':(_("with the following comment:") . " &quot;$row[0]&quot;")) . ".</strong>\n            </p>\n";    
   } else {
    $msg .="            <p>\n              " . _("Enter association comment") . ' ' . stripslashes(implode(' / ', $vrai)) . "&nbsp;:<br>\n              <textarea name=\"rmq\" rows=\"4\" cols=\"60\"></textarea><br>\n              <input type=\"submit\" name=\"submit\" value=\"" . _("Next") . "\"></p>\n";
   };
  };
 } else {
  $result=sql_query("INSERT INTO couple (name1,name2,notes) VALUES ('$vrai[0]','$vrai[1]','" . addslashes(htmlentities($rmq)) . "');",$dbconn);
  if(strlen($r=sql_last_error($dbconn))) {
   $msg .= "            <p>\n            <a href=\"" . $base_url . $plugin . "new_couple.php\"><img src=\"" . $base_url .  "images/oops.png\" alt=\"\"></a>&nbsp;<strong>Oops</strong>\n            </p>\n            <p>\n              $r            </p>\n";
  } else {
   $msg .= "            <p>\n            <a href=\"" . $base_url . $plugin . "\"><img src=\"" . $base_url .  "images/ok.png\" alt=\"\"></a>&nbsp;<strong>" . _("Association added") . "</strong>\n            </p>\n";
  };
 };
 textdomain('phplabdb');
 $status=$_SESSION['status'];
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
.oligo { background: url('images/oligo.png') no-repeat right top; }
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
            <?php print _("New association"); ?> 
          </h3>
          <form action="<?php print $base_url . $plugin; ?>new_couple.php" method="post">
<?php print $msg; ?>
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

