<?php
ob_start("ob_gzhandler");
session_start();
require_once('../includes/main.inc');

function get_semacode ($url) {
  global $config;
  $tmpfname = $config['tmp'] . uniqid('') . '.png';
  exec("java -Djava.awt.headless=true -jar ../scripts/semacode_tag.jar -u '$url' -f $tmpfname -w 100", $output, $error);
  if ($error == 0) {
    $img = imagecreatefrompng($tmpfname);
    unlink($tmpfname);
    return $img;
  }
  return false;
}

if ($config['login'] && !empty($_GET['semacode']) && preg_match('/([OP])(\d+)\.(\d+)/', rawurldecode($_GET['semacode']), $matches)) {
  $url = '/' . $matches[0];
  if (isset($url) && ($semacode = get_semacode($config['server'] . $url)) !== false) {
    header('Content-type: image/png');
    imagepng($semacode);
    imagedestroy($semacode);
  }
}
?>