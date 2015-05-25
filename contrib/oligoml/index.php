<?php
ob_start("ob_gzhandler");
session_start();
require_once('../includes/main.inc');

if ($config['login']) {
  head('oligoml');
?>
         <div class="items">
           <h1><?php print $plugin['oligoml']['name']; ?><small><?php print $plugin['oligoml']['description']; ?></small></h1><br />
<?php if ($_SESSION['login']['right'] >= 2) {
?>
            <div><h2><?php print _("Submit primers"); ?><small><a href="<?php print $config['server'] . $plugin['oligoml']['url']; ?>/add" title="<?php print _("Add a new oligonuclotide or a primer set"); ?>"><?php print _("Add a primer..."); ?></a></small></h2><br /><?php print _("You may submit new oligonucloeotides or a primer set that could be shared. On-line primer submission is available."); ?><br /></div>
<?php }
?>
          <form method="post" action="<?php print $config['server'] . $plugin['oligoml']['url']; ?>/search">
          <div>
            <h2><?php print _("Search"); ?><small><a href="<?php print $config['server'] . $plugin['oligoml']['url']; ?>/search" title="<?php print _("Identify a oligonuclotide or a primer set"); ?>"><?php print _("Advanced search"); ?></a></small></h2><br /><?php print _("Retrieve an oligonucleotide, a primer or a primer set. You may provide a reference numbre, a primer name, or a nucleic sequence."); ?><br /><br />
            <div>
              <label for="search"><?php print _("search"); ?></label>
              <input name="search" id="search" type="text" maxlength="32" />
              <br />
            </div>
            <br />
            <input type="hidden" name="oligoml" value="<?php print md5('search' . floor(intval(date('b')))); ?>" />
            <input type="submit" value="<?php print _("Search"); ?>" />
          </div>
          </form>
         </div>
<?php
  foot();
}else {
  header('Location: ' . $config['server']);
}
?>