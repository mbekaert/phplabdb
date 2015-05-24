<?php
 ob_start("ob_gzhandler");
 session_start();
 include_once '../includes/login.inc';
 if (!isset($_SESSION['status']) && !isset($plugin_db['oligodb'])) {
  header('Location: ' . $base_url);
  exit;
 };
 $lev=error_reporting(8);
 function reverse ($seq) {
  $rsequence='';
  for($i=(strlen($seq)-1); $i>=0; $i--)
   $rsequence.= (($seq[$i]=='A')?'T':(($seq[$i]=='G')?'C':(($seq[$i]=='C')?'G':(($seq[$i]=='T')?'A':(($seq[$i]=='U')?'A':(($seq[$i]=='V')?'B':(($seq[$i]=='D')?'H':(($seq[$i]=='M')?'K':(($seq[$i]=='W')?'W':(($seq[$i]=='Y')?'R':(($seq[$i]=='H')?'D':(($seq[$i]=='B')?'V':(($seq[$i]=='R')?'Y':(($seq[$i]=='S')?'S':(($seq[$i]=='K')?'M':(($seq[$i]=='N')?'N':(($seq[$i]=='I')?'I':'N')))))))))))))))));
  return $rsequence;
 };
 $status=$_SESSION['status'];
 if (isset($_GET['plasmid'])) $plasmid=stripslashes(rawurldecode($_GET['plasmid']));
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
            <?php print _("Search"); ?> 
          </h3>
<?php
 $dbconn=sql_connect($plugin_db['plasmiddb']);
 if (isset($_POST['length'])) {
  if (isset($_POST['forward']) && isset($_POST['reverse'])) {
	 $i=0;
?>
          <table id="list-database" width="100%" summary="">
            <tr>
              <th>
                <?php print _("Association"); ?> 
              </th>
              <th>
                <?php print _("PCR amplification"); ?> 
              </th>
            </tr>
<?php 
  foreach($_POST['forward'] as $fkey => $fname) {
   foreach($_POST['reverse'] as $rkey => $rname) {
    print "            <tr class=\"" . (($i++%2 ==1) ? 'odd' : 'even' ) . "\">\n              <td class=\"column-1\">\n                &nbsp;<strong>$fname</strong> / <strong>$rname</strong>&nbsp;\n              </td>\n              <td class=\"column-3\">\n                &nbsp;" . ((($pos=($_POST['position'][$rkey] - $_POST['position'][$fkey]))>0)?$pos:($_POST['length']+$pos)) . ' ' . _("bp") . "&nbsp;\n              </td>\n            </tr>\n";
   };
  };
?>
          </table>
<?php
  } else {
   print "          <p>\n            <em>" . _("No result") . "</em>\n          </p>\n";
  };
 } else {
  $primer=-10;
  $result=sql_query("SELECT value FROM config WHERE id='primer';",$dbconn);
  if(!(strlen($r=sql_last_error($dbconn)))&&(sql_num_rows($result)==1)) {
   $row=sql_fetch_row($result);
   if(isset($row[0])) $primer=+-intval($row[0]);
  };
  $result=sql_query('SELECT seq FROM seq WHERE name=\'' . addslashes($plasmid) .'\';',$dbconn);
  if(!(strlen($r=sql_last_error($dbconn))) && (sql_num_rows($result)==1)) {
   $row=sql_fetch_row($result);
   $seq0=$row[0];
   $seq=$seq0 . substr($seq0,($primer+1));
   $rseq=reverse($seq);
   $dbconn=sql_connect($plugin_db['oligodb']);
   switch ($sqlserver) {
    case 'mysql':
     $result2=sql_query("SELECT name,barcode FROM oligo WHERE INSTR('$seq',SUBSTRING(oligo,$primer))>0 ORDER BY name;",$dbconn);
     $result3=sql_query("SELECT name,barcode FROM oligo WHERE INSTR('$rseq',SUBSTRING(oligo,$primer))>0 ORDER BY name;",$dbconn);
     break;
    case 'postgresql':
     $result2=sql_query("SELECT name,barcode,oligo FROM oligo WHERE strpos('$seq',substr(oligo,$primer))>0 ORDER BY name;",$dbconn);
     $result3=sql_query("SELECT name,barcode,oligo FROM oligo WHERE strpos('$rseq',substr(oligo,$primer))>0 ORDER BY name;",$dbconn);
     break;
    default:
     exit;
   };
   if(!(strlen($r=sql_last_error($dbconn)))) {
    print "          <form action=\"primer.php\" method=\"post\">\n";
    $oligo=sql_num_rows($result2);
    $j=0;
    if ($oligo!=0) {
     $i=0;
?>
          <p>
            <?php print (($oligo>1)? _("Forward oligonuleotides found:"): _("Forward oligonuleotide found:")) . ' <strong>' . $oligo . '</strong>'; ?> 
          </p>
          <table id="list-database" width="100%" summary="">
            <tr>
              <th>
                <?php print _("Name"); ?> 
              </th>
              <th>
                <?php print _("Identity"); ?> 
              </th>
              <th>
                <?php print _("Position (W)"); ?> 
              </th>
              <th>
                <?php print _("See?"); ?> 
              </th>
            </tr>
<?php 
     while($row=sql_fetch_row($result2)) {
      $pos=-1;
      while(($pos=strpos($seq,substr($row[2],$primer),$pos+1))) {
       $posi=$pos-strlen($row[2])-$primer;
       $subseq=substr($seq0,$posi,strlen($row[2])).''.((($posi+strlen($row[2])) > strlen($seq0) )?substr($seq0,0,($posi+strlen($row[2])-strlen($seq0))):'');
       print "            <tr class=\"" . (($i%2 ==1) ? 'odd' : 'even' ) . "\">\n              <td class=\"column-1\">\n                &nbsp;<a href=\"" . $base_url . 'oligodb/details.php?barcode='. rawurlencode($row[1]) . '" title="' . _("details") . '">' . $row[0] . "</a>&nbsp;\n              </td>\n              <td class=\"column-3\">\n                &nbsp;" . (int) ((1-levenshtein($subseq,$row[2])/strlen($row[2]))*100) . "%&nbsp;\n              </td>\n              <td class=\"column-3\">\n                &nbsp;$posi&nbsp;\n              </td>\n              <td class=\"column-3\">\n                <a href=\"#\" onClick=\"window.open('primer_view.php?oligo=" . rawurlencode($row[2]) . "&amp;seq=" . rawurlencode($subseq) . "', '" . _("Alignment") . "', 'toolbar=no, location=no, directories=no, status=no, scrollbars=yes, resizable=no, copyhistory=no, width=450, height=300, left=300, top=50')\"><img src=\"images/see.png\" alt=\"\"></a>&nbsp;<input type=\"checkbox\" name=\"forward[" . ++$j ."]\" value=\"" . rawurlencode($row[0]) . "\"><input type=\"hidden\" name=\"position[$j]\" value=\"$posi\">\n              </td>\n            </tr>\n";
      };
      $i++;
     };
    };
?>
          </table>
<?php
    $oligo=sql_num_rows($result3);
    if ($oligo!=0) {
     $i=0;
?>
          <p>
            <?php print (($oligo>1)? _("Reverse oligonuleotides found:"):_("Reverse oligonuleotide found:")) . ' <strong>' . $oligo . '</strong>'; ?> 
          </p>
          <table id="list-database" width="100%" summary="">
            <tr>
              <th>
                <?php print _("Name"); ?> 
              </th>
              <th>
                <?php print _("Identity"); ?> 
              </th>
              <th>
                <?php print _("Position (W)"); ?> 
              </th>
              <th>
                <?php print _("See?"); ?> 
              </th>
            </tr>
<?php 
     while($row=sql_fetch_row($result3)) {
      $pos=-1;
      $roligo=reverse($row[2]);
      while(($pos=strpos($seq,substr($roligo,$primer),$pos+1))) {
       $posi=$pos-strlen($roligo)-$primer;
       $subseq=substr($seq0,$posi,strlen($roligo)).''.((($posi+strlen($roligo)) > strlen($seq0) )?substr($seq0,0,($posi+strlen($roligo)-strlen($seq0))):'');
       print "            <tr class=\"" . (($i%2 ==1) ? 'odd' : 'even' ) . "\">\n              <td class=\"column-1\">\n                &nbsp;<a href=\"" . $base_url . 'oligodb/details.php?barcode='. rawurlencode($row[1]) . '" title="' . _("details") . '">' . $row[0] . "</a>&nbsp;\n              </td>\n              <td class=\"column-3\">\n                &nbsp;" . (int) ((1-levenshtein($subseq,$roligo)/strlen($roligo))*100) . "%&nbsp;\n              </td>\n              <td class=\"column-3\">\n                &nbsp;$posi&nbsp;\n              </td>\n              <td class=\"column-3\">\n                <a href=\"#\" onClick=\"window.open('primer_view.php?oligo=" . rawurlencode($row[2]) . "&amp;seq=" . rawurlencode(reverse($subseq)) . "', '" . _("Alignment") . "', 'toolbar=no, location=no, directories=no, status=no, scrollbars=yes, resizable=no, copyhistory=no, width=450, height=300, left=300, top=50')\"><img src=\"images/see.png\" alt=\"\"></a>&nbsp;<input type=\"checkbox\" name=\"reverse[" . ++$j ."]\" value=\"" . rawurlencode($row[0]) . "\"><input type=\"hidden\" name=\"position[$j]\" value=\"" . ($pos-strlen($roligo)) . "\">\n              </td>\n            </tr>\n";
      };
      $i++;
     };
    };
?>
          </table>
<?php
   };
   print "        <p>\n          <input type=\"hidden\" name=\"length\" value=\"" . strlen($seq0) . '"><input name="clear" type="reset" value="' . _("Clear") . '"> &nbsp; ' . _("or") . ' &nbsp; <input type="submit" name="next" value="' . _("Search") . " &gt;&gt;\">\n        </p>\n        </form>\n";
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
