<?php
 ob_start("ob_gzhandler");
 session_start();
 include_once '../../includes/login.inc';
 if (!($_SESSION['status'] & pow(2,$plugin_level['straindb']))) {
  header('Location: ' . $base_url);
  exit;
 };
 $lev=error_reporting(8);
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
 $status=$_SESSION['status'];
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
.strain { background: url('../images/strain.png') no-repeat right top; }
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
            <?php print _("Update"); ?>
          </h3>
<?php 
 if (isset($_GET['remove'])) {
  if (isset($_GET['barcode'])) {
   $result=sql_query('DELETE FROM strain WHERE barcode=' . intval(rawurldecode($_GET['barcode'])) . ';',$dbconn);
   if (!(strlen($r=sql_last_error($dbconn)))) {
    print "          <p>\n            <a href=\"" . $base_url . $plugin . "admin/\"><img src=\"" . $base_url . 'images/ok.png" alt=""></a>&nbsp;<strong>' . _("Strain removed") . "</strong>\n          </p>\n";       
   } else {
    print "         <p>\n           <a href=\"" . $base_url . $plugin . "admin/\"><img src=\"" . $base_url . "images/oops.png\" alt=\"\"></a>&nbsp;<strong>Oops</strong>\n         </p>\n         <p>\n           $r\n         </p>\n";
   };
  } else {
   print "          <p>\n            <a href=\"" . $base_url . $plugin . "admin/\"><img src=\"" . $base_url . 'images/no.png" alt=""></a>&nbsp;<strong>' . _("Missinn data!") . "</strong>\n          </p>\n";    
  };
 } elseif (isset($_POST['next'])) {
  if (isset($_POST['barcode'])) {
   if ((trim(stripslashes($_POST['name']))!='') && (intval(trim(stripslashes($_POST['box'])))>0)  && (intval(trim(stripslashes($_POST['rank'])))>0) && (intval(trim(stripslashes($_POST['species'])))>0)) {
    $result=sql_query("SELECT name,box,rank FROM strain WHERE (box=" . intval(trim(stripslashes($_POST['box']))) . ' AND rank=' . intval(trim(stripslashes($_POST['rank']))) . ' AND barcode!='.intval($_POST['barcode']).');',$dbconn);
    if ((!(strlen($r=sql_last_error($dbconn)))) && (sql_num_rows($result)==0)) {
     $result=sql_query("SELECT name,box,rank FROM strain WHERE (name='" . addslashes(trim(stripslashes($_POST['name']))) . "' AND barcode!=".intval($_POST['barcode']).');',$dbconn);
     if ((!(strlen($r=sql_last_error($dbconn)))) && (sql_num_rows($result)==0)) {
      $strain['name']=trim(stripslashes($_POST['name']));
      $strain['released']=mktime(0,0,0,trim(stripslashes($_POST['date_M'])),trim(stripslashes($_POST['date_D'])),trim(stripslashes($_POST['date_Y'])));
      $strain['box']=intval(trim(stripslashes($_POST['box'])));
      $strain['rank']=intval(trim(stripslashes($_POST['rank'])));
      $strain['species']=intval(trim(stripslashes($_POST['species'])));
      $strain['ascendant']=(($_POST['ascendant']=='other')?trim(stripslashes($_POST['ascdID'])):trim(stripslashes($_POST['ascendant'])));
      $strain['pmid']=trim(stripslashes($_POST['pmid']));    
      $strain['ploidy']=trim(stripslashes($_POST['ploidy']));
      $strain['plasmid']=trim(stripslashes($_POST['plasmid']));
      $strain['phenotype']=trim(stripslashes($_POST['phenotype']));
      $strain['genotype']=trim(stripslashes($_POST['genotype']));
      $strain['medium']=trim(stripslashes($_POST['medium']));
      $strain['notes']=trim(stripslashes($_POST['notes']));
      $lev=error_reporting(8);
      $result=sql_query("UPDATE strain SET name='" . addslashes($strain['name']) . "',released='" . date("Y-m-d",$strain['released']) . "',box=" . $strain['box']  . ",rank=" . $strain['rank']  . ",species=" . $strain['species'] . ",strain_origin=" . (($strain['ascendant']!='')?("'".addslashes($strain['ascendant'])."'"):'NULL') . ",ploidy=" . (($strain['ploidy']!='')?("'".addslashes($strain['ploidy'])."'"):'NULL') . ",plasmid=" . (($strain['plasmid']!='')?("'".addslashes($strain['plasmid'])."'"):'NULL') . ",phenotype=" . (($strain['phenotype']!='')?("'".addslashes($strain['phenotype'])."'"):'NULL') . ",genotype=" . (($strain['genotype']!='')?("'".addslashes($strain['genotype'])."'"):'NULL') . ",medium=" . (($strain['medium']!='')?("'".addslashes($strain['medium'])."'"):'NULL') . ",pmid=" . (($strain['pmid']!='')?("'".addslashes($strain['pmid'])."'"):'NULL') . ",notes=" . (($strain['notes']!='')?("'".addslashes($strain['notes'])."'"):'NULL') . ' WHERE barcode=' . intval($_POST['barcode']) . ';',$dbconn);
      error_reporting($lev);
      if(strlen ($r=sql_last_error($dbconn))) {
       print "          <p>\n            <a href=\"" . $base_url . $plugin . 'admin/modif.php?barcode=' . intval($_POST['barcode']) . '"><img src="' . $base_url . "images/oops.png\" alt=\"\"></a>&nbsp;<strong>Oops</strong><br>\n            $r\n          </p>\n";
      } else {
       print "          <p>\n            <a href=\"" . $base_url . $plugin . 'admin/"><img src="' . $base_url . 'images/ok.png" alt=""></a>&nbsp;<strong>' . _("New strain added") . "</strong>\n          </p>\n";
      };      
     } else {
      $row = sql_fetch_row($result);   
      print "          <p>\n            <a href=\"" . $base_url . $plugin . 'admin/modif.php?barcode=' . rawurlencode($_POST['barcode']) . "\"><img src=\"" . $base_url . 'images/no.png" alt=""></a>&nbsp;<strong>' . _("The strain already exists in box") . " $row[1] " . _("rank") . " $row[2]</strong>\n          </p>\n";   
     };
    } else {
     $row = sql_fetch_row($result);   
     print "          <p>\n            <a href=\"" . $base_url . $plugin . "admin/modif.php?barcode=" . rawurlencode($_POST['barcode']) . "\"><img src=\"" . $base_url . 'images/no.png" alt=""></a>&nbsp;<strong>' . _("The place") . " $row[1] ($row[2]) " . _("is already used by") . " '$row[0]'</strong>\n          </p>\n";
    };
   } else {
    print "          <p>\n            <a href=\"" . $base_url . $plugin . "admin/modif.php?barcode=" . rawurlencode($_POST['barcode']) . "\"><img src=\"" . $base_url . 'images/no.png" alt=""></a>&nbsp;<strong>' . _("Missing data!") . "</strong>\n          </p>\n";   
   };
  } else {
   print "          <p>\n            <a href=\"" . $base_url . $plugin . "admin/\"><img src=\"" . $base_url . 'images/no.png" alt=""></a>&nbsp;<strong>' . _("Missing data!") . "</strong>\n          </p>\n";
  };
 } else {
  $result=sql_query('SELECT barcode, name, released, box, rank, strain_origin, ploidy, plasmid, phenotype, genotype, medium, pmid, species, notes FROM strain WHERE barcode=' . intval(rawurldecode($_GET['barcode'])) . ';',$dbconn);
  if(!(strlen($r=sql_last_error($dbconn))) && (sql_num_rows($result)==1)) {
   $row=sql_fetch_row($result);
?>
          <p>
            <?php print _("Inform following information:"); ?><br>
            <sup>*</sup> <small><?php print _("needed information"); ?></small>
          </p>
          <form action="<?php print $base_url . $plugin; ?>admin/modif.php" method="post">
            <table summary="">
              <tr>
                <td>
                  <label for="name">&nbsp;<?php print _("Name"); ?>*&nbsp;</label>
                </td>
                <td>
                  <input type="text" size="10" maxlength="50" name="name" id="name" title="<?php print _("Strain name"); ?>" value="<?php print $row[1]; ?>">
                </td>
              </tr>
              <tr>
                <td>
                  &nbsp;<?php print _("Date"); ?>*&nbsp;
                </td>
                <td>
                  <?php print newtime($row[2]); ?> 
                </td>
              </tr>
              <tr>
                <td>
                  <label for="box">&nbsp;<?php print _("Box"); ?>*&nbsp;</label>
                </td>
                <td>
                  <select name="box" id="box" title="<?php print _("Box"); ?>"><option></option><?php for ($i=1 ; $i<101; $i++) {print "<option value=\"$i\"".(($i==$row[3])?' selected="selected"':'').">$i</option>"; }; ?></select>
                </td>
              </tr>
              <tr>
                <td>
                  <label for="rank">&nbsp;<?php print _("Rank"); ?>*&nbsp;</label>
                </td>
                <td>
                  <select name="rank" id="rank" title="<?php print _("place on the box"); ?>"><option></option><?php for ($i=1 ; $i<101; $i++) {print "<option value=\"$i\"".(($i==$row[4])?' selected="selected"':'').">$i</option>"; }; ?></select>
                </td>
              </tr>
              <tr>
                <td>
                  <label for="species">&nbsp;<?php print _("Species"); ?><sup>*</sup>&nbsp;</label>
                </td>
                <td>
                  <select name="species" id="species" title="<?php print _("Species"); ?>"><option></option><?php
  $result2=sql_query('SELECT id, name FROM species ORDER BY name;',$dbconn);
  if(!(strlen($r=sql_last_error($dbconn)))) {
   while($row2=sql_fetch_row($result2)) {
    print "<option value=\"$row2[0]\"".(($row2[0]==$row[12])?' selected="selected"':'').">$row2[1]</option>";
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
                  <select name="ascendant" id="ascendant" title="Parental plasmid/vector" onclick="if (this.value=='other') {document.forms[0].ascdID.style.display='';} else {document.forms[0].ascdID.style.display='none';}"><option></option><?php 
  $result2=sql_query('SELECT name FROM strain;',$dbconn);
  while($row2=sql_fetch_row($result2)) {
   if ($row2[0]!=$row[1])
    if ($row2[0]==$row[5]) {
     print "<option value=\"$row2[0]\" selected=\"selected\">$row2[0]</option>";
     $ok=true;
    } else {
     print "<option value=\"$row2[0]\">$row2[0]</option>";
    };
  };
?><option value="other"<?php print ((!empty($row[5]) && !isset($ok))?' selected="selected"':''); ?>><?php print _("other"); ?></option></select>&nbsp;&nbsp;<input type="text" name="ascdID" size="15" id="ascdID" <?php  print ((!empty($row[5]) && !isset($ok))?" value=\"$row[5]\"":' style="display:none;"'); ?>">
                </td>
              </tr>              
              <tr>
                <td>
                  <label for="phenotype">&nbsp;<?php print _("Phenotype"); ?>&nbsp;</label>
                </td>
                <td>
                  <input type="text" size="20" name="phenotype" id="phenotype" title="<?php print _("Phenotype"); ?>" value="<?php print $row[8]; ?>">
                </td>
              </tr>
              <tr>
                <td>
                  <label for="genotype">&nbsp;<?php print _("Genotype"); ?>&nbsp;</label>
                </td>
                <td>
                  <input type="text" size="20" name="genotype" id="genotype" title="<?php print _("Genotype"); ?>" value="<?php print $row[9]; ?>">
                </td>
              </tr>
              <tr>
                <td>
                  <label for="ploidy">&nbsp;<?php print _("Ploidy"); ?>&nbsp;</label>
                </td>
                <td>
                  <input type="text" size="20" name="ploidy" id="ploidy" title="<?php print _("Ploidy"); ?>" value="<?php print $row[6]; ?>">
                </td>
              </tr>
              <tr>
                <td>
                  <label for="plasmid">&nbsp;<?php print _("Plasmid"); ?>&nbsp;</label>
                </td>
                <td>
                  <input type="text" size="20" name="plasmid" id="plasmid" title="<?php print _("Plasmid"); ?>" value="<?php print $row[7]; ?>">
                </td>
              </tr>
              <tr>
                <td>
                  <label for="medium">&nbsp;<?php print _("Medium"); ?>&nbsp;</label>
                </td>
                <td>
                  <input type="text" size="20" name="medium" id="medium" title="<?php print _("medium"); ?>" value="<?php print $row[10]; ?>">
                </td>
              </tr>
              <tr>
                <td>
                  <label for="pmid">&nbsp;PMID/GenBank&nbsp;</label>
                </td>
                <td>
                  <input type="text" size="20" name="pmid" id="pmid" title="<?php print _("PubMed IDs"); ?>" value="<?php print $row[11]; ?>">
                </td>
              </tr>
              <tr>
                <td>
                  <label for="note">&nbsp;<?php print _("Notes"); ?>&nbsp;</label>
                </td>
                <td>
                  <textarea name="notes" id="note" rows="4" cols="40"><?php print $row[13]; ?></textarea>
                </td>
              </tr>
              <tr>
                <td colspan="2">
                  <?php print '<input type="hidden" name="barcode" value="' . $row[0] . '"><input name="clear" type="reset" value="' . _("Clear") . '"> &nbsp; ' . _("or") . ' &nbsp; <input type="submit" name="next" value="' . _("Update") . ' &gt;&gt;">'; ?>                
                </td>
              </tr>
            </table>
          </form>
<?php } else {
   print "          <p>\n            <a href=\"" . $base_url . $plugin . "admin/\"><img src=\"" . $base_url . 'images/no.png" alt=""></a>&nbsp;<strong>' . _("Missing data!") . "</strong>\n          </p>\n";
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
