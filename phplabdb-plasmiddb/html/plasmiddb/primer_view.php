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
          <h3>
            <?php print _("Alignment"); ?> 
          </h3>
<?php
 if (isset($_GET['oligo']) && isset($_GET['seq'])) {
  $oligo=stripslashes(rawurldecode($_GET['oligo']));
  $seq=stripslashes(rawurldecode($_GET['seq']));
	$cmp='';
	$rseq='';
  for($i=0; $i<strlen($oligo); $i++){
   $cmp.=(($oligo[$i]==$seq[$i])?'|':'&nbsp;');
   $rseq.= (($seq[$i]=='A')?'T':(($seq[$i]=='G')?'C':(($seq[$i]=='C')?'G':(($seq[$i]=='T')?'A':(($seq[$i]=='U')?'A':(($seq[$i]=='V')?'B':(($seq[$i]=='D')?'H':(($seq[$i]=='M')?'K':(($seq[$i]=='W')?'W':(($seq[$i]=='Y')?'R':(($seq[$i]=='H')?'D':(($seq[$i]=='B')?'V':(($seq[$i]=='R')?'Y':(($seq[$i]=='S')?'S':(($seq[$i]=='K')?'M':(($seq[$i]=='N')?'N':(($seq[$i]=='I')?'I':'N')))))))))))))))));
  };
  print "          <p>\n            <code>\n            5'-$oligo-3' " . _("Oligo") . "<br>\n            &nbsp;&nbsp;&nbsp;$cmp<br>\n            3'-$rseq-5' " . _("Seq") . "\n            </code>\n\n          </p>\n";
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
