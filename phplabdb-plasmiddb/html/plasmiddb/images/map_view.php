<?php 
 ob_start("ob_gzhandler");
 session_start();
 include_once '../../includes/login.inc';
 $plasmide=stripslashes(rawurldecode($_GET['plasmid']));
 header ("Expires: " . gmdate("D, d M Y H:i:s") . " GMT"); 
 header('Cache-Control: no-store, no-cache');
 if (!(empty($plasmide))) {
  $dbconn=sql_connect($plugin_db['plasmiddb']); 
  $lev=error_reporting(8);
  $result=sql_query("SELECT a.name, b.length, a.markers, a.enzymes FROM map as a, prototype as b WHERE (a.name='" . addslashes($plasmide) . "' AND b.name='" . addslashes($plasmide) . "');",$dbconn);
  error_reporting($lev);
  if((strlen ($r=sql_last_error($dbconn))) || (sql_num_rows($result)!=1)) {
   print "          <p><strong>Oops</strong></p><p>$r</p>\n";
  } else {
   header ("Content-type: image/svg+xml");
   print "<?xml version='1.0' encoding='iso-8859-1' standalone='yes'?>\n";
   $row=sql_fetch_row($result);
   $size=intval($row[1]);
?>
<!DOCTYPE svg PUBLIC "-//W3C//DTD SVG 20001102//EN"
  "http://www.w3.org/TR/2000/CR-SVG-20001102/DTD/svg-20001102.dtd">
<svg width="600" height="400">
<script type="text/ecmascript">
//<![CDATA[
 var shown = 1;
 function click() {
  var seq = document.getElementById("enzyme");
  if (shown==1) {
   seq.setAttribute("style", "visibility: hidden");
   shown = 0;
  } else {
   seq.setAttribute("style", "visibility: visible");
   shown = 1;
  };
 };
 //]]>
</script>
<rect style="stroke: white; fill: white" height="400" x="0" y="0" width="600" onclick="click()" />
<circle cx="300" cy="200" r="150" style="fill:none;stroke:black;stroke-width:0.5" />
<text x="300" y="200" style="text-anchor:middle; font-size:12px; font-weight:bold; font-family:Arial;"><?php print $row[0]; ?></text>
<text x="300" y="215" style="text-anchor:middle; font-size:10px; font-family:Arial;"><?php print $size . ' ' . _("bp"); ?></text>
<g id="enzyme">
<?php
   foreach(explode(';',$row[3]) as $value) {
    list($name,$pos) = explode(' ',$value);
    if ($name != '') {
     $alpha=(((2*pi())*intval($pos))/$size)-pi()/2;
     print ' <path d="M ' . (cos($alpha)*150+300) . ' ' . (sin($alpha)*150+200) . ' L ' . (cos($alpha)*175+300) . ' ' . (sin($alpha)*175+200) . '" style="fill:none;stroke:black;stroke-width:0.5" />' . "\n";
     print ' <text x="' . (cos($alpha)*180+300) . '" y="' . (sin($alpha)*180+200) . '" style="text-anchor:' . ((cos($alpha)>0)?'start':'end') . '; font-size:9px; font-family:Arial;">' . $name . ' ' . $pos . '</text>' . "\n";
    };
   };
?>
</g>
<g id="marker">
<?php
   foreach(explode("\n",$row[2]) as $value) {
    list($name,$pos1,$pos2,$arrow,$color,$border) = explode(' ',$value);
    if ($name != '') {
     $alpha1=(((2*pi())*intval($pos1))/$size)-pi()/2;
     $alpha2=(((2*pi())*intval($pos2))/$size)-pi()/2;
     echo ' <text x="' . (cos(($alpha1+$alpha2)/2)*125+300) . '" y="' . (sin(($alpha1+$alpha2)/2)*125+200) . '" style="text-anchor:' . ((cos(($alpha1+$alpha2)/2)>0)?'end':'start') . '; font-size:10px; font-family:Arial; font-weight:bold;">' . $name . '</text>' . "\n";
     if (($arrow=='arrow_off') || (abs($alpha1-$alpha2)<0.05)) {
      print ' <path d="M ' . (cos($alpha1)*(150-($border/2))+300) . ' ' . (sin($alpha1)*(150-($border/2))+200) . ' L ' . (cos($alpha1)*(150+($border/2))+300) . ' ' . (sin($alpha1)*(150+($border/2))+200) . ' A ' . (150+($border/2)) . ',' . (150+($border/2)) . ' 0 0,' . (($pos1>$pos2)?'0':'1') . ' ' . (cos($alpha2)*(150+($border/2))+300) . ' ' . (sin($alpha2)*(150+($border/2))+200) . ' L ' . (cos($alpha2)*(150-($border/2))+300) . ' ' . (sin($alpha2)*(150-($border/2))+200) . ' A ' . (150-($border/2)) . ',' . (150-($border/2)) . ' 0 0,' . (($pos1>$pos2)?'1':'0') . ' ' . (cos($alpha1)*(150-($border/2))+300) . ' ' . (sin($alpha1)*(150-($border/2))+200) . '" style="fill:' . $color . ';stroke:black;stroke-width:0.5;"/>' . "\n";
     } else {
      $alpha3=$alpha2 - (($pos1>$pos2)?(-0.05):(+0.05));
      print ' <path d="M ' . (cos($alpha1)*(150-($border/2))+300) . ' ' . (sin($alpha1)*(150-($border/2))+200) . ' L ' . (cos($alpha1)*(150+($border/2))+300) . ' ' . (sin($alpha1)*(150+($border/2))+200) . ' A ' . (150+($border/2)) . ',' . (150+($border/2)) . ' 0 0,' . (($pos1>$pos2)?'0':'1') . ' ' . (cos($alpha3)*(150+($border/2))+300) . ' ' . (sin($alpha3)*(150+($border/2))+200) . ' L ' . (cos($alpha3)*(150+($border/2)+5)+300) . ' ' . (sin($alpha3)*(150+($border/2)+5)+200) . ' L ' . (cos($alpha2)*150+300) . ' ' . (sin($alpha2)*150+200) . ' L ' . (cos($alpha3)*(150-($border/2)-5)+300) . ' ' . (sin($alpha3)*(150-($border/2)-5)+200) . ' L ' . (cos($alpha3)*(150-($border/2))+300) . ' ' . (sin($alpha3)*(150-($border/2))+200) . ' A ' . (150-($border/2)) . ',' . (150-($border/2)) . ' 0 0,' . (($pos1>$pos2)?'1':'0') . ' ' . (cos($alpha1)*(150-($border/2))+300) . ' ' . (sin($alpha1)*(150-($border/2))+200) . '" style="fill:' . $color . ';stroke:black;stroke-width:0.5;"/>' . "\n";
     };
    };
   };
?>
</g>
</svg>
<?php  }; }; ?>
