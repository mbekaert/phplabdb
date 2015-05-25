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
  $result=sql_query("SELECT id, name, taxon FROM species ORDER BY id;",$dbconn);
  if(!(strlen($r=sql_last_error($dbconn)))) {
   while($row=sql_fetch_row($result)) {
    $species[$row[0]][0]=htmlentities($row[1]);
    if (isset($row[1])) $species[$row[0]][1]=$row[2];
   };
  };
  return $species;
 };
 $status=$_SESSION['status'];
 $barcode=intval(rawurldecode($_GET['barcode']));
 header_start();
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
            <?php print _("Search details"); ?> 
          </h3>
<?php
 if (!(empty($barcode))) {
  $dbconn=sql_connect($plugin_db['straindb']);
  $result=sql_query("SELECT barcode, name, released, box, rank, species, strain_origin, ploidy, plasmid, phenotype, genotype, medium, pmid, notes FROM strain WHERE barcode='" . $barcode . "';",$dbconn);
  if(strlen ($r=sql_last_error($dbconn))) {
   print "          <p>\n            <a href=\"" . $base_url . $plugin . "\"><img src=\"" . $base_url .  "images/oops.png\" alt=\"\"></a>&nbsp;<strong>Oops</strong>\n          </p>\n          <p>\n            $r\n          </p>\n";
  } else {
   if (sql_num_rows($result)==1) {
    $row=sql_fetch_row($result);

?>
          <table id="list-database" summary="">
            <tr class="odd">
              <td class="column-1">
                <?php print _("Name"); ?> 
              </td>
              <td class="column-2">
                <strong><?php print $row[1]; ?></strong>
              </td>
            </tr>
            <tr class="even">
              <td class="column-1">
                <?php print _("Date of storage"); ?> 
              </td>
              <td class="column-2">
                <strong><?php print date(_("m/d/Y"),strtotime($row[2])); ?></strong>
              </td>
            </tr>
            <tr class="odd">
              <td class="column-1">
                <?php print _("Place"); ?> 
              </td>
              <td class="column-2">
                <strong><?php print $row[3] . (($row[4]!='')?" ($row[4])":''); ?></strong>
              </td>
            </tr>
            <tr class="even">
              <td class="column-1">
                <?php $species=species($dbconn); print _("Species"); ?> 
              </td>
              <td class="column-2">
                <strong><em><?php print ((isset($species[$row[5]][1]))?('<a href="http://www.ncbi.nlm.nih.gov/Taxonomy/Browser/wwwtax.cgi?mode=Info&id=' . $species[$row[5]][1] . '">' . $species[$row[5]][0] . '</a>'):$species[$row[5]][0]); ?></em></strong>
              </td>
            </tr>
<?php  if ($row[6]!='') { ?>
            <tr class="<?php print (($i++%2==0)?'odd':'even'); ?>">
              <td class="column-1">
                <?php print _("Strain origin"); ?> 
              </td>
              <td class="column-2">
                <strong><?php print $row[6]; ?></strong>
              </td>
            </tr>
<?php } if ($row[7]!='') { ?>
            <tr class="<?php print (($i++%2==0)?'odd':'even'); ?>">
              <td class="column-1">
                <?php print _("Ploidy"); ?> 
              </td>
              <td class="column-2">
                <strong><?php print $row[7]; ?></strong>
              </td>
            </tr>
<?php } if ($row[8]!='') { ?>
            <tr class="<?php print (($i++%2==0)?'odd':'even'); ?>">
              <td class="column-1">
                <?php print _("Plasmid"); ?> 
              </td>
              <td class="column-2">
                <strong><?php print $row[8]; ?></strong>
              </td>
            </tr>
<?php } if ($row[10]!='') { ?>
            <tr class="<?php print (($i++%2==0)?'odd':'even'); ?>">
              <td class="column-1">
                <?php print _("Genotype"); ?> 
              </td>
              <td class="column-2">
                <strong><?php print $row[10]; ?></strong>
              </td>
            </tr>
<?php } if ($row[9]!='') { ?>
            <tr class="<?php print (($i++%2==0)?'odd':'even'); ?>">
              <td class="column-1">
                <?php print _("Phenotype"); ?> 
              </td>
              <td class="column-2">
                <strong><?php print $row[9]; ?></strong>
              </td>
            </tr>
<?php } if ($row[11]!='') { ?>
            <tr class="<?php print (($i++%2==0)?'odd':'even'); ?>">
              <td class="column-1">
                <?php print _("Medium"); ?> 
              </td>
              <td class="column-2">
                <strong><?php print $row[11]; ?></strong>
              </td>
            </tr>
<?php } if ($row[12]!='') { ?>
            <tr class="<?php print (($i++%2==0)?'odd':'even'); ?>">
              <td class="column-1">
                <?php print _("PMID"); ?> 
              </td>
              <td class="column-2">
                <strong><?php foreach (explode(' ',$row[12]) as $value) { echo '<a href="' . ((intval($value)==0)?('http://www.ncbi.nlm.nih.gov/entrez/viewer.fcgi?val=' . $value ):'http://www.ncbi.nlm.nih.gov/entrez/query.fcgi?cmd=Retrieve&db=PubMed&list_uids=' . $value . '&dopt=Abstract') . "\">$value</a> "; }; ?></strong>
              </td>
            </tr>
<?php } if ($row[13]!='') { ?>
            <tr class="<?php print (($i++%2==0)?'odd':'even'); ?>">
              <td class="column-1">
                <?php print _("Notes"); ?> 
              </td>
              <td class="column-2">
                <strong><?php print $row[13]; ?></strong>
              </td>
            </tr>
<?php }; ?>    
          </table>
<?php
    $result2=sql_query("SELECT value FROM config WHERE id='barcode';",$dbconn);
    if(!(strlen($r=sql_last_error($dbconn)))&&(sql_num_rows($result2)==1)) {
     $row2=sql_fetch_row($result2);
     if(isset($row2[0])) {
      printf("          <img src=\"%simages/barcode.php?code=%06d&amp;style=198&amp;type=I25&amp;width=125&amp;height=50&amp;xres=2&amp;font=3\" alt=\"\">\n",$base_url,$row[0]);			
     };
    };
   };
  };
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

