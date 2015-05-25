<?php
 ob_start("ob_gzhandler");
 session_start();
 include_once '../includes/login.inc';
 if (!isset($_SESSION['status'])) {
  header('Location: ' . $base_url);
  exit;
 };
 $lev=error_reporting(8);
 function plasmidID($string) { #alias crc20 ?!!
  $crc = 0xFFFFF;
  for ($x = 0; $x < strlen($string); $x++) {
   $crc = $crc ^ ord($string[$x]);
   for ($y = 0; $y < 8; $y++) {
    if (($crc & 0x00001) == 0x00001) {
     $crc = (($crc >> 1) ^ 0xA0001);
    } else {
     $crc = $crc >> 1;
    };
   };
  };
  return $crc;
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
 $lang=((isset($_COOKIE['lang']))?substr($_COOKIE['lang'],0,2):'en');
 $status=$_SESSION['status'];
 header_start();
 $dbconn=sql_connect($plugin_db['plasmiddb']);
 if (!isset($_POST['add'])) {
  unset($_SESSION['plasmid_proto']);
  unset($_SESSION['plasmid_local']);
  unset($_SESSION['plasmid_alias']);
  unset($_SESSION['plasmid_seq']);
  unset($_SESSION['plasmid_map']);
 };
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.01//EN">
<html>
  <head>
    <title>
      ..:: phpLabDB::PlasmidDB ::..
    </title>
    <meta http-equiv="Content-Type" content="text/html">
    <link rel="stylesheet" type="text/css" media="print" href="<?php print $base_url; ?>css/print.css">
    <link rel="stylesheet" type="text/css" media="screen" href="<?php print $base_url; ?>css/screen.css">
    <style type="text/css">
.plasmid { background: url('images/plasmid.png') no-repeat right top; }
    </style>
<?php if (!(isset($_POST['add']))) { ?>
    <script type="text/javascript">
    //<![CDATA[
function chkFormulaire() {
 if(document.forms(0).name.value == "") {
  alert("<?php textdomain('plasmiddb'); print _("There is no name!"); textdomain('phplabdb'); ?>");
  return false;
 };
};
    //]]>
    </script>
<?php }; ?>  </head>
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
  if ($key=='plasmiddb') {
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
      <div id="content" class="plasmid">
        <div id="page-main">
<?php  textdomain('plasmiddb'); ?>
          <h1>
            PlasmidDB plug-in
          </h1>
          <h3>
            <?php print _("New plasmid"); ?> 
          </h3>
<?php
 if (isset($_POST['next'])) {
  if ($_POST['add']==1) {
    unset($_SESSION['plasmid_proto']);
    unset($_SESSION['plasmid_local']);
    unset($_SESSION['plasmid_alias']);
// Vérification des données du Prototype...
   if ((trim(stripslashes($_POST['name']))!='') && (trim(stripslashes($_POST['circ']))!='') && (trim(stripslashes($_POST['dna']))!='') && (trim(stripslashes($_POST['local']))!='')) {
// Le prototype existe-t-il deja ?
    $lev=error_reporting(8);
    $result=sql_query("SELECT name,length,circ,dna,pmid,created,ascendant,plasmid,selection,replication,notes FROM prototype WHERE name='" . addslashes(htmlentities(trim(stripslashes($_POST['name'])))) . "';",$dbconn);
    error_reporting($lev);
    if(!(strlen($r=sql_last_error($dbconn)))) {
     if (sql_num_rows($result)==0) {
      $_SESSION['plasmide_proto']['name']=trim(stripslashes($_POST['name']));
      $_SESSION['plasmide_proto']['length']=intval(trim(stripslashes($_POST['length'])));
      $_SESSION['plasmide_proto']['circ']=trim(stripslashes($_POST['circ']));
      $_SESSION['plasmide_proto']['dna']=trim(stripslashes($_POST['dna']));
      $_SESSION['plasmide_proto']['pmid']=trim(stripslashes($_POST['pmid']));
      $_SESSION['plasmide_proto']['created']=mktime (0,0,0,trim(stripslashes($_POST['date_M'])),trim(stripslashes($_POST['date_D'])),trim(stripslashes($_POST['date_Y'])));
      $_SESSION['plasmide_proto']['ascendant']=(($_POST['ascendant']=='other')?trim(stripslashes($_POST['ascdID'])):trim(stripslashes($_POST['ascendant'])));
      $_SESSION['plasmide_proto']['local']=trim(stripslashes($_POST['local']));
      $_SESSION['plasmide_proto']['processed']='f';
     } else {
      $row = sql_fetch_row($result);
      $_SESSION['plasmide_proto']['name']=$row[0];
      $_SESSION['plasmide_proto']['length']=intval($row[1]);
      $_SESSION['plasmide_proto']['circ']=$row[2];
      $_SESSION['plasmide_proto']['dna']=$row[3];
      $_SESSION['plasmide_proto']['pmid']=$row[4];
      $_SESSION['plasmide_proto']['created']=strtotime($row[5]);
      $_SESSION['plasmide_proto']['ascendant']=$row[6];
      $_SESSION['plasmide_proto']['selection']=$row[8];
      $_SESSION['plasmide_proto']['replication']=$row[9];
      $_SESSION['plasmide_proto']['description']=$row[10];
      $_SESSION['plasmide_proto']['processed']='t';
      if (trim(stripslashes($_POST['local']))=='t') {
       $_SESSION['plasmide_proto']['local']='t';
       print "          <p>\n            <img src=\"" . $base_url . $plugin . 'images/notice.png" alt="">&nbsp;<strong>' . _("Prototype") . ' ' . trim(stripslashes($_POST['name'])) . ' ' . _("already exists and will be not modified!") . "</strong>\n          </p>\n";
      } else {
       unset($_SESSION['plasmid_proto']);
       unset($_SESSION['plasmid_local']);
       unset($_SESSION['plasmid_alias']);
      };
     };
     if (isset($_SESSION['plasmide_proto']['local'])) {
?>
          <p>
            <?php print _("Inform all following information:"); ?><br>
            <sup>*</sup> <small><?php print _("needed information"); ?></small>
          </p>
<?php
    $lev=error_reporting(8);
    if ($sqlserver=='postgresql') {
     $result=sql_query('SELECT max(box), max(rank) FROM plasmid WHERE box=(SELECT max(box) FROM plasmid);',$dbconn);
    } elseif ($sqlserver=='mysql') {
     $result=sql_query('SELECT max(box),NULL FROM plasmid;',$dbconn);
     if (sql_num_rows($result)==1) {
      $row=sql_fetch_row($result);
      $result=sql_query("SELECT $row[0], max(rank) FROM plasmid WHERE box=$row[0];",$dbconn);
     };
    } else {
     print "          <p>\n            <a href=\"" . $base_url . $plugin . "new_plasmid.php\"><img src=\"" . $base_url . "images/oops.png\" alt=\"\"></a>&nbsp;<strong>Oops</strong>\n          </p>\n          <p>\n            SQL server type '$sqlserver' unknown on this server\n          </p>\n";
    };
    error_reporting($lev);
    if((!(strlen ($r=sql_last_error($dbconn)))) && (sql_num_rows($result)==1)) {
     $row=sql_fetch_row($result);
     print "          <p>\n            " . _("Last place occupied: box") . " <strong>$row[0]</strong>, " . _("rank") . " <strong>$row[1]</strong>\n          </p>\n";
    };
?>
          <form action="<?php print $base_url . $plugin; ?>new_plasmid.php" method="post">
            <table summary="">
              <tr>
                <td>
                  &nbsp;<?php print _("Name"); ?>&nbsp;
                </td>
                <td>
                  <strong><?php print $_SESSION['plasmide_proto']['name']; ?></strong>
                </td>
              </tr>
<?php if ($_SESSION['plasmide_proto']['processed']=='f') { ?>
              <tr>
                <td>
                  <label for="alias">&nbsp;<?php print _("Alias"); ?>&nbsp;</label>
                </td>
                <td>
                  <input type="text" size="20" maxlength="50" name="alias" id="alias" title="<?php print _("plasmid/vector alias or short name"); ?>">
                </td>
              </tr>
              <tr>
                <td>
                  &nbsp;<?php print _("Sequence"); ?>&nbsp;
                </td>
                <td>
                  <a href="#" onClick="window.open('addseq.php', '<?php print _("Sequence"); ?>', 'toolbar=no, location=no, directories=no, status=no, scrollbars=no, resizable=yes, copyhistory=no, width=600, height=400, left=300, top=50')"><img src="images/wizard.png" alt="<?php print _("Sequence"); ?>"></a>
                </td>
              </tr>
              <tr>
                <td>
                  &nbsp;<?php print _("Map"); ?>&nbsp;
                </td>
                <td>
                  <a href="#" onClick="window.open('addmap.php', '<?php print _("Map"); ?>', 'toolbar=no, location=no, directories=no, status=no, scrollbars=yes, resizable=yes, copyhistory=no, width=600, height=500, left=150, top=50')"><img src="images/wizard.png" alt="<?php print _("Map"); ?>"></a>
                </td>
              </tr>
<?php }; if ($_SESSION['plasmide_proto']['local']=='t') { ?>
              <tr>
                <td>
                  <label for="box">&nbsp;<?php print _("Box"); ?>*&nbsp;</label>
                </td>
                <td>
                  <select name="box" id="box" title="<?php print _("Box"); ?>"><option></option><?php for ($i=1 ; $i<101; $i++) {print "<option value=\"$i\">$i</option>"; }; ?></select>
                </td>
              </tr>
              <tr>
                <td>
                  <label for="rank">&nbsp;<?php print _("Rank"); ?>*&nbsp;</label>
                </td>
                <td>
                  <select name="rank" id="rank" title="<?php print _("place on the box"); ?>"><option></option><?php for ($i=1 ; $i<101; $i++) {print "<option value=\"$i\">$i</option>"; }; ?></select>
                </td>
              </tr>
              <tr>
                <td>
                  <label for="conditioning">&nbsp;<?php print _("Conditioning"); ?>*&nbsp;</label>
                </td>
                <td>
                  <select name="conditioning" id="conditioning" title="<?php print _("Conditioning"); ?>" onchange="if (this.value=='other') {document.forms[0].condiID.style.display='';} else {document.forms[0].condiID.style.display='none';}"><option></option><?php
      $result=sql_query("SELECT legend FROM conditioning WHERE lang='$lang';",$dbconn);
      if(!(strlen($r=sql_last_error($dbconn))) && (sql_num_rows($result)!=0)) {
       while($row=sql_fetch_row($result)) {
        print "<option value=\"$row[0]\">$row[0]</option>";
       };
      };
?><option value="other"><?php print _("other"); ?></option></select>&nbsp;&nbsp;<input type="text" name="condiID" size="15" id="condiID" style="display:none;">
                </td>
              </tr>
              <tr>
                <td>
                  <label for="preparation">&nbsp;<?php print _("Preparation"); ?>*&nbsp;</label>
                </td>
                <td>
                <select name="preparation" id="preparation" title="<?php print _("Preparation"); ?>" onchange="if (this.value=='other') {document.forms[0].prepID.style.display='';} else {document.forms[0].prepID.style.display='none';}"><option></option><?php
     $result=sql_query("SELECT legend FROM preparation WHERE lang='$lang';",$dbconn);
     if(!(strlen($r=sql_last_error($dbconn))) && (sql_num_rows($result)!=0)) {
      while($row=sql_fetch_row($result)) {
       print "<option value=\"$row[0]\">$row[0]</option>";
      };
     };
?><option value="other"><?php print _("other"); ?></option></select>&nbsp;&nbsp;<input type="text" name="prepID" size="15" tabindex="11" id="prepID" style="display:none;"> &nbsp; - &nbsp; <input type="text" name="conc" title="<?php print _("Product concentration"); ?>" size="5" />&nbsp;&micro;g/&micro;L
                </td>
              </tr>
<?php }; if ($_SESSION['plasmide_proto']['processed']=='f') { ?>
              <tr>
                <td>
                  &nbsp;<?php print _("Selection and Replication"); ?>*&nbsp;
                </td>
                <td>
                  <table summary="">
<?php
     $lev=error_reporting(8);
     $result=sql_query("SELECT a.organism, a.legend, b.legend FROM selection a, replication b WHERE a.lang=b.lang AND a.lang='$lang' AND a.organism=b.organism;",$dbconn);
     error_reporting($lev);
     if(!(strlen($r=sql_last_error($dbconn)))) {
      $i=0;
      while($row=sql_fetch_row($result)) {
       $selection='';
       $replication='';
       foreach((explode('|',$row[1])) as $select) {
        $selection .=  "<option value=\"$select\">$select</option>";
       };
       foreach((explode('|',$row[2])) as $replic) {
        $replication .=  "<option value=\"$replic\">$replic</option>";
       };
?> 
                    <tr>
                      <td>
                        <input type="checkbox" name="navette[<?php print $i; ?>]" title="<?php print $organisme[0]; ?>" onclick="if (this.checked) {document.forms[0].organismeID<?php print $i; ?>.style.display='';document.forms[0].replication<?php print $i; ?>.style.display='';document.forms[0].organisme_other<?php print $i; ?>.style.display=''} else {document.forms[0].organismeID<?php print $i; ?>.style.display='none'; document.forms[0].organisme_other<?php print $i; ?>.style.display='none'; document.forms[0].replication<?php print $i; ?>.style.display='none'; document.forms[0].repli<?php print $i; ?>.style.display='none';document.forms[0].organisme_other<?php print $i; ?>.style.display='none'};"><em><?php print $row[0]?></em>&nbsp;<input type="hidden" value="<?php print $row[0]; ?>" name="organisme[<?php print $i; ?>]" id="organisme<?php print $i; ?>">
                      </td>
                      <td>
                        <select multiple="multiple" title="<?php print _("Selection"); ?>" name="organismeID[<?php print $i; ?>][]" id="organismeID<?php print $i; ?>" size="2" style="display:none;"><?php print $selection; ?></select>
                      </td>
                      <td>
                        <input type="text" size="20" name="organisme_other[<?php print $i; ?>]" id="organisme_other<?php print $i; ?>" style="display:none;">
                      </td>
                      <td>
                        <select title="<?php print _("Replication"); ?>" name="replication[<?php print $i; ?>]" id="replication<?php print $i; ?>" style="display:none" onchange="if(this.value=='other'){ document.forms[0].repli<?php print $i; ?>.style.display=''; } else { document.forms[0].repli<?php print $i; ?>.style.display='none'; }"><option></option><?php print $replication; ?><option value="other" ><?php print _("other"); ?></option></select>
                      </td>
                      <td>
                        <input type="text" size="20" name="repli[<?php print $i; ?>]" id="repli<?php print $i; ?>" style="display:none;">
                      </td>
                    </tr>
<?php
       $i++;
      };
     };
?>
                  </table>
                </td>
              </tr>
<?php }; ?>
              <tr>
                <td>
                  <label for="note">&nbsp;<?php print _("Notes"); ?>&nbsp;</label>
                </td>
                <td>
                  <textarea name="notes" id="note" rows="4" cols="40"></textarea>
                </td>
              </tr>
              <tr>
                <td colspan="2">
                  <?php print '<input type="hidden" name="add" value="2"><input name="clear" type="reset" value="' . _("Clear") . '"> &nbsp; ' . _("or") . ' &nbsp; <input type="submit" name="next" value="' . _("Next") . ' &gt;&gt;">'; ?> 
                </td>
              </tr>
            </table>
          </form>
<?php 
     } else {
      print "          <p>\n            <a href=\"" . $base_url . $plugin . "new_plasmid.php\"><img src=\"" . $base_url .  'images/no.png" alt=""></a>&nbsp;<strong>' . _("The prototype") . ' ' . trim(stripslashes($_POST['name'])) . ' ' . _("already exists and will be not modified!") . "</strong>\n          </p>\n";
     };
    } else {
     print "          <p>\n            <a href=\"" . $base_url . $plugin . "new_plasmid.php\"><img src=\"" . $base_url .  'images/no.png" alt=""></a>&nbsp;<strong>' . _("Invalid data") . "</strong>\n          </p>\n";
    };
   } else {
    print "          <p>\n            <a href=\"" . $base_url . $plugin . "new_plasmid.php\"><img src=\"" . $base_url .  'images/no.png" alt=""></a>&nbsp;<strong>' . _("Invalid data") . "</strong>\n          </p>\n";
   };
  } elseif ($_POST['add']==2) {
   $plasmide_local_replication=array();
   $plasmide_local_selection=array();
   $_SESSION['plasmide_proto']['description']=(($_SESSION['plasmide_proto']['processed']=='t')?$_SESSION['plasmide_proto']['description']:trim(stripslashes($_POST['notes'])));
   if(trim(stripslashes($_POST['alias']))!='') {
    $tmp=array_unique(explode(' ',trim(stripslashes($_POST['alias']))));
    $result=sql_query("SELECT alias FROM alias WHERE (alias='" . implode("' OR alias='",$tmp)."');",$dbconn);
    if(sql_num_rows($result)==0) {
     $result=sql_query("SELECT name FROM prototype WHERE (name='" . implode("' OR name='",$tmp)."');",$dbconn);
     if(sql_num_rows($result)==0) {
      $_SESSION['plasmide_alias']=$tmp;
     } else {
      $row = sql_fetch_row($result);
      print "          <p>\n            <a href=\"" . $base_url . $plugin . "new_plasmid.php\"><img src=\"" . $base_url .  'images/no.png" alt=""></a>&nbsp;<strong>' . _("The alias") . " '$row[0]' " . _("is also a name!") . "</strong>\n          </p>\n";
      $out=1;     
     };
    } else {
     $row = sql_fetch_row($result);
     print "          <p>\n            <a href=\"" . $base_url . $plugin . "new_plasmid.php\"><img src=\"" . $base_url .  'images/no.png" alt=""></a>&nbsp;<strong>' . _("The alias") . " '$row[0]' " . _("is already used!") . "</strong>\n          </p>\n";
     $out=1;     
    };
   };   
// Analyse de la coherences des arguments Selection/Replication
   if (isset($_POST['navette'])) {
    foreach($_POST['navette'] as $navette_key => $navette_value) {
     $plasmide_local_replication[$navette_key]=trim(stripslashes($_POST['organisme'][$navette_key])) . '|';
     $plasmide_local_selection[$navette_key]=trim(stripslashes($_POST['organisme'][$navette_key])) . '|';
     if (isset($_POST['organismeID'][$navette_key])) {
      $plasmide_local_selection[$navette_key].=implode('#',$_POST['organismeID'][$navette_key]);
     };
     if (!(empty($_POST['organisme_other'][$navette_key]))) {
      $plasmide_local_selection[$navette_key].=((isset($_POST['organismeID'][$navette_key]))?'#':'') . trim(stripslashes($_POST['organisme_other'][$navette_key]));
     };
     if (!(empty($_POST['repli'][$navette_key])) && ($_POST['replication'][$navette_key]=='other')) {
      $plasmide_local_replication[$navette_key].=trim(stripslashes($_POST['repli'][$navette_key]));
     } elseif (!(empty($_POST['replication'][$navette_key]))) {
      $plasmide_local_replication[$navette_key].=trim(stripslashes($_POST['replication'][$navette_key]));
     };
    };
   };
   $_SESSION['plasmide_proto']['replication']=(($_SESSION['plasmide_proto']['processed']=='t')?$_SESSION['plasmide_proto']['replication']:implode('*',$plasmide_local_replication));
   $_SESSION['plasmide_proto']['selection']=(($_SESSION['plasmide_proto']['processed']=='t')?$_SESSION['plasmide_proto']['selection']:implode('*',$plasmide_local_selection));
   if ($_SESSION['plasmide_proto']['local']=='t') {
// Vérification des données du Local...
    if ((intval(trim(stripslashes($_POST['box'])))>0) && (intval(trim(stripslashes($_POST['rank'])))>0)) {
     $_SESSION['plasmide_local']['box']=intval(trim(stripslashes($_POST['box'])));
     $_SESSION['plasmide_local']['rank']=intval(trim(stripslashes($_POST['rank'])));
     $_SESSION['plasmide_local']['conditioning']=(($_POST['conditioning']=='other')?trim(stripslashes($_POST['condiID'])):trim(stripslashes($_POST['conditioning'])));
     $_SESSION['plasmide_local']['preparation']=(($_POST['preparation']=='other')?trim(stripslashes($_POST['prepID'])):trim(stripslashes($_POST['preparation'])));
     $_SESSION['plasmide_local']['conc']=doubleval(str_replace(',','.',trim(stripslashes($_POST['conc']))));
// Reste-t-il de la place ?
     $result=sql_query('SELECT name FROM plasmid WHERE box=' . $_SESSION['plasmide_local']['box'] . ' AND rank=' . $_SESSION['plasmide_local']['rank'] . ';',$dbconn);
     if(!(strlen($r=sql_last_error($dbconn)))) {
      if (sql_num_rows($result)==1) {
       $row = sql_fetch_row($result);
       print "          <p>\n            <a href=\"" . $base_url . $plugin . "new_plasmid.php\"><img src=\"" . $base_url . 'images/no.png" alt=""></a>&nbsp;<strong>' . _("The place") . ' ' . $_SESSION['plasmide_local']['box'] . ' (' . $_SESSION['plasmide_local']['rank'] . ') ' . _("is already used by") . " '$row[0]'.</strong>\n          </p>\n";
       $out=1;     
      } elseif ($_SESSION['plasmide_local']['conditioning']!='') {
       $ID_string=$_SESSION['plasmide_proto']['name'];
       do {
        $ID=plasmidID($ID_string);
        $resultID=sql_query('SELECT barcode FROM plasmid WHERE barcode=' . $ID . ';',$dbconn);
        $ID_string=($_SESSION['plasmide_proto']['name'].dechex(time()));
       } while ((sql_num_rows($resultID)!=0)&&($ID>1000000)&&($ID==0));
       $_SESSION['plasmide_local']['barcode']=intval($ID);
      } else { 
       print "          <p>\n            <a href=\"" . $base_url . $plugin . "new_plasmid.php\"><img src=\"" . $base_url . 'images/no.png" alt=""></a>&nbsp;<strong>' . _("Conditioning must be specified!") . "</strong>\n          </p>\n";
       $out=1;     
      };
     } else {
      print "          <p>\n            <a href=\"" . $base_url . $plugin . "new_plasmid.php\"><img src=\"" . $base_url . 'images/no.png" alt=""></a>&nbsp;<strong>' . _("Unable to open database!") . "</strong>\n          </p>\n";
      $out=1;     
     };
    } else {
     print "          <p>\n            <a href=\"" . $base_url . $plugin . "new_plasmid.php\"><img src=\"" . $base_url . 'images/no.png" alt=""></a>&nbsp;<strong>' . _("A box and rank must be provided!") . "</strong>\n          </p>\n";
     $out=1;
    };
   };
   if (($_SESSION['plasmide_proto']['replication']=='') || ($_SESSION['plasmide_proto']['selection']=='')) {
    print "          <p>\n            <a href=\"" . $base_url . $plugin . "new_plasmid.php\"><img src=\"" . $base_url . 'images/no.png" alt=""></a>&nbsp;<strong>' . _("An organism must be selected!") . "</strong>\n          </p>\n";
    $out=1;
   };
   if (!isset($out)) {
?>
          <form action="<?php print $base_url . $plugin; ?>new_plasmid.php" method="post">
            <table summary="">
              <tr>
                <td>
                  <?php print _("Name"); ?> 
                </td>
                <td>
                  <strong><?php print $_SESSION['plasmide_proto']['name']; ?></strong>
                </td>
              </tr>
<?php  if (isset($_SESSION['plasmide_alias'])) { ?>
              <tr>
                <td>
                  <?php print _("Alias"); ?> 
                </td>
                <td>
                  <strong><?php print implode('; ', $_SESSION['plasmide_alias']); ?></strong>
                </td>
              </tr>
<?php  }; if ($_SESSION['plasmide_proto_length']!=0) { ?>
              <tr>
                <td>
                  <?php print _("Length"); ?> 
                </td>
                <td>
                  <strong><?php print $_SESSION['plasmide_proto']['length']; ?></strong>
                </td>
              </tr>
<?php  }; ?>
              <tr>
                <td>
                  <?php print _("Type"); ?> 
                </td>
                <td>
                  <strong><?php print (($_SESSION['plasmide_proto']['circ']=='t')? _("Circular"): _("Linear")); ?></strong>
                </td>
              </tr>
              <tr>
                <td>
                  <?php print _("Nucleic Acid"); ?> 
                </td>
                <td>
                  <strong><?php print (($_SESSION['plasmide_proto']['dna']=='d')? _("DNA"): _("RNA")); ?></strong>
                </td>
              </tr>
              <tr>
                <td>
                  <?php print _("Creation date"); ?> 
                </td>
                <td>
                  <strong><?php print date( _("m/d/Y"),$_SESSION['plasmide_proto']['created']); ?></strong>
                </td>
              </tr>
<?php  if ($_SESSION['plasmide_proto']['pmid']!='') { ?>
              <tr>
                <td>
                  PMID/GenBank 
                </td>
                <td>
                  <strong><?php print $_SESSION['plasmide_proto']['pmid']; ?></strong>
                </td>
              </tr>
<?php  }; if ($_SESSION['plasmide_proto']['ascendant']!='') { ?>
              <tr>
                <td>
                  <?php print _("Ascendant"); ?> 
                </td>
                <td>
                  <strong><?php print $_SESSION['plasmide_proto']['ascendant']; ?></strong>
                </td>
              </tr>
<?php  }; if ($_SESSION['plasmide_proto']['description']!='') { ?>
              <tr>
                <td>
                  <?php print _("Description"); ?> 
                </td>
                <td>
                  <img src="images/yes.png" alt="">
                </td>
              </tr>
<?php  }; if ($_SESSION['plasmide_proto']['local']=='t') { ?>
              <tr>
                <td>
                  <?php print _("Box and rank"); ?> 
                </td>
                <td>
                  <strong><?php print $_SESSION['plasmide_local']['box'] . ' (' . $_SESSION['plasmide_local']['rank'] . ')'; ?></strong>
                </td>
              </tr>
<?php  }; if ($_SESSION['plasmide_local']['conditioning']!='') { ?>
              <tr>
                <td>
                  <?php print _("Conditioning"); ?> 
                </td>
                <td>
                  <strong><?php print $_SESSION['plasmide_local']['conditioning']; ?></strong>
                </td>
              </tr>
<?php  }; if ($_SESSION['plasmide_local']['preparation']!='') { ?>
              <tr>
                <td>
                  <?php print _("Preparation"); ?> 
                </td>
                <td>
                  <strong><?php print $_SESSION['plasmide_local']['preparation'] . (($_SESSION['plasmide_local']['conc']>0)?(' (' . $_SESSION['plasmide_local']['conc'] . ' &micro;g/&micro;L)'):''); ?></strong>
                </td>
              </tr>
<?php }; ?>
              <tr>
                <td>
                  <?php print _("Replication"); ?> 
                </td>
                <td>
                  <strong><?php print implode('<br />',explode('*',implode(', ',explode('#',implode(': ',explode('|',$_SESSION['plasmide_proto']['replication'])))))); ?></strong>
                </td>
              </tr>
              <tr>
                <td>
                  <?php print _("Selection"); ?> 
                </td>
                <td>
                  <strong><?php print implode('<br />',explode('*',implode(', ',explode('#',implode(': ',explode('|',$_SESSION['plasmide_proto']['selection'])))))); ?></strong>
                </td>
              </tr>
<?php if ($_SESSION['plasmide_proto']['processed']=='f') { ?>
              <tr>
                <td>
                  <?php print _("Sequence"); ?> 
                </td>
                <td>
                  <img src="images/<?php print ((isset($_SESSION['plasmide_seq']))?'yes':'no'); ?>.png" alt="">
                </td>
              </tr>
              <tr>
                <td>
                  <?php print _("Map"); ?> 
                </td>
                <td>
                  <img src="images/<?php print ((isset($_SESSION['plasmide_map']))?'yes':'no'); ?>.png" alt="">
                </td>
              </tr>
<?php  }; if ($_SESSION['plasmide_proto']['local']=='t') { ?>
              <tr>
                <td colspan="2">
                  <img src="<?php printf("%simages/barcode.php?code=%06d",$base_url,$_SESSION['plasmide_local']['barcode']); ?>&amp;style=198&amp;type=I25&amp;width=125&amp;height=50&amp;xres=2&amp;font=3" alt="">
                </td>
              </tr>
<?php }; ?>            
              <tr>
                <td colspan="2">
                  <?php print '<input type="hidden" name="add" value="3"><input type="submit" name="next" value="' . _("Add") . ' &gt;&gt;">'; ?> 
                </td>
              </tr>
            </table>
          </form>
<?php
   };
  } elseif ($_POST['add']==3) {
   if ($_SESSION['plasmide_proto']['processed']=='f') {
    $result=sql_query("INSERT INTO prototype (name, length, circ, ascendant, dna, pmid, plasmid, created, selection, replication, notes) VALUES ('" . addslashes($_SESSION['plasmide_proto']['name']) . "'," . (($_SESSION['plasmide_proto']['length']>0)?$_SESSION['plasmide_proto']['length']:'NULL') . ",'" . $_SESSION['plasmide_proto']['circ'] . "'," . (($_SESSION['plasmide_proto']['ascendant']!='')?("'".addslashes($_SESSION['plasmide_proto']['ascendant'])."'"):'NULL') . ",'" . $_SESSION['plasmide_proto']['dna'] . "'," . (($_SESSION['plasmide_proto']['pmid']!='')?("'".addslashes($_SESSION['plasmide_proto']['pmid'])."'"):'NULL') . ",'" . $_SESSION['plasmide_proto']['local'] . "','" . date("Y-m-d",$_SESSION['plasmide_proto']['created']) . "'," . (($_SESSION['plasmide_proto']['selection']!='')?("'".addslashes($_SESSION['plasmide_proto']['selection'])."'"):'NULL') . "," . (($_SESSION['plasmide_proto']['replication']!='')?("'".addslashes($_SESSION['plasmide_proto']['replication'])."'"):'NULL') . "," . (($_SESSION['plasmide_proto']['description']!='')?("'".addslashes($_SESSION['plasmide_proto']['description'])."'"):'NULL') . ");",$dbconn);
    if(!(strlen($r=sql_last_error($dbconn)))) {    
     if (isset($_SESSION['plasmide_alias'])) { 
      foreach($_SESSION['plasmide_alias'] as $alias) {
       if ($alias!=$_SESSION['plasmide_proto']['name']) {
        $result=sql_query("INSERT INTO alias (name, alias) VALUES ('" . addslashes($_SESSION['plasmide_proto']['name']) . "','" . addslashes($alias) . "');",$dbconn);
        if(strlen($r=sql_last_error($dbconn))) {
         print "          <p>\n            <a href=\"" . $base_url . $plugin . "new_plasmid.php\"><img src=\"" . $base_url . "images/oops.png\" alt=\"\"></a>&nbsp;<strong>Oops</strong><br>\n            $r\n          </p>\n";
         $in=1;
        };
       };
      };
     };
     if (isset($_SESSION['plasmide_seq'])) {
      $result=sql_query("INSERT INTO seq (name, seq) VALUES ('" . addslashes($_SESSION['plasmide_proto']['name']) . "','" . $_SESSION['plasmide_seq'] . "');",$dbconn);
      if(strlen($r=sql_last_error($dbconn))) {
       print "          <p>\n            <a href=\"" . $base_url . $plugin . "new_plasmid.php\"><img src=\"" . $base_url . "images/oops.png\" alt=\"\"></a>&nbsp;<strong>Oops</strong><br>\n            $r\n          </p>\n";
       $in=1;
      };
     };
     if (isset($_SESSION['plasmide_map'])) {
      $result=sql_query("INSERT INTO map (name, markers, enzymes) VALUES ('" . addslashes($_SESSION['plasmide_proto']['name']) . "'," . (($_SESSION['plasmide_map']['markers']!='')?("'".addslashes($_SESSION['plasmide_map']['markers'])."'"):'NULL') . "," . (($_SESSION['plasmide_map']['enzymes']!='')?("'".addslashes($_SESSION['plasmide_map']['enzymes'])."'"):'NULL') . ");",$dbconn);
      if(strlen($r=sql_last_error($dbconn))) {
       print "          <p>\n            <a href=\"" . $base_url . $plugin . "new_plasmid.php\"><img src=\"" . $base_url . "images/oops.png\" alt=\"\"></a>&nbsp;<strong>Oops</strong><br>\n            $r\n          </p>\n";
       $in=1;
      };
     };      
    } elseif ($_SESSION['plasmide_proto']['local']=='t') {
     $result=sql_query("UPDATE prototype SET plasmid='t' WHERE name='" . addslashes($_SESSION['plasmide_proto']['name']) . "';",$dbconn);
     if(strlen($r=sql_last_error($dbconn))) {
      print "          <p>\n            <a href=\"" . $base_url . $plugin . "new_plasmid.php\"><img src=\"" . $base_url . "images/oops.png\" alt=\"\"></a>&nbsp;<strong>Oops</strong><br>\n            $r\n          </p>\n";
      $in=1;
     };
    } else {
     print "          <p>\n            <a href=\"" . $base_url . $plugin . "new_plasmid.php\"><img src=\"" . $base_url . "images/oops.png\" alt=\"\"></a>&nbsp;<strong>Oops</strong><br>\n            $r\n          </p>\n";
     $in=1;
    };
   };
   if ($_SESSION['plasmide_proto']['local']=='t') {  
    $result=sql_query("INSERT INTO plasmid (name, barcode, released, box, rank, conditioning, preparation, conc) VALUES ('" . addslashes($_SESSION['plasmide_proto']['name']) . "'," . $_SESSION['plasmide_local']['barcode'] . ",NOW()," . $_SESSION['plasmide_local']['box'] . "," . $_SESSION['plasmide_local']['rank'] . ",'" . addslashes($_SESSION['plasmide_local']['conditioning']) . "'," . (($_SESSION['plasmide_local']['preparation']!='')?("'".addslashes($_SESSION['plasmide_local']['preparation'])."'"):'NULL') . "," . (($_SESSION['plasmide_local']['conc']>0)?$_SESSION['plasmide_local']['conc']:'NULL') . ");",$dbconn);
    if(strlen($r=sql_last_error($dbconn))) {
     print "          <p>\n            <a href=\"" . $base_url . $plugin . "new_plasmid.php\"><img src=\"" . $base_url . "images/oops.png\" alt=\"\"></a>&nbsp;<strong>Oops</strong><br>\n            $r\n          </p>\n";
     $in=1;
    };
   };
   if (!isset($in)) {
    print "          <p>\n            <a href=\"" . $base_url . $plugin . "\"><img src=\"" . $base_url . 'images/ok.png" alt=""></a>&nbsp;<strong>' . _("New plasmid added") . "</strong>\n          </p>\n";
   };
//bug ??
   unset($_SESSION['plasmid_proto']);
   unset($_SESSION['plasmid_local']);
   unset($_SESSION['plasmid_alias']);
   unset($_SESSION['plasmid_seq']);
   unset($_SESSION['plasmid_map']);
?>
          <form action="<?php print $base_url . $plugin; ?>new_plasmid.php" method="post">
            <div>
              <input type="submit" value="<?php print _("New"); ?>">
            </div>
          </form>
<?php 
  };
 } else {
?>
          <p>
            <?php print _("Inform all following information:"); ?><br>
            <sup>*</sup> <small><?php print _("needed information"); ?></small>
          </p>
          <form action="<?php print $base_url . $plugin; ?>new_plasmid.php" method="post" onsubmit="return chkFormulaire()">
            <table summary="">
              <tr>
                <td>
                  <label for="name">&nbsp;<?php print _("Name"); ?>*&nbsp;</label>
                </td>
                <td>
                  <input type="text" size="10" maxlength="100" name="name" id="name" title="<?php print _("plasmid/vector alias or short name"); ?>">
                </td>
              </tr>
              <tr>
                <td>
                  <label for="length">&nbsp;<?php print _("Length"); ?>&nbsp;</label>
                </td>
                <td>
                  <input type="text" size="10" maxlength="10" name="length" id="length" title="<?php print _("length in nucleotides"); ?>">
                </td>
              </tr>
              <tr>
                <td>
                  &nbsp;<?php print _("Type"); ?>*&nbsp;
                </td>
                <td>
                  <input type="radio" name="circ" id="circular" title="<?php print _("Circular plasmid/vector"); ?>" value="t" checked="checked">&nbsp;<label for="circular"><?php print _("circular"); ?></label>&nbsp;&nbsp;<input type="radio" name="circ" id="linear" title="<?php print _("Linear plasmid/vector"); ?>" value="f">&nbsp;<label for="linear"><?php print _("linear"); ?></label>
                </td>
              </tr>
              <tr>
                <td>
                  &nbsp;<?php print _("Nucleic Acid"); ?>*&nbsp;
                </td>
                <td>
                  <input type="radio" name="dna" id="dna" title="<?php print _("DNA"); ?>" value="d" checked="checked">&nbsp;<label for="dna"><?php print _("DNA"); ?></label>&nbsp;&nbsp;<input type="radio" name="dna" id="rna" title="<?php print _("RNA"); ?>" value="r">&nbsp;<label for="rna"><?php print _("RNA"); ?></label>
                </td>
              </tr>
              <tr>
                <td>
                  &nbsp;<?php print _("Date"); ?>*&nbsp;
                </td>
                <td>
                  <?php print newtime(); ?> 
                </td>
              </tr>
              <tr>
                <td>
                  <label for="ascendant">&nbsp;<?php print _("Ascendance"); ?>&nbsp;</label>
                </td>
                <td>
                  <select name="ascendant" id="ascendant" title="<?php print _("Parental plasmid/vector"); ?>" onclick="if (this.value=='other') {document.forms[0].ascdID.style.display='';} else {document.forms[0].ascdID.style.display='none';}"><option></option><?php 
  $result=sql_query('SELECT name FROM prototype;',$dbconn);
  while($row=sql_fetch_row($result)) {
   print "<option value=\"$row[0]\">$row[0]</option>";
  };
?><option value="other"><?php print _("other"); ?></option></select>&nbsp;&nbsp;<input type="text" name="ascdID" size="15" id="ascdID" style="display:none;">
                </td>
              </tr>
              <tr>
                <td>
                  <label for="pmid">&nbsp;PMID/GenBank&nbsp;</label>
                </td>
                <td>
                  <input type="text" size="20" maxlength="100" name="pmid" id="pmid" title="<?php print _("PubMed IDs"); ?>">
                </td>
              </tr>
              <tr>
                <td>
                  &nbsp;<?php print _("Local Storage"); ?>*&nbsp;
                </td>
                <td>
                  <input type="radio" name="local" id="local" title="<?php print _("Local Storage"); ?>" value="t" checked="checked">&nbsp;<label for="local">yes</label>&nbsp;&nbsp;<input type="radio" name="local" id="prototype" title="description only" value="f">&nbsp;<label for="prototype">no</label>
                </td>
              </tr>
              <tr>
                <td colspan="2">
                  <?php print '<input type="hidden" name="add" value="1"><input name="clear" type="reset" value="' . _("Clear") . '"> &nbsp; ' . _("or") . ' &nbsp; <input type="submit" name="next" value="' . _("Next") . ' &gt;&gt;">'; ?>                
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

