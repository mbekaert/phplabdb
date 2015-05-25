<?php
 ob_start("ob_gzhandler");
 session_start();
 include_once '../includes/login.inc';
 $lang=((isset($_COOKIE['lang']))?substr($_COOKIE['lang'],0,2):'en');
 if (!isset($_SESSION['status'])) {
  header('Location: ' . $base_url);
  exit;
 };
 $lev=error_reporting(8); 
 function seedID($string) { #alias crc20 ?!!
  $crc = 0xFFFFF;
  for ($x = 0; $x < strlen($string); $x++) {
   $crc = $crc ^ ord($string[$x]);
   for ($y = 0; $y < 8; $y++) {
    if (($crc & 0x00001) == 0x00001) {
      $crc = (($crc >> 1) ^ 0xA0001);
    } else { $crc = $crc >> 1; };
   };
  };
  return $crc;
 };
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
 function newtime() {
  $list_date=getdate();
  $day = '<select name="date_D" title="' . _("day") . '">';
  for ($i=1 ; $i<32; $i++) 
   $day .= '<option' . (($list_date['mday']==$i)? ' selected="selected">':'>') . $i . '</option>';
  $day .= '</select>';
  $month = '<select name="date_M" title="' . _("month") . '">';
  for ($i=1 ; $i<13; $i++) 
   $month .= '<option' . (($list_date['mon']==$i)? ' selected="selected">':'>') . $i . '</option>';
  $month .= '</select>';
  $year = '<select name="date_Y" title="' . _("year") . '">';
  for ($i=1950 ; $i<=($list_date['year']); $i++)
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
 header_start();
 $dbconn=sql_connect($plugin_db['seeddb']);
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
.seed { background: url('images/seeds.png') no-repeat right top; }
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
          <?php print '<strong><a href="' . $base_url . 'database.php">' . _("Databases") . '</strong></a>'; ?> 
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
          <h3>
            <?php print _("New prospection seed"); ?> 
          </h3>
<?php if (isset($_POST['next'])) {
  unset($_SESSION['seed_raw']);
  $_SESSION['seed_raw']=array(); // bug
  $seed_raw['prospection']=stripslashes(trim($_POST['prospection']));
  $seed_raw['date']=mktime( 0,0,0, intval($_POST['date_M']), 1, intval($_POST['date_Y']));
  $seed_raw['country']=stripslashes(trim($_POST['country']));
  $seed_raw['species']=$_POST['species'];
  if(empty($seed_raw['prospection'])||empty($seed_raw['country'])||($seed_raw['species']=='')) {
   print "          <p>\n            <a href=\"" . $base_url . $plugin . "new_seed.php\"><img src=\"" . $base_url . 'images/no.png" alt=""></a>&nbsp;<strong>' . _("Missing data") . "</strong>\n          </p>\n";
  } else {
   $seed_raw['vernacular']=stripslashes(trim($_POST['vernacular']));
   $seed_raw['locality']=stripslashes(trim($_POST['locality']));
   $seed_raw['latitude']=stripslashes(trim($_POST['latitude']));
   $seed_raw['longitude']=stripslashes(trim($_POST['longitude']));
   $seed_raw['altitude']=stripslashes(trim($_POST['altitude']));
   $seed_raw['ethnos']=stripslashes(trim($_POST['ethnos']));
   $seed_raw['nature']=intval($_POST['nature']);
   $seed_raw['form']=intval($_POST['form']);
   $seed_raw['size']=intval($_POST['size']);
   $seed_raw['distribution']=intval($_POST['distribution']);
   $seed_raw['weather']=intval($_POST['weather']);
   $seed_raw['precocity']=intval($_POST['precocity']);
   $seed_raw['note']=stripslashes(trim($_POST['note']));
   $ID_string=$seed_raw['prospection'];
   do {
    $ID=seedID($ID_string);
    $resultID=sql_query('SELECT barcode FROM seeds WHERE barcode=' . $ID . ';',$dbconn);
    $ID_string=($seed_raw['prospection'].dechex(time()));
   } while ((sql_num_rows($resultID)!=0)&&($ID>1000000)&&($ID==0));
   $seed_raw['barcode']=intval($ID);
   $_SESSION['seed_raw']=$seed_raw;
?>
          <p>
            <?php print _("Inform all following information:"); ?> 
          </p>
          <form action="<?php print $base_url . $plugin; ?>new_seed.php" method="post">
            <table summary="">
              <tr>
                <td>
                  <?php print _("Prospection"); ?>&nbsp;
                </td>
                <td>
                  <strong><?php print $seed_raw['prospection']; ?></strong>
                </td>
              </tr>
              <tr>
                <td>
                  <?php print _("Country"); ?>&nbsp;
                </td>
                <td>
                  <strong><?php print $seed_raw['country']; ?></strong>
                </td>
              </tr>
              <tr>
                <td>
                  <?php print _("Species"); ?>&nbsp;
                </td>
                <td>
                  <strong><em><?php 
    $result3=sql_query('SELECT name FROM species WHERE id=' . $seed_raw['species'] . ';',$dbconn);
    if(!(strlen($r=sql_last_error($dbconn))) && (sql_num_rows($result3)==1)) {
     $row3=sql_fetch_row($result3);
     print $row3[0];
    };
?></em></strong>
                </td>
              </tr>
              <tr>
                <td>
                  <?php print _("Prospection date"); ?>&nbsp;
                </td>
                <td>
                  <strong><?php print date("F Y",$seed_raw['date']); ?></strong>
                </td>
              </tr>
              <tr>
                <td>
                  <label for="seeds"><?php print _("Seed number"); ?>&nbsp;</label>
                </td>
                <td>
                  <input type="text" size="5" maxlength="10" name="seeds" id="seeds">
                </td>
              </tr>
              <tr>
                <td>
                  <?php print _("Date"); ?>&nbsp;
                </td>
                <td>
                  &nbsp;<?php print newtime(); ?>&nbsp;
                </td>
              </tr>
              <tr>
                <td>
                  <label for="ref"><?php print _("Reference"); ?><sup>*</sup>&nbsp;</label>
                </td>
                <td>
                  <input type="text" size="10" maxlength="50" name="ref" id="ref" value="<?php $result=sql_query('SELECT barcode FROM seeds WHERE date>=' . date("'Y-01-01'") . ';',$dbconn); print (sql_num_rows($result)+1) . date(".y"); ?>">
                </td>
              </tr>
             <tr>
                <td colspan="2">
                  <?php print '<input name="clear" type="reset" value="' . _("Clear") . '"> &nbsp; ' . _("or") . ' &nbsp; <input type="submit" name="add" value="' . _("Add") . ' &gt;&gt;">'; ?> 
                </td>
              </tr>
            </table>
          </form>
<?php
  };
 } elseif (isset($_POST['add'])) {
  if (isset($_POST['ref'])) {
   $seed_raw=$_SESSION['seed_raw'];
   $seed_raw['seeds_date']=mktime( 0,0,0, intval($_POST['date_M']), intval($_POST['date_D']), intval($_POST['date_Y']));
   $seed_raw['ref']=stripslashes(trim($_POST['ref']));
   $result=sql_query("INSERT INTO prospection (barcode,prospection,vernacular,species,date,country,locality,latitude,longitude,altitude,ethnos,nature,form,size,distribution,weather,precocity,note) VALUES (" . $seed_raw['barcode'] . ",'" . addslashes(htmlentities($seed_raw['prospection'])) . "'," . ((empty($seed_raw['vernacular']))?'NULL':("'" . addslashes(htmlentities($seed_raw['vernacular'])) . "'")) . ',' . intval($seed_raw['species']) . ",'" . date("Y-m-d",$seed_raw['date']) . "'," . ((empty($seed_raw['country']))?'NULL':("'" . addslashes(htmlentities($seed_raw['country'])) . "'")) . ',' . ((empty($seed_raw['locality']))?'NULL':("'" . addslashes(htmlentities($seed_raw['locality'])) . "'")) . ',' . ((empty($seed_raw['latitude']))?'NULL':("'" . addslashes(htmlentities($seed_raw['latitude'])) . "'")) . ',' . ((empty($seed_raw['longitude']))?'NULL':("'" . addslashes(htmlentities($seed_raw['longitude'])) . "'")) . ',' . ((empty($seed_raw['altitude']))?'NULL':("'" . addslashes(htmlentities($seed_raw['altitude'])) . "'")) . ',' . ((empty($seed_raw['ethnos']))?'NULL':("'" . addslashes(htmlentities($seed_raw['ethnos'])) . "'")) . ',' . ((empty($seed_raw['nature']))?'NULL':$seed_raw['nature']) . ',' . ((empty($seed_raw['form']))?'NULL':$seed_raw['form']) . ',' . ((empty($seed_raw['size']))?'NULL':$seed_raw['size']) . ',' . ((empty($seed_raw['distribution']))?'NULL':$seed_raw['distribution']) . ',' . ((empty($seed_raw['weather']))?'NULL':$seed_raw['weather']) . ',' . ((empty($seed_raw['precocity']))?'NULL':$seed_raw['precocity']) . ',' . ((empty($seed_raw['note']))?'NULL':("'" . addslashes(htmlentities($seed_raw['note'])) . "'")) . ');',$dbconn);
   if(strlen ($r=sql_last_error($dbconn))) {
    print "          <p>\n            <a href=\"" . $base_url . $plugin . "new_seed.php\"><img src=\"" . $base_url . "images/no.png\" alt=\"\"></a>&nbsp;<strong>Oops</strong><br>\n            $r\n          </p>\n";
   } else {
    print "          <p>\n            <a href=\"" . $base_url . $plugin . "\"><img src=\"" . $base_url . 'images/ok.png" alt=""></a>&nbsp;<strong>' . ("New prospection seed added") . "</strong>\n          </p>\n";
    $result=sql_query('INSERT INTO seeds (barcode,stock,date,ref,father,fbarcode,mother,mbarcode,crosstype,species,note) VALUES (' . $seed_raw['barcode'] . ',' . ((is_numeric($_POST['seeds']))?intval($_POST['seeds']):'NULL') . ",'" . date("Y-m-d",$seed_raw['seeds_date']) . "','" . addslashes(htmlentities($seed_raw['ref'])) . "',NULL,NULL,NULL,NULL,NULL," .intval($seed_raw['species']) . ',' . ((empty($seed_raw['note']))?'NULL':("'" . addslashes(htmlentities($seed_raw['note'])) . "'")) . ");",$dbconn);
    if(strlen ($r=sql_last_error($dbconn))) {
     print "          <p>\n            <a href=\"" . $base_url . $plugin . "new_seed.php\"><img src=\"" . $base_url . "images/no.png\" alt=\"\"></a>&nbsp;<strong>Oops</strong><br>\n            $r\n          </p>\n";
    } else {
     print "          <p>\n            <a href=\"" . $base_url . $plugin . "\"><img src=\"" . $base_url . 'images/ok.png" alt=""></a>&nbsp;<strong>' . ("New seeds added") . "</strong>\n          </p>\n";
     $_SESSION['seed_raw']=array(); // bug
     unset($_SESSION['seed_raw']);
    };
   };
  } else {
   print "          <p>\n            <a href=\"" . $base_url . $plugin . "new_seed.php\"><img src=\"" . $base_url . 'images/no.png" alt=""></a>&nbsp;<strong>' . ("Missing data") . "</strong>\n          </p>\n";
  };
 } else { ?>
          <p>
            <?php print _("Inform following information:"); ?><br>
             <sup>*</sup> <small><?php print _("needed information"); ?></small>
          </p>
          <form action="<?php print $base_url . $plugin; ?>new_seed.php" method="post">
            <table summary="">
              <tr>
                <td>
                  <label for="prospection">&nbsp;<?php print _("Prospection"); ?><sup>*</sup>&nbsp;</label>
                </td>
                <td>
                  <input type="text" size="20" maxlength="50" name="prospection" id="prospection">
                </td>
              </tr>
              <tr>
                <td>
                  <label for="vernacular">&nbsp;<?php print _("Vernacular name"); ?>&nbsp;</label>
                </td>
                <td>
                  <input type="text" size="20" maxlength="50" name="vernacular" id="vernacular">
                </td>
              </tr>
              <tr>
                <td>
                  &nbsp;<?php print _("Date"); ?><sup>*</sup>&nbsp;
                </td>
                <td>
                  <select name="date_M" title="month"><?php $liste_date=getdate(); for ($i=1 ; $i<13; $i++) {print "<option" . ( ($liste_date['mon']==$i)? " selected>":">") . $i . "</option>"; }; ?></select> &nbsp;/&nbsp; <select name="date_Y" title="year"><?php $annee = date("Y"); for ($i=1950 ; $i<=($annee); $i++) {print "<option" . ( ($liste_date['year']==$i)? " selected>":">") . $i . "</option>"; }; ?></select>
                </td>
              </tr>
              <tr>
                <td>
                  <label for="country">&nbsp;<?php print _("Country"); ?><sup>*</sup>&nbsp;</label>
                </td>
                <td>
                  <input type="text" size="20" maxlength="50" name="country" id="country">
                </td>
              </tr>
              <tr>
                <td>
                  <label for="locality">&nbsp;<?php print _("Locality"); ?>&nbsp;</label>
                </td>
                <td>
                  <input type="text" size="20" maxlength="50" name="locality" id="locality">
                </td>
              </tr>
              <tr>
                <td>
                  <label for="latitude">&nbsp;<?php print _("Latitude"); ?>&nbsp;</label>
                </td>
                <td>
                  <input type="text" size="10" maxlength="20" name="latitude" id="latitude">
                </td>
              </tr>
              <tr>
                <td>
                  <label for="longitude">&nbsp;<?php print _("Longitude"); ?>&nbsp;</label>
                </td>
                <td>
                  <input type="text" size="10" maxlength="20" name="longitude" id="longitude">
                </td>
              </tr>
              <tr>
                <td>
                  <label for="altitude">&nbsp;<?php print _("Altitude"); ?>&nbsp;</label>
                </td>
                <td>
                  <input type="text" size="10" maxlength="20" name="altitude" id="altitude">
                </td>
              </tr>
              <tr>
                <td>
                  <label for="ethnos">&nbsp;<?php print _("Ethnos group"); ?>&nbsp;</label>
                </td>
                <td>
                  <input type="text" size="20" maxlength="50" name="ethnos" id="ethnos" title="<?php print _("Ethnos group"); ?>">
                </td>
              </tr>
              <tr>
                <td>
                  <label for="species">&nbsp;<?php print _("Species"); ?><sup>*</sup>&nbsp;</label>
                </td>
                <td>
                  <select name="species" id="species" title="<?php print _("Species"); ?>"><option></option><?php
 $result=sql_query('SELECT id, name FROM species ORDER BY name;',$dbconn);
 if(!(strlen($r=sql_last_error($dbconn)))) {
  while($row=sql_fetch_row($result)) {
   print "<option value=\"$row[0]\">$row[1]</option>";
  };
 };
?></select>
                </td>
              </tr>
              <tr>
                <td>
                  <label for="nature">&nbsp;<?php print _("Landscape nature"); ?>&nbsp;</label>
                </td>
                <td>
                  <select name="nature" id="nature" title="<?php print _("Nature of the collecting site"); ?>"><option></option><?php foreach(read_table($dbconn,'nature',$lang) as $key => $value) print "<option value=\"$key\">$value</option>"; ?></select>
                </td>
              </tr>
              <tr>
                <td>
                  <label for="form">&nbsp;<?php print _("Botanical form"); ?>&nbsp;</label>
                </td>
                <td>
                  <select name="form" id="form" title="<?php print _("Botanical form"); ?>"><option></option><?php foreach(read_table($dbconn,'form',$lang) as $key => $value) print "<option value=\"$key\">$value</option>"; ?></select>
                </td>
              </tr>
              <tr>
                <td>
                  <label for="size">&nbsp;<?php print _("Population size"); ?>&nbsp;</label>
                </td>
                <td>
                  <select name="size" id="size" title="<?php print _("Population size"); ?>"><option></option><?php foreach(read_table($dbconn,'size',$lang) as $key => $value) print "<option value=\"$key\">$value</option>"; ?></select>
                </td>
              </tr>
              <tr>
                <td>
                  <label for="distribution">&nbsp;<?php print _("Plant distribution"); ?>&nbsp;</label>
                </td>
                <td>
                  <select name="distribution" id="distribution" title="<?php print _("Distribution pattern"); ?>"><option></option><<?php foreach(read_table($dbconn,'distribution',$lang) as $key => $value) print "<option value=\"$key\">$value</option>"; ?></select>
                </td>
              </tr>
              <tr>
                <td>
                  <label for="weather">&nbsp;<?php print _("Weather/Culture type"); ?>&nbsp;</label>
                </td>
                <td>
                  <select name="weather" id="weather" title="<?php print _("Local weather"); ?>"><option></option><?php foreach(read_table($dbconn,'weather',$lang) as $key => $value) print "<option value=\"$key\">$value</option>"; ?></select>
                </td>
              </tr>
              <tr>
                <td>
                  <label for="precocity">&nbsp;<?php print _("Plant precocity"); ?>&nbsp;</label>
                </td>
                <td>
                  <select name="precocity" id="precocity" title="<?php print _("Plant precocity"); ?>"><option></option><?php foreach(read_table($dbconn,'precocity',$lang) as $key => $value) print "<option value=\"$key\">$value</option>"; ?></select>
                </td>
              </tr>
              <tr>
                <td>
                  <label for="note">&nbsp;<?php print _("Notes"); ?>&nbsp;</label>
                </td>
                <td>
                  <textarea name="note" id="note" rows="4" cols="40"></textarea>
                </td>
              </tr>
              <tr>
                <td colspan="2">
                  <?php print '<input name="clear" type="reset" value="' . _("Clear") . '"> &nbsp; ' . _("or") . ' &nbsp; <input type="submit" name="next" value="' . _("Next") . ' &gt;&gt;">'; ?>
                </td>
              </tr>
            </table>
          </form>
<?php };
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
