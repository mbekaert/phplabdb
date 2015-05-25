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
            <?php print _("Sequence") . ' (' . $_SESSION['plasmide_proto']['name'] . ' - ' . $_SESSION['plasmide_proto']['length'] . ' ' . _("bp") . ')'; ?>
          </h3>
<?php if (!(isset($_POST['seq']))) { ?>
          <form action="<?php print $base_url . $plugin_dir['plasmiddb']; ?>addseq.php" method="post">
            <div>
              <textarea cols="60" rows="10" name="seq"><?php if (isset($_SESSION['plasmide_seq'])) {print $_SESSION['plasmide_seq']; }; ?></textarea><br>
              <input type="submit" value="<?php print _("Save"); ?>">
            </div>
          </form>
<?php } else {
 $_SESSION['plasmide_seq']=strtoupper(trim(stripslashes($_POST['seq'])));
 if (strlen($_SESSION['plasmide_seq'])!=0) {
  if ($_SESSION['plasmide_proto']['length']!=strlen($_SESSION['plasmide_seq'])) {
   print "          <p>\n            " . _("Sequence length:") . ' ' . strlen($_SESSION['plasmide_seq']) . ' ' . _("bp") . ', ' . _("length given:") . ' ' . $_SESSION['plasmide_proto']['length'] . ' ' . _("bp") . '... ' . _("New length:") . ' ' . strlen($_SESSION['plasmide_seq']) . ' ' . _("bp") . ".\n          </p>\n";
   $_SESSION['plasmide_proto']['length']=strlen($_SESSION['plasmide_seq']);
  };     
 } else {
  unset($_SESSION['plasmide_seq']);
  print "          <p>\n            " . _("Sequence cleaned") . "\n          </p>\n";
 };
?>
          <p>
            <a href="javascript:window.close();"><?php print _("Close the windows"); ?></a>
          </p>
<?php }; ?>
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
