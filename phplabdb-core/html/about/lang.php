<?php
 ob_start("ob_gzhandler");
 session_start();
 include_once '../includes/login.inc';
 if (!isset($_SESSION['status'])) {
  header('Location: ' . $base_url);
  exit;
 };
 $lang_new=(isset($_POST['lang'])?$_POST['lang']:'');
 if ($lang_new) {
   setcookie('lang',"$lang_new",mktime(1,0,0,date("m"),date("d")+1,date("Y"))+(isset($_POST['setAsDefault'])?1:0),'/');
   header("Location: " . $base_url);
   exit;
 };
 $lang=((isset($_COOKIE['lang']))?$_COOKIE['lang']:'en_US');
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
  if ($status & pow(2,30)) {
?>
        <li>
          <?php print '<a href="' . $base_url . 'admin/">' . _("Administration") . '</a>'; ?> 
        </li>
<?php }; ?>
        <li>
          <?php print '<strong><a href="' . $base_url . 'about/">' . _("About") . '</a></strong>'; ?> 
          <ul>
            <li>
              <?php print '<a href="' . $base_url . 'about/bug.php">' . _("Bug report") . '</a>'; ?> 
            </li>
            <li>
              <?php print '<strong><a href="' . $base_url . 'about/lang.php">' . _("Language") . '</a></strong>'; ?> 
            </li>
          </ul>
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
            <?php print _("Language Selector"); ?> 
          </h1>
          <p>
            <?php print _("Choose a language:"); ?> 
          </p>
          <form action="<?php print $base_url; ?>about/lang.php" method="post">
            <div>
              <input name="lang" id="en" type="radio" value="en_US"<?php if ($lang=='en_US') print ' checked="checked"'; ?>>&nbsp;<label for="en">English</label>&nbsp;<img src="<?php print $base_url; ?>images/flag_en.gif" alt=""><br>
              <input name="lang" id="fr" type="radio" value="fr_FR"<?php if ($lang=='fr_FR') print ' checked="checked"'; ?>>&nbsp;<label for="fr">Fran&#231;ais</label>&nbsp;<img src="<?php print $base_url; ?>images/flag_fr.gif" alt=""><br>
              &nbsp;<br>
              <input name="setAsDefault" id="setAsDefault" type="checkbox" value="true">&nbsp;<label for="setAsDefault"><?php print _("Set as default"); ?></label><br>
              &nbsp;<br>
              <input type="submit" name="submit" value="<?php print _("Submit"); ?>">
            </div>
          </form>
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
