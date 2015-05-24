<?php
 ob_start("ob_gzhandler");
 session_start();
 include_once '../includes/login.inc';
 if (!($_SESSION['status'] & pow(2,30))) {
  header('Location: ' . $base_url);
  exit;
 };
 $lev=error_reporting(8);
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
    <script type="text/javascript">
    //<![CDATA[
 function confirmation(){
  if (confirm('<?php print _("Remove this user?"); ?>')){
   return true;
  } else {
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
              <?php print '<strong><a href="' . $base_url . 'admin/users.php">' . _("Users") . '</a></strong>'; ?> 
            </li>
            <li>
              <?php print '<a href="' . $base_url . 'admin/db.php">' . _("Databases") . '</a>'; ?> 
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
           <?php print _("Users Management"); ?> 
         </h1>
<?php
 if (!(empty($_POST['search']))) {
?>
          <h3>
            <?php print _("Search"); ?> 
          </h3>
          <p>
            <?php print _("Type username:"); ?><br>
          </p>
          <form action="<?php print $base_url; ?>admin/users.php" method="post">
            <table summary="">
              <tr>
                <td>
                  <label for="search">&nbsp;<?php print _("Username"); ?>&nbsp;</label>
                </td>
                <td>
                  <input type="text" size="20" maxlength="20" name="search" id="search">
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
  $dbconn=sql_connect('db_phplabdb');
  $result=sql_query('SELECT login, email, status FROM membres WHERE login' . sql_reg($_POST['search']) . ';',$dbconn);
  if(strlen ($r=sql_last_error($dbconn))) {
   print "         <p>\n           <a href=\"" . $base_url . "admin/users.php\"><img src=\"" . $base_url . "images/oops.png\" alt=\"\"></a>&nbsp;<strong>Oops</strong>\n         </p>\n         <p>\n           $r\n         </p>\n";
  } else {
   $numbre_membre=sql_num_rows($result);
   if ( $numbre_membre != 0) { ?>
          <p>
            <?php print (($numbre_membre>1)? _("Result"): _("Results")) . ' 1 ' . _("to") . ' ' . $numbre_membre; ?> 
          </p>
          <table id="list-database" width="100%" summary="">
            <tr>
              <th>
                <?php print _("Name"); ?> 
              </th>
              <th>
                <?php print _("Description"); ?> 
              </th>
              <th>
                <?php print _("Action"); ?> 
              </th>
            </tr>
<?php   
    while( $row = sql_fetch_row($result) ) {
     print "            <tr class=\"" . (($i++%2==1)?'odd':'even') . "\">\n              <td class=\"column-1\">\n                $row[0]\n              </td>\n              <td class=\"column-2\">\n                $row[1]\n              </td>\n              <td class=\"column-3\">\n                <a href=\"users_modif.php?membre=". rawurlencode($row[0]) . '">' . _("Modify") . '</a>&nbsp;&nbsp;|&nbsp;&nbsp;<a href="users_modif.php?membre=' . rawurlencode($row[0]) . '&amp;remove=1" onclick="return confirmation();">' . _("Remove") . "</a>\n              </td>\n            </tr>\n"; 
    };
?>
</table>
<?php   } else { ?>
          <p>
            <em><?php print _("No result"); ?></em>
          </p>
<?php   };
  };
 } else {?>
          <h3>
            <?php print _("New user"); ?> 
          </h3>
          <p>
            <?php print _("To add user, just click") . ' <a href="' . $base_url . 'admin/users_add.php"><strong>' . _("here") . '</strong></a>' ?>.
          </p>
          <h3>
            <?php print _("Search"); ?> 
          </h3>
          <p>
            <?php print _("Type username:"); ?><br>
          </p>
          <form action="<?php print $base_url; ?>admin/users.php" method="post">
            <table summary="">
              <tr>
                <td>
                  <label for="search">&nbsp;<?php print _("Username"); ?>&nbsp;</label>
                </td>
                <td>
                  <input type="text" size="20" maxlength="20" name="search" id="search">
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
<?php };?>
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
