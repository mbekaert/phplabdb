<?php
 ob_start("ob_gzhandler");
 session_start();
 include_once '../includes/login.inc';
 if (!isset($_SESSION['status'])) {
  header('Location: ' . $base_url);
  exit;
 };
 $lev=error_reporting(8);
 function species($dbconn) {
  $species=array();
  $result=sql_query("SELECT id, name FROM species ORDER BY id;",$dbconn);
  if(!(strlen($r=sql_last_error($dbconn)))) {
   while($row=sql_fetch_row($result)) {
    $species[$row[0]]=htmlentities($row[1]);
   };
  };
  return $species;
 };
 $status=$_SESSION['status'];
 $type=stripslashes(rawurldecode(((empty($_POST['type']))?((empty($_GET['type']))?'':$_GET['type']):$_POST['type'])));
 $data=stripslashes(rawurldecode(((empty($_POST['data']))?((empty($_GET['data']))?'':$_GET['data']):$_POST['data'])));
 header_start();
 if (!(empty($data)) && ($type=='released')) { 
  if (($datestamp=strtotime($data))==-1) {
   $data=date("Y-m-d");
  } else {
   $data=date("Y-m-d",$datestamp);
  };
 };
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
    <script type="text/javascript">
    //<![CDATA[
function chkFormulaire() {
 if(document.forms(0).data.value == "") {
  alert("<?php textdomain('straindb'); print _("Search is empty!"); textdomain('phplabdb'); ?>");
  return false;
 };
};
    //]]>
    </script>
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
            <?php print _("Search"); ?> 
          </h3>
          <p>
            <?php print _("Enter the name of the searched strain:"); ?> 
          </p>
          <form action="<?php echo $base_url . $plugin; ?>search.php" method="post" onsubmit="return chkFormulaire()">
            <table summary="">
              <tr>
                <td>
                  <select name="type">
                    <option value="name"<?php print (($type=='name')?' selected="selected"':'')?>>
                      <?php print _("Name"); ?> 
                    </option>
                    <option value="species"<?php print (($type=='species')?' selected="selected"':'')?>>
                      <?php print _("Species"); ?> 
                    </option>
                    <option value="box"<?php print (($type=='box')?' selected="selected"':'')?>>
                      <?php print _("Box"); ?>
                    </option>
                    <option value="strain_origin"<?php print (($type=='strain_origine')?' selected="selected"':'')?>>
                      <?php print _("Strain origin"); ?> 
                    </option>
                    <option value="plasmid"<?php print (($type=='plasmid')?' selected="selected"':'')?>>
                      <?php print _("Plasmid"); ?> 
                    </option>
                    <option value="released"<?php print (($type=='released')?' selected="selected"':'')?>>
                      <?php print _("Date"); ?> 
                    </option>
                    <option value="notes"<?php print (($type=='notes')?' selected="selected"':'')?>>
                      <?php print _("Notes"); ?> 
                    </option>
                    <option value="barcode"<?php print (($type=='barcode')?' selected="selected"':'')?>>
                      <?php print _("ID"); ?> 
                    </option>
                  </select>
                </td>
                <td>
                  <input type="text" size="20" maxlength="100" name="data" value="<?php print $data; ?>">
                </td>
              </tr>
              <tr>
                <td>
                </td>
                <td>
                  <input type="submit" name="submit" value="<?php print _("Search"); ?>">
                </td>
              </tr>
            </table>
          </form>
          <h3>
            <?php print _("Result"); ?> 
          </h3>
<?php
  if (!(empty($data))) {
   $dbconn=sql_connect($plugin_db['straindb']);
   $species=species($dbconn);
   if ($type=='species') {
    foreach ($species as $key => $value) {
     similar_text(strtolower(stripslashes(trim($data))),strtolower($value),$simil[$key]);
    };
    arsort($simil);
    $data=key($simil);
   };
   $result=sql_query("SELECT name, barcode, released, box, rank, ploidy, species FROM strain WHERE $type" . (($type=='box')?("='". addslashes(htmlentities($data)) ."'"):(sql_reg(addslashes(htmlentities($data))))) . " ORDER BY " . (($type=='box')?('rank'):($type)) . ';',$dbconn);
   if(!(strlen($r=sql_last_error($dbconn)))) {
    $nombre_membre=sql_num_rows($result);
    if ($nombre_membre!=0) {
		 $i=0;
?>
          <p>
            <?php print (($nombre_membre>1)? _("Strains found:"): _("Strain found:")) . ' <strong>' . $nombre_membre . '</strong>'; ?>
          </p>
          <table id="list-database" width="100%" summary="">
            <tr>
              <th>
                <?php print _("Name"); ?> 
              </th>
              <th>
                <?php print _("Species"); ?>
              </th>
              <th>
                <?php print _("Ploidy"); ?>
              </th>
              <th>
                <?php print _("Date"); ?> 
              </th>
              <th>
                <?php print _("Box"); ?> 
              </th>
            </tr>
<?php
     while($row=sql_fetch_row($result)) {
      print "            <tr class=\"" . (($i++%2 ==1) ? 'odd' : 'even' ) . "\">\n              <td class=\"column-1\">\n                &nbsp;<a href=\"" . $base_url . $plugin . 'details.php?barcode='. rawurlencode($row[1]) . '" title="' . _("details") . '">' . $row[0] . "</a>&nbsp;\n              </td>\n              <td class=\"column-2\">\n                &nbsp;<em>" . $species[$row[6]] . "</em>&nbsp;\n              </td>\n              <td class=\"column-2\">\n                &nbsp;<small>$row[5]</small>&nbsp;\n              </td>\n              <td class=\"column-3\">\n                &nbsp;" . date(_("m/d/Y"),strtotime($row[2])) . "&nbsp;\n              </td>\n              <td class=\"column-3\">\n                &nbsp;" . $row[3] . (($row[4]==0) ? '' : ('&nbsp;(' . $row[4] . ')')) . "&nbsp;\n              </td>\n            </tr>\n";
     };
     print "          </table>\n";
    } else {
     print "          <p>\n            <em>" . _("No result") . "</em>\n          </p>\n";
    };
   } else {
   print "          <p>\n            <em>" . _("No result") . "</em>\n          </p>\n";
//   print "          <p>\n            <a href=\"" . $base_url . $plugin . "\"><img src=\"" . $base_url .  "images/oops.png\" alt=\"\"></a>&nbsp;<strong>Oops</strong>\n          </p>\n          <p>\n            $r\n          </p>\n";
  };
 } else {
  print "          <p>\n            <em>" . _("No result") . "</em>\n          </p>\n";
 };
 textdomain('phplabdb');
?>
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

