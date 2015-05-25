<?php
$buffer[] = "\$config = array(
  'server'=>'http'.((!empty(\$_SERVER['HTTPS']) && (\$_SERVER['HTTPS']=='on'))?'s':'').'://".((strpos($_SERVER['SERVER_NAME'],':')!==false)?'[':'').$_SERVER['SERVER_NAME'].((strpos($_SERVER['SERVER_NAME'],':')!==false)?']':'').((dirname($_SERVER['SCRIPT_NAME'])!='/')?dirname($_SERVER['SCRIPT_NAME']):'')."',
  'powered' => '" . _VERSION . "',
  'db' => '" . trim($_POST['db']) . "',
  'gmap'=>'ABQIAAAAbsERbX0JYQ2efimaL55KMhTVtSzBuv6XzqxfMxPpnd0wzfffKBRfi3lYPgh7WZY4P3bBCiB_vlS4Fw',
  'tmp' => ((ini_get('upload_tmp_dir') != '')?ini_get('upload_tmp_dir'):'/tmp/'),
  'secure' => ".(isset($_POST['secure'])?"((!empty(\$_SERVER['SSL_CLIENT_VERIFY']) && !empty(\$_SERVER['SSL_CIPHER_USEKEYSIZE']) && !empty(\$_SERVER['SSL_CLIENT_S_DN_CN']) && !empty(\$_SERVER['SSL_SERVER_S_DN_CN']) && !empty(\$_SERVER['HTTPS']) && \$_SERVER['SSL_CLIENT_VERIFY'] == 'SUCCESS' && intval(\$_SERVER['SSL_CIPHER_USEKEYSIZE']) >= 128 && \$_SERVER['HTTPS'] == 'on')?true:false)":"false,\n  'unsafe' => true").",
  'login' => ((isset(\$_SESSION['login']['user_id']) && isset(\$_SESSION['login']['ip']) && isset(\$_COOKIE['user_id']) && (\$_SESSION['login']['user_id'] == strip_tags(trim(\$_COOKIE['user_id']))) && (\$_SESSION['login']['ip'] == \$_SERVER['REMOTE_ADDR']))?true:false),
  'sqlserver' => '" . trim($_POST['sqlserver']) . "',
  'sqlport' => '" . ((!empty($_POST['sqlport']) && (intval($_POST['sqlport']) > 0))?intval($_POST['sqlport']):'') . "',
  'sqlhost' => '" . (!empty($_POST['sqlhost'])?strtolower(trim($_POST['sqlhost'])):'localhost') . "',
  'sqlpassword' => '" . (!empty($_POST['sqlpassword'])?trim($_POST['sqlpassword']):'') . "',
  'sqllogin' => '" . (!empty($_POST['sqllogin'])?trim($_POST['sqllogin']):'') . "'
  );\n";
?>