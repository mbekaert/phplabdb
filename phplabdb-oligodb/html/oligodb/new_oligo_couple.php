<?php
 ob_start("ob_gzhandler");
 session_start();
 include_once '../includes/login.inc';
 if (!isset($_SESSION['status'])) {
  header('Location: ' . $base_url);
  exit;
 };
 $lev=error_reporting(8); 
 function printer($oligo,$liste_oligo) {
  for($i=0;$i<count($liste_oligo);$i++) {
   if ($liste_oligo[$i][0]!=$oligo) {
    print '<tr class="' . (($i%2==0) ? 'odd' : 'even' ) . '" ><td class="column-1">&nbsp;' . $liste_oligo[$i][1] . '</a>&nbsp;</td><td class="column-2">&nbsp;<small>' . wordwrap(((strlen($liste_oligo[$i][2])>39)?(substr($liste_oligo[$i][2],0,39).'...'):($liste_oligo[$i][2])),3,' ',1) . '</small>&nbsp;</td><td class="column-3"><input type="checkbox" name="oligos[' . $i . ']" ' . ((in_array($liste_oligo[$oligo][1],$liste_oligo[$i][7]))?'checked="checked" ':'') . '/></td></tr>';
   };
  };
  if(isset($liste_oligo[$oligo][8])){
   foreach($liste_oligo[$oligo][8] as $newcouple) {
    print '<tr class="' . (($i%2==1) ? 'odd' : 'even' ) . '" ><td class="column-1">&nbsp;' . $newcouple . '</a>&nbsp;</td><td class="column-2">&nbsp;<small>' . _("Oligonucleotide from database") . '</small>&nbsp;</td><td class="column-3"><input type="checkbox" name="oligos[' . $i++ . ']" checked="checked"></td></tr>';
   };
  };
  return $i;
 };
 function affiche_recherche($data,$type,$j,$lister) {
  global $plugin_db;
  $dbconn=sql_connect($plugin_db['oligodb']);
  if ($type=='synthesis') $data=date("Y-m-d",strtotime($data));
  $result=sql_query("SELECT name, oligo FROM oligo WHERE $type" . (($type=='box')?("='". addslashes(htmlentities($data)) ."'"):(sql_reg(addslashes(htmlentities($data))))) . " ORDER BY " . (($type=='box')?('rank'):($type)) . ';',$dbconn);
  if (!(strlen($r=sql_last_error($dbconn)))) {
   $nombre_membre=sql_num_rows( $result );
   if ($nombre_membre!=0) {
    while($row=sql_fetch_row($result)) {
     if (!(in_array($row[0],$lister))) {
      print '<tr class="' . (($j++%2==1) ? 'odd' : 'even' ) . '"><td class="column-1">&nbsp;' . $row[0] . '&nbsp;</td><td class="column-2">&nbsp;<small>' . wordwrap(((strlen($row[1])>39)?(substr($row[1],0,39).'...'):($row[1])),3,' ',1) . '</small>&nbsp;</td><td class="column-3"><input type="checkbox" name="couple[' . $j . ']" value="' . rawurlencode($row[0]) . '"></td></tr>';
     };
    };
   };
  };
 };
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
            <?php print _("New oligonucleotide associations"); ?> 
          </h3>
          <form method="post" action="<?php print $base_url . $plugin; ?>new_oligo_couple.php">
            <table id="list-database" width="100%" summary="">
              <tr>
                <th>
                  <?php print _("Name"); ?> 
                </th>
                <th>
                  <?php print _("Sequence"); ?> 
                </th>
                <th>
                  <?php print _("Select"); ?> 
                </th>
              </tr>
<?php
  if ((isset($_POST['oligo'])) && (!(isset($_POST['type'])))) {
   $liste_oligo=$_SESSION['oligo_liste'];
   list($oligo,)=each($_POST['oligo']);
   printer($oligo, $liste_oligo);
   } else {
   $liste_oligo=$_SESSION['oligo_liste'];
   $oligos=(isset($_POST['oligos'])?$_POST['oligos']:array());
   $oligo=$_POST['oligo'];
   for($i=0;$i<count($liste_oligo);$i++) {
    if ($liste_oligo[$i][0]!=$oligo) {
     if (isset($oligos[$i])) {
      $liste_oligo[$i][7][]=$liste_oligo[$oligo][1];
      $liste_oligo[$oligo][7][]=$liste_oligo[$i][1];
      } elseif (in_array($liste_oligo[$oligo][1],$liste_oligo[$i][7])) {
      unset($liste_oligo[$i][7][array_search($liste_oligo[$oligo][1],$liste_oligo[$i][7])]);
      unset($liste_oligo[$oligo][7][array_search($liste_oligo[$i][1],$liste_oligo[$oligo][7])]);
     };
     $liste_oligo[$i][7]=array_unique($liste_oligo[$i][7]);
    };
   };
   if(isset($liste_oligo[$oligo][8])){
    foreach($liste_oligo[$oligo][8] as $newcouple) {
     if(!(isset($oligos[$i++]))) {
      unset($liste_oligo[$oligo][8][array_search(rawurldecode($newcouple),$liste_oligo[$oligo][8])]);
     };
    };
   };
   if(isset($_POST['couple'])){
    foreach($_POST['couple'] as $newcouple) {
     $liste_oligo[$oligo][8][]=rawurldecode($newcouple);
    };
   };
   $liste_oligo[$oligo][7]=array_unique($liste_oligo[$oligo][7]);
   $liste_oligo[$oligo][8]=array_unique($liste_oligo[$oligo][8]);
   $_SESSION['oligo_liste']=$liste_oligo;
   $j=printer($oligo, $liste_oligo);
   if (!(empty($_POST['data']))){
    affiche_recherche($_POST['data'],$_POST['type'],$j,$liste_oligo[$oligo][8]);
   };
  };
?>
            </table>
            <p>
              <?php print _("Enter the name of the searched oligonucleotide, or a part of its sequence:"); ?> 
            </p>
            <table summary="">
              <tr>
                <td>
                  <select name="type">
                    <option value="name">
                      <?php print _("Name"); ?> 
                    </option>
                    <option value="oligo">
                      <?php print _("Sequence"); ?> 
                    </option>
                    <option value="synthesis">
                      <?php print _("Date of synthesis"); ?> 
                    </option>
                    <option value="box">
                      <?php print _("Box"); ?> 
                    </option>
                    <option value="freezer">
                      <?php print _("Freezer"); ?> 
                    </option>										
                    <option value="notes">
                      <?php print _("Notes"); ?> 
                    </option>
                    <option value="barcode">
                      <?php print _("ID"); ?> 
                    </option>
                  </select>
                </td>
                <td>
                  <input type="text" size="20" maxlength="100" name="data">
                </td>
              </tr>
              <tr>
                <td colspan="2">
                  <input type="hidden" name="oligo" value="<?php print $oligo; ?>"><input name="clear" type="reset" value="<?php print _("Clear"); ?>"> &nbsp; <input type="submit" name="submit" value="<?php print _("Validate"); ?>">
                </td>
              </tr>
            </table>
          </form>
          <h3>
            <?php print _("Back"); ?> 
          </h3>
          <form method="post" action="<?php print $base_url . $plugin; ?>new_oligo.php">
            <div>
              <?php print _("Don&#39;t forget to validate your selection before back"); ?>.<br>
               <input type="hidden" name="new" value="2"><input type="submit" value="<?php print _("Back"); ?>">
            </div>
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

