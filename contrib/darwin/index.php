<?php
ob_start("ob_gzhandler");
session_start();
require_once('../includes/main.inc');

if ($config['login']) {
  head('darwin');
?>
         <div class="items">
           <h1><?php print $plugin['darwin']['name']; ?><small><?php print $plugin['darwin']['description']; ?></small></h1><br />
            <div><h2><?php print _("Darwin Manager"); ?></h2>
                <ul>
                  <li><a href="<?php print $config['server'] . $plugin['darwin']['url']; ?>/sample" title="<?php print _("Manage the samples"); ?>"><?php print _("Samples"); ?></a> - <?php print _("You may manage all the samples of the collection"); ?>.</li>
                  <li><a href="<?php print $config['server'] . $plugin['darwin']['url']; ?>/bioject" title="<?php print _("Manage the Specimens"); ?>"><?php print _("Specimens"); ?></a> - <?php print _("You may manage all the Specimens of the collection"); ?>.</li>
                  <li><a href="<?php print $config['server'] . $plugin['darwin']['url']; ?>/event" title="<?php print _("Manage the collecting events"); ?>"><?php print _("Collecting events"); ?></a> - <?php print _("You may manage all collection event (the conjunctions of time and place)"); ?>.</li>
                  <li><a href="<?php print $config['server'] . $plugin['darwin']['url']; ?>/geolocation" title="<?php print _("Manage the collecting locations"); ?>"><?php print _("Locations"); ?></a> - <?php print _("You may manage all the places where a collecting events were conducted"); ?>.</li>
<?php if ($_SESSION['login']['right'] >= 3) {
?>
                  <li><a href="<?php print $config['server'] . $plugin['darwin']['url']; ?>/collection" title="<?php print _("Manage the collections"); ?>"><?php print _("Collections"); ?></a> - <?php print _("You may manage all the collections"); ?>.</li>
                  <li><a href="<?php print $config['server'] . $plugin['darwin']['url']; ?>/institution" title="<?php print _("Manage the institutions"); ?>"><?php print _("Institutions"); ?></a> - <?php print _("You may manage all the Institutions"); ?>.</li>
                  <li><a href="<?php print $config['server'] . $plugin['darwin']['url']; ?>/author" title="<?php print _("Manage the authors"); ?>"><?php print _("Authors"); ?></a> - <?php print _("You may manage all authors / collectors"); ?>.</li>
<?php }
?>
</ul>
</div>
           <br />
         </div>
         <div class="items">
          <form method="post" action="<?php print $config['server'] . $plugin['darwin']['url']; ?>/search">
          <div>
            <h2><?php print _("Search"); ?><small><a href="<?php print $config['server'] . $plugin['darwin']['url']; ?>/search" title="<?php print _("Identify a sample or a collection entry"); ?>"><?php print _("Advanced search"); ?></a></small></h2><br /><?php print _("Retrieve a sample, an collection entry... You may provide a reference numbre, a keyword, or a name."); ?><br /><br />
            <div>
              <label for="search"><?php print _("search"); ?></label>
              <input name="search" id="search" type="text" maxlength="32"<?php print (!empty($_POST['search'])?' value="' . stripslashes(strip_tags(trim($_POST['search']))) . '"':''); ?> />
              <br />
            </div>
            <br />
            <input type="hidden" name="darwin" value="<?php print md5('search' . floor(intval(date('b')))); ?>" />
            <input type="submit" value="<?php print _("Search"); ?>" />
          </div>
          </form>
          <br />
        </div>
<?php
  foot();
}else {
  header('Location: ' . $config['server']);
}
?>