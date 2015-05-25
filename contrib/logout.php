<?php
ob_start("ob_gzhandler");
session_start();
require_once('includes/config.inc');

unset($_SESSION['login']);
session_unset();
session_destroy();
setcookie('user_id', '', 0, dirname($_SERVER['REQUEST_URI']));
setcookie(session_name(), '', 0, '/');
header('Location: ' . $config['server'] . '/');
exit;
?>