<?php
 ob_start("ob_gzhandler");
 include_once 'includes/login.inc';
 header_start();
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.01//EN">
<html>
  <head>
    <title>
      ..:: phpLabDB ::..
    </title>
    <meta http-equiv="Content-Type" content="text/html">
    <link rel="stylesheet" type="text/css" media="print" href="<?php print $base_url; ?>css/print.css">
    <link rel="stylesheet" type="text/css" media="screen" href="<?php print $base_url; ?>css/screen.css">
    <style type="text/css">
     .imageleft {float:left;margin:0 15px;}
    </style>
  </head>
  <body>
    <div id="header">
      <div id="header-logo">
        <?php print "<a href=\"$organisation[1]\"><img src=\"$organisation[2]\" alt=\"$organisation[0]\"></a>"; ?> 
      </div>
      <div id="header-items">
        <span class="header-icon"><?php print '<a href="' . $base_url .'lang.php"><img src="' . $base_url . 'images/header-langs.png" alt="">' . _("Language") . '</a>'; ?></span>
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
          <strong><?php print '<a href="' . $base_url . '">' . _("Home") . '</a>'; ?></strong>
        </li>
        <li>
          <?php print '<a href="' . $base_url . 'about.php">' . _("About") . '</a>'; ?> 
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
      <div id="content">
        <div id="page-main">
         <h1>
           <?php print _("Access Deny"); ?> 
         </h1>
          <p>
            <img src="<?php print $base_url; ?>images/error.png" alt="" width="79" height="79" class="imageleft"><br>
            <strong><?php print _("You are not authorized to access this server"); ?></strong>. <?php print _("You need a username and a password. Try") . ' <a href="' . $base_url . 'forgot.php">' . _("Password retriever") . '</a>. ' . _("Or contact the") . ' <a href="mailto:' . $_SERVER['SERVER_ADMIN'] . '">' . _("webmaster") . '</a>'; ?>.
          </p>
          <p>
            &nbsp;
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
    <div id="side-right">
      <div class="side-right-content">
        <h1>
          <?php print _("Help"); ?> 
        </h1>
        <ul>
          <li>
            <?php print '<a href="' . $base_url . 'login.php">' . _("Login") . '</a>'; ?> 
          </li>
          <li>
            <?php print '<a href="' . $base_url . 'forgot.php">' . _("Password retriever") . '</a>'; ?> 
          </li>
        </ul>
      </div>
    </div>
    <div id="footer">
      - <?php print "<a href=\"$organisation[1]\">$organisation[0]</a> " . _("powered by"); ?> <a href="http://sourceforge.net/projects/phplabdb/">phpLabDB</a> -<br>
       &nbsp;<br>
    </div>
  </body>
</html>
