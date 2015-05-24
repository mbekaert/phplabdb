<?php
 ob_start("ob_gzhandler");
 session_start();
 include_once '../../includes/login.inc';
 if (!($_SESSION['status'] & pow(2,$plugin_level['oligodb']))) {
  header('Location: ' . $base_url);
  exit;
 };
 $lev=error_reporting(8); 
 function newtime($oldate) {
  $list_date=getdate(strtotime($oldate));
  $yearlimit = date("Y");
  $day = '<select name="date_D" title="' . _("day of synthesis") . '">';
  for ($i=1 ; $i<32; $i++) 
   $day .= '<option' . (($list_date['mday']==$i)? ' selected="selected">':'>') . $i . '</option>';
  $day .= '</select>';
  $month = '<select name="date_M" title="' . _("month of synthesis") . '">';
  for ($i=1 ; $i<13; $i++) 
   $month .= '<option' . (($list_date['mon']==$i)? ' selected="selected">':'>') . $i . '</option>';
  $month .= '</select>';
  $year = '<select name="date_Y" title="' . _("year of synthesis") . '">';
  for ($i=1985 ; $i<=($yearlimit); $i++)
   $year .= '<option' . (($list_date['year']==$i)? ' selected="selected">':'>') . $i . '</option>';
  $year .= '</select>';
  switch (((isset($_COOKIE['lang']))?$_COOKIE['lang']:'en_US')) {
   case 'fr_FR':
    $value= $day . ' &nbsp;/&nbsp; ' . $month . ' &nbsp;/&nbsp; ' . $year;
    break;
   default:
    $value= $month . ' &nbsp;/&nbsp; ' . $day . ' &nbsp;/&nbsp; ' . $year;
  };
  return $value;
 };
 $status=$_SESSION['status'];
 $barcode=intval(((empty($_POST['barcode']))?$_GET['barcode']:$_POST['barcode']));
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
 if (confirm('<?php textdomain('oligodb'); print _("Remove this oligonucleotide?"); textdomain('phplabdb'); ?>')){
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
            <?php print _("Modification"); ?> 
          </h3>
<?php
 if ($barcode>0) {
  $dbconn=sql_connect($plugin_db['oligodb']);
  $result=sql_query("SELECT name, oligo, synthesis, box, rank, freezer, barcode, notes FROM oligo WHERE barcode=$barcode;",$dbconn);
  if(strlen ($r=sql_last_error($dbconn))) {
   print "          <p>\n            <a href=\"" . $base_url . $plugin . "admin/\"><img src=\"" . $base_url . "images/oops.png\" alt=\"\"></a>&nbsp;<strong>Oops</strong>\n          </p>\n          <p>\n            $r\n          </p>\n";  
  } else {
   if(sql_num_rows($result)==1) {
    $row = sql_fetch_row($result);
    if ($action==_("Update")) {
     if( (strlen(trim($_POST['sequence']))>1) && (intval(trim($_POST['box']))>0) && (intval(trim($_POST['rank']))>0) ) {
      $seq=addslashes(trim(stripslashes(strtoupper(str_replace(" ", "", $_POST['sequence'])))));
      $box=intval(trim($_POST['box']));
      $rank=intval(trim($_POST['rank']));
      $note=addslashes(htmlentities(trim(stripslashes($_POST['note']))));
      $newdate=mktime( 0,0,0, intval($_POST['date_M']), intval($_POST['date_D']), intval($_POST['date_Y']));
  		$freezer=((isset($_POST['freezer']) && trim(stripslashes($_POST['freezer']))!='')?("'".addslashes(htmlentities(trim(stripslashes($_POST['freezer']))))."'"):'NULL');
      $result=sql_query("SELECT name FROM oligo WHERE (freezer=$freezer AND box=$box AND rank=$rank AND name!='" . addslashes($row[0]) . "');",$dbconn);
      if (sql_num_rows($result)==1) {
       $row=sql_fetch_row($result);
       print "          <p>\n            <a href=\"" . $base_url . $plugin . "admin/modif.php?barcode=$barcode\"><img src=\"" . $base_url . 'images/no.png" alt=""></a>&nbsp;<strong>' . _("The place") . ((isset($_POST['freezer']) && trim(stripslashes($_POST['freezer']))!='')?(' <strong>'.htmlentities(trim(stripslashes($_POST['freezer']))).'</strong> -'):'') . " $box ($rank) " . _("is already used by") . " '$row[0]'</strong>\n          </p>\n";
      } else {
       $result=sql_query("UPDATE oligo SET oligo='$seq', synthesis='" . date("Y-m-d",$newdate) . "', box=$box, rank=$rank, freezer=$freezer, notes='$note' WHERE barcode=$barcode;",$dbconn);
       if(strlen ($r=sql_last_error($dbconn))) {
        print "          <p>\n            <a href=\"" . $base_url . $plugin . "admin/modif.php?barcode=$barcode\"><img src=\"" . $base_url . "images/oops.png\" alt=\"\"></a>&nbsp;<strong>Oops</strong>\n          </p>\n          <p>\n            $r\n          </p>\n";
       } else {
        print "          <p>\n            <a href=\"" . $base_url . $plugin . "admin/\"><img src=\"" . $base_url . 'images/ok.png" alt=""></a>&nbsp;<strong>' . _("Modifications confirmed") . "</strong>\n          </p>\n";
       };
      };
     } else {
      print "          <p>\n           <a href=\"" . $base_url . $plugin . "admin/modif.php?barcode=$barcode\"><img src=\"" . $base_url . 'images/no.png" alt=""></a>&nbsp;<strong>' . _("Update error") . "</strong>\n          </p>\n";
     };
    } elseif ($action==_("Remove")) {
     $name=addslashes($_POST['oligo']);
     $result=sql_query("DELETE FROM couple WHERE (name1='$name' OR name2='$name');",$dbconn);
     if(strlen ($r=sql_last_error($dbconn))) {
      print "          <p>\n            <a href=\"" . $base_url . $plugin . "admin/modif.php?barcode=$barcode\"><img src=\"" . $base_url . "images/oops.png\" alt=\"\"></a>&nbsp;<strong>Oops</strong>\n          </p>\n          <p>\n            $r\n          </p>\n";
     } else {
      $result=sql_query("DELETE FROM oligo WHERE name='$name';",$dbconn);
      if(strlen ($r=sql_last_error($dbconn))) {
       print "          <p>\n            <a href=\"" . $base_url . $plugin . "admin/modif.php\"><img src=\"" . $base_url . "images/oops.png\" alt=\"\"></a>&nbsp;<strong>Oops</strong>\n          </p>\n          <p>\n            $r\n          </p>\n";
      } else {
        print "          <p>\n            <a href=\"" . $base_url . $plugin . "admin/\"><img src=\"" . $base_url . 'images/ok.png" alt=""></a>&nbsp;<strong>' . _("Suppression confirmed") . "</strong>\n          </p>\n";
      };
     };
    } else {
?>
          <form method="post" action="<?php print $base_url . $plugin; ?>admin/modif.php">
            <table summary="">
              <tr>
                <td>
                  &nbsp;<?php print _("Name"); ?>&nbsp;
                </td>
                <td>
                  &nbsp;<strong><input type="hidden" value="<?php print $barcode; ?>" name="barcode"><input type="hidden" value="<?php print $row[0]; ?>" name="oligo"><acronym title="<?php print _("Oligonucleotide name (read only)"); ?>"><?php print $row[0]; ?></acronym></strong>&nbsp;
                </td>
              </tr>
              <tr>
                <td>
                  &nbsp;<label for="sequence"><?php print _("Sequence"); ?></label>&nbsp;
                </td>
                <td>
                  &nbsp;<textarea name="sequence" id="sequence" rows="2" cols="50" title="<?php print _("Oligonucleotide sequence"); ?>"><?php print $row[1]; ?></textarea>&nbsp;
                </td>
              </tr>
              <tr>
                <td>
                  &nbsp;<?php print _("Date of synthesis"); ?>&nbsp;
                </td>
                <td>
                  &nbsp;<?php print newtime($row[2]); ?>&nbsp;
                </td>
              </tr>
              <tr>
                <td>
                  &nbsp;<label for="freezer"><?php print _("Freezer"); ?></label>&nbsp;
                </td>
                <td>
                  &nbsp;<input type="text" id="freezer" maxlength="20" name="freezer" title="<?php print _("Freezer"); ?>" value="<?php print $row[5]; ?>">&nbsp;
                </td>
              </tr>
              <tr>
                <td>
                  &nbsp;<label for="box"><?php print _("Box"); ?></label>&nbsp;
                </td>
                <td>
                  &nbsp;<select name="box" id="box" title="<?php print _("Box"); ?>"><option></option><?php for ($i=1 ; $i<101; $i++) {print "<option value=\"$i\"" . ( ($row[3]==$i)? " selected>":">") . $i . "</option>"; }; ?></select>&nbsp;
                </td>
              </tr>
              <tr>
                <td>
                  &nbsp;<label for="rank"><?php print _("Rank"); ?></label>&nbsp;
                </td>
                <td>
                  &nbsp;<select name="rank" id="rank"title="<?php print _("Place on the box"); ?>"><option></option><?php for ($i=1 ; $i<101; $i++) {print "<option value=\"$i\"" . ( ($row[4]==$i)? " selected>":">") . $i . "</option>"; }; ?></select>&nbsp;
                </td>
              </tr>
              <tr>
                <td>
                  &nbsp;<label for="notes"><?php print _("Comment"); ?></label>&nbsp;
                </td>
                <td>
                  &nbsp;<textarea name="note" id="note" cols="50" rows="6" title="<?php print _("Comment"); ?>"><?php print $row[7]; ?></textarea>&nbsp;
                </td>
              </tr>
            </table>
            <div>
              <input type="submit" name="action" value="<?php print _("Update"); ?>">
            </div>
          </form>
          <form method="post" action="<?php print $base_url . $plugin; ?>admin/modif.php" onsubmit="return confirmation();">
            <div>
              <input type="hidden" value="<?php print $barcode; ?>" name="barcode"><input type="hidden" value="<?php print $row[0]; ?>" name="oligo"><input type="submit" name="action" value="<?php print _("Remove"); ?>">
            </div>
          </form>
<?php
    };
   } else {
    print "          <p>\n            <a href=\"" . $base_url . $plugin . "admin/\"><img src=\"" . $base_url . 'images/no.png" alt=""></a>&nbsp;<strong>' . _("Oligonucleotide unknown!") . "</strong>\n          </p>\n";
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
