<?php
 ob_start("ob_gzhandler");
 session_start();
 include_once '../includes/login.inc';
 if (!isset($_SESSION['status'])) {
  header('Location: ' . $base_url);
  exit;
 };
 $lev=error_reporting(8);
 function StrainID($string) { #alias crc20 ?!!
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
 $status=$_SESSION['status'];
 if (!isset($_POST['add'])) {
  unset($_SESSION['strain']);
 };
 header_start();
 $dbconn=sql_connect($plugin_db['straindb']);
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.01//EN">
<html>
  <head>
    <title>
      ..:: phpLabDB::StrainDB ::..
    </title>
    <meta http-equiv="Content-Type" content="text/html">
    <link rel="stylesheet" type="text/css" media="print" href="<?php print $base_url; ?>css/print.css">
    <link rel="stylesheet" type="text/css" media="screen" href="<?php print $base_url; ?>css/screen.css">
    <style type="text/css">
.strain { background: url('images/strain.png') no-repeat right top; }
    </style>
<?php if (!(isset($_POST['add']))) { ?>
    <script type="text/javascript">
    //<![CDATA[
function chkFormulaire() {
 if(document.forms(0).name.value == "") {
  alert("<?php textdomain('straindb'); print _("There is no name!"); textdomain('phplabdb'); ?>");
  return false;
 };
};
    //]]>
    </script>
<?php }; ?>  </head>
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
  if ($key=='straindb') {
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
      <div id="content" class="strain">
        <div id="page-main">
<?php  textdomain('straindb'); ?>
          <h1>
            StrainDB plug-in
          </h1>
          <h3>
            <?php print _("New"); ?>
          </h3>
<?php if (isset($_POST['next'])) {
  if ($_POST['add']==1) {
   if ((trim(stripslashes($_POST['name']))!='') && (intval(trim(stripslashes($_POST['box'])))>0)  && (intval(trim(stripslashes($_POST['rank'])))>0) && (intval(trim(stripslashes($_POST['species'])))>0)) {
   $result=sql_query("SELECT name,box,rank FROM strain WHERE (box=" . intval(trim(stripslashes($_POST['box']))) . ' AND rank=' . intval(trim(stripslashes($_POST['rank']))) . ');',$dbconn);
   if ((!(strlen($r=sql_last_error($dbconn)))) && (sql_num_rows($result)==0)) {
    $result=sql_query("SELECT name,box,rank FROM strain WHERE name='" . addslashes(trim(stripslashes($_POST['name']))) . "';",$dbconn);
    if ((!(strlen($r=sql_last_error($dbconn)))) && (sql_num_rows($result)==0)) {
     $_SESSION['strain']['name']=trim(stripslashes($_POST['name']));
     $_SESSION['strain']['released']=mktime(0,0,0,trim(stripslashes($_POST['date_M'])),trim(stripslashes($_POST['date_D'])),trim(stripslashes($_POST['date_Y'])));
     $_SESSION['strain']['box']=intval(trim(stripslashes($_POST['box'])));
     $_SESSION['strain']['rank']=intval(trim(stripslashes($_POST['rank'])));
     $_SESSION['strain']['species']=intval(trim(stripslashes($_POST['species'])));
     $_SESSION['strain']['ascendant']=(($_POST['ascendant']=='other')?trim(stripslashes($_POST['ascdID'])):trim(stripslashes($_POST['ascendant'])));
     $_SESSION['strain']['pmid']=trim(stripslashes($_POST['pmid']));    
     $_SESSION['strain']['ploidy']=trim(stripslashes($_POST['ploidy']));
     $_SESSION['strain']['plasmid']=trim(stripslashes($_POST['plasmid']));
     $_SESSION['strain']['phenotype']=trim(stripslashes($_POST['phenotype']));
     $_SESSION['strain']['genotype']=trim(stripslashes($_POST['genotype']));
     $_SESSION['strain']['medium']=trim(stripslashes($_POST['medium']));
     $_SESSION['strain']['notes']=trim(stripslashes($_POST['notes']));
     $ID_string=$_SESSION['strain']['name'];
     do {
      $ID=strainID($ID_string);
      if (($ID<1000000)&&($ID!=0)) {
       $resultID=sql_query('SELECT barcode FROM strain WHERE barcode=' . $ID . ';',$dbconn);
      };
      $ID_string=($_SESSION['strain']['name'].dechex(time()));
     } while (sql_num_rows($resultID)!=0);
     $_SESSION['strain']['barcode']=$ID;
?>
          <form action="<?php print $base_url . $plugin; ?>new_strain.php" method="post">
            <table summary="">
              <tr>
                <td>
                  <?php print _("Name"); ?> 
                </td>
                <td>
                  <strong><?php print $_SESSION['strain']['name']; ?></strong>
                </td>
              </tr>
              <tr>
                <td>
                  <?php print _("Date"); ?> 
                </td>
                <td>
                  <strong><?php print date( _("m/d/Y"),$_SESSION['strain']['released']); ?></strong>
                </td>
              </tr>
              <tr>
                <td>
                  <?php print _("Box and rank"); ?> 
                </td>
                <td>
                  <strong><?php print $_SESSION['strain']['box'] . ' (' . $_SESSION['strain']['rank'] . ')'; ?></strong>
                </td>
              </tr>
              <tr>
                <td>
                  <?php print _("Species"); ?>&nbsp;
                </td>
                <td>
                  <strong><em><?php 
    $result=sql_query('SELECT name FROM species WHERE id=' . $_SESSION['strain']['species'] . ';',$dbconn);
    if(!(strlen($r=sql_last_error($dbconn))) && (sql_num_rows($result)==1)) {
     $row=sql_fetch_row($result);
     print $row[0];
    };
?></em></strong>
                </td>
              </tr>
<?php if ($_SESSION['strain']['ascendant']!='') { ?>
              <tr>
                <td>
                  <?php print _("Ascendant"); ?> 
                </td>
                <td>
                  <strong><?php print $_SESSION['strain']['ascendant']; ?></strong>
                </td>
              </tr>
<?php }; if ($_SESSION['strain']['phenotype']!='') { ?>
              <tr>
                <td>
                  <?php print _("Phenotype"); ?> 
                </td>
                <td>
                  <strong><?php print $_SESSION['strain']['phenotype']; ?></strong>
                </td>
              </tr>
<?php }; if ($_SESSION['strain']['genotype']!='') { ?>
              <tr>
                <td>
                  <?php print _("Genotype"); ?> 
                </td>
                <td>
                  <strong><?php print $_SESSION['strain']['genotype']; ?></strong>
                </td>
              </tr>
<?php }; if ($_SESSION['strain']['ploidy']!='') { ?>
              <tr>
                <td>
                  <?php print _("Ploidy"); ?> 
                </td>
                <td>
                  <strong><?php print $_SESSION['strain']['ploidy']; ?></strong>
                </td>
              </tr>
<?php }; if ($_SESSION['strain']['medium']!='') { ?>
              <tr>
                <td>
                  <?php print _("Medium"); ?> 
                </td>
                <td>
                  <strong><?php print $_SESSION['strain']['medium']; ?></strong>
                </td>
              </tr>
<?php }; if ($_SESSION['strain']['plasmid']!='') { ?>
              <tr>
                <td>
                  <?php print _("Plasmid"); ?> 
                </td>
                <td>
                  <strong><?php print $_SESSION['strain']['plasmid']; ?></strong>
                </td>
              </tr>
<?php  }; if ($_SESSION['strain']['pmid']!='') { ?>
              <tr>
                <td>
                  PMID/GenBank 
                </td>
                <td>
                  <strong><?php print $_SESSION['strain']['pmid']; ?></strong>
                </td>
              </tr>
<?php  }; if ($_SESSION['strain']['notes']!='') { ?>
              <tr>
                <td>
                  <?php print _("Description"); ?> 
                </td>
                <td>
                  <img src="images/yes.png" alt="">
                </td>
              </tr>
<?php  }; ?>
              <tr>
                <td colspan="2">
                  <img src="<?php printf("%simages/barcode.php?code=%06d",$base_url,$_SESSION['strain']['barcode']); ?>&amp;style=198&amp;type=I25&amp;width=125&amp;height=50&amp;xres=2&amp;font=3" alt="">
                </td>
              </tr>
              <tr>
                <td colspan="2">
                  <?php print '<input type="hidden" name="add" value="2"><input type="submit" name="next" value="' . _("Add") . ' &gt;&gt;">'; ?> 
                </td>
              </tr>
            </table>
          </form>
<?php    
     } else {
      $row = sql_fetch_row($result);   
      print "          <p>\n            <a href=\"" . $base_url . $plugin . "new_strain.php\"><img src=\"" . $base_url . 'images/no.png" alt=""></a>&nbsp;<strong>' . _("The strain already exists in box") . " $row[1] " . _("rank") . " $row[2]</strong>\n          </p>\n";   
     };
    } else {
     $row = sql_fetch_row($result);   
     print "          <p>\n            <a href=\"" . $base_url . $plugin . "new_strain.php\"><img src=\"" . $base_url . 'images/no.png" alt=""></a>&nbsp;<strong>' . _("The place") . " $row[1] ($row[2]) " . _("is already used by") . " '$row[0]'</strong>\n          </p>\n";
    };
   } else {
    print "          <p>\n            <a href=\"" . $base_url . $plugin . "new_strain.php\"><img src=\"" . $base_url . 'images/no.png" alt=""></a>&nbsp;<strong>' . _("Missing data!") . "</strong>\n          </p>\n";   
   };
  } elseif ($_POST['add']==2) {
   $result=sql_query("INSERT INTO strain (barcode, name, released, box, rank, strain_origin, ploidy, plasmid, phenotype, genotype, medium, pmid, species, notes) VALUES (" . addslashes($_SESSION['strain']['barcode']) . ",'" . addslashes($_SESSION['strain']['name']) . "','" . date("Y-m-d",$_SESSION['strain']['released']) . "'," . $_SESSION['strain']['box']  . "," . $_SESSION['strain']['rank']  . "," . (($_SESSION['strain']['ascendant']!='')?("'".addslashes($_SESSION['strain']['ascendant'])."'"):'NULL') . "," . (($_SESSION['strain']['ploidy']!='')?("'".addslashes($_SESSION['strain']['ploidy'])."'"):'NULL') . "," . (($_SESSION['strain']['plasmid']!='')?("'".addslashes($_SESSION['strain']['plasmid'])."'"):'NULL') . "," . (($_SESSION['strain']['phenotype']!='')?("'".addslashes($_SESSION['strain']['phenotype'])."'"):'NULL') . "," . (($_SESSION['strain']['genotype']!='')?("'".addslashes($_SESSION['strain']['genotype'])."'"):'NULL') . "," . (($_SESSION['strain']['medium']!='')?("'".addslashes($_SESSION['strain']['medium'])."'"):'NULL') . "," . (($_SESSION['strain']['pmid']!='')?("'".addslashes($_SESSION['strain']['pmid'])."'"):'NULL') . "," . $_SESSION['strain']['species'] . "," . (($_SESSION['strain']['notes']!='')?("'".addslashes($_SESSION['strain']['notes'])."'"):'NULL') . ");",$dbconn);
  if(strlen ($r=sql_last_error($dbconn))) {
   print "          <p>\n            <a href=\"" . $base_url . $plugin . "new_strain.php\"><img src=\"" . $base_url . "images/oops.png\" alt=\"\"></a>&nbsp;<strong>Oops</strong><br>\n            $r\n          </p>\n";
  } else {
   unset($_SESSION['strain']);
   print "          <p>\n            <a href=\"" . $base_url . $plugin . "\"><img src=\"" . $base_url . 'images/ok.png" alt=""></a>&nbsp;<strong>' . _("New strain added") . "</strong>\n          </p>\n";
  };
  } else {
   print "          <p>\n            <a href=\"" . $base_url . $plugin . "new_strain.php\"><img src=\"" . $base_url . 'images/no.png" alt=""></a>&nbsp;<strong>' . _("Missing data!") . "</strong>\n          </p>\n";
  };
 } else {
?>
          <p>
            <?php print _("Inform all following information:"); ?><br>
            <sup>*</sup> <small><?php print _("needed information"); ?></small>
          </p>
<?php
   $lev=error_reporting(8);
   if ($sqlserver=='postgresql') {
    $result=sql_query('SELECT max(box), max(rank) FROM strain WHERE box=(SELECT max(box) FROM strain);',$dbconn);
   } elseif ($sqlserver=='mysql') {
    $result=sql_query('SELECT max(box),NULL FROM strain;',$dbconn);
    if (sql_num_rows($result)==1) {
     $row=sql_fetch_row($result);
     $result=sql_query("SELECT $row[0], max(rank) FROM strain WHERE box=$row[0];",$dbconn);
    };
   } else {
    print "          <p>\n            <a href=\"" . $base_url . $plugin . "new_strain.php\"><img src=\"" . $base_url . "images/oops.png\" alt=\"\"></a>&nbsp;<strong>Oops</strong>\n          </p>\n          <p>\n            SQL server type '$sqlserver' unknown on this server\n          </p>\n";
   };
   error_reporting($lev);
   if((!(strlen ($r=sql_last_error($dbconn)))) && (sql_num_rows($result)==1)) {
    $row=sql_fetch_row($result);
    print "          <p>\n            " . _("Last place occupied: box") . " <strong>$row[0]</strong>, " . _("rank") . " <strong>$row[1]</strong>\n          </p>\n";
   };
?>
          <form action="<?php print $base_url . $plugin; ?>new_strain.php" method="post" onsubmit="return chkFormulaire()">
            <table summary="">
              <tr>
                <td>
                  <label for="name">&nbsp;<?php print _("Name"); ?>*&nbsp;</label>
                </td>
                <td>
                  <input type="text" size="10" maxlength="100" name="name" id="name" title="<?php print _("Strain name"); ?>">
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
                  <label for="ascendant">&nbsp;<?php print _("Ascendance"); ?>&nbsp;</label>
                </td>
                <td>
                  <select name="ascendant" id="ascendant" title="<?php print _("Parental strain"); ?>" onclick="if (this.value=='other') {document.forms[0].ascdID.style.display='';} else {document.forms[0].ascdID.style.display='none';}"><option></option><?php 
  $result=sql_query('SELECT name FROM strain;',$dbconn);
  if(!(strlen($r=sql_last_error($dbconn)))) {
   while($row=sql_fetch_row($result)) {
    print "<option value=\"$row[0]\">$row[0]</option>";
   };
  };
?><option value="other"><?php print _("other"); ?></option></select>&nbsp;&nbsp;<input type="text" name="ascdID" size="15" id="ascdID" style="display:none;">
                </td>
              </tr>
              <tr>
                <td>
                  <label for="phenotype">&nbsp;<?php print _("Phenotype"); ?>&nbsp;</label>
                </td>
                <td>
                  <input type="text" size="20" name="phenotype" id="phenotype" title="<?php print _("Phenotype"); ?>">
                </td>
              </tr>
              <tr>
                <td>
                  <label for="genotype">&nbsp;<?php print _("Genotype"); ?>&nbsp;</label>
                </td>
                <td>
                  <input type="text" size="20" name="genotype" id="genotype" title="<?php print _("Genotype"); ?>">
                </td>
              </tr>
              <tr>
                <td>
                  <label for="ploidy">&nbsp;<?php print _("Ploidy"); ?>&nbsp;</label>
                </td>
                <td>
                  <input type="text" size="20" name="ploidy" id="ploidy" title="<?php print _("Ploidy"); ?>">
                </td>
              </tr>
              <tr>
                <td>
                  <label for="plasmid">&nbsp;<?php print _("Plasmid"); ?>&nbsp;</label>
                </td>
                <td>
                  <input type="text" size="20" name="plasmid" id="plasmid" title="<?php print _("Plasmid"); ?>">
                </td>
              </tr>
              <tr>
                <td>
                  <label for="medium">&nbsp;<?php print _("Medium"); ?>&nbsp;</label>
                </td>
                <td>
                  <input type="text" size="20" name="medium" id="medium" title="<?php print _("medium"); ?>">
                </td>
              </tr>
              <tr>
                <td>
                  <label for="pmid">&nbsp;PMID/GenBank&nbsp;</label>
                </td>
                <td>
                  <input type="text" size="20" name="pmid" id="pmid" title="<?php print _("PubMed IDs"); ?>">
                </td>
              </tr>
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
