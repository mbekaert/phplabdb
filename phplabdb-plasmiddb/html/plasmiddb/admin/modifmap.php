<?php
 ob_start("ob_gzhandler");
 session_start();
 include_once '../../includes/login.inc';
 if (!isset($_SESSION['status'])) {
  header('Location: ' . $base_url);
  exit;
 };
 $lev=error_reporting(8);
 $status=$_SESSION['status'];
 header_start();
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.01//EN">
<html>
  <head>
    <title>
      ..:: phpLabDB::PlasmidDB ::..
    </title>
    <meta http-equiv="Content-Type" content="text/html">
    <link rel="stylesheet" type="text/css" media="print" href="<?php print $base_url; ?>css/print.css">
    <link rel="stylesheet" type="text/css" media="screen" href="<?php print $base_url; ?>css/screen.css">
    <style type="text/css">
.plasmid { background: url('../images/plasmid.png') no-repeat right top; }
    </style>
<?php
 textdomain('plasmiddb'); 
 if (!(isset($_POST['markers']))) { ?>
    <script type="text/javascript">
    //<![CDATA[
var plasmid_size='<?php print $_SESSION['plasmide_modif']['length']; ?>';
function is_valid_position(pos){
 if(plasmid_size != ""){
  var s = parseFloat(plasmid_size);
  var p = parseFloat(pos);
  if( (p > s) || (p < 0) ){
   return false;
  } else {
   return true;
  };
 } else {
  return false;
 };
};

function add_to_marker(){
 var name=document.forms(0).add_marker_name.value;
 var start=document.forms(0).add_marker_start.value;
 var end=document.forms(0).add_marker_end.value;
 var arrow="";
 if(document.forms(0).add_marker_arrow.checked){
  arrow=document.forms(0).add_marker_arrow.value;
 } else {
  arrow='arrow_off';
 };
 var fill=document.forms(0).add_marker_fill.options[document.forms(0).add_marker_fill.selectedIndex].text;
 var thickness=document.forms(0).add_marker_thickness.value;
 if(is_valid_position(start) && is_valid_position(end)) {
  document.forms(0).markers.value = document.forms(0).markers.value + name + " " + start + " " + end +" " + arrow +" " + fill + " " + thickness + "; ";
 } else {
  alert('<?php print _("Position invalide!"); ?>');
 };
};

function add_enzyme() {
 var p = document.forms(0).add_enzyme_position.value;
 if( is_valid_position(p)) {
  document.forms(0).enzymes.value = document.forms(0).enzymes.value + document.forms(0).add_enzyme_name.value + " " + p + "; ";
  document.forms(0).add_enzyme_position.value = "";
  document.forms(0).add_enzyme_name.value = "";
 } else {
  alert('<?php print _("Position invalide!"); ?>');
 };
};
    //]]>
    </script>
<?php }; ?>  </head>
  <body>
    <div id="middle-one">
      <div class="corner-tr">
        &nbsp;
      </div>
      <div class="corner-tl">
        &nbsp;
      </div>
      <div id="content" class="plasmid">
        <div id="page-main">
          <h1>
            PlasmidDB plug-in
          </h1>
          <h3>
            <?php print _("Map") . ' (' . $_SESSION['plasmide_modif']['name'] . ' - ' . $_SESSION['plasmide_modif']['length'] . ' ' . _("bp") . ')'; ?> 
          </h3>
<?php if (!(isset($_POST['markers']))) { ?>
          <form action="<?php print $base_url . $plugin_dir['plasmiddb']; ?>admin/modifmap.php" method="post">
            <div>
              <strong><label for="markers"><?php print _("Markers:"); ?></label></strong><br>
               <textarea name="markers" id="markers" rows="5" cols="50"><?php if (isset($_SESSION['plasmide_mmap'])) {print $_SESSION['plasmide_mmap']['markers']; }; ?></textarea><br>
               <label for="add_marker_name"><?php print _("Name:"); ?></label>&nbsp;<input type="text" name="add_marker_name" id="add_marker_name" size="10">&nbsp; &nbsp;<label for="add_marker_start"><?php print _("Start:"); ?></label>&nbsp;<input type="text" name="add_marker_start" id="add_marker_start" size="10">&nbsp; &nbsp;<label for="add_marker_end"><?php print _("Finish:"); ?></label>&nbsp;<input type="text" name="add_marker_end" id="add_marker_end" size="10">&nbsp; &nbsp;<input type="checkbox" checked="checked" name="add_marker_arrow" id="add_marker_arrow" value="arrow_on">&nbsp;<label for="add_marker_arrow"><?php print _("Arrow&nbsp;on"); ?></label><br>
               <label for="add_marker_fill"><?php print _("Fill-color:"); ?></label>&nbsp;<select name="add_marker_fill" id="add_marker_fill" size="1">
                <option>
                  <?php print _("Aqua"); ?> 
                </option>
                <option value="Black">
                  <?php print _("Black"); ?> 
                </option>
                <option value="Blue">
                  <?php print _("Blue"); ?> 
                </option>
                <option value="Fuschia">
                  <?php print _("Fuschia"); ?> 
                </option>
                <option value="Gray">
                  <?php print _("Gray"); ?> 
                </option>
                <option value="Green">
                  <?php print _("Green"); ?> 
                </option>
                <option value="Lime">
                  <?php print _("Lime"); ?> 
                </option>
                <option value="Maroon">
                  <?php print _("Maroon"); ?> 
                </option>
                <option value="Navy">
                  <?php print _("Navy"); ?> 
                </option>
                <option value="Olive">
                  <?php print _("Olive"); ?> 
                </option>
                <option value="Purple">
                  <?php print _("Purple"); ?> 
                </option>
                <option selected="selected" value="Red">
                  <?php print _("Red"); ?> 
                </option>
                <option value="Silver">
                  <?php print _("Silver"); ?> 
                </option>
                <option value="Teal">
                  <?php print _("Teal"); ?> 
                </option>
                <option value="Yellow">
                  <?php print _("Yellow"); ?> 
                </option>
              </select>&nbsp; &nbsp;<label for="add_marker_thickness"><?php print _("Line Thickness:"); ?></label>&nbsp;<input type="text" name="add_marker_thickness" id="add_marker_thickness" size="5" value="12"><br>
              <input type="button" name="add_to_marker_list" value="<?php print _("Add"); ?>" onclick="javascript:add_to_marker()">
            <hr>
            <strong><label for="enzymes"><?php print _("Enzymes:"); ?></label></strong><br>
            <textarea name="enzymes" id="enzymes" rows="2" cols="50"><?php if (isset($_SESSION['plasmide_mmap'])) {print $_SESSION['plasmide_mmap']['enzymes']; }; ?></textarea><br>
            <label for="add_enzyme_name"><?php print _("Name:"); ?></label>&nbsp;<input type="text" name="add_enzyme_name" id="add_enzyme_name" size="10">&nbsp; &nbsp;<label for="add_enzyme_position"><?php print _("Position:"); ?></label>&nbsp;<input type="text" name="add_enzyme_position" id="add_enzyme_position" size="10"><br>
            <input type="button" name="add_to_enzyme_list" value="<?php print _("Add"); ?>" onclick="add_enzyme()">
            <hr>
            <input type="submit" value="<?php print _("Save"); ?>">
          </div>
        </form>
<?php } else {
 $_SESSION['plasmide_mmap']['modif']=true;
 $_SESSION['plasmide_mmap']['markers']=trim(stripslashes($_POST['markers']));
 $_SESSION['plasmide_mmap']['enzymes']=trim(stripslashes($_POST['enzymes']));
 if (($_SESSION['plasmide_mmap']['markers']=='') && ($_SESSION['plasmide_mmap']['enzymes']=='')) {
  unset($_SESSION['plasmide_mmap']);
  print "          <p>\n            " . _("Map cleaned") . "\n          </p>\n";
 };
?>
          <p>
            <a href="javascript:window.close();"><?php print _("Close the windows"); ?></a>
          </p>
<?php }; ?>
        </div>
      </div>
      <div class="corner-br">
        &nbsp;
      </div>
      <div class="corner-bl">
        &nbsp;
      </div>
    </div>
  </body>
</html>
