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
 function crossing($dbconn,$lang) {
  $cross=array();
  $result=sql_query("SELECT id, legend FROM crosstype WHERE lang='$lang' ORDER BY id;",$dbconn);
  if(!(strlen($r=sql_last_error($dbconn)))) {
   while($row=sql_fetch_row($result)) {
    $cross[$row[0]]=htmlentities($row[1]);
   };
  };
  return $cross;
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
            <?php print _("New cross"); ?> 
          </h3>
<?php
   if (isset($_POST['add'])) {
    $cross_raw=$_SESSION['cross_raw'];
    if (!(empty($_POST['ref']))) {
     $cross_raw['ref']=stripslashes(trim($_POST['ref']));
     $resultID=sql_query("SELECT ref FROM seeds WHERE ref='" . addslashes(htmlentities($cross_raw['ref'])) . "';",$dbconn);
     if (sql_num_rows($resultID)!=0) {
      print "          <p>\n            <a href=\"" . $base_url . $plugin . "new_cross.php\"><img src=\"" . $base_url . 'images/no.png" alt=""></a>&nbsp;<strong>' . _("Reference already exists!") . "</strong>\n          </p>\n";
      } else {
      $ID_string=$cross_raw['ref'];
      do {
       $ID=seedID($ID_string);
       $resultID=sql_query('SELECT barcode FROM seeds WHERE barcode=' . $ID . ';',$dbconn);
       $ID_string=($cross_raw['ref'].dechex(time()));
      } while ((sql_num_rows($resultID)!=0)&&($ID>1000000)&&($ID==0));
      $cross_raw['barcode']=intval($ID);
      if (!(isset($cross_raw['species']))) $cross_raw['species']=intval($_POST['species']);
      $result=sql_query('INSERT INTO seeds (barcode, stock, date, ref, father, fbarcode, mother, mbarcode, crosstype, species, note) VALUES (' . $cross_raw['barcode'] . ',' . ((isset($cross_raw['seeds']))?$cross_raw['seeds']:'NULL') . ",'" . date("Y-m-d",$cross_raw['date']) . "','" . addslashes(htmlentities($cross_raw['ref'])) . "'," . ((isset($cross_raw['father']))?("'" . addslashes(htmlentities($cross_raw['father'])) . "'"):'NULL') . "," . ((isset($cross_raw['fbarcode']))?$cross_raw['fbarcode']:'NULL') . "," . ((isset($cross_raw['mother']))?("'" . addslashes(htmlentities($cross_raw['mother'])) . "'"):'NULL') . "," . ((isset($cross_raw['mbarcode']))?$cross_raw['mbarcode']:'NULL') . ','. ((isset($cross_raw['cross']))?$cross_raw['cross']:'NULL') . ',' . ((isset($cross_raw['species']))?$cross_raw['species']:'NULL') . ',' . ((isset($cross_raw['note']))?("'".addslashes(htmlentities($cross_raw['note']))."'"):'NULL') . ');',$dbconn);
      if(strlen ($r=sql_last_error($dbconn))) {
       print "          <p>\n            <a href=\"" . $base_url . $plugin . "new_cross.php\"><img src=\"" . $base_url . "images/oops.png\" alt=\"\"></a>&nbsp;<strong>Oops</strong><br>\n            $r\n          </p>\n";
       } else {
       print "          <p>\n            <a href=\"" . $base_url . $plugin . "\"><img src=\"" . $base_url . 'images/ok.png" alt=""></a>&nbsp;<strong>' . _("New cross added") . "</strong>\n          </p>\n";
       $_SESSION['cross_raw']=array(); // bug
       unset($_SESSION['cross_raw']);
      };
     };
     } else {
     print "          <p>\n            <a href=\"" . $base_url . $plugin . "new_cross.php\"><img src=\"" . $base_url . 'images/no.png" alt=""></a>&nbsp;<strong>' . _("No reference!") . "</strong>\n          </p>\n";
    };
    } elseif (isset($_POST['next'])) {
    unset($_SESSION['cross_raw']);
    $_SESSION['cross_raw']=array(); // bug
    if((empty($_POST['father']) && empty($_POST['mother'])) || (!(is_numeric($_POST['cross'])))) {
     print "          <p>\n            <a href=\"" . $base_url . $plugin . "new_cross.php\"><img src=\"" . $base_url . 'images/no.png" alt=""></a>&nbsp;<strong>' . _("Missing data") . "</strong>\n          </p>\n";
     } else {
     $cross_raw['cross']=intval($_POST['cross']);
     if (!(empty($_POST['father']))) {
      $cross_raw['father']=stripslashes(trim($_POST['father']));
      $result=sql_query("SELECT barcode, ref, species FROM seeds WHERE (ref='" . addslashes(htmlentities($cross_raw['father'])) . "'" . ((is_numeric($cross_raw['father']))?(' OR barcode=' . intval($cross_raw['father'])):'') . ");",$dbconn);
      if (sql_num_rows($result)==1) {
       $row = sql_fetch_row($result);
       $cross_raw['father']=$row[1];
       $cross_raw['fbarcode']=$row[0];
       $cross_raw['fspecies']=$row[2];
      };
      } elseif (isset($_POST['population'])) {
      $cross_raw['father']='none';
     };
     if (!(empty($_POST['mother']))) {
      $cross_raw['mother']=stripslashes(trim($_POST['mother']));
      $result=sql_query("SELECT barcode, ref, species FROM seeds WHERE (ref='" . addslashes(htmlentities($cross_raw['mother'])) . "'" . ((is_numeric($cross_raw['mother']))?(' OR barcode=' . intval($cross_raw['mother'])):'') . ");",$dbconn);
      if (sql_num_rows($result)==1) {
       $row = sql_fetch_row($result);
       $cross_raw['mother']=$row[1];
       $cross_raw['mbarcode']=$row[0];
       $cross_raw['mspecies']=$row[2];
      };
     };
     switch ($cross_raw['cross']) {
      case 1:
      if (isset($cross_raw['mother'])&&(!(isset($cross_raw['father']))||(isset($cross_raw['father'])&&($cross_raw['father']==$cross_raw['mother'])))) {
       $cross_raw['father']=$cross_raw['mother'];
       if (isset($cross_raw['mbarcode'])) $cross_raw['fbarcode']=$cross_raw['mbarcode'];
       if (isset($cross_raw['mspecies'])) $cross_raw['fspecies']=$cross_raw['mspecies'];
       $ok=true;
      };
      break;
      case 2:
      if (isset($cross_raw['mother'])&&isset($cross_raw['father'])&&($cross_raw['father']!=$cross_raw['mother'])) {
       if (($cross_raw['father']=='none') && (isset($cross_raw['mspecies']))) $cross_raw['fspecies']=$cross_raw['mspecies'];
       $ok=true;
      };
      break;
      case 3:
      if (!(isset($cross_raw['mother']))&&isset($cross_raw['father'])&&($cross_raw['father']!='none')) {
       $ok=true;
       if (isset($cross_raw['fspecies'])) $cross_raw['mspecies']=$cross_raw['fspecies'];
      };
     };
     if (isset($cross_raw['mspecies']) && isset($cross_raw['fspecies'])) {
      if ($cross_raw['mspecies']==$cross_raw['fspecies']) {
       $cross_raw['species']=$cross_raw['mspecies'];
       } else {
       $cross_raw['species']=0; //hybrid
      };
     }
     if (isset($ok)) {
      if (!(empty($_POST['note']))) $cross_raw['note']=stripslashes(trim($_POST['note']));
      $cross_raw['date']=mktime( 0,0,0, intval($_POST['date_M']), intval($_POST['date_D']), intval($_POST['date_Y']));
      if (is_numeric($_POST['seeds'])) $cross_raw['seeds']=intval($_POST['seeds']);
      $_SESSION['cross_raw']=$cross_raw;
?>
          <form action="<?php print $base_url . $plugin; ?>new_cross.php" method="post">
            <table summary="">
              <tr>
                <td>
                  <label for="ref"><?php print _("Reference"); ?>&nbsp;</label>
                </td>
                <td>
                  <input type="text" size="10" maxlength="50" name="ref" id="ref" value="<?php $result=sql_query("SELECT barcode FROM seeds WHERE ( date>='" . intval($_POST['date_Y']) . "-01-01' AND date<'" . intval($_POST['date_Y']+1) . "-01-01');",$dbconn); print (sql_num_rows($result)+1) . date(".y",$cross_raw['date']); ?>">
                </td>
              </tr>
              <tr>
                <td>
                  <?php print _("Cross type"); ?>&nbsp;
                </td>
                <td>
<?php
      if (($cross_raw['cross']==1) && (isset($cross_raw['mbarcode']))) {
       $mother=$cross_raw['mbarcode'];
       do {
        $i++;
        $result=sql_query("SELECT mbarcode, crosstype FROM seeds WHERE barcode=" . $mother . ";",$dbconn);
        if (sql_num_rows($result)==1) {
         $row=sql_fetch_row($result);
         if ($row[1]==2) {
          $mess=' (' . _("F") . ($i+1) . ')';
          break;
          } elseif($row[1]!=1) {
          break;
         };
         $mother=$row[0];
        };
       } while ((sql_num_rows($result)!=0)&&!(empty($mother)));
       $mess=((isset($mess))?$mess:(' (' . _("AF") . "$i)"));
      } elseif ($cross_raw['cross']==2 && (isset($cross_raw['mbarcode'])) && (isset($cross_raw['fbarcode']))) {
       $result=sql_query("SELECT barcode, mbarcode, fbarcode FROM seeds WHERE ((barcode=" . $cross_raw['mbarcode'] . " AND crosstype=2 AND fbarcode=" . $cross_raw['fbarcode'] . ") OR (barcode=" . $cross_raw['fbarcode'] . " AND crosstype=2 AND mbarcode=" . $cross_raw['mbarcode'] . "));",$dbconn);
       if (sql_num_rows($result)==1) {
        $row=sql_fetch_row($result);
        if (($row[0]==$cross_raw['fbarcode']) ) {
         if (isset($row[2])) {
          $father=$row[2];
          do {
           $i++;
           $result=sql_query("SELECT fbarcode FROM seeds WHERE (barcode=$father AND crosstype=2 AND mbarcode=" . $cross_raw['mbarcode'] . ');',$dbconn);
           if (sql_num_rows($result)==1) {
            $row=sql_fetch_row($result);
            $father=$row[0];
           };
          } while ((sql_num_rows($result)==1)&&!(empty($father)));
         };
         $mess=' (' . _("BC") . ((sql_num_rows($result)!=1)?($i+1):'') . ', ' . _("same mother") . ')';
         } elseif (($row[0]==$cross_raw['mbarcode']) ) {
         if (isset($row[1])) {
          $mother=$row[1];
          do {
           $i++;
           $result=sql_query("SELECT mbarcode FROM seeds WHERE (barcode=$mother AND crosstype=2 AND fbarcode=" . $cross_raw['fbarcode'] . ');',$dbconn);
           if (sql_num_rows($result)==1) {
            $row=sql_fetch_row($result);
            $mother=$row[0];
           };
          } while ((sql_num_rows($result)==1)&&!(empty($mother)));
         };
         $mess=' (' . _("BC") . ((sql_num_rows($result)!=1)?($i+1):'') . ', ' . _("same father") . ')';
        };
       };
      };
      $cross=crossing($dbconn,$lang);
      print $cross[$cross_raw['cross']] . $mess;
?>
                </td>
              </tr>
              <tr>
                <td>
                  <?php print _("Father (pollen)"); ?>&nbsp;
                </td>
                <td>
                  <?php  print (($cross_raw['father']=='none')? _("Population / free union"):htmlentities($cross_raw['father'])); ?> 
                </td>
              </tr>
<?php if (isset($cross_raw['mother'])) { ?>
              <tr>
                <td>
                  <?php print _("Mother (carry seed)"); ?>&nbsp;
                </td>
                <td>
                  <?php  print htmlentities($cross_raw['mother']); ?> 
                </td>
              </tr>
<?php }; ?>
              <tr>
                <td>
                  <?php print _("Cross date"); ?>&nbsp;
                </td>
                <td>
                  <?php  print date(_("m/d/Y"),$cross_raw['date']); ?> 
                </td>
              </tr>
<?php if (!(isset($cross_raw['species']))) { ?>
             <tr>
                <td>
                  <label for="species"><?php print _("Species"); ?>&nbsp;</label>
                </td>
                <td>
                  <select name="species" id="species" title="<?php print _("Species"); ?>"><option value="0" selected="selected"><?php print _("n.a."); ?></option><?php
      $result=sql_query('SELECT id, name FROM species;',$dbconn);
      if(!(strlen($r=sql_last_error($dbconn)))) {
       while($row=sql_fetch_row($result) ) {
        print "<option value=\"$row[0]\">$row[1]</option>";
       };
      };
?></select>
                </td>
              </tr>
<?php }; ?>
              <tr>
                <td>
                  <?php print _("Seed number"); ?>&nbsp;
                </td>
                <td>
                  <?php  print $cross_raw['seeds']; ?> 
                </td>
              </tr>
              <tr>
                <td>
                  <?php print _("Notes"); ?>&nbsp;
                </td>
                <td>
                  <?php  print htmlentities($cross_raw['note']); ?> 
                </td>
              </tr>
              <tr>
                <td colspan="2">
                  <?php print '<input name=""back" type="submit" value="' . _("Back") . '"> &nbsp; ' . _("or") . ' &nbsp; <input type="submit" name="add" value="' . _("Add") . ' &gt;&gt;">'; ?>                  
                </td>
              </tr>
            </table>
          </form>
<?php
      } else {
      print "          <p>\n            <a href=\"" . $base_url . $plugin . "new_cross.php\"><img src=\"" . $base_url . 'images/no.png" alt=""></a>&nbsp;<strong>' . _("Cross type and parents are not consistant") . "</strong>\n          </p>\n";
     };
    };
 } else { ?>
          <p>
            <?php print _("Inform all following information:"); ?> 
          </p>
          <form action="<?php print $base_url . $plugin; ?>new_cross.php" method="post">
            <table summary="">
              <tr>
                <td>
                  <label for="cross">&nbsp;<?php print _("Cross type"); ?>&nbsp;</label>
                </td>
                <td>
                  <select name="cross" id="cross" title="<?php print _("Cross type"); ?>"><?php $cross=crossing($dbconn,$lang); foreach($cross as $key => $value) print "<option value=\"$key\"" . (($key==2)?' selected="selected"':''). ">$value</option>"; ?></select>
                </td>
              </tr>
              <tr>
                <td>
                  <label for="cross">&nbsp;<?php print _("Father"); ?>&nbsp;</label>
                </td>
                <td>
                  <?php print '<input type="text" size="10" maxlength="50" name="father" id="father" title="' . _("pollen [reference or ID]") .'"> ' . _("or") . ' <input type="checkbox" name="population" id="population" title="' .  _("free union (population)") . '">&nbsp;<label for="population">' . _("free union (population)") . '</label>'; ?>
                </td>
              </tr>
              <tr>
                <td>
                  <label for="cross">&nbsp;<?php print _("Mother"); ?>&nbsp;</label>
                </td>
                <td>
                  <input type="text" size="10" maxlength="50" name="mother" id="mother" title="<?php print _("carry seed [reference or ID]"); ?>">
                </td>
              </tr>
              <tr>
                <td>
                  &nbsp;<?php print _("Date"); ?>&nbsp;
                </td>
                <td>
                 <?php print newtime(); ?> 
                </td>
              </tr>
              <tr>
                <td>
                  <label for="seeds">&nbsp;<?php print _("Seed number"); ?>&nbsp;</label>
                </td>
                <td>
                  <input type="text" size="5" maxlength="10" name="seeds" id="seeds">
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
