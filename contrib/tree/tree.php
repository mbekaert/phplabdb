<?php
ob_start("ob_gzhandler");
    set_time_limit(2500);
session_start();
require_once('../includes/main.inc');
require_once('tree.inc');


function get_taxon ($taxon) {
  if (!empty($taxon) && (intval($taxon)>0)) {
    $url = "http://eutils.ncbi.nlm.nih.gov/entrez/eutils/efetch.fcgi?db=taxonomy&id=$taxon&retmode=xml";
    if ($taxonfile = file_get_contents($url)) {
      if ((($nodes = readXML($taxonfile)) !== false) && isset($nodes['children'][0]['children'][0])) {
        foreach($nodes['children'][0]['children'] as $ref) {
          switch ($ref['name']) {
            case 'TAXID':
              $ret['taxonid'] = $ref['cdata'];
              break;
            case 'SCIENTIFICNAME':
              $ret['scientificname'] = $ref['cdata'];
              break;
            case 'LINEAGE':
              $ret['taxonomy'] = $ref['cdata'];
              break;
            case 'OTHERNAMES':
              foreach($ref['children'] as $value) {
                if ($value['name'] == 'GENBANKCOMMONNAME') $ret['commonname'] = $value['cdata'];
              }
              break;
           }
        }
        return $ret;
      }
    }
  }
}


if ($config['login']) {
  $sql = sql_connect($config['db']);
  if (!empty($_POST['tree']) && ($_POST['tree'] == md5('tree' . floor(intval(date('b'))))) && !empty($_POST['species'][1]) && !empty($_POST['taxon'])) {
    $tree = array();
    $result = sql_query('SELECT scientificname, commonname, ' . (($_POST['taxon'] == 'manual')?'tkingdom, tphylum, tclass, torder, tfamily, ttribe, tgenus':'taxon') . ' FROM tree_taxonomy ORDER BY ' . (($_POST['taxon'] == 'manual')?'tkingdom, tphylum, tclass, torder, tfamily, ttribe, tgenus, scientificname':'taxon, scientificname') . ';', $sql);
    if ((!strlen($r = sql_last_error($sql))) && (sql_num_rows($result) > 0)) {
      while ($row = sql_fetch_row($result)) {
        if (in_array($row[0], $_POST['species'])) {
          if ($_POST['taxon'] == 'manual') {
            $localtree = array();
            for($i = 2; $i <= 8; $i++) if (!empty($row[$i])) $localtree[] = $row[$i];
            $localtree[] = $row[0] . '|' . str_replace(';', '+', $row[1]);
          }else {
            $localtree = explode(';', $row[2] . ';' . $row[0] . '|' . str_replace(';', '+', $row[1]));
          }
          $tree = deeptree($tree, $localtree);
        }
      }
    }
    if (($tree = compacttree($tree)) !== false) {
      header ('Expires: ' . gmdate("D, d M Y H:i:s") . ' GMT');
//      header ('Cache-Control: no-cache, must-revalidate');
      if (!empty($_POST['output']) && ($_POST['output'] == 'png')) {
        header ('Content-type: image/png');
        header('Content-Disposition: inline; filename=tree.png');
        $h = (!empty($_POST['height']) ? intval($_POST['height']) : 400);
        $w = (!empty($_POST['width']) ? intval($_POST['width']) : 600);
        if (($nodes = readXML(printtree($tree))) !== false) {
          $line = 0;
          $image = imagecreatetruecolor($w, $h);
          $white = imagecolorallocate($image, 255, 255, 255);
          $grey = imagecolorallocate($image, 153, 153, 153);
          $black = imagecolorallocate($image, 0, 0, 0);
          imagefill($image, 0, 0, $white); //imagecreatetruecolor bug fix
          imagecolortransparent($image, $white);
          nbleafs($nodes);
          imagepng($image);
        }
      }elseif (!empty($_POST['output']) && ($_POST['output'] == 'tree')) {
        header("Content-Type: application/treeview");
        header('Content-Disposition: inline; filename=tree.tre');
        print newick($tree) . ";\n";
      }else {
        header('Content-Disposition: inline; filename=tree.xml');
        header("Content-Type: application/xml");
        print printtree($tree);
      }
    }
  }elseif (isset($_FILES['import']) && ($_FILES['import']['error'] == UPLOAD_ERR_OK) && ($_FILES['import']['size'] > 0) && is_uploaded_file($_FILES['import']['tmp_name']) && ($import = file_get_contents($_FILES['import']['tmp_name']))) {
    header ('Expires: ' . gmdate("D, d M Y H:i:s") . ' GMT');
//    header ('Cache-Control: no-cache, must-revalidate');
    if (($nodes = readXML($import)) !== false) {
      if (!empty($_POST['output']) && ($_POST['output'] == 'png')) {
        require_once('tree.inc');
        header ('Content-type: image/png');
        header('Content-Disposition: inline; filename=tree.png');
        $h = (!empty($_POST['height']) ? intval($_POST['height']) : 400);
        $w = (!empty($_POST['width']) ? intval($_POST['width']) : 600);
        $line = 0;
        $image = imagecreatetruecolor($w, $h);
        $white = imagecolorallocate($image, 255, 255, 255);
        $grey = imagecolorallocate($image, 153, 153, 153);
        $black = imagecolorallocate($image, 0, 0, 0);
        imagefill($image, 0, 0, $white); //imagecreatetruecolor bug fix
        imagecolortransparent($image, $white);
        nbleafs($nodes);
        imagepng($image);
      }elseif (!empty($_POST['output']) && ($_POST['output'] == 'tree')) {
//        header("Content-Type: application/treeview");
//        header('Content-Disposition: inline; filename=tree.tre');
//        print newick($tree) . ";\n";
      }else {
        header('Content-Type: application/xml');
        header('Content-Disposition: inline; filename=tree.xml');
        print $import;
      }
    } else {

      foreach(explode("\n",$import) as $line) {
        if (($taxon = get_taxon(trim($line))) !== false) {
          $localtree = explode(';', $taxon['taxonomy'] . ';' . $taxon['scientificname'] . '|' . str_replace(';', '+', $taxon['commonname']));
          if (!empty($taxon['taxonomy'])) $tree = deeptree($tree, $localtree);
        }
      }
    if (($tree = compacttree($tree)) !== false) {
      if (!empty($_POST['output']) && ($_POST['output'] == 'png')) {
        header ('Content-type: image/png');
        header('Content-Disposition: inline; filename=tree.png');
        $h = (!empty($_POST['height']) ? intval($_POST['height']) : 400);
        $w = (!empty($_POST['width']) ? intval($_POST['width']) : 600);
        if (($nodes = readXML(printtree($tree))) !== false) {
          $line = 0;
          $image = imagecreatetruecolor($w, $h);
          $white = imagecolorallocate($image, 255, 255, 255);
          $grey = imagecolorallocate($image, 153, 153, 153);
          $black = imagecolorallocate($image, 0, 0, 0);
          imagefill($image, 0, 0, $white); //imagecreatetruecolor bug fix
          imagecolortransparent($image, $white);
          nbleafs($nodes);
          imagepng($image);
        }
      }elseif (!empty($_POST['output']) && ($_POST['output'] == 'tree')) {
        header("Content-Type: application/treeview");
        header('Content-Disposition: inline; filename=tree.tre');
        print newick($tree) . ";\n";
      }else {
        header('Content-Disposition: inline; filename=tree.xml');
        header("Content-Type: application/xml");
        print printtree($tree);
      }
    }



    }
  }else {
    head('tree', true);
?>
        <div class="items">
          <h1><?php print $plugin['tree']['name']; ?><small><?php print $plugin['tree']['description']; ?></small></h1><br />
          <form method="post" action="<?php print $config['server'] . $plugin['tree']['url']; ?>/tree" enctype="multipart/form-data">
          <div>
            <h2><?php print _("Draw a tree"); ?></h2><br /><?php print _("You select the species you want and draw a simple phylogenetic tree."); ?><br /><br />
            <div>
              <label for="taxon"><?php print _("Taxonomy used"); ?></label>
              <select name="taxon" id="taxon" title="<?php print _("Select your type of taxonomy"); ?>"><option value="manual" rel="none"><?php print _("Manual entries"); ?></option><option value="ncbi" rel="none"><?php print _("Full taxonomy"); ?></option><option disabled="disabled" value="calculated" rel="calculated"><?php print _("Calculated"); ?></option></select>
              <br />
            </div>
            <div>
              <label for="species"><?php print _("species"); ?></label>
              <select name="species[]" id="species" multiple="yes" size="10"><?php
    $result = sql_query('SELECT scientificname, commonname FROM tree_taxonomy ORDER BY scientificname;', $sql);
    if ((!strlen($r = sql_last_error($sql))) && (sql_num_rows($result) > 0)) {
      while ($row = sql_fetch_row($result)) {
        print "<option value=\"$row[0]\">" . $row[0] . (isset($row[1])?' (' . $row[1] . ')':'') . "</option>";
      }
    }
?></select>
              <br />
            </div>
            <strong>or</strong>
            <div>
              <label for="import"><?php print _("Import XML"); ?></label>
              <input name="import" id="import" type="file" title="<?php print _("Import XML file"); ?>" />
              <br />
            </div>
            <strong>and</strong>
            <div>
              <label for="output"><?php print _("Output"); ?></label>
              <select name="output" id="output" title="<?php print _("Select your type of output"); ?>"><option value="xml" rel="none"><?php print _("XML (exportation file)"); ?></option><option value="png" rel="png"><?php print _("PNG (image)"); ?></option><option value="tree" rel="none"><?php print _("Tree file"); ?></option></select>
              <br />
            </div>
            <div rel="png">
              <label for="height"><?php print _("Height"); ?></label>
              <input name="height" id="height" type="text" maxlength="8" title="<?php print _("Image height"); ?>" value="400" class="half" /> px
              <br />
            </div>
            <div rel="png">
              <label for="width"><?php print _("Width"); ?></label>
              <input name="width" id="width" type="text" maxlength="8" title="<?php print _("Image width"); ?>" value="600" class="half" /> px
              <br />
            </div>
            <br />
            <input type="hidden" name="tree" value="<?php print md5('tree' . floor(intval(date('b')))); ?>" />
            <input type="submit" value="<?php print _("Draw"); ?>" />
          </div>
          </form>
          <br />
        </div>
<?php
    foot();
  }
}else {
  header('Location: ' . $config['server']);
}
?>