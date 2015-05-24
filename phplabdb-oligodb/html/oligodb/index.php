<?php
 ob_start("ob_gzhandler");
 session_start();
 include_once '../includes/login.inc';
 if (!isset($_SESSION['status'])) {
  header('Location: ' . $base_url);
  exit;
 };
 $status=$_SESSION['status'];
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
.oligo { background: url('images/oligo.png') no-repeat right top; }
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
            <?php print _("New"); ?> 
          </h3>
          <p>
            <?php print _("To add one new oligonucleotide click") . ' <a href="' . $base_url . $plugin . 'one_oligo.php"><strong>' . _("here") . '</strong></a>'; ?>.<br>
            <?php print _("To add new oligonucleotides click") . ' <a href="' . $base_url . $plugin . 'new_oligo.php"><strong>' . _("here") . '</strong></a>'; ?>.<br>
            <?php print _("To annotate a new oligonucleotide association click") . ' <a href="' . $base_url . $plugin . 'new_couple.php"><strong>' . _("here") . '</strong></a>'; ?>.
          </p>
<?php if ($status & pow(2,$plugin_level['oligodb'])) { ?>
          <h3>
            <?php print _("Administration"); ?> 
          </h3>
          <p>
            <?php print _("To access to the administration interface click") . ' <a href="' . $base_url . $plugin . 'admin/"><strong>' . _("here") . '</strong></a>'; ?>.
          </p>
<?php }; ?>
          <h3>
            <?php print _("Search"); ?> 
          </h3>
          <p>
            <?php print _("Enter the name of the searched oligonucleotide, or a part of its sequence:"); ?> 
          </p>
          <form action="<?php print $base_url . $plugin; ?>search.php" method="post" onsubmit="return chkFormulaire()">
            <table summary="">
              <tr>
                <td>
                  <select name="type">
                    <option value="name">
                      <?php print _("Name"); ?> 
                    </option>
                    <option value="oligo">
                      <?php print _("Sequence"); ?> 
                    </option>
                    <option value="synthesis">
                      <?php print _("Date of synthesis"); ?> 
                    </option>
                    <option value="box">
                      <?php print _("Box"); ?> 
                    </option>
                    <option value="freezer">
                      <?php print _("Freezer"); ?> 
                    </option>										
                    <option value="notes">
                      <?php print _("Notes"); ?> 
                    </option>
                    <option value="barcode">
                      <?php print _("ID"); ?> 
                    </option>
                  </select>
                </td>
                <td>
                  <input type="text" size="20" maxlength="100" name="data">
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

