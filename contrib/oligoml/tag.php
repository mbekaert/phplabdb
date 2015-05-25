<?php
ob_start("ob_gzhandler");
session_start();
require_once('../includes/main.inc');
require_once('../includes/dna.inc');

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

if ($config['login'] && !empty($_GET['tag']) && preg_match('/([OP])(\d+)\.(\d+)/', rawurldecode($_GET['tag']), $matches)) {
  $sql = sql_connect($config['db']);
  header('Content-type: image/png');
  header('Content-Disposition: inline; filename=' . rawurldecode($matches[0]) . '.png');
  $img = imagecreatefrompng('label.png');

  $white = imagecolorallocate($img, 128, 128, 128);
  $black = imagecolorallocate($img, 0, 0, 0);
  $grey = imagecolorallocate($img, 128, 128, 128);
  if ($matches[1] == 'O') {
    $result = sql_query('SELECT prefix, id, sequence, name FROM oligoml_oligo WHERE (prefix=' . octdec(intval($matches[2])) . ' AND id=' . octdec(intval($matches[3])) . ');', $sql);
    if ((!strlen($r = sql_last_error($sql))) && (sql_num_rows($result) == 1)) {
      $row = sql_fetch_row($result);
      imagestringup($img, 6, 3, 70, 'oligo', $white);
      imagestring($img, 2, 180, 25, _("ID:"), $grey);
      imagestring($img, 2, 205, 25, 'O' . decoct($row[0]) . '.' . decoct($row[1]) . ' (' . $row[3] . ')', $black);
      if (($semacode = get_semacode($config['server'] . '/O' . decoct($row[0]) . '.' . decoct($row[1]))) !== false) {
        imagecolortransparent($semacode, imagecolorallocate($semacode, 255, 255, 255));
        imagecopymerge($img, $semacode, 78, 1, 0, 0, 100, 100, 100);
      }
      imagestring($img, 2, 25, 25, _("GC:"), $grey);
      imagestring($img, 2, 50, 25, CG($row[2]) . '%', $black);
      imagestring($img, 2, 25, 40, _("TM:"), $grey);
      imagestring($img, 2, 50, 40, round(Tm($row[2]), 0) . '°', $black);
      imagestring($img, 2, 25, 55, _("MW:"), $grey);
      imagestring($img, 2, 50, 55, round(Mw($row[2]), 2), $black);
      imagestring($img, 1, 50, 67, _("g/mol"), $black);
      imagestring($img, 2, 180, 40, _("Sequence:"), $grey);
      imagestring($img, 2, 240, 40, substr($row[2], 0, 14), $black);
      if (strlen($row[2]) > 14) imagestring($img, 2, 180, 53, substr($row[2], 14, 24), $black);
      if (strlen($row[2]) > 38) imagestring($img, 2, 180, 66, substr($row[2], 38, 24), $black);
    }
  }elseif ($matches[1] == 'P') {
    $result = sql_query('SELECT a.prefix, a.id, a.forward_prefix, a.forward_id, b.name, a.reverse_prefix, a.reverse_id, c.name FROM oligoml_pair AS a, oligoml_oligo AS b, oligoml_oligo AS c WHERE (a.prefix=' . octdec(intval($matches[2])) . ' AND a.id=' . octdec(intval($matches[3])) . ' AND b.prefix=a.forward_prefix AND b.id=a.forward_id AND c.prefix=a.reverse_prefix AND c.id=a.reverse_id);', $sql);
    if ((!strlen($r = sql_last_error($sql))) && (sql_num_rows($result) == 1)) {
      $row = sql_fetch_row($result);
      if (($semacode = get_semacode($config['server'] . '/P' . decoct($row[0]) . '.' . decoct($row[1]))) !== false) {
        imagecolortransparent($semacode, imagecolorallocate($semacode, 255, 255, 255));
        imagecopymerge($img, $semacode, 48, 1, 0, 0, 100, 100, 100);
      }
      imagestringup($img, 6, 3, 65, _("pair"), $white);
      imagestring($img, 2, 155, 25, _("ID:"), $grey);
      imagestring($img, 2, 180, 25, 'P' . decoct($row[0]) . '.' . decoct($row[1]), $black);
      imagestring($img, 2, 155, 40, _("Forward:"), $grey);
      imagestring($img, 2, 210, 40, 'O' . decoct($row[2]) . '.' . decoct($row[3]) . ' (' . $row[4] . ')', $black);
      imagestring($img, 2, 155, 55, _("Reverse:"), $grey);
      imagestring($img, 2, 210, 55, 'O' . decoct($row[5]) . '.' . decoct($row[6]) . ' (' . $row[7] . ')', $black);
    }
  }
  imagepng($img);
  imagedestroy($img);
}
?>