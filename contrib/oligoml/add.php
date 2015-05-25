<?php
ob_start("ob_gzhandler");
session_start();
require_once('../includes/main.inc');

function getoligo($oligo, $sql) {
  if (!empty($oligo)) {
    if (preg_match('/O(\d+)\.(\d+)/', $oligo, $matches)) {
      $result = sql_query('SELECT prefix, id FROM oligoml_oligo WHERE (prefix=' . octdec(intval($matches[1])) . ' AND id=' . octdec(intval($matches[2])) . ');', $sql);
    }else {
      $oligo = preg_replace('/[^\w\d\.\-\_\/ \(\)]/', '', stripslashes(strip_tags(trim($oligo))));
      $result = sql_query('SELECT prefix, id FROM oligoml_oligo WHERE name=\'' . $oligo . '\';', $sql);
    }
    if ((!strlen($r = sql_last_error($sql))) && (sql_num_rows($result) == 1)) {
      $row = sql_fetch_row($result);
      return array('prefix' => $row[0], 'id' => $row[1]);
    }
  }
}

class XMLParser {
  var $stack = array();
  function startTag($parser, $name, $attrs) {
    $tag = array('name' => $name, 'attrs' => $attrs);
    array_push($this->stack, $tag);
  }
  function cdata($parser, $cdata) {
    if (trim($cdata))
      if (isset($this->stack[count($this->stack)-1]['cdata'])) {
        $this->stack[count($this->stack)-1]['cdata'] .= $cdata;
      }else {
        $this->stack[count($this->stack)-1]['cdata'] = $cdata;
      }
    }
    function endTag($parser, $name) {
      $this->stack[count($this->stack)-2]['children'][] = $this->stack[count($this->stack)-1];
      array_pop($this->stack);
    }
  }

  function readXML($buffer) {
    $xml_parser = xml_parser_create();
    $my_parser = new XMLParser();
    xml_set_object($xml_parser, $my_parser);
    xml_set_element_handler($xml_parser, "startTag", "endTag");
    xml_set_character_data_handler($xml_parser, "cdata");
    $data = xml_parse($xml_parser, $buffer);
    if (!$data)
      return false;
    xml_parser_free($xml_parser);
    return $my_parser->stack[0];
  }


function step($step = 1, $error) {
  global $config, $plugin;
  switch ($step) {
    case 1:
?>
          <form method="post" action="<?php print $config['server'] . $plugin['oligoml']['url']; ?>/add" enctype="multipart/form-data">
          <div>
            <?php print _("What type of entry are you submitting? There are three ways to add a oligonucleotide: manually (one at a time), or from a file containing one or more oligonucleotides, or as a primer set of two oligonucleotides."); ?><br /><br />
<?php print (isset($error)?'            <strong>' . $error . "</strong><br /><br />\n":''); ?>
            <div>
              <label for="add"><strong><?php print _("Add"); ?></strong></label>
              <select name="add" id="add" title="<?php print _("Select your type of submission"); ?>"><option value="oligo" rel="none"<?php print ((empty($_POST['add']) || ($_POST['add'] == 'oligo'))?' selected="selected"':''); ?>><?php print _("an oligonucleotide"); ?></option><option value="oligofile" rel="oligofile"<?php print ((!empty($_POST['add']) && ($_POST['add'] == 'oligofile'))?' selected="selected"':''); ?>><?php print _("a file of oligonucleotides"); ?></option><option value="pair" rel="pair"<?php print ((!empty($_POST['add']) && ($_POST['add'] == 'pair'))?' selected="selected"':''); ?>><?php print _("a primers pair"); ?></option></select>
              <br />
            </div>
            <div rel="pair">
              <label for="primer_l"><?php print _("Existing forward primer"); ?></label>
              <input type="checkbox" name="primer[0]" id="primer_l" rel="forward"<?php print (!empty($_POST['primer'][0])?' checked="checked"':''); ?> />
              <br />
            </div>
            <div rel="forward">
              <label for="ref"><?php print _("Reference"); ?></label>
              <input name="ref[0]" id="ref" type="text" maxlength="16" title="<?php print _("Reference of the oligonucleotide in the databank"); ?>"<?php print (!empty($_POST['ref'][0])?' value="' . strip_tags(trim($_POST['ref'][0])) . '"':''); ?> />
              <br />
            </div>
            <div rel="pair">
              <label for="primer_r"><?php print _("Existing reverse primer"); ?></label>
              <input type="checkbox" name="primer[1]" id="primer_r" rel="reverse"<?php print (!empty($_POST['primer'][1])?' checked="checked"':''); ?> />
              <br />
            </div>
            <div rel="reverse">
              <label for="ref"><?php print _("Reference"); ?></label>
              <input name="ref[1]" id="ref" type="text" maxlength="16" title="<?php print _("Reference of the oligonucleotide in the databank"); ?>"<?php print (!empty($_POST['ref'][1])?' value="' . strip_tags(trim($_POST['ref'][1])) . '"':''); ?> />
              <br />
            </div>
            <div rel="oligofile">
              <label for="oligofile"><?php print _("File name"); ?></label>
              <input name="oligofile" id="oligofile" type="file" title="<?php print _("File containing one or more oligonucleotides"); ?>" />
              <br />
            </div>
            <div rel="oligofile"><?php print _("The file must be a text file containing the following information, separated by tabs: Name [tab] Sequence [tab] Box ([tab] Rank [tab] Freezer [tab] PMID [tab] Comments) or an OligoML file. The program will extract the primer information from the file. (From Excel, choose 'Save AS', then 'Text (Tab delimited)' as format)"); ?><br /></div>
            <br />
            <input type="hidden" name="oligoml" value="<?php print md5('next' . floor(intval(date('b')))); ?>" />
            <input type="reset" value="<?php print _("Clear"); ?>" />&nbsp;<input type="submit" value="<?php print _("Next"); ?>" />
          </div>
          </form>
<?php
      break;
    case 2:
?>
          <form method="post" action="<?php print $config['server'] . $plugin['oligoml']['url']; ?>/add">
          <div>
            <?php print _("Please use the form below to submit a primer set to the Database."); ?><br /><br />
<?php print (isset($error)?'            <strong>' . $error . "</strong><br /><br />\n":'');
?>
            <h2><?php print _("Amplicon"); ?></h2>
            <div>
              <label for="advanced"><?php print _("Advanced"); ?></label>
              <input type="checkbox" name="advanced" id="advanced" rel="advanced"<?php print (!empty($_POST['advanced'])?' checked="checked"':''); ?> />
              <br />
            </div>
            <div>
              <label for="specificity"><strong><?php print _("Specie"); ?></strong></label>
              <input name="specificity" id="specificity" type="text" maxlength="128" title="<?php print _("Species or clade specificity"); ?>"<?php print (!empty($_POST['specificity'])?' value="' . stripslashes(strip_tags(trim($_POST['specificity']))) . '"':''); ?> />
              <br />
            </div>
            <div>
              <label for="target"><strong><?php print _("Target"); ?></strong></label>
              <input name="target" id="target" type="text" maxlength="64" title="<?php print _("Gene or DNA regions targeted"); ?>"<?php print (!empty($_POST['target'])?' value="' . stripslashes(strip_tags(trim($_POST['target']))) . '"':''); ?> />
              <br />
            </div>
            <div>
              <label for="length"><strong><?php print _("Product length"); ?></strong></label>
              <input name="length" id="length" type="text" maxlength="64" title="<?php print _("Product length (bp)"); ?>"<?php print (!empty($_POST['length'])?' value="' . stripslashes(strip_tags(trim($_POST['length']))) . '"':''); ?> />
              <br />
            </div>
            <div>
              <label for="location"><?php print _("Location"); ?></label>
              <input name="location" id="location" type="text" maxlength="64" title="<?php print _("Location of the amplicon (e.g. exon 2-intron 2)"); ?>"<?php print (!empty($_POST['location'])?' value="' . stripslashes(strip_tags(trim($_POST['location']))) . '"':''); ?> />
              <br />
            </div>
            <div rel="advanced">
              <label for="pcr"><?php print _("PCR conditions"); ?></label>
              <input name="pcr" id="pcr" type="text" maxlength="256" title="<?php print _("PCR cycles (e.g. 5'@95, 38x:(60''@95, 30''@55, 60''@72), 7'@72)"); ?>"<?php print (!empty($_POST['pcr'])?' value="' . stripslashes(strip_tags(trim($_POST['pcr']))) . '"':''); ?> />
              <br />
            </div>
            <div rel="advanced">
               <label for="buffer"><?php print _("Buffer"); ?></label>
               <input name="buffer" id="buffer" type="text" maxlength="256" title="<?php print _("Buffer specificity"); ?>"<?php print (!empty($_POST['buffer'])?' value="' . stripslashes(strip_tags(trim($_POST['buffer']))) . '"':''); ?> />
               <br />
            </div>
            <div rel="advanced">
              <label for="comments_a"><?php print _("Comments"); ?></label>
              <textarea name="comments_a" id="comments_a" rows="4" cols="30" title="<?php print _("General comments"); ?>"><?php print (!empty($_POST['comments_a'])?stripslashes(strip_tags(trim($_POST['comments_a']))):''); ?></textarea>
              <br />
            </div>
            <div rel="advanced">
              <label for="design"><?php print _("Design"); ?></label>
              <select name="design" id="design" title="<?php print _("Primer pair design"); ?>"><option value="none" rel="none"<?php print ((empty($_POST['design']) || ($_POST['design'] == 'none'))?' selected="selected"':''); ?>></option><option value="manual" rel="none"<?php print ((!empty($_POST['design']) && ($_POST['design'] == 'manual'))?' selected="selected"':''); ?>><?php print _("manual"); ?></option><option value="software" rel="software"<?php print ((!empty($_POST['design']) && ($_POST['design'] == 'software'))?' selected="selected"':''); ?>><?php print _("software"); ?></option></select>
              <br />
            </div>
            <div rel="software">
              <label for="program"><?php print _("Program"); ?></label>
              <input name="program" id="program" type="text" maxlength="64" title="<?php print _("Program used to design primers"); ?>"<?php print (!empty($_POST['program'])?' value="' . stripslashes(strip_tags(trim($_POST['program']))) . '"':''); ?> />
              <br />
            </div>
            <div rel="software">
              <label for="version"><?php print _("Version"); ?></label>
              <input name="version" id="version" type="text" maxlength="16" title="<?php print _("Program version"); ?>"<?php print (!empty($_POST['version'])?' value="' . stripslashes(strip_tags(trim($_POST['version']))) . '"':''); ?> />
              <br />
            </div>
            <div rel="software">
              <label for="comments_s"><?php print _("Comments"); ?></label>
              <textarea name="comments_s" id="comments_s" rows="4" cols="30" title="<?php print _("General comments"); ?>"><?php print (!empty($_POST['comments_s'])?stripslashes(strip_tags(trim($_POST['comments_s']))):''); ?></textarea>
              <br />
            </div>
            <div rel="advanced">
              <label for="biblio"><?php print _("Bibliography"); ?></label>
              <input type="checkbox" name="biblio" id="biblio" rel="biblio"<?php print (!empty($_POST['biblio'])?' checked="checked"':''); ?> />
              <br />
            </div>
            <div rel="biblio">
              <label for="pmid"><?php print _("PMID"); ?></label>
              <input name="pmid" id="pmid" type="text" maxlength="16" title="<?php print _("PubMed Identifier of the reference"); ?>"<?php print (!empty($_POST['pmid'])?' value="' . stripslashes(strip_tags(trim($_POST['pmid']))) . '"':''); ?> />
              <br />
            </div>
            <div rel="biblio">
              <label for="comments_b"><?php print _("Comments"); ?></label>
              <textarea name="comments_b" id="comments_b" rows="4" cols="30" title="<?php print _("General comments"); ?>"><?php print (!empty($_POST['comments_b'])?stripslashes(strip_tags(trim($_POST['comments_b']))):''); ?></textarea>
              <br />
            </div>
<?php if (!isset($_SESSION['oligoml']['forward'])) {
?>
            <h2><?php print _("Forward primer"); ?></h2>
            <div>
              <label for="name_l"><strong><?php print _("Name"); ?></strong></label>
              <input name="name[0]" id="name_l" type="text" maxlength="32" title="<?php print _("Short name of the primer"); ?>"<?php print (!empty($_POST['name'][0])?' value="' . stripslashes(strip_tags(trim($_POST['name'][0]))) . '"':''); ?> />
              <br />
            </div>
            <div>
              <label for="sequence_l"><strong><?php print _("Sequence"); ?></strong></label>
              <input name="sequence[0]" id="sequence_l" type="text" maxlength="128" title="<?php print _("DNA sequence of the oligonucleotide (IUPAC code) [ACGTMRWSYKVHDBN]"); ?>"<?php print (!empty($_POST['sequence'][0])?' value="' . stripslashes(strip_tags(trim($_POST['sequence'][0]))) . '"':''); ?> />
              <br />
            </div>
            <div rel="advanced">
            <label for="modif_l"><?php print _("Modification"); ?></label>
              <input name="modif[0]" id="modif_l" type="text" title="<?php print _("Modification of the oligonucleotide"); ?>"<?php print (!empty($_POST['modif'][0])?' value="' . stripslashes(strip_tags(trim($_POST['modif'][0]))) . '"':''); ?> />
              <br />
            </div>
            <div rel="advanced">
            <label for="freezer_l"><?php print _("Freezer"); ?></label>
              <input name="freezer[0]" id="freezer_l" type="text" maxlength="64" title="<?php print _("Freezer or room of storage"); ?>"<?php print (!empty($_POST['freezer'][0])?' value="' . stripslashes(strip_tags(trim($_POST['freezer'][0]))) . '"':''); ?> />
              <br />
              </div>
            <div>
              <label for="box_l"><strong><?php print _("Box"); ?></strong></label>
              <input name="box[0]" id="box_l" type="text" maxlength="64" title="<?php print _("Box reference of storage"); ?>"<?php print (!empty($_POST['box'][0])?' value="' . stripslashes(strip_tags(trim($_POST['box'][0]))) . '"':''); ?> />
              <br />
            </div>
            <div rel="advanced">
              <label for="rank_l"><?php print _("Rank"); ?></label>
              <input name="rank[0]" id="rank_l" type="text" maxlength="64" title="<?php print _("Rank into the box"); ?>"<?php print (!empty($_POST['rank'][0])?' value="' . stripslashes(strip_tags(trim($_POST['rank'][0]))) . '"':''); ?> />
              <br />
            </div>
            <div rel="advanced">
              <label for="comments_l"><?php print _("Comments"); ?></label>
              <textarea name="comments[0]" id="comments_l" rows="4" cols="30" title="<?php print _("General comments"); ?>"><?php print (!empty($_POST['comments'][0])?stripslashes(strip_tags(trim($_POST['comments'][0]))):''); ?></textarea>
              <br />
            </div>
<?php }
      if (!isset($_SESSION['oligoml']['reverse'])) {
?>
            <h2><?php print _("Reverse primer"); ?></h2>
            <div>
              <label for="name_r"><strong><?php print _("Name"); ?></strong></label>
              <input name="name[1]" id="name_r" type="text" maxlength="32" title="<?php print _("Short name of the primer"); ?>"<?php print (!empty($_POST['name'][1])?' value="' . stripslashes(strip_tags(trim($_POST['name'][1]))) . '"':''); ?> />
              <br />
            </div>
            <div>
              <label for="sequence_r"><strong><?php print _("Sequence"); ?></strong></label>
              <input name="sequence[1]" id="sequence_r" type="text" maxlength="128" title="<?php print _("DNA sequence of the oligonucleotide (IUPAC code) [ACGTMRWSYKVHDBN]"); ?>"<?php print (!empty($_POST['sequence'][1])?' value="' . stripslashes(strip_tags(trim($_POST['sequence'][1]))) . '"':''); ?> />
              <br />
            </div>
            <div rel="advanced">
            <label for="modif_r"><?php print _("Modification"); ?></label>
              <input name="modif[1]" id="modif_r" type="text" title="<?php print _("Modification of the oligonucleotide"); ?>"<?php print (!empty($_POST['modif'][1])?' value="' . stripslashes(strip_tags(trim($_POST['modif'][1]))) . '"':''); ?> />
              <br />
            </div>
            <div rel="advanced">
              <label for="freezer_r"><?php print _("Freezer"); ?></label>
              <input name="freezer[1]" id="freezer_r" type="text" maxlength="64" title="<?php print _("Freezer or room of storage"); ?>"<?php print (!empty($_POST['freezer'][1])?' value="' . stripslashes(strip_tags(trim($_POST['freezer'][1]))) . '"':''); ?> />
              <br />
            </div>
            <div>
              <label for="box_r"><strong><?php print _("Box"); ?></strong></label>
              <input name="box[1]" id="box_r" type="text" maxlength="16" title="<?php print _("Box reference of storage"); ?>"<?php print (!empty($_POST['box'][1])?' value="' . stripslashes(strip_tags(trim($_POST['box'][1]))) . '"':''); ?> />
              <br />
            </div>
            <div rel="advanced">
              <label for="rank_r"><?php print _("Rank"); ?></label>
              <input name="rank[1]" id="rank_r" type="text" maxlength="64" title="<?php print _("Rank into the box"); ?>"<?php print (!empty($_POST['rank'][1])?' value="' . stripslashes(strip_tags(trim($_POST['rank'][1]))) . '"':''); ?> />
              <br />
            </div>
            <div rel="advanced">
              <label for="comments_r"><?php print _("Comments"); ?></label>
              <textarea name="comments[1]" id="comments_r" rows="4" cols="30" title="<?php print _("General comments"); ?>"><?php print (!empty($_POST['comments'][1])?stripslashes(strip_tags(trim($_POST['comments'][1]))):''); ?></textarea>
              <br />
            </div>
<?php }
?>
            <br />
            <input type="hidden" name="oligoml" value="<?php print md5('add' . floor(intval(date('b')))); ?>" />
            <input type="reset" value="<?php print _("Clear"); ?>" />&nbsp;<input type="submit" value="<?php print _("Add"); ?>" />
          </div>
          </form>
<?php
      break;
    case 3:
?>
          <form method="post" action="<?php print $config['server'] . $plugin['oligoml']['url']; ?>/add">
          <div>
            <?php print _("Please use the form below to submit a Primer to the Database."); ?><br /><br />
<?php print (isset($error)?'            <strong>' . $error . "</strong><br /><br />\n":''); ?>
            <div>
              <label for="advanced"><?php print _("Advanced"); ?></label>
              <input type="checkbox" name="advanced" id="advanced" rel="advanced"<?php print (!empty($_POST['advanced'])?' checked="checked"':''); ?> />
              <br />
            </div>
            <div>
              <label for="name_l"><strong><?php print _("Name"); ?></strong></label>
              <input name="name[0]" id="name_l" type="text" maxlength="32" title="<?php print _("Short name of the primer"); ?>"<?php print (!empty($_POST['name'][0])?' value="' . stripslashes(strip_tags(trim($_POST['name'][0]))) . '"':''); ?> />
              <br />
            </div>
            <div>
              <label for="sequence_l"><strong><?php print _("Sequence"); ?></strong></label>
              <input name="sequence[0]" id="sequence_l" type="text" maxlength="128" title="<?php print _("DNA sequence of the oligonucleotide (IUPAC code) [ACGTMRWSYKVHDBN]"); ?>"<?php print (!empty($_POST['sequence'][0])?' value="' . stripslashes(strip_tags(trim($_POST['sequence'][0]))) . '"':''); ?> />
              <br />
            </div>
            <div rel="advanced">
            <label for="modif_l"><?php print _("Modification"); ?></label>
              <input name="modif[0]" id="modif_l" type="text" title="<?php print _("Modification of the oligonucleotide"); ?>"<?php print (!empty($_POST['modif'][0])?' value="' . stripslashes(strip_tags(trim($_POST['modif'][0]))) . '"':''); ?> />
              <br />
            </div>
            <div rel="advanced">
              <label for="freezer_l"><?php print _("Freezer"); ?></label>
              <input name="freezer[0]" id="freezer_l" type="text" maxlength="64" title="<?php print _("Freezer or room of storage"); ?>"<?php print (!empty($_POST['freezer'][0])?' value="' . stripslashes(strip_tags(trim($_POST['freezer'][0]))) . '"':''); ?> />
              <br />
            </div>
            <div>
              <label for="box_l"><strong><?php print _("Box"); ?></strong></label>
              <input name="box[0]" id="box_l" type="text" maxlength="64" title="<?php print _("Box reference of storage"); ?>"<?php print (!empty($_POST['box'][0])?' value="' . stripslashes(strip_tags(trim($_POST['box'][0]))) . '"':''); ?> />
              <br />
            </div>
            <div rel="advanced">
              <label for="rank_l"><?php print _("Rank"); ?></label>
              <input name="rank[0]" id="rank_l" type="text" maxlength="64" title="<?php print _("Rank into the box"); ?>"<?php print (!empty($_POST['rank'][0])?' value="' . stripslashes(strip_tags(trim($_POST['rank'][0]))) . '"':''); ?> />
              <br />
            </div>
            <div rel="advanced">
              <label for="comments_l"><?php print _("Comments"); ?></label>
              <textarea name="comments[0]" id="comments_l" rows="4" cols="30" title="<?php print _("General comments"); ?>"><?php print (!empty($_POST['comments'][0])?stripslashes(strip_tags(trim($_POST['comments'][0]))):''); ?></textarea>
              <br />
            </div>
            <div rel="advanced">
              <label for="design"><?php print _("Design"); ?></label>
              <select name="design" id="design" title="<?php print _("Primer pair design"); ?>"><option value="none" rel="none"<?php print ((empty($_POST['design']) || ($_POST['design'] == 'none'))?' selected="selected"':''); ?>></option><option value="manual" rel="none"<?php print ((!empty($_POST['design']) && ($_POST['design'] == 'manual'))?' selected="selected"':''); ?>><?php print _("manual"); ?></option><option value="software" rel="software"<?php print ((!empty($_POST['design']) && ($_POST['design'] == 'software'))?' selected="selected"':''); ?>><?php print _("software"); ?></option></select>
              <br />
            </div>
            <div rel="software">
              <label for="program"><?php print _("Program"); ?></label>
              <input name="program" id="program" type="text" maxlength="64" title="<?php print _("Program used to design primers"); ?>"<?php print (!empty($_POST['program'])?' value="' . stripslashes(strip_tags(trim($_POST['program']))) . '"':''); ?> />
              <br />
            </div>
            <div rel="software">
              <label for="version"><?php print _("Version"); ?></label>
              <input name="version" id="version" type="text" maxlength="16" title="<?php print _("Program version"); ?>"<?php print (!empty($_POST['version'])?' value="' . stripslashes(strip_tags(trim($_POST['version']))) . '"':''); ?> />
              <br />
            </div>
            <div rel="software">
              <label for="comments_s"><?php print _("Comments"); ?></label>
              <textarea name="comments_s" id="comments_s" rows="4" cols="30" title="<?php print _("General comments"); ?>"><?php print (!empty($_POST['comments_s'])?stripslashes(strip_tags(trim($_POST['comments_s']))):''); ?></textarea>
              <br />
            </div>
            <div rel="advanced">
              <label for="biblio"><?php print _("Bibliography"); ?></label>
              <input type="checkbox" name="biblio" id="biblio" rel="biblio"<?php print (!empty($_POST['biblio'])?' checked="checked"':''); ?> />
              <br />
            </div>
            <div rel="biblio">
              <label for="pmid"><?php print _("PMID"); ?></label>
              <input name="pmid" id="pmid" type="text" maxlength="16" title="<?php print _("PubMed Identifier of the reference"); ?>"<?php print (!empty($_POST['pmid'])?' value="' . stripslashes(strip_tags(trim($_POST['pmid']))) . '"':''); ?> />
              <br />
            </div>
            <div rel="biblio">
              <label for="comments_b"><?php print _("Comments"); ?></label>
              <textarea name="comments_b" id="comments_b" rows="4" cols="30" title="<?php print _("General comments"); ?>"><?php print (!empty($_POST['comments_b'])?stripslashes(strip_tags(trim($_POST['comments_b']))):''); ?></textarea>
              <br />
            </div>
            <br />
            <input type="hidden" name="oligoml" value="<?php print md5('add' . floor(intval(date('b')))); ?>" />
            <input type="reset" value="<?php print _("Clear"); ?>" />&nbsp;<input type="submit" value="<?php print _("Add"); ?>" />
          </div>
          </form>
<?php
      break;
  }
}

if ($config['login'] && ($_SESSION['login']['right'] >= 2)) {
  head('oligoml', true);
  $sql = sql_connect($config['db']);
?>
        <div class="items">
          <h1><?php print $plugin['oligoml']['name']; ?><small><?php print _("Submit new oligo pair"); ?></small></h1><br />
<?php
  if (!empty($_POST['oligoml']) && ($_POST['oligoml'] == md5('add' . floor(intval(date('b'))))) && ((($_SESSION['oligoml']['add'] == 'pair') && (!empty($_POST['specificity']) && !empty($_POST['target']) && !empty($_POST['length']) && (isset($_SESSION['oligoml']['forward']) || (!empty($_POST['name'][0]) && !empty($_POST['sequence'][0]) && !empty($_POST['box'][0]))) && (isset($_SESSION['oligoml']['reverse']) || (!empty($_POST['name'][1]) && !empty($_POST['sequence'][1]) && !empty($_POST['box'][1]))))) || (($_SESSION['oligoml']['add'] == 'oligo') && (!empty($_POST['name'][0]) && !empty($_POST['sequence'][0]) && !empty($_POST['box'][0]))))) {
    $prefix = floor(((intval(date('Y', time())) - 2001) * 12 + intval(date('m', time())) - 1) / 1.5);
    if (!empty($_POST['biblio']) && !empty($_POST['pmid'])) {
      $ref = getref(intval($_POST['pmid']), $sql);
    }
    for($i = (isset($_SESSION['oligoml']['forward'])?1:0);$i < ((isset($_SESSION['oligoml']['reverse']) || ($_SESSION['oligoml']['add'] == 'oligo'))?1:2);$i++) {
      $result = sql_query('SELECT prefix, id, name, sequence FROM oligoml_oligo WHERE (name=\'' . preg_replace('/[^\w\d\.\-\_\/ \(\)]/', '', stripslashes(strip_tags(trim($_POST['name'][$i])))) . '\' OR sequence=\'' . preg_replace('/[^ATYRWKMDVHBNXCGS]/', '', strtoupper(stripslashes(strip_tags(trim($_POST['sequence'][$i]))))) . '\');', $sql);
      if ((!strlen($r = sql_last_error($sql))) && (sql_num_rows($result) == 0)) {
        if (!empty($_POST['design']) && ($_POST['design'] == 'manual')) {
          $design = '1, NULL, NULL, NULL';
        }elseif (!empty($_POST['design']) && ($_POST['design'] == 'software')) {
          $design = '2, ' . (!empty($_POST['program']) ? ('\'' . preg_replace('/[^\w\d\.\-\_\/ \(\)]/', '', stripslashes(strip_tags(trim($_POST['program'])))) . '\'') : 'NULL') . ', ' . ((!empty($_POST['version']) && (floatval($_POST['version']) > 0)) ? floatval($_POST['version']) : 'NULL') . ', ' . (!empty($_POST['comments_s']) ? ('\'' . stripslashes(strip_tags(trim($_POST['comments_s']))) . '\'') : 'NULL');
        }else {
          $design = 'NULL, NULL, NULL, NULL';
        }
        $result = sql_query('INSERT INTO oligoml_oligo (prefix, id, name, sequence, box, modification, freezer, rank, comments, design, program, version, design_comments, reference, author) SELECT ' . $prefix . ', CASE WHEN max(id)>=1 THEN max(id)+1 ELSE 1 END, \'' . preg_replace('/[^\w\d\.\-\_\/ \(\)]/', '', stripslashes(strip_tags(trim($_POST['name'][$i])))) . '\', \'' . preg_replace('/[^ATYRWKMDVHBNXCGS]/', '', strtoupper(stripslashes(strip_tags(trim($_POST['sequence'][$i]))))) . '\', \'' . stripslashes(strip_tags(trim($_POST['box'][$i]))) . '\', ' . (!empty($_POST['modif'][$i])?'\'' . preg_replace('/[^\w\d\.\-\_\/ \(\)]/', '', stripslashes(strip_tags(trim($_POST['modif'][$i])))) . '\'':'NULL') . ', ' . (!empty($_POST['freezer'][$i])?'\'' . preg_replace('/[^\w\d\.\-\_\/ \(\)]/', '', stripslashes(strip_tags(trim($_POST['freezer'][$i])))) . '\'':'NULL') . ', ' . (!empty($_POST['rank'][$i])?'\'' . preg_replace('/[^\w\d\.\-\_\/ \(\)]/', '', stripslashes(strip_tags(trim($_POST['rank'][$i])))) . '\'':'NULL') . ', ' . (!empty($_POST['comments'][$i])?'\'' . preg_replace('/[^\w\d\.\-\_\/ \(\)]/', '', stripslashes(strip_tags(trim($_POST['comments'][$i])))) . '\'':'NULL') . ', ' . $design . ', ' . (!empty($ref)?'\'' . $ref . '\'':'NULL') . ', \'' . $_SESSION['login']['username'] . '\' FROM oligoml_oligo WHERE prefix=' . $prefix . ';', $sql);
        if (!strlen($r = sql_last_error($sql))) {
          $result = sql_query('SELECT prefix, id FROM oligoml_oligo WHERE (prefix=' . $prefix . ' AND name=\'' . preg_replace('/[^\w\d\.\-\_\/ \(\)]/', '', stripslashes(strip_tags(trim($_POST['name'][$i])))) . '\' AND release=1);', $sql);
          if ((!strlen($r = sql_last_error($sql))) && (sql_num_rows($result) == 1)) {
            $row = sql_fetch_row($result);
            $_SESSION['oligoml'][(($i == 0)?'forward':'reverse')] = array('prefix' => $row[0], 'id' => $row[1]);
          }else {
            $error = _("Database entry error:") . ' ' . $r;
          }
        }else {
          $error = _("Database entry error:") . ' ' . $r;
        }
      }else {
        $error = _("The same name or the same sequence already exist in the database for the " . (($i == 0)?'forward':'reverse') . " primer!");;
      }
    }
    if (isset($_SESSION['oligoml']['forward']) && isset($_SESSION['oligoml']['reverse'])) {
      $result = sql_query('SELECT prefix, id FROM oligoml_pair WHERE (forward_prefix=' . $_SESSION['oligoml']['forward']['prefix'] . ' AND forward_id=' . $_SESSION['oligoml']['forward']['id'] . ' AND reverse_prefix=' . $_SESSION['oligoml']['reverse']['prefix'] . ' AND reverse_id=' . $_SESSION['oligoml']['reverse']['id'] . ');', $sql);
      if ((!strlen($r = sql_last_error($sql))) && (sql_num_rows($result) == 0)) {
        $result = sql_query('INSERT INTO oligoml_pair (prefix, id, forward_prefix, forward_id, reverse_prefix, reverse_id, speciesid, species, geneid, locus, amplicon, sequenceid, location, pcr, buffer, comments, reference, author) SELECT ' . $prefix . ', CASE WHEN max(id)>=1 THEN max(id)+1 ELSE 1 END, ' . $_SESSION['oligoml']['forward']['prefix'] . ', ' . $_SESSION['oligoml']['forward']['id'] . ', ' . $_SESSION['oligoml']['reverse']['prefix'] . ', ' . $_SESSION['oligoml']['reverse']['id'] . ', ' . ((intval($_POST['specificity']) > 0)?'\'' . intval($_POST['specificity']) . '\', NULL':'NULL, \'' . stripslashes(strip_tags(trim($_POST['specificity']))) . '\'') . ', ' . ((intval($_POST['target']) > 0)?'\'' . intval($_POST['target']) . '\', NULL':'NULL, \'' . stripslashes(strip_tags(trim($_POST['target']))) . '\'') . ', ' . ((intval($_POST['length']) > 0)?'\'' . intval($_POST['length']) . '\', NULL':'NULL, \'' . stripslashes(strip_tags(trim($_POST['length']))) . '\'') . ', ' . (!empty($_POST['location'])?'\'' . stripslashes(strip_tags(trim($_POST['location']))) . '\'':'NULL') . ', ' . (!empty($_POST['pcr'])?'\'' . stripslashes(strip_tags(trim($_POST['pcr']))) . '\'':'NULL') . ', ' . (!empty($_POST['buffer'])?'\'' . stripslashes(strip_tags(trim($_POST['buffer']))) . '\'':'NULL') . ', ' . (!empty($_POST['comments_a'])?'\'' . stripslashes(strip_tags(trim($_POST['comments_a']))) . '\'':'NULL') . ', ' . (!empty($ref)?'\'' . $ref . '\'':'NULL') . ', \'' . $_SESSION['login']['username'] . '\' FROM oligoml_pair WHERE prefix=' . $prefix . ';', $sql);
        if (!strlen($r = sql_last_error($sql))) {
          $result = sql_query('SELECT prefix, id FROM oligoml_pair WHERE (forward_prefix=' . $_SESSION['oligoml']['forward']['prefix'] . ' AND forward_id=' . $_SESSION['oligoml']['forward']['id'] . ' AND reverse_prefix=' . $_SESSION['oligoml']['reverse']['prefix'] . ' AND reverse_id=' . $_SESSION['oligoml']['reverse']['id'] . ');', $sql);
          if ((!strlen($r = sql_last_error($sql))) && (sql_num_rows($result) == 1)) {
            $row = sql_fetch_row($result);
            $_SESSION['oligoml']['pair'] = array('prefix' => $row[0], 'id' => $row[1]);
          }else {
            $error = _("Database entry error:") . ' ' . $r;
          }
        }else {
          $error = _("Database entry error:") . ' ' . $r;
        }
      }else {
        $error = _("The same primer association already exist in the database!");
      }
    }
    if (isset($error)) {
      step(($_SESSION['oligoml']['add'] == 'pair')?2:3, $error);
    }else {
      unset($_SESSION['oligoml']);
      header('Location: ' . $config['server'] . $plugin['oligoml']['url'] . '/');
      exit;
    }
  }elseif (!empty($_POST['oligoml']) && (($_POST['oligoml'] == md5('add' . floor(intval(date('b'))))) || (($_POST['oligoml'] == md5('next' . floor(intval(date('b'))))) && !empty($_POST['add']) && (($_POST['add'] == 'oligo') || (($_POST['add'] == 'pair') && (empty($_POST['primer'][0]) || (($_POST['primer'][0] == 'on') && !empty($_POST['ref'][0]) && ($ref[0] = getoligo(stripslashes(strip_tags(trim($_POST['ref'][0]))), $sql)))) && (empty($_POST['primer'][1]) || (($_POST['primer'][1] == 'on') && !empty($_POST['ref'][1]) && ($ref[1] = getoligo(stripslashes(strip_tags(trim($_POST['ref'][1]))), $sql))))))))) {
    if ($_POST['oligoml'] == md5('add' . floor(intval(date('b'))))) {
      $error = _("All fields have not been given!");
    }else {
      $_SESSION['oligoml']['add'] = $_POST['add'];
      if (isset($ref[0])) $_SESSION['oligoml']['forward'] = $ref[0];
      if (isset($ref[1])) $_SESSION['oligoml']['reverse'] = $ref[1];
    }
    step(($_SESSION['oligoml']['add'] == 'pair')?2:3, (isset($error)?$error:null));
  }elseif (!empty($_POST['oligoml']) && ($_POST['oligoml'] == md5('next' . floor(intval(date('b'))))) && !empty($_POST['add']) && ($_POST['add'] == 'oligofile') && isset($_FILES['oligofile']) && ($_FILES['oligofile']['error'] == UPLOAD_ERR_OK) && ($_FILES['oligofile']['size'] > 0) && is_uploaded_file($_FILES['oligofile']['tmp_name'])) {
$prefix = floor(((intval(date('Y', time())) - 2001) * 12 + intval(date('m', time())) - 1) / 1.5);
      if ($import = file_get_contents($_FILES['oligofile']['tmp_name'])) {
      if (substr($import,0,50)=='<!DOCTYPE oligoml PUBLIC "-//OLIGOML//DTD OLIGOML ') {
        if ((($oligoml=readXML($import))!==false) && isset($oligoml['children']) && isset($oligoml['attrs']['VERSION']) && (floatval($oligoml['attrs']['VERSION'])>=0.4)) {
          foreach( $oligoml['children'] as $entry ) {

          }
        }
      } elseif (substr($import,0,53)=='<!DOCTYPE uniprime PUBLIC "-//UNIPRIME//DTD UNIPRIME ') {
          if ((($oligoml=readXML($import))!==false) && isset($oligoml['children']) && isset($oligoml['attrs']['VERSION']) && (floatval($oligoml['attrs']['VERSION'])>=0.4)) {
              foreach( $oligoml['children'] as $entry ) {
                  switch($entry['name']) {
                      case 'OLIGO':
                          $oligo['name']=$entry['attrs']['ID'];
                          if (isset($entry['children'])) {
                             foreach( $entry['children'] as $value ) {
                                 switch($value['name']) {
                                     case 'NAME':
                                         $oligo['name']=$value['cdata'];
                                         break;
                                     case 'SEQUENCE':
                                         $oligo['sequence']=$value['cdata'];
                                         break;
                                     case 'REVISION':
                                         if (isset($value['children'])) {
                                             foreach($value['children'] as $subvalue ) {
                                                 switch($subvalue['name']) {
                                                     case 'AUTHOR':
                                                         $oligo['author']=$subvalue['cdata'];
                                                         break;
                                                     case 'DATE':
                                                         $oligo['date']=strtotime($subvalue['cdata']);
                                                         break;
                                                 }
                                                 
                                             }
                                         }
                                         
                                         break;
                                     case 'REFERENCE':
                                         if (isset($value['attrs']['CLASS'])) {
                                             $oligo['design']=1;
                                             if (($value['attrs']['CLASS']=='software') && isset($value['children'])) {
                                                 $oligo['design']=2;
                                                 foreach($value['children'] as $subvalue ) {
                                                     if ($subvalue['name']) {
                                                             $oligo['program']=$subvalue['cdata'];
                                                         if (isset($subvalue['attrs']['VERSION'])) $oligo['version']=$subvalue['attrs']['VERSION'];

                                                     }
                                                 }
                                                 
                                             }
                                         }
                                         
                                         break;
                                 }
                                 
                             }
                          }
                          if (!empty($oligo['sequence'])) {
                            $result = sql_query('SELECT prefix, id FROM oligoml_oligo WHERE (name=\'' . preg_replace('/[^\w\d\.\-\_\/ \(\)]/', '', stripslashes(strip_tags(trim($oligo['name'])))) . '\' OR sequence=\'' . preg_replace('/[^ATYRWKMDVHBNXCGS]/', '', strtoupper(stripslashes(strip_tags(trim($oligo['sequence']))))) . '\');', $sql);
                            if ((!strlen($r = sql_last_error($sql))) && (sql_num_rows($result) == 0)) {
                                $result = sql_query('INSERT INTO oligoml_oligo (prefix, id, name, sequence, box, design, program, version, comments, author) SELECT ' . $prefix . ', CASE WHEN max(id)>=1 THEN max(id)+1 ELSE 1 END, \'' . preg_replace('/[^\w\d\.\-\_\/ \(\)]/', '', stripslashes(strip_tags(trim($oligo['name'])))) . '\', \'' . preg_replace('/[^ATYRWKMDVHBNXCGS]/', '', strtoupper(stripslashes(strip_tags(trim($oligo['sequence']))))) . '\', \'\', ' . (!empty($oligo['design'])?$oligo['design']:'NULL') . ', ' . (!empty($oligo['program'])?'\'' . preg_replace('/[^\w\d\.\-\_\/ \(\)]/', '', stripslashes(strip_tags(trim($oligo['program'])))) . '\'':'NULL') . ', ' . (!empty($oligo['version'])?'\'' . preg_replace('/[^\w\d\.\-\_\/ \(\)]/', '', stripslashes(strip_tags(trim($oligo['version'])))) . '\'':'NULL') . ', ' . (!empty($oligo['author'])?'\'Imported from ['.$entry['attrs']['REF'].'|'.substr($entry['attrs']['ID'],0,-2)."]\n" . preg_replace('/[^\w\d\.\-\_\/ \(\)]/', '', stripslashes(strip_tags(trim($oligo['author'])))) . (!empty($oligo['date'])?' (' . date(_("d-m-Y"), $oligo['date']) . ')':'').'\'':'NULL') . ', \'' . $_SESSION['login']['username'] . '\' FROM oligoml_oligo WHERE prefix=' . $prefix . ';', $sql);
                                if (!strlen($r = sql_last_error($sql))) {
                                    $result = sql_query('SELECT prefix, id FROM oligoml_oligo WHERE (prefix=' . $prefix . ' AND name=\'' . preg_replace('/[^\w\d\.\-\_\/ \(\)]/', '', stripslashes(strip_tags(trim($oligo['name'])))) . '\' AND release=1);', $sql);
                                    if ((strlen($r = sql_last_error($sql))) || (sql_num_rows($result) != 1)) {
                                        $error = _("Database entry error:") . ' ' . $r;
                                    } else {
                                        $ref[$entry['attrs']['ID']]=sql_fetch_row($result);
                                    }
                                }else {
                                    $error = _("Database entry error:") . ' ' . $r;
                                }
                            }else {
                                $ref[$entry['attrs']['ID']]=sql_fetch_row($result);
                            }
                          }
                          
                          break;
                      case 'PAIR':
                          $pair['comments']='Imported from ['.$entry['attrs']['REF'].'|'.$entry['attrs']['ID'].']';
                          if (isset($entry['children'])) {
                              foreach( $entry['children'] as $value ) {
                                  switch($value['name']) {
                                      case 'FORWARD':
                                          if (isset($ref[$value['cdata']])) $pair['forward']=$ref[$value['cdata']];
                                          break;
                                      case 'REVERSE':
                                          if (isset($ref[$value['cdata']])) $pair['reverse']=$ref[$value['cdata']];
                                          break;
                                      case 'SPECIFICITY':
                                          if (isset($value['children'])) {
                                              foreach($value['children'] as $subvalue ) {
                                                  switch($subvalue['name']) {
                                                      case 'SPECIES':
                                                          $pair['species']=$subvalue['cdata'];
                                                          if (isset($subvalue['attrs']['ID'])) $pair['speciesid']=intval($subvalue['attrs']['ID']);
                                                          break;
                                                      case 'TARGET':
                                                          $pair['locus']=$subvalue['cdata'];
                                                          if (isset($subvalue['attrs']['ID'])) $pair['geneid']=intval($subvalue['attrs']['ID']);
                                                          break;
                                                      case 'LENGTH':
                                                          $pair['amplicon']=intval($subvalue['cdata']);
                                                          break;
                                                      case 'LOCATION':
                                                          $pair['location']=$subvalue['cdata'];
                                                          break;
                                                  }
                                                  
                                              }
                                          }
                                          break;
                                      case 'CONDITION':
if (isset($value['children'])) {
    foreach($value['children'] as $subvalue ) {
        switch($subvalue['name']) {
            case 'PCR':
                $pair['pcr']=$subvalue['cdata'];
                break;
        }
        
    }
}
                                            break;
                                      case 'COMMENTS':
                                          $pair['comments'].="\n".$value['cdata'];
                                          break;
                                      case 'REVISION':
                                          if (isset($value['children'])) {
                                              foreach($value['children'] as $subvalue ) {
                                                  switch($subvalue['name']) {
                                                      case 'AUTHOR':
                                                          $pair['author']=$subvalue['cdata'];
                                                          break;
                                                      case 'DATE':
                                                          $pair['date']=strtotime($subvalue['cdata']);
                                                          break;
                                                  }
                                                  
                                              }
                                          }
                                          
                                          break;      
                                  }
                                  
                              }
                          }
                          if (!empty($oligo['sequence'])) {
                              
                              $result = sql_query('SELECT prefix, id FROM oligoml_pair WHERE (forward_prefix=' . $pair['forward'][0] . ' AND forward_id=' . $pair['forward'][1] . ' AND reverse_prefix=' . $pair['reverse'][0] . ' AND reverse_id=' . $pair['reverse'][1] . ');', $sql);
                              if ((!strlen($r = sql_last_error($sql))) && (sql_num_rows($result) == 0)) {
                                  $result = sql_query('INSERT INTO oligoml_pair (prefix, id, forward_prefix, forward_id, reverse_prefix, reverse_id, speciesid, species, geneid, locus, amplicon, location, pcr, comments, author) SELECT ' . $prefix . ', CASE WHEN max(id)>=1 THEN max(id)+1 ELSE 1 END, ' . $pair['forward'][0] . ', ' . $pair['forward'][1] . ', ' . $pair['reverse'][0] . ', ' .$pair['reverse'][1] . ', ' . (!empty($pair['speciesid'])?$pair['speciesid']:'NULL') . ', ' . (!empty($pair['species'])?'\'' . stripslashes(strip_tags(trim($pair['species']))) . '\'':'NULL') . ', ' . (!empty($pair['geneid'])?$pair['geneid']:'NULL') . ', ' . (!empty($pair['locus'])?'\'' . stripslashes(strip_tags(trim($pair['locus']))) . '\'':'NULL').', ' . (!empty($pair['amplicon'])?$pair['amplicon']:'NULL') . ', ' . (!empty($pair['location'])?'\'' . stripslashes(strip_tags(trim($pair['location']))) . '\'':'NULL') . ', ' . (!empty($pair['pcr'])?'\'' . stripslashes(strip_tags(trim($pair['pcr']))) . '\'':'NULL') . ', ' . (!empty($pair['comments'])?'\'' . addslashes(html_entity_decode(stripslashes(strip_tags(trim($pair['comments']))))) . '\'':'NULL') . ', \'' . $_SESSION['login']['username'] . '\' FROM oligoml_pair WHERE prefix=' . $prefix . ';', $sql);
                                  if (!strlen($r = sql_last_error($sql))) {
                                      $result = sql_query('SELECT prefix, id FROM oligoml_pair WHERE (forward_prefix=' . $pair['forward'][0] . ' AND forward_id=' . $pair['forward'][1] . ' AND reverse_prefix=' . $pair['reverse'][0] . ' AND reverse_id=' . $pair['reverse'][1] . ');', $sql);
                                      if ((strlen($r = sql_last_error($sql))) || (sql_num_rows($result) != 1)) {
                                          $error = _("Database entry error:") . ' ' . $r;
                                      }
                                  }else {
                                      $error = _("Database entry error:") . ' ' . $r;
                                  }
                              }else {
                                  $error = _("The same primer association already exist in the database!");
                              }
}
                          break;
                  }
                  
                  
              }
          }
      } else {
      foreach(explode ("\n", $import) as $line) {
        $row = explode("\t", trim($line), 7);
        if (count($row > 2) && !empty($row[0]) && !empty($row[1]) && !empty($row[2])) {
          if (!empty($row[5])) {
            $ref = getref(intval($row[5]), $sql);
          }
          $result = sql_query('SELECT prefix, id, name, sequence FROM oligoml_oligo WHERE (name=\'' . preg_replace('/[^\w\d\.\-\_\/ \(\)]/', '', stripslashes(strip_tags(trim($row[0])))) . '\' OR sequence=\'' . preg_replace('/[^ATYRWKMDVHBNXCGS]/', '', strtoupper(stripslashes(strip_tags(trim($row[1]))))) . '\');', $sql);
          if ((!strlen($r = sql_last_error($sql))) && (sql_num_rows($result) == 0)) {
            $result = sql_query('INSERT INTO oligoml_oligo (prefix, id, name, sequence, box, freezer, rank, comments, reference, author) SELECT ' . $prefix . ', CASE WHEN max(id)>=1 THEN max(id)+1 ELSE 1 END, \'' . preg_replace('/[^\w\d\.\-\_\/ \(\)]/', '', stripslashes(strip_tags(trim($row[0])))) . '\', \'' . preg_replace('/[^ATYRWKMDVHBNXCGS]/', '', strtoupper(stripslashes(strip_tags(trim($row[1]))))) . '\', \'' . stripslashes(strip_tags(trim($row[2]))) . '\', ' . (!empty($row[4])?'\'' . preg_replace('/[^\w\d\.\-\_\/ \(\)]/', '', stripslashes(strip_tags(trim($row[4])))) . '\'':'NULL') . ', ' . (!empty($row[3])?'\'' . preg_replace('/[^\w\d\.\-\_\/ \(\)]/', '', stripslashes(strip_tags(trim($row[3])))) . '\'':'NULL') . ', ' . (!empty($row[6])?'\'' . preg_replace('/[^\w\d\.\-\_\/ \(\)]/', '', stripslashes(strip_tags(trim($row[6])))) . '\'':'NULL') . ', ' . (!empty($ref)?$ref:'NULL') . ', \'' . $_SESSION['login']['username'] . '\' FROM oligoml_oligo WHERE prefix=' . $prefix . ';', $sql);
            if (!strlen($r = sql_last_error($sql))) {
              $result = sql_query('SELECT prefix, id FROM oligoml_oligo WHERE (prefix=' . $prefix . ' AND name=\'' . preg_replace('/[^\w\d\.\-\_\/ \(\)]/', '', stripslashes(strip_tags(trim($_POST['name'][$i])))) . '\' AND release=1);', $sql);
              if ((strlen($r = sql_last_error($sql))) || (sql_num_rows($result) != 1)) {
                $error = _("Database entry error:") . ' ' . $r;
              }
            }else {
              $error = _("Database entry error:") . ' ' . $r;
            }
          }else {
            $error = _("The same name or the same sequence already exist in the database for the " . (($i == 0)?'forward':'reverse') . " primer!");
          }
        }
      }
      }
      if (!isset($error)) {
        header('Location: ' . $config['server'] . $plugin['oligoml']['url'] . '/');
        exit;
      }
    }else {
      step(1, _("Unreadable file"));
    }
  }else {
    if (!empty($_POST['oligoml'])) {
      $error = _("Unknown oligonucleotide!");
    }
    step(1, (isset($error)?$error:null));
  }
?>
         </div>
<?php
  foot();
}else {
  header('Location: ' . $config['server']);
}
?>