<?php
 ob_start("ob_gzhandler");
 session_start();
 include_once '../../includes/login.inc';
 if (!isset($_SESSION['status'])) {
  header('Location: ' . $base_url);
  exit;
 };
 $lev=error_reporting(8);
 $status=$_SESSION['status'];
 $type=stripslashes(rawurldecode(((empty($_POST['type']))?$_GET['type']:$_POST['type'])));
 $data=stripslashes(rawurldecode(((empty($_POST['data']))?$_GET['data']:$_POST['data'])));
 header_start();
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
    <script type="text/javascript">
    //<![CDATA[
 function chkFormulaire() {
  if(document.forms(0).data.value == "") {
   alert("<?php textdomain('plasmiddb'); print _("Search is empty!"); ?>");
   return false;
  };
 };
 function confirmation_plasmid(){
  if (confirm('<?php print _("Remove this plasmid?"); ?>')){
   return true;
  } else {
   return false;
  };
 };
 function confirmation_proto(){
  if (confirm('<?php print _("Remove this prototype and all associted plasmids?"); textdomain('phplabdb'); ?>')){
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
            <?php print _("Search"); ?> 
          </h3>
          <p>
            <?php print _("Enter the name of the searched plasmid, or a part of its sequence:"); ?> 
          </p>
          <form action="<?php print $base_url . $plugin; ?>admin/search.php" method="post" onsubmit="return chkFormulaire()">
            <table summary="">
              <tr>
                <td>
                  <select name="type">
                    <option value="name"<?php print (($type=='name')?' selected="selected"':'')?>>
                      <?php print _("Name"); ?> 
                    </option>
                    <option value="alias"<?php print (($type=='alias')?' selected="selected"':'')?>>
                      <?php print _("Alias"); ?> 
                    </option>
                    <option value="seq"<?php print (($type=='seq')?' selected="selected"':'')?>>
                      <?php print _("Sequence"); ?> 
                    </option>
                    <option value="box"<?php print (($type=='box')?' selected="selected"':'')?>>
                      <?php print _("Box"); ?> 
                    </option>
                    <option value="conditioning"<?php print (($type=='conditioning')?' selected="selected"':'')?>>
                      <?php print _("Conditioning"); ?> 
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
   $dbconn=sql_connect($plugin_db['plasmiddb']);
   if ($type=='name' || $type=='notes') {
    $string_search="SELECT name,length,plasmid FROM prototype WHERE $type" . sql_reg(addslashes($data)) . ' ORDER BY name;';
    $visual=true;    
   } elseif ($type=='seq' || $type=='alias') {
    $string_search="SELECT a.name,a.length,a.plasmid FROM prototype as a, $type as b WHERE (a.name=b.name AND b.$type" . sql_reg(addslashes($data)) . ') ORDER BY a.name;';
    $visual=true;
   } else {
    $string_search="SELECT name,box,rank,conditioning,preparation,barcode FROM plasmid WHERE $type" . (($type=='box')?('='. addslashes($data)):(sql_reg(addslashes($data)))) . ' ORDER BY ' . (($type=='box')?('rank'):($type)) . ';';
   };
   $result=sql_query($string_search,$dbconn);
   if(!(strlen($r=sql_last_error($dbconn)))) {
    $nombre_membre=sql_num_rows($result);
    if ($nombre_membre!=0) {
		 $i=0;
?>
          <p>
            <?php print (($nombre_membre>1)? _("Plasmids found:"):_("Plasmid found:")) . ' <strong>' . $nombre_membre . '</strong>'; ?> 
          </p>
<?php if (isset($visual)) { ?>
          <table id="list-database" width="100%" summary="">
            <tr>
              <th>
                <?php print _("Name"); ?> 
              </th>
              <th>
                <?php print _("Length"); ?> 
              </th>
              <th>
                <?php print _("Ready?"); ?> 
              </th>
              <th>
                <?php print _("Action"); ?> 
              </th>
            </tr>
<?php 
      while($row=sql_fetch_row($result)) {
       print "            <tr class=\"" . (($i++%2 ==1) ? 'odd' : 'even' ) . "\">\n              <td class=\"column-1\">\n                &nbsp;$row[0]&nbsp;\n              </td>\n              <td class=\"column-2\">\n                &nbsp;" . ((intval($row[1])>1)?$row[1]:'-') . ' ' . _("bp") . "&nbsp;\n              </td>\n              <td class=\"column-3\">\n                &nbsp;<img src=\"../images/" . (($row[2]=='t')?'yes':'no') . ".png\" alt=\"\" />&nbsp;\n              </td>\n              <td class=\"column-3\">\n                &nbsp;<a href=\"modif.php?plasmid=" . rawurlencode($row[0]) . '">' . _("Modify") . '</a>&nbsp;&nbsp;|&nbsp;&nbsp;<a href="modif.php?plasmid=' . rawurlencode($row[0]) . '&amp;remove=1" onclick="return confirmation_proto();">' . _("Remove") . "</a>&nbsp;\n              </td>\n            </tr>\n";
      };
?>
          </table>
<?php } else {?>
          <table id="list-database" width="100%" summary="">
            <tr>
              <th>
                <?php print _("Name"); ?> 
              </th>
              <th>
                <?php print _("Conditioning"); ?> 
              </th>
              <th>
                <?php print _("Preparation"); ?> 
              </th>
              <th>
                <?php print _("Box"); ?> 
              </th>
              <th>
                <?php print _("Action"); ?> 
              </th>
            </tr>
<?php 
      while($row=sql_fetch_row($result)) {
       print "            <tr class=\"" . (($i%2 ==1) ? 'odd' : 'even' ) . "\">\n              <td class=\"column-1\">\n                &nbsp;$row[0]&nbsp;\n              </td>\n              <td class=\"column-2\">\n                &nbsp;$row[3]&nbsp;\n              </td>\n              <td class=\"column-2\">\n                &nbsp;$row[4]&nbsp;\n              </td>\n              <td class=\"column-3\">\n                &nbsp;" . $row[1] . (($row[2]==0) ? '' : ('&nbsp;(' . $row[2] . ')')) . "&nbsp;\n              </td>\n              <td class=\"column-3\">\n                &nbsp;<a href=\"modif.php?barcode=" . rawurlencode($row[5]) . '">' . _("Modify") . '</a>&nbsp;&nbsp;|&nbsp;&nbsp;<a href="modif.php?barcode=' . rawurlencode($row[5]) . '&amp;remove=1" onclick="return confirmation_plasmid();">' . _("Remove") . "</a>&nbsp;\n              </td>\n            </tr>\n\n";
      };
?>
          </table>
<?php
     };
    } else {
     print "          <p>\n            <em>" . _("No result") . "</em>\n          </p>\n";
    };
   } else {
    print "          <p>\n            <em>" . _("No result") . "</em>\n          </p>\n";
   };
 } else {
  print "          <p>\n            <em>" . _("No result") . "</em>\n          </p>\n";
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
