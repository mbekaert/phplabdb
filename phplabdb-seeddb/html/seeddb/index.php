<?php
 ob_start("ob_gzhandler");
 session_start();
 include_once '../includes/login.inc';
 $lang=((isset($_COOKIE['lang']))?substr($_COOKIE['lang'],0,2):'en');
 if (!isset($_SESSION['status'])) {
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
 $status=$_SESSION['status'];
 header_start();
 $dbconn=sql_connect($plugin_db['seeddb']);
 $result=sql_query("SELECT value FROM config WHERE id='tips';",$dbconn);
 if(!(strlen($r=sql_last_error($dbconn)))&&(sql_num_rows($result)==1)) {
  $row = sql_fetch_row($result);
  if(isset($row[0])) $tips=true;
 };
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
.seed { background: url('images/seeds.png') no-repeat right top; }
    </style>
<?php if(isset($tips)){ ?>
   <script type="text/javascript">
    //<![CDATA[
 var arrayData = new Array();
 arrayData['a.ref'] = '<?php print _("Seed reference"); ?>';
 arrayData['b.vernacular'] = '<?php print _("Vernacular name"); ?>';
 arrayData['b.country'] = '<?php print _("Country name"); ?>';
 arrayData['a.species'] = '<?php print _("Species name"); ?>';
 arrayData['a.crosstype']	= '<?php print _("Cross type name"); ?> [<?php print implode(" | ", read_table($dbconn,'crosstype',$lang)); ?>]';
 arrayData['a.father'] = '<?php print _("Pollen reference"); ?>';
 arrayData['a.mother'] = '<?php print _("Carry seed reference"); ?>';
 arrayData['b.prospection']	= '<?php print _("Prospection reference"); ?>';
 arrayData['b.locality']	= '<?php print _("Locality name"); ?>';
 arrayData['b.ethnos'] = '<?php print _("Ethnos group name"); ?>';
 arrayData['b.nature'] = '<?php print _("Landscape nature"); ?> [<?php print implode(" | ", read_table($dbconn,'nature',$lang)); ?>]';
 arrayData['b.form'] = '<?php print _("Botanical form"); ?> [<?php print implode(" | ", read_table($dbconn,'form',$lang)); ?>]';
 arrayData['b.size'] = '<?php print _("Population size"); ?> [<?php print implode(" | ", read_table($dbconn,'size',$lang)); ?>]';
 arrayData['b.distribution'] = '<?php print _("Plant distribution"); ?> [<?php print implode(" | ", read_table($dbconn,'distribution',$lang)); ?>]';
 arrayData['b.weather'] = '<?php print _("Weather/Culture type"); ?> [<?php print implode(" | ", read_table($dbconn,'weather',$lang)); ?>]';
 arrayData['b.precocity'] = '<?php print _("Plant precocity"); ?> [<?php print implode(" | ", read_table($dbconn,'precocity',$lang)); ?>]';
 arrayData['a.note'] = '<?php print _("Notes and remarques"); ?>';
 arrayData['a.barcode'] = '<?php print _("Internal reference / barcode"); ?>';
 function populatehelp( name ) {
  if( document.getElementById( "helpspan" ) != null ) {
   helpspan.innerText = arrayData[name];
  };
 };
    //]]>
    </script>
<?php }; ?>
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
          <?php print '<strong><a href="' . $base_url . 'database.php">' . _("Databases") . '</strong></a>'; ?> 
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
            <?php print _("New"); ?> 
          </h3>
          <p>
            <?php print _("To add new seeds click") . ' <a href="' . $base_url . $plugin . 'new_seed.php"><strong>' . _("here") . '</strong></a>'; ?>.<br>
            <?php print _("To add new cross click") . ' <a href="' . $base_url . $plugin . 'new_cross.php"><strong>' . _("here") . '</strong></a>'; ?>.
          </p>
<?php if ($status & pow(2,$plugin_level['seeddb'])) { ?>
          <h3>
            <?php print _("Administration"); ?> 
          </h3>
          <p>
            <?php print _("To access to the administration interface click") . ' <a href="' . $base_url . $plugin . 'admin/"><strong>' . _("here") . '</strong></a>'; ?>.
          </p>
<?php };?>
          <h3>
            <?php print _("Search"); ?> 
          </h3>
          <p>
            <?php print _("Enter the name of the searched seed:"); ?> 
          </p>
          <form action="<?php print $base_url . $plugin; ?>search.php" method="post">
            <table summary="">
              <tr>
                <td>
                  <select name="type[0]"<?php if(isset($tips)) print ' onChange="javascript:populatehelp(this.options[selectedIndex].value)"'; ?>>
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
                  <small><input type="image" src="images/plus.png" alt="<?php print _("add"); ?>" name="add">&nbsp;<?php print _("add"); ?></small>
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
<?php if(isset($tips)) { ?>
            <p>
              <small><?php print _("Tips:"); ?> <span id="helpspan"><?php print _("Seed reference"); ?></span></small>
            </p>
<?php }; ?>
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
