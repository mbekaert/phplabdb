<?php
 ob_start("ob_gzhandler");
 session_start();
 include_once '../../includes/login.inc';
 function view_tree ($barcode,$mess,$x,$y,$dbconn,$dir) {
  $lev=error_reporting(8);
  $result=sql_query("SELECT barcode, ref, father, fbarcode, mother, mbarcode, crosstype FROM seeds WHERE barcode=$barcode;",$dbconn);
  error_reporting($lev);
  if(!(strlen($r=sql_last_error($dbconn))) && (sql_num_rows($result)==1)) {
   if ($dir[1]==0) {
    $mess['path'] .= '    <line x1="' . ($x-50) . '" y1="' . ($y+25) . '" x2="' . ($x-25) . '" y2="' . ($y+25) . '" stroke-width="2" />' . "\n";
    } else {
    $mess['path'] .= '    <line x1="' . ($x-25) . '" y1="' . ($y+25-(100*$dir[1])) . '" x2="' . ($x-25) . '" y2="' . ($y+25) . '" stroke-width="2" />' . "\n";
   };
   $dir[1]=0;
   $row=sql_fetch_row($result);
   $mess['box'] .= '    <a xlink:href="details.php?barcode='. urlencode($row[0]) . '"><rect x="' . $x . '" y="' . $y . '" width="150" height="50" rx="10" fill="' . $dir[0] . '" /><text x="' . ($x+75) . '" y="' . ($y+33) . '" font-family="Verdana" font-size="25px" style="text-anchor: middle;" fill="black" >' . $row[1] . "</text></a>\n";
   $mess['path'] .= '    <line x1="' . ($x-25) . '" y1="' . ($y+25) . '" x2="' . $x . '" y2="' . ($y+25) . '" stroke-width="2" />' . "\n";
   if ($row[6]!=1) {
    if (isset($row[3])) {
     list($mess,$y,$dir[1])=view_tree($row[3],$mess,$x+200,$y,$dbconn,array('lightblue',0));
     $y+=100;
     $dir[1]++;
     } elseif (isset($row[2])) {
     $mess['box'] .= '    <rect x="' . ($x+200) . '" y="' . $y . '" width="150" height="50" rx="10" fill="lightblue" /><text x="' . ($x+275) . '" y="' . ($y+33) . '" font-family="Verdana" font-size="25px" style="text-anchor: middle;" fill="black" >' . $row[2] . "</text>\n";
     $mess['path'] .= '    <line x1="' . ($x+125) . '" y1="' . ($y+25) . '" x2="' . ($x+200) . '" y2="' . ($y+25) . '" stroke-width="2" />' . "\n";
     $y+=100;
     $dir[1]++;
    };
   };
   if (isset($row[5])) {
    list($mess,$y,$dir[1])=view_tree($row[5],$mess,$x+200,$y,$dbconn,array('lightpink',$dir[1]));
    $dir[1]+=2;
    } elseif (isset($row[4])) {
    if ($dir[1]==0) {
     $mess['path'] .= '    <line x1="' . ($x+125) . '" y1="' . ($y+25) . '" x2="' . ($x+175) . '" y2="' . ($y+25) . '" stroke-width="2" />' . "\n";
     } else {
     $mess['path'] .= '    <line x1="' . ($x+175) . '" y1="' . ($y+25-(100*$dir[1])) . '" x2="' . ($x+175) . '" y2="' . ($y+25) . '" stroke-width="2" />' . "\n";
    };
    $mess['box'] .= '    <rect x="' . ($x+200) . '" y="' . $y . '" width="150" height="50" rx="10" fill="lightpink" /><text x="' . ($x+275) . '" y="' . ($y+33) . '" font-family="Verdana" font-size="25px" style="text-anchor: middle;" fill="black" >' . $row[4] . "</text>\n";
    $mess['path'] .= '    <line x1="' . ($x+175) . '" y1="' . ($y+25) . '" x2="' . ($x+200) . '" y2="' . ($y+25) . '" stroke-width="2" />' . "\n";
   };
  };
  return array($mess,$y,$dir[1]);
 };
 if(isset($_GET['barcode'])) $barcode=intval(rawurldecode($_GET['barcode']));
 header ("Expires: " . gmdate("D, d M Y H:i:s") . " GMT");
 header('Cache-Control: no-store, no-cache');
 $plugin=$plugin_dir['seeddb'];
 if (isset($barcode)) {

  $dbconn=sql_connect($plugin_db['seeddb']);
  list($mess,$y,$dir)=view_tree($barcode,array(),0,0,$dbconn,array('beige',0));
  header('Content-type: image/svg+xml');
  print "<?xml version='1.0' encoding='iso-8859-1' standalone='yes'?>\n";
?>
<!DOCTYPE svg PUBLIC "-//W3C//DTD SVG 1.1//EN"
  "http://www.w3.org/Graphics/SVG/1.1/DTD/svg11.dtd">
<svg width="12cm" height="12cm" viewBox="0 0 1200 1200" preserveAspectRatio="xMinYMin" xmlns="http://www.w3.org/2000/svg" version="1.1">
  <g id="path" stroke="black">
<?php print $mess['path']; ?>
  </g>
  <g id="box">
<?php print $mess['box']; ?>
  </g>
</svg>
<?php }; ?>