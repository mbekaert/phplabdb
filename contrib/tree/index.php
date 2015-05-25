<?php
ob_start("ob_gzhandler");
session_start();
require_once('../includes/main.inc');

if ($config['login']) {
  head('tree');
?>
        <div class="items">
          <h1><?php print $plugin['tree']['name']; ?><small><?php print $plugin['tree']['description']; ?></small></h1><br />
<?php if ($_SESSION['login']['right'] >= 3) {
?>
          <div><h2><?php print _("Add specie"); ?><small><a href="<?php print $config['server'] . $plugin['tree']['url']; ?>/species/add" title="<?php print _("Add a new specie"); ?>"><?php print _("Add an organism..."); ?></a></small></h2><br /><?php print _("You may submit new organism and enable it for the other modules."); ?><br /></div>
<?php }
?>
          <div><h2><?php print _("Draw a tree"); ?><small><a href="<?php print $config['server'] . $plugin['tree']['url']; ?>/tree" title="<?php print _("Draw a phylogenetic tree"); ?>"><?php print _("Draw"); ?></a></small></h2><br /><?php print _("You select the species you want and draw a simple phylogenetic tree."); ?><br /></div>
          <form method="post" action="<?php print $config['server'] . $plugin['tree']['url']; ?>/search">
          <div>
            <h2><?php print _("Search"); ?><small><a href="<?php print $config['server'] . $plugin['tree']['url']; ?>/search" title="<?php print _("Identify an organism"); ?>"><?php print _("Advanced search"); ?></a></small></h2><br /><?php print _("Retrieve an organism. You may provide a reference numbre, a latin or an english name."); ?><br /><br />
            <div>
              <label for="search"><?php print _("search"); ?></label>
              <input name="search" id="search" type="text" maxlength="32" />
              <br />
            </div>
            <br />
            <input type="hidden" name="tree" value="<?php print md5('search' . floor(intval(date('b')))); ?>" />
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