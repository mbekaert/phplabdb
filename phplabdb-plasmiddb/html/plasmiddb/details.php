<?php
 ob_start("ob_gzhandler");
 session_start();
 include_once '../includes/login.inc';
 if (!isset($_SESSION['status'])) {
  header('Location: ' . $base_url);
  exit;
 };
 $lev=error_reporting(8);
 $status=$_SESSION['status'];
 if(isset($_GET['plasmid'])) $plasmid=stripslashes(rawurldecode($_GET['plasmid']));
 if(isset($_GET['barcode'])) $barcode=intval(rawurldecode($_GET['barcode'])); 
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
.plasmid { background: url('images/plasmid.png') no-repeat right top; }
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
            <?php print _("Search details"); ?> 
          </h3>
<?php
 $dbconn=sql_connect($plugin_db['plasmiddb']);
 if (isset($barcode)) {
  $result=sql_query("SELECT name FROM plasmid WHERE barcode=" . intval($barcode) . ";",$dbconn);
  if ((strlen ($r=sql_last_error($dbconn))) || (sql_num_rows($result)!=1)) {
   print "          <p>\n            <a href=\"" . $base_url . $plugin . "\"><img src=\"" . $base_url . "images/oops.png\" alt=\"\"></a>&nbsp;<strong>Oops</strong><br>\n            $r\n          </p>\n";
  } else {
   $row=sql_fetch_row($result);
   $plasmid=$row[0];
  };
 };
 if (isset($plasmid)) {
  $result=sql_query("SELECT name, length, circ, dna, ascendant, pmid, selection, replication, plasmid, created, notes FROM prototype WHERE name='" . addslashes($plasmid) . "';",$dbconn);
  if ((strlen ($r=sql_last_error($dbconn))) || (sql_num_rows($result)!=1)) {
   print "          <p>\n            <a href=\"" . $base_url . $plugin . "\"><img src=\"" . $base_url . "images/oops.png\" alt=\"\"></a>&nbsp;<strong>Oops</strong><br>\n            $r\n          </p>\n";
  } else {
   $row=sql_fetch_row($result);
?>
          <table summary="">
            <tr class="list-row-odd">
              <td>
                <?php print _("Name"); ?> 
              </td>
              <td>
                <strong><?php print $row[0]; ?></strong>
              </td>
            </tr>
<?php  
   $result2=sql_query("SELECT alias FROM alias WHERE name='" . addslashes($row[0]) ."';",$dbconn);
   if (sql_num_rows($result2)!=0) {
?>
            <tr class="<?php print (($i++%2==1) ? 'list-row-odd' : 'list-row-even' ); ?>">
              <td>
                <?php print _("Alias"); ?> 
              </td>
              <td>
                <strong><?php  while($row2=sql_fetch_row($result2)) { print "$row2[0] "; }; ?></strong>
              </td>
            </tr>            
<?php  }; if (intval($row[1])!=0) { ?>
            <tr class="<?php print (($i++%2==1) ? 'list-row-odd' : 'list-row-even' ); ?>">
              <td>
                <?php print _("Length"); ?> 
              </td>
              <td>
                <strong><?php print $row[1] . ' ' . _("bp"); ?></strong>
              </td>
            </tr>
<?php  }; ?>
            <tr class="<?php print (($i++%2==1) ? 'list-row-odd' : 'list-row-even' ); ?>">
              <td>
                <?php print _("Type"); ?> 
              </td>
              <td>
                <strong><?php print (($row[2]=='t')?  _("Circular"): _("Linear")); ?></strong>
              </td>
            </tr>
            <tr class="<?php print (($i++%2==1) ? 'list-row-odd' : 'list-row-even' ); ?>">
              <td>
                <?php print _("Nucleic Acid"); ?> 
              </td>
              <td>
                <strong><?php print (($row[3]=='d')? _("DNA"): _("RNA")); ?></strong>
              </td>
            </tr>
            <tr class="<?php print (($i++%2==1) ? 'list-row-odd' : 'list-row-even' ); ?>">
              <td>
                <?php print _("Creation date"); ?> 
              </td>
              <td>
                <strong><?php print date(_("m/d/Y"),strtotime($row[9])); ?></strong>
              </td>
            </tr>
<?php  if ($row[5]!='') { ?>
            <tr class="<?php print (($i++%2==1) ? 'list-row-odd' : 'list-row-even' ); ?>">
              <td>
                PMID/Genbank
              </td>
              <td>
                <strong><?php foreach (explode(' ',$row[5]) as $value) { print '<a href="' . ((intval($value)==0)?('http://www.ncbi.nlm.nih.gov/entrez/viewer.fcgi?val=' . $value ):'http://www.ncbi.nlm.nih.gov/entrez/query.fcgi?cmd=Retrieve&db=PubMed&list_uids=' . $value . '&dopt=Abstract') . "\">$value</a> "; }; ?></strong>
              </td>
            </tr>
<?php  }; if ($row[4]!='') { ?>
            <tr class="<?php print (($i++%2==1) ? 'list-row-odd' : 'list-row-even' ); ?>">
              <td>
                <?php print _("Original"); ?> 
              </td>
              <td>
                <strong><?php print $row[4]; ?></strong>
              </td>
            </tr>
<?php  }; ?>            
            <tr class="<?php print (($i++%2==1) ? 'list-row-odd' : 'list-row-even' ); ?>">
              <td>
                <?php print _("Replication"); ?> 
              </td>
              <td>
                <?php 
     $x=explode('*',$row[7]);
     $j=0;
     foreach($x as $y) {
      $j++;
      $z=explode('|',$y);
      $z2=implode(', ',explode('#',$z[1]));
      print "<strong>$z[0]</strong>" . ((strlen($z2)>1)?": $z2":'') . (($j!=count($x))?'<br>':'');
     };
?> 
              </td>
            </tr>
            <tr class="<?php print (($i++%2==1) ? 'list-row-odd' : 'list-row-even' ); ?>">
              <td>
                <?php print _("Selection"); ?> 
              </td>
              <td>
                <?php 
     $x=explode('*',$row[6]);
     $j=0;
     foreach($x as $y) {
      $j++;
      $z=explode('|',$y);
      $z2=implode(', ',explode('#',$z[1]));
      print "<strong>$z[0]</strong>" . ((strlen($z2)>1)?": $z2":'') . (($j!=count($x))?'<br>':'');
     };
?> 
              </td>
            </tr>
<?php  
   $result2=sql_query("SELECT name FROM seq WHERE name='" . addslashes($row[0]) ."';",$dbconn);
   if (sql_num_rows($result2)!=0) { ?>
            <tr class="<?php print (($i++%2==1) ? 'list-row-odd' : 'list-row-even' ); ?>">
              <td>
                <?php print _("Sequence"); ?> 
              </td>
              <td>
                <a href="#" onClick="window.open('seq_view.php?plasmid=<?php print rawurlencode($row[0]); ?>', '<?php print _("Sequence"); ?>', 'toolbar=no, location=no, directories=no, status=no, scrollbars=yes, resizable=no, copyhistory=no, width=450, height=400, left=300, top=50')"><img src="images/wizard.png" alt="<?php print _("Sequence"); ?>"></a>
              </td>
            </tr>
<?php  
    if (isset($plugin_db['oligodb'])) { ?>
            <tr class="<?php print (($i++%2==1) ? 'list-row-odd' : 'list-row-even' ); ?>">
              <td>
                <?php print _("Primer"); ?> 
              </td>
              <td>
                <a href="primer.php?plasmid=<?php print rawurlencode($row[0]); ?>"><img src="images/wizard.png" alt="<?php print _("Find primer from OligoDB"); ?>"></a>
              </td>
            </tr>
<?php  };}; if ($row[10]!='') { ?>
            <tr class="<?php print (($i++%2==1) ? 'list-row-odd' : 'list-row-even' ); ?>">
              <td>
                <?php print _("Description"); ?> 
              </td>
              <td>
                <?php print $row[10]; ?> 
              </td>
            </tr>
<?php  };
   if ($row[8]=='t') {
    if (isset($barcode)) {
     $result2=sql_query("SELECT barcode, released, box, rank, conditioning, preparation, conc FROM plasmid WHERE barcode=" . $barcode . ";",$dbconn);
    } else {
     $result2=sql_query("SELECT barcode, released, box, rank, conditioning, preparation, conc FROM plasmid WHERE name='" . addslashes($plasmid) . "';",$dbconn);
    };
    if ((strlen ($r=sql_last_error($dbconn))) || (sql_num_rows($result2)==0)) {
     print "          </table>\n          <p>\n            <a href=\"" . $base_url . $plugin . "\"><img src=\"" . $base_url . "images/oops.png\" alt=\"\"></a>&nbsp;<strong>Oops</strong><br>\n            $r\n          </p>\n";
    } else {
     print "            <tr>\n              <td colspan=\"2\">\n";
     while( $row2 = sql_fetch_row($result2) ) {
?>
                <table id="list-database" width="100%" summary="">
                  <tr>
                    <td>
                      <?php print _("Box and rank"); ?> 
                    </td>
                    <td>
                      <strong><?php print $row2[2]. ' (' . $row2[3] . ')'; ?></strong>
                    </td>
                    <td rowspan="4" width="140">
<?php
   $result3=sql_query("SELECT value FROM config WHERE id='barcode';",$dbconn);
   if(!(strlen($r=sql_last_error($dbconn)))&&(sql_num_rows($result3)==1)) {
    $row3=sql_fetch_row($result3);
    if(isset($row3[0])) {
     printf("          <img src=\"%simages/barcode.php?code=%06d&amp;style=198&amp;type=I25&amp;width=125&amp;height=50&amp;xres=2&amp;font=3\" alt=\"\">\n",$base_url,$row2[0]);
    };
   };
?>
                    </td>
                  </tr>
                  <tr>
                    <td>
                      <?php print _("Date"); ?> 
                    </td>
                    <td>
                      <strong><?php print  date(_("m/d/Y"),strtotime($row2[1])); ?></strong>
                    </td>
                  </tr>
                  <tr>
                    <td>
                      <?php print _("Conditioning"); ?> 
                    </td>
                    <td>
                      <strong><?php print $row2[4]; ?></strong>
                    </td>
                  </tr>
                  <tr>
                    <td>
                      <?php print _("Preparation"); ?> 
                    </td>
                    <td>
                      <strong><?php print $row2[5] . (($row2[6]>0)?(' (' . $row2[6] . ' &micro;g/&micro;L)'):''); ?></strong>
                    </td>
                  </tr>
                </table>
                <br>
<?php 
     };
    };
   };
?>
              </td>
            </tr>
          </table>
<?php 
   $result2=sql_query("SELECT name FROM map WHERE name='" . addslashes($row[0]) ."';",$dbconn);
   if ((sql_num_rows($result2)!=0) && (intval($row[1])>0)) { print '<object data="images/map_view.php?plasmid=' . rawurlencode(addslashes($plasmid)) . '" width="500" height="400" align="center" type="image/svg+xml" pluginspage="http://www.adobe.com/svg/viewer/install/"><embed src="images/map_view.php?plasmid=' . rawurlencode(addslashes($plasmid)) . '" width="500" height="400" align="center" type="image/svg+xml" pluginspage="http://www.adobe.com/svg/viewer/install/"></object>'; };
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

