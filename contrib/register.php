<?php
ob_start("ob_gzhandler");
session_start();
require_once('includes/main.inc');
mt_srand(time());

head('login');
if (!empty($_POST['register']) && ($_POST['register'] == md5('register' . floor(intval(date('b')))))) {
  if (!empty($_POST['firstname']) && strlen(strip_tags(trim($_POST['firstname']))) > 2 && !empty($_POST['lastname']) && strlen(strip_tags(trim($_POST['lastname']))) > 2 && !empty($_POST['email']) && strlen(strip_tags(trim($_POST['email']))) > 6 && stristr(strip_tags(trim($_POST['email'])), '@') && stristr(strip_tags(trim($_POST['email'])), '.') && !empty($_POST['username']) && strlen(strip_tags(trim($_POST['username']))) > 2 && !empty($_POST['password']) && strlen(strip_tags(trim($_POST['password']))) > 6 && !empty($_POST['password2']) && md5(strip_tags(trim($_POST['password']))) == md5(strip_tags(trim($_POST['password2']))) && strip_tags(trim($_POST['password'])) != strip_tags(trim($_POST['username'])) && !empty($_POST['organism']) && strlen(strip_tags(trim($_POST['organism']))) > 2 && (empty($config['organisation']['voucher']) || (!empty($_POST['voucher']) && strip_tags(trim($_POST['voucher'])) == sha1($config['organisation']['voucher'] . strip_tags(trim($_POST['email'])) . $config['server'])))) {
    $sql = sql_connect($config['db']);
    $activated = uniqid(mt_rand(), true);
    $result = sql_query('INSERT INTO users (username,real_name,password,email,taxon,active) VALUES (\'' . addslashes(strtolower(strip_tags(trim($_POST['username'])))) . '\',\'' . addslashes(strip_tags(trim($_POST['firstname']))) . ' ' . addslashes(strip_tags(trim($_POST['lastname']))) . '\',\'' . md5(strip_tags(trim($_POST['password']))) . '\',\'' . addslashes(strip_tags(trim($_POST['email']))) . '\',\'' . addslashes(strip_tags(trim($_POST['organism']))) . '\',\'' . md5($activated) . '\');', $sql);
    if (!strlen($r = sql_last_error($sql))) {
      $subject = $config['powered'] . ': Verify Your Email Address';
      $message = 'Hello ' . strip_tags(trim($_POST['firstname'])) . ",\n\nBefore you can use your account, you have to\nverify that your email address is correct by clicking the link below\n\n" . $config['server'] . '/activate/' . $activated . "\n\nOnce we have verified your email address, your account will be\nactivated.\n\n** This is an automated response, please do not reply! **\n\n--\n" . $config['powered'] . "\n" . $config['server'] . "\n";
      $headers = 'From: ' . $config['powered'] . ' <' . $_SERVER['SERVER_ADMIN'] . '>';
      $mailsend = @mail(strip_tags(trim($_POST['email'])), $subject, $message, $headers);
      $notice = '           <p>' . _("Before you can use your account, we have to verify that your email address is correct. You will receved an email, follow informations provided. Once you have verified your email address, your account will be activated.") . "</p>\n";
    }else {
      $error = _("The username/email already exists!");
    }
  }else {
    $error = _("All fields have not been given!");
  }
}
?>
         <div class="items">
           <h1><?php print _("Register"); ?><small><?php print _("Register to get a full access"); ?></small></h1><br />
<?php if (isset($notice)) {
  print $notice;
}else {
?>
          <form method="post" action="<?php print $config['server']; ?>/register">
          <div>
            <?php print _("All fields are mandorited to allow you to login."); ?><br /><br />
<?php print (isset($error)?'            <strong>' . $error . "</strong><br /><br />\n":''); ?>
            <div>
              <label for="firstname"><strong><?php print _("First name"); ?></strong></label>
              <input name="firstname" id="firstname" type="text" maxlength="32" title="<?php print _("Your first name"); ?>"<?php print (!empty($_POST['firstname'])?' value="' . strip_tags(trim($_POST['firstname'])) . '"':''); ?> />
              <br />
            </div>
            <div>
              <label for="lastname"><strong><?php print _("Last name"); ?></strong></label>
              <input name="lastname" id="lastname" type="text" maxlength="32" title="<?php print _("Your last name"); ?>"<?php print (!empty($_POST['lastname'])?' value="' . strip_tags(trim($_POST['lastname'])) . '"':''); ?> />
              <br />
            </div>
            <div>
              <label for="username"><strong><?php print _("Username"); ?></strong></label>
              <input name="username" id="username" type="text" maxlength="32" title="<?php print _("login/username"); ?>"<?php print (!empty($_POST['username'])?' value="' . strip_tags(trim($_POST['username'])) . '"':''); ?> />
              <br />
            </div>
            <div>
              <label for="email"><strong><?php print _("E-mail"); ?></strong></label>
              <input name="email" id="email" type="text" maxlength="128" title="<?php print _("Your email address"); ?>"<?php print (!empty($_POST['email'])?' value="' . strip_tags(trim($_POST['email'])) . '"':(!empty($_GET['email'])?' value="' . strip_tags(trim($_GET['email'])) . '"':'')); ?> />
              <br />
            </div>
            <div>
              <label for="password"><strong><?php print _("Password"); ?></strong></label>
              <input name="password" id="password" type="password" maxlength="32" title="<?php print _("Your login password (more than 6 characters)"); ?>"<?php print (!empty($_POST['password'])?' value="' . strip_tags(trim($_POST['password'])) . '"':''); ?> />
              <br />
            </div>
            <div>
              <label for="password2"><strong><?php print _("Repeat password"); ?></strong></label>
              <input name="password2" id="password2" type="password" maxlength="32" title="<?php print _("Your login password for verification"); ?>"<?php print (!empty($_POST['password2'])?' value="' . strip_tags(trim($_POST['password2'])) . '"':''); ?> />
              <br />
            </div>
<?php if (!empty($config['organisation']['voucher'])) {
?>
            <div>
              <label for="voucher"><strong><?php print _("Voucher number"); ?></strong></label>
              <input name="voucher" id="voucher" type="password" maxlength="64" title="<?php print _("Your voucher number"); ?>"<?php print (!empty($_POST['voucher'])?' value="' . strip_tags(trim($_POST['voucher'])) . '"':(!empty($_GET['voucher'])?' value="' . strip_tags(trim($_GET['voucher'])) . '"':'')); ?> />
              <br />
            </div>
<?php }
?>
            <div>
              <label for="organism"><?php print _("Taxonomic group(s) you work on"); ?></label>
              <input name="organism" id="organism" type="text" maxlength="128" title="<?php print _("Family or specie names"); ?>"<?php print (!empty($_POST['organism'])?' value="' . strip_tags(trim($_POST['organism'])) . '"':''); ?> />
              <small>&nbsp;<?php print _("We are curious"); ?></small>
              <br />
            </div>
            <br />
            <strong><?php print _("Notice:"); ?> </strong><?php print _("Before you can use your account, we have to verify that your email address is correct. You will receved an email, follow informations provided. Once you have verified your email address, your account will be activated."); ?><br />
            <br />
            <input type="hidden" name="register" value="<?php print md5('register' . floor(intval(date('b')))); ?>" />
            <input type="reset" value="<?php print _("Clear"); ?>" />&nbsp;<input type="submit" value="<?php print _("Register"); ?>" />
          </div>
          </form>
<?php
}
?>
         </div>
<?php
foot();
?>