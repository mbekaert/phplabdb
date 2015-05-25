<?php
 ob_start("ob_gzhandler");
 session_start();
 include_once '../../includes/login.inc';
 $lang=((isset($_COOKIE['lang']))?substr($_COOKIE['lang'],0,2):'en');
 if (!($_SESSION['status'] & pow(2,$plugin_level['seeddb']))) {
  header('Location: ' . $base_url);
  exit;
 };
 $lev=error_reporting(8);
 function read_table($dbconn,$name,$lang) {
  $table=array();
  $result=sql_query("SELECT id, legend FROM $name WHERE lang='$lang' ORDER BY id;",$dbconn);
  if(!(strlen($r=sql_last_error($dbconn)))) {
   while( $row = sql_fetch_row($result) ) {
    $table[$row[0]]=htmlentities($row[1]);
   };
  };
  return $table;
 };
 function species($dbconn) {
  $species=array();
  $result=sql_query("SELECT id, name FROM species ORDER BY id;",$dbconn);
  if(!(strlen($r=sql_last_error($dbconn)))) {
   while( $row = sql_fetch_row($result) ) {
    $species[$row[0]]=htmlentities($row[1]);
   };
  };
  return $species;
 };
 function newtime($oldate) {
  $list_date=getdate(strtotime($oldate));
  $yearlimit = date("Y");
  $day = '<select name="date_D" title="' . _("day") . '">';
  for ($i=1 ; $i<32; $i++) 
   $day .= '<option' . (($list_date['mday']==$i)? ' selected="selected">':'>') . $i . '</option>';
  $day .= '</select>';
  $month = '<select name="date_M" title="' . _("month") . '">';
  for ($i=1 ; $i<13; $i++) 
   $month .= '<option' . (($list_date['mon']==$i)? ' selected="selected">':'>') . $i . '</option>';
  $month .= '</select>';
  $year = '<select name="date_Y" title="' . _("year") . '">';
  for ($i=1950 ; $i<=($yearlimit); $i++)
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
 header_start();
 $dbconn=sql_connect($plugin_db['seeddb']);
 if ((empty($_GET['barcode'])) && (!(empty($_POST['barcode'])))) {
  $modif_raw['barcode']=intval($_POST['barcode']);
  $modif_raw['species']=$_POST['species'];
  $modif_raw['date']=mktime( 0,0,0, intval($_POST['date_M']), intval($_POST['date_D']), intval($_POST['date_Y']));
  $modif_raw['ref']=stripslashes(trim($_POST['ref']));
  $modif_raw['note']=stripslashes(trim($_POST['note']));
  if (isset($_POST['prosp_Y'])) { //prospection
  $modif_raw['prospection']=stripslashes(trim($_POST['prospection']));
  $modif_raw['pdate']=mktime( 0,0,0, intval($_POST['prosp_M']), 1, intval($_POST['prosp_Y']));
  $modif_raw['country']=stripslashes(trim($_POST['country']));
  if(empty($modif_raw['prospection'])||empty($modif_raw['country'])||($modif_raw['species']=='')) {
   $msg=1;
   } else {
   $result=sql_query('UPDATE prospection SET prospection=\'' . addslashes(htmlentities($modif_raw['prospection'])) . '\', vernacular=' . ((empty($modif_raw['vernacular']))?'NULL':("'" . addslashes(htmlentities($modif_raw['vernacular'])) . "'")) . ', species=' . intval($modif_raw['species']) . ', date=\'' . date("Y-m-d",$modif_raw['pdate']) . '\', country=' . ((empty($modif_raw['country']))?'NULL':("'" . addslashes(htmlentities($modif_raw['country'])) . "'")) . ', locality=' . ((empty($modif_raw['locality']))?'NULL':("'" . addslashes(htmlentities($modif_raw['locality'])) . "'")) . ', latitude=' . ((empty($modif_raw['latitude']))?'NULL':("'" . addslashes(htmlentities($modif_raw['latitude'])) . "'")) . ', longitude=' . ((empty($modif_raw['longitude']))?'NULL':("'" . addslashes(htmlentities($modif_raw['longitude'])) . "'")) . ', altitude=' . ((empty($modif_raw['altitude']))?'NULL':("'" . addslashes(htmlentities($modif_raw['altitude'])) . "'")) . ', ethnos=' . ((empty($modif_raw['ethnos']))?'NULL':("'" . addslashes(htmlentities($modif_raw['ethnos'])) . "'")) . ', nature=' . ((is_numeric($_POST['nature']))?intval($_POST['nature']):'NULL') . ', form=' . ((is_numeric($_POST['form']))?intval($_POST['form']):'NULL') . ', size=' . ((is_numeric($_POST['size']))?intval($_POST['size']):'NULL') . ', distribution=' . ((is_numeric($_POST['distribution']))?intval($_POST['distribution']):'NULL') . ', weather=' . ((is_numeric($_POST['weather']))?intval($_POST['weather']):'NULL') . ', precocity=' . ((is_numeric($_POST['precocity']))?intval($_POST['precocity']):'NULL') . ', note=' . ((empty($modif_raw['note']))?'NULL':("'" . addslashes(htmlentities($modif_raw['note'])) . "'")) . ' WHERE barcode=' . $modif_raw['barcode'] . ';',$dbconn);
   if(!(strlen($r=sql_last_error($dbconn)))) {
    $result=sql_query('UPDATE seeds SET stock=' . ((is_numeric($_POST['seeds']))?intval($_POST['seeds']):'NULL') . ', date=\'' . date("Y-m-d",$modif_raw['date']) . '\', ref=\'' . addslashes(htmlentities($modif_raw['ref'])) . '\', father=NULL, fbarcode=NULL, mother=NULL, mbarcode=NULL, crosstype=NULL, species=' . intval($modif_raw['species']) . ', note=' . ((empty($modif_raw['note']))?'NULL':("'" . addslashes(htmlentities($modif_raw['note'])) . "'")) . ' WHERE barcode=' . $modif_raw['barcode'] . ';',$dbconn);
    if(!(strlen($r=sql_last_error($dbconn)))) {
     $msg=0;
     } else {
     $msg="         <p>\n           <a href=\"" . $base_url . $plugin . "admin/motif.php?barcode=" . $_POST['barcode'] . "\"><img src=\"" . $base_url . "images/oops.png\" alt=\"\"></a>&nbsp;<strong>Oops</strong>\n         </p>\n         <p>\n           $r\n         </p>\n";
    };
    } else {
    $msg="         <p>\n           <a href=\"" . $base_url . $plugin . "admin/motif.php?barcode=" . $_POST['barcode'] . "\"><img src=\"" . $base_url . "images/oops.png\" alt=\"\"></a>&nbsp;<strong>Oops</strong>\n         </p>\n         <p>\n           $r\n         </p>\n";
   };
  };
  } else {
  if((empty($_POST['father'])&&empty($_POST['mother']))||(!(is_numeric($_POST['cross'])))) {
   $msg=1;
   } else {
   $modif_raw['cross']=intval($_POST['cross']);
   if (!(empty($_POST['father']))) {
    $modif_raw['father']=stripslashes(trim($_POST['father']));
    $result=sql_query("SELECT barcode, ref, species FROM seeds WHERE (ref='" . addslashes(htmlentities($modif_raw['father'])) . "'" . ((is_numeric($modif_raw['father']))?(' OR barcode=' . intval($modif_raw['father'])):'') . ");",$dbconn);
    if (sql_num_rows($result)==1) {
     $row = sql_fetch_row($result);
     $modif_raw['father']=$row[1];
     $modif_raw['fbarcode']=$row[0];
     $modif_raw['fspecies']=$row[2];
    };
   } elseif (isset($_POST['population'])) {
    $modif_raw['father']='none';
   };
   if (!(empty($_POST['mother']))) {
    $modif_raw['mother']=stripslashes(trim($_POST['mother']));
    $result=sql_query("SELECT barcode, ref, species FROM seeds WHERE (ref='" . addslashes(htmlentities($modif_raw['mother'])) . "'" . ((is_numeric($modif_raw['mother']))?(' OR barcode=' . intval($modif_raw['mother'])):'') . ");",$dbconn);
    if (sql_num_rows($result)==1) {
     $row = sql_fetch_row($result);
     $modif_raw['mother']=$row[1];
     $modif_raw['mbarcode']=$row[0];
     $modif_raw['mspecies']=$row[2];
    };
   };
   switch ($modif_raw['cross']) {
    case 1:
    if (isset($modif_raw['mother'])&&(!(isset($modif_raw['father']))||(isset($modif_raw['father'])&&($modif_raw['father']==$modif_raw['mother'])))) {
     $modif_raw['father']=$modif_raw['mother'];
     if (isset($modif_raw['mbarcode'])) $modif_raw['fbarcode']=$modif_raw['mbarcode'];
     if (isset($modif_raw['mspecies'])) $modif_raw['fspecies']=$modif_raw['mspecies'];
     $ok=true;
    };
    break;
    case 2:
    if (isset($modif_raw['mother'])&&isset($modif_raw['father'])&&($modif_raw['father']!=$modif_raw['mother'])) {
     if (($modif_raw['father']=='none') && (isset($modif_raw['mspecies']))) $modif_raw['fspecies']=$modif_raw['mspecies'];
     $ok=true;
    };
    break;
    case 3:
    if (!(isset($modif_raw['mother']))&&isset($modif_raw['father'])&&($modif_raw['father']!='none')) {
     $ok=true;
     if (isset($modif_raw['fspecies'])) $modif_raw['mspecies']=$modif_raw['fspecies'];
    };
   };
   if (isset($modif_raw['mspecies']) && isset($modif_raw['fspecies'])) {
    if ($modif_raw['mspecies']==$modif_raw['fspecies']) {
     $modif_raw['species2']=$modif_raw['mspecies'];
     } else {
     $modif_raw['species2']=0;
    };
   }
   if (isset($ok)) {
    if(isset($modif_raw['species2']) && ($modif_raw['species2']!=$modif_raw['species'])) {
     $species=species($dbconn,'species');
     $msg="          <p>\n            <a href=\"" . $base_url . $plugin . "admin/motif.php?barcode=" . $_POST['barcode'] . "\"><img src=\"" . $base_url . 'images/no.png" alt=""></a>&nbsp;<strong>' . _("Warning:") . '</strong> ' . _("Informed species") . ' (<em>' . $species[$modif_raw['species']] . '</em>) ' . _("is differente of calculated species") . ' (<em>' . $species[$modif_raw['species2']] . '</em>)!. ' . _("Informed species was used") . ".\n          </p>\n";
    };
    $result=sql_query('UPDATE seeds SET stock=' . ((is_numeric($_POST['seeds']))?intval($_POST['seeds']):'NULL') . ', date=\'' . date("Y-m-d",$modif_raw['date']) . '\', ref=\'' . addslashes(htmlentities($modif_raw['ref'])) . '\', father=' . ((isset($modif_raw['father']))?("'" . addslashes(htmlentities($modif_raw['father'])) . "'"):'NULL') . ', fbarcode=' . ((isset($modif_raw['fbarcode']))?$modif_raw['fbarcode']:'NULL') . ', mother=' . ((isset($modif_raw['mother']))?("'" . addslashes(htmlentities($modif_raw['mother'])) . "'"):'NULL') . ', mbarcode=' . ((isset($modif_raw['mbarcode']))?$modif_raw['mbarcode']:'NULL') .  ', crosstype=' . $modif_raw['cross'] . ', species=' . intval($modif_raw['species']) . ', note=' . ((empty($modif_raw['note']))?'NULL':("'" . addslashes(htmlentities($modif_raw['note'])) . "'")) . ' WHERE barcode=' . $modif_raw['barcode'] . ';',$dbconn);
    if(!(strlen($r=sql_last_error($dbconn)))) {
     $msg.= "        <p>\n          <a href=\"" . $base_url . $plugin . "admin/\"><img src=\"" . $base_url . 'images/ok.png" alt=""></a>&nbsp;<strong>' . _("Update successfull") . "</strong>\n        </p>\n";
     } else {
     $msg.="         <p>\n           <a href=\"" . $base_url . $plugin . "admin/motif.php?barcode=" . $_POST['barcode'] . "\"><img src=\"" . $base_url . "images/oops.png\" alt=\"\"></a>&nbsp;<strong>Oops</strong>\n         </p>\n         <p>\n           $r\n         </p>\n";
    };
    } else {
    $msg="          <p>\n            <a href=\"" . $base_url . $plugin . "admin/motif.php?barcode=" . $_POST['barcode'] . "\"><img src=\"" . $base_url . 'images/no.png" alt=""></a>&nbsp;<strong>' . _("Cross type and parents are not consistant") . "</strong>\n          </p>\n";
   };
  };
 };
 } elseif ((empty($_POST['barcode'])) && (!(empty($_GET['barcode'])))) {
 if (isset($_GET['remove'])) {
  $result=sql_query('DELETE FROM seeds WHERE barcode=' . intval($_GET['barcode']) . ';',$dbconn);
  if(strlen ($r=sql_last_error($dbconn))) {
   $msg="         <p>\n           <a href=\"" . $base_url . $plugin . "admin/\"><img src=\"" . $base_url . "images/oops.png\" alt=\"\"></a>&nbsp;<strong>Oops</strong>\n         </p>\n         <p>\n           $r\n         </p>\n";
   } else {
   if (isset($_GET['prospection'])) {
    $result=sql_query('DELETE FROM prospection WHERE barcode=' . intval($_GET['barcode']) . ';',$dbconn);
    if(strlen ($r=sql_last_error($dbconn))) {
     $msg="         <p>\n           <a href=\"" . $base_url . $plugin . "admin/\"><img src=\"" . $base_url . "images/oops.png\" alt=\"\"></a>&nbsp;<strong>Oops</strong>\n         </p>\n         <p>\n           $r\n         </p>\n";
     } else {
     $msg=2;
    };
   };
   $msg=2;
  };
  } else {
  $result=sql_query('SELECT barcode, ref, date, species, stock, father, fbarcode, mother, mbarcode, crosstype, note FROM seeds WHERE barcode=' . intval($_GET['barcode']) . ';',$dbconn);
  if(strlen ($r=sql_last_error($dbconn))) {
   $msg="         <p>\n           <a href=\"" . $base_url . $plugin . "admin/\"><img src=\"" . $base_url . "images/oops.png\" alt=\"\"></a>&nbsp;<strong>Oops</strong>\n         </p>\n         <p>\n           $r\n         </p>\n";
   } else {
   $nombre_membre = sql_num_rows($result);
   if ( $nombre_membre == 1) {
    $row = sql_fetch_row($result);
    if(!(isset($row[9])) && isset($_GET['prospection'])) {
     $result2=sql_query('SELECT barcode, date, prospection, species, vernacular, country, locality, latitude, longitude, altitude, ethnos, nature, form, size, distribution, weather, precocity, note FROM prospection WHERE barcode=' . intval($_GET['barcode']) . ';',$dbconn);
     if(!(strlen($r=sql_last_error($dbconn))) && (sql_num_rows($result2)==1)) {
      $row2 = sql_fetch_row($result2);
      } else {
      header('Location: ' . $base_url . $plugin_dir['seeddb'] . 'admin/');
     };
    };
    } else {
    header('Location: ' . $base_url . $plugin_dir['seeddb'] . 'admin/');
    exit;
   };
  };
 };
 } else {
 header('Location: ' . $base_url . $plugin_dir['seeddb'] . 'admin/');
 exit;
};
$status=$_SESSION['status'];
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
 if ( (empty($_POST['barcode']) && !(empty($_GET['barcode']))) && !(isset($_GET['remove'])) ) {
?>
          <h3>
            <?php print _("Update"); ?>
          </h3>
          <form action="<?php print $base_url . $plugin; ?>admin/modif.php" method="post">
          <table summary="">
            <tr>
              <td>
                &nbsp;<?php print _("ID"); ?>&nbsp;
              </td>
              <td>
                <em><?php  print $row[0]; ?></em><input type="hidden" name="barcode" value="<?php print $row[0]; ?>">
              </td>
             </tr>
              <tr>
                <td>
                  <label for="ref">&nbsp;<?php print _("Reference"); ?>&nbsp;</label>
                </td>
                <td>
                  <input type="text" size="10" maxlength="50" name="ref" id="ref" value="<?php print $row[1]; ?>">
                </td>
              </tr>
<?php if(isset($row2)) { ?>
              <tr>
                <td>
                  <label for="prospection">&nbsp;<?php print _("Prospection"); ?>&nbsp;</label>
                </td>
                <td>
                  <input type="text" size="20" maxlength="50" name="prospection" id="prospection" value="<?php print $row2[2]; ?>">
                </td>
              </tr>
              <tr>
                <td>
                  <label for="vernacular">&nbsp;<?php print _("Vernacular name"); ?>&nbsp;</label>
                </td>
                <td>
                  <input type="text" size="20" maxlength="50" name="vernacular" id="vernacular"<?php print ((isset($row2[4]))?" value=\"$row2[4]\"":'') ?>>
                </td>
              </tr>
              <tr>
                <td>
                  &nbsp;<?php print _("Prospection date"); ?>&nbsp;
                </td>
                <td>
                  <select name="prosp_M" title="<?php print _("month"); ?>"><?php $liste_date=getdate(strtotime($row2[1])); for ($i=1 ; $i<13; $i++) {print "<option" . ( ($liste_date['mon']==$i)? " selected>":">") . $i . "</option>"; }; ?></select> &nbsp;/&nbsp; <select name="prosp_Y" title="<?php print _("year"); ?>"><?php $annee = date("Y"); for ($i=1950 ; $i<=($annee); $i++) {print "<option" . ( ($liste_date['year']==$i)? " selected>":">") . $i . "</option>"; }; ?></select>
                </td>
              </tr>
<?php } else { ?>
              <tr>
                <td>
                  <label for="cross">&nbsp;<?php print _("Cross type"); ?>&nbsp;</label>
                </td>
                <td>
                  <select name="cross" id="cross" title="<?php print _("Cross type"); ?>"><?php foreach(read_table($dbconn,'crosstype',$lang) as $key => $value) print "<option value=\"$key\"" . (($key==$row[9])?' selected="selected"':''). ">$value</option>"; ?></select>
                </td>
              </tr>
              <tr>
                <td>
                  <label for="cross">&nbsp;<?php print _("Father"); ?>&nbsp;</label>
                </td>
                <td>
                  <input type="text" size="10" maxlength="50" name="father" id="father" title="<?php print _("pollen [reference or ID]"); ?>"<?php print ((isset($row[5]) && ($row[5]!='none'))?" value=\"$row[5]\"":'') ?>> or <input type="checkbox" name="population" id="population" title="<?php print _("free union (population)"); ?>" <?php print ((isset($row[5]) && ($row[5]=='none'))?" checked=\"checked\"":'') ?>>&nbsp;<label for="population"><?php print _("free union (population)"); ?></label>
                </td>
              </tr>
              <tr>
                <td>
                  <label for="cross">&nbsp;<?php print _("Mother"); ?>&nbsp;</label>
                </td>
                <td>
                  <input type="text" size="10" maxlength="50" name="mother" id="mother" title="<?php print _("carry seed [reference or ID]"); ?>"<?php print ((isset($row[7]))?" value=\"$row[7]\"":'') ?>>
                </td>
              </tr>
<?php }; ?>
              <tr>
                <td>
                  <label for="species">&nbsp;<?php print _("Species"); ?>&nbsp;</label>
                </td>
                <td>
                  <select name="species" id="species" title="Species"><?php foreach(species($dbconn,'species') as $key => $value) print "<option value=\"$key\"" . (($key==$row[3])?' selected="selected"':''). ">$value</option>"; ?></select>
                </td>
              </tr>
              <tr>
                <td>
                  &nbsp;<?php print _("Entry date"); ?>&nbsp;
                </td>
                <td>
                  <?php print newtime($row[2]); ?> 
                </td>
              </tr>
              <tr>
                <td>
                  <label for="seeds">&nbsp;<?php print _("Seed number"); ?>&nbsp;</label>
                </td>
                <td>
                  <input type="text" size="5" maxlength="10" name="seeds" id="seeds"<?php print ((isset($row[4]))?" value=\"$row[4]\"":'') ?>>
                </td>
              </tr>
<?php if(isset($row2)) { ?>
              <tr>
                <td>
                  <label for="country">&nbsp;<?php print _("Country"); ?>&nbsp;</label>
                </td>
                <td>
                  <input type="text" size="20" maxlength="50" name="country" id="country"<?php print ((isset($row2[5]))?" value=\"$row2[5]\"":'') ?>>
                </td>
              </tr>
              <tr>
                <td>
                  <label for="locality">&nbsp;<?php print _("Locality"); ?>&nbsp;</label>
                </td>
                <td>
                  <input type="text" size="20" maxlength="50" name="locality" id="locality"<?php print ((isset($row2[6]))?" value=\"$row2[6]\"":'') ?>>
                </td>
              </tr>
              <tr>
                <td>
                  <label for="latitude">&nbsp;<?php print _("Latitude"); ?>&nbsp;</label>
                </td>
                <td>
                  <input type="text" size="10" maxlength="20" name="latitude" id="latitude"<?php print ((isset($row2[7]))?" value=\"$row2[7]\"":'') ?>>
                </td>
              </tr>
              <tr>
                <td>
                  <label for="longitude">&nbsp;<?php print _("Longitude"); ?>&nbsp;</label>
                </td>
                <td>
                  <input type="text" size="10" maxlength="20" name="longitude" id="longitude"<?php print ((isset($row2[8]))?" value=\"$row2[8]\"":'') ?>>
                </td>
              </tr>
              <tr>
                <td>
                  <label for="altitude">&nbsp;<?php print _("Altitude"); ?>&nbsp;</label>
                </td>
                <td>
                  <input type="text" size="10" maxlength="20" name="altitude" id="altitude"<?php print ((isset($row2[9]))?" value=\"$row2[9]\"":'') ?>>
                </td>
              </tr>
              <tr>
                <td>
                  <label for="ethnos">&nbsp;<?php print _("Ethnos group"); ?>&nbsp;</label>
                </td>
                <td>
                  <input type="text" size="20" maxlength="50" name="ethnos" id="ethnos" title="<?php print _("Ethnos group"); ?>"<?php print ((isset($row2[10]))?" value=\"$row2[10]\"":'') ?>>
                </td>
              </tr>
              <tr>
                <td>
                  <label for="nature">&nbsp;<?php print _("Landscape nature"); ?>&nbsp;</label>
                </td>
                <td>
                  <select name="nature" id="nature" title="<?php print _("Nature of the collecting site"); ?>"><option></option><?php foreach(read_table($dbconn,'nature',$lang) as $key => $value) print "<option value=\"$key\"" . (($key==$row2[11])?' selected="selected"':''). ">$value</option>"; ?></select>
                </td>
              </tr>
              <tr>
                <td>
                  <label for="form">&nbsp;<?php print _("Botanical form"); ?>&nbsp;</label>
                </td>
                <td>
                  <select name="form" id="form" title="<?php print _("Botanical form"); ?>"><option></option><?php foreach(read_table($dbconn,'form',$lang) as $key => $value) print "<option value=\"$key\"" . (($key==$row2[12])?' selected="selected"':''). ">$value</option>"; ?></select>
                </td>
              </tr>
              <tr>
                <td>
                  <label for="size">&nbsp;<?php print _("Population size"); ?>&nbsp;</label>
                </td>
                <td>
                  <select name="size" id="size" title="<?php print _("Population size"); ?>"><option></option><?php foreach(read_table($dbconn,'size',$lang) as $key => $value) print "<option value=\"$key\"" . (($key==$row2[13])?' selected="selected"':''). ">$value</option>"; ?></select>
                </td>
              </tr>
              <tr>
                <td>
                  <label for="distribution">&nbsp;<?php print _("Plant distribution"); ?>&nbsp;</label>
                </td>
                <td>
                  <select name="distribution" id="distribution" title="<?php print _("Distribution pattern"); ?>"><option></option><?php foreach(read_table($dbconn,'distribution',$lang) as $key => $value) print "<option value=\"$key\"" . (($key==$row2[14])?' selected="selected"':''). ">$value</option>"; ?></select>
                </td>
              </tr>
              <tr>
                <td>
                  <label for="weather">&nbsp;<?php print _("Weather/Culture type"); ?>&nbsp;</label>
                </td>
                <td>
                  <select name="weather" id="weather" title="<?php print _("Local weather"); ?>"><option></option><?php foreach(read_table($dbconn,'weather',$lang) as $key => $value) print "<option value=\"$key\"" . (($key==$row2[15])?' selected="selected"':''). ">$value</option>"; ?></select>
                </td>
              </tr>
              <tr>
                <td>
                  <label for="precocity">&nbsp;<?php print _("Plant precocity"); ?>&nbsp;</label>
                </td>
                <td>
                  <select name="precocity" id="precocity" title="<?php print _("Plant precocity"); ?>"><option></option><?php foreach(read_table($dbconn,'precocity',$lang) as $key => $value) print "<option value=\"$key\"" . (($key==$row2[16])?' selected="selected"':''). ">$value</option>"; ?></select>
                </td>
              </tr>
<?php }; ?>
              <tr>
                <td>
                  <label for="note">&nbsp;<?php print _("Notes"); ?>&nbsp;</label>
                </td>
                <td>
                  <textarea name="note" id="note" rows="4" cols="40"><?php print ((isset($row[10]))?$row[10]:'') ?></textarea>
                </td>
              </tr>
              <tr>
                <td colspan="2">
                  <?php print '<input name="reset" type="reset" value="' . _("Clear") . '"> &nbsp; ' . _("or") . ' &nbsp; <input type="submit" name="update" value="' . _("Update") . ' &gt;&gt;">'; ?> 
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
    print "        <p>\n          <a href=\"" . $base_url . $plugin . "admin/\"><img src=\"" . $base_url . 'images/ok.png" alt=""></a>&nbsp;<strong>' . _("Update successfull") . "</strong>\n        </p>\n";
    break;
    case 1:
    print "        <p>\n          <a href=\"" . $base_url . $plugin . "admin/motif.php?barcode=" . $_POST['barcode'] . "\"><img src=\"" . $base_url . 'images/no.png" alt=""></a>&nbsp;<strong>' . _("Invalid informations") . "</strong>\n        </p>\n";
    break;
    case 2:
    print "        <p>\n          <a href=\"" . $base_url . $plugin . "admin/><img src=\"" . $base_url . 'images/ok.png" alt=""></a>&nbsp;<strong>' . _("Seed deleted successfull") . "</strong>\n        </p>\n";
    break;
   };
  };
 };
 textdomain('phplabdb'); ?>
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
