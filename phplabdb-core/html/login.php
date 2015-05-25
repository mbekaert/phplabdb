<?php
 ob_start("ob_gzhandler");
 mt_srand(time());
 include_once 'includes/login.inc';
 if (!empty($_POST['login']) && !empty($_POST['password'])) {
  $login=$_POST['login'];
  $password=$_POST['password'];
  $sql=sql_connect('db_phplabdb');
  $result=sql_query("SELECT status FROM membres WHERE login='$login' AND password='$password';",$sql);
  if ($result && (sql_num_rows($result)==1)) {
   $userid=md5(uniqid(mt_rand()));
   setcookie('user_id',"$userid", mktime(1,0,0,date("m"),date("d"),date("Y")+1),'/');
   $result=sql_query("UPDATE membres SET ip='" . $_SERVER['REMOTE_ADDR'] . "', id='$userid' WHERE login='$login' AND password='$password';",$sql);
   header("Location: " . $base_url);
   exit;
  } else {
   setcookie('user_id','',0,'/');
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
          <?php print _("Login"); ?> 
         </h1>
         <p>
           <?php print _("You need a username and a password. Try") . ' <a href="' . $base_url . 'forgot.php">' . _("Password retriever") . '</a>. ' . _("Or contact the") . ' <a href="mailto:' . $_SERVER['SERVER_ADMIN'] . '">' . _("webmaster") . '</a>.' ?> 
         </p>
         <hr>
         <h1>
          <?php print _("Login to") . ' <em>' . $_SERVER['SERVER_NAME'] . '</em> ' . _("server"); ?> 
         </h1>
           <form action="<?php print $base_url; ?>login.php" method="post">
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
                <td>
                  <label for="password"><?php print _("Password"); ?></label>
                </td>
                <td>
                  <input type="password" size="20" maxlength="20" name="password" id="password">
                </td>
              </tr>
              <tr>
                <td colspan="2">
                  <input name="clear" type="reset" value="<?php print _("Clear"); ?>"> &nbsp; <input type="submit" name="submit" value="<?php print _("Submit"); ?>">
                </td>
              </tr>
            </table>
          </form>
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
