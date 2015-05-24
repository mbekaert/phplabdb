<?php
 ob_start("ob_gzhandler");
 session_start();
 include_once '../../includes/login.inc';
 $lang=((isset($_COOKIE['lang']))?substr($_COOKIE['lang'],0,2):'en');
 if (!($_SESSION['status'] & pow(2,$plugin_level['plasmiddb']))) {
  header('Location: ' . $base_url);
  exit;
 };
 $lev=error_reporting(8);
 function read_table($dbconn,$name,$lang) {
  $result=sql_query("SELECT id, legend FROM $name WHERE lang='$lang' ORDER BY id;",$dbconn);
  if(!(strlen($r=sql_last_error($dbconn)))) {
   while( $row = sql_fetch_row($result) ) {
    $table[$row[0]]=$row[1];
   };
  };
  return $table;
 };
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
 $dbconn=sql_connect($plugin_db['plasmiddb']);
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
.plasmid { background: url('../images/plasmid.png') no-repeat right top; }
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
<?php
 if (isset($_GET['remove'])) {
  if (isset($_GET['barcode'])) {
   $result=sql_query('SELECT name FROM plasmid WHERE barcode=' . intval($_GET['barcode']) . ';',$dbconn);
   if (!(strlen($r=sql_last_error($dbconn)))) {
    $row = sql_fetch_row($result);
    $result=sql_query("SELECT count(*) FROM plasmid WHERE AND name='$row[0]';",$dbconn);
    if (!(strlen($r=sql_last_error($dbconn)))) {
     if (sql_num_rows($result)!=1) {
      $result=sql_query("UPDATE FROM prototype SET plasmid='f' WHERE name='$row[0]';",$dbconn);
     };
     $result=sql_query('DELETE FROM plasmid WHERE barcode=' . intval($_GET['barcode']) . ';',$dbconn);
     if (!(strlen($r=sql_last_error($dbconn)))) {
      $msg="          <p>\n            <a href=\"" . $base_url . $plugin . "admin/\"><img src=\"" . $base_url . 'images/ok.png" alt=""></a>&nbsp;<strong>' . _("Plasmid removed") . "</strong>\n          </p>\n";       
     } else {
      $msg="         <p>\n           <a href=\"" . $base_url . $plugin . "admin/\"><img src=\"" . $base_url . "images/oops.png\" alt=\"\"></a>&nbsp;<strong>Oops</strong>\n         </p>\n         <p>\n           $r\n         </p>\n";
     };
    } else {
     $msg="         <p>\n           <a href=\"" . $base_url . $plugin . "admin/\"><img src=\"" . $base_url . "images/oops.png\" alt=\"\"></a>&nbsp;<strong>Oops</strong>\n         </p>\n         <p>\n           $r\n         </p>\n";    
    };
   } else {
    $msg="         <p>\n           <a href=\"" . $base_url . $plugin . "admin/\"><img src=\"" . $base_url . "images/oops.png\" alt=\"\"></a>&nbsp;<strong>Oops</strong>\n         </p>\n         <p>\n           $r\n         </p>\n";   
   };
  } elseif (isset($_GET['plasmid'])) {
   $plasmid=addslashes(stripslashes(rawurldecode($_GET['plasmid'])));
   $result=sql_query("DELETE FROM plasmid WHERE name='$plasmid';",$dbconn);
   if (!(strlen($r=sql_last_error($dbconn)))) {
    $result=sql_query("DELETE FROM alias WHERE name='$plasmid';",$dbconn);
    if (!(strlen($r=sql_last_error($dbconn)))) {
     $result=sql_query("DELETE FROM seq WHERE name='$plasmid';",$dbconn);
     if (!(strlen($r=sql_last_error($dbconn)))) {
      $result=sql_query("DELETE FROM map WHERE name='$plasmid';",$dbconn);
      if (!(strlen($r=sql_last_error($dbconn)))) {
       $result=sql_query("DELETE FROM prototype WHERE name='$plasmid';",$dbconn);
       if (!(strlen($r=sql_last_error($dbconn)))) {
        $msg="          <p>\n            <a href=\"" . $base_url . $plugin . "admin/\"><img src=\"" . $base_url . 'images/ok.png" alt=""></a>&nbsp;<strong>' . _("Prototype removed") . "</strong>\n          </p>\n";
       } else {
        $msg="         <p>\n           <a href=\"" . $base_url . $plugin . "admin/\"><img src=\"" . $base_url . "images/oops.png\" alt=\"\"></a>&nbsp;<strong>Oops</strong>\n         </p>\n         <p>\n           $r\n         </p>\n";
       };
      } else {
       $msg="         <p>\n           <a href=\"" . $base_url . $plugin . "admin/\"><img src=\"" . $base_url . "images/oops.png\" alt=\"\"></a>&nbsp;<strong>Oops</strong>\n         </p>\n         <p>\n           $r\n         </p>\n";
      };
     } else {
      $msg="         <p>\n           <a href=\"" . $base_url . $plugin . "admin/\"><img src=\"" . $base_url . "images/oops.png\" alt=\"\"></a>&nbsp;<strong>Oops</strong>\n         </p>\n         <p>\n           $r\n         </p>\n";
     };
    } else {
     $msg="         <p>\n           <a href=\"" . $base_url . $plugin . "admin/\"><img src=\"" . $base_url . "images/oops.png\" alt=\"\"></a>&nbsp;<strong>Oops</strong>\n         </p>\n         <p>\n           $r\n         </p>\n";
    };
   } else {
    $msg="         <p>\n           <a href=\"" . $base_url . $plugin . "admin/\"><img src=\"" . $base_url . "images/oops.png\" alt=\"\"></a>&nbsp;<strong>Oops</strong>\n         </p>\n         <p>\n           $r\n         </p>\n";
   };
  };
 } elseif ( !(isset($_POST['barcode'])) && !(empty($_GET['barcode']))) {
  $result=sql_query('SELECT name,box,rank,conditioning,preparation,conc FROM plasmid WHERE barcode=' . rawurldecode($_GET['barcode']) . ';',$dbconn);
  if(!(strlen($r=sql_last_error($dbconn))) && (sql_num_rows($result)==1)) {
   $row=sql_fetch_row($result);
?>
          <h3>
            <?php print _("Update"); ?>
          </h3>
          <form action="<?php print $base_url . $plugin; ?>admin/modif.php" method="post">
          <table summary="">
            <tr>
              <td>
                &nbsp;<?php print _("ID"); ?>&nbsp;
              </td>
              <td>
                <em><?php  print $_GET['barcode']; ?></em><input type="hidden" name="barcode" value="<?php print rawurlencode(rawurldecode($_GET['barcode'])); ?>">
              </td>
            </tr>
            <tr>
              <td>
                &nbsp;<?php print _("Prototype"); ?>&nbsp;
              </td>
              <td>
                <em><?php  print $row[0]; ?></em>
              </td>
            </tr>
            <tr>
              <td>
                <label for="box">&nbsp;<?php print _("Box"); ?>&nbsp;</label>
              </td>
              <td>
                <select name="box" id="box" title="<?php print _("Box"); ?>"><option></option><?php for ($i=1 ; $i<101; $i++) {print "<option value=\"$i\"".(($i==$row[1])?' selected="selected"':'').">$i</option>"; }; ?></select>
              </td>
            </tr>
            <tr>
              <td>
                <label for="rank">&nbsp;<?php print _("Rank"); ?>&nbsp;</label>
              </td>
              <td>
                <select name="rank" id="rank" title="<?php print _("place on the box"); ?>"><option></option><?php for ($i=1 ; $i<101; $i++) {print "<option value=\"$i\"".(($i==$row[2])?' selected="selected"':'').">$i</option>"; }; ?></select>
              </td>
            </tr>
            <tr>
                <td>
                  <label for="conditioning">&nbsp;<?php print _("Conditioning"); ?>&nbsp;</label>
                </td>
                <td>
                  <select name="conditioning" id="conditioning" title="<?php print _("Conditioning"); ?>" onchange="if (this.value=='other') {document.forms[0].condiID.style.display='';} else {document.forms[0].condiID.style.display='none';}"><option></option><?php
     unset($ok);
     $result2=sql_query("SELECT legend FROM conditioning WHERE lang='$lang';",$dbconn);
     if(!(strlen($r=sql_last_error($dbconn))) && (sql_num_rows($result2)!=0)) {
      while($row2=sql_fetch_row($result2)) {
       print "<option value=\"$row2[0]\"".(($row2[0]==$row[3])?' selected="selected"':'').">$row2[0]</option>";
       if ($row2[0]==$row[3]) $ok=true;
      };
     };
?><option value="other"<?php print ((!isset($ok) && !empty($row[3]))?' selected="selected"':''); ?>><?php print _("other"); ?></option></select>&nbsp;&nbsp;<input type="text" name="condiID" size="15" id="condiID" <?php print ((!isset($ok) && !empty($row[3]))?(' value="' . htmlentities($row[3]) . '"'):' style="display:none;"'); ?>>
                </td>
              </tr>
              <tr>
                <td>
                  <label for="preparation">&nbsp;<?php print _("Preparation"); ?>&nbsp;</label>
                </td>
                <td>
                <select name="preparation" id="preparation" title="<?php print _("Preparation"); ?>" onchange="if (this.value=='other') {document.forms[0].prepID.style.display='';} else {document.forms[0].prepID.style.display='none';}"><option></option><?php
    unset($ok);
    $result2=sql_query("SELECT legend FROM preparation WHERE lang='$lang';",$dbconn);
    if(!(strlen($r=sql_last_error($dbconn))) && (sql_num_rows($result2)!=0)) {
     while($row2=sql_fetch_row($result2)) {
      print "<option value=\"$row2[0]\"".(($row2[0]==$row[4])?' selected="selected"':'').">$row2[0]</option>";
      if ($row2[0]==$row[4]) $ok=true;
     };
    };
?><option value="other" <?php print ((!isset($ok) && !empty($row[4]))?' selected="selected"':''); ?>><?php print _("other"); ?></option></select>&nbsp;&nbsp;<input type="text" name="prepID" size="15" tabindex="11" id="prepID" <?php print ((!isset($ok) && !empty($row[4]))?(' value="' . htmlentities($row[4]) . '"'):' style="display:none;"'); ?>> &nbsp; - &nbsp; <input type="text" name="conc" title="<?php print _("Product concentration"); ?>" size="5" value="<?php print $row[5]; ?>"/>&nbsp;&micro;g/&micro;L
                </td>
              </tr>
            <tr>
              <td colspan="2">
                <?php print '<input name="reset" type="reset" value="' . _("Clear") . '"> &nbsp; ' . _("or") . ' &nbsp; <input type="submit" name="update" value="' . _("Update") . ' &gt;&gt;">'; ?> 
              </td>
            </tr>
          </table>
          </form>
<?php
  };
 }elseif ( !(isset($_GET['barcode'])) && !(empty($_POST['barcode']))) {
  if ((intval(trim(stripslashes($_POST['box'])))>0) && (intval(trim(stripslashes($_POST['rank'])))>0)) {
   $modif_local['box']=intval(trim(stripslashes($_POST['box'])));
   $modif_local['rank']=intval(trim(stripslashes($_POST['rank'])));
   $modif_local['conditioning']=(($_POST['conditioning']=='other')?trim(stripslashes($_POST['condiID'])):trim(stripslashes($_POST['conditioning'])));
   $modif_local['preparation']=(($_POST['preparation']=='other')?trim(stripslashes($_POST['prepID'])):trim(stripslashes($_POST['preparation'])));
   $modif_local['conc']=doubleval(str_replace(',','.',trim(stripslashes($_POST['conc']))));
   $result=sql_query('SELECT name FROM plasmid WHERE (box=' . $modif_local['box'] . ' AND rank=' . $modif_local['rank'] . ' AND barcode!=' . $_POST['barcode']. ');',$dbconn);
   if(!(strlen($r=sql_last_error($dbconn)))) {
    if (sql_num_rows($result)==1) {
     $row = sql_fetch_row($result);
     print "          <p>\n            <a href=\"" . $base_url . $plugin . "admin/modif.php?barcode=" . rawurlencode($_POST['barcode']) . "\"><img src=\"" . $base_url .  'images/no.png" alt=""></a>&nbsp;<strong>' . _("The place") . ' ' . $modif_local['box'] . ' (' . $modif_local['rank'] . ') ' . _("is already used by") . " '$row[0]'.</strong>\n          </p>\n";
    } elseif ($modif_local['conditioning']=='') {
     print "          <p>\n            <a href=\"" . $base_url . $plugin . "admin/modif.php?barcode=" . rawurlencode($_POST['barcode']) . "\"><img src=\"" . $base_url .  'images/no.png" alt=""></a>&nbsp;<strong>' . _("Conditioning must be specified!") . "</strong>\n          </p>\n";
    } else {
     $result=sql_query('UPDATE plasmid SET released=NOW(), box=' . $modif_local['box']. ', rank=' . $modif_local['rank'] . ', conditioning=\'' . addslashes($modif_local['conditioning']) . '\', preparation=' . (($modif_local['preparation']!='')?("'".addslashes($modif_local['preparation'])."'"):'NULL') . ', conc=' . (($modif_local['conc']>0)?$modif_local['conc']:'NULL') . ' WHERE barcode=' . $_POST['barcode'] . ';',$dbconn);
     if(strlen($r=sql_last_error($dbconn))) {
      print "          <p>\n            <a href=\"" . $base_url . $plugin . "admin/modif.php?barcode=" . rawurlencode($_POST['barcode']) . "\"><img src=\"" . $base_url . "images/oops.png\" alt=\"\"></a>&nbsp;<strong>Oops</strong><br>\n            $r\n          </p>\n";
     } else {
      print "          <p>\n            <a href=\"" . $base_url . $plugin . "admin/\"><img src=\"" . $base_url . 'images/ok.png" alt=""></a>&nbsp;<strong>' . _("Plasmid updated") . "</strong>\n          </p>\n";
     };
    };
   } else {
    print "          <p>\n            <a href=\"" . $base_url . $plugin . "admin/modif.php?barcode=" . rawurlencode($_POST['barcode']) . "\"><img src=\"" . $base_url .  'images/no.png" alt=""></a>&nbsp;<strong>' . _("Unable to open database!") . "</strong>\n          </p>\n";
   };
  } else {
   print "          <p>\n            <a href=\"" . $base_url . $plugin . "admin/modif.php?barcode=" . rawurlencode($_POST['barcode']) . "\"><img src=\"" . $base_url .  'images/no.png" alt=""></a>&nbsp;<strong>' . _("A box and rank must be provided!") . "</strong>\n          </p>\n";
  };
 } elseif ( !(isset($_POST['plasmid'])) && !(empty($_GET['plasmid']))) {
  unset($_SESSION['plasmid_mmap']);
  unset($_SESSION['plasmid_mseq']);
  $plasmid=addslashes(stripslashes(rawurldecode($_GET['plasmid']))); 
  $result=sql_query("SELECT name, length, circ, ascendant, dna, pmid, plasmid, created, selection, replication, notes FROM prototype WHERE name='$plasmid';",$dbconn);
  if(!(strlen($r=sql_last_error($dbconn))) && (sql_num_rows($result)==1)) {
   $row = sql_fetch_row($result);
   $result=sql_query("SELECT alias FROM alias WHERE name='$plasmid';",$dbconn);
   if(!(strlen($r=sql_last_error($dbconn)))) {
    while($row2=sql_fetch_row($result)) {
     $alias.=$row2[0] . ' ';
    };
    $_SESSION['plasmide_modif']['name']=$row[0];
    $_SESSION['plasmide_modif']['length']=$row[1];
    $result2=sql_query("SELECT seq FROM seq WHERE name='$plasmid';",$dbconn);
    if(!(strlen($r=sql_last_error($dbconn))) && (sql_num_rows($result2)==1)) {
     $row2 = sql_fetch_row($result2);
     $_SESSION['plasmide_mseq']['seq']=$row2[0];
    };
    $result2=sql_query("SELECT markers, enzymes FROM map WHERE name='$plasmid';",$dbconn);
    if(!(strlen($r=sql_last_error($dbconn))) && (sql_num_rows($result2)==1)) {
     $row2 = sql_fetch_row($result2);
     $_SESSION['plasmide_mmap']['markers']=$row2[0];
     $_SESSION['plasmide_mmap']['enzymes']=$row2[1];
    };
?>
          <h3>
            <?php print _("Update"); ?> 
          </h3>
          <form action="<?php print $base_url . $plugin; ?>admin/modif.php" method="post">
          <table summary="">
            <tr>
              <td>
                &nbsp;<?php print _("Prototype"); ?>&nbsp;
              </td>
              <td>
                <em><?php  print $row[0]; ?></em><input type="hidden" name="plasmid" value="<?php print rawurlencode(addslashes($row[0])); ?>">
              </td>
            </tr>
              <tr>
                <td>
                  <label for="alias">&nbsp;<?php print _("Alias"); ?>&nbsp;</label>
                </td>
                <td>
                  <input type="text" size="20" maxlength="50" name="alias" id="alias" <?php print 'title="' . _("plasmid/vector alias or short name") . '" value="' . $alias . '"'; ?>">
                </td>
              </tr>
              <tr>
                <td>
                  <label for="alias">&nbsp;<?php print _("Sequence"); ?>&nbsp;</label>
                </td>
                <td>
                  <a href="#" onClick="window.open('modifseq.php', '<?php print _("Sequence"); ?>', 'toolbar=no, location=no, directories=no, status=no, scrollbars=no, resizable=yes, copyhistory=no, width=600, height=400, left=300, top=50')"><img src="../images/wizard.png" alt="<?php print _("Sequence"); ?>"></a>
                </td>
              </tr>
              <tr>
                <td>
                  <label for="alias">&nbsp;<?php print _("Map"); ?>&nbsp;</label>
                </td>
                <td>
                  <a href="#" onClick="window.open('modifmap.php', '<?php print _("Map"); ?>', 'toolbar=no, location=no, directories=no, status=no, scrollbars=yes, resizable=yes, copyhistory=no, width=600, height=500, left=150, top=50')"><img src="../images/wizard.png" alt="<?php print _("Map"); ?>"></a>
                </td>
              </tr>
              <tr>
                <td>
                  <label for="length">&nbsp;<?php print _("Length"); ?>&nbsp;</label>
                </td>
                <td>
                  <input type="text" size="10" maxlength="10" name="length" id="length" title="<?php print _("length in nucleotides"); ?>" value="<?php print $row[1]; ?>">
                </td>
              </tr>
              <tr>
                <td>
                  &nbsp;<?php print _("Type"); ?>&nbsp;
                </td>
                <td>
                  <input type="radio" name="circ" id="circular" title="<?php print _("Circular plasmid/vector"); ?>" value="t"<?php print (($row[2]=='t')?' checked="checked"':''); ?>>&nbsp;<label for="circular"><?php print _("circular"); ?></label>&nbsp;&nbsp;<input type="radio" name="circ" id="linear" title="<?php print _("Linear plasmid/vector"); ?>" value="f"<?php print (($row[2]=='f')?' checked="checked"':''); ?>>&nbsp;<label for="linear"><?php print _("linear"); ?></label>
                </td>
              </tr>
              <tr>
                <td>
                  &nbsp;<?php print _("Nucleic Acid"); ?>&nbsp;
                </td>
                <td>
                  <input type="radio" name="dna" id="dna" title="<?php print _("DNA"); ?>" value="d" <?php print (($row[4]=='d')?' checked="checked"':''); ?>>&nbsp;<label for="dna"><?php print _("DNA"); ?></label>&nbsp;&nbsp;<input type="radio" name="dna" id="rna" title="<?php print _("RNA"); ?>" value="r"<?php print (($row[4]=='r')?' checked="checked"':''); ?>>&nbsp;<label for="rna"><?php print _("RNA"); ?></label>
                </td>
              </tr>
              <tr>
                <td>
                  &nbsp;<?php print _("Date"); ?>&nbsp;
                </td>
                <td>
                  <?php print newtime($row[7]); ?> 
                </td>
              </tr>
              <tr>
                <td>
                  <label for="ascendant">&nbsp;<?php print _("Ascendance"); ?>&nbsp;</label>
                </td>
                <td>
                  <select name="ascendant" id="ascendant" title="Parental plasmid/vector" onclick="if (this.value=='other') {document.forms[0].ascdID.style.display='';} else {document.forms[0].ascdID.style.display='none';}"><option></option><?php 
  $result3=sql_query('SELECT name FROM prototype;',$dbconn);
  while($row3=sql_fetch_row($result3)) {
   if ($row3[0]!=$row[0])
    if ($row3[0]==$row[3]) {
     print "<option value=\"$row3[0]\" selected=\"selected\">$row3[0]</option>";
     $ok=true;
    } else {
     print "<option value=\"$row3[0]\">$row3[0]</option>";
    };
  };
?><option value="other"<?php print ((!empty($row[3]) && !isset($ok))?' selected="selected"':''); ?>><?php print _("other"); ?></option></select>&nbsp;&nbsp;<input type="text" name="ascdID" size="15" id="ascdID" <?php  print ((!empty($row[3]) && !isset($ok))?" value=\"$row[3]\"":' style="display:none;"'); ?>">
                </td>
              </tr>
              <tr>
                <td>
                  <label for="pmid">&nbsp;PMID/GenBank&nbsp;</label>
                </td>
                <td>
                  <input type="text" size="20" maxlength="100" name="pmid" id="pmid" title="<?php print _("PubMed IDs"); ?>" value="<?php print $row[5]; ?>">
                </td>
              </tr>
              <tr>
                <td>
                  &nbsp;<?php print _("Selection and Replication"); ?>&nbsp;
                </td>
                <td>
                  <table summary="">
<?php
    $result3=sql_query("SELECT a.organism, a.legend, b.legend FROM selection a, replication b WHERE a.lang=b.lang AND a.lang='$lang' AND a.organism=b.organism;",$dbconn);
    if(!(strlen($r=sql_last_error($dbconn)))) {
     $i=0;
     foreach((explode('*',$row[8])) as $x) {
      $y=explode('|',$x);
      $organism_selection[$y[0]]=array();
      foreach((explode('#',$y[1])) as $z) {
       if (!empty($z)) $organism_selection[$y[0]][]=$z;
      };
     };
     foreach((explode('*',$row[9])) as $x) {
      $y=explode('|',$x);
      $organism_replication[$y[0]]=array();
      foreach((explode('#',$y[1])) as $z) {
       if (!empty($z)) $organism_replication[$y[0]][]=$z;
      };
     };
     while($row3=sql_fetch_row($result3)) {
      $selection='';
      $replication='';
      foreach((explode('|',$row3[1])) as $select) {
       if (!empty($organism_selection[$row3[0]])) {
        $selection .=  "<option value=\"$select\"" . ((array_search($select,$organism_selection[$row3[0]])===false)?'':' selected="selected"') . ">$select</option>";
        if (!((($key=array_search($select,$organism_selection[$row3[0]]))===false))) {
         unset($organism_selection[$row3[0]][$key]);
        };
       } else {
        $selection .= "<option value=\"$select\">$select</option>";
       };
      };
      foreach((explode('|',$row3[2])) as $replic) {
       if (!empty($organism_replication[$row3[0]])) {
        $replication .=  "<option value=\"$replic\"" . ((array_search($replic,$organism_replication[$row3[0]])===false)?'':' selected="selected"') . ">$replic</option>";
        if (!((($key=array_search($replic,$organism_replication[$row3[0]]))===false))) {
         unset($organism_replication[$row3[0]][$key]);
        };
       };
      };
?> 
                    <tr>
                      <td>
                        <input type="checkbox" name="navette[<?php print $i; ?>]" title="<?php print $organisme[0]; ?>" onclick="if (this.checked) {document.forms[0].organismeID<?php print $i; ?>.style.display='';document.forms[0].replication<?php print $i; ?>.style.display='';document.forms[0].organisme_other<?php print $i; ?>.style.display=''} else {document.forms[0].organismeID<?php print $i; ?>.style.display='none'; document.forms[0].organisme_other<?php print $i; ?>.style.display='none'; document.forms[0].replication<?php print $i; ?>.style.display='none'; document.forms[0].repli<?php print $i; ?>.style.display='none';document.forms[0].organisme_other<?php print $i; ?>.style.display='none'};" <?php print ((isset($organism_selection[$row3[0]]))?' checked="checked"':''); ?>><em><?php print $row3[0]?></em>&nbsp;<input type="hidden" value="<?php print $row3[0]; ?>" name="organisme[<?php print $i; ?>]" id="organisme<?php print $i; ?>">
                      </td>
                      <td>
                        <select multiple="multiple" title="<?php print _("Selection"); ?>" name="organismeID[<?php print $i; ?>][]" id="organismeID<?php print $i; ?>" size="2" <?php print ((isset($organism_selection[$row3[0]]))?'':' style="display:none;"'); ?>><?php print $selection; ?></select>
                      </td>
                      <td>
                        <input type="text" size="20" name="organisme_other[<?php print $i; ?>]" id="organisme_other<?php print $i . '"' . ((isset($organism_selection[$row3[0]]))?'':' style="display:none;"') . ((count($organism_selection[$row3[0]])!=0)?(' value="' . implode(', ', $organism_selection[$row3[0]]) . '"'):''); ?>>
                      </td>
                      <td>
                        <select title="<?php print _("Replication"); ?>" name="replication[<?php print $i; ?>]" id="replication<?php print $i . '"' . ((isset($organism_selection[$row3[0]]))?'':' style="display:none;"'); ?> onchange="if(this.value=='other'){ document.forms[0].repli<?php print $i; ?>.style.display=''; } else { document.forms[0].repli<?php print $i; ?>.style.display='none'; }"><option></option><?php print $replication; ?><option value="other"<?php print ((count($organism_replication[$row3[0]])!=0)?' selected="selected"':'')?>><?php print _("other"); ?></option></select>
                      </td>
                      <td>
                        <input type="text" size="20" name="repli[<?php print $i; ?>]" id="repli<?php print $i; ?>"<?php print ((count($organism_replication[$row3[0]])!=0)?(' value="' . implode(', ', $organism_replication[$row3[0]]) . '"'):' style="display:none;"'); ?>>
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
            <tr>
              <td>
                <label for="note">&nbsp;<?php print _("Notes"); ?>&nbsp;</label>
              </td>
              <td>
                <textarea name="notes" id="note" rows="4" cols="40"><?php print $row[10] ?></textarea>
              </td>
            </tr>
            <tr>
              <td colspan="2">
                <?php print '<input name="reset" type="reset" value="' . _("Clear") . '"> &nbsp; ' . _("or") . ' &nbsp; <input type="submit" name="update" value="' . _("Update") . ' &gt;&gt;">'; ?> 
              </td>
            </tr>
          </table>
          </form>
<?php
   };
  };
 } elseif ( !(isset($_GET['plasmid'])) && !(empty($_POST['plasmid']))) {
  if ((trim(stripslashes($_POST['circ']))!='') && (trim(stripslashes($_POST['dna']))!='')) {
   $modif_proto['name']=trim(stripslashes($_POST['plasmid']));
   $modif_proto['length']=(($_SESSION['plasmide_modif']['length']>0)?$_SESSION['plasmide_modif']['length']:intval(trim(stripslashes($_POST['length']))));
   $modif_proto['circ']=trim(stripslashes($_POST['circ']));
   $modif_proto['dna']=trim(stripslashes($_POST['dna']));
   $modif_proto['pmid']=trim(stripslashes($_POST['pmid']));
   $modif_proto['created']=mktime (0,0,0,trim(stripslashes($_POST['date_M'])),trim(stripslashes($_POST['date_D'])),trim(stripslashes($_POST['date_Y'])));
   $modif_proto['ascendant']=(($_POST['ascendant']=='other')?trim(stripslashes($_POST['ascdID'])):trim(stripslashes($_POST['ascendant'])));
   $modif_proto['description']=trim(stripslashes($_POST['notes']));   
   if(trim(stripslashes($_POST['alias']))!='') {
    $tmp=array_unique(explode(' ',trim(stripslashes($_POST['alias']))));
    $result=sql_query("SELECT alias FROM alias WHERE (name!='" . $modif_proto['name'] . "' AND alias='" . implode("' OR alias='",$tmp)."');",$dbconn);
    if(sql_num_rows($result)==0) {
     $modif_proto['alias']=$tmp;
    } else {
     $row = sql_fetch_row($result);
     print "          <p>\n            <a href=\"" . $base_url . $plugin . "admin/modif.php?barcode=" . rawurlencode($_POST['barcode']) . "\"><img src=\"" . $base_url .  'images/no.png" alt=""></a>&nbsp;<strong>' . _("The alias") . " '$row[0]' " . _("is already used!") . "</strong>\n          </p>\n";
     $out=1;
    };
   };
   if (isset($_POST['navette'])) {
    foreach($_POST['navette'] as $navette_key => $navette_value) {
     $plasmide_replication[$navette_key]=trim(stripslashes($_POST['organisme'][$navette_key])) . '|';
     $plasmide_selection[$navette_key]=trim(stripslashes($_POST['organisme'][$navette_key])) . '|';
     if (isset($_POST['organismeID'][$navette_key])) {
      $plasmide_selection[$navette_key].=implode('#',$_POST['organismeID'][$navette_key]);
     };
     if (!(empty($_POST['organisme_other'][$navette_key]))) {
      $plasmide_selection[$navette_key].=((isset($_POST['organismeID'][$navette_key]))?'#':'') . trim(stripslashes($_POST['organisme_other'][$navette_key]));
     };
     if (!(empty($_POST['repli'][$navette_key])) && ($_POST['replication'][$navette_key]=='other')) {
      $plasmide_replication[$navette_key].=trim(stripslashes($_POST['repli'][$navette_key]));
     } elseif (!(empty($_POST['replication'][$navette_key]))) {
      $plasmide_replication[$navette_key].=trim(stripslashes($_POST['replication'][$navette_key]));
     };
    };
   };
   $modif_proto['replication']=implode('*',$plasmide_replication);
   $modif_proto['selection']=implode('*',$plasmide_selection);
   if (!isset($out)) {
    $result=sql_query("UPDATE prototype SET length=" . (($modif_proto['length']>0)?$modif_proto['length']:'NULL') . ", circ='" . $modif_proto['circ'] . "', ascendant=" . (($modif_proto['ascendant']!='')?("'".addslashes($modif_proto['ascendant'])."'"):'NULL') . ", dna='" . $modif_proto['dna'] . "', pmid=" . (($modif_proto['pmid']!='')?("'".addslashes($modif_proto['pmid'])."'"):'NULL') . ", created='" . date("Y-m-d",$modif_proto['created']) . "', selection=" . (($modif_proto['selection']!='')?("'".addslashes($modif_proto['selection'])."'"):'NULL') . ", replication=" . (($modif_proto['replication']!='')?("'".addslashes($modif_proto['replication'])."'"):'NULL') . ", notes=" . (($modif_proto['description']!='')?("'".addslashes($modif_proto['description'])."'"):'NULL') . " WHERE name='" . addslashes($modif_proto['name']) . "';",$dbconn);
    unset($_SESSION['plasmide_modif']);
    if(!(strlen($r=sql_last_error($dbconn)))) {
     if (isset($modif_proto['alias'])) { 
      $result=sql_query("DELETE FROM alias WHERE name='" . addslashes($modif_proto['name']) . "';",$dbconn);
      foreach($modif_proto['alias'] as $alias) {
       if ($alias!=$modif_proto['name']) {
        $result=sql_query("INSERT INTO alias (name, alias) VALUES ('" . addslashes($modif_proto['name']) . "','" . addslashes($alias) . "');",$dbconn);
        if(strlen($r=sql_last_error($dbconn))) {
         print "          <p>\n            <a href=\"" . $base_url . $plugin . "admin/modif.php?barcode=" . rawurlencode($_POST['barcode']) . "\"><img src=\"" . $base_url . "images/oops.png\" alt=\"\"></a>&nbsp;<strong>Oops</strong><br>\n            $r\n          </p>\n";
         $in=1;
        };
       };
      };
     };
     if (isset($_SESSION['plasmide_mseq']['modif'])) {
      $result=sql_query("DELETE FROM seq WHERE name='" . addslashes($modif_proto['name']) . "';",$dbconn);      
      if (strlen($_SESSION['plasmide_mseq']['seq'])>0) $result=sql_query("INSERT INTO seq (name, seq) VALUES ('" . addslashes($modif_proto['name']) . "','" . $_SESSION['plasmide_mseq']['seq'] . "');",$dbconn);
      unset($_SESSION['plasmide_mseq']);
      if(strlen($r=sql_last_error($dbconn))) {
       print "          <p>\n            <a href=\"" . $base_url . $plugin . "admin/modif.php?barcode=" . rawurlencode($_POST['barcode']) . "\"><img src=\"" . $base_url . "images/oops.png\" alt=\"\"></a>&nbsp;<strong>Oops</strong><br>\n            $r\n          </p>\n";
       $in=1;
      };
     };
     if (isset($_SESSION['plasmide_mmap']['modif'])) {
      $result=sql_query("DELETE FROM map WHERE name='" . addslashes($modif_proto['name']) . "';",$dbconn);
      if ((strlen($_SESSION['plasmide_mmap']['markers'])>0) || (strlen($_SESSION['plasmide_mmap']['enzymes'])>0)) $result=sql_query("INSERT INTO map (name, markers, enzymes) VALUES ('" . addslashes($modif_proto['name']) . "'," . (($_SESSION['plasmide_mmap']['markers']!='')?("'".addslashes($_SESSION['plasmide_mmap']['markers'])."'"):'NULL') . "," . (($_SESSION['plasmide_mmap']['enzymes']!='')?("'".addslashes($_SESSION['plasmide_mmap']['enzymes'])."'"):'NULL') . ");",$dbconn);
      unset($_SESSION['plasmide_mmap']);
      if(strlen($r=sql_last_error($dbconn))) {
       print "          <p>\n            <a href=\"" . $base_url . $plugin . "admin/modif.php?barcode=" . rawurlencode($_POST['barcode']) . "\"><img src=\"" . $base_url . "images/oops.png\" alt=\"\"></a>&nbsp;<strong>Oops</strong><br>\n            $r\n          </p>\n";
       $in=1;
      };
     };
    } else {
     print "          <p>\n            <a href=\"" . $base_url . $plugin . "admin/modif.php?barcode=" . rawurlencode($_POST['barcode']) . "\"><img src=\"" . $base_url . "images/oops.png\" alt=\"\"></A>&nbsp;<strong>Oops</strong><br>\n            $r\n          </p>\n";
     $in=1;
    };
   };
   if (!isset($in)) {
    print "          <p>\n            <a href=\"" . $base_url . $plugin . "admin/\"><img src=\"" . $base_url . 'images/ok.png" alt=""></a>&nbsp;<strong>' . _("Prototype updated") . "</strong>\n          </p>\n";
   };
  } else {
   print "          <p>\n            <a href=\"" . $base_url . $plugin . "admin/modif.php?barcode=" . rawurlencode($_POST['barcode']) . "\"><img src=\"" . $base_url .  'images/no.png" alt=""></a>&nbsp;<strong>' . _("Invalid data") . "</strong>\n          </p>\n";
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
