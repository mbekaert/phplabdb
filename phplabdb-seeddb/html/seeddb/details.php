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
 function read_table($dbconn,$name,$lang) {
  $table=array();
  $result=sql_query("SELECT id, legend FROM $name WHERE lang='$lang' ORDER BY id;",$dbconn);
  if(!(strlen($r=sql_last_error($dbconn)))) {
   while($row=sql_fetch_row($result)) {
    $table[$row[0]]=htmlentities($row[1]);
   };
  };
  return $table;
 };
 $status=$_SESSION['status'];
 if(isset($_GET['barcode'])) $barcode=intval($_GET['barcode']);
 header_start();
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
            <?php print _("Search details"); ?> 
          </h3>
<?php
  if (isset($barcode)) {
   $dbconn=sql_connect($plugin_db['seeddb']);
   $result=sql_query("SELECT barcode, ref, date, species, stock, father, fbarcode, mother, mbarcode, crosstype, note FROM seeds WHERE barcode=$barcode;",$dbconn);
   if(!(strlen($r=sql_last_error($dbconn))) && (sql_num_rows($result)==1)) {
    $row=sql_fetch_row($result);
    if(isset($row[9])) {
?>
          <table id="list-database" width="100%" summary="">
            <tr>
              <td>
                <?php print _("Reference") . ' / <em><small>' . _("ID") . '</small></em>'; ?>&nbsp;
              </td>
              <td>
                <?php  print "<strong>$row[1]</strong> / <em><small>$row[0]</small></em>"; ?> 
              </td>
            </tr>
            <tr>
              <td>
                <?php print _("Cross type"); ?>&nbsp;
              </td>
              <td>
<?php
     if (($row[9]==1) && (isset($row[8]))) {
      $mother=$row[8];
      do {
       $i++;
       $result2=sql_query("SELECT mbarcode, crosstype FROM seeds WHERE barcode=" . $mother . ";",$dbconn);
       if (sql_num_rows($result2)==1) {
        $row2 = sql_fetch_row($result2);
        if ($row2[1]==2) {
         $mess=' (' . _("F") . ($i+1) . ')';
         break;
         } elseif($row2[1]!=1) {
         break;
        };
        $mother=$row2[0];
       };
      } while ((sql_num_rows($result)!=0)&&!(empty($mother)));
      $mess=((isset($mess))?$mess:(' (' . _("AF") . "$i)"));
      } elseif ($row[9]==2 && (isset($row[8])) && (isset($row[6]))) {
      $result2=sql_query("SELECT barcode, mbarcode, fbarcode FROM seeds WHERE ((barcode=" . $row[8] . " AND crosstype=2 AND fbarcode=" . $row[6] . ") OR (barcode=" . $row[6] . " AND crosstype=2 AND mbarcode=" . $row[8] . "));",$dbconn);
      if (sql_num_rows($result2)==1) {
       $row2=sql_fetch_row($result2);
       if (($row2[0]==$row[6]) ) {
        if (isset($row2[2])) {
         $father=$row2[2];
         do {
          $i++;
          $result2=sql_query("SELECT fbarcode FROM seeds WHERE (barcode=$father AND crosstype=2 AND mbarcode=" . $row[8] . ');',$dbconn);
          if (sql_num_rows($result2)==1) {
           $row2=sql_fetch_row($result2);
           $father=$row2[0];
          };
         } while ((sql_num_rows($result2)==1)&&!(empty($father)));
        };
        $mess=' (' . _("BC") . ((sql_num_rows($result2)!=1)?($i+1):'') . ', ' . _("same mother") . ')';
        } elseif (($row2[0]==$row[8]) ) {
        if (isset($row2[1])) {
         $mother=$row2[1];
         do {
          $i++;
          $result2=sql_query("SELECT mbarcode FROM seeds WHERE (barcode=$mother AND crosstype=2 AND fbarcode=" . $row[6] . ');',$dbconn);
          if (sql_num_rows($result2)==1) {
           $row2=sql_fetch_row($result2);
           $mother=$row2[0];
          };
         } while ((sql_num_rows($result2)==1)&&!(empty($mother)));
        };
        $mess=' (' . _("BC") . ((sql_num_rows($result2)!=1)?($i+1):'') . ', ' . _("same father") . ')';
       };
      };
     };
     $cross= read_table($dbconn,'crosstype',$lang);
     print $cross[$row[9]] . $mess;
?>
                </td>
              </tr>
              <tr>
                <td>
                  <?php print _("Father (pollen)"); ?>&nbsp;
                </td>
                <td>
                  <?php  print (($row[5]=='none')? _("Population / free union"):((isset($row[6]))?('<a href="' . $base_url . $plugin . 'details.php?barcode='. rawurlencode($row[6]) . '">' . $row[5] . '</a>'):$row[5])); ?> 
                </td>
              </tr>
<?php if (isset($row[7])) { ?>
              <tr>
                <td>
                  <?php print _("Mother (carry seed)"); ?>&nbsp;
                </td>
                <td>
                  <?php  print ((isset($row[8]))?('<a href="' . $base_url . $plugin . 'details.php?barcode='. rawurlencode($row[8]) . '">' . $row[7] . '</a>'):$row[7]); ?> 
                </td>
              </tr>
<?php }; ?>
              <tr>
                <td>
                  <?php print _("Cross date"); ?>&nbsp;
                </td>
                <td>
                  <?php  print date(_("m/d/Y"),strtotime($row[2])); ?>
                </td>
              </tr>
             <tr>
                <td>
                  <?php print _("Species"); ?>&nbsp;
                </td>
                <td>
<em><?php
     $result2=sql_query("SELECT name, taxon FROM species WHERE id=$row[3];",$dbconn);
     if(!(strlen($r=sql_last_error($dbconn))) && (sql_num_rows($result2)==1)) {
      $row2=sql_fetch_row($result2);
      print ((isset($row2[1]))?('<a href="http://www.ncbi.nlm.nih.gov/Taxonomy/Browser/wwwtax.cgi?mode=Info&id=' . $row2[1] . '">' . $row2[0] . '</a>'):$row2[0]);
     };
?></em>
                </td>
              </tr>
<?php if (isset($row[4])) { ?>
              <tr>
                <td>
                  <?php print _("Seed number"); ?>&nbsp;
                </td>
                <td>
                  <?php  print $row[4]; ?> 
                </td>
              </tr>
<?php }; if (isset($row[10])) { ?>
              <tr>
                <td>
                  <?php print _("Notes"); ?>&nbsp;
                </td>
                <td>
                  <?php  print $row[10]; ?> 
                </td>
              </tr>
<?php }; ?>
            </table>
            <p>
<?php
     print '              <embed src="' . $base_url . $plugin . 'images/tree.php?barcode=' . rawurlencode($row[0]) . '" width="100%" height="400" type="image/svg+xml" pluginspage="http://www.adobe.com/svg/viewer/install/"></embed>'."\n            </p>";
     } else { // prospection
     $result2=sql_query("SELECT barcode, date, prospection, species, vernacular, country, locality, latitude, longitude, altitude, ethnos, nature, form, size, distribution, weather, precocity, note FROM prospection WHERE barcode=$barcode;",$dbconn);
     if(!(strlen($r=sql_last_error($dbconn))) && (sql_num_rows($result2)==1)) {
      $row2=sql_fetch_row($result2);
?>
          <table id="list-database" width="100%" summary="">
            <tr>
              <td>
                <?php print _("Prospection"); ?>&nbsp;
              </td>
              <td>
                <strong><?php  print $row2[2]; ?></strong>
              </td>
            </tr>
            <tr>
              <td>
                <?php print _("Reference") . ' / <em><small>' . _("ID") . '</small></em>'; ?>&nbsp;
              </td>
              <td>
                <?php  print "<strong>$row[1]</strong> / <em><small>$row[0]</small></em>"; ?> 
              </td>
            </tr>
            <tr>
              <td>
                <?php print _("Species"); ?>&nbsp;
              </td>
              <td>
<em><?php
      $result3=sql_query("SELECT name, taxon FROM species WHERE id=$row2[3];",$dbconn);
      if(!(strlen($r=sql_last_error($dbconn))) && (sql_num_rows($result3)==1)) {
       $row3=sql_fetch_row($result3);
       print ((isset($row3[1]))?('<a href="http://www.ncbi.nlm.nih.gov/Taxonomy/Browser/wwwtax.cgi?mode=Info&amp;id=' . $row3[1] . '">' . $row3[0] . '</a>'):$row3[0]);
      };
?></em>
              </td>
            </tr>
<?php  if (isset($row2[4])) { ?>
            <tr>
              <td>
                <?php print _("Vernacular name"); ?>&nbsp;
              </td>
              <td>
                <?php  print $row2[4]; ?> 
              </td>
            </tr>
<?php }; ?>
            <tr>
              <td>
               <?php print _("Prospection date"); ?>&nbsp;
              </td>
              <td>
                <?php  print date("F Y",strtotime($row2[1])); ?> 
              </td>
            </tr>
<?php  if (isset($row[4])) { ?>
            <tr>
              <td>
                <?php print _("Seed number"); ?>&nbsp;
              </td>
              <td>
                <?php  print $row[4]; ?> 
              </td>
            </tr>
<?php }; ?>
            <tr>
              <td>
               <?php print _("Entry date"); ?>&nbsp;
              </td>
              <td>
                <?php  print date(_("m/d/Y"),strtotime($row[2])); ?> 
              </td>
            </tr>
<?php if (isset($row2[5])) { ?>
            <tr>
              <td>
                <?php print _("Country"); ?>&nbsp;
              </td>
              <td>
                <?php  print $row2[5]; ?> 
              </td>
            </tr>
<?php }; if (isset($row2[6]) || isset($row2[7]) || isset($row2[8]) || isset($row2[9])) { ?>
            <tr>
              <td>
                <?php print _("Locality"); ?>&nbsp;
              </td>
              <td>
                <?php  print $row2[6] . ((isset($row2[7]))?(' ' . _("Lat:") . " $row2[7]"):'') . ((isset($row2[8]))?(' ' . _("Long:") . " $row2[8]"):'') . ((isset($row2[9]))?(' ' . _("Alt:") . " $row2[9]"):''); ?>
              </td>
            </tr>
<?php }; if (isset($row2[10])) { ?>
            <tr>
              <td>
                <?php print _("Ethnos group"); ?>&nbsp;
              </td>
              <td>
                <?php  print $row2[10]; ?> 
              </td>
            </tr>
<?php }; if (isset($row2[11])) { ?>
            <tr>
              <td>
                <?php print _("Landscape nature"); ?>&nbsp;
              </td>
              <td>
<?php
      $tab=read_table($dbconn,'nature',$lang);
      print $tab[$row2[11]];
?>
              </td>
            </tr>
<?php }; if (isset($row2[12])) { ?>
            <tr>
              <td>
                <?php print _("Botanical form"); ?>&nbsp;
              </td>
              <td>
<?php
      $tab=read_table($dbconn,'form',$lang);
      print $tab[$row2[12]];
?>
              </td>
            </tr>
<?php }; if (isset($row2[13])) { ?>
            <tr>
              <td>
                <?php print _("Population size"); ?>&nbsp;
              </td>
              <td>
<?php
      $tab=read_table($dbconn,'size',$lang);
      print $tab[$row2[13]];
?>
              </td>
            </tr>
<?php }; if (isset($row2[14])) { ?>
            <tr>
              <td>
                <?php print _("Plant distribution"); ?>&nbsp;
              </td>
              <td>
<?php
      $tab=read_table($dbconn,'distribution',$lang);
      print $tab[$row2[14]];
?>
              </td>
            </tr>
<?php }; if (isset($row2[15])) { ?>
            <tr>
              <td>
                <?php print _("Weather/Culture type"); ?>&nbsp;
              </td>
              <td>
<?php
      $tab=read_table($dbconn,'weather',$lang);
      print $tab[$row2[15]];
?>
              </td>
            </tr>
<?php }; if (isset($row2[16])) { ?>
            <tr>
              <td>
                <?php print _("Plant precocity"); ?>&nbsp;
              </td>
              <td>
<?php
      $tab=read_table($dbconn,'precocity',$lang);
      print $tab[$row2[16]];
?>
              </td>
            </tr>
<?php }; if (isset($row2[17])) { ?>
            <tr>
              <td>
                <?php print _("Note"); ?>&nbsp;
              </td>
              <td>
                <?php  print $row2[17]; ?> 
              </td>
            </tr>
<?php }; ?>
          </table>
<?php
     };
    };
    $result2=sql_query("SELECT value FROM config WHERE id='barcode';",$dbconn);
    if(!(strlen($r=sql_last_error($dbconn)))&&(sql_num_rows($result2)==1)) {
     $row2=sql_fetch_row($result2);
     if(isset($row2[0])) {
      printf("          <img src=\"%simages/barcode.php?code=%06d&amp;style=198&amp;type=I25&amp;width=125&amp;height=50&amp;xres=2&amp;font=3\" alt=\"\">\n",$base_url,$row[0]);
     };
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
