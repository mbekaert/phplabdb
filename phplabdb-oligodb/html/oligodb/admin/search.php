<?php
 ob_start("ob_gzhandler");
 session_start();
 include_once '../../includes/login.inc';
 if (!($_SESSION['status'] & pow(2,$plugin_level['oligodb']))) {
  header('Location: ' . $base_url);
  exit;
 };
 $lev=error_reporting(8);
 $status=$_SESSION['status'];
 $type=stripslashes(rawurldecode($_POST['type']));
 $data=stripslashes(rawurldecode($_POST['data']));
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
.oligo { background: url('../images/oligo.png') no-repeat right top; }
    </style>
    <script type="text/javascript">
    //<![CDATA[
function chkFormulaire() {
 if(document.forms(0).data.value == "") {
  alert("<?php textdomain('oligodb'); print _("Search is empty!"); textdomain('phplabdb'); ?>");
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
            <?php print _("Search"); ?> 
          </h3>
          <p>
            <?php print _("Enter the name of the searched oligonucleotide, or a part of its sequence:"); ?> 
          </p>
          <form action="<?php print $base_url . $plugin; ?>admin/search.php" method="post" onsubmit="return chkFormulaire()">
            <table summary="">
              <tr>
                <td>
                  <select name="type">
                    <option value="name"<?php print (($type=='name')?' selected="selected"':'')?>>
                      <?php print _("Name"); ?>
                    </option>
                    <option value="oligo"<?php print (($type=='oligo')?' selected="selected"':'')?>>
                      <?php print _("Sequence"); ?>
                    </option>
                    <option value="synthesis"<?php print (($type=='synthesis')?' selected="selected"':'')?>>
                      <?php print _("Date of synthesis"); ?>
                    </option>
                    <option value="box"<?php print (($type=='box')?' selected="selected"':'')?>>
                      <?php print _("Box"); ?>
                    </option>
                    <option value="freezer"<?php print (($type=='freezer')?' selected="selected"':'')?>>
                      <?php print _("Freezer"); ?> 
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
                  <input type="text" size="20" maxlength="100" name="data" value="<?php print $data ?>">
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
  if ($type=='synthesis') $data=date("Y-m-d",strtotime($data));
  $dbconn=sql_connect($plugin_db['oligodb']);
  $result=sql_query("SELECT name, oligo, synthesis, box, rank, freezer, barcode FROM oligo WHERE $type" . (($type=='box')?("='". addslashes(htmlentities($data)) ."'"):(sql_reg(addslashes(htmlentities($data))))) . " ORDER BY " . (($type=='box')?('rank'):($type)) . ';',$dbconn);
  if(!(strlen($r=sql_last_error($dbconn)))) {
   $nombre_membre=sql_num_rows($result);
   if ($nombre_membre!=0) {
    $i=0;
?>
          <p>
            <?php print (($nombre_membre>1)? _("Oligonucleotides found:"): _("Oligonucleotide found:")) . ' <strong>' . $nombre_membre . '</strong>'; ?>
          </p>
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
            </tr>
<?php
    while($row=sql_fetch_row($result)) {
     print "            <tr class=\"" . (($i%2 ==1) ? 'odd' : 'even' ) . "\">\n              <td class=\"column-1\">\n                &nbsp;<a href=\"" . $base_url . $plugin . 'admin/modif.php?barcode='. rawurlencode($row[6]) . '" title="' . _("Modify") . '">' . $row[0] . "</a>&nbsp;\n              </td>\n              <td class=\"column-2\">\n                &nbsp;<small>" . wordwrap(((strlen($row[1])>39)?(substr($row[1],0,39).'...'):($row[1])),3,' ',1) . "</small>&nbsp;\n              </td>\n              <td class=\"column-3\">\n                &nbsp;" . date(_("m/d/Y"),strtotime($row[2])) . "&nbsp;\n              </td>\n              <td class=\"column-3\">\n                &nbsp;" . ((isset($row[5])) ? ('<strong>' . $row[5] . '</strong> - '):'') . $row[3] . (($row[4]==0) ? '' : ('&nbsp;(' . $row[4] . ')')) . "&nbsp;\n              </td>\n            </tr>\n";
     $result2=sql_query("SELECT name1, name2 FROM couple WHERE (name1='" . addslashes($row[0]) . "') OR (name2='" . addslashes($row[0]) . "');",$dbconn);
     if(!(strlen($r=sql_last_error($dbconn)))) {
      $nombre_membre2=sql_num_rows($result2);
      if ($nombre_membre2!=0) {
       print "            <tr class=\"" . (($i%2 ==1) ? 'odd' : 'even' ) . "\">\n              <td class=\"column-1\">\n              </td>\n              <td colspan=\"3\">\n                &nbsp;";      
       while($row2=sql_fetch_row($result2) ){
        print '<a href="' . $base_url . $plugin  . 'admin/modif_couple.php?name[0]=' . rawurlencode(addslashes($row2[0])) . '&name[1]=' . rawurlencode(addslashes($row2[1])) . '" title="' . _("Modify") . '">' . (($row2[0]==$row[0])?$row2[1]:$row2[0]) . '</a> ';
       };
       print "&nbsp;\n              </td>\n            </tr>\n";
      };
      $i++;
     };
    };
     print "          </table>\n";
    } else {
     print "          <p>\n            <em>" . _("No result") . "</em>\n          </p>\n";
    };
   } else {
    print "          <p>\n            <em>" . _("No result") . "</em>\n          </p>\n";
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
