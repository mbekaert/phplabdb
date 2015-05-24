<?php
 ob_start("ob_gzhandler");
 session_start();
 include_once '../../includes/login.inc';
 if (!($_SESSION['status'] & pow(2,$plugin_level['seeddb']))) {
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
      ..:: phpLabDB::SeedDB ::..
    </title>
    <meta http-equiv="Content-Type" content="text/html">
    <link rel="stylesheet" type="text/css" media="print" href="<?php print $base_url; ?>css/print.css">
    <link rel="stylesheet" type="text/css" media="screen" href="<?php print $base_url; ?>css/screen.css">
    <style type="text/css">
.seed { background: url('../images/seeds.png') no-repeat right top; }
    </style>
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
<?php if ($status & pow(2,30)) { ?>
          <h3>
            <?php print _("Administration"); ?> 
          </h3>
          <p>
            <?php print _("To access to the administation options click") . ' <a href="' . $base_url . $plugin . 'admin/tips.php"><strong>' . _("here") . '</strong></a>'; ?>.
          </p>
<?php }; ?>
          <h3>
            <?php print _("Option management"); ?> 
          </h3>
          <ul>
            <li><?php print _("To add new") . ' <a href="' . $base_url . $plugin .'admin/options.php?table=species"><strong>' . _("Species") . '</strong></a>'; ?>.</li>
            <li><?php print _("To add new") . ' <a href="' . $base_url . $plugin .'admin/options.php?table=form"><strong>' . _("Botanical form") . '</strong></a>'; ?>.</li>
            <li><?php print _("To add new") . ' <a href="' . $base_url . $plugin .'admin/options.php?table=distribution"><strong>' . _("Plant distribution") . '</strong></a>'; ?>.</li>
            <li><?php print _("To add new") . ' <a href="' . $base_url . $plugin .'admin/options.php?table=size"><strong>' . _("Population size") . '</strong></a>'; ?>.</li>
            <li><?php print _("To add new") . ' <a href="' . $base_url . $plugin .'admin/options.php?table=nature"><strong>' . _("Landscape nature") . '</strong></a>'; ?>.</li>
            <li><?php print _("To add new") . ' <a href="' . $base_url . $plugin .'admin/options.php?table=weather"><strong>' . _("Weather/culture type") . '</strong></a>'; ?>.</li>
            <li><?php print _("To add new") . ' <a href="' . $base_url . $plugin .'admin/options.php?table=precocity"><strong>' . _("Plant precocity") . '</strong></a>'; ?>.</li>
          </ul>
          <h3>
            <?php print _("Search"); ?> 
          </h3>
          <p>
            <?php print _("Enter the name of the searched seed:"); ?> 
          </p>
          <form action="<?php print $base_url . $plugin; ?>admin/search.php" method="post">
            <table summary="">
              <tr>
                <td>
                  <select name="type[0]">
                    <option value="a.ref">
                      <?php print _("Reference"); ?> 
                    </option>
                    <option value="b.vernacular">
                      <?php print _("Vernacular name"); ?> 
                    </option>
                    <option value="b.country">
                      <?php print _("Country"); ?> 
                    </option>
                    <option value="a.species">
                      <?php print _("Species"); ?> 
                    </option>
                    <option value="a.crosstype">
                      <?php print _("Cross type"); ?> 
                    </option>
                    <option value="a.father">
                      <?php print _("Father"); ?> 
                    </option>
                    <option value="a.mother">
                      <?php print _("Mother"); ?> 
                    </option>
                    <option value="b.prospection">
                      <?php print _("Prospection ref"); ?> 
                    </option>
                    <option value="b.locality">
                      <?php print _("Locality"); ?> 
                    </option>
                    <option value="b.ethnos">
                      <?php print _("Ethnos group"); ?> 
                    </option>
                    <option value="b.nature">
                      <?php print _("Landscape nature"); ?> 
                    </option>
                    <option value="b.form">
                      <?php print _("Botanical form"); ?> 
                    </option>
                    <option value="b.size">
                      <?php print _("Population size"); ?> 
                    </option>
                    <option value="b.distribution">
                      <?php print _("Plant distribution"); ?> 
                    </option>
                    <option value="b.weather">
                      <?php print _("Weather/Culture type"); ?> 
                    </option>
                    <option value="b.precocity">
                      <?php print _("Plant precocity"); ?> 
                    </option>
                    <option value="a.note">
                      <?php print _("Notes"); ?> 
                    </option>
                    <option value="a.barcode">
                      <?php print _("ID"); ?> 
                    </option>
                  </select>
                </td>
                <td>
                  <input type="text" size="20" maxlength="100" name="data[0]">
                </td>
                <td>
                  <small><input type="image" src="../images/plus.png" alt="<?php print _("add"); ?>" name="add">&nbsp;<?php print _("add"); ?></small>
                </td>
              </tr>
              <tr>
                <td>
                </td>
                <td>
                  <input type="hidden" name="n" value="0"><input type="submit" name="submit" value="<?php print _("Search"); ?>">
                </td>
                <td>
                </td>
              </tr>
            </table>
          </form>
<?php  textdomain('phplabdb'); ?>
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
