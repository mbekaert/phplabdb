<?php
 ob_start("ob_gzhandler");
 include_once 'includes/login.inc';
 if (!empty($_POST['login'])) {
  $sql=sql_connect('db_phplabdb');
  $result=sql_query("SELECT email, password FROM membres WHERE login='" . $_POST['login'] . "';",$sql);
  if (sql_num_rows($result)==1) {
   $row=sql_fetch_row($result);
   mail("$row[0]", "phpLabDB password", _("Here's how you can log in at") . ' http://' . $_SERVER['SERVER_ADDR'] . $base_url . "/login.php\n" . _("Username/login") . ':  ' . $_POST['login'] . "\n" . _("Email") . ":  $row[0]\n" . _("Password") . ":  $row[1]\n\n** " . _("Passwords are case sensitive") . " **\n\n\n" . _("If you did not request your password, please forward this email to") . ' ' . $_SERVER['SERVER_ADMIN'] . ' ' . _("immediately") . "\n" . _("Logged IP") . ':  ' . $_SERVER['REMOTE_ADDR'],"from: Webmaster");
   $msg=1;
  };
 };
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
          <?php print _("Forgot Your Password?"); ?> 
         </h1>
<?php if (isset($msg)) { ?>
         <p>
           <?php print _("You will received your password by email"); ?>.
         </p>
<?php } else { ?>
         <p>
           <?php print _("Type your username"); ?>.
         </p>
         <hr>
         <h1>
           <?php print _("Account on") . ' <em>' . $_SERVER['SERVER_NAME'] .'</em> ' . _("server"); ?> 
         </h1>
          <form action="<?php print $base_url; ?>forgot.php" method="post">
            <table>
              <tr>
                <td>
                  <label for="login"><?php print _("Username"); ?></label>
                </td>
                <td>
                  <input type="text" size="20" maxlength="20" name="login" id="login">
                </td>
              </tr>
              <tr>
                <td colspan="2">
                  <input name="clear" type="reset" value="<?php print _("Clear"); ?>"> &nbsp; <input type="submit" name="submit" value="<?php print _("Submit"); ?>">
                </td>
              </tr>
            </table>
          </form>
<?php }; ?>        </div>
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
