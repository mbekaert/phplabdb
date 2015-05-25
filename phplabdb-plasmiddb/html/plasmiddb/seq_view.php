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
<?php textdomain('plasmiddb'); ?>
  </head>
  <body>
    <div id="middle-one">
      <div class="corner-tr">
        &nbsp;
      </div>
      <div class="corner-tl">
        &nbsp;
      </div>
      <div id="content" class="plasmid">
        <div id="page-main">      
          <h1>
            PlasmidDB plug-in
          </h1>
<?php if (isset($_GET['plasmid'])) {
  $plasmide=stripslashes(rawurldecode($_GET['plasmid']));
  print "          <h3>\n            $plasmide\n          </h3>\n";
  $dbconn=sql_connect($plugin_db['plasmiddb']);
  $result=sql_query("SELECT seq FROM seq WHERE name='" . addslashes($plasmide) ."';",$dbconn);
  if ((strlen ($r=sql_last_error($dbconn))) || (sql_num_rows($result)!=1)) {
   print "          <p>\n            <img src=\"" . $base_url . "images/oops.png\" alt=\"\">&nbsp;<strong>Oops</strong><br>\n            $r\n          </p>\n";
  } else {
   $row=sql_fetch_row($result);
   print '          <p><pre>' . wordwrap(wordwrap($row[0],10,' ',1),55) . "</pre><p>\n";
  };
 };
?>
          <p>
            <a href="javascript:window.close();"><?php print _("Close the windows"); ?></a>
          </p>
        </div>
      </div>
      <div class="corner-br">
        &nbsp;
      </div>
      <div class="corner-bl">
        &nbsp;
      </div>
    </div>
  </body>
</html>
