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
 function newtime($oldtime) {
  switch (((isset($_COOKIE['lang']))?$_COOKIE['lang']:'en_US')) {
   case 'fr_FR':
    $day=substr($oldtime,0,2);
    $month=substr($oldtime,3,2);
    $year=substr($oldtime,6,4);
    break;
   default:
    $month=substr($oldtime,0,2);
    $day=substr($oldtime,3,2);
    $year=substr($oldtime,6,4);
  };
  return strtotime($month . '/' . $day . '/' . $year);
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
<?php if (!(isset($_POST['new']))) { ?>
    <script type="text/javascript">
    //<![CDATA[
function chkFormulaire() {
 if(document.forms(0).data.value == "") {
  alert("<?php textdomain('oligodb'); print _("There is no oligo!"); textdomain('phplabdb'); ?>");
  return false;
 };
};
    //]]>
    </script>
<?php }; ?>
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
            <?php print _("For each new oligonucleotide indicate successively (one line per nucleotide):"); ?><br>
            <code><?php print _("name ; sequence ; date {mm/dd/yyyy} ; box ; rank [ ; [ freezer ] ; comment ]"); ?></code>
          </p>
          <form method="post" action="<?php print $base_url . $plugin; ?>new_oligo.php" onsubmit="return chkFormulaire()">
            <div>
              <textarea cols="60" rows="10" name="data"></textarea><br>
               &nbsp;<br>
               <input type="hidden" name="new" value="1"><input type="submit" value="<?php print _("Add"); ?>">
             </div>
          </form>
<?php } elseif (($_POST['new']==1) && (!(empty($_POST['data'])))) {
   unset($_SESSION['oligo_liste']);
   $i=0;
   foreach(explode("\n",$_POST['data']) as $key) {
    if(strlen(trim(stripslashes($key)))>20) {
     $liste=explode(";",$key,7);
     if((strlen(trim($liste[0]))>0) && (strlen(trim($liste[1]))>1) && (strlen(trim($liste[2]))==10) && (($newdate=newtime(trim($liste[2])))!=-1) && (intval(trim($liste[3]))>0) && (intval(trim($liste[4]))>0) ) {
      $name=trim(stripslashes($liste[0]));
      $seq=trim(stripslashes(strtoupper(str_replace(" ", "", $liste[1]))));
      $box=intval(trim($liste[3]));
      $rank=intval(trim($liste[4]));
  		$freezer=((isset($liste[5]) && trim(stripslashes($liste[5]))!='')?("'".addslashes(htmlentities(trim(stripslashes($liste[5]))))."'"):'NULL');			
      //freezer, box and rank already exist ?
      $result=sql_query("SELECT name FROM oligo WHERE freezer=$freezer AND box=$box AND rank=$rank;",$dbconn);
      if (sql_num_rows($result)==1) {
       $row=sql_fetch_row($result);
       print "          <p>\n            <a href=\"" . $base_url . $plugin . "new_oligo.php\"><img src=\"" . $base_url .  'images/no.png" alt=""></a>&nbsp;<strong>' . _("The place") . ((isset($liste[5]) && trim(stripslashes($liste[5]))!='')?(' <strong>'.htmlentities(trim(stripslashes($liste[5]))).'</strong> -'):'') . " $box ($rank) " . _("is already used by") . " '$row[0]'</strong>\n          </p>\n";
       } else {;
       //oligo already exist ?
       $result=sql_query("SELECT box,rank,freezer FROM oligo WHERE name='" . addslashes(htmlentities($name)) . "' OR oligo='$seq';",$dbconn);
       if (sql_num_rows($result)==1) {
        $row= sql_fetch_row($result);
        print "          <p>\n            <a href=\"" . $base_url . $plugin . "new_oligo.php\"><img src=\"" . $base_url . 'images/no.png" alt=""></a>&nbsp;<strong>' . _("The oligonucleotide already exists in box") . ((isset($row[2]))?(" <strong>$row[2]</strong> -"):'') . " $row[0] " . _("rank") . " $row[1]</strong>\n          </p>\n";
        } else {;
        //analyse
        $liste_oligo[$i][0]=$i;
        $liste_oligo[$i][1]=$name;
        $liste_oligo[$i][2]=$seq;
        $liste_oligo[$i][3]=$newdate;
        $liste_oligo[$i][4]=$box;
        $liste_oligo[$i][5]=$rank;
        $liste_oligo[$i][6]=trim(stripslashes($liste[6]));
        $liste_oligo[$i][7]=array();
        $liste_oligo[$i][8]=array();
        $liste_oligo[$i++][9]=((isset($liste[5]))?trim(stripslashes($liste[5])):'');				
       };
      };
     };
    };
   };
   if (isset($liste_oligo)) {
    foreach($liste_oligo as $key) {
     foreach($liste_oligo as $key2) {
      if($key[0]!=$key2[0]) {
       if($key[1]==$key2[1]){ $verif_nom=false; };
       if($key[2]==$key2[2]){ $verif_seq=false; };
       if(($key[9]==$key2[9]) && ($key[4]==$key2[4]) && ($key[5]==$key2[5])){ $verif_pos=false; };
      };
     };
    };
    if(isset($verif_nom)) {
     print "          <p>\n            <a href=\"" . $base_url . $plugin . "new_oligo.php\"><img src=\"" . $base_url . 'images/no.png" alt=""></a>&nbsp;<strong>' . _("A oligonucleotide was entered several times") . "</strong>\n          </p>\n";
    } elseif(isset($verif_seq)){
     print "          <p>\n            <a href=\"" . $base_url . $plugin . "new_oligo.php\"><img src=\"" . $base_url . 'images/no.png" alt=""></a>&nbsp;<strong>' . _("The same sequence was entered several times") . "</strong>\n          </p>\n";
    } elseif(isset($verif_pos)){
     print "          <p>\n            <a href=\"" . $base_url . $plugin . "new_oligo.php\"><img src=\"" . $base_url . 'images/no.png" alt=""></a>&nbsp;<strong>' . _("The same place is used several times") . "</strong>\n          </p>\n";
    } else {
     $_SESSION['oligo_liste']=$liste_oligo;
     print '          <form method="post" action="' . $base_url . $plugin . 'new_oligo_couple.php">';
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
     for($i=0;$i<count($liste_oligo);$i++) {
      print '              <tr class="' . (($i%2==1)?'odd':'even') . "\" >\n                <td  class=\"column-1\">\n                  &nbsp;" . $liste_oligo[$i][1] . "&nbsp;\n                </td>\n                <td class=\"column-2\">\n                  &nbsp;<small>" . wordwrap(((strlen($liste_oligo[$i][2])>39)?(substr($liste_oligo[$i][2],0,39).'...'):($liste_oligo[$i][2])),3,' ',1) . "</small>&nbsp;\n                </td>\n                <td class=\"column-3\">\n                  &nbsp;" . date(_("m/d/Y"),$liste_oligo[$i][3]) . "&nbsp;\n                </td>\n                <td class=\"column-3\">\n                  &nbsp;" . (($liste_oligo[$i][9]=='') ? '' : ('<strong>' . $liste_oligo[$i][9] . '</strong> - ')) . $liste_oligo[$i][4] . ' (' . $liste_oligo[$i][5] . ")&nbsp;\n                </td>\n                <td  class=\"column-3\">\n                  <input type=\"image\" src=\"images/edit.png\" name=\"oligo[$i]\" value=\"send\" alt=\"" . _("association") . "\">\n                </td>\n              </tr>\n";
      if(!empty($liste_oligo[$i][6])) { print '              <tr class="' . (($i%2==1)?'odd':'even') . "\" >\n                <td class=\"column-1\">\n                </td>\n                <td colspan=\"4\">\n                  &nbsp;" . ((strlen($liste_oligo[$i][6])>60)?(substr($liste_oligo[$i][6],0,60).'...'):($liste_oligo[$i][6])) . "&nbsp;\n                </td>\n              </tr>\n"; };
     };
     print "            </table>\n          </form>\n";
?>
          <p>
            &nbsp;
          </p>
          <form method="post" action="<?php print $base_url . $plugin; ?>new_oligo.php">
            <div>
              <input type="hidden" name="new" value="3"><input type="submit" value="<?php print _("Next"); ?>">
            </div>
          </form>
<?php
    };
    } else {
    print "          <p>\n            <a href=\"" . $base_url . $plugin . "new_oligo.php\"><img src=\"" . $base_url . 'images/no.png" alt=""></a>&nbsp;<strong>' . _("No valid data!") . "</strong>\n          </p>\n";
   };
  } elseif (($_POST['new']==2) && (isset($_SESSION['oligo_liste']))) { //Affichage simple...
   $liste_oligo=$_SESSION['oligo_liste'];
   print '          <form method="post" action="' . $base_url . $plugin . 'new_oligo_couple.php">';
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
   for($i=0;$i<count($liste_oligo);$i++) {
    print '              <tr class="' . (($i%2==1)?'odd':'even') . "\" >\n                <td  class=\"column-1\">\n                  &nbsp;" . $liste_oligo[$i][1] . "&nbsp;\n                </td>\n                <td class=\"column-2\">\n                  &nbsp;<small>" . wordwrap(((strlen($liste_oligo[$i][2])>39)?(substr($liste_oligo[$i][2],0,39).'...'):($liste_oligo[$i][2])),3,' ',1) . "</small>&nbsp;\n                </td>\n                <td class=\"column-3\">\n                  &nbsp;" . date(_("m/d/Y"),$liste_oligo[$i][3]) . "&nbsp;\n                </td>\n                <td class=\"column-3\">\n                  &nbsp;" . (($liste_oligo[$i][9]=='') ? '' : ('<strong>' . $liste_oligo[$i][9] . '</strong> - ')) . $liste_oligo[$i][4] . ' (' . $liste_oligo[$i][5] . ")&nbsp;\n                </td>\n                <td  class=\"column-3\">\n                  <input type=\"image\" src=\"images/edit" . (((count($liste_oligo[$i][7])>0)||(count($liste_oligo[$i][8])>0))?'ed':'') . ".png\" name=\"oligo[$i]\" value=\"send\" alt=\"" . _("association") . "\">\n                </td>\n              </tr>\n";
    if(!empty($liste_oligo[$i][6])) { print '              <tr class="' . (($i%2==1)?'odd':'even') . "\" >\n                <td class=\"column-1\">\n                </td>\n                <td colspan=\"4\">\n                  &nbsp;" . ((strlen($liste_oligo[$i][6])>60)?(substr($liste_oligo[$i][6],0,60).'...'):($liste_oligo[$i][6])) . "&nbsp;\n                </td>\n              </tr>\n"; };
   };
   print "            </table>\n          </form>\n";
?>
          <p>
            &nbsp;
          </p>
          <form method="post" action="<?php print $base_url . $plugin ; ?>new_oligo.php">
            <div>
              <input type="hidden" name="new" value="3"><input type="submit" value="<?php print _("Next"); ?>">
            </div>
          </form>
<?php
  } elseif (($_POST['new']==3) && (isset($_SESSION['oligo_liste']))) { //save...
	 $k=0;
   $liste_oligo=$_SESSION['oligo_liste'];
   $msg = "          <form method=\"post\" action=\"" . $base_url . $plugin . "new_oligo.php\">\n";
   for($i=0;$i<count($liste_oligo);$i++) {
    foreach($liste_oligo[$i][8] as $key){
     $msg .= "            <table width=\"200\" summary=\"\">\n              <tr>\n                <td align=\"left\">\n                  &nbsp;" . _("Association") . ' ' . $liste_oligo[$i][1] . ' / ' . $key . '&nbsp;-&nbsp;(' . ("description") . ")&nbsp;\n                </td>\n              </tr>\n              <tr>\n                <td>\n                  <textarea name=\"rmq[" . ++$k . "]\" rows=\"4\" cols=\"60\"></textarea>\n                <td>\n              </tr>\n            </table>\n            <p>\n              &nbsp;\n            </p>\n";
    };
    foreach($liste_oligo[$i][7] as $key){
     $msg .= "            <table width=\"200\" summary=\"\">\n              <tr>\n                <td align=\"left\">\n                  &nbsp;" . _("Association") . ' ' . $liste_oligo[$i][1] . ' / ' . $key . '&nbsp;-&nbsp;(' . ("description") . ")&nbsp;\n                </td>\n              </tr>\n              <tr>\n                <td>\n                  <textarea name=\"rmq[" . ++$k . "]\" rows=\"4\" cols=\"60\"></textarea>\n                <td>\n              </tr>\n            </table>\n            <p>\n              &nbsp;\n            </p>\n";
     for($j=$i;$j<count($liste_oligo);$j++) {
      if ($liste_oligo[$j][1]==$key) {
       break;
      };
     };
     unset($liste_oligo[$j][7][array_search($liste_oligo[$i][1],$liste_oligo[$j][7])]);
    };
   };
   $msg .= "              <div>\n                <input type=\"hidden\" name=\"new\" value=\"4\"><input type=\"submit\" value=\"" . _("Finish") . "\">\n              </div>\n            </form>\n";
   if($k>0) {
    print $msg;
    } else {
    for($i=0;$i<count($liste_oligo);$i++) {
     $ID_string=$liste_oligo[$i][1];
     do {
      $ID=oligoID($ID_string);
      if (($ID<1000000)&&($ID!=0)) {
       $resultID=sql_query('SELECT barcode FROM oligo WHERE barcode=' . $ID . ';',$dbconn);
      };
      $ID_string=($liste_oligo[$i][1].dechex(time()));
     } while (sql_num_rows($resultID)!=0);
     $result=sql_query("INSERT INTO oligo (name, oligo, synthesis, box, rank, freezer, barcode, notes) VALUES ('" . addslashes(htmlentities($liste_oligo[$i][1])) . "','" . addslashes($liste_oligo[$i][2]) . "','" . date("Y-m-d",$liste_oligo[$i][3]) . "'," . $liste_oligo[$i][4] . ',' . $liste_oligo[$i][5] .  ',' . (($liste_oligo[$i][9]!='')?("'".addslashes(htmlentities($liste_oligo[$i][9]))."'"):'NULL') . ',' . $ID . ",'" . addslashes(htmlentities($liste_oligo[$i][6])) . "');",$dbconn);
     if(strlen($r=sql_last_error($dbconn))) {
      print "          <p>\n            <a href=\"" . $base_url . $plugin . "new_oligo.php\"><img src=\"" . $base_url . 'images/no.png" alt=""></a>&nbsp;' . _("Oligo") . ' <strong>' . $liste_oligo[$i][1] . "</strong>: Oops<br>\n            $r\n          </p>\n";
     } else {
      print "          <p>\n            <a href=\"" . $base_url . $plugin . "\"><img src=\"" . $base_url . 'images/ok.png" alt=""></a>&nbsp;' . _("Oligo") . ' <strong>' . $liste_oligo[$i][1] . '</strong>: ' . _("added") . "\n          </p>\n";
      unset($_SESSION['oligo_liste']);
     };
    };
   };
  } elseif (($_POST['new']==4) && (isset($_SESSION['oligo_liste']))) { //save with association...
   $liste_oligo=$_SESSION['oligo_liste'];
   $rmq=$_POST['rmq'];
   for($i=0;$i<count($liste_oligo);$i++) {
    $ID_string=$liste_oligo[$i][1];
    do {
     $ID=oligoID($ID_string);
     $resultID=sql_query('SELECT barcode FROM oligo WHERE barcode=' . $ID . ';',$dbconn);
     $ID_string=($liste_oligo[$i][1].dechex(time()));
    } while ((sql_num_rows($resultID)!=0)&&($ID>1000000)&&($ID==0));
    $result=sql_query("INSERT INTO oligo (name, oligo, synthesis, box, rank, freezer, barcode, notes) VALUES ('" . addslashes(htmlentities($liste_oligo[$i][1])) . "','" . addslashes($liste_oligo[$i][2]) . "','" . date("Y-m-d",$liste_oligo[$i][3]) . "'," . $liste_oligo[$i][4] . ',' . $liste_oligo[$i][5] . ',' . (($liste_oligo[$i][9]!='')?("'".addslashes(htmlentities($liste_oligo[$i][9]))."'"):'NULL') . ',' . $ID . ",'" . addslashes(htmlentities($liste_oligo[$i][6])) . "');",$dbconn);
    if(strlen($r=sql_last_error($dbconn))) {
     print "          <p>\n            <a href=\"" . $base_url . $plugin . "new_oligo.php\"><img src=\"" . $base_url . 'images/no.png" alt=""></a>&nbsp;' . _("Oligo") . ' <strong>' . $liste_oligo[$i][1] . "</strong>: Oops<br>\n            $r\n          </p>\n";
     $stop=1;
    } else {
     $k=0;		
     print "          <p>\n            <a href=\"" . $base_url . $plugin . "\"><img src=\"" . $base_url . 'images/ok.png" alt=""></a>&nbsp;' . _("Oligo") . ' <strong>' . $liste_oligo[$i][1] . '</strong>: ' . _("added") . "\n          </p>\n";
     unset($_SESSION['oligo_liste']);
     unset($stop);
     foreach($liste_oligo[$i][8] as $key) {
      if(isset($rmq[++$k])) {
       $result=sql_query("INSERT INTO couple (name1, name2, notes) VALUES ('" . addslashes(htmlentities($liste_oligo[$i][1])) . "','" . addslashes(htmlentities($key)) . "','" . addslashes(htmlentities(trim(stripslashes($rmq[$k])))) . "');",$dbconn);
       if(strlen ($r=sql_last_error($dbconn))) {
        print "          <p>\n            <a href=\"" . $base_url . $plugin . "new_oligo.php\"><img src=\"" . $base_url . 'images/no.png" alt=""></a>&nbsp;' . _("Association") . ' <strong>' . $liste_oligo[$i][1] . " / $key</strong>: Oops<br>\n            $r\n          </p>\n";        
       } else {
        print "          <p>\n            <a href=\"" . $base_url . $plugin . "\"><img src=\"" . $base_url . 'images/ok.png" alt=""></a>&nbsp;' . _("Association") . ' <strong>' . $liste_oligo[$i][1] . " / $key</strong>: " . _("added") . "\n          </p>\n";        
       };
      };
     };
    };
    foreach($liste_oligo[$i][7] as $key){
     if(isset($rmq[++$k]) && !(isset($stop))) {
      $result=sql_query("INSERT INTO couple (name1, name2, notes) VALUES ('" . addslashes(htmlentities($liste_oligo[$i][1])) . "','" . addslashes(htmlentities($key)) . "','" . addslashes(htmlentities(trim(stripslashes($rmq[$k])))) . "');",$dbconn);
      if(strlen ($r=sql_last_error($dbconn))) {
       print "          <p>\n            <a href=\"" . $base_url . $plugin . "new_oligo.php\"><img src=\"" . $base_url . 'images/no.png" alt=""></a>&nbsp;' . _("Association") . ' <strong>' . $liste_oligo[$i][1] . " / $key</strong>: Oops<br>\n            $r\n          </p>\n";       
      } else {
       print "          <p>\n            <a href=\"" . $base_url . $plugin . "\"><img src=\"" . $base_url . 'images/ok.png" alt=""></a>&nbsp;' . _("Association") . ' <strong>' . $liste_oligo[$i][1] . " / $key</strong>: " . _("added") . "\n          </p>\n";
      };
     };
     for($j=$i;$j<count($liste_oligo);$j++) {
      if ($liste_oligo[$j][1]==$key) {
       break;
      };
     };
     unset($liste_oligo[$j][7][array_search($liste_oligo[$i][1],$liste_oligo[$j][7])]);
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

