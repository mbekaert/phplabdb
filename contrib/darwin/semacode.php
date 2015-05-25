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
  }else {
    print_r($output);
  }
  return false;
}

if ($config['login'] && !empty($_GET['semacode'])) {
  if (($semacode = get_semacode(str_rot13($_GET['semacode']))) !== false) {
    header('Content-type: image/png');
    imagepng($semacode);
    imagedestroy($semacode);
  }
}
?>