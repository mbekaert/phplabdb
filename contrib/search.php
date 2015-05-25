<?php
ob_start("ob_gzhandler");
session_start();
require_once('includes/main.inc');

if ($config['login']) {
  if (isset($plugin) && isset($_GET['xml'])) {
    header('Content-Type: application/opensearchdescription+xml');
    print '<' . '?xml version="1.0" encoding="UTF-8"?' . ">\n";
?>
<OpenSearchDescription xmlns="http://a9.com/-/spec/opensearch/1.1/">    <ShortName><?php print _("phpLabDB"); ?></ShortName>     <Description><?php print _("phpLabDB Search Engine Plugin"); ?></Description>    <Url type="text/html" method="get" template="<?php print $config['server']; ?>/search/{searchTerms}"/>     <Image width="16" height="16" type="image/x-icon"><?php print $config['server']; ?>/favicon.ico</Image></OpenSearchDescription>
<?php
  } elseif (isset($plugin) && ((isset($_GET['query']) && preg_match('/(\w)(\d+)\.(\d+)/', strtoupper($_GET['query']), $matches)) || (isset($_GET['search']) && preg_match('/(\w)(\d+)\.(\d+)/', strtoupper($_GET['search']), $matches)))) {
    foreach($plugin as $value) {
      if (in_array($matches[1], explode('|', $value['code']))) {
        header('Location: ' . $config['server'] . $value['url'] . '/search?query=' . strtoupper($matches[0]));
        exit;
      }
    }
  } else {
    head('home');
print_r($_GET);
?>
         <div class="items">
           <h1><?php print _("Search"); ?><small><?php print _("Search through the databases"); ?></small></h1><br />
           <br />
         </div>
<?php
    foot();
  }
}else {
  header('Location: ' . $config['server']);
}
?>