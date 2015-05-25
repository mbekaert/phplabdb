<?php
ob_start("ob_gzhandler");
session_start();
require_once('../includes/main.inc');

function trim_alter(&$item) {
  $item = trim($item);
}

function get_taxon ($taxonid, $sql) {
  if (!empty($taxonid)) {
    $url = "http://eutils.ncbi.nlm.nih.gov/entrez/eutils/efetch.fcgi?db=taxonomy&id=$taxonid&retmode=xml";
    if ($taxonfile = file_get_contents($url)) {
      require_once('tree.inc');
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
            case 'DIVISION':
              $result = sql_query('SELECT reference FROM tree_division WHERE name' . sql_reg(substr($ref['cdata'], 1)) . ';', $sql);
              if ((!strlen($r = sql_last_error($sql))) && (sql_num_rows($result) == 1)) {
                $row2 = sql_fetch_row($result);
                $ret['division'] = $row2[0];
              }
              break;
            case 'LINEAGEEX':
              foreach($ref['children'] as $value) {
                foreach($value['children'] as $value2) {
                  if ($value2['name'] == 'SCIENTIFICNAME') $tmp = $value2['cdata'];
                  if ($value2['name'] == 'RANK') {
                    $ret['taxon'][$value2['cdata']] = $tmp;
                  }
                }
              }
              break;
          }
        }
        if (empty($ret['taxon']['species']) && !empty($ret['scientificname'])) $ret['taxon']['species'] = trim(strstr($ret['scientificname'], ' '));
        if (strstr($ret['taxon']['species'], ' ') !== false) {
          $ret['taxon']['subspecies'] = trim(strstr($ret['taxon']['species'], ' '));
          $ret['taxon']['species'] = trim(substr($ret['taxon']['species'], 0, strpos($ret['taxon']['species'], ' ')));
        }
        if (empty($ret['taxon']['genus']) && !empty($ret['scientificname'])) $ret['taxon']['genus'] = trim(substr($ret['scientificname'], 0, strpos($ret['scientificname'], ' ')));
        return $ret;
      }
    }
  }
}

if ($config['login']) {
  if (($_SESSION['login']['right'] >= 3) && !empty($_GET['edit'])) {
    $sql = sql_connect($config['db']);
    if (isset($_POST['remove']) && !empty($_POST['key']) && ($_POST['key'] == md5(strip_tags(str_replace('_', ' ', rawurldecode($_GET['edit']))) . floor(intval(date('b')))))) {
      $result = sql_query('DELETE FROM tree_taxonomy WHERE scientificname=\'' . addslashes(stripslashes(strip_tags(str_replace('_', ' ', rawurldecode($_GET['edit']))))) . '\';', $sql);
      header('Location: ' . $config['server'] . $plugin['tree']['url'] . '/');
      exit;
    }elseif (isset($_POST['edit']) && !empty($_POST['key']) && ($_POST['key'] == md5(strip_tags(str_replace('_', ' ', rawurldecode($_GET['edit']))) . floor(intval(date('b'))))) && !empty($_POST['genus']) && (strlen(stripslashes(strip_tags(trim($_POST['genus'])))) > 2) && !empty($_POST['species']) && (strlen(stripslashes(strip_tags(trim($_POST['species'])))) > 2) && !empty($_POST['division']) && !empty($_POST['nomenclaturalcode'])) {
      if (isset($_POST['taxonomy']) && !fullyempty($_POST['taxonomy'])) {
        $taxonomy = preg_split('/;/', preg_replace('/[^\d\w\.\-\_\\/ \;]/', '', stripslashes(strip_tags(trim($_POST['taxonomy'])))), -1, PREG_SPLIT_NO_EMPTY);
        array_walk($taxonomy, 'trim_alter');
        $taxonomy = implode (' ; ', $taxonomy);
      }
      $result = sql_query('UPDATE tree_taxonomy SET alias=' . (!empty($_POST['alias'])?'\'' . addslashes(stripslashes(strip_tags(trim($_POST['alias'])))) . '\'':'NULL') . ', taxon=' . (!empty($taxonomy)?'\'' . addslashes($taxonomy) . '\'':'NULL') . ', division=\'' . addslashes(stripslashes(strip_tags(trim($_POST['division'])))) . '\', taxonid=' . ((isset($_POST['taxonid']) && !fullyempty($_POST['taxonid']))?intval($_POST['taxonid']):'NULL') . ', nomenclaturalcode=\'' . addslashes(stripslashes(strip_tags(trim($_POST['nomenclaturalcode'])))) . '\', tkingdom=' . (!empty($_POST['kingdom'])?'\'' . addslashes(ucfirst(stripslashes(strip_tags(trim($_POST['kingdom']))))) . '\'':'NULL') . ', tphylum=' . (!empty($_POST['phylum'])?'\'' . addslashes(ucfirst(stripslashes(strip_tags(trim($_POST['phylum']))))) . '\'':'NULL') . ', tclass=' . (!empty($_POST['class'])?'\'' . addslashes(ucfirst(stripslashes(strip_tags(trim($_POST['class']))))) . '\'':'NULL') . ', torder=' . (!empty($_POST['order'])?'\'' . addslashes(ucfirst(stripslashes(strip_tags(trim($_POST['order']))))) . '\'':'NULL') . ', tfamily=' . (!empty($_POST['family'])?'\'' . addslashes(ucfirst(stripslashes(strip_tags(trim($_POST['family']))))) . '\'':'NULL') . ', ttribe=' . (!empty($_POST['tribe'])?'\'' . addslashes(ucfirst(stripslashes(strip_tags(trim($_POST['tribe']))))) . '\'':'NULL') . ', tgenus=' . (!empty($_POST['genus'])?'\'' . addslashes(ucfirst(stripslashes(strip_tags(trim($_POST['genus']))))) . '\'':'NULL') . ', tspecies=' . (!empty($_POST['species'])?'\'' . addslashes(stripslashes(strip_tags(trim($_POST['species'])))) . '\'':'NULL') . ', tsubspecies=' . (!empty($_POST['subspecies'])?'\'' . addslashes(stripslashes(strip_tags(trim($_POST['subspecies'])))) . '\'':'NULL') . ', commonname=' . (!empty($_POST['commonname'])?'\'' . addslashes(ucfirst(stripslashes(strip_tags(trim($_POST['commonname']))))) . '\'':'NULL') . ', abbrivation=' . (!empty($_POST['abbrivation'])?'\'' . addslashes(stripslashes(strip_tags(trim($_POST['abbrivation'])))) . '\'':'NULL') . ', comments=' . ((isset($_POST['comments']) && !fullyempty($_POST['comments']))?'\'' . addslashes(stripslashes(strip_tags(trim($_POST['comments'])))) . '\'':'NULL') . ', updated=NOW(), author=\'' . addslashes($_SESSION['login']['username']) . '\' WHERE scientificname=\'' . addslashes(stripslashes(strip_tags(str_replace('_', ' ', rawurldecode($_GET['edit']))))) . '\' ;', $sql);
      if (!strlen($r = sql_last_error($sql))) {
        header('Location: ' . $config['server'] . $plugin['tree']['url'] . '/species/' . rawurlencode(strip_tags(trim(rawurldecode($_GET['edit'])))));
        exit;
      }
    }
    head('tree');
?>
      <div class="items">
        <h1><?php print $plugin['tree']['name']; ?><small><?php print $plugin['tree']['description']; ?></small></h1><br />
<?php
    $result = sql_query('SELECT scientificname, alias, taxon, division, taxonid, nomenclaturalcode, tkingdom, tphylum, tclass, torder, tfamily, ttribe, tgenus, tspecies, tsubspecies, commonname, abbrivation, comments FROM tree_taxonomy WHERE scientificname=\'' . addslashes(stripslashes(strip_tags(str_replace('_', ' ', rawurldecode($_GET['edit']))))) . '\' ;', $sql);
    if (!strlen($r = sql_last_error($sql)) && (sql_num_rows($result) == 1)) {
      $row = sql_fetch_row($result); ?>
        <form method="post" action="<?php print $config['server'] . $plugin['tree']['url'] . '/species/edit/' . rawurlencode(str_replace(' ', '_', $row[0])); ?>">
        <div>
          <h2><em><?php print $row[0]; ?></em></h2><br />
          <div>
            <label for="alias"><?php print _("Alias"); ?></label>
            <input type="text" name="alias" id="alias" maxlength="128" title="<?php print _("Other/Old name"); ?>" <?php print (!empty($_POST['alias'])?' value="' . stripslashes(strip_tags(trim($_POST['alias']))) . '" ':(isset($row[1])?' value="' . $row[1] . '"':'')); ?>/>
            <br />
          </div>
          <div>
            <label for="commonname"><?php print _("Common name"); ?></label>
            <input type="text" name="commonname" id="commonname" maxlength="128" title="<?php print _("English name"); ?>" <?php print (!empty($_POST['commonname'])?' value="' . stripslashes(strip_tags(trim($_POST['commonname']))) . '"':(isset($taxon['commonname'])?' value="' . $taxon['commonname'] . '"':(isset($row[15])?' value="' . $row[15] . '"':''))); ?>/>
            <br />
          </div>
          <div>
            <label for="abbrivation"><?php print _("Abbreviation"); ?></label>
            <input type="text" name="abbrivation" id="abbrivation" maxlength="8" title="<?php print _("Unique abbreviation (e.g. Homo sapiens as Hsp)"); ?>" <?php print (!empty($_POST['abbrivation'])?' value="' . stripslashes(strip_tags(trim($_POST['abbrivation']))) . '" ':(isset($row[16])?' value="' . $row[16] . '"':'')); ?>class="half" />
            <br />
          </div>
          <div>
            <label for="nomenclaturalcode"><strong><?php print _("Nomenclature"); ?></strong></label>
            <select name="nomenclaturalcode" id="nomenclaturalcode" title="<?php print _("The nomenclatural code under which the scientific name is constructed"); ?>"><option value=""></option><?php
      $result = sql_query('SELECT name, description FROM tree_nomenclaturalcode ORDER BY name;', $sql);
      if ((!strlen($r = sql_last_error($sql))) && (sql_num_rows($result) > 0)) {
        while ($row2 = sql_fetch_row($result)) {
          print "<option value=\"$row2[0]\"" . (((isset($_POST['nomenclaturalcode']) && ($row2[0] == $_POST['nomenclaturalcode'])) || (!isset($_POST['nomenclaturalcode']) && !empty($row[5]) && ($row[5] == $row2[0])))?' selected="selected"':'') . ">$row2[1]</option>";
        }
      }
?></select>
            <br />
          </div>
          <div>
            <label for="division"><strong><?php print _("Division"); ?></strong></label>
            <select name="division" id="division" title="<?php print _("Genbank Division"); ?>"><option value=""></option><?php
      $result = sql_query('SELECT reference, name FROM tree_division;', $sql);
      if ((!strlen($r = sql_last_error($sql))) && (sql_num_rows($result) > 0)) {
        while ($row2 = sql_fetch_row($result)) {
          print "<option value=\"$row2[0]\"" . (((isset($_POST['division']) && ($row2[0] == $_POST['division'])) || (!isset($_POST['division']) && !empty($row[3]) && ($row[3] == $row2[0])))?' selected="selected"':'') . ">$row2[1]</option>";
        }
      }
?></select>
            <br />
          </div>
          <div>
            <label for="taxonid"><?php print _("TaxonID"); ?></label>
            <input type="text" name="taxonid" id="taxonid" maxlength="10" title="<?php print _("Genbank taxonid"); ?>" <?php print ((isset($_POST['taxonid']) && !fullyempty($_POST['taxonid']))?' value="' . intval($_POST['taxonid']) . '" ':(isset($row[4])?' value="' . $row[4] . '"':'')); ?>/>
            <br />
          </div>
          <div>
            <label for="taxonomy"><?php print _("Full taxonomy"); ?></label>
            <textarea name="taxonomy" id="taxonomy" cols="30" rows="3" title="<?php print _("Full taxonomy"); ?>"><?php print ((isset($_POST['taxonomy']) && !fullyempty($_POST['taxonomy']))?stripslashes(strip_tags(trim($_POST['taxonomy']))):(isset($row[2])?$row[2]:'')); ?></textarea>
            <br />
          </div>
          <div>
            <label for="kingdom"><?php print _("Kingdom"); ?></label>
            <input type="text" name="kingdom" id="kingdom" maxlength="128" title="<?php print _("The name of the kingdom"); ?>" <?php print (!empty($_POST['kingdom'])?' value="' . stripslashes(strip_tags(trim($_POST['kingdom']))) . '"':(isset($row[6])?' value="' . $row[6] . '"':'')); ?>/>
            <br />
          </div>
          <div>
            <label for="phylum"><?php print _("Phylum"); ?></label>
            <input type="text" name="phylum" id="phylum" maxlength="128" title="<?php print _("The name of the phylum"); ?>" <?php print (!empty($_POST['phylum'])?' value="' . stripslashes(strip_tags(trim($_POST['phylum']))) . '" ':(isset($row[7])?' value="' . $row[7] . '"':'')); ?>/>
            <br />
          </div>
          <div>
            <label for="class"><?php print _("Class"); ?></label>
            <input type="text" name="class" id="class" maxlength="128" title="<?php print _("The name of the class"); ?>" <?php print (!empty($_POST['class'])?' value="' . stripslashes(strip_tags(trim($_POST['class']))) . '" ':(isset($row[8])?' value="' . $row[8] . '"':'')); ?>/>
            <br />
          </div>
          <div>
            <label for="order"><?php print _("Order"); ?></label>
            <input type="text" name="order" id="order" maxlength="128" title="<?php print _("The name of the order"); ?>" <?php print (!empty($_POST['order'])?' value="' . stripslashes(strip_tags(trim($_POST['order']))) . '" ':(isset($row[9])?' value="' . $row[9] . '"':'')); ?>/>
            <br />
          </div>
          <div>
            <label for="family"><?php print _("Family"); ?></label>
            <input type="text" name="family" id="family" maxlength="128" title="<?php print _("The name of the family"); ?>" <?php print (!empty($_POST['family'])?' value="' . stripslashes(strip_tags(trim($_POST['family']))) . '" ':(isset($row[10])?' value="' . $row[10] . '"':'')); ?>/>
            <br />
          </div>
          <div>
            <label for="subfamily"><?php print _("Tribe"); ?></label>
            <input type="text" name="tribe" id="tribe" maxlength="128" title="<?php print _("The name of the tribe"); ?>" <?php print (!empty($_POST['tribe'])?' value="' . stripslashes(strip_tags(trim($_POST['tribe']))) . '" ':(isset($row[11])?' value="' . $row[11] . '"':'')); ?>/>
            <br />
          </div>
          <div>
            <label for="genus"><strong><?php print _("Genus"); ?></strong></label>
            <input type="text" name="genus" id="genus" maxlength="128" title="<?php print _("The name of the genus"); ?>" <?php print (!empty($_POST['genus'])?' value="' . stripslashes(strip_tags(trim($_POST['genus']))) . '" ':(isset($row[12])?' value="' . $row[12] . '"':'')); ?>/>
            <br />
          </div>
          <div>
            <label for="species"><strong><?php print _("Species"); ?></strong></label>
            <input type="text" name="species" id="species" maxlength="128" title="<?php print _("The name of the species"); ?>" <?php print (!empty($_POST['species'])?' value="' . stripslashes(strip_tags(trim($_POST['species']))) . '" ':(isset($row[13])?' value="' . $row[13] . '"':'')); ?>/>
            <br />
          </div>
          <div>
            <label for="subspecies"><?php print _("Sub-Species"); ?></label>
            <input type="text" name="subspecies" id="subspecies" maxlength="128" title="<?php print _("The name of the subspecies"); ?>" <?php print (!empty($_POST['subspecies'])?' value="' . stripslashes(strip_tags(trim($_POST['subspecies']))) . '" ':(isset($row[14])?' value="' . $row[14] . '"':'')); ?>/>
            <br />
          </div>
<div>
<label for="comments"><?php print _("Comments"); ?></label>
<textarea name="comments" id="comments" cols="30" rows="3" title="<?php print _("Organism comments"); ?>"><?php print ((isset($_POST['comments']) && !fullyempty($_POST['comments']))?stripslashes(strip_tags(trim($_POST['comments']))):(isset($row[17])?$row[17]:'')); ?></textarea>
<br />
</div>
            <br />
            <input type="hidden" name="key" value="<?php print md5(strip_tags(str_replace('_', ' ', rawurldecode($_GET['edit']))) . floor(intval(date('b')))); ?>" />
            <input type="reset" value="<?php print _("Clear"); ?>" />&nbsp;<input type="submit"  name="edit" value="<?php print _("Edit"); ?>" />&nbsp;<input type="submit" name="remove" value="<?php print _("Remove"); ?>" onclick="return confirm('<?php print _("Are you sure you want to delete?"); ?>')"/>
          </div>
          </form>
          <br />
        </div>
<?php
    }
  }elseif (($_SESSION['login']['right'] >= 2) && !empty($_GET['add'])) {
    $sql = sql_connect($config['db']);
    if (!empty($_POST['tree']) && ($_POST['tree'] == md5('next' . floor(intval(date('b'))))) && !empty($_POST['guideline']) && (($_POST['guideline'] == 'unknown') || (($_POST['guideline'] == 'taxon') && !empty($_POST['taxonid']) && (($taxon = get_taxon(intval($_POST['taxonid']), $sql)) !== false)))) {
      $step2 = true;
    }elseif (!empty($_POST['tree']) && ($_POST['tree'] == md5('add' . floor(intval(date('b'))))) && !empty($_POST['scientificname']) && (strlen(stripslashes(strip_tags(trim($_POST['scientificname'])))) > 2) && !empty($_POST['genus']) && (strlen(stripslashes(strip_tags(trim($_POST['genus'])))) > 2) && !empty($_POST['species']) && (strlen(stripslashes(strip_tags(trim($_POST['species'])))) > 2) && !empty($_POST['division']) && !empty($_POST['nomenclaturalcode'])) {
      $step2 = true;
      if (isset($_POST['taxonomy']) && !fullyempty($_POST['taxonomy'])) {
        $taxonomy = preg_split('/;/', preg_replace('/[^\d\w\.\-\_\\/ \;]/', '', stripslashes(strip_tags(trim($_POST['taxonomy'])))), -1, PREG_SPLIT_NO_EMPTY);
        array_walk($taxonomy, 'trim_alter');
        $taxonomy = implode (' ; ', $taxonomy);
      }
      $result = sql_query('INSERT INTO tree_taxonomy (scientificname, alias, taxon, division, taxonid, nomenclaturalcode, tkingdom, tphylum, tclass, torder, tfamily, ttribe, tgenus, tspecies, tsubspecies, commonname, abbrivation, comments, author) VALUES (\'' . addslashes(ucfirst(stripslashes(strip_tags(trim($_POST['scientificname']))))) . '\',' . (!empty($_POST['alias'])?'\'' . addslashes(stripslashes(strip_tags(trim($_POST['alias'])))) . '\'':'NULL') . ',' . (!empty($taxonomy)?'\'' . addslashes($taxonomy) . '\'':'NULL') . ',\'' . addslashes(stripslashes(strip_tags(trim($_POST['division'])))) . '\',' . ((isset($_POST['taxonid']) && !fullyempty($_POST['taxonid']))?intval($_POST['taxonid']):'NULL') . ',\'' . addslashes(stripslashes(strip_tags(trim($_POST['nomenclaturalcode'])))) . '\',' . (!empty($_POST['kingdom'])?'\'' . addslashes(ucfirst(stripslashes(strip_tags(trim($_POST['kingdom']))))) . '\'':'NULL') . ',' . (!empty($_POST['phylum'])?'\'' . addslashes(ucfirst(stripslashes(strip_tags(trim($_POST['phylum']))))) . '\'':'NULL') . ',' . (!empty($_POST['class'])?'\'' . addslashes(ucfirst(stripslashes(strip_tags(trim($_POST['class']))))) . '\'':'NULL') . ',' . (!empty($_POST['order'])?'\'' . addslashes(ucfirst(stripslashes(strip_tags(trim($_POST['order']))))) . '\'':'NULL') . ',' . (!empty($_POST['family'])?'\'' . addslashes(ucfirst(stripslashes(strip_tags(trim($_POST['family']))))) . '\'':'NULL') . ',' . (!empty($_POST['tribe'])?'\'' . addslashes(ucfirst(stripslashes(strip_tags(trim($_POST['tribe']))))) . '\'':'NULL') . ',' . (!empty($_POST['genus'])?'\'' . addslashes(ucfirst(stripslashes(strip_tags(trim($_POST['genus']))))) . '\'':'NULL') . ',' . (!empty($_POST['species'])?'\'' . addslashes(stripslashes(strip_tags(trim($_POST['species'])))) . '\'':'NULL') . ',' . (!empty($_POST['subspecies'])?'\'' . addslashes(stripslashes(strip_tags(trim($_POST['subspecies'])))) . '\'':'NULL') . ',' . (!empty($_POST['commonname'])?'\'' . addslashes(ucfirst(stripslashes(strip_tags(trim($_POST['commonname']))))) . '\'':'NULL') . ',' . (!empty($_POST['abbrivation'])?'\'' . addslashes(stripslashes(strip_tags(trim($_POST['abbrivation'])))) . '\'':'NULL') . ',' . ((isset($_POST['comments']) && !fullyempty($_POST['comments']))?'\'' . addslashes(stripslashes(strip_tags(trim($_POST['comments'])))) . '\'':'NULL') . ',\'' . addslashes($_SESSION['login']['username']) . '\');', $sql);
      if (!strlen($r = sql_last_error($sql))) {
        header('Location: ' . $config['server'] . $plugin['tree']['url'] . '/species/' . rawurlencode(str_replace(' ', '_', ucfirst(stripslashes(strip_tags(trim($_POST['scientificname'])))))));
        exit;
      }
    }elseif (!empty($_POST['tree']) && ($_POST['tree'] == md5('add' . floor(intval(date('b')))))) {
      $step2 = true;
    }
    head('tree', true); ?>
      <div class="items">
        <h1><?php print $plugin['tree']['name']; ?><small><?php print $plugin['tree']['description']; ?></small></h1><br />
        <form method="post" action="<?php print $config['server'] . $plugin['tree']['url']; ?>/species/add">
        <div>
          <h2><?php print _("New Specie"); ?></h2><br /><?php print _("You can specify thr taxon, the group, etc. of the new specie."); ?><br /><br />
<?php if (!empty($step2)) {
?>
          <div>
            <label for="advanced"><?php print _("Advanced"); ?></label>
            <input type="checkbox" name="advanced" id="advanced" rel="advanced"<?php print ((!empty($_POST['advanced']) || !empty($taxon))?' checked="checked"':''); ?> />
            <br />
          </div>
          <div>
            <label for="scientificname"><strong><?php print _("Latin name"); ?></strong></label>
            <input type="text" name="scientificname" id="scientificname" maxlength="128" title="<?php print _("Full organism latin name"); ?>" <?php print (!empty($_POST['scientificname'])?' value="' . stripslashes(strip_tags(trim($_POST['scientificname']))) . '"':(isset($taxon['scientificname'])?' value="' . $taxon['scientificname'] . '"':'')); ?>/>
            <br />
          </div>
          <div rel="advanced">
            <label for="alias"><?php print _("Alias"); ?></label>
            <input type="text" name="alias" id="alias" maxlength="128" title="<?php print _("Other/Old name"); ?>" <?php print (!empty($_POST['alias'])?' value="' . stripslashes(strip_tags(trim($_POST['alias']))) . '" ':''); ?>/>
            <br />
          </div>
          <div>
            <label for="commonname"><?php print _("Common name"); ?></label>
            <input type="text" name="commonname" id="commonname" maxlength="128" title="<?php print _("English name"); ?>" <?php print (!empty($_POST['commonname'])?' value="' . stripslashes(strip_tags(trim($_POST['commonname']))) . '"':(isset($taxon['commonname'])?' value="' . $taxon['commonname'] . '"':'')); ?>/>
            <br />
          </div>
          <div>
            <label for="abbrivation"><?php print _("Abbreviation"); ?></label>
            <input type="text" name="abbrivation" id="abbrivation" maxlength="8" title="<?php print _("Unique abbreviation (e.g. Homo sapiens as Hsp)"); ?>" <?php print (!empty($_POST['abbrivation'])?' value="' . stripslashes(strip_tags(trim($_POST['abbrivation']))) . '" ':''); ?>class="half" />
            <br />
          </div>
          <div>
            <label for="nomenclaturalcode"><strong><?php print _("Nomenclature"); ?></strong></label>
            <select name="nomenclaturalcode" id="nomenclaturalcode" title="<?php print _("The nomenclatural code under which the scientific name is constructed"); ?>"><option value=""></option><?php
      $result = sql_query('SELECT name, description FROM tree_nomenclaturalcode ORDER BY name;', $sql);
      if ((!strlen($r = sql_last_error($sql))) && (sql_num_rows($result) > 0)) {
        while ($row = sql_fetch_row($result)) {
          print "<option value=\"$row[0]\"" . ((!empty($_POST['nomenclaturalcode']) && ($row[0] == $_POST['nomenclaturalcode']))?' selected="selected"':'') . ">$row[1]</option>";
        }
      }
?></select>
            <br />
          </div>
          <div>
            <label for="division"><strong><?php print _("Division"); ?></strong></label>
            <select name="division" id="division" title="<?php print _("Genbank Division"); ?>"><option value=""></option><?php
      $result = sql_query('SELECT reference, name FROM tree_division;', $sql);
      if ((!strlen($r = sql_last_error($sql))) && (sql_num_rows($result) > 0)) {
        while ($row = sql_fetch_row($result)) {
          print "<option value=\"$row[0]\"" . (((isset($_POST['division']) && ($row[0] == $_POST['division'])) || (!isset($_POST['division']) && !empty($taxon['division']) && ($taxon['division'] == $row[0])))?' selected="selected"':'') . ">$row[1]</option>";
        }
      }
?></select>
            <br />
          </div>
          <div>
            <label for="taxonid"><?php print _("TaxonID"); ?></label>
            <input type="text" name="taxonid" id="taxonid" maxlength="10" title="<?php print _("Genbank taxonid"); ?>" <?php print ((isset($_POST['taxonid']) && !fullyempty($_POST['taxonid']))?' value="' . intval($_POST['taxonid']) . '" ':''); ?>/>
            <br />
          </div>
          <div rel="advanced">
            <label for="taxonomy"><?php print _("Full taxonomy"); ?></label>
            <textarea name="taxonomy" id="taxonomy" cols="30" rows="3" title="<?php print _("Full taxonomy"); ?>"><?php print ((isset($_POST['taxonomy']) && !fullyempty($_POST['taxonomy']))?stripslashes(strip_tags(trim($_POST['taxonomy']))):(isset($taxon['taxonomy'])?$taxon['taxonomy']:'')); ?></textarea>
            <br />
          </div>
          <div rel="advanced">
            <label for="kingdom"><?php print _("Kingdom"); ?></label>
            <input type="text" name="kingdom" id="kingdom" maxlength="128" title="<?php print _("The name of the kingdom"); ?>" <?php print (!empty($_POST['kingdom'])?' value="' . stripslashes(strip_tags(trim($_POST['kingdom']))) . '"':(isset($taxon['taxon']['kingdom'])?' value="' . $taxon['taxon']['kingdom'] . '"':'')); ?>/>
            <br />
          </div>
          <div rel="advanced">
            <label for="phylum"><?php print _("Phylum"); ?></label>
            <input type="text" name="phylum" id="phylum" maxlength="128" title="<?php print _("The name of the phylum"); ?>" <?php print (!empty($_POST['phylum'])?' value="' . stripslashes(strip_tags(trim($_POST['phylum']))) . '" ':(isset($taxon['taxon']['phylum'])?' value="' . $taxon['taxon']['phylum'] . '"':'')); ?>/>
            <br />
          </div>
          <div rel="advanced">
            <label for="class"><?php print _("Class"); ?></label>
            <input type="text" name="class" id="class" maxlength="128" title="<?php print _("The name of the class"); ?>" <?php print (!empty($_POST['class'])?' value="' . stripslashes(strip_tags(trim($_POST['class']))) . '" ':(isset($taxon['taxon']['class'])?' value="' . $taxon['taxon']['class'] . '"':'')); ?>/>
            <br />
          </div>
          <div rel="advanced">
            <label for="order"><?php print _("Order"); ?></label>
            <input type="text" name="order" id="order" maxlength="128" title="<?php print _("The name of the order"); ?>" <?php print (!empty($_POST['order'])?' value="' . stripslashes(strip_tags(trim($_POST['order']))) . '" ':(isset($taxon['taxon']['order'])?' value="' . $taxon['taxon']['order'] . '"':'')); ?>/>
            <br />
          </div>
          <div rel="advanced">
            <label for="family"><?php print _("Family"); ?></label>
            <input type="text" name="family" id="family" maxlength="128" title="<?php print _("The name of the family"); ?>" <?php print (!empty($_POST['family'])?' value="' . stripslashes(strip_tags(trim($_POST['family']))) . '" ':(isset($taxon['taxon']['family'])?' value="' . $taxon['taxon']['family'] . '"':'')); ?>/>
            <br />
          </div>
          <div rel="advanced">
            <label for="subfamily"><?php print _("Tribe"); ?></label>
            <input type="text" name="tribe" id="tribe" maxlength="128" title="<?php print _("The name of the tribe"); ?>" <?php print (!empty($_POST['tribe'])?' value="' . stripslashes(strip_tags(trim($_POST['tribe']))) . '" ':(isset($taxon['taxon']['tribe'])?' value="' . $taxon['taxon']['tribe'] . '"':'')); ?>/>
            <br />
          </div>
          <div>
            <label for="genus"><strong><?php print _("Genus"); ?></strong></label>
            <input type="text" name="genus" id="genus" maxlength="128" title="<?php print _("The name of the genus"); ?>" <?php print (!empty($_POST['genus'])?' value="' . stripslashes(strip_tags(trim($_POST['genus']))) . '" ':(isset($taxon['taxon']['genus'])?' value="' . $taxon['taxon']['genus'] . '"':'')); ?>/>
            <br />
          </div>
          <div>
            <label for="species"><strong><?php print _("Species"); ?></strong></label>
            <input type="text" name="species" id="species" maxlength="128" title="<?php print _("The name of the species"); ?>" <?php print (!empty($_POST['species'])?' value="' . stripslashes(strip_tags(trim($_POST['species']))) . '" ':(isset($taxon['taxon']['species'])?' value="' . $taxon['taxon']['species'] . '"':'')); ?>/>
            <br />
          </div>
          <div rel="advanced">
            <label for="subspecies"><?php print _("Sub-Species"); ?></label>
            <input type="text" name="subspecies" id="subspecies" maxlength="128" title="<?php print _("The name of the subspecies"); ?>" <?php print (!empty($_POST['subspecies'])?' value="' . stripslashes(strip_tags(trim($_POST['subspecies']))) . '" ':(isset($taxon['taxon']['subspecies'])?' value="' . $taxon['taxon']['subspecies'] . '"':'')); ?>/>
            <br />
          </div>
          <div rel="advanced">
            <label for="comments"><?php print _("Comments"); ?></label>
            <textarea name="comments" id="comments" cols="30" rows="3" title="<?php print _("Organism comments"); ?>"><?php print ((isset($_POST['comments']) && !fullyempty($_POST['comments']))?stripslashes(strip_tags(trim($_POST['comments']))):''); ?></textarea>
            <br />
          </div>
<?php }else {
?>
          <div>
            <label for="guideline"><strong><?php print _("New species"); ?></strong></label>
            <select name="guideline" id="guideline" title="<?php print _("Select your type of submission"); ?>"><option value="unknown" rel="none"><?php print _("New unknown species"); ?></option><option value="taxon" rel="taxon"><?php print _("GenBank species"); ?></option></select>
            <br />
          </div>
          <div rel="taxon">
            <label for="taxonid"><strong><?php print _("TaxonID"); ?></strong></label>
            <input name="taxonid" id="taxonid" type="text" maxlength="16" title="<?php print _("GenBank TaxonID"); ?>" class="half" />
            <br />
          </div>
<?php }
?>
          <br />
          <input type="hidden" name="tree" value="<?php print md5((isset($step2)?'add':'next') . floor(intval(date('b')))); ?>" />
          <input type="reset" value="<?php print _("Clear"); ?>" />&nbsp;<input type="submit" value="<?php print _("Add"); ?>" />
        </div>
        </form>
        <br />
      </div>
<?php
  }elseif (!empty($_GET['species'])) {
    head('tree');
?>
      <div class="items">
        <h1><?php print $plugin['tree']['name']; ?><small><?php print $plugin['tree']['description']; ?></small></h1><br />
<?php
    $sql = sql_connect($config['db']);
    $result = sql_query('SELECT a.scientificname, a.alias, a.taxon, a.division, a.taxonid, b.description, a.tkingdom, a.tphylum, a.tclass, a.torder, a.tfamily, a.ttribe, a.tgenus, a.tspecies, a.tsubspecies, a.commonname, a.abbrivation, a.comments, a.updated, a.author, c.code FROM tree_taxonomy AS a, tree_nomenclaturalcode AS b, users AS c WHERE a.scientificname=\'' . addslashes(stripslashes(strip_tags(str_replace('_', ' ', rawurldecode($_GET['species']))))) . '\' AND b.name=a.nomenclaturalcode AND c.username=a.author;', $sql);
    if ((!strlen($r = sql_last_error($sql))) && (sql_num_rows($result) == 1)) {
      $row = sql_fetch_row($result);
?>
        <div>
          <h2><em><?php print $row[0] . '</em>' . (($_SESSION['login']['right'] >= 3)?'<small><a href="' . $config['server'] . $plugin['tree']['url'] . '/species/edit/' . rawurlencode(str_replace(' ', '_', $row[0])) . '" title="' . _("Edit") . '">' . _("edit") . '</a></small>':''); ?></h2>
<?php
      print '          <h3>' . ("Details") . "</h3>\n";
      print '          <div class="details"><div class="title">' . _("Species") . '</div><div class="label"><em>' . $row[0] . "</em></div></div>\n";
      if (!empty($row[1])) print '          <div class="details"><div class="title">' . _("Alias") . '</div><div class="label">' . $row[1] . "</div></div>\n";
      if (!empty($row[15])) print '          <div class="details"><div class="title">' . _("Common Name") . '</div><div class="label">' . $row[15] . "</div></div>\n";
      if (!empty($row[16])) print '          <div class="details"><div class="title">' . _("Abbrivation") . '</div><div class="label">' . $row[16] . "</div></div>\n";
      print "          <br />\n" . '          <div class="details"><div class="title">' . _("Nomemclature") . '</div><div class="label">' . $row[5] . "</div></div>\n";
      // print '          <div class="details"><div class="title">' . _("Division") . '</div><div class="label">' . $row[3] . "</div></div>\n";
      print '          <div class="details"><div class="title">' . _("Release") . '</div><div class="label">' . gmdate(_("d-m-Y"), strtotime($row[18])) . ' <span class="grey">(' . $row[20] . $row[19] . ")</span></div></div>\n";
      if (!empty($row[2]) || !empty($row[4])) {
        print '          <h3>' . ("Phylum") . "</h3>\n";
        if (!empty($row[4])) print '          <div class="details"><div class="title">' . _("TaxonID") . '</div><div class="label"><a href="http://www.ncbi.nlm.nih.gov/Taxonomy/Browser/wwwtax.cgi?mode=Info&amp;id=' . $row[4] . '">' . $row[4] . "</a></div></div>\n";
        if (!empty($row[2])) print '          <div class="details">' . htmlentities($row[2], ENT_COMPAT, 'ISO-8859-1') . "</div>\n";
      }
      print '          <h3>' . ("Taxonomy") . "</h3>\n";
      if (!empty($row[6])) print '          <div class="details"><div class="title">' . _("Kingdom") . '</div><div class="label">' . $row[6] . "</div></div>\n";
      if (!empty($row[7])) print '          <div class="details"><div class="title">' . _("Phylum") . '</div><div class="label">' . $row[7] . "</div></div>\n";
      if (!empty($row[8])) print '          <div class="details"><div class="title">' . _("Class") . '</div><div class="label">' . $row[8] . "</div></div>\n";
      if (!empty($row[9])) print '          <div class="details"><div class="title">' . _("Order") . '</div><div class="label">' . $row[9] . "</div></div>\n";
      if (!empty($row[10])) print '          <div class="details"><div class="title">' . _("Family") . '</div><div class="label">' . $row[10] . "</div></div>\n";
      if (!empty($row[11])) print '          <div class="details"><div class="title">' . _("Tribe") . '</div><div class="label">' . $row[11] . "</div></div>\n";
      print '          <div class="details"><div class="title">' . _("Genus") . '</div><div class="label">' . $row[12] . "</div></div>\n";
      print '          <div class="details"><div class="title">' . _("Species") . '</div><div class="label">' . $row[13] . "</div></div>\n";
      if (!empty($row[14])) print '          <div class="details"><div class="title">' . _("Subspecies/Var.") . '</div><div class="label">' . $row[14] . "</div></div>\n";
      if (!empty($row[17])) print '          <h3>' . ("Comments") . "</h3>\n            <div class=\"details\">" . preg_replace('/\[([^\|\]]*)\|([^\|\]]*)\]/', '<a href="\1">\2</a><br />', htmlentities($row[17], ENT_COMPAT, 'ISO-8859-15')) . "</div>\n";
      print '        </div>';
    }
?>
        <br />
      </div>
<?php
  }else {
  }
  foot();
}else {
  header('Location: ' . $config['server']);
}
?>