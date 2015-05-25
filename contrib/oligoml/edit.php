<?php
ob_start("ob_gzhandler");
session_start();
require_once('../includes/main.inc');

function getoligo($oligo, $sql) {
  if (!empty($oligo) && preg_match('/O(\d+)\.(\d+)/', $oligo, $matches)) {
    $result = sql_query('SELECT prefix, id FROM oligoml_oligo WHERE (prefix=' . octdec(intval($matches[1])) . ' AND id=' . octdec(intval($matches[2])) . ' AND release=1);', $sql);
    if ((!strlen($r = sql_last_error($sql))) && (sql_num_rows($result) == 1)) {
      $row = sql_fetch_row($result);
      return array('prefix' => $row[0], 'id' => $row[1]);
    }
  }
}

if ($config['login'] && ($_SESSION['login']['right'] >= 3)) {
  head('oligoml', true);
?>
      <div class="items">
        <h1><?php print _("Edit"); ?><small><?php print _("All about..."); ?></small></h1><br />
<?php
  if (!empty($_GET['edit']) && preg_match('/([OP])(\d+)\.(\d+)/', rawurldecode($_GET['edit']), $matches)) {
    $sql = sql_connect($config['db']);
    if ($matches[1] == 'O') {
      if (isset($_POST['remove']) && !empty($_POST['oligoml']) && ($_POST['oligoml'] == md5('edit' . floor(intval(date('b')))))) {
        $result = sql_query('DELETE FROM oligoml_pair WHERE ((forward_prefix=' . octdec(intval($matches[2])) . ' AND forward_id=' . octdec(intval($matches[3])) . ') OR (reverse_prefix=' . octdec(intval($matches[2])) . ' AND reverse_id=' . octdec(intval($matches[3])) . '));', $sql);
        $result = sql_query('DELETE FROM oligoml_oligo WHERE (prefix=' . octdec(intval($matches[2])) . ' AND id=' . octdec(intval($matches[3])) . ');', $sql);
        header('Location: ' . $config['server'] . $plugin['oligoml']['url'] . '/');
        exit;
      }elseif (isset($_POST['edit']) && !empty($_POST['oligoml']) && ($_POST['oligoml'] == md5('edit' . floor(intval(date('b'))))) && !empty($_POST['name']) && !empty($_POST['sequence']) && !empty($_POST['box'])) {
        if (!empty($_POST['biblio']) && !empty($_POST['pmid'])) {
          $ref = getref(intval($_POST['pmid']), $sql);
        }
        $result = sql_query('SELECT DISTINCT prefix, id, name, sequence FROM oligoml_oligo WHERE ((name=\'' . preg_replace('/[^\w\d\.\-\_\/ \(\)]/', '', stripslashes(strip_tags(trim($_POST['name'])))) . '\' OR sequence=\'' . preg_replace('/[^ATYRWKMDVHBNXCGS]/', '', strtoupper(stripslashes(strip_tags(trim($_POST['sequence']))))) . '\') AND (prefix, id)!=(' . octdec(intval($matches[2])) . ', ' . octdec(intval($matches[3])) . '));', $sql);
        if ((!strlen($r = sql_last_error($sql))) && (sql_num_rows($result) == 0)) {
          if (!empty($_POST['biblio']) && !empty($_POST['pmid'])) {
            $ref = getref(intval($_POST['pmid']), $sql);
          }
          if (!empty($_POST['design']) && ($_POST['design'] == 'manual')) {
            $design = 'design=1, program=NULL, version=NULL, design_comments=NULL';
          }elseif (!empty($_POST['design']) && ($_POST['design'] == 'software')) {
            $design = 'design=2, program=' . (!empty($_POST['program']) ? ('\'' . preg_replace('/[^\w\d\.\-\_\/ \(\)]/', '', stripslashes(strip_tags(trim($_POST['program'])))) . '\'') : 'NULL') . ', version=' . ((!empty($_POST['version']) && (floatval($_POST['version']) > 0)) ? floatval($_POST['version']) : 'NULL') . ', design_comments=' . (!empty($_POST['comments_s']) ? ('\'' . stripslashes(strip_tags(trim($_POST['comments_s']))) . '\'') : 'NULL');
          }else {
            $design = 'design=NULL, program=NULL, version=NULL, design_comments=NULL';
          }
          $result = sql_query('UPDATE oligoml_oligo SET name=\'' . preg_replace('/[^\w\d\.\-\_\/ \(\)]/', '', stripslashes(strip_tags(trim($_POST['name'])))) . '\', sequence=\'' . preg_replace('/[^ATYRWKMDVHBNXCGS]/', '', strtoupper(stripslashes(strip_tags(trim($_POST['sequence']))))) . '\', box=\'' . stripslashes(strip_tags(trim($_POST['box']))) . '\', modification=' . (!empty($_POST['modif'])?'\'' . preg_replace('/[^\w\d\.\-\_\/ \(\)]/', '', stripslashes(strip_tags(trim($_POST['modif'])))) . '\'':'NULL') . ', freezer=' . (!empty($_POST['freezer'])?'\'' . preg_replace('/[^\w\d\.\-\_\/ \(\)]/', '', stripslashes(strip_tags(trim($_POST['freezer'])))) . '\'':'NULL') . ', rank=' . (!empty($_POST['rank'])?'\'' . preg_replace('/[^\w\d\.\-\_\/ \(\)]/', '', stripslashes(strip_tags(trim($_POST['rank'])))) . '\'':'NULL') . ', comments=' . (!empty($_POST['comments'])?'\'' . preg_replace('/[^\w\d\.\-\_\/ \(\)]/', '', stripslashes(strip_tags(trim($_POST['comments'])))) . '\'':'NULL') . ', ' . $design . ', reference=' . (!empty($ref)?'\'' . $ref . '\'':'NULL') . ', updated=NOW(), release=(release+1), author=\'' . $_SESSION['login']['username'] . '\' WHERE prefix=' . octdec(intval($matches[2])) . ' AND id=' . octdec(intval($matches[3])) . ';', $sql);
          if (strlen($r = sql_last_error($sql))) {
            $error = _("Database entry error:") . ' ' . $r;
          }
        }else {
          $error = _("The same name or the same sequence already exist in the database!");
        }
        if (!isset($error)) {
          header('Location: ' . $config['server'] . $plugin['oligoml']['url'] . '/oligo/' . $matches[0]);
          exit;
        }
      }
      $result = sql_query('SELECT prefix, id, reference, name, sequence, modification, box, freezer, rank, comments, design, program, version, design_comments FROM oligoml_oligo WHERE (prefix=' . octdec(intval($matches[2])) . ' AND id=' . octdec(intval($matches[3])) . ');', $sql);
      if ((!strlen($r = sql_last_error($sql))) && (sql_num_rows($result) == 1)) {
        $row = sql_fetch_row($result);
?>
        <div>
          <h2><?php print $row[3]; ?><small>O<?php print decoct($row[0]) . '.' . decoct($row[1]); ?></small></h2>
          <form method="post" action="<?php print $config['server'] . $plugin['oligoml']['url'] . '/edit/O' . decoct($row[0]) . '.' . decoct($row[1]); ?>">
          <div><?php print _("Please use the form below to edit a Primer to the Database."); ?><br /><br />
<?php print (isset($error)?'            <strong>' . $error . "</strong><br /><br />\n":''); ?>
            <div>
              <label for="name_l"><strong><?php print _("Name"); ?></strong></label>
              <input name="name" id="name_l" type="text" maxlength="32" title="<?php print _("Short name of the primer"); ?>"<?php print (isset($_POST['name'])?' value="' . stripslashes(strip_tags(trim($_POST['name']))) . '"':(!empty($row[3])?' value="' . $row[3] . '"':'')); ?> />
              <br />
            </div>
            <div>
              <label for="sequence_l"><strong><?php print _("Sequence"); ?></strong></label>
              <input name="sequence" id="sequence_l" type="text" maxlength="128" title="<?php print _("DNA sequence of the oligonucleotide (IUPAC code) [ACGTMRWSYKVHDBN]"); ?>"<?php print (isset($_POST['sequence'])?' value="' . stripslashes(strip_tags(trim($_POST['sequence']))) . '"':(!empty($row[4])?' value="' . $row[4] . '"':'')); ?> />
              <br />
            </div>
            <div>
            <label for="modif_l"><?php print _("Modification"); ?></label>
              <input name="modif" id="modif_l" type="text" title="<?php print _("Modification of the oligonucleotide"); ?>"<?php print (!empty($_POST['modif'])?' value="' . stripslashes(strip_tags(trim($_POST['modif']))) . '"':(!empty($row[5])?' value="' . $row[5] . '"':'')); ?> />
              <br />
            </div>
            <div>
              <label for="freezer_l"><?php print _("Freezer"); ?></label>
              <input name="freezer" id="freezer_l" type="text" maxlength="64" title="<?php print _("Freezer or room of storage"); ?>"<?php print (isset($_POST['freezer'])?' value="' . stripslashes(strip_tags(trim($_POST['freezer']))) . '"':(!empty($row[7])?' value="' . $row[7] . '"':'')); ?> />
              <br />
            </div>
            <div>
              <label for="box_l"><strong><?php print _("Box"); ?></strong></label>
              <input name="box" id="box_l" type="text" maxlength="64" title="<?php print _("Box reference of storage"); ?>"<?php print (isset($_POST['box'])?' value="' . stripslashes(strip_tags(trim($_POST['box']))) . '"':(!empty($row[6])?' value="' . $row[6] . '"':'')); ?> />
              <br />
            </div>
            <div>
              <label for="rank_l"><?php print _("Rank"); ?></label>
              <input name="rank" id="rank_l" type="text" maxlength="64" title="<?php print _("Rank into the box"); ?>"<?php print (isset($_POST['rank'])?' value="' . stripslashes(strip_tags(trim($_POST['rank']))) . '"':(!empty($row[8])?' value="' . $row[8] . '"':'')); ?> />
              <br />
            </div>
            <div>
              <label for="comments_l"><?php print _("Comments"); ?></label>
              <textarea name="comments" id="comments_l" rows="4" cols="30" title="<?php print _("General comments"); ?>"><?php print (isset($_POST['comments'])?stripslashes(strip_tags(trim($_POST['comments']))):(!empty($row[9])?$row[9]:'')); ?></textarea>
              <br />
            </div>
            <div>
              <label for="design"><?php print _("Design"); ?></label>
              <select name="design" id="design" title="<?php print _("Primer pair design"); ?>"><option value="none" rel="none"<?php print ((!empty($_POST['design']) && ($_POST['design'] == 'none'))?' selected="selected"':((empty($_POST['design']) && (empty($row[10]) || ($row[10] == 0)))?' selected="selected"':'')); ?>></option><option value="manual" rel="none"<?php print ((!empty($_POST['design']) && ($_POST['design'] == 'manual'))?' selected="selected"':((empty($_POST['design']) && (!empty($row[10]) && ($row[10] == 1)))?' selected="selected"':'')); ?>><?php print _("manual"); ?></option><option value="software" rel="software"<?php print ((!empty($_POST['design']) && ($_POST['design'] == 'software'))?' selected="selected"':((empty($_POST['design']) && (!empty($row[10]) && ($row[10] == 2)))?' selected="selected"':'')); ?>><?php print _("software"); ?></option></select>
              <br />
            </div>
            <div rel="software">
              <label for="program"><?php print _("Program"); ?></label>
              <input name="program" id="program" type="text" maxlength="64" title="<?php print _("Program used to design primers"); ?>"<?php print (isset($_POST['program'])?' value="' . stripslashes(strip_tags(trim($_POST['program']))) . '"':(!empty($row[11])?' value="' . $row[11] . '"':'')); ?> />
              <br />
            </div>
            <div rel="software">
              <label for="version"><?php print _("Version"); ?></label>
              <input name="version" id="version" type="text" maxlength="16" title="<?php print _("Program version"); ?>"<?php print (isset($_POST['version'])?' value="' . stripslashes(strip_tags(trim($_POST['version']))) . '"':(!empty($row[12])?' value="' . $row[12] . '"':'')); ?> />
              <br />
            </div>
            <div rel="software">
              <label for="comments_s"><?php print _("Comments"); ?></label>
              <textarea name="comments_s" id="comments_s" rows="4" cols="30" title="<?php print _("General comments"); ?>"><?php print (isset($_POST['comments_s'])?stripslashes(strip_tags(trim($_POST['comments_s']))):(!empty($row[13])?$row[13]:'')); ?></textarea>
              <br />
            </div>
            <div>
              <label for="biblio"><?php print _("Bibliography"); ?></label>
              <input type="checkbox" name="biblio" id="biblio" rel="biblio"<?php print ((!empty($_POST['biblio']) || (!isset($_POST['pmid']) && !empty($row[2])))?' checked="checked"':''); ?> />
              <br />
            </div>
<?php
        if (!isset($_POST['pmid']) && !empty($row[2])) {
          $result_ref = sql_query('SELECT url, comments FROM reference WHERE id=' . intval($row[2]) . ';', $sql);
          if ((!strlen($r = sql_last_error($sql))) && (sql_num_rows($result_ref) == 1)) {
            $row2 = sql_fetch_row($result_ref);
          }
        }
?>
            <div rel="biblio">
              <label for="pmid"><?php print _("PMID"); ?></label>
              <input name="pmid" id="pmid" type="text" maxlength="16" title="<?php print _("PubMed Identifier of the reference"); ?>"<?php print (isset($_POST['pmid'])?' value="' . stripslashes(strip_tags(trim($_POST['pmid']))) . '"':(!empty($row2[0])?' value="' . substr($row2[0], - (strlen($row2[0]) - strpos($row2[0], '=')-1)) . '"':'')); ?> />
              <br />
            </div>

            <div rel="biblio">
              <label for="comments_b"><?php print _("Comments"); ?></label>
              <textarea name="comments_b" id="comments_b" rows="4" cols="30" title="<?php print _("General comments"); ?>"><?php print (isset($_POST['comments_b'])?stripslashes(strip_tags(trim($_POST['comments_b']))):(!empty($row2[1])?$row2[1]:'')); ?></textarea>
              <br />
            </div>
            <br />
            <input type="hidden" name="oligoml" value="<?php print md5('edit' . floor(intval(date('b')))); ?>" />
            <input type="reset" value="<?php print _("Clear"); ?>" />&nbsp;<input type="submit" name="edit" value="<?php print _("Edit"); ?>" />&nbsp;<input type="submit" name="remove" value="<?php print _("Remove"); ?>" onclick="return confirm('<?php print _("Are you sure you want to delete?"); ?>')" />
          </div>
          </form>
<?php
      }
      print "        </div>\n";
    }elseif ($matches[1] == 'P') {
      if (isset($_POST['remove']) && !empty($_POST['oligoml']) && ($_POST['oligoml'] == md5('edit' . floor(intval(date('b')))))) {
        $result = sql_query('DELETE FROM oligoml_pair WHERE (prefix=' . octdec(intval($matches[2])) . ' AND id=' . octdec(intval($matches[3])) . ');', $sql);
        header('Location: ' . $config['server'] . $plugin['oligoml']['url']);
        exit;
      }elseif (isset($_POST['edit']) && !empty($_POST['oligoml']) && ($_POST['oligoml'] == md5('edit' . floor(intval(date('b'))))) && (!empty($_POST['specificity']) || (!empty($_POST['taxonid']) && (intval($_POST['taxonid']) > 0))) && (!empty($_POST['target']) || (!empty($_POST['geneid']) && (intval($_POST['geneid']) > 0))) && (!empty($_POST['length']) || (!empty($_POST['gi']) && (intval($_POST['gi']) > 0))) && !empty($_POST['forward']) && !empty($_POST['reverse'])) {
        if (($ref[0] = getoligo(stripslashes(strip_tags(trim($_POST['forward']))), $sql)) && ($ref[1] = getoligo(stripslashes(strip_tags(trim($_POST['reverse']))), $sql))) {
          if (!empty($_POST['biblio']) && !empty($_POST['pmid'])) {
            $reference = getref(intval($_POST['pmid']), $sql);
          }
          $result = sql_query('SELECT prefix, id FROM oligoml_pair WHERE (forward_prefix=' . $ref[0]['prefix'] . ' AND forward_id=' . $ref[0]['id'] . ' AND reverse_prefix=' . $ref[1]['prefix'] . ' AND reverse_id=' . $ref[1]['id'] . ' AND (prefix, id)!=(' . octdec(intval($matches[2])) . ', ' . octdec(intval($matches[3])) . '));', $sql);
          if ((!strlen($r = sql_last_error($sql))) && (sql_num_rows($result) == 0)) {
            $result = sql_query('UPDATE oligoml_pair SET forward_prefix=' . $ref[0]['prefix'] . ', forward_id=' . $ref[0]['id'] . ', reverse_prefix=' . $ref[1]['prefix'] . ', reverse_id=' . $ref[1]['id'] . ', species=' . (!empty($_POST['specificity'])?'\'' . stripslashes(strip_tags(trim($_POST['specificity']))) . '\'':'NULL') . ', speciesid=' . ((!empty($_POST['taxonid']) && (intval($_POST['taxonid']) > 0))?intval($_POST['taxonid']):'NULL') . ', locus=' . (!empty($_POST['target'])?'\'' . stripslashes(strip_tags(trim($_POST['target']))) . '\'':'NULL') . ', geneid=' . ((!empty($_POST['geneid']) && (intval($_POST['geneid']) > 0))?intval($_POST['geneid']):'NULL') . ', amplicon=' . (!empty($_POST['length'])?'\'' . stripslashes(strip_tags(trim($_POST['length']))) . '\'':'NULL') . ', sequenceid=' . ((!empty($_POST['gi']) && (intval($_POST['gi']) > 0))?intval($_POST['gi']):'NULL') . ', location=' . (!empty($_POST['location'])?'\'' . stripslashes(strip_tags(trim($_POST['location']))) . '\'':'NULL') . ', pcr=' . (!empty($_POST['pcr'])?'\'' . stripslashes(strip_tags(trim($_POST['pcr']))) . '\'':'NULL') . ', buffer=' . (!empty($_POST['buffer'])?'\'' . stripslashes(strip_tags(trim($_POST['buffer']))) . '\'':'NULL') . ', comments=' . (!empty($_POST['comments_a'])?'\'' . stripslashes(strip_tags(trim($_POST['comments_a']))) . '\'':'NULL') . ', reference=' . (!empty($reference)?'\'' . $reference . '\'':'NULL') . ', updated=NOW(), release=(release+1), author=\'' . $_SESSION['login']['username'] . '\' WHERE prefix=' . octdec(intval($matches[2])) . ' AND id=' . octdec(intval($matches[3])) . ';', $sql);
            if (strlen($r = sql_last_error($sql))) {
              $error = _("Database entry error:") . ' ' . $r;
            }
          }else {
            $error = _("The same primer association already exist in the database!");
          }
        }else {
          $error = _("Primer unknown in the database!");
        }
        if (!isset($error)) {
          header('Location: ' . $config['server'] . $plugin['oligoml']['url'] . '/pair/' . $matches[0]);
          exit;
        }
      }
      $result = sql_query('SELECT prefix, id, reference, forward_prefix, forward_id, reverse_prefix, reverse_id, speciesid, species, geneid, locus, amplicon, sequenceid, location, pcr, buffer, comments, reference FROM oligoml_pair WHERE (prefix=' . octdec(intval($matches[2])) . ' AND id=' . octdec(intval($matches[3])) . ');', $sql);
      if ((!strlen($r = sql_last_error($sql))) && (sql_num_rows($result) == 1)) {
        $row = sql_fetch_row($result);
?>
        <div>
          <h2>O<?php print decoct($row[3]) . '.' . decoct($row[4]) . ' / O' . decoct($row[5]) . '.' . decoct($row[6]) ; ?><small>P<?php print decoct($row[0]) . '.' . decoct($row[1]); ?></small></h2>
          <form method="post" action="<?php print $config['server'] . $plugin['oligoml']['url'] . '/edit/P' . decoct($row[0]) . '.' . decoct($row[1]); ?>">
          <div><?php print _("Please use the form below to edit a Primer set to the Database."); ?><br /><br />
<?php print (isset($error)?'            <strong>' . $error . "</strong><br /><br />\n":''); ?>
            <div>
              <label for="forward"><strong><?php print _("Forward primer"); ?></strong></label>
              <input name="forward" id="forward" type="text" maxlength="16" title="<?php print _("Reference of the forward oligonucleotide in the databank"); ?>"<?php print (isset($_POST['forward'])?' value="' . strip_tags(trim($_POST['forward'])) . '"':' value="O' . decoct($row[3]) . '.' . decoct($row[4]) . '"'); ?> />
              <br />
            </div>
            <div>
              <label for="reverse"><strong><?php print _("Reverse primer"); ?></strong></label>
              <input name="reverse" id="reverse" type="text" maxlength="16" title="<?php print _("Reference of the reverse oligonucleotide in the databank"); ?>"<?php print (isset($_POST['reverse'])?' value="' . strip_tags(trim($_POST['reverse'])) . '"':' value="O' . decoct($row[5]) . '.' . decoct($row[6]) . '"'); ?> />
              <br />
            </div>
            <div>
              <label for="specificity"><strong><?php print _("Specie"); ?></strong></label>
              <input name="specificity" id="specificity" type="text" maxlength="128" title="<?php print _("Species or clade specificity [latin name]"); ?>"<?php print (isset($_POST['specificity'])?' value="' . stripslashes(strip_tags(trim($_POST['specificity']))) . '"':(!empty($row[8])?' value="' . $row[8] . '"':'')); ?> />&nbsp;<?php print _("or"); ?>&nbsp;<input name="taxonid" class="half" id="taxonid" type="text" maxlength="128" title="<?php print _("Species or clade specificity[Genbank TaxonID]"); ?>"<?php print (isset($_POST['taxonid'])?' value="' . stripslashes(strip_tags(trim($_POST['taxonid']))) . '"':(!empty($row[7])?' value="' . $row[7] . '"':'')); ?> />
              <br />
            </div>
            <div>
              <label for="target"><strong><?php print _("Target"); ?></strong></label>
              <input name="target" id="target" type="text" maxlength="64" title="<?php print _("Gene or DNA regions targeted [name]"); ?>"<?php print (isset($_POST['target'])?' value="' . stripslashes(strip_tags(trim($_POST['target']))) . '"':(!empty($row[10])?' value="' . $row[10] . '"':'')); ?> />&nbsp;<?php print _("or"); ?>&nbsp;<input name="geneid" class="half" id="geneid" type="text" maxlength="128" title="<?php print _("Gene or DNA regions targeted [Genbank GeneID]"); ?>"<?php print (isset($_POST['geneid'])?' value="' . stripslashes(strip_tags(trim($_POST['geneid']))) . '"':(!empty($row[9])?' value="' . $row[9] . '"':'')); ?> />
              <br />
            </div>
            <div>
              <label for="length"><strong><?php print _("Product length"); ?></strong></label>
              <input name="length" id="length" type="text" maxlength="64" title="<?php print _("Product length (bp)"); ?>"<?php print (isset($_POST['length'])?' value="' . stripslashes(strip_tags(trim($_POST['length']))) . '"':(!empty($row[11])?' value="' . $row[11] . '"':'')); ?> />&nbsp;<?php print _("or"); ?>&nbsp;<input name="gi" class="half" id="gi" type="text" maxlength="128" title="<?php print _("GenBank SequenceID (GI number)"); ?>"<?php print (isset($_POST['gi'])?' value="' . stripslashes(strip_tags(trim($_POST['gi']))) . '"':(!empty($row[12])?' value="' . $row[12] . '"':'')); ?> />
              <br />
            </div>
            <div>
              <label for="location"><?php print _("Location"); ?></label>
              <input name="location" id="location" type="text" maxlength="64" title="<?php print _("Location of the amplicon (e.g. exon 2-intron 2)"); ?>"<?php print (isset($_POST['location'])?' value="' . stripslashes(strip_tags(trim($_POST['location']))) . '"':(!empty($row[13])?' value="' . $row[13] . '"':'')); ?> />
              <br />
            </div>
            <div>
              <label for="pcr"><?php print _("PCR conditions"); ?></label>
              <input name="pcr" id="pcr" type="text" maxlength="256" title="<?php print _("PCR cycles (e.g. 5'@95, 38x:(60''@95, 30''@55, 60''@72), 7'@72)"); ?>"<?php print (isset($_POST['pcr'])?' value="' . stripslashes(strip_tags(trim($_POST['pcr']))) . '"':(!empty($row[14])?' value="' . $row[14] . '"':'')); ?> />
              <br />
            </div>
            <div>
              <label for="buffer"><?php print _("Buffer"); ?></label>
              <input name="buffer" id="buffer" type="text" maxlength="256" title="<?php print _("Buffer specificity"); ?>"<?php print (isset($_POST['buffer'])?' value="' . stripslashes(strip_tags(trim($_POST['buffer']))) . '"':(!empty($row[15])?' value="' . $row[15] . '"':'')); ?> />
              <br />
            </div>
            <div>
              <label for="comments_a"><?php print _("Comments"); ?></label>
              <textarea name="comments_a" id="comments_a" rows="4" cols="30" title="<?php print _("General comments"); ?>"><?php print (isset($_POST['comments_a'])?stripslashes(strip_tags(trim($_POST['comments_a']))):(!empty($row[16])?$row[16]:'')); ?></textarea>
              <br />
            </div>
            <div>
              <label for="biblio"><?php print _("Bibliography"); ?></label>
              <input type="checkbox" name="biblio" id="biblio" rel="biblio"<?php print ((!empty($_POST['biblio']) || (!isset($_POST['pmid']) && !empty($row[2])))?' checked="checked"':''); ?> />
              <br />
            </div>
<?php
        if (!isset($_POST['pmid']) && !empty($row[2])) {
          $result_ref = sql_query('SELECT pmid, comments FROM reference WHERE id=' . intval($row[2]) . ';', $sql);
          if ((!strlen($r = sql_last_error($sql))) && (sql_num_rows($result_ref) == 1)) {
            $row2 = sql_fetch_row($result_ref);
          }
        }
?>
            <div rel="biblio">
              <label for="pmid"><?php print _("PMID"); ?></label>
              <input name="pmid" id="pmid" type="text" maxlength="16" title="<?php print _("PubMed Identifier of the reference"); ?>"<?php print (isset($_POST['pmid'])?' value="' . stripslashes(strip_tags(trim($_POST['pmid']))) . '"':(!empty($row2[0])?' value="' . substr($row2[0], - (strlen($row2[0]) - strpos($row2[0], '=')-1)) . '"':'')); ?> />
              <br />
            </div>
            <div rel="biblio">
              <label for="comments_b"><?php print _("Comments"); ?></label>
              <textarea name="comments_b" id="comments_b" rows="4" cols="30" title="<?php print _("General comments"); ?>"><?php print (isset($_POST['comments_b'])?stripslashes(strip_tags(trim($_POST['comments_b']))):(!empty($row2[1])?$row2[1]:'')); ?></textarea>
              <br />
            </div>
            <br />
            <input type="hidden" name="oligoml" value="<?php print md5('edit' . floor(intval(date('b')))); ?>" />
            <input type="reset" value="<?php print _("Clear"); ?>" /><input type="submit" name="edit" value="<?php print _("Edit"); ?>" />&nbsp;<input type="submit" name="remove" value="<?php print _("Remove"); ?>" onclick="return confirm('<?php print _("Are you sure you want to delete?"); ?>')" />
          </div>
          </form>
<?php
        print "        </div>\n";
      }
    }
  }
?>
        </div>
<?php
  foot();
}else {
  header('Location: ' . $config['server']);
}
?>