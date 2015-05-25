<?php
ob_start("ob_gzhandler");
session_start();
require_once('includes/main.inc');
mt_srand(time());

if ($config['login'] && ($_SESSION['login']['right'] == 9)) {
  if (!empty($_POST['edit']) && ($_POST['edit'] == md5('website' . floor(intval(date('b'))))) && (empty($_POST['logo']) || is_readable(strip_tags(trim($_POST['logo'])))) && (empty($_POST['url']) || is_readable(strip_tags(trim($_POST['url']))))) {
    $buffer = '<' . '?php $config[\'organisation\']=array(';
    if (!empty($_POST['name']) && (strlen(strip_tags(trim($_POST['name']))) > 5)) {
      $buffer .= "'name' => '" . strip_tags(trim($_POST['name'])) . "', ";
    }
    if (!empty($_POST['url'])) {
      $buffer .= " 'url' => '" . strip_tags(trim($_POST['url'])) . "', ";
    }
    if (!empty($_POST['welcome']) && (strlen(strip_tags(trim($_POST['welcome']))) > 5)) {
      $buffer .= "'welcome' => '" . strip_tags(trim($_POST['welcome'])) . "', ";
    }
    if (!empty($_POST['logo'])) {
      $buffer .= "'logo' => '" . strip_tags(trim($_POST['logo'])) . "', ";
    }
    if (!empty($_POST['voucher'])) {
      $buffer .= "'voucher' => '" . str_pad(mt_rand(0x000001, 0xffffff), 6, '0', STR_PAD_LEFT) . "'";
    }
    $buffer .= '); ?' . '>';
    if (@file_put_contents('includes/website.inc', $buffer)) {
      header('Location: ' . $config['server'] . '/my');
      exit;
    }else {
      $error = _("Configuration file not rewritable!");
    }
  }
  head('admin');
?>
        <div class="items">
          <h1><?php print _("Website"); ?><small><?php print _("Website manager"); ?></small></h1><br />
          <form method="post" action="<?php print $config['server']; ?>/website">
          <div>
            <?php print _("You can customize the website (including an organisation name, and logo) in order to keep it up-to-date."); ?><br /><br />
<?php print (isset($error)?"            <strong>" . $error . "</strong><br /><br />\n":''); ?>
            <div>
              <label for="name"><?php print _("Organisation name"); ?></label>
              <input name="name" id="name" type="text" maxlength="128"<?php print (!empty($_POST['name'])?' value="' . strip_tags(trim($_POST['name'])) . '"':(isset($config['organisation']['name'])?' value="' . $config['organisation']['name'] . '"':'')); ?> title="<?php print _("The name of your organisation"); ?>" />
              <br />
            </div>
            <div>
              <label for="url"><?php print _("Organisation URL"); ?></label>
              <input name="url" id="url" type="text" maxlength="128"<?php print (!empty($_POST['url'])?' value="' . strip_tags(trim($_POST['url'])) . '"':(isset($config['organisation']['url'])?' value="' . $config['organisation']['url'] . '"':'')); ?> title="<?php print _("URL of your main website (complete address, e.g. http://www.example.com/)"); ?>" />
              <br />
            </div>
            <div>
              <label for="png"><?php print _("Logo (236px x 138px)"); ?></label>
              <input name="logo" id="png" type="text" maxlength="128"<?php print (!empty($_POST['logo'])?' value="' . strip_tags(trim($_POST['logo'])) . '"':(isset($config['organisation']['logo'])?' value="' . $config['organisation']['logo'] . '"':'')); ?> title="<?php print _("URL of your organisation logo (complete address, e.g. http://www.example.com/logo.png)"); ?>" />
              <br />
            </div>
            <div>
              <label for="welcome"><?php print _("Welcome message"); ?></label>
              <textarea name="welcome" id="welcome" rows="4" cols="30" title="<?php print _("Welcome message of the homepage"); ?>"><?php print (!empty($_POST['welcome'])?strip_tags(trim($_POST['welcome'])):(isset($config['organisation']['welcome'])?$config['organisation']['welcome']:'')); ?></textarea>
              <br />
            </div>
            <div>
              <label for="voucher"><?php print _("Request a voucher"); ?></label>
              <input name="voucher" id="voucher" type="checkbox"<?php print (!empty($_POST['voucher'])?' checked="checked"':(!empty($config['organisation']['voucher'])?' checked="checked"':'')); ?> title="<?php print _("Is a voucher number require before register?"); ?>" />
              <br />
            </div>
            <br />
            <input type="hidden" name="edit" value="<?php print md5('website' . floor(intval(date('b')))); ?>" />
            <input type="reset" value="<?php print _("Clear"); ?>" />&nbsp;<input type="submit" value="<?php print _("Update"); ?>" />
          </div>
          </form>
        </div>
<?php
  foot();
}else {
  header('Location: ' . $config['server']);
}
?>