<?php
 ob_start("ob_gzhandler");
 session_start();
 include_once '../../includes/login.inc';
 $lang=((isset($_COOKIE['lang']))?substr($_COOKIE['lang'],0,2):'en');
 if (!($_SESSION['status'] & pow(2,$plugin_level['seeddb']))) {
  header('Location: ' . $base_url);
  exit;
 };
 $lev=error_reporting(8);
 function read_table($dbconn,$name,$lang) {
  $table=array();
  $result=sql_query("SELECT id, legend FROM $name WHERE lang='$lang' ORDER BY id;",$dbconn);
  if(!(strlen($r=sql_last_error($dbconn)))) {
   while( $row = sql_fetch_row($result) ) {
    $table[$row[0]]=htmlentities($row[1]);
   };
  };
  return $table;
 };
 function species($dbconn) {
  $species=array(); 
  $result=sql_query("SELECT id, name FROM species ORDER BY id;",$dbconn);
  if(!(strlen($r=sql_last_error($dbconn)))) {
   while( $row = sql_fetch_row($result) ) {
    $species[$row[0]]=htmlentities($row[1]);
   };
  };
  return $species;
 };
 $status=$_SESSION['status'];
 $type=((empty($_POST['type']))?((empty($_POST['type']))?array():$_GET['type']):$_POST['type']);
 $data=((empty($_POST['data']))?((empty($_POST['data']))?array():$_GET['data']):$_POST['data']);
 $jointure=((empty($_POST['jointure']))?((empty($_POST['jointure']))?array():$_GET['jointure']):$_POST['jointure']);
 $n=((isset($_POST['n']))?$_POST['n']:1);
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
.seed { background: url('../images/seeds.png') no-repeat right top; }
    </style>
    <script type="text/javascript">
    //<![CDATA[
 function chkFormulaire() {
  if(document.forms(0).data.value == "") {
   alert("<?php textdomain('seeddb'); print _("Search is empty!"); ?>");
   return false;
  };
 };
 function confirmation(){
  if (confirm('<?php print _("Remove this strain?");  textdomain('phplabdb');?>')){
   return true;
  } else {
   return false;
  };
 };
    //]]>
    </script>
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
            <?php print _("Search"); ?> 
          </h3>
          <p>
            <?php print _("Enter the name of the searched seed:"); ?> 
          </p>
          <form action="<?php print $base_url . $plugin; ?>admin/search.php" method="post" onsubmit="return chkFormulaire()">
            <table summary="">
<?php
  if (isset($_POST['add_x'])) $n++;
  if (isset($_POST['supr_x'])) {
   unset($type[$n]);
   $n--;
  };
  for($i=0; $i<=$n; $i++) { ?>
              <tr>
                <td>
<?php if ($i>0) { ?> 
                  <select name="jointure[<?php print $i ?>]">
                    <option value="AND"<?php print (($jointure[$i]=='AND')?' selected="selected"':'')?>>
                      <?php print _("AND"); ?>
                    </option>
                    <option value="OR"<?php print (($jointure[$i]=='OR')?' selected="selected"':'')?>>
                      <?php print _("OR"); ?>
                    </option>
                    <option value="NOT"<?php print (($jointure[$i]=='NOT')?' selected="selected"':'')?>>
                      <?php print _("NOT"); ?>
                    </option>
                  </select>
<?php }; ?> 
                </td>
                <td>
                  <select name="type[<?php print $i ?>]">
                    <option value="a.ref"<?php print (($type[$i]=='a.ref')?' selected="selected"':'')?>>
                      <?php print _("Reference"); ?> 
                    </option>
                    <option value="b.vernacular"<?php print (($type[$i]=='b.vernacular')?' selected="selected"':'')?>>
                      <?php print _("Vernacular name"); ?> 
                    </option>
                    <option value="b.country"<?php print (($type[$i]=='b.country')?' selected="selected"':'')?>>
                      <?php print _("Country"); ?> 
                    </option>
                    <option value="a.species"<?php print (($type[$i]=='a.species')?' selected="selected"':'')?>>
                      <?php print _("Species"); ?> 
                    </option>
                    <option value="a.crosstype"<?php print (($type[$i]=='a.crosstype')?' selected="selected"':'')?>>
                      <?php print _("Cross type"); ?> 
                    </option>
                    <option value="a.father"<?php print (($type[$i]=='a.father')?' selected="selected"':'')?>>
                      <?php print _("Father"); ?> 
                    </option>
                    <option value="a.mother"<?php print (($type[$i]=='a.mother')?' selected="selected"':'')?>>
                      <?php print _("Mother"); ?> 
                    </option>
                    <option value="b.prospection"<?php print (($type[$i]=='b.prospection')?' selected="selected"':'')?>>
                      <?php print _("Prospection ref"); ?>
                    </option>
                    <option value="b.locality"<?php print (($type[$i]=='b.locality')?' selected="selected"':'')?>>
                      <?php print _("Locality"); ?>
                    </option>
                    <option value="b.ethnos"<?php print (($type[$i]=='b.ethnos')?' selected="selected"':'')?>>
                      <?php print _("Ethnos group"); ?>
                    </option>
                    <option value="b.nature"<?php print (($type[$i]=='b.nature ')?' selected="selected"':'')?>>
                      <?php print _("Landscape nature"); ?>
                    </option>
                    <option value="b.form"<?php print (($type[$i]=='b.form')?' selected="selected"':'')?>>
                      <?php print _("Botanical form"); ?>
                    </option>
                    <option value="b.size"<?php print (($type[$i]=='b.size')?' selected="selected"':'')?>>
                      <?php print _("Population size"); ?>
                    </option>
                    <option value="b.distribution"<?php print (($type[$i]=='b.distribution')?' selected="selected"':'')?>>
                      <?php print _("Plant distribution"); ?>
                    </option>
                    <option value="b.weather"<?php print (($type[$i]=='b.weather')?' selected="selected"':'')?>>
                      <?php print _("Weather/Culture type"); ?>
                    </option>
                    <option value="b.precocity"<?php print (($type[$i]=='b.precocity')?' selected="selected"':'')?>>
                      <?php print _("Plant precocity"); ?>
                    </option>
                    <option value="a.note"<?php print (($type[$i]=='a.note')?' selected="selected"':'')?>>
                      <?php print _("Notes"); ?>
                    </option>
                    <option value="a.barcode"<?php print (($type[$i]=='a.barcode')?' selected="selected"':'')?>>
                      <?php print _("ID"); ?>
                    </option>
                  </select>
                </td>
                <td>
                  <input type="text" size="20" maxlength="100" name="data[<?php print $i ?>]" value="<?php print $data[$i] ?>">
                </td>
                <td><?php if($i==$n) print '                  <small><input type="image" src="../images/plus.png" alt="' . _("add") . '" name="add">&nbsp;' . _("add") . (($n>0)?'&nbsp;&nbsp;<input type="image" src="../images/minus.png" alt="' . _("remove") . '" name="supr">&nbsp;' . _("remove"):'') . "</small>\n"; ?>
                </td>
              </tr>
<?php }; ?>
              <tr>
                <td colspan="2">
                </td>
                <td>
                  <input type="hidden" name="n" value="<?php print $n ?>"><input type="submit" name="submit" value="<?php print _("Search"); ?>">
                </td>
                <td>
                </td>
              </tr>
            </table>
          </form>
          <h3>
            <?php print _("Result"); ?> 
          </h3>
<?php
  if (!(empty($data))) {
   $dbconn=sql_connect($plugin_db['seeddb']);
   $cross=read_table($dbconn,'crosstype',$lang);
   $species=species($dbconn);
   foreach($type as $key => $value) {
    if (!(empty($data[$key]))) {
     unset($simil);
     $joint=((isset($jointure[$key]))?($jointure[$key] . ' '):'');
     if (substr($value, 0, 1)=='b') $b=', prospection as b';
     if (in_array($value, array('a.crosstype','b.nature','b.form','b.size','b.distribution','b.weather','b.precocity'))) {      //natural to numeric language...
     foreach (read_table($dbconn,substr($value,2),$lang) as $rkey => $rvalue) {
      similar_text(strtolower(stripslashes(trim($data[$key]))),strtolower($rvalue),$simil[$rkey]);
     };
     arsort($simil);
     $resquest[]=$joint .  $value . '=' . key($simil);
     } elseif ($value=='a.species') {
     foreach ($species as $skey => $svalue) {
      similar_text(strtolower(stripslashes(trim($data[$key]))),strtolower($svalue),$simil[$skey]);
     };
     arsort($simil);
     $resquest[] = $joint . "a.species=" . key($simil);
     } else {;
     $resquest[] = $joint . $value . sql_reg(addslashes(htmlentities($data[$key])));
    };
   };
  };
  $form=read_table($dbconn,'form',$lang);
  if (isset($resquest)) {
   $result=sql_query('SELECT a.barcode, a.ref, a.crosstype, a.stock, a.fbarcode' . ((isset($b))?', b.prospection, b.country, b.form':'') . ' FROM seeds as a' . ((isset($b))?$b:''). ' WHERE (' . ((isset($b))?'a.barcode=b.barcode AND ':'') . implode(' ',$resquest) . ") ORDER BY a.date;",$dbconn);
   if(!(strlen($r=sql_last_error($dbconn)))) {
    $nombre_membre = sql_num_rows($result);
    if ( $nombre_membre != 0) {
?>
          <p>
            <?php print (($nombre_membre>1)? _("Seeds found:"): _("Seed found:")) . ' <strong>' . $nombre_membre; ?></strong>
          </p>
          <table id="list-database" width="100%" summary="">
            <tr>
              <th>
                <?php print _("Reference"); ?> 
              </th>
              <th>
                <?php print _("Description"); ?> 
              </th>
              <th>
                &nbsp;
              </th>
            </tr>
<?php
     while( $row = sql_fetch_row($result) ) {
      if (isset($b)) {
       $desc='<strong>' . _("Prospection") . "</strong>: $row[5], $row[6], " . $form[intval($row[7])];
      } else {
       $desc=$cross[intval($row[2])];
       if(!(isset($row[2]))) {
        $result2=sql_query("SELECT prospection, country, form FROM prospection WHERE barcode=$row[0];",$dbconn);
        error_reporting($lev);
        if(!(strlen ($r=sql_last_error($dbconn))) && (sql_num_rows($result2)==1)) {
         $row2 = sql_fetch_row($result2);
         $desc='<strong>' . _("Prospection") . "</strong>: $row2[0], $row2[1], " . $form[intval($row2[2])];
        };
       };
      };
      print "            <tr class=\"" . (($i++%2 ==1) ? 'odd' : 'even' ) . "\">\n              <td class=\"column-1\">\n                &nbsp;$row[1]&nbsp;\n              </td>\n              <td class=\"column-2\">\n                &nbsp;$desc&nbsp;\n              </td>\n              <td class=\"column-3\">\n                &nbsp;<a href=\"modif.php?barcode=" . rawurlencode($row[0]) . (((!(isset($row[2])))||(isset($b)))?'&amp;prospection=1':'' ) . '">' . _("Modify") . '</a>&nbsp;&nbsp;|&nbsp;&nbsp;<a href="modif.php?barcode=' . rawurlencode($row[0]) . (((!(isset($row[2])))||(isset($b)))?'&amp;prospection=1':'' ) . '&amp;remove=1" onclick="return confirmation();">' . _("Remove") . "</a>&nbsp;\n              </td>\n            </tr>\n";
     };
     print "          </table>\n";
    } else {
     print "          <p>\n            <em>" . _("No result") . "</em>\n          </p>\n";
    };
   } else {
    print "          <p>\n            <em" . _("No result") . "</em>\n          </p>\n";
   };
  } else {
   print "          <p>\n            <em>" . _("No result") . "</em>\n          </p>\n";
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
