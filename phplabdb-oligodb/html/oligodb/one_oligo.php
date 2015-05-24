<?php
 ob_start("ob_gzhandler");
 session_start();
 include_once '../includes/login.inc';
 if (!isset($_SESSION['status'])) {
  header('Location: ' . $base_url);
  exit;
 };
 $lev=error_reporting(8);
 function oligoID($string) { #alias crc20 ?!!
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
 function newtime() {
  $list_date=getdate();
  $day = '<select name="date_D" title="' . _("day of synthesis") . '">';
  for ($i=1 ; $i<32; $i++) 
   $day .= '<option' . (($list_date['mday']==$i)? ' selected="selected">':'>') . $i . '</option>';
  $day .= '</select>';
  $month = '<select name="date_M" title="' . _("month of synthesis") . '">';
  for ($i=1 ; $i<13; $i++) 
   $month .= '<option' . (($list_date['mon']==$i)? ' selected="selected">':'>') . $i . '</option>';
  $month .= '</select>';
  $year = '<select name="date_Y" title="' . _("year of synthesis") . '">';
  for ($i=1985 ; $i<=($list_date['year']); $i++)
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
 $dbconn=sql_connect($plugin_db['oligodb']);
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
            <?php print _("New oligonucleotides"); ?> 
          </h3>

<?php if (!(isset($_POST['new']))) { ?>
          <p>
            <?php print _("Inform all following information:"); ?><br>
            <sup>*</sup> <small><?php print _("needed information"); ?></small>
          </p>
          <form method="post" action="<?php print $base_url . $plugin; ?>one_oligo.php">
            <table summary="">
              <tr>
                <td>
                  &nbsp;<label for="name"><?php print _("Name"); ?></label>*&nbsp;
                </td>
                <td>
                  &nbsp;<input type="text" id="name" maxlength="100" name="name" title="<?php print _("Name"); ?>">&nbsp;
                </td>
              </tr>
              <tr>
                <td>
                  &nbsp;<label for="sequence"><?php print _("Sequence"); ?></label>*&nbsp;
                </td>
                <td>
                  &nbsp;<textarea name="sequence" id="sequence" rows="2" cols="50" title="<?php print _("Oligonucleotide sequence"); ?>"></textarea>&nbsp;
                </td>
              </tr>
              <tr>
                <td valign="top">
                  &nbsp;<?php print _("Date of synthesis"); ?>*&nbsp;
                </td>
                <td>
                  &nbsp;<?php print newtime(); ?>&nbsp;
                </td>
              </tr>
              <tr>
                <td>
                  &nbsp;<label for="freezer"><?php print _("Freezer"); ?></label>&nbsp;
                </td>
                <td>
                  &nbsp;<input type="text" id="freezer" maxlength="20" name="freezer" title="<?php print _("Freezer"); ?>">&nbsp;
                </td>
              </tr>
              <tr>
                <td>
                  &nbsp;<label for="box"><?php print _("Box"); ?></label>*&nbsp;
                </td>
                <td>
                  &nbsp;<select name="box" id="box" title="<?php print _("Box"); ?>"><option></option><?php for ($i=1 ; $i<101; $i++) {print "<option>$i</option>"; }; ?></select>&nbsp;
                </td>
              </tr>
              <tr>
                <td>
                  &nbsp;<label for="rank"><?php print _("Rank"); ?></label>*&nbsp;
                </td>
                <td>
                  &nbsp;<select name="rank" id="rank"title="<?php print _("Place on the box"); ?>"><option></option><?php for ($i=1 ; $i<101; $i++) {print "<option>$i</option>"; }; ?></select>&nbsp;
                </td>
              </tr>
              <tr>
                <td>
                  &nbsp;<label for="notes"><?php print _("Comment"); ?></label>&nbsp;
                </td>
                <td>
                  &nbsp;<textarea name="note" id="note" cols="50" rows="6" title="<?php print _("Comment"); ?>"></textarea>&nbsp;
                </td>
              </tr>
            </table>
            <div>
              &nbsp;<br>
              <input type="hidden" name="new" value="1"><input type="submit" value="<?php print _("Add"); ?>">
            </div>
          </form>
<?php } elseif (($_POST['new']==1) && (!(empty($_POST['name'])))) {
   unset($_SESSION['oligo_liste']);
   if((strlen(trim($_POST['name']))>0) && (strlen(trim($_POST['sequence']))>1) && (($newdate=mktime(0, 0, 0, intval($_POST['date_M']), intval($_POST['date_D']), intval($_POST['date_Y'])))!=-1) && (intval(trim($_POST['box']))>0) && (intval(trim($_POST['rank']))>0) ) {
    $name=trim(stripslashes($_POST['name']));
    $seq=trim(stripslashes(strtoupper(str_replace(" ", "", $_POST['sequence']))));
    $box=intval(trim($_POST['box']));
    $rank=intval(trim($_POST['rank']));
		$freezer=((isset($_POST['freezer']) && trim(stripslashes($_POST['freezer']))!='')?("'".addslashes(htmlentities(trim(stripslashes($_POST['freezer']))))."'"):'NULL');
    //freezer, box and rank already exist ?
    $result=sql_query("SELECT name FROM oligo WHERE freezer=$freezer AND box=$box AND rank=$rank;",$dbconn);
		if (!(strlen ($r=sql_last_error($dbconn)))) {
     if (sql_num_rows($result)==1) {
      $row=sql_fetch_row($result);
      print "          <p>\n            <a href=\"" . $base_url . $plugin . "one_oligo.php\"><img src=\"" . $base_url .  'images/no.png" alt=""></a>&nbsp;<strong>' . _("The place") . ((isset($_POST['freezer']) && trim(stripslashes($_POST['freezer']))!='')?(' <strong>'.htmlentities(trim(stripslashes($_POST['freezer']))).'</strong> -'):'') . " $box ($rank) " . _("is already used by") . " '$row[0]'</strong>\n          </p>\n";
     } else {;
      //oligo already exist ?
      $result=sql_query("SELECT box,rank,freezer FROM oligo WHERE name='" . addslashes(htmlentities($name)) . "' OR oligo='$seq';",$dbconn);
      if (sql_num_rows($result)==1) {
       $row= sql_fetch_row($result);
       print "          <p>\n            <a href=\"" . $base_url . $plugin . "one_oligo.php\"><img src=\"" . $base_url . 'images/no.png" alt=""></a>&nbsp;<strong>' . _("The oligonucleotide already exist in box") . ((isset($row[2]))?(" <strong>$row[2]</strong> -"):'') . " $row[0] " . _("rank") . " $row[1]</strong>\n          </p>\n";
      } else {
       //analyse
       $liste_oligo[0]=1;
       $liste_oligo[1]=$name;
       $liste_oligo[2]=$seq;
       $liste_oligo[3]=$newdate;
       $liste_oligo[4]=$box;
       $liste_oligo[5]=$rank;
       $liste_oligo[6]=trim(stripslashes($_POST['note']));
       $liste_oligo[7]=array();
       $liste_oligo[8]=((isset($_POST['freezer']))?trim(stripslashes($_POST['freezer'])):'');			 
       $_SESSION['oligo_liste']=$liste_oligo;
       print '          <form method="post" action="' . $base_url . $plugin . 'one_oligo_couple.php">';
?>

            <table id="list-database" width="100%" summary="">
              <tr>
                <th>
                  <?php print _("Name"); ?> 
                </th>
                <th>
                  <?php print _("Sequence"); ?> 
                </th>
                <th>
                  <?php print _("Date"); ?> 
                </th>
                <th>
                  <?php print _("Box"); ?> 
                </th>
                <th>
                  <?php print _("Association"); ?> 
                </th>
              </tr>
<?php
       print "              <tr class=\"odd\" >\n                <td  class=\"column-1\">\n                  &nbsp;" . $liste_oligo[1] . "&nbsp;\n                </td>\n                <td class=\"column-2\">\n                  &nbsp;<small>" . wordwrap(((strlen($liste_oligo[2])>39)?(substr($liste_oligo[2],0,39).'...'):($liste_oligo[2])),3,' ',1) . "</small>&nbsp;\n                </td>\n                <td class=\"column-3\">\n                  &nbsp;" . date(_("m/d/Y"),$liste_oligo[3]) . "&nbsp;\n                </td>\n                <td class=\"column-3\">\n                  &nbsp;" . (($liste_oligo[8]=='') ? '' : ('<strong>' . $liste_oligo[8] . '</strong> - ')) . $liste_oligo[4] . ' (' . $liste_oligo[5] . ")&nbsp;\n                </td>\n                <td  class=\"column-3\">\n                  <input type=\"image\" src=\"images/edit.png\" name=\"oligo\" value=\"send\" alt=\"" . _("association") . "\">\n                </td>\n              </tr>\n";
       if(!empty($liste_oligo[6])) { print "              <tr class=\"odd\" >\n                <td class=\"column-1\">\n                </td>\n                <td colspan=\"4\">\n                  &nbsp;" . ((strlen($liste_oligo[6])>60)?(substr($liste_oligo[6],0,60).'...'):($liste_oligo[6])) . "&nbsp;\n                </td>\n              </tr>\n"; };
       print "            </table>\n          </form>\n";
?>
          <p>
            &nbsp;
          </p>
          <form method="post" action="<?php print $base_url . $plugin; ?>one_oligo.php">
            <div>
              <input type="hidden" name="new" value="3"><input type="submit" value="<?php print _("Next"); ?>">
            </div>
          </form>
<?php
      };
     };
    } else {
     print "          <p>\n            <a href=\"" . $base_url . $plugin . "one_oligo.php\"><img src=\"" . $base_url . 'images/no.png" alt=""></a>&nbsp;<strong>' . _("No valid data!") . "</strong>\n          </p>\n";
    };
   } else {
    print "          <p>\n            <a href=\"" . $base_url . $plugin . "one_oligo.php\"><img src=\"" . $base_url . 'images/no.png" alt=""></a>&nbsp;<strong>' . _("No valid data!") . "</strong>\n          </p>\n";
   };
  } elseif (($_POST['new']==2) && (isset($_SESSION['oligo_liste']))) {
   $liste_oligo=$_SESSION['oligo_liste'];
   print '          <form method="post" action="' . $base_url . $plugin . 'one_oligo_couple.php">';
?>

            <table id="list-database" width="100%" summary="">
              <tr>
              <tr>
                <th>
                  <?php print _("Name"); ?> 
                </th>
                <th>
                  <?php print _("Sequence"); ?> 
                </th>
                <th>
                  <?php print _("Date"); ?> 
                </th>
                <th>
                  <?php print _("Box"); ?> 
                </th>
                <th>
                  <?php print _("Association"); ?> 
                </th>
              </tr>
<?php
      print "              <tr class=\"odd\" >\n                <td  class=\"column-1\">\n                  &nbsp;" . $liste_oligo[1] . "&nbsp;\n                </td>\n                <td class=\"column-2\">\n                  &nbsp;<small>" . wordwrap(((strlen($liste_oligo[2])>39)?(substr($liste_oligo[2],0,39).'...'):($liste_oligo[2])),3,' ',1) . "</small>&nbsp;\n                </td>\n                <td class=\"column-3\">\n                  &nbsp;" . date(_("m/d/Y"),$liste_oligo[3]) . "&nbsp;\n                </td>\n                <td class=\"column-3\">\n                  &nbsp;" . (($liste_oligo[8]=='') ? '' : ('<strong>' . $liste_oligo[8] . '</strong> - ')) . $liste_oligo[4] . ' (' . $liste_oligo[5] . ")&nbsp;\n                </td>\n                <td  class=\"column-3\">\n                  <input type=\"image\" src=\"images/edit" . ((count($liste_oligo[7])>0)?'ed':'') . ".png\" name=\"oligo\" value=\"send\" alt=\"" . _("association") . "\">\n                </td>\n              </tr>\n";
      if(!empty($liste_oligo[6])) { print "              <tr class=\"odd\" >\n                <td class=\"column-1\">\n                </td>\n                <td colspan=\"4\">\n                  &nbsp;" . ((strlen($liste_oligo[6])>60)?(substr($liste_oligo[6],0,60).'...'):($liste_oligo[6])) . "&nbsp;\n                </td>\n              </tr>\n"; };
      print "            </table>\n          </form>\n";
?>
          <p>
            &nbsp;
          </p>
          <form method="post" action="<?php print $base_url . $plugin; ?>one_oligo.php">
            <div>
              <input type="hidden" name="new" value="3"><input type="submit" value="<?php print _("Next"); ?>">
            </div>
          </form>
<?php
  } elseif (($_POST['new']==3) && (isset($_SESSION['oligo_liste']))) { //save...
	 $k=0;
   $liste_oligo=$_SESSION['oligo_liste'];
   $msg = "          <form method=\"post\" action=\"" . $base_url . $plugin . "one_oligo.php\">\n";
   foreach($liste_oligo[7] as $key){
    $msg .= "            <table width=\"200\" summary=\"\">\n              <tr>\n                <td align=\"left\">\n                  &nbsp;" . _("Association") . ' ' . $liste_oligo[1] . ' / ' . $key . '&nbsp;-&nbsp;(' . _("description") . ")&nbsp;\n                </td>\n              </tr>\n              <tr>\n                <td>\n                  <textarea name=\"rmq[" . ++$k . "]\" rows=\"4\" cols=\"60\"></textarea>\n                <td>\n              </tr>\n            </table>\n            <p>\n              &nbsp;\n            </p>\n";
   };
   $msg .= "              <div>\n                <input type=\"hidden\" name=\"new\" value=\"4\"><input type=\"submit\" value=\"" . _("Finish") . "\">\n              </div>\n            </form>\n";
   if($k>0) {
    print $msg;
   } else {
    $ID_string=$liste_oligo[1];
    do {
     $ID=oligoID($ID_string);
     if (($ID<1000000)&&($ID!=0)) {
      $resultID=sql_query('SELECT barcode FROM oligo WHERE barcode=' . $ID . ';',$dbconn);
     };
     $ID_string=($liste_oligo[1].dechex(time()));
    } while (sql_num_rows($resultID)!=0);
    $result=sql_query("INSERT INTO oligo (name, oligo, synthesis, box, rank, freezer, barcode, notes) VALUES ('" . addslashes(htmlentities($liste_oligo[1])) . "','" . addslashes($liste_oligo[2]) . "','" . date("Y-m-d",$liste_oligo[3]) . "'," . $liste_oligo[4] . ',' . $liste_oligo[5] . ',' . (($liste_oligo[8]!='')?("'".addslashes(htmlentities($liste_oligo[8]))."'"):'NULL') . ',' . $ID . ",'" . addslashes(htmlentities($liste_oligo[6])) . "');",$dbconn);
    if(strlen($r=sql_last_error($dbconn))) {
     print "          <p>\n            <a href=\"" . $base_url . $plugin . "one_oligo.php\"><img src=\"" . $base_url . 'images/no.png" alt=""></a>&nbsp;' . _("Oligo") . ' <strong>' . $liste_oligo[1] . "</strong>: Oops<br>\n            $r\n          </p>\n";
    } else {
     print "          <p>\n            <a href=\"" . $base_url . $plugin . "\"><img src=\"" . $base_url . 'images/ok.png" alt=""></a>&nbsp;' . _("Oligo") . ' <strong>' . $liste_oligo[1] . '</strong>: ' . _("added") . "\n          </p>\n";
     unset($_SESSION['oligo_liste']);
    };
   };
  } elseif (($_POST['new']==4) && (isset($_SESSION['oligo_liste']))) { //save with association...
   $liste_oligo=$_SESSION['oligo_liste'];
   $rmq=$_POST['rmq'];
   $ID_string=$liste_oligo[1];
   do {
    $ID=oligoID($ID_string);
    $resultID=sql_query('SELECT barcode FROM oligo WHERE barcode=' . $ID . ';',$dbconn);
    $ID_string=($liste_oligo[1].dechex(time()));
   } while ((sql_num_rows($resultID)!=0)&&($ID>1000000)&&($ID==0));
   $result=sql_query("INSERT INTO oligo (name, oligo, synthesis, box, rank, freezer, barcode, notes) VALUES ('" . addslashes(htmlentities($liste_oligo[1])) . "','" . addslashes($liste_oligo[2]) . "','" . date("Y-m-d",$liste_oligo[3]) . "'," . $liste_oligo[4] . ',' . $liste_oligo[5] . ',' . (($liste_oligo[8]!='')?("'".addslashes(htmlentities($liste_oligo[8]))."'"):'NULL') . ',' . $ID . ",'" . addslashes(htmlentities($liste_oligo[6])) . "');",$dbconn);
   if(strlen($r=sql_last_error($dbconn))) {
    print "          <p>\n            <a href=\"" . $base_url . $plugin . "one_oligo.php\"><img src=\"" . $base_url . 'images/no.png" alt=""></a>&nbsp;' . _("Oligo") . ' <strong>' . $liste_oligo[1] . "</strong>: Oops<br>\n            $r\n          </p>\n";
    $stop=1;
   } else {
		$k=0;
    print "          <p>\n            <a href=\"" . $base_url . $plugin . "\"><img src=\"" . $base_url . 'images/ok.png" alt=""></a>&nbsp;' . _("Oligo") . ' <strong>' . $liste_oligo[1] . '</strong>: ' . _("added") . "\n          </p>\n";
    unset($_SESSION['oligo_liste']);
    unset($stop);
    foreach($liste_oligo[7] as $key) {
     if(isset($rmq[++$k])) {
      $result=sql_query("INSERT INTO couple (name1, name2, notes) VALUES ('" . addslashes(htmlentities($liste_oligo[1])) . "','" . addslashes(htmlentities($key)) . "','" . addslashes(htmlentities(trim(stripslashes($rmq[$k])))) . "');",$dbconn);
      if(strlen ($r=sql_last_error($dbconn))) {
       print "          <p>\n            <a href=\"" . $base_url . $plugin . "one_oligo.php\"><img src=\"" . $base_url . 'images/no.png" alt=""></a>&nbsp;' . _("Association") . ' <strong>' . $liste_oligo[1] . " / $key</strong>: Oops<br>\n            $r\n          </p>\n";
      } else {
       print "          <p>\n            <a href=\"" . $base_url . $plugin . "\"><img src=\"" . $base_url . 'images/ok.png" alt=""></a>&nbsp;' . _("Association") . ' <strong>' . $liste_oligo[1] . " / $key</strong>: " . _("added") . "\n          </p>\n";
      };
     };
    };
   };
  } else {
   print "          <p>\n            <a href=\"" . $base_url . $plugin . "one_oligo.php\"><img src=\"" . $base_url . 'images/no.png" alt=""></a>&nbsp;<strong>' . _("No valid data!") . "</strong>\n          </p>\n";
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

