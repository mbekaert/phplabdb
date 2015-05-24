<?php
 session_start();
 include_once '../includes/login.inc';
 if (!($_SESSION['status'] & pow(2,30))) {
  header('Location: ' . $base_url);
  exit;
 };
 if(isset($_GET['action']) && isset($_GET['db'])) {
  if ($_GET['action']=='download') {
   $mime_type=((preg_match("/MSIE/", $_SERVER['HTTP_USER_AGENT']) || preg_match("/OPERA/", $_SERVER['HTTP_USER_AGENT']))?'application/octetstream':'application/octet-stream');
   if ($sqlserver=='postgresql') {
    $handle=popen("$sqldump -c -d " . $_GET['db'] . ' -Fc -Z9',"r"); //compress
    $ext='db';
   } elseif ($sqlserver=='mysql') {
    $handle=popen("$sqldump -a -l -F -C -u $sqllogin -p$sqlpassword -h $sqlhost --add-drop-table --add-locks -B " . $_GET['db'],"r");
    if (isset($_GET['z']) && @function_exists('bzcompress')) {
     $ext='bz2';
     $mime_type='application/x-bzip';
    } else {
     $ext='sql';
    };
   } else {
    mes_error(_("SQL server type '$sqlserver' unknown on this server"));
   };
   while ($s=fread($handle,1024)) {
    $buffers .= $s;
   };
   pclose($handle);
   header('Content-Type: ' . $mime_type);
   if (preg_match("/MSIE/", $_SERVER['HTTP_USER_AGENT'])) {
    header('Content-Disposition: inline; filename="' . $_GET['db'] . '.' . $ext . '"');
    header('Expires: 0');
    header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
    header('Pragma: public');
   } else {
    header('Content-Disposition: attachment; filename="' . $_GET['db'] . '.' . $ext . '"');
    header('Expires: 0');
    header('Pragma: no-cache');
   }
    if (isset($_GET['z']) && @function_exists('bzcompress')) {
    print bzcompress($buffers);
   } else {
    print $buffers;
   };
   exit;
  };
 };
 ob_start("ob_gzhandler");
 $status=$_SESSION['status'];
 header_start();
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.01//EN">
<html>
  <head>
    <title>
      ..:: phpLabDB ::..
    </title>
    <meta http-equiv="Content-Type" content="text/html">
    <link rel="stylesheet" type="text/css" media="print" href="<?php print $base_url; ?>css/print.css">
    <link rel="stylesheet" type="text/css" media="screen" href="<?php print $base_url; ?>css/screen.css">
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
<?php if (isset($plugin_name)) {?>
        <li>
          <?php print '<a href="' . $base_url . 'database.php">' . _("Databases") . '</a>'; ?> 
        </li>
<?php };
  if (isset($mods_name)) {
   foreach($mods_title as $key => $value) {
    print "        <li>\n          <a href=\"" . $base_url . $mods_dir[$key] . "\">$value</a>\n        </li>\n";
   };
  };
?>
        <li>
          <?php print '<strong><a href="' . $base_url . 'admin/">' . _("Administration") . '</strong></a>'; ?> 
          <ul>
            <li>
              <?php print '<a href="' . $base_url . 'admin/config.php">' . _("System config") . '</a>'; ?> 
            </li>
            <li>
              <?php print '<a href="' . $base_url . 'admin/users.php">' . _("Users") . '</a>'; ?> 
            </li>
            <li>
              <?php print '<strong><a href="' . $base_url . 'admin/db.php">' . _("Databases") . '</a></strong>'; ?> 
            </li>
            <li>
              <?php print '<a href="' . $base_url . 'admin/plugin.php">' . _("Plug-ins") . '</a>'; ?> 
            </li>
            <li>
              <?php print '<a href="' . $base_url . 'admin/mods.php">' . _("Mods") . '</a>'; ?> 
            </li>
          </ul>
        </li>
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
      <div id="content">
        <div id="page-main">
         <h1>
           <?php print _("Database Management"); ?> 
         </h1>
         <table id="list-database" width="100%" summary="">
           <tr>
             <th>
               <?php print _("Name"); ?> 
             </th>
             <th>
               <?php print _("Database"); ?> 
             </th>
             <th>
               <?php print _("Action"); ?> 
             </th>
           </tr>
           <tr class="odd">
             <td class="column-1">
               phpLabDB
             </td>
             <td class="column-2">
               db_phplabdb
             </td>
             <td class="column-3">
               <a href="db.php?action=download&amp;db=db_phplabdb"><?php print _("Download"); ?></a>&nbsp;&nbsp;|&nbsp;&nbsp;<a href="db_upload.php?action=upload&amp;db=db_phplabdb"><?php print _("Upload"); ?></a>
             </td>
           </tr>
<?php
 if (isset($plugin_db)) {
  $i=0;
  foreach($plugin_db as $key => $value) {
   print "           <tr class=\"" . (($i++%2==1)?'odd':'even') . "\">\n             <td class=\"column-1\">\n               " . $plugin_name[$key] . "\n             </td>\n             <td class=\"column-2\">\n               $value\n             </td>\n             <td class=\"column-3\">\n               <a href=\"db.php?action=download&amp;db=$value\">" . _("Download") . "</a>&nbsp;&nbsp;|&nbsp;&nbsp;<a href=\"db_upload.php?action=upload&amp;db=$value\">" . _("Upload") . "</a>\n             </td>\n           </tr>\n";
  };
 };
?>          </table>
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
