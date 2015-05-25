<?php
ob_start("ob_gzhandler");
session_start();
require_once('includes/main.inc');

head('about');
?>
         <div class="items">
           <h1><?php print _("About"); ?><small><?php print _("Who are we? How is it build?"); ?></small></h1><br />
           <div>
             <div><?php print _("phpLabDB Project is a Laboratory Information Management Systems (LIMS) for research laboratories. It is specifically designed for molecular biology labs. phpLabDB runs on a server and is accessed through a web browser. A web-interface lets the system administrator easily design new tables, which are completely integrated with phpLabDB. phpLabDB consists of a number of PHP scripts. It is developed using postgres or mysql as an SQL server. It has been tested on Linux, Debian, Mac OS X and Windows XP/Vista."); ?><br /></div>
             <div><h2>Web Framework</h2><img src="<?php print $config['server']; ?>/images/xhtml11.png" alt="XHTML 1.1 valid" width="80" height="15" />&nbsp;Conform to the <abbr title="Extensible HyperText Markup Language">XHTML</abbr> 1.1 standard recommended by the <abbr title="World Wide Web Consortium">W3C</abbr><br /><img src="<?php print $config['server']; ?>/images/css.png" alt="CSS 2.0 valid" width="80" height="15" />&nbsp;Conform to the <abbr title="Cascading Style Sheets">CSS</abbr> 2.0 standard recommended by the <abbr title="World Wide Web Consortium">W3C</abbr><br /><img src="<?php print $config['server']; ?>/images/waiaaa.png" alt="WAI-Triple A valid" width="80" height="15" />&nbsp;Conform to the <abbr title="Web Accessibility Initiative">WAI</abbr>-Triple A 1.0 standard recommended by the <abbr title="World Wide Web Consortium">W3C</abbr><br /><img src="<?php print $config['server']; ?>/images/browsers.png" alt="All browers valid" width="80" height="15" />&nbsp;Optimized for all browsers<br /></div>
<?php
if (isset($plugin)) {
  print '             <div><h2>Plug-in</h2>';
  foreach($plugin as $value) {
    print '<img src="' . $config['server'] . '/' . $value['url'] . '/logo.png" alt="' . $value['name'] . '" width="80" height="15" />&nbsp;' . $value['name'] . ' <strong>' . $value['version'] . '</strong><br />';
  }
  print "</div>\n";
}
?>
             <div><h2>Implementation</h2><img src="<?php print $config['server']; ?>/images/gimp.png" alt="The GIMP" width="80" height="15" />&nbsp;Graphics developed with The GIMP<br /><img src="<?php print $config['server']; ?>/images/vi.png" alt="Vim" width="80" height="15" />&nbsp;Script edited with The <abbr title="Vi IMproved">VIM</abbr><br /><img src="<?php print $config['server']; ?>/images/php.png" alt="PHP" width="80" height="15" />&nbsp;Site developed using <abbr title="recursive acronym for PHP: Hypertext Preprocessor">PHP</abbr><br /><?php
if ($config['sqlserver'] == 'postgresql') {
  print '<img src="' . $config['server'] . '/images/pgsql.png" alt="PostgreSQL" width="80" height="15" />&nbsp;Site developed using PostgreSQL<br />';
}elseif ($config['sqlserver'] == 'mysql') {
  print '<img src="' . $config['server'] . '/images/mysql.png" alt="MySQL" width="80" height="15" />&nbsp;Site developed using MySQL<br />';
}
?></div>
             <div><h2>License</h2><img src="<?php print $config['server']; ?>/images/cc.png" alt="Creative common" width="80" height="15" />&nbsp;This work is licensed under a Creative Commons License<br /></div>
             <div><h2>Contact</h2><img src="<?php print $config['server']; ?>/images/phplabdb.png" alt="<?php print $config['powered']; ?>" width="80" height="15" />&nbsp;The <a href="http://phplabdb.sourceforge.net">phpLabDB project</a> is hosted by <a href="http://sourceforge.net/">sourceforge.net</a> and developed by <script type="text/javascript">zulu('michael.bekaert','Micha&euml;l Bekaert','ucd.ie');</script>.<br /></div>
           </div>
           <br />
         </div>
<?php
foot(); ?>