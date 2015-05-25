<?php
ob_start("ob_gzhandler");
session_start();
require_once('../includes/main.inc');

if ($config['login']) {
  head('uniprime');
?>
        <div class="items">
          <h1><?php print $plugin['uniprime']['name']; ?><small><?php print $plugin['uniprime']['description']; ?></small></h1><br />
          <div><h2><?php print _("Options"); ?><small><a href="<?php print $config['server'] . $plugin['uniprime']['url']; ?>/options" title="<?php print _("Addvanced options"); ?>"><?php print _("Advanced options"); ?></a></small></h2><br /><?php print _("You may modify the output options such the common name, latin name etc."); ?><br /></div>
          <div><h2><?php print _("Browse"); ?><small><a href="<?php print $config['server'] . $plugin['uniprime']['url']; ?>/browse" title="<?php print _("Browse through the database"); ?>"><?php print _("Browse"); ?></a></small></h2><br /><?php print _("You may browse through the loci and retrieve all related informations."); ?><br /></div>
          <form method="post" action="<?php print $config['server'] . $plugin['uniprime']['url']; ?>/search">
           <div>
            <h2><?php print _("Search"); ?><small><a href="<?php print $config['server'] . $plugin['uniprime']['url']; ?>/search" title="<?php print _("Identify a locus or a sequence"); ?>"><?php print _("Advanced search"); ?></a></small></h2><br /><?php print _("Retrive a locus, a sequence, an alignment or a primer set. You may provide a reference numbre, a primer name, or a keywork."); ?><br /><br />
            <div>
              <label for="search"><?php print _("search"); ?></label>
              <input name="search" id="search" type="text" maxlength="32" />
              <br />
            </div>
            <br />
            <input type="hidden" name="uniprime" value="<?php print md5('search' . floor(intval(date('b')))); ?>" />
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