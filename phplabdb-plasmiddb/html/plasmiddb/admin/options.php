<?php
 ob_start("ob_gzhandler");
 session_start();
 include_once '../../includes/login.inc';
 if (!($_SESSION['status'] & pow(2,$plugin_level['plasmiddb']))) {
  header('Location: ' . $base_url);
  exit;
 };
 $lev=error_reporting(8);
 $status=$_SESSION['status'];
 $table=((empty($_POST['table']))?rawurldecode($_GET['table']):$_POST['table']);
 if (!(in_array($table, array('selection','replication','conditioning','preparation')))) {
  header('Location: ' . $base_url . 'logout.php');
  exit;
 };
 $language=((empty($_POST['lang']))?((empty($_GET['lang']))?'':$_GET['lang']):(($_POST['lang']=='new')?trim(stripslashes($_POST['langID'])):trim(stripslashes($_POST['lang']))));
 if (isset($_GET['remove'])) $remove=true;
 if (isset($_GET['key'])) $key_table=rawurldecode($_GET['key']);
 if (isset($_POST['key'])) $key_data=$_POST['key'];
 if (isset($_POST['data'])) $data=stripslashes(trim($_POST['data']));
 if (isset($_POST['id']))  $id=(($_POST['id']=='new')?trim(stripslashes($_POST['idID'])):trim(stripslashes($_POST['id'])));
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
    <script type="text/javascript">
    //<![CDATA[
 function confirmation(){
  if (confirm('<?php textdomain('plasmiddb'); print _("Remove this entry?"); textdomain('phplabdb'); ?>')){
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
            <?php print _("Option for") . " '<em>" . $table . "</em>'"; ?> 
          </h3>
<?php
  if (isset($remove)) {
   $result=sql_query("DELETE FROM $table WHERE (" . ((($table=='conditioning') || ($table=='preparation'))?"id=$key_table":"organism='$key_table'") . " AND lang='$language');",$dbconn);
   if(strlen ($r=sql_last_error($dbconn))) {
    print "         <p>\n           <a href=\"" . $base_url . $plugin . "admin/\"><img src=\"" . $base_url . "images/oops.png\" alt=\"\"></a>&nbsp;<strong>Oops</strong>\n         </p>\n         <p>\n           $r\n         </p>\n";
    } else {
    print "        <p>\n          <a href=\"" . $base_url . $plugin . "admin/\"><img src=\"" . $base_url . 'images/ok.png" alt=""></a>&nbsp;<strong>' . _("Entry deleted") . "</strong>\n        </p>\n";
   };
  } elseif(isset($key_table)) {
   switch($table) {
    case 'conditioning':
    case 'preparation':
       $result=sql_query("SELECT id, legend, lang FROM $table WHERE (id=$key_table AND lang='$language');",$dbconn);
       break;
    case 'selection':
    case 'replication':
       $result=sql_query("SELECT organism, legend, lang FROM $table WHERE (organism='$key_table' AND lang='$language');",$dbconn);
       break;
    };
   if(!(strlen($r=sql_last_error($dbconn)))) {
    $row = sql_fetch_row($result);
?>
          <form action="<?php print $base_url . $plugin; ?>admin/options.php" method="post">
            <table summary="">
              <tr>
                <td>
                  &nbsp;<?php print _("Reference"); ?>&nbsp;
                </td>
                <td>
                  <strong><?php  print $key_table; ?></strong><input type="hidden" name="key" value="<?php print $key_table; ?>">
                </td>
              </tr>
              <tr>
                <td>
                  <label for="data">&nbsp;<?php print _("Description"); ?>&nbsp;</label>
                </td>
                <td>
                  <input type="text" size="50" maxlength="50" name="data" id="data" value="<?php  print $row[1]; ?>">
                </td>
              </tr>
              <tr>
                <td>
                  &nbsp;<?php print _("Lang"); ?>&nbsp;
                </td>
                <td>
                  <strong><?php  print $language; ?></strong><input type="hidden" name="lang" value="<?php print $language; ?>">
                </td>
              </tr>
              <tr>
                <td>
                </td>
                <td>
                  <?php print '<input type="hidden" name="table" value="' . $table . '"><input type="submit" name="next" value="' . _("Update") . ' &gt;&gt;">'; ?> 
                </td>
              </tr>
            </table>
          </form>
<?php
    } else {
    print "         <p>\n           <a href=\"" . $base_url . $plugin . "admin/options.php?table=$table\"><img src=\"" . $base_url . "images/oops.png\" alt=\"\"></a>&nbsp;<strong>Oops</strong>\n         </p>\n         <p>\n           $r\n         </p>\n";
   };
  } elseif(isset($key_data)) {
   if (!(empty($data))) {
    if (($table=='selection') || ($table=='replication')) {
     $result=sql_query("UPDATE $table SET legend='" . addslashes($data). "' WHERE (organism='" . addslashes($key_data). "' AND lang='" . addslashes($language). "');",$dbconn);
     if(strlen ($r=sql_last_error($dbconn))) {
      print "         <p>\n           <a href=\"" . $base_url . $plugin . "admin/options.php?table=$table\"><img src=\"" . $base_url . "images/oops.png\" alt=\"\"></a>&nbsp;<strong>Oops</strong>\n         </p>\n         <p>\n           $r\n         </p>\n";
     } else {
      print "        <p>\n          <a href=\"" . $base_url . $plugin . "admin/\"><img src=\"" . $base_url . 'images/ok.png" alt=""></a>&nbsp;<strong>' . _("Entry updated") . "</strong>\n        </p>\n";
     };
    } else {
     $result=sql_query("UPDATE $table SET legend='" . addslashes($data). "' WHERE (id=$key_data AND lang='" . addslashes($language). "');",$dbconn);
     if(strlen ($r=sql_last_error($dbconn))) {
      print "         <p>\n           <a href=\"" . $base_url . $plugin . "admin/options.php?table=$table\"><img src=\"" . $base_url . "images/oops.png\" alt=\"\"></a>&nbsp;<strong>Oops</strong>\n         </p>\n         <p>\n           $r\n         </p>\n";
     } else {
      print "        <p>\n          <a href=\"" . $base_url . $plugin . "admin/\"><img src=\"" . $base_url . 'images/ok.png" alt=""></a>&nbsp;<strong>' . _("Entry added") . "</strong>\n        </p>\n";
     };
    };
   } else {
    print "        <p>\n          <a href=\"" . $base_url . $plugin . "admin/options.php?table=$table\"><img src=\"" . $base_url . 'images/no.png" alt=""></a>&nbsp;<strong>' . _("Invalid description") . "</strong>\n        </p>\n";
   };
  } elseif(isset($data) && isset($id)) {
   if (!(empty($data))) {
    if (($table=='selection') || ($table=='replication')) {
     $result=sql_query("INSERT INTO $table (organism,legend,lang) VALUES ('" . addslashes($id). "','" . addslashes($data). "','" . addslashes($language). "');",$dbconn);
     if(strlen ($r=sql_last_error($dbconn))) {
      print "         <p>\n           <a href=\"" . $base_url . $plugin . "admin/options.php?table=$table\"><img src=\"" . $base_url . "images/oops.png\" alt=\"\"></a>&nbsp;<strong>Oops</strong>\n         </p>\n         <p>\n           $r\n         </p>\n";
     } else {
      print "        <p>\n          <a href=\"" . $base_url . $plugin . "admin/\"><img src=\"" . $base_url . 'images/ok.png" alt=""></a>&nbsp;<strong>' . _("Entry added") . "</strong>\n        </p>\n";
     };
    } else {
     $result=sql_query("INSERT INTO $table (id,legend,lang) VALUES ($id,'" . addslashes($data). "','" . addslashes($language). "');",$dbconn);
     if(strlen ($r=sql_last_error($dbconn))) {
      print "         <p>\n           <a href=\"" . $base_url . $plugin . "admin/options.php\"><img src=\"" . $base_url . "images/oops.png\" alt=\"\"></a>&nbsp;<strong>Oops</strong>\n         </p>\n         <p>\n           $r\n         </p>\n";
      } else {
      print "        <p>\n          <a href=\"" . $base_url . $plugin . "admin/\"><img src=\"" . $base_url . 'images/ok.png" alt=""></a>&nbsp;<strong>' . _("Entry added") . "</strong>\n        </p>\n";
     };
    };
   } else {
    print "        <p>\n          <a href=\"" . $base_url . $plugin . "admin/options.php?table=$table\"><img src=\"" . $base_url . 'images/no.png" alt=""></a>&nbsp;<strong>' . _("Invalid description") . "</strong>\n        </p>\n";
   };
  } else {
?>
          <p>
            <?php print _("Existing options"); ?> 
          </p>
          <table id="list-database" width="100%" summary="">
<?php
   switch($table) {
    case 'conditioning':
    case 'preparation':
       $result=sql_query("SELECT id, legend, lang FROM $table ORDER BY id, lang;",$dbconn);
       break;
    case 'selection':
    case 'replication':
       $result=sql_query("SELECT organism, legend, lang FROM $table ORDER BY organism, lang;",$dbconn);
       break;
    };
    if(!(strlen($r=sql_last_error($dbconn)))) {
     while( $row = sql_fetch_row($result) ) {
      print "            <tr class=\"" . (($row[0]%2 ==1) ? 'odd' : 'even' ) . "\">\n              <td class=\"column-1\">\n                &nbsp;" . ((($table=='selection') || ($table=='replication'))?"<strong>$row[0]</strong> - ":'') . ((strlen($row[1])>50)?(substr($row[1],0,50).'...'):($row[1])) . "&nbsp;\n              </td>\n              <td class=\"column-2\">\n                &nbsp;$row[2]&nbsp;\n              </td>\n              <td class=\"column-3\">\n                &nbsp;<a href=\"options.php?table=$table&amp;key=$row[0]&amp;lang=$row[2]\">" . _("Modify") . "</a>&nbsp;&nbsp;|&nbsp;&nbsp;<a href=\"options.php?table=$table&amp;key=$row[0]&amp;lang=$row[2]&amp;remove=1\" onclick=\"return confirmation();\">" . _("Remove") . "</a>&nbsp;\n              </td>\n            </tr>\n";
      $lang_table[]=$row[2];
      $id[]=$row[0];
     };
    };
?>
          </table>
          <h3>
            <?php print _("New"); ?>
          </h3>
          <form action="<?php print $base_url . $plugin; ?>admin/options.php" method="post">
            <table summary="">
              <tr>
                <td>
                  <label for="id">&nbsp;<?php print _("ID"); ?>&nbsp;</label>
                </td>
                <td>
                  <select name="id" id="id"<?php print ((($table=='selection') || ($table=='replication'))?" onclick=\"if (this.value=='new') {document.forms[0].idID.style.display='';} else {document.forms[0].idID.style.display='none';}\"":''); ?>><?php 
    foreach(array_unique($id) as $value)
     print '<option value="' . rawurlencode($value) . "\">$value</option>";
    rsort($id); reset($id); 
    if (($table=='selection') || ($table=='replication')) {
     print '<option value="new" selected="selected">' . _("New") . '</option>';
    } else {
     print '<option value="' . ($id[0]+1) . '" selected="selected">' . ($id[0]+1) .' - ' . _("New") . '</option>';
    };
?></select><?php  print ((($table=='selection') || ($table=='replication'))?'&nbsp;&nbsp;<input type="text" name="idID" size="15" id="idID" style="display:none;">':''); ?> 
                </td>
              </tr>
              <tr>
                <td>
                  <label for="data">&nbsp;<?php print _("Description"); ?>&nbsp;</label>
                </td>
                <td>
                  <input type="text" size="50" maxlength="50" name="data" id="data">
                </td>
              </tr>
              <tr>
                <td>
                  <label for="lang">&nbsp;<?php print _("Lang"); ?>&nbsp;</label>
                </td>
                <td>
                  <select name="lang" id="lang" onclick="if (this.value=='new') {document.forms[0].langID.style.display='';} else {document.forms[0].langID.style.display='none';}"><?php  foreach(array_unique($lang_table) as $value) print "<option value=\"$value\"" . (($value==$lang)?' selected="selected"':''). ">$value</option>"; ?><option value="new"><?php print _("New"); ?></option></select>&nbsp;&nbsp;<input type="text" name="langID" size="15" id="langID" style="display:none;">
                </td>
              </tr>
              <tr>
                <td>
                </td>
                <td>
                  <?php print '<input type="hidden" name="table" value="' . $table . '"><input type="submit" name="next" value="' . _("Add") . ' &gt;&gt;">'; ?> 
                </td>
              </tr>
            </table>
          </form>
<?php 
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
