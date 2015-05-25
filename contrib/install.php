<?php
/* install.php */
/* phpLabDB installer (v0.1) */

/* Distribution package */
define('_VERSION', 'phpLabDB v1.201 (2007-02-01)');
$plugins=array('phplabdb','oligoml','tree','darwin','uniprime');

mt_srand(time());

function lookdir($dir) {
  if (is_dir($dir) && ($dh = opendir($dir))) {    while (($file = readdir($dh)) !== false) {
      if ($file!=='..' && $file!=='.') {
        if (is_dir($dir .'/'. $file)) {
          lookdir($dir .'/'. $file);
          @chmod($dir .'/'. $file, 0110);
        } else {
          @chmod($dir .'/'. $file, 0440);
        }      }
    }    closedir($dh);
  }
}

if (!empty($_POST['addict']) && (intval($_POST['addict']) == 3)) {
  $step=2;
  if (!empty($_POST['email']) && (strlen(strip_tags(trim($_POST['email']))) > 6) && stristr(strip_tags(trim($_POST['email'])), '@') && stristr(strip_tags(trim($_POST['email'])), '.') && !empty($_POST['username']) && (strlen(strip_tags(trim($_POST['username']))) > 2)) {
    @include_once('includes/main.inc');
    if (isset($config)) {
      $sql = sql_connect($config['db']);
      if (isset($sql)) {
        $salt = array('a', 'b', 'c', 'd', 'e', 'f', 'g', 'h', 'j', 'k', 'm', 'n', 'p', 'q', 'r', 's', 't', 'u', 'v', 'w', 'x', 'y', 'z', 'A', 'B', 'C', 'E', 'F', 'G', 'H', 'J', 'K', 'L', 'M', 'N', 'P', 'Q', 'R', 'S', 'T', 'U', 'V', 'W', 'X', 'Y', 'Z', '2', '3', '4', '5', '6', '7', '8', '9', '#', '_', '$', '*', '!', '=', '-', '+', '@');
        $random_password = '';
        for ($i = 0; $i < 10; $i++) $random_password .= $salt[mt_rand(0, count($salt)-1)];
        $result=@sql_query("INSERT INTO users (username, real_name, password, email, taxon, code, rights, activated, active) VALUES ('".addslashes(strtolower(strip_tags(trim($_POST['username']))))."', '','".md5($random_password)."', '".addslashes(strtolower(strip_tags(trim($_POST['email']))))."', '', '@', 9, 2, NULL);",$sql);
        if (strlen($r = sql_last_error($sql))) {
          $error = 'SQL server connexion error';
        }
      } else {
        $error = 'SQL server connexion error';
      }   
    } else {
      $error = 'Panic';
    }
  } else {
    $error = 'Data are missing';
  }
}elseif (!empty($_POST['addict']) && (intval($_POST['addict']) == 2)) {
  $step=1;
  if (!empty($_POST['db']) && !empty($_POST['sqlserver'])) {
    if ($_POST['sqlserver'] == 'postgresql') {
      require_once('includes/postgresql.inc');
    }elseif ($_POST['sqlserver'] == 'mysql') {
      require_once('includes/mysql.inc');
    }else {
      $error = 'SQL server unknown';
    }
    if (empty($error)) {
      $config = array('db' => trim($_POST['db']), 'sqlserver' => $_POST['sqlserver'], 'sqlhost' => (!empty($_POST['sqlhost'])?strtolower(trim($_POST['sqlhost'])):'localhost'), 'sqlport' => ((!empty($_POST['sqlport']) && (intval($_POST['sqlport']) > 0))?intval($_POST['sqlport']):''), 'sqlpassword' => (!empty($_POST['sqlpassword'])?trim($_POST['sqlpassword']):''), 'sqllogin' => (!empty($_POST['sqllogin'])?trim($_POST['sqllogin']):''));
	  $sql = sql_connect((($config['sqlserver']=='postgresql')?'postgres':'mysql'));
      if (isset($sql)) {
        $result = @sql_query('CREATE DATABASE ' . $config['db'] . ';', $sql);
        if (!strlen($r = sql_last_error($sql))) {
          $sql = sql_connect($config['db']);
          foreach($plugins as $plugin) {
            if (is_readable('dist/'.$_POST['sqlserver'].'_'.$plugin.'.sql') && is_readable('dist/'.$plugin.'.inc') && is_readable((($plugin!='phplabdb')?$plugin.'/':'').'.htaccess') && is_writable((($plugin!='phplabdb')?$plugin.'/':'').'.htaccess') && ($sqlfile=file_get_contents('dist/'.$_POST['sqlserver'].'_'.$plugin.'.sql')) && ($htaccess=file_get_contents((($plugin!='phplabdb')?$plugin.'/':'').'.htaccess'))) {
              foreach(explode(";\n",$sqlfile) as $line) {
               $result = @sql_query( $line. ';', $sql);
              }
              include('dist/'.$plugin.'.inc');
              @unlink('dist/'.$plugin.'.inc');
              @unlink('dist/postgres_'.$plugin.'.sql');
              @unlink('dist/mysql_'.$plugin.'.sql');
              file_put_contents((($plugin!='phplabdb')?$plugin.'/':'').'.htaccess', preg_replace('/RewriteBase \/phplabdb(.*)\n/', 'RewriteBase ' . dirname($_SERVER['SCRIPT_NAME']) . "$1\n", $htaccess));
            }
          }
          if(isset($buffer)) {
            file_put_contents('includes/config.inc','<'."?php\n".implode("\n",$buffer).'?'.'>');
            touch('includes/website.inc');
            @rmdir('dist');
            lookdir('.');
            @chmod('includes/website.inc', 0640);
            @chmod('includes/config.inc', 0400);
            @chmod(__FILE__, 0600);
          } else {
            $result = @sql_query('DROP DATABASE ' . $config['db'] . ';', $sql);
            $error = 'Installation file not readable???';
          }
        } else {
          $error = 'Database not ready for installation';
        }
      } else {
        $error = 'SQL server connexion error';
      }
    }
  } else {
    $error = 'SQL server connexion error';
  }
} 

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN"
    "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html dir='ltr' xmlns="http://www.w3.org/1999/xhtml">
  <head>
    <title>
      phpLabDB Installer
    </title>
    <meta http-equiv="Expires" content="0" />
    <meta http-equiv="cache-control" content="no-cache,no-store" />
    <meta http-equiv="pragma" content="no-cache" />
    <meta http-equiv="Content-Type" content="text/html; charset=us-ascii" />
<style type="text/css">
/*<![CDATA[*/
body { font-family: Georgia,Garamond,Times,serif; background-color: white; color: black; margin: 4em; text-align: center; }
#main { width: 25em; text-align: left; }
h1 { font-size: 110%; font-weight: bold; color: #970038; }
label { font-size: 90%; }
input[type=text], select { width: 10em; }
.right { text-align: right; }
/*]]>*/
</style>
  </head>
  <body>
    <div id="main">
      <h1>
	<?php print _VERSION; ?>
      </h1>
<?php

if (isset($step) && ($step==2) && !isset($error) && isset($random_password)) {
?>
      <p>
        <strong>Installation done.</strong>
      </p>
      <p>
        Your username: <?php print strtolower(strip_tags(trim($_POST['username']))); ?><br />
        Your temporary password: <?php print $random_password; ?><br />
      </p>
      <p>
        Please <?php print (isset($config['unsafe'])?'':'set-up your certificate, securly ')?><a href=".">log-in</a> and change your password. 
      </p>
<?php
  @unlink (__FILE__);
  chmod('.', 0100);
}elseif (isset($step) && (($step==2) || (($step==1) && !isset($error))) ) {
?>
      <p>
        <strong>Tuning your installation.</strong>
      </p>
      <p>
        Set-up the administrative account.
      </p>
      <p>
        <?php print (isset($error)?'<strong>'.$error.'</strong>':'Please field the form and click on the button to continue'); ?>.
      </p>
      <div class="right">
        <form action="<?php print basename($_SERVER['PHP_SELF']); ?>" method="post">
        <label for="username" accesskey="u"><strong>Username</strong></label>&nbsp;<input type="text" name="username" id="username" title="Administrator login" value="<?php print ((isset($_SERVER["SERVER_ADMIN"]) && stristr(strip_tags(trim($_SERVER["SERVER_ADMIN"])), '@') && stristr(strip_tags(trim($_SERVER["SERVER_ADMIN"])), '.'))?substr($_SERVER["SERVER_ADMIN"], 0, strpos($_SERVER["SERVER_ADMIN"], '@')):''); ?>" /><br />
          <label for="email" accesskey="e"><strong>Email</strong></label>&nbsp;<input type="text" name="email" id="email" title="Administrator email" value="<?php print ((isset($_SERVER["SERVER_ADMIN"]) && stristr(strip_tags(trim($_SERVER["SERVER_ADMIN"])), '@') && stristr(strip_tags(trim($_SERVER["SERVER_ADMIN"])), '.'))?$_SERVER["SERVER_ADMIN"]:''); ?>" /><br />
          <input type="hidden" name="addict" value="3" /><input type="submit" accesskey="f" value="Finish &gt;&gt;" />
        </form>
      </div>
<?php
} else {
?>
      <p>
        <strong>Tuning your installation.</strong>
      </p>
      <p>
        First, the program will set-up the database and the configuration, and then adjust the file permissions.
      </p>
      <p>
        <?php print (isset($error)?'<strong>'.$error.'</strong>':'Please field the form and click on the button to continue'); ?>.
      </p>
      <div class="right">
        <form action="<?php print basename($_SERVER['PHP_SELF']); ?>" method="post">
          <label for="secure" accesskey="c">Request SSL and certificate for the administrators</label>&nbsp;<input type="checkbox" name="secure" id="secure" checked="checked" /><br /><br />
          <label for="db" accesskey="n"><strong>SQL database name</strong></label>&nbsp;<input type="text" name="db" id="db" title="The full database name" value="phplabdb" /><br />
          <label for="sqlserver" accesskey="t"><strong>SQL server type</strong></label>&nbsp;<select name="sqlserver" id="sqlserver" title="Select your SQL server type"><option value="postgresql" selected="selected">PostgreSQL</option><option value="mysql">MySQL</option></select><br />
          <label for="sqlhost" accesskey="a">SQL host address</label>&nbsp;<input type="text" name="sqlhost" id="sqlhost" title="The full SQL IP address - leave empty for localhost" /><br />
          <label for="sqlport" accesskey="p">SQL port address</label>&nbsp;<input type="text" name="sqlport" id="sqlport" title="The SQL port (e.g. 5432 [PostgreSQL], or 3307 [MySQL])" /><br />
          <label for="sqllogin" accesskey="l">SQL login</label>&nbsp;<input type="text" name="sqllogin" id="sqllogin" title="The SQL login name of your webserver (e.g. www, apache, etc.)" /><br />
          <label for="sqlpassword" accesskey="p">SQL password</label>&nbsp;<input type="text" name="sqlpassword" id="sqlpassword" title="The SQL password (e.g. 123456789)" /><br />
          <input type="hidden" name="addict" value="2" /><input type="submit" accesskey="f" value="Next &gt;&gt;" />
        </form>
      </div>
<?php
}
?>
    </div>
  </body>
</html>