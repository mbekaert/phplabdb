<?php
ob_start("ob_gzhandler");
session_start();
require_once('../includes/main.inc');

if ($config['login']) {
  if (!isset($_SESSION['delay'])) {
    @set_pref('delay', true);
  }
  if (!isset($_SESSION['specie'])) {
    @set_pref('specie', 'name');
  }
  if (!isset($_SESSION['primer'])) {
    @set_pref('primer', 5);
  }
  $sql = sql_connect($config['db']);
  if (isset($_GET['sequence']) && preg_match('/^(\d+)\.(\d+)/', $_GET['sequence'], $matches)) { // add sequence
    $matches[1] = octdec($matches[1]);
    $matches[2] = octdec($matches[2]);
    if (isset($_POST['type'])) {
      $entry['sequence']['type'] = intval($_POST['type']);
    }
    if (isset($_POST['addict']) && isset($entry['sequence']['type']) && (intval($_POST['addict']) == 2)) {
      if (($entry['sequence']['type'] == 1) && !empty($_POST['accession']) && isset($_POST['start']) && isset($_POST['end']) && (intval($_POST['start']) > 0) && (intval($_POST['end']) > 0)) {
        require_once('includes/import.inc');
        $entry['sequence']['accession'] = preg_replace('/[^\d\w\.]/', '', strtoupper($_POST['accession']));
        if (!empty($_POST['isolate'])) {
          $entry['sequence']['isolate'] = preg_replace('/[^\d\w\.\-\_\ ]/', '', $_POST['isolate']);
        }
        if (!empty($_POST['map'])) $entry['sequence']['map'] = preg_replace('/[^\d\w\.\-\_\ ]/', '', $_POST['map']);
        if (intval($_POST['start']) > intval($_POST['end'])) {
          $entry['sequence']['end'] = intval($_POST['start']);
          $entry['sequence']['start'] = intval($_POST['end']);
        }else {
          $entry['sequence']['start'] = intval($_POST['start']);
          $entry['sequence']['end'] = intval($_POST['end']);
        }
        if (!empty($_POST['seq_comments'])) {
          $entry['sequence']['comments'] = ucfirst(preg_replace('/&amp;(#x?[0-9a-f]+);/', '&\1', htmlentities(html_entity_decode($_POST['seq_comments']), ENT_QUOTES, 'ISO8859-1')));
        }
        if (!empty($_POST['seq_references'])) {
          $entry['sequence']['references'] = preg_replace('/&amp;(#x?[0-9a-f]+);/', '&\1', htmlentities(html_entity_decode($_POST['seq_references']), ENT_QUOTES, 'ISO8859-1'));
        }
        $msg = addSequence($entry, $sql);
        if (empty($msg)) {
          header('Location: ' . $config['server'] . '/locus/' . $matches[0]);
          exit(0);
        }
      }elseif (($entry['sequence']['type'] == 2) && !empty($_POST['accession']) && isset($_POST['evalue']) && isset($_POST['start']) && isset($_POST['end']) && (floatval($_POST['start']) >= 0) && (intval($_POST['start']) > 0) && (intval($_POST['end']) > 0)) {
        require_once('includes/import.inc');
        $entry['sequence']['accession'] = preg_replace('/[^\d\w\.]/', '', strtoupper($_POST['accession']));
        if (!empty($_POST['isolate'])) {
          $entry['sequence']['isolate'] = preg_replace('/[^\d\w\.\-\_\ ]/', '', $_POST['isolate']);
        }
        if (!empty($_POST['map'])) $entry['sequence']['map'] = preg_replace('/[^\d\w\.\-\_\ ]/', '', $_POST['map']);
        $entry['sequence']['evalue'] = floatval($_POST['evalue']);
        if (intval($_POST['start']) > intval($_POST['end'])) {
          $entry['sequence']['end'] = intval($_POST['start']);
          $entry['sequence']['start'] = intval($_POST['end']);
        }else {
          $entry['sequence']['start'] = intval($_POST['start']);
          $entry['sequence']['end'] = intval($_POST['end']);
        }
        if (!empty($_POST['seq_comments'])) {
          $entry['sequence']['comments'] = ucfirst(preg_replace('/&amp;(#x?[0-9a-f]+);/', '&\1', htmlentities(html_entity_decode($_POST['seq_comments']), ENT_QUOTES, 'ISO8859-1')));
        }
        if (!empty($_POST['seq_references'])) {
          $entry['sequence']['references'] = preg_replace('/&amp;(#x?[0-9a-f]+);/', '&\1', htmlentities(html_entity_decode($_POST['seq_references']), ENT_QUOTES, 'ISO8859-1'));
        }
        $msg = addSequence($entry, $sql);
        if (empty($msg)) {
          header('Location: ' . $config['server'] . '/locus/' . $matches[0]);
          exit(0);
        }
      }elseif (($entry['sequence']['type'] == 3) && isset($_POST['primer']) && (intval($_POST['primer']) > 0) && ((!empty($_POST['accession']) && isset($_POST['start']) && isset($_POST['end']) && (intval($_POST['start']) > 0) && (intval($_POST['end']) > 0)) || (!empty($_POST['name']) && !empty($_POST['sequence']) && !empty($_POST['topology']) && !empty($_POST['molecule']) && isset($_POST['organism']) && isset($_POST['strand']) && (intval($_POST['strand']) != 0) && (intval($_POST['organism']) > 0)))) {
        $entry['sequence']['primer'] = intval($_POST['primer']);
        if (!empty($_POST['isolate'])) {
          $entry['sequence']['isolate'] = preg_replace('/[^\d\w\.\-\_\ ]/', '', $_POST['isolate']);
        }
        if (!empty($_POST['seq_map'])) {
          foreach (explode("\n", $_POST['seq_map']) as $ligne) {
            $char = explode('|', $ligne, 4);
            if ((isset($char[0]) && (intval($char[0]) > 0)) && (isset($char[1]) && (intval($char[1]) > intval($char[0]))) && (isset($char[2]) && ((trim($char[2]) == 'P') || (trim($char[2]) == 'V') || (trim($char[2]) == 'S'))) && (!empty($char[3]))) {
              $ret[] = intval($char[0]) . '|' . intval($char[1]) . '|' . trim($char[2]) . '|' . ucfirst(preg_replace('/&amp;(#x?[0-9a-f]+);/', '&\1', htmlentities(html_entity_decode($char[3]), ENT_QUOTES, 'ISO8859-1')));
            }
          }
          if (isset($ret)) $entry['sequence']['structure'] = join("\n", $ret);
        }
        if (!empty($_POST['map'])) $entry['sequence']['map'] = preg_replace('/[^\d\w\.\-\_\ ]/', '', $_POST['map']);
        if (!empty($_POST['seq_comments'])) {
          $entry['sequence']['comments'] = ucfirst(preg_replace('/&amp;(#x?[0-9a-f]+);/', '&\1', htmlentities(html_entity_decode($_POST['seq_comments']), ENT_QUOTES, 'ISO8859-1')));
        }
        if (!empty($_POST['seq_references'])) {
          $entry['sequence']['references'] = preg_replace('/&amp;(#x?[0-9a-f]+);/', '&\1', htmlentities(html_entity_decode($_POST['seq_references']), ENT_QUOTES, 'ISO8859-1'));
        }
        if (!empty($_POST['accession'])) {
          $entry['sequence']['accession'] = preg_replace('/[^\d\w\.]/', '', strtoupper($_POST['accession']));
          if (intval($_POST['start']) > intval($_POST['end'])) {
            $entry['sequence']['end'] = intval($_POST['start']);
            $entry['sequence']['start'] = intval($_POST['end']);
          }else {
            $entry['sequence']['start'] = intval($_POST['start']);
            $entry['sequence']['end'] = intval($_POST['end']);
          }
          $msg = addSequence($entry, $sql);
          if (empty($msg)) {
            header('Location: ' . $config['server'] . '/locus/' . $matches[0]);
            exit(0);
          }
        }else {
          $entry['sequence']['name'] = preg_replace('/&amp;(#x?[0-9a-f]+);/', '&\1', htmlentities(html_entity_decode($_POST['name']), ENT_QUOTES, 'ISO8859-1'));
          $entry['sequence']['sequence'] = preg_replace('/[^\w]/', '', strtoupper($_POST['sequence']));
          if (!empty($_POST['alias'])) {
            $entry['sequence']['alias'] = preg_replace('/&amp;(#x?[0-9a-f]+);/', '&\1', htmlentities(html_entity_decode($_POST['alias']), ENT_QUOTES, 'ISO8859-1'));
          }
          if (!empty($_POST['location'])) {
            $entry['sequence']['location'] = preg_replace('/[^\d\w\.\-\_\(\)]/', '', $_POST['location']);
          }
          $entry['sequence']['strand'] = intval($_POST['strand']);
          if (!empty($_POST['chromosome'])) {
            $entry['sequence']['chromosome'] = preg_replace('/[^\d\w\.\-\_]/', '', $_POST['chromosome']);
          }
          $entry['sequence']['circular'] = (($_POST['topology'] == 't')?'t':'f');
          $entry['sequence']['molecule'] = $entry['sequence']['organelle'] = preg_replace('/[^\w]/', '', $_POST['molecule']);
          $entry['sequence']['organism'] = intval($_POST['organism']);
          if (!empty($_POST['organelle'])) {
            $entry['sequence']['organelle'] = preg_replace('/[^\d\w\.\-\_\ ]/', '', $_POST['organelle']);
          }
          $entry['sequence']['translation'] = intval($_POST['translation']);
          // INSERT INTO sequence (prefix, id, locus_prefix, locus_id, name, isolate, location, organelle, translation, molecule, circular, chromosome, structure, map, accession, hgnc, geneid, organism, go, sequence_type, stop, start, strand, sequence, evalue, sources, comments, author) SELECT ?, CASE WHEN max(id)>=1 THEN max(id)+1 ELSE 1 END, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ? FROM sequence WHERE prefix=?;
          $result = sql_query('INSERT INTO sequence (locus_,                            name,alias,location,isolate,organelle,translation,molecule,circular,chromosome,map,organism,sequence_type,stop,start,strand,sequence,sources,comments,structure,primer,reviewer) VALUES (' . $entry['locus']['id'] . ',\'' . addslashes($entry['sequence']['name']) . '\',' . (isset($entry['sequence']['alias'])?'\'' . addslashes($entry['sequence']['alias']) . '\'':'NULL') . ',' . (isset($entry['sequence']['location'])?'\'' . addslashes($entry['sequence']['location']) . '\'':'NULL') . ',' . (isset($entry['sequence']['isolate'])?'\'' . addslashes($entry['sequence']['isolate']) . '\'':'NULL') . ',' . (isset($entry['sequence']['organelle'])?'\'' . addslashes($entry['sequence']['organelle']) . '\'':'NULL') . ',' . $entry['sequence']['translation'] . ',\'' . addslashes($entry['sequence']['molecule']) . '\',\'' . $entry['sequence']['circular'] . '\',' . (isset($entry['sequence']['chromosome'])?'\'' . addslashes($entry['sequence']['chromosome']) . '\'':'NULL') . ',' . (isset($entry['sequence']['map'])?'\'' . addslashes($entry['sequence']['map']) . '\'':'NULL') . ',' . $entry['sequence']['organism'] . ',' . $entry['sequence']['type'] . ',' . strlen($entry['sequence']['sequence']) . ',1,1,\'' . addslashes(base64_encode(bzcompress($entry['sequence']['sequence']))) . '\',' . (isset($entry['sequence']['references'])?'\'' . addslashes($entry['sequence']['references']) . '\'':'NULL') . ',' . (isset($entry['sequence']['comments'])?'\'' . addslashes($entry['sequence']['comments']) . '\'':'NULL') . ',' . (isset($entry['sequence']['structure'])?'\'' . addslashes($entry['sequence']['structure']) . '\'':'NULL') . ',' . $entry['sequence']['primer'] . ',\'' . addslashes($_SESSION['login']['username']) . '\');', $sql);
          if (!strlen($r = sql_last_error($sql))) {
            header('Location: ' . $config['server'] . '/locus/' . $matches[0]);
            exit(0);
          }else {
            $msg = '        <div class="warning"><p><img src="' . $config['server'] . '/images/warning.png" alt="">&nbsp;' . _("Entry invalid, check your data") . "</p></div>\n";
          }
        }
      }
    }
    head('new');
    if (!empty($msg)) {
      print $msg;
    }
?>
        <form action="<?php print $config['server'] . '/sequence/' . $matches[0]; ?>/add" method="post">
        <div class="item">
          <h1>
            <?php print _("Sequence"); ?> 
          </h1>
          <div class="clear">&nbsp;</div>
          <p>
<?php
    $result = sql_query('SELECT id,sequence_type FROM sequence_type ORDER BY id;', $sql);
    if (sql_num_rows($result) != 0) {
      print '            <label for="type"><strong>' . _("Sequence type:") . '</strong></label> <select name="type" id="type" title="' . _("Sequence type") . '" onchange="javascript:this.form.submit();">';
      while ($row = sql_fetch_row($result)) {
        print "<option value=\"$row[0]\"" . ((isset($entry['sequence']['type']) && $entry['sequence']['type'] == $row[0])?' selected="selected"':'') . ">$row[1]</option>";
      }
      print "</select><br /><br />\n";
    }
    if (isset($entry['sequence']['type']) && ($entry['sequence']['type'] == 1)) {
?>
            <label for="accession"><strong><?php print _("Accession:"); ?></strong></label> <input type="text" name="accession" id="accession" maxlength="50" title="<?php print _("Genbank accession number"); ?>" <?php print (isset($entry['sequence']['accession'])?' value="' . $entry['sequence']['accession'] . '" ':''); ?>/><br />
            <label for="map"><?php print _("Map:"); ?></label> <input type="text" name="map" id="map" maxlength="100" title="<?php print _("Gene mapping"); ?>" <?php print (isset($entry['sequence']['map'])?' value="' . $entry['sequence']['map'] . '" ':''); ?>/><br />
            <label for="isolate"><?php print _("Isolate"); ?></label> <input type="text" name="isolate" id="isolate" maxlength="100" title="<?php print _("Isolate name / Country / Lat-Long"); ?>" <?php print (isset($entry['sequence']['isolate'])?' value="' . $entry['sequence']['isolate'] . '" ':''); ?>/><br />
            <label for="start"><strong><?php print _("Start:"); ?></strong></label> <input type="text" name="start" id="start" maxlength="20" title="<?php print _("Start position of the sequence"); ?>" <?php print (isset($entry['sequence']['start'])?' value="' . $entry['sequence']['start'] . '" ':''); ?>/><br />
            <label for="end"><strong><?php print _("End:"); ?></strong></label> <input type="text" name="end" id="end" maxlength="20" title="<?php print _("End position of the sequence"); ?>" <?php print (isset($entry['sequence']['end'])?' value="' . $entry['sequence']['end'] . '" ':''); ?>/><br />
            <label for="seq_comments"><?php print _("Description:"); ?></label> <textarea name="seq_comments" id="seq_comments" cols="30" rows="3" title="<?php print _("Sequence description"); ?>"><?php print (isset($entry['sequence']['comments'])?$entry['sequence']['comments']:''); ?></textarea><br />
            <label for="seq_references"><?php print _("References:"); ?></label> <textarea name="seq_references" id="seq_references" cols="30" rows="3" title="<?php print _("References (wiki style [ full_url | name ])"); ?>"><?php print (isset($entry['sequence']['references'])?$entry['sequence']['references']:''); ?></textarea><br />
<?php
    }elseif (isset($entry['sequence']['type']) && ($entry['sequence']['type'] == 2)) {
?>
            <label for="accession"><strong><?php print _("Accession:"); ?></strong></label> <input type="text" name="accession" id="accession" maxlength="50" title="<?php print _("Genbank accession number"); ?>" <?php print (isset($entry['sequence']['accession'])?' value="' . $entry['sequence']['accession'] . '" ':''); ?>/><br />
            <label for="evalue"><strong><?php print _("E-value:"); ?></strong></label> <input type="text" name="evalue" id="evalue" maxlength="50" title="<?php print _("E-value"); ?>" <?php print (isset($entry['sequence']['evalue'])?' value="' . $entry['sequence']['evalue'] . '" ':''); ?>/><br />
            <label for="map"><?php print _("Map:"); ?></label> <input type="text" name="map" id="map" maxlength="100" title="<?php print _("Gene mapping"); ?>" <?php print (isset($entry['sequence']['map'])?' value="' . $entry['sequence']['map'] . '" ':''); ?>/><br />
            <label for="isolate"><?php print _("Isolate:"); ?></label> <input type="text" name="isolate" id="isolate" maxlength="100" title="<?php print _("Isolate name / Country / Lat-Long"); ?>" <?php print (isset($entry['sequence']['isolate'])?' value="' . $entry['sequence']['isolate'] . '" ':''); ?>/><br />
            <label for="start"><strong><?php print _("Start:"); ?></strong></label> <input type="text" name="start" id="start" maxlength="20" title="<?php print _("Start position of the sequence"); ?>" <?php print (isset($entry['sequence']['start'])?' value="' . $entry['sequence']['start'] . '" ':''); ?>/><br />
            <label for="end"><strong><?php print _("End:"); ?></strong></label> <input type="text" name="end" id="end" maxlength="20" title="<?php print _("End position of the sequence"); ?>" <?php print (isset($entry['sequence']['end'])?' value="' . $entry['sequence']['end'] . '" ':''); ?>/><br />
            <label for="seq_comments"><?php print _("Description:"); ?></label> <textarea name="seq_comments" id="seq_comments" cols="30" rows="3" title="<?php print _("Sequence description"); ?>"><?php print (isset($entry['sequence']['comments'])?$entry['sequence']['comments']:''); ?></textarea><br />
            <label for="seq_references"><?php print _("References:"); ?></label> <textarea name="seq_references" id="seq_references" cols="30" rows="3" title="<?php print _("References (wiki style [ full_url | name ])"); ?>"><?php print (isset($entry['sequence']['references'])?$entry['sequence']['references']:''); ?></textarea><br />
<?php
    }elseif (isset($entry['sequence']['type']) && ($entry['sequence']['type'] == 3)) {
?>
            <label for="accession"><strong><?php print _("Accession:"); ?></strong></label> <input type="text" name="accession" id="accession" maxlength="50" title="<?php print _("Genbank accession number"); ?>" <?php print (isset($entry['sequence']['accession'])?' value="' . $entry['sequence']['accession'] . '" ':''); ?>/><br />
            <label for="start"><strong><?php print _("Start:"); ?></strong></label> <input type="text" name="start" id="start" maxlength="20" title="<?php print _("Start position of the sequence"); ?>" <?php print (isset($entry['sequence']['start'])?' value="' . $entry['sequence']['start'] . '" ':''); ?>/><br />
            <label for="end"><strong><?php print _("End:"); ?></strong></label> <input type="text" name="end" id="end" maxlength="20" title="<?php print _("End position of the sequence"); ?>" <?php print (isset($entry['sequence']['end'])?' value="' . $entry['sequence']['end'] . '" ':''); ?>/><br /><br />
          </p>
        </div>
        <div class="clear">&nbsp;</div>
        <strong><?php print _("or"); ?></strong><br /><br />
        <div class="clear">&nbsp;</div>
        <div class="item">
          <p>
            <label for="name"><strong><?php print _("Name:"); ?></strong></label> <input type="text" name="name" id="name" maxlength="100" title="<?php print _("Sequence name"); ?>" <?php print (isset($entry['sequence']['name'])?' value="' . $entry['sequence']['name'] . '" ':''); ?>/><br />
            <label for="alias"><?php print _("Alias:"); ?></label> <input type="text" name="alias" id="alias" maxlength="100" title="<?php print _("Sequence alias"); ?>" <?php print (isset($entry['sequence']['alias'])?' value="' . $entry['sequence']['alias'] . '" ':''); ?>/><br />
            <label for="location"><?php print _("Location:"); ?></label> <input type="text" name="location" id="location" title="<?php print _("Sequence location"); ?>" <?php print (isset($entry['sequence']['location'])?' value="' . $entry['sequence']['location'] . '" ':''); ?>/><br />
            <label for="strand"><strong><?php print _("Strand:"); ?></strong></label> <select name="strand" id="strand" title="<?php print _("Strand"); ?>"><option value="1"<?php print ((isset($entry['sequence']['strand']) && $entry['sequence']['strand'] == 1)?' selected="selected"':''); ?>><?php print _("Direct"); ?></option><option value="-1"<?php print ((isset($entry['sequence']['strand']) && $entry['sequence']['strand'] == -1)?' selected="selected"':''); ?>><?php print _("Complementary"); ?></option></select><br />
            <label for="chromosome"><?php print _("Chromosome:"); ?></label> <input type="text" name="chromosome" id="chromosome" maxlength="50" title="<?php print _("Chromosome name"); ?>" <?php print (isset($entry['sequence']['chromosome'])?' value="' . $entry['sequence']['chromosome'] . '" ':''); ?>/><br />
            <label for="topology"><strong><?php print _("Topology:"); ?></strong></label> <select name="topology" id="topology" title="<?php print _("Molecule topology"); ?>"><option value="f"<?php print ((isset($entry['sequence']['topology']) && $entry['sequence']['topology'] == 'f')?' selected="selected"':''); ?>><?php print _("Linear"); ?></option><option value="t"<?php print ((isset($entry['sequence']['topology']) && $entry['sequence']['topology'] == 't')?' selected="selected"':''); ?>><?php print _("Circular"); ?></option></select><br />
<?php
      $result = sql_query('SELECT id,molecule FROM molecule;', $sql);
      if ((!strlen($r = sql_last_error($sql))) && (sql_num_rows($result) != 0)) {
        print '            <label for="molecule"><strong>' . _("Molecule:") . '</strong></label> <select name="molecule" id="molecule" title="' . _("Molecule type") . '">';
        while ($row = sql_fetch_row($result)) {
          print "<option value=\"$row[0]\"" . ((isset($entry['sequence']['molecule']) && $entry['sequence']['molecule'] == $row[0])?' selected="selected"':'') . ">$row[1]</option>";
        }
        print "</select><br />\n";
      }
      $result = sql_query('SELECT id,name,alias FROM organism ORDER BY name;', $sql);
      if ((!strlen($r = sql_last_error($sql))) && (sql_num_rows($result) != 0)) {
        print '            <label for="organism"><strong>' . _("Organism:") . '</strong></label> <select name="organism" id="organism" title="' . _("Organism name") . '">';
        while ($row = sql_fetch_row($result)) {
          print "<option value=\"$row[0]\"" . ((isset($entry['sequence']['organism']) && $entry['sequence']['organism'] == $row[0])?' selected="selected"':'') . ">$row[1]" . (isset($row[2])?" ($row[2])":'') . '</option>';
        }
        print "</select><br />\n";
      }
?>
            <label for="organelle"><?php print _("Organelle:"); ?></label> <input type="text" name="organelle" id="organelle" maxlength="50" title="<?php print _("Organelle"); ?>" <?php print (isset($entry['sequence']['organelle'])?' value="' . $entry['sequence']['organelle'] . '" ':''); ?>/><br />
<?php
      $result = sql_query('SELECT id,translation FROM translation;', $sql);
      if ((!strlen($r = sql_last_error($sql))) && (sql_num_rows($result) != 0)) {
        print '            <label for="translation">' . _("Genetic table:") . '</label> <select name="translation" id="translation" title="' . _("Genetic table (if relevant)") . '">';
        while ($row = sql_fetch_row($result)) {
          print "<option value=\"$row[0]\"" . ((isset($entry['sequence']['translation']) && $entry['sequence']['translation'] == $row[0])?' selected="selected"':'') . ">$row[1]</option>";
        }
        print "</select><br />\n";
      }
?>
            <label for="sequence"><strong><?php print _("Sequence:"); ?></strong></label> <textarea name="sequence" id="sequence" cols="30" rows="3" title="<?php print _("Sequence description"); ?>"><?php print (isset($entry['sequence']['sequence'])?$entry['sequence']['sequence']:''); ?></textarea><br /><br />
          </p>
        </div>
        <div class="clear">&nbsp;</div>
        <strong><?php print _("and"); ?></strong><br /><br />
        <div class="clear">&nbsp;</div>
        <div class="item">
          <p>
<?php
      $result = sql_query('SELECT id, left_name, right_name, location FROM primer WHERE locus=' . $locus . ';', $sql);
      if ((!strlen($r = sql_last_error($sql))) && (sql_num_rows($result) > 0)) {
        print '            <label for="primer"><strong>' . _("Primers:") . '</strong></label> <select name="primer" id="primer" title="' . _("Primers used") . '" class="multiple">';
        while ($row = sql_fetch_row($result)) {
          print "<option value=\"$row[0]\"" . ((isset($entry['sequence']['primer']) && $entry['sequence']['primer'] == $row[0])?' selected="selected"':'') . '>' . _("Primer") . ((sql_num_rows($result) == 1)?'':' ' . $i) . (isset($row[3])?(': ' . $row[3]):'') . ((isset($row[1]) && isset($row[2]))?(' - ' . $row[1] . ' / ' . $row[2]):'') . '</option>';
        }
        print "</select><br />\n";
      }
?>
            <label for="map"><?php print _("Map:"); ?></label> <input type="text" name="map" id="map" maxlength="100" title="<?php print _("Gene mapping"); ?>" <?php print (isset($entry['sequence']['map'])?' value="' . $entry['sequence']['map'] . '" ':''); ?>/><br />
            <label for="isolate"><?php print _("Isolate:"); ?></label> <input type="text" name="isolate" id="isolate" maxlength="100" title="<?php print _("Isolate name / Country / Lat-Long"); ?>" <?php print (isset($entry['sequence']['isolate'])?' value="' . $entry['sequence']['isolate'] . '" ':''); ?>/><br />
            <label for="seq_map"><?php print _("Structure:"); ?></label> <textarea name="seq_map" id="seq_map" cols="30" rows="3" title="<?php print _("Sequence structure (see help)"); ?>"><?php print (isset($entry['sequence']['structure'])?$entry['sequence']['structure']:''); ?></textarea>&nbsp;[<a href="<?php print $config['server'] . '/help?id=structure'; ?>/add"><?php print _("help"); ?></a>]<br />
            <label for="seq_comments"><?php print _("Description:"); ?></label> <textarea name="seq_comments" id="seq_comments" cols="30" rows="3" title="<?php print _("Sequence description"); ?>"><?php print (isset($entry['sequence']['comments'])?$entry['sequence']['comments']:''); ?></textarea><br />
            <label for="seq_references"><?php print _("References:"); ?></label> <textarea name="seq_references" id="seq_references" cols="30" rows="3" title="<?php print _("References (wiki style [ full_url | name ])"); ?>"><?php print (isset($entry['sequence']['references'])?$entry['sequence']['references']:''); ?></textarea><br />
<?php
    }
?>
            <input type="hidden" name="addict" value="2" /><br /><button type="submit"><?php print _("Next"); ?>&nbsp;&gt;&gt;</button><br /><br />
          </p>
        </div>
        <div class="clear">&nbsp;</div>
        </form>
<?php
  }elseif (isset($_GET['mrna']) && (($sequence = intval($_GET['mrna'])) > 0)) { // add mRNA
    if (isset($_POST['addict']) && ($_POST['addict'] == '3') && !empty($_POST['mrna']) && isset($_POST['type']) && (intval($_POST['type']) > 0)) {
      $entry['mrna']['type'] = intval($_POST['type']);
      $entry['mrna']['mrna'] = preg_replace('/[^\w]/', '', strtoupper($_POST['mrna']));
      if (!empty($_POST['location'])) {
        $entry['mrna']['location'] = preg_replace('/[^\d\w\.\-\_\(\)]/', '', $_POST['location']);
      }
      if (!empty($_POST['comments'])) {
        $entry['mrna']['comments'] = ucfirst(preg_replace('/&amp;(#x?[0-9a-f]+);/', '&\1', htmlentities(html_entity_decode($_POST['comments']), ENT_QUOTES, 'ISO8859-1')));
      }
      $result = sql_query('SELECT locus FROM sequence WHERE id=' . $sequence . ';', $sql);
      if ((!strlen($r = sql_last_error($sql))) && (sql_num_rows($result) == 1)) {
        $row = sql_fetch_row($result);
        $locus = intval($row[0]);
        $result = sql_query('INSERT INTO mrna (locus, sequence, mrna_type, location, mrna, comments, reviewer) VALUES (' . $locus . ',' . $sequence . ',' . $entry['mrna']['type'] . ',' . (isset($entry['mrna']['location'])?('\'' . addslashes($entry['mrna']['location']) . '\''):'NULL') . ',\'' . addslashes(base64_encode(bzcompress($entry['mrna']['mrna']))) . '\',' . (isset($entry['mrna']['comments'])?('\'' . addslashes($entry['mrna']['comments']) . '\''):'NULL') . ',\'' . addslashes($_SESSION['login']['username']) . '\');', $sql);
        if (!strlen($r = sql_last_error($sql))) {
          header('Location: ' . $config['server'] . '/sequence/' . $sequence);
          exit(0);
        }else {
          $msg = '        <div class="warning"><p><img src="' . $config['server'] . '/images/warning.png" alt="">&nbsp;' . _("Entry invalid, check your data") . "</p></div>\n";
        }
      }
    }
    head('new');
    if (!empty($msg)) {
      print $msg;
    }
?>
        <form action="<?php print $config['server'] . '/mrna/' . $sequence; ?>/add" method="post">
        <div class="item">
          <h1>
            <?php print _("mRNA"); ?> 
          </h1>
          <div class="clear">&nbsp;</div>
          <p>
            <label for="location"><?php print _("Coordonate:"); ?></label> <input type="text" name="location" id="location" title="<?php print _("Full coordinates (GenBank style: complement(join(aa..bb,cc..dd))"); ?>" <?php print (isset($entry['mrna']['location'])?' value="' . $entry['mrna']['location'] . '" ':''); ?>/><br />
            <label for="type"><strong><?php print _("mRNA type:"); ?></strong></label> <select name="type" id="type" title="'. _("mRNA sequence type").'"><option value="1">single mRNA</option><?php
    for ($i = 1; $i < 11 ; $i++) {
      print '<option value="' . $i . '"' . ((($entry['mrna']['type'] == $i) && ($i > 1))?' selected="selected"':'') . '>' . _("Transcript") . ' ' . $i . '</option>';
    }
?></select><br />
            <label for="mrna"><strong><?php print _("Sequence:"); ?></strong></label> <textarea name="mrna" id="mrna" cols="30" rows="3" title="<?php print _("mRNA sequence"); ?>"><?php print (isset($entry['mrna']['mrna'])?$entry['mrna']['mrna']:''); ?></textarea><br />
            <label for="comments"><?php print _("Comments:"); ?></label> <textarea name="comments" id="comments" cols="30" rows="3" title="<?php print _("mRNA comments"); ?>"><?php print (isset($entry['mrna']['comments'])?$entry['mrna']['comments']:''); ?></textarea><br />
            <input type="hidden" name="addict" value="3" /><br /><button type="submit"><?php print _("Next"); ?>&nbsp;&gt;&gt;</button><br /><br />
          </p>
        </div>
        <div class="clear">&nbsp;</div>
        </form>
<?php
  }elseif (isset($_GET['alignment']) && (($locus = intval($_GET['alignment'])) > 0)) { // add alignment
    if (isset($_POST['addict']) && ($_POST['addict'] == '4') && !empty($_POST['program']) && !empty($_POST['alignment']) && (substr(trim($_POST['alignment']), 0, 10) == 'CLUSTAL W(') && !empty($_POST['consensus']) && isset($_POST['sequences'][2])) {
      $entry['alignment']['sequences'] = $_POST['sequences'];
      $entry['alignment']['alignment'] = preg_replace('/&amp;(#x?[0-9a-f]+);/', '&\1', htmlentities(html_entity_decode($_POST['alignment']), ENT_QUOTES, 'ISO8859-1'));
      $entry['alignment']['consensus'] = preg_replace('/[^\w]/', '', strtoupper($_POST['consensus']));
      $entry['alignment']['program'] = preg_replace('/[^\d\w\.\-\_\ ]/', '', $_POST['program']);
      if (!empty($_POST['comments'])) {
        $entry['alignment']['comments'] = ucfirst(preg_replace('/&amp;(#x?[0-9a-f]+);/', '&\1', htmlentities(html_entity_decode($_POST['comments']), ENT_QUOTES, 'ISO8859-1')));
      }
      $result = sql_query('INSERT INTO alignment (locus, sequences, alignment, consensus, program, comments, reviewer) VALUES (' . $locus . ',\'' . implode(' ', $entry['alignment']['sequences']) . '\',\'' . addslashes(base64_encode(bzcompress($entry['alignment']['alignment']))) . '\',\'' . addslashes(base64_encode(bzcompress($entry['alignment']['consensus']))) . '\',\'' . addslashes($entry['alignment']['program']) . '\',' . (isset($entry['alignment']['comments'])?('\'' . addslashes($entry['alignment']['comments']) . '\''):'NULL') . ',\'' . addslashes($_SESSION['login']['username']) . '\');', $sql);
      if (!strlen($r = sql_last_error($sql))) {
        header('Location: ' . $config['server'] . '/locus/' . $locus);
        exit(0);
      }else {
        $msg = '        <div class="warning"><p><img src="' . $config['server'] . '/images/warning.png" alt="">&nbsp;' . _("Entry invalid, check your data") . "</p></div>\n";
      }
    }
    head('new');
    if (!empty($msg)) {
      print $msg;
    }
?>
        <form action="<?php print $config['server'] . '/alignment/' . $locus; ?>/add" method="post">
        <div class="item">
          <h1>
            <?php print _("Alignment"); ?> 
          </h1>
          <div class="clear">&nbsp;</div>
          <p>
            <label for="program"><strong><?php print _("Program used:"); ?></strong></label> <input type="text" name="program" id="program" maxlength="100" title="<?php print _("Program and version used"); ?>" <?php print (isset($entry['alignment']['program'])?' value="' . $entry['alignment']['program'] . '" ':''); ?>/><br />
<?php
    $result = sql_query('SELECT a.id,a.name,a.accession,c.name,c.alias FROM sequence AS a, sequence_type AS b, organism as c WHERE a.locus=' . $locus . ' AND a.sequence_type=b.id AND a.organism=c.id ORDER BY a.release;', $sql);
    if ((!strlen($r = sql_last_error($sql))) && (sql_num_rows($result) > 0)) {
      print '            <label for="sequences"><strong>' . _("Sequence used:") . '</strong></label> <select name="sequences[]" id="sequences" title="' . _("Sequences used for the alignment") . '" multiple="multiple" class="multiple">';
      while ($row = sql_fetch_row($result)) {
        if (($_SESSION['specie'] == 'alias') && isset($row[4])) {
          $specie = ucfirst($row[4]);
        }else {
          $specie = ucfirst($row[3]);
        }
        print "<option value=\"$row[0]\"" . ((isset($entry['alignment']['sequences']) && (array_search($row[0], $entry['alignment']['sequences']) !== false))?' selected="selected"':'') . ">$row[1] ($row[2] - $specie)</option>";
      }
      print "</select><br />\n";
    }
?>
            <label for="alignment"><strong><?php print _("Alignment:"); ?></strong></label> <textarea name="alignment" id="alignment" cols="30" rows="6" title="<?php print _("Interleave ClustalW Alignment"); ?>"><?php print (isset($entry['alignment']['alignment'])?$entry['alignment']['alignment']:''); ?></textarea><br />
            <label for="consensus"><strong><?php print _("Consensus:"); ?></strong></label> <textarea name="consensus" id="consensus" cols="30" rows="3" title="<?php print _("Consensus 80%"); ?>"><?php print (isset($entry['alignment']['consensus'])?$entry['alignment']['consensus']:''); ?></textarea><br />
            <label for="comments"><?php print _("Comments:"); ?></label> <textarea name="comments" id="comments" cols="30" rows="3" title="<?php print _("Primer pair comments"); ?>"><?php print (isset($entry['alignment']['comments'])?$entry['alignment']['comments']:''); ?></textarea><br />
            <input type="hidden" name="addict" value="4" /><br /><button type="submit"><?php print _("Next"); ?>&nbsp;&gt;&gt;</button><br /><br />
          </p>
        </div>
        <div class="clear">&nbsp;</div>
        </form>
<?php
  }elseif (isset($_GET['primer']) && (($alignment = intval($_GET['primer'])) > 0)) { // add primer
    if (isset($_POST['addict']) && ($_POST['addict'] == '5') && !empty($_POST['left_seq']) && !empty($_POST['right_seq'])) {
      require_once('includes/dna.inc');
      $entry['primer']['left_seq'] = preg_replace('/[^\w]/', '', strtoupper($_POST['left_seq']));
      if (!empty($_POST['left_name'])) {
        $entry['primer']['left_name'] = preg_replace('/[^\d\w\.\-\_\ ]/', '', $_POST['left_name']);
      }
      $entry['primer']['left_tm'] = ((!empty($_POST['left_tm']) && (floatval($_POST['left_tm']) > 0))?floatval($_POST['left_tm']):Tm($entry['primer']['left_seq']));
      $entry['primer']['left_penality'] = ((!empty($_POST['left_penality']) && (floatval($_POST['left_penality']) > 0))?floatval($_POST['left_penality']):0);
      $entry['primer']['right_seq'] = preg_replace('/[^\w]/', '', strtoupper($_POST['right_seq']));
      if (!empty($_POST['right_name'])) {
        $entry['primer']['right_name'] = preg_replace('/[^\d\w\.\-\_\ ]/', '', $_POST['right_name']);
      }
      $entry['primer']['right_tm'] = ((!empty($_POST['right_tm']) && (floatval($_POST['right_tm']) > 0))?floatval($_POST['right_tm']):Tm($entry['primer']['right_seq']));
      $entry['primer']['right_penality'] = ((!empty($_POST['right_penality']) && (floatval($_POST['right_penality']) > 0))?floatval($_POST['right_penality']):0);
      if (!empty($_POST['location'])) {
        $entry['primer']['location'] = preg_replace('/[^\d\w\.\-\_\ ]/', '', $_POST['location']);
      }
      if (!empty($_POST['comments'])) {
        $entry['primer']['comments'] = ucfirst(preg_replace('/&amp;(#x?[0-9a-f]+);/', '&\1', htmlentities(html_entity_decode($_POST['comments']), ENT_QUOTES, 'ISO8859-1')));
      }
      if (!empty($_POST['pcr'])) {
        $entry['primer']['pcr'] = preg_replace('/&amp;(#x?[0-9a-f]+);/', '&\1', htmlentities(html_entity_decode($_POST['pcr']), ENT_QUOTES, 'ISO8859-1'));
      }
      if (isset($_POST['left_pos']) && (intval($_POST['left_pos']) > 0)) $entry['primer']['left_pos'] = intval($_POST['left_pos']);
      if (isset($_POST['right_pos']) && (intval($_POST['right_pos']) > 0)) $entry['primer']['right_pos'] = intval($_POST['right_pos']);
      $result = sql_query('SELECT locus, consensus FROM alignment WHERE id=' . $alignment . ';', $sql);
      if ((!strlen($r = sql_last_error($sql))) && (sql_num_rows($result) == 1)) {
        $row = sql_fetch_row($result);
        $locus = intval($row[0]);
        $consensus = bzdecompress(base64_decode($row[1]));
        $forward = (isset($entry['primer']['left_pos']) ? $entry['primer']['left_pos'] : strpos($consensus, align($entry['primer']['left_seq'], $consensus, get_pref('primer'))));
        $reverse = (isset($entry['primer']['right_pos']) ? $entry['primer']['right_pos'] : strpos($consensus, align(revert($entry['primer']['right_seq']), $consensus, get_pref('primer'))) + strlen($entry['primer']['right_seq'])-1);
        $forwardgc = (substr_count($entry['primer']['left_seq'], 'G') + substr_count($entry['primer']['left_seq'], 'C') + substr_count($entry['primer']['left_seq'], 'N') * 0.5) / strlen($entry['primer']['left_seq']) * 100;
        $reversegc = (substr_count($entry['primer']['right_seq'], 'G') + substr_count($entry['primer']['right_seq'], 'C') + substr_count($entry['primer']['right_seq'], 'N') * 0.5) / strlen($entry['primer']['right_seq']) * 100;
        $result = sql_query('INSERT INTO primer (locus, alignment, penality, left_seq, left_data, left_name, right_seq, right_data, right_name, location, pcr, comments, reviewer) VALUES (' . $locus . ',' . $alignment . ',' . ($entry['primer']['left_penality'] + $entry['primer']['right_penality']) . ',\'' . addslashes($entry['primer']['left_seq']) . '\',\'' . addslashes($forward . '|' . strlen($entry['primer']['left_seq']) . '|' . $entry['primer']['left_tm'] . '|' . $forwardgc . '|' . $entry['primer']['left_penality']) . '\',' . (isset($entry['primer']['left_name'])?('\'' . addslashes($entry['primer']['left_name']) . '\''):'NULL') . ',\'' . addslashes($entry['primer']['right_seq']) . '\',\'' . addslashes($reverse . '|' . strlen($entry['primer']['right_seq']) . '|' . $entry['primer']['right_tm'] . '|' . $reversegc . '|' . $entry['primer']['right_penality']) . '\',' . (isset($entry['primer']['right_name'])?('\'' . addslashes($entry['primer']['right_name']) . '\''):'NULL') . ',' . (isset($entry['primer']['location'])?('\'' . addslashes($entry['primer']['location']) . '\''):'NULL') . ',' . (isset($entry['primer']['pcr'])?('\'' . addslashes($entry['primer']['pcr']) . '\''):'NULL') . ',' . (isset($entry['primer']['comments'])?('\'' . addslashes($entry['primer']['comments']) . '\''):'NULL') . ',\'' . addslashes($_SESSION['login']['username']) . '\');', $sql);
        if (!strlen($r = sql_last_error($sql))) {
          header('Location: ' . $config['server'] . '/alignment/' . $alignment);
          exit(0);
        }else {
          $msg = '        <div class="warning"><p><img src="' . $config['server'] . '/images/warning.png" alt="">&nbsp;' . _("Entry invalid, check your data") . "</p></div>\n";
        }
      }else {
        $msg = '        <div class="warning"><p><img src="' . $config['server'] . '/images/warning.png" alt="">&nbsp;' . _("Entry invalid, check your data") . "</p></div>\n";
      }
    }
    head('new');
    if (!empty($msg)) {
      print $msg;
    }
?>
        <form action="<?php print $config['server'] . '/primer/' . $alignment; ?>/add" method="post">
        <div class="item">
          <h1>
            <?php print _("Primers"); ?> 
          </h1>
          <div class="clear">&nbsp;</div>
          <h2>
            <?php print _("Forward primer"); ?> 
          </h2>
          <p>
            <label for="left_name"><?php print _("Primer name:"); ?></label> <input type="text" name="left_name" id="left_name" maxlength="50" title="<?php print _("Reference name"); ?>" <?php print (isset($entry['primer']['left_name'])?' value="' . $entry['primer']['left_name'] . '" ':''); ?>/><br />
            <label for="left_seq"><strong><?php print _("Sequence:"); ?></strong></label> <input type="text" name="left_seq" id="left_seq" maxlength="50" title="<?php print _("DNA sequence"); ?>" <?php print (isset($entry['primer']['left_seq'])?' value="' . $entry['primer']['left_seq'] . '" ':''); ?>/><br />
            <label for="left_tm"><?php print _("TM:"); ?></label> <input type="text" name="left_tm" id="left_tm" maxlength="10" title="<?php print _("Estimate TM"); ?>" <?php print (isset($entry['primer']['left_tm'])?' value="' . $entry['primer']['left_tm'] . '" ':''); ?>/>°C<br />
            <label for="left_pos"><?php print _("Position:"); ?></label> <input type="text" name="left_pos" id="left_pos" maxlength="10" title="<?php print _("Start position of the primer in the alignment"); ?>" <?php print (isset($entry['primer']['left_pos'])?' value="' . $entry['primer']['left_pos'] . '" ':''); ?>/><br />
            <label for="left_penality"><?php print _("Primer penality:"); ?></label> <input type="text" name="left_penality" id="left_penality" maxlength="10" title="<?php print _("Primer penality (defined by Primer3)"); ?>" <?php print (isset($entry['primer']['left_penality'])?' value="' . $entry['primer']['left_penality'] . '" ':''); ?>/><br />
          </p>
          <h2>
            <?php print _("Reverse primer"); ?> 
          </h2>
          <p>
            <label for="right_name"><?php print _("Primer name:"); ?></label> <input type="text" name="right_name" id="right_name" maxlength="50" title="<?php print _("Reference name"); ?>" <?php print (isset($entry['primer']['right_name'])?' value="' . $entry['primer']['right_name'] . '" ':''); ?>/><br />
            <label for="right_seq"><strong><?php print _("Sequence:"); ?></strong></label> <input type="text" name="right_seq" id="right_seq" maxlength="50" title="<?php print _("DNA sequence"); ?>" <?php print (isset($entry['primer']['right_seq'])?' value="' . $entry['primer']['right_seq'] . '" ':''); ?>/><br />
            <label for="right_tm"><?php print _("TM:"); ?></label> <input type="text" name="right_tm" id="right_tm" maxlength="10" title="<?php print _("Estimate TM"); ?>" <?php print (isset($entry['primer']['right_tm'])?' value="' . $entry['primer']['right_tm'] . '" ':''); ?>/>°C<br />
            <label for="right_pos"><?php print _("Position:"); ?></label> <input type="text" name="right_pos" id="right_pos" maxlength="10" title="<?php print _("Start position of the primer in the alignment"); ?>" <?php print (isset($entry['primer']['right_pos'])?' value="' . $entry['primer']['right_pos'] . '" ':''); ?>/><br />
            <label for="right_penality"><?php print _("Primer penality:"); ?></label> <input type="text" name="right_penality" id="right_penality" maxlength="10" title="<?php print _("Primer penality (defined by Primer3)"); ?>" <?php print (isset($entry['primer']['right_penality'])?' value="' . $entry['primer']['right_penality'] . '" ':''); ?>/><br />
          </p>
          <h2>
            <?php print _("PCR conditions"); ?> 
          </h2>
          <p>
            <label for="pcr"><?php print _("PCR conditions:"); ?></label> <input type="text" name="pcr" id="pcr" title="<?php print _("PCR conditions"); ?>" <?php print (isset($entry['primer']['pcr'])?' value="' . $entry['primer']['pcr'] . '" ':''); ?>/><br />
          </p>
          <h2>
            <?php print _("General"); ?> 
          </h2>
          <p>
            <label for="location"><?php print _("Product location:"); ?></label> <input type="text" name="location" id="location" title="<?php print _("Product location (ex. intron1-exon2)"); ?>" <?php print (isset($entry['primer']['location'])?' value="' . $entry['primer']['location'] . '" ':''); ?>/><br />
            <label for="comments"><?php print _("Comments:"); ?></label> <textarea name="comments" id="comments" cols="30" rows="3" title="<?php print _("Primer pair comments"); ?>"><?php print (isset($entry['primer']['comments'])?$entry['primer']['comments']:''); ?></textarea><br />
            <input type="hidden" name="addict" value="5" /><br /><button type="submit"><?php print _("Next"); ?>&nbsp;&gt;&gt;</button><br /><br />
          </p>
        </div>
        <div class="clear">&nbsp;</div>
        </form>
<?php
  }else { // add locus
    if (isset($_POST['addict']) && !empty($_POST['name']) && !empty($_POST['functions']) && !empty($_POST['accession']) && !empty($_POST['evidence']) && isset($_POST['locus']) && isset($_POST['class']) && isset($_POST['status']) && isset($_POST['start']) && isset($_POST['end']) && (intval($_POST['start']) > 0) && (intval($_POST['end']) > 0) && ($_POST['addict'] == '1') && (intval($_POST['locus']) > 0) && (intval($_POST['class']) > 0) && (intval($_POST['status']) > 0)) {
      require_once('includes/import.inc');
      $entry['locus']['name'] = preg_replace('/&amp;(#x?[0-9a-f]+);/', '&\1', htmlentities(html_entity_decode($_POST['name']), ENT_QUOTES, 'ISO8859-1'));
      if (!empty($_POST['alias'])) $entry['locus']['alias'] = preg_replace('/&amp;(#x?[0-9a-f]+);/', '&\1', htmlentities(html_entity_decode($_POST['alias']), ENT_QUOTES, 'ISO8859-1'));
      $entry['locus']['type'] = intval($_POST['locus']);
      $entry['locus']['evidence'] = $_POST['evidence'];
      $entry['locus']['class'] = intval($_POST['class']);
      $entry['locus']['status'] = intval($_POST['status']);
      if (!empty($_POST['functions'])) {
        $entry['locus']['functions'] = ucfirst(preg_replace('/&amp;(#x?[0-9a-f]+);/', '&\1', htmlentities(html_entity_decode($_POST['functions']), ENT_QUOTES, 'ISO8859-1')));
      }
      if (!empty($_POST['comments'])) {
        $entry['locus']['comments'] = ucfirst(preg_replace('/&amp;(#x?[0-9a-f]+);/', '&\1', htmlentities(html_entity_decode($_POST['comments']), ENT_QUOTES, 'ISO8859-1')));
      }
      if (!empty($_POST['references'])) {
        $entry['locus']['references'] = preg_replace('/&amp;(#x?[0-9a-f]+);/', '&\1', htmlentities(html_entity_decode($_POST['references']), ENT_QUOTES, 'ISO8859-1'));
      }
      $entry['sequence']['type'] = 1;
      $entry['sequence']['accession'] = preg_replace('/[^\d\w\.]/', '', strtoupper($_POST['accession']));
      if (!empty($_POST['isolate'])) {
        $entry['sequence']['isolate'] = preg_replace('/[^\d\w\.\-\_\ ]/', '', $_POST['isolate']);
      }
      if (!empty($_POST['map'])) $entry['sequence']['map'] = preg_replace('/[^\d\w\.\-\_\ ]/', '', $_POST['map']);
      if (intval($_POST['start']) > intval($_POST['end'])) {
        $entry['sequence']['end'] = intval($_POST['start']);
        $entry['sequence']['start'] = intval($_POST['end']);
      }else {
        $entry['sequence']['start'] = intval($_POST['start']);
        $entry['sequence']['end'] = intval($_POST['end']);
      }
      if (!empty($_POST['seq_comments'])) {
        $entry['sequence']['comments'] = ucfirst(preg_replace('/&amp;(#x?[0-9a-f]+);/', '&\1', htmlentities(html_entity_decode($_POST['seq_comments']), ENT_QUOTES, 'ISO8859-1')));
      }
      if (!empty($_POST['seq_references'])) {
        $entry['sequence']['references'] = preg_replace('/&amp;(#x?[0-9a-f]+);/', '&\1', htmlentities(html_entity_decode($_POST['seq_references']), ENT_QUOTES, 'ISO8859-1'));
      }
      $msg = addLocus($entry, $_SESSION['delay'], $sql);
      if (empty($msg)) {
        header('Location: ' . $config['server'] . '/browse');
        exit(0);
      }
    }
    head('new');
    if (!empty($msg)) {
      print $msg;
    }
?>
        <form action="<?php print $config['server']; ?>/add" method="post">
        <div class="item">
          <h1>
            <?php print _("Locus"); ?> 
          </h1>
          <div class="clear">&nbsp;</div>
          <p>
            <label for="name"><strong><?php print _("Locus name:"); ?></strong></label> <input type="text" name="name" id="name" maxlength="100" title="<?php print _("Reference name or gene name"); ?>" <?php print (isset($entry['locus']['name'])?' value="' . $entry['locus']['name'] . '" ':''); ?>/><br />
            <label for="alias"><?php print _("Alias:"); ?></label> <input type="text" name="alias" id="alias" maxlength="100" title="<?php print _("Locus alias"); ?>" <?php print (isset($entry['locus']['alias'])?' value="' . $entry['locus']['alias'] . '" ':''); ?>/><br />
<?php
    $result = sql_query('SELECT id,locus_type FROM locus_type ORDER BY id;', $sql);
    if ((!strlen($r = sql_last_error($sql))) && (sql_num_rows($result) != 0)) {
      print '        <label for="locus"><strong>' . _("Locus type:") . '</strong></label> <select name="locus" id="locus" title="' . _("Specifies the type of locus, as defined by the NCBI") . '">';
      while ($row = sql_fetch_row($result)) {
        print "<option value=\"$row[0]\"" . ((isset($entry['locus']['type']) && $entry['locus']['type'] == $row[0])?' selected="selected"':'') . ">$row[1]</option>";
      }
      print "</select><br />\n";
    }
    $result = sql_query('SELECT id,evidence FROM evidence;', $sql);
    if ((!strlen($r = sql_last_error($sql))) && (sql_num_rows($result) != 0)) {
      print '            <label for="evidence"><strong>' . _("Evidence:") . '</strong></label> <select name="evidence" id="evidence" title="' . _("Biological evidence (at least for the reference sequence)") . '">';
      while ($row = sql_fetch_row($result)) {
        print "<option value=\"$row[0]\"" . ((isset($entry['locus']['evidence']) && $entry['locus']['evidence'] == $row[0])?' selected="selected"':'') . ">$row[1]</option>";
      }
      print "</select><br />\n";
    }
    $result = sql_query('SELECT id,class FROM class ORDER BY id;', $sql);
    if ((!strlen($r = sql_last_error($sql))) && (sql_num_rows($result) != 0)) {
      print '            <label for="class"><strong>' . _("Class:") . '</strong></label> <select name="class" id="class" title="' . _("Gene class (at least for the reference sequence)") . '">';
      while ($row = sql_fetch_row($result)) {
        print "<option value=\"$row[0]\"" . ((isset($entry['locus']['class']) && $entry['locus']['class'] == $row[0])?' selected="selected"':'') . ">$row[1]</option>";
      }
      print "</select><br />\n";
    }
    $result = sql_query('SELECT id,status FROM status ORDER BY id;', $sql);
    if ((!strlen($r = sql_last_error($sql))) && (sql_num_rows($result) != 0)) {
      print '            <label for="status"><strong>' . _("Status:") . '</strong></label> <select name="status" id="status" title="' . _("Status details") . '">';
      while ($row = sql_fetch_row($result)) {
        print "<option value=\"$row[0]\"" . ((isset($entry['locus']['status']) && $entry['locus']['status'] == $row[0])?' selected="selected"':'') . ">$row[1]</option>";
      }
      print "</select><br />\n";
    }
?>
            <label for="functions"><strong><?php print _("Functions:"); ?></strong></label> <textarea name="functions" id="functions" cols="30" rows="3" title="<?php print _("Known functions (at least for the reference sequence)"); ?>"><?php print (isset($entry['locus']['functions'])?$entry['locus']['functions']:''); ?></textarea><br />
            <label for="comments"><?php print _("Comments:"); ?></label> <textarea name="comments" id="comments" cols="30" rows="3" title="<?php print _("Extra comments"); ?>"><?php print (isset($entry['locus']['comments'])?$entry['locus']['comments']:''); ?></textarea><br />
            <label for="references"><?php print _("References:"); ?></label> <textarea name="references" id="references" cols="30" rows="3" title="<?php print _("References for the locus (wiki style [ full_url | name ])"); ?>"><?php print (isset($entry['locus']['references'])?$entry['locus']['references']:''); ?></textarea><br />
          </p>
        </div>
        <div class="clear">&nbsp;</div>
        <div class="item">
          <h2>
            <?php print _("Sequence"); ?> 
          </h2>
          <div class="clear">&nbsp;</div>
          <p>
            <label for="accession"><strong><?php print _("Accession:"); ?></strong></label> <input type="text" name="accession" id="accession" maxlength="50" title="<?php print _("Genbank accession number"); ?>" <?php print (isset($entry['sequence']['accession'])?' value="' . $entry['sequence']['accession'] . '" ':''); ?>/><br />
            <label for="map"><?php print _("Map:"); ?></label> <input type="text" name="map" id="map" maxlength="100" title="<?php print _("Gene mapping"); ?>" <?php print (isset($entry['sequence']['map'])?' value="' . $entry['sequence']['map'] . '" ':''); ?>/><br />
            <label for="isolate"><?php print _("Isolate:"); ?></label> <input type="text" name="isolate" id="isolate" maxlength="100" title="<?php print _("Isolate name / Country / Lat-Long"); ?>" <?php print (isset($entry['sequence']['isolate'])?' value="' . $entry['sequence']['isolate'] . '" ':''); ?>/><br />
            <label for="start"><strong><?php print _("Start:"); ?></strong></label> <input type="text" name="start" id="start" maxlength="20" title="<?php print _("Start position of the sequence"); ?>" <?php print (isset($entry['sequence']['start'])?' value="' . $entry['sequence']['start'] . '" ':''); ?>/><br />
            <label for="end"><strong><?php print _("End:"); ?></strong></label> <input type="text" name="end" id="end" maxlength="20" title="<?php print _("End position of the sequence"); ?>" <?php print (isset($entry['sequence']['end'])?' value="' . $entry['sequence']['end'] . '" ':''); ?>/><br />
            <label for="seq_comments"><?php print _("Description:"); ?></label> <textarea name="seq_comments" id="seq_comments" cols="30" rows="3" title="<?php print _("Sequence description"); ?>"><?php print (isset($entry['sequence']['comments'])?$entry['sequence']['comments']:''); ?></textarea><br />
            <label for="seq_references"><?php print _("References:"); ?></label> <textarea name="seq_references" id="seq_references" cols="30" rows="3" title="<?php print _("References (wiki style [ full_url | name ])"); ?>"><?php print (isset($entry['sequence']['references'])?$entry['sequence']['references']:''); ?></textarea><br />
            <input type="hidden" name="addict" value="1" /><br /><button type="submit"><?php print _("Next"); ?>&nbsp;&gt;&gt;</button><br /><br />
          </p>
        </div>
        <div class="clear">&nbsp;</div>
        </form>
<?php
  }
  foot();
}else {
  header("HTTP/1.0 403 Forbidden");
  exit(403);
}
?>