<?php
 ob_start("ob_gzhandler");
 session_start();
 include_once '../includes/login.inc';
 if (!isset($_SESSION['status'])) {
  header('Location: ' . $base_url);
  exit;
 };
 $lev=error_reporting(8); 
 $status=$_SESSION['status'];
 $barcode=intval(rawurldecode($_GET['barcode']));
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
            <?php print _("Search details"); ?> 
          </h3>
<?php
 if (!(empty($barcode))) {
  $dbconn=sql_connect($plugin_db['oligodb']);
  $result=sql_query("SELECT name, oligo, synthesis, box, rank, freezer, barcode, notes FROM oligo WHERE barcode='" . $barcode . "';",$dbconn);
  if(strlen ($r=sql_last_error($dbconn))) {
   print "          <p>\n            <a href=\"" . $base_url . $plugin . "\"><img src=\"" . $base_url .  "images/oops.png\" alt=\"\"></a>&nbsp;<strong>Oops</strong>\n          </p>\n          <p>\n            $r\n          </p>\n";
  } else {
   $nombre_membre = sql_num_rows($result);
   if ($nombre_membre==1) {
    print "          <table id=\"list-database\" summary=\"\">\n";
    $row=sql_fetch_row($result);
    $A=substr_count($row[1],'A');
    $C=substr_count($row[1],'C');
    $T=substr_count($row[1],'T');
    $G=substr_count($row[1],'G');
    $other=strlen($row[1])-$A-$T-$G-$C;
    $C+=$other;
    $MW=(($A*313.21)+($C*289.18)+($G*329.21)+($T*304.20)-62);
    $E260=(0.89*(($A*15480)+($C*7340)+($G*11760)+($T*8850)));
    print "            <tr class=\"odd\">\n              <td class=\"column-1\">\n                " . _("Name") . "</td>\n              <td class=\"column-2\">\n                $row[0]\n              </td>\n            </tr>\n";
    print "            <tr class=\"even\">\n              <td class=\"column-1\">\n                " . _("Date of synthesis") . "</td>\n              <td class=\"column-2\">\n                " . date(_("m/d/Y"),strtotime($row[2])) . "\n              </td>\n            </tr>\n";
    print "            <tr class=\"odd\">\n              <td class=\"column-1\">\n                " . _("Freezer") . "</td>\n              <td class=\"column-2\">\n                $row[5]\n              </td>\n            </tr>\n";
    print "            <tr class=\"even\">\n              <td class=\"column-1\">\n                " . _("Place") . "</td>\n              <td class=\"column-2\">\n                $row[3]" . (($row[4]!='')?" ($row[4])":'') . "\n              </td>\n            </tr>\n";
    print "            <tr class=\"odd\">\n              <td class=\"column-1\">\n                " . _("Notes") . "</td>\n              <td class=\"column-2\">\n                $row[7]\n              </td>\n            </tr>\n";
    print "            <tr class=\"even\">\n              <td class=\"column-1\">\n                " . _("Sequence") . "</td>\n              <td class=\"column-2\">\n                " . wordwrap($row[1],3,' ',1) . "\n              </td>\n            </tr>\n";
    print "            <tr class=\"odd\">\n              <td class=\"column-1\">\n                " . _("Length") . "</td>\n              <td class=\"column-2\">\n                " . strlen($row[1]) . ' ' . _("bases") . "\n              </td>\n            </tr>\n";
    print "            <tr class=\"even\">\n              <td class=\"column-1\">\n                " . _("Mass") . "</td>\n              <td class=\"column-2\">\n                " . (($other!=0)?('> '):('')) . round($MW,2) . " g/mol\n              </td>\n            </tr>\n";
    print "            <tr class=\"odd\">\n              <td class=\"column-1\">\n                E<sub>260</sub></td>\n              <td class=\"column-2\">\n                " . (($other!=0)?('> '):('')) . round($E260,2) . "\n              </td>\n            </tr>\n";
    print "            <tr class=\"even\">\n              <td class=\"column-1\">\n                pmol/&mu;g</td>\n              <td class=\"column-2\">\n                " . (($other!=0)?('< '):('')) . round(1000000/$MW,2) . "\n              </td>\n            </tr>\n";
    print "            <tr class=\"odd\">\n              <td class=\"column-1\">\n                pmol/DO</td>\n              <td class=\"column-2\">\n                " . (($other!=0)?('< '):('')) . round(1000000000/$E260,2) . "\n              </td>\n            </tr>\n";
    print "            <tr class=\"even\">\n              <td class=\"column-1\">\n                &mu;g/DO</td>\n              <td class=\"column-2\">\n                " . (($other!=0)?('> '):('')) . round(1000*$MW/$E260,2) . "\n              </td>\n            </tr>\n";
    print "            <tr class=\"odd\">\n              <td class=\"column-1\">\n                %CG</td>\n              <td class=\"column-2\">\n                " . (($other!=0)?('n.a.'):(round((($G+$C)/strlen($row[1]))*100,1) .'%')) . "\n              </td>\n          </tr>\n";
    print "            <tr class=\"even\">\n              <td class=\"column-1\">\n                Tm</td>\n              <td class=\"column-2\">\n                " . (($other!=0)?('> '):('')) . round(81.5-16.6+(41*($G+$C)/strlen($row[1]))-(500/strlen($row[1])),2) . "&deg;C&nbsp;&nbsp;&nbsp;<small>([Na<sup>+</sup>]=0.1&nbsp;M)</small>\n              </td>\n            </tr>\n";
    print "          </table>\n";
   };
  };
  if ($row[3]!='' && $row[4]!='') {
   $result2=sql_query("SELECT value FROM config WHERE id='barcode';",$dbconn);
   if(!(strlen($r=sql_last_error($dbconn)))&&(sql_num_rows($result2)==1)) {
    $row2=sql_fetch_row($result2);
    if(isset($row2[0])) {
     printf("          <img src=\"%simages/barcode.php?code=%06d&amp;style=198&amp;type=I25&amp;width=125&amp;height=50&amp;xres=2&amp;font=3\" alt=\"\">\n",$base_url,$row[6]);			
    };
   };
  };
  $result2=sql_query("SELECT name1, name2, notes FROM couple WHERE (name1='" . addslashes($row[0]) . "') OR (name2='" . addslashes($row[0]) . "');",$dbconn);
  if(strlen($r=sql_last_error($dbconn))) {
   print "          <p>\n            <a href=\"" . $base_url . $plugin . "\"><img src=\"" . $base_url .  "images/oops.png\" alt=\"\"></a>&nbsp;<strong>Oops</strong>\n          </p>\n          <p>\n            $r\n          </p>\n";
  } else {
   $nombre_couple=sql_num_rows($result2);
   if ($nombre_couple!=0) {
	  $i=0;
    print "          <h3>\n            " . (($nombre_membre>1)? _("Associated oligonucleotides"): _("Associated oligonucleotide")) . "\n          </h3>\n";
    print "          <table id=\"list-database\" summary=\"\">\n";
    while($row2=sql_fetch_row($result2)) {
     print '          <tr class="' . (($i++%2 ==0) ? 'odd' : 'even' ) . "\">\n            <td class=\"column-1\">\n              &nbsp;$row2[0] / $row2[1]&nbsp;\n            </td>\n            <td class=\"column-2\">\n              &nbsp;$row2[2]&nbsp;\n            </td>\n          </tr>\n";
    };
    print "          </table>\n";
   };
  };
 };
?>
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

