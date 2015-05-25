<?php
ob_start("ob_gzhandler");
session_start();
require_once('../includes/main.inc');

if ($config['login']) {
  if (!isset($_SESSION['specie'])) {
    @set_pref('specie', 'name');
  }
  if (!isset($_SESSION['primer'])) {
    @set_pref('primer', 5);
  }
  $sql = sql_connect($config['db']);
  if (isset($_GET['locus']) && preg_match('/^(\d+)\.(\d+)/', $_GET['locus'], $matches)) { // Locus
    $matches[1] = octdec($matches[1]);
    $matches[2] = octdec($matches[2]);
    if (isset($_POST['addict']) && ($_POST['addict'] == '1') && isset($_POST['remove']) && ($_POST['remove'] == 'remove')) {
      $result = sql_query('DELETE FROM locus WHERE prefix=' . $matches[1] . ' AND id=' . $matches[2] . ';', $sql);
      $result = sql_query('DELETE FROM sequence WHERE locus_prefix=' . $matches[1] . ' AND locus_id=' . $matches[2] . ';', $sql);
      $result = sql_query('DELETE FROM mrna WHERE locus_prefix=' . $matches[1] . ' AND locus_id=' . $matches[2] . ';', $sql);
      $result = sql_query('DELETE FROM alignment WHERE locus_prefix=' . $matches[1] . ' AND locus_id=' . $matches[2] . ';', $sql);
      $result = sql_query('DELETE FROM primer WHERE locus_prefix=' . $matches[1] . ' AND locus_id=' . $matches[2] . ';', $sql);
      header('Location: ' . $config['server'] . '/browse');
      exit(0);
    }elseif (isset($_POST['addict']) && ($_POST['addict'] == '1') && !empty($_POST['name']) && !empty($_POST['functions']) && !empty($_POST['evidence']) && isset($_POST['class']) && isset($_POST['status']) && (intval($_POST['class']) > 0) && (intval($_POST['status']) > 0)) {
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
      $result = sql_query('UPDATE locus SET name=\'' . addslashes($entry['locus']['name']) . '\', alias=' . (isset($entry['locus']['alias'])?'\'' . addslashes($entry['locus']['alias']) . '\'':'NULL') . ', locus_type=' . $entry['locus']['type'] . ', evidence=\'' . $entry['locus']['evidence'] . '\', class=' . $entry['locus']['class'] . ', status=' . $entry['locus']['status'] . ', functions=' . (isset($entry['locus']['functions'])?'\'' . addslashes($entry['locus']['functions']) . '\'':'NULL') . ', comments=' . (isset($entry['locus']['comments'])?'\'' . addslashes($entry['locus']['comments']) . '\'':'NULL') . ', sources=' . (isset($entry['locus']['references'])?'\'' . addslashes($entry['locus']['references']) . '\'':'NULL') . ', author=\'' . addslashes($_SESSION['login']['username']) . '\', updated=CURRENT_TIMESTAMP WHERE prefix=' . $matches[1] . ' AND id=' . $matches[2] . ';', $sql);
      if (!strlen($r = sql_last_error($sql))) {
        header('Location: ' . $config['server'] . '/locus/' . $matches[0]);
        exit(0);
      }else {
        $msg = '        <div class="warning"><p><img src="' . $config['server'] . '/images/warning.png" alt="">&nbsp;' . _("Entry invalid, check your data") . "</p></div>\n";
      }
    }
    head('locus', true);
    if (isset($msg)) {
      print $msg;
    }
    $result = sql_query('SELECT name,alias,functions,comments,sources,evidence,class,status,locus_type FROM locus WHERE prefix=' . $matches[1] . ' AND id=' . $matches[2] . ';', $sql);
    if ((!strlen($r = sql_last_error($sql))) && (sql_num_rows($result) == 1)) {
      $row = sql_fetch_row($result);
?>
        <div class="item">
        <form action="<?php print $config['server'] . '/locus/' . $matches[0]; ?>/edit" method="post">
          <h1>
            <?php print _("Locus"); ?> 
          </h1>
          <div class="clear">&nbsp;</div>
          <p>
            <label for="name"><strong><?php print _("Locus name:"); ?></strong></label> <input type="text" name="name" id="name" maxlength="100" title="<?php print _("Reference name or gene name"); ?>" <?php print (isset($row[0])?' value="' . $row[0] . '" ':''); ?>/><br />
            <label for="alias"><?php print _("Alias:"); ?></label> <input type="text" name="alias" id="alias" maxlength="100" title="<?php print _("Locus alias"); ?>" <?php print (isset($row[1])?' value="' . $row[1] . '" ':''); ?>/><br />
<?php
      $result = sql_query('SELECT id,locus_type FROM locus_type ORDER BY id;', $sql);
      if ((!strlen($r = sql_last_error($sql))) && (sql_num_rows($result) != 0)) {
        print '            <label for="locus"><strong>' . _("Locus type:") . '</strong></label> <select name="locus" id="locus" title="' . _("Specifies the type of locus, as defined by the NCBI") . '">';
        while ($row2 = sql_fetch_row($result)) {
          print "<option value=\"$row2[0]\"" . ((isset($row[8]) && $row[8] == $row2[0])?' selected="selected"':'') . ">$row2[1]</option>";
        }
        print "</select><br />\n";
      }
      $result = sql_query('SELECT id,evidence FROM evidence;', $sql);
      if ((!strlen($r = sql_last_error($sql))) && (sql_num_rows($result) != 0)) {
        print '            <label for="evidence"><strong>' . _("Evidence:") . '</strong></label> <select name="evidence" id="evidence" title="' . _("Biological evidence (at least for the reference sequence)") . '">';
        while ($row2 = sql_fetch_row($result)) {
          print "<option value=\"$row2[0]\"" . ((isset($row[5]) && $row[5] == $row2[0])?' selected="selected"':'') . ">$row2[1]</option>";
        }
        print "</select><br />\n";
      }
      $result = sql_query('SELECT id,class FROM class ORDER BY id;', $sql);
      if ((!strlen($r = sql_last_error($sql))) && (sql_num_rows($result) != 0)) {
        print '            <label for="class"><strong>' . _("Class:") . '</strong></label> <select name="class" id="class" title="' . _("Gene class (at least for the reference sequence)") . '">';
        while ($row2 = sql_fetch_row($result)) {
          print "<option value=\"$row2[0]\"" . ((isset($row[6]) && $row[6] == $row2[0])?' selected="selected"':'') . ">$row2[1]</option>";
        }
        print "</select><br />\n";
      }
      $result = sql_query('SELECT id,status FROM status ORDER BY id;', $sql);
      if ((!strlen($r = sql_last_error($sql))) && (sql_num_rows($result) != 0)) {
        print '            <label for="status"><strong>' . _("Status:") . '</strong></label> <select name="status" id="status" title="' . _("Status details") . '">';
        while ($row2 = sql_fetch_row($result)) {
          print "<option value=\"$row2[0]\"" . ((isset($row[7]) && $row[7] == $row2[0])?' selected="selected"':'') . ">$row2[1]</option>";
        }
        print "</select><br />\n";
      }
?>
            <label for="functions"><strong><?php print _("Functions:"); ?></strong></label> <textarea name="functions" id="functions" cols="30" rows="3" title="<?php print _("Known functions (at least for the reference sequence)"); ?>"><?php print (isset($row[2])?$row[2]:''); ?></textarea><br />
            <label for="comments"><?php print _("Comments:"); ?></label> <textarea name="comments" id="comments" cols="30" rows="3" title="<?php print _("Extra comments"); ?>"><?php print (isset($row[3])?$row[3]:''); ?></textarea><br />
            <label for="references"><?php print _("References:"); ?></label> <textarea name="references" id="references" cols="30" rows="3" title="<?php print _("References for the locus (wiki style [ full_url | name ])"); ?>"><?php print (isset($row[4])?$row[4]:''); ?></textarea><br />
            <input type="hidden" name="addict" value="1" /><br /><button type="submit"><?php print _("Next"); ?>&nbsp;&gt;&gt;</button><button type="submit" name="remove" value="remove" onclick="return confirmSubmit('<?php print _("Are you sure you wish to continue?"); ?>')"><?php print _("Remove"); ?></button><br /><br />
          </p>
        </form>
        </div>
        <div class="clear">&nbsp;</div>
<?php
    }
    foot();
  }elseif (isset($_GET['sequence']) && preg_match('/^(\d+)\.(\d+)/', $_GET['sequence'], $matches)) { // sequence
    $matches[1] = octdec($matches[1]);
    $matches[2] = octdec($matches[2]);
    if (isset($_POST['addict']) && ($_POST['addict'] == '2') && isset($_POST['remove']) && ($_POST['remove'] == 'remove')) {
      $result = sql_query('DELETE FROM sequence WHERE prefix=' . $matches[1] . ' AND id=' . $matches[2] . ';', $sql);
      header('Location: ' . $config['server'] . '/browse');
      exit(0);
    }elseif (isset($_POST['addict']) && ($_POST['addict'] == '2') && !isset($_POST['update']) && isset($_POST['type']) && (((intval($_POST['type']) == 1) && empty($_POST['primer']) && empty($_POST['evalue'])) || ((intval($_POST['type']) == 2) && empty($_POST['primer']) && !empty($_POST['evalue'])) || ((intval($_POST['type']) == 3) && !empty($_POST['primer']) && empty($_POST['evalue']))) && !empty($_POST['name']) && !empty($_POST['sequence']) && !empty($_POST['topology']) && !empty($_POST['molecule']) && isset($_POST['organism']) && isset($_POST['strand']) && (intval($_POST['strand']) != 0) && (intval($_POST['organism']) > 0)) {
      $entry['sequence']['type'] = intval($_POST['type']);
      if (!empty($_POST['primer'])) {
        $entry['sequence']['primer'] = preg_replace('/&amp;(#x?[0-9a-f]+);/', '&\1', htmlentities(html_entity_decode($_POST['primer']), ENT_QUOTES, 'ISO8859-1'));
      }
      if (!empty($_POST['evalue'])) {
        $entry['sequence']['evalue'] = floatval($_POST['evalue']);
      }
      if (!empty($_POST['isolate'])) {
        $entry['sequence']['isolate'] = preg_replace('/[^\d\w\.\-\_\ ]/', '', $_POST['isolate']);
      }
      if (!empty($_POST['map'])) $entry['sequence']['map'] = preg_replace('/[^\d\w\.\-\_\ ]/', '', $_POST['map']);
      $entry['sequence']['sequence'] = preg_replace('/[^\w]/', '', strtoupper($_POST['sequence']));
      if (!empty($_POST['seq_map'])) {
        foreach (explode("\n", $_POST['seq_map']) as $ligne) {
          $char = explode('|', $ligne, 4);
          if ((isset($char[0]) && (intval($char[0]) > 0)) && (isset($char[1]) && (intval($char[1]) > intval($char[0]))) && (isset($char[2]) && ((trim($char[2]) == 'P') || (trim($char[2]) == 'V') || (trim($char[2]) == 'S'))) && (!empty($char[3]))) {
            $ret[] = intval($char[0]) . '|' . intval($char[1]) . '|' . trim($char[2]) . '|' . ucfirst(preg_replace('/&amp;(#x?[0-9a-f]+);/', '&\1', htmlentities(html_entity_decode($char[3]), ENT_QUOTES, 'ISO8859-1')));
          }
        }
        if (isset($ret)) $entry['sequence']['structure'] = join("\n", $ret);
      }
      if (!empty($_POST['seq_comments'])) {
        $entry['sequence']['comments'] = ucfirst(preg_replace('/&amp;(#x?[0-9a-f]+);/', '&\1', htmlentities(html_entity_decode($_POST['seq_comments']), ENT_QUOTES, 'ISO8859-1')));
      }
      if (!empty($_POST['seq_references'])) {
        $entry['sequence']['references'] = preg_replace('/&amp;(#x?[0-9a-f]+);/', '&\1', htmlentities(html_entity_decode($_POST['seq_references']), ENT_QUOTES, 'ISO8859-1'));
      }
      if (!empty($_POST['accession']) && isset($_POST['start']) && isset($_POST['end'])) {
        $entry['sequence']['accession'] = preg_replace('/[^\d\w\.]/', '', strtoupper($_POST['accession']));
        if (intval($_POST['start']) > intval($_POST['end'])) {
          $entry['sequence']['end'] = intval($_POST['start']);
          $entry['sequence']['start'] = intval($_POST['end']);
        }else {
          $entry['sequence']['start'] = intval($_POST['start']);
          $entry['sequence']['end'] = intval($_POST['end']);
        }
      }else {
        $entry['sequence']['start'] = 1;
        $entry['sequence']['end'] = strlen($entry['sequence']['sequence']);
      }
      if (!empty($_POST['go'])) {
        $entry['sequence']['go'] = preg_replace('/&amp;(#x?[0-9a-f]+);/', '&\1', htmlentities(html_entity_decode($_POST['go']), ENT_QUOTES, 'ISO8859-1'));
      }
      if (!empty($_POST['hgnc'])) {
        $entry['sequence']['hgnc'] = intval($_POST['hgnc']);
      }
      if (!empty($_POST['geneid'])) {
        $entry['sequence']['geneid'] = intval($_POST['geneid']);
      }
      if (!empty($_POST['alias'])) {
        $entry['sequence']['alias'] = preg_replace('/&amp;(#x?[0-9a-f]+);/', '&\1', htmlentities(html_entity_decode($_POST['alias']), ENT_QUOTES, 'ISO8859-1'));
      }
      $entry['sequence']['name'] = preg_replace('/&amp;(#x?[0-9a-f]+);/', '&\1', htmlentities(html_entity_decode($_POST['name']), ENT_QUOTES, 'ISO8859-1'));
      if (!empty($_POST['location'])) {
        $entry['sequence']['location'] = $entry['sequence']['chromosome'] = preg_replace('/[^\d\w\.\-\_\(\)]/', '', $_POST['location']);
      }
      $entry['sequence']['strand'] = intval($_POST['strand']);
      if (!empty($_POST['chromosome'])) {
        $entry['sequence']['chromosome'] = preg_replace('/[^\d\w\.\-\_]/', '', $_POST['chromosome']);
      }
      $entry['sequence']['circular'] = (($_POST['topology'] == 't')?'t':'f');
      $entry['sequence']['molecule'] = preg_replace('/[^\w]/', '', $_POST['molecule']);
      $entry['sequence']['organism'] = intval($_POST['organism']);
      if (!empty($_POST['organelle'])) {
        $entry['sequence']['organelle'] = preg_replace('/[^\d\w\.\-\_\ ]/', '', $_POST['organelle']);
      }
      $entry['sequence']['translation'] = intval($_POST['translation']);
      $result = sql_query('UPDATE sequence SET name=\'' . addslashes($entry['sequence']['name']) . '\', alias=' . (isset($entry['locus']['alias'])?'\'' . addslashes($entry['sequence']['alias']) . '\'':'NULL') . ', location=' . (isset($entry['sequence']['location'])?'\'' . addslashes($entry['sequence']['location']) . '\'':'NULL') . ', isolate=' . (isset($entry['sequence']['isolate'])?'\'' . addslashes($entry['sequence']['isolate']) . '\'':'NULL') . ', organelle=' . (isset($entry['sequence']['organelle'])?'\'' . addslashes($entry['sequence']['organelle']) . '\'':'NULL') . ', translation=' . $entry['sequence']['translation'] . ', molecule=\'' . addslashes($entry['sequence']['molecule']) . '\', circular=\'' . $entry['sequence']['circular'] . '\', chromosome=' . (isset($entry['sequence']['chromosome'])?'\'' . addslashes($entry['sequence']['chromosome']) . '\'':'NULL') . ', map=' . (isset($entry['sequence']['map'])?'\'' . addslashes($entry['sequence']['map']) . '\'':'NULL') . ', accession=' . (isset($entry['sequence']['accession'])?'\'' . addslashes($entry['sequence']['accession']) . '\'':'NULL') . ', hgnc=' . (isset($entry['sequence']['hgnc'])?$entry['sequence']['hgnc']:'NULL') . ', geneid=' . (isset($entry['sequence']['geneid'])?$entry['sequence']['geneid']:'NULL') . ', organism=' . $entry['sequence']['organism'] . ', go=' . (isset($entry['sequence']['go'])?'\'' . addslashes($entry['sequence']['go']) . '\'':'NULL') . ', sequence_type=' . $entry['sequence']['type'] . ', stop=' . $entry['sequence']['end'] . ', start=' . $entry['sequence']['start'] . ', strand=' . $entry['sequence']['strand'] . ', sequence=\'' . base64_encode(bzcompress($entry['sequence']['sequence'])) . '\', evalue=' . (isset($entry['sequence']['evalue'])?$entry['sequence']['evalue']:'NULL') . ', sources=' . (isset($entry['sequence']['references'])?'\'' . addslashes($entry['sequence']['references']) . '\'':'NULL') . ', comments=' . (isset($entry['sequence']['comments'])?'\'' . addslashes($entry['sequence']['comments']) . '\'':'NULL') . ', structure=' . (isset($entry['sequence']['structure'])?'\'' . addslashes($entry['sequence']['structure']) . '\'':'NULL') . ', primer=' . (isset($entry['sequence']['primer'])?'\'' . addslashes($entry['sequence']['primer']) . '\'':'NULL') . ', author=\'' . addslashes($_SESSION['login']['username']) . '\', updated=CURRENT_TIMESTAMP WHERE prefix=' . $matches[1] . ' AND id=' . $matches[2] . ';', $sql);
      if (!strlen($r = sql_last_error($sql))) {
        header('Location: ' . $config['server'] . '/sequence/' . $matches[0]);
        exit(0);
      }else {
        $msg = '        <div class="warning"><p><img src="' . $config['server'] . '/images/warning.png" alt="">&nbsp;' . _("Entry invalid, check your data") . "</p></div>\n";
      }
    }elseif (isset($_POST['addict']) && ($_POST['addict'] = '2') && !empty($_POST['accession']) && isset($_POST['update']) && isset($_POST['start']) && isset($_POST['end']) && ($_POST['update'] == 'go') && (intval($_POST['start']) > 0) && (intval($_POST['end']) > 0)) {
      require_once('includes/import.inc');
      if ((($entry['genbank'] = getgenbank($_POST['accession'], $_POST['start'], $_POST['end'])) !== false) && !empty($entry['genbank']['gene']) && !empty($entry['genbank']['location']) && !empty($entry['genbank']['molecule']) && !empty($entry['genbank']['circular']) && !empty($entry['genbank']['reference']) && !empty($entry['genbank']['geneid']) && !empty($entry['genbank']['strand']) && !empty($entry['genbank']['sequence']) && !empty($entry['genbank']['division']) && !empty($entry['genbank']['organism']) && !empty($entry['genbank']['phylum']) && !empty($entry['genbank']['taxonid'])) {
        $msg = '        <div class="info"><p><img src="' . $config['server'] . '/images/info.png" alt="">&nbsp;' . _("Genbank data updated (preview)") . "</p></div>\n";
      }else {
        unset($entry['genbank']);
        $msg = '        <div class="warning"><p><img src="' . $config['server'] . '/images/warning.png" alt="">&nbsp;' . _("Accession number unknown") . "</p></div>\n";
      }
    }
    head('sequence', true);
    if (isset($msg)) {
      print $msg;
    }
    $result = sql_query('SELECT locus_prefix,locus_id,name,alias,location,translation,molecule,circular,chromosome,isolate,organelle,map,accession,hgnc,geneid,organism,go,sequence_type,primer,start,stop,strand,sequence,evalue,sources,comments,structure FROM sequence WHERE prefix=' . $matches[1] . ' AND id=' . $matches[2] . ';', $sql);
    if ((!strlen($r = sql_last_error($sql))) && (sql_num_rows($result) == 1)) {
      $row = sql_fetch_row($result);
      $row[22] = bzdecompress(base64_decode($row[22]));
?>
        <form action="<?php print $config['server'] . '/sequence/' . $matches[0]; ?>/edit" method="post">
        <div class="item">
          <h1>
            <?php print _("Sequence"); ?> 
          </h1>
          <div class="clear">&nbsp;</div>
          <p>
            <label for="accession"><strong><?php print _("Accession:"); ?></strong></label> <input type="text" name="accession" id="accession" maxlength="50" title="<?php print _("Genbank accession number"); ?>" <?php print (!empty($entry['genbank']['reference'])?' value="' . $entry['genbank']['reference'] . '" ':(isset($row[12])?' value="' . $row[12] . '" ':'')); ?>/><?php print ((!empty($entry['genbank']['reference']) && (empty($row[12]) || ($row[12] != $entry['genbank']['reference'])))?'&nbsp;<span class="warning">updated from Genbank</span>':''); ?><br />
            <label for="start"><strong><?php print _("Start:"); ?></strong></label> <input type="text" name="start" id="start" maxlength="20" title="<?php print _("Start position of the sequence"); ?>" <?php print (!empty($entry['genbank']['start'])?' value="' . $entry['genbank']['start'] . '" ':(isset($row[19])?' value="' . $row[19] . '" ':'')); ?>/><?php print ((!empty($entry['genbank']['start']) && (empty($row[19]) || ($row[19] != $entry['genbank']['start'])))?'&nbsp;<span class="warning">updated from Genbank</span>':''); ?><br />
            <label for="end"><strong><?php print _("End:"); ?></strong></label> <input type="text" name="end" id="end" maxlength="20" title="<?php print _("End position of the sequence"); ?>" <?php print (!empty($entry['genbank']['end'])?' value="' . $entry['genbank']['end'] . '" ':(isset($row[20])?' value="' . $row[20] . '" ':'')); ?>/><?php print ((!empty($entry['genbank']['end']) && (empty($row[20]) || ($row[20] != $entry['genbank']['end'])))?'&nbsp;<span class="warning">updated from Genbank</span>':''); ?><br />
            <input type="hidden" name="addict" value="2" /><br /><button type="submit" name="update" value="go"><?php print _("Update from GenBank"); ?></button><br /><br />
          </p>
        </div>
        <div class="clear">&nbsp;</div>
        <div class="item">
          <p>
<?php
      $result = sql_query('SELECT id,sequence_type FROM sequence_type ORDER BY id;', $sql);
      if (sql_num_rows($result) != 0) {
        print '            <label for="type"><strong>' . _("Sequence type:") . '</strong></label> <select name="type" id="type" title="' . _("Sequence type") . '">';
        while ($row2 = sql_fetch_row($result)) {
          print "<option value=\"$row2[0]\"" . ((isset($row[17]) && ($row[17] == $row2[0]))?' selected="selected"':'') . ">$row2[1]</option>";
        }
        print "</select><br /><br />\n";
      }
?>
            <label for="name"><strong><?php print _("Name:"); ?></strong></label> <input type="text" name="name" id="name" maxlength="100" title="<?php print _("Sequence Name"); ?>" <?php print (!empty($entry['genbank']['gene'])?' value="' . $entry['genbank']['gene'] . '" ':(isset($row[2])?' value="' . $row[2] . '" ':'')); ?>/><?php print ((!empty($entry['genbank']['gene']) && (empty($row[2]) || ($row[2] != $entry['genbank']['gene'])))?'&nbsp;<span class="warning">updated from Genbank</span>':''); ?><br />
            <label for="alias"><?php print _("Alias:"); ?></label> <input type="text" name="alias" id="alias" maxlength="100" title="<?php print _("Locus alias"); ?>" <?php print (isset($row[3])?' value="' . $row[3] . '" ':''); ?>/><br />
            <label for="evalue"><strong><?php print _("E-value:"); ?></strong></label> <input type="text" name="evalue" id="evalue" maxlength="50" title="<?php print _("E-value"); ?>" <?php print (isset($row[23])?' value="' . $row[23] . '" ':''); ?>/>&nbsp;<?php print _("for blasted sequence only"); ?><br />
            <label for="primer"><strong><?php print _("Primer:"); ?></strong></label> <input type="text" name="primer" id="primer" maxlength="100" title="<?php print _("Primer used"); ?>" <?php print (isset($row[18])?' value="' . $row[18] . '" ':''); ?>/>&nbsp;<?php print _("for generated sequence only"); ?><br />
            <label for="location"><?php print _("Location:"); ?></label> <input type="text" name="location" id="location" title="<?php print _("Sequence location"); ?>" <?php print (!empty($entry['genbank']['location'])?' value="' . $entry['genbank']['location'] . '" ':(isset($row[4])?' value="' . $row[4] . '" ':'')); ?>/><?php print ((!empty($entry['genbank']['location']) && (empty($row[4]) || ($row[4] != $entry['genbank']['location'])))?'&nbsp;<span class="warning">updated from Genbank</span>':''); ?><br />
            <label for="strand"><strong><?php print _("Strand:"); ?></strong></label> <select name="strand" id="strand" title="<?php print _("Strand"); ?>"><option value="1"<?php print (isset($entry['genbank']['strand'])?(($entry['genbank']['strand'] == 1)?' selected="selected"':''):(($row[21] == 1)?' selected="selected"':'')); ?>><?php print _("Direct"); ?></option><option value="-1"<?php print (isset($entry['genbank']['strand'])?(($entry['genbank']['strand'] == -1)?' selected="selected"':''):(($row[21] == -1)?' selected="selected"':'')); ?>><?php print _("Complementary"); ?></option></select><?php print ((!empty($entry['genbank']['strand']) && (empty($row[21]) || ($row[21] != $entry['genbank']['strand'])))?'&nbsp;<span class="warning">updated from Genbank</span>':''); ?><br />
            <label for="chromosome"><?php print _("Chromosome:"); ?></label> <input type="text" name="chromosome" id="chromosome" maxlength="50" title="<?php print _("Chromosome name"); ?>"  <?php print (!empty($entry['genbank']['chromosome'])?' value="' . $entry['genbank']['chromosome'] . '" ':(isset($row[8])?' value="' . $row[8] . '" ':'')); ?>/><?php print ((!empty($entry['genbank']['chromosome']) && (empty($row[8]) || ($row[8] != $entry['genbank']['chromosome'])))?'&nbsp;<span class="warning">updated from Genbank</span>':''); ?><br />
            <label for="topology"><strong><?php print _("Topology:"); ?></strong></label> <select name="topology" id="topology" title="<?php print _("Molecule topology"); ?>"><option value="f"<?php print (isset($entry['genbank']['circular'])?(($entry['genbank']['circular'] == 'f')?' selected="selected"':''):(($row[7] == 'f')?' selected="selected"':'')); ?>><?php print _("Linear"); ?></option><option value="t"<?php print (isset($entry['genbank']['circular'])?(($entry['genbank']['circular'] == 't')?' selected="selected"':''):(($row[7] == 't')?' selected="selected"':'')); ?>><?php print _("Circular"); ?></option></select><?php print ((!empty($entry['genbank']['circular']) && (empty($row[7]) || ($row[7] != $entry['genbank']['circular'])))?'&nbsp;<span class="warning">updated from Genbank</span>':''); ?><br />
<?php
      $result = sql_query('SELECT id,molecule FROM molecule;', $sql);
      if ((!strlen($r = sql_last_error($sql))) && (sql_num_rows($result) > 0)) {
        print '            <label for="molecule"><strong>' . _("Molecule:") . '</strong></label> <select name="molecule" id="molecule" title="' . _("Molecule type") . '">';
        while ($row2 = sql_fetch_row($result)) {
          print "<option value=\"$row2[0]\"" . (isset($entry['genbank']['molecule'])?(($entry['genbank']['molecule'] == $row2[0])?' selected="selected"':''):(($row[6] == $row2[0])?' selected="selected"':'')) . ">$row2[1]</option>";
        }
        print '</select>' . ((!empty($entry['genbank']['molecule']) && (empty($row[6]) || ($row[6] != $entry['genbank']['molecule'])))?'&nbsp;<span class="warning">updated from Genbank</span>':'') . "<br />\n";
      }
      $result = sql_query('SELECT id,name,alias FROM organism ORDER BY name;', $sql);
      if ((!strlen($r = sql_last_error($sql))) && (sql_num_rows($result) > 0)) {
        print '            <label for="organism"><strong>' . _("Organism:") . '</strong></label> <select name="organism" id="organism" title="' . _("Organism name") . '">';
        while ($row2 = sql_fetch_row($result)) {
          print "<option value=\"$row2[0]\"" . (isset($entry['genbank']['organism'])?(($entry['genbank']['organism'] == $row2[1])?' selected="selected"':''):(($row[15] == $row2[0])?' selected="selected"':'')) . ">$row2[1]" . (isset($row2[2])?" ($row2[2])":'') . '</option>';
          if (isset($entry['genbank']['organism']) && ($entry['genbank']['organism'] == $row2[1])) {
            $organism = (($row[15] == $row2[0])?1:2);
          }
        }
        print '</select>' . ((isset($organism) && ($organism == 2))?'&nbsp;<span class="warning">updated from Genbank</span>':((!isset($organism) && isset($entry['genbank']['organism']))?'&nbsp;<span class="warning">Unknown organism: <em>' . $entry['genbank']['organism'] . '</em>' . (isset($entry['genbank']['common'])?' (' . $entry['genbank']['common'] . ')':'') . '</span>':'')) . "<br />\n";
      }
?>
            <label for="organelle"><?php print _("Organelle:"); ?></label> <input type="text" name="organelle" id="organelle" maxlength="50" title="<?php print _("Organelle"); ?>" <?php print (!empty($entry['genbank']['organelle'])?' value="' . $entry['genbank']['organelle'] . '" ':(isset($row[10])?' value="' . $row[10] . '" ':'')); ?>/><?php print ((!empty($entry['genbank']['organelle']) && (empty($row[10]) || ($row[10] != $entry['genbank']['organelle'])))?'&nbsp;<span class="warning">updated from Genbank</span>':''); ?><br />
<?php
      $result = sql_query('SELECT id,translation FROM translation;', $sql);
      if ((!strlen($r = sql_last_error($sql))) && (sql_num_rows($result) > 0)) {
        print '            <label for="translation">' . _("Genetic table:") . '</label> <select name="translation" id="translation" title="' . _("Genetic table (if relevant)") . '">';
        while ($row2 = sql_fetch_row($result)) {
          print "<option value=\"$row2[0]\"" . (isset($entry['genbank']['translation'])?(($entry['genbank']['translation'] == $row2[0])?' selected="selected"':''):(($row[5] == $row2[0])?' selected="selected"':'')) . ">$row2[1]</option>";
        }
        print '</select>' . ((!empty($entry['genbank']['translation']) && (empty($row[5]) || ($row[5] != $entry['genbank']['translation'])))?'&nbsp;<span class="warning">updated from Genbank</span>':'') . "<br />\n";
      }
?>
            <label for="hgnc"><acronym title="HUGO Gene Nomenclature Committee">HGNC</acronym></label> <input type="text" name="hgnc" id="hgnc" maxlength="20" title="<?php print _("HGNC ID"); ?>" <?php print (!empty($entry['genbank']['hgnc'])?' value="' . $entry['genbank']['hgnc'] . '" ':(isset($row[13])?' value="' . $row[13] . '" ':'')); ?>/><?php print ((!empty($entry['genbank']['hgnc']) && (empty($row[13]) || ($row[13] != $entry['genbank']['hgnc'])))?'&nbsp;<span class="warning">updated from Genbank</span>':''); ?><br />
            <label for="geneid"><acronym title="Genbank GeneID">GeneID</acronym></label> <input type="text" name="geneid" id="geneid" maxlength="20" title="<?php print _("GenBank gene ID"); ?>" <?php print (!empty($entry['genbank']['geneid'])?' value="' . $entry['genbank']['geneid'] . '" ':(isset($row[14])?' value="' . $row[14] . '" ':'')); ?>/><?php print ((!empty($entry['genbank']['geneid']) && (empty($row[14]) || ($row[14] != $entry['genbank']['geneid'])))?'&nbsp;<span class="warning">updated from Genbank</span>':''); ?><br />
            <label for="map"><?php print _("Map:"); ?></label> <input type="text" name="map" id="map" maxlength="100" title="<?php print _("Gene mapping"); ?>" <?php print (isset($row[11])?' value="' . $row[11] . '" ':''); ?>/><br />
            <label for="isolate"><?php print _("Isolate"); ?></label> <input type="text" name="isolate" id="isolate" maxlength="100" title="<?php print _("Isolate name / Country / Lat-Long"); ?>" <?php print (isset($row[9])?' value="' . $row[9] . '" ':''); ?>/><br />
            <label for="sequence"><strong><?php print _("Sequence:"); ?></strong></label> <textarea name="sequence" id="sequence" cols="30" rows="3" title="<?php print _("Sequence"); ?>"><?php print (!empty($entry['genbank']['sequence'])?$entry['genbank']['sequence']:(isset($row[22])?$row[22]:'')); ?></textarea><?php print ((!empty($entry['genbank']['sequence']) && (empty($row[22]) || ($row[22] != $entry['genbank']['sequence'])))?'&nbsp;<span class="warning">updated from Genbank</span>':''); ?><br />
            <label for="go"><acronym title="Gene Ontology terms">GO</acronym></label> <textarea name="go" id="go" cols="30" rows="3" title="<?php print _("GO annotations"); ?>"><?php print (!empty($entry['genbank']['go'])?$entry['genbank']['go']:(isset($row[16])?$row[16]:'')); ?></textarea><?php print ((!empty($entry['genbank']['go']) && (empty($row[16]) || ($row[16] != $entry['genbank']['go'])))?'&nbsp;<span class="warning">updated from Genbank</span>':''); ?><br />
            <label for="seq_map"><?php print _("Structure:"); ?></label> <textarea name="seq_map" id="seq_map" cols="30" rows="3" title="<?php print _("Sequence structure (see help)"); ?>"><?php print (isset($row[26])?$row[26]:''); ?></textarea>&nbsp;[<a href="<?php print $config['server'] . '/help?id=structure'; ?>/add"><?php print _("help"); ?></a>]<br />
            <label for="seq_comments"><?php print _("Description:"); ?></label> <textarea name="seq_comments" id="seq_comments" cols="30" rows="3" title="<?php print _("Sequence description"); ?>"><?php print (isset($row[25])?$row[25]:''); ?></textarea><br />
            <label for="seq_references"><?php print _("References:"); ?></label> <textarea name="seq_references" id="seq_references" cols="30" rows="3" title="<?php print _("References (wiki style [ full_url | name ])"); ?>"><?php print (isset($row[24])?$row[24]:''); ?></textarea><br />
            <input type="hidden" name="addict" value="2" /><br /><button type="submit"><?php print _("Next"); ?>&nbsp;&gt;&gt;</button><button type="submit" name="remove" value="remove" onclick="return confirmSubmit('<?php print _("Are you sure you wish to continue?"); ?>')"><?php print _("Remove"); ?></button><br /><br />
          </p>
        </form>
        </div>
        <div class="clear">&nbsp;</div>
<?php
    }
    foot();
  }elseif (isset($_GET['mrna']) && preg_match('/^(\d+)\.(\d+)/', $_GET['mrna'], $matches)) { // mrna
    $matches[1] = octdec($matches[1]);
    $matches[2] = octdec($matches[2]);
    if (isset($_POST['addict']) && ($_POST['addict'] == '3') && isset($_POST['remove']) && ($_POST['remove'] == 'remove')) {
      $result = sql_query('DELETE FROM mrna WHERE prefix=' . $matches[1] . ' AND id=' . $matches[2] . ';', $sql);
      header('Location: ' . $config['server'] . '/browse');
      exit(0);
    }elseif (isset($_POST['addict']) && ($_POST['addict'] == '3') && !empty($_POST['mrna']) && isset($_POST['type']) && (intval($_POST['type']) > 0)) {
      $entry['mrna']['type'] = intval($_POST['type']);
      $entry['mrna']['mrna'] = preg_replace('/[^\w]/', '', strtoupper($_POST['mrna']));
      if (!empty($_POST['location'])) {
        $entry['mrna']['location'] = preg_replace('/[^\d\w\.\-\_\(\)]/', '', $_POST['location']);
      }
      if (!empty($_POST['comments'])) {
        $entry['mrna']['comments'] = ucfirst(preg_replace('/&amp;(#x?[0-9a-f]+);/', '&\1', htmlentities(html_entity_decode($_POST['comments']), ENT_QUOTES, 'ISO8859-1')));
      }
      $result = sql_query('UPDATE mrna SET mrna_type=' . $entry['mrna']['type'] . ', location=' . (isset($entry['mrna']['location'])?('\'' . addslashes($entry['mrna']['location']) . '\''):'NULL') . ', mrna=\'' . addslashes(base64_encode(bzcompress($entry['mrna']['mrna']))) . '\', comments=' . (isset($entry['mrna']['comments'])?('\'' . addslashes($entry['mrna']['comments']) . '\''):'NULL') . ', author=\'' . addslashes($_SESSION['login']['username']) . '\', updated=CURRENT_TIMESTAMP WHERE prefix=' . $matches[1] . ' AND id=' . $matches[2] . ';', $sql);
      if (!strlen($r = sql_last_error($sql))) {
        header('Location: ' . $config['server'] . '/mrna/' . $matches[0]);
        exit(0);
      }else {
        $msg = '        <div class="warning"><p><img src="' . $config['server'] . '/images/warning.png" alt="">&nbsp;' . _("Entry invalid, check your data") . "</p></div>\n";
      }
    }
    head('mrna', true);
    if (isset($msg)) {
      print $msg;
    }
    $result = sql_query('SELECT mrna_type,location,mrna,comments FROM mrna WHERE prefix=' . $matches[1] . ' AND id=' . $matches[2] . ';', $sql);
    if ((!strlen($r = sql_last_error($sql))) && (sql_num_rows($result) == 1)) {
      $row = sql_fetch_row($result);
      $row[2] = bzdecompress(base64_decode($row[2]));
?>
        <div class="item">
        <form action="<?php print $config['server'] . '/mrna/' . $matches[0]; ?>/edit" method="post">
          <h1>
            <?php print _("mRNA"); ?> 
          </h1>
          <div class="clear">&nbsp;</div>
          <p>
            <label for="location"><?php print _("Coordinate:"); ?></label> <input type="text" name="location" id="location" title="<?php print _("Full coordinates (GenBank style: complement(join(aa..bb,cc..dd))"); ?>" <?php print (isset($row[1])?' value="' . $row[1] . '" ':''); ?>/><br />
            <label for="type"><strong><?php print _("mRNA type:"); ?></strong></label> <select name="type" id="type" title="'. _("mRNA sequence type").'"><?php
      for ($i = 1; $i < 11 ; $i++) {
        print '<option value="' . $i . '"' . (($row[0] == $i)?' selected="selected"':'') . '>' . _("Transcript") . ' ' . $i . '</option>';
      }
?></select><br />
            <label for="mrna"><strong><?php print _("Sequence:"); ?></strong></label> <textarea name="mrna" id="mrna" cols="30" rows="3" title="<?php print _("mRNA sequence"); ?>"><?php print (isset($row[2])?$row[2]:''); ?></textarea><br />
            <label for="comments"><?php print _("Comments:"); ?></label> <textarea name="comments" id="comments" cols="30" rows="3" title="<?php print _("mRNA comments"); ?>"><?php print (isset($row[3])?$row[3]:''); ?></textarea><br />
            <input type="hidden" name="addict" value="3" /><br /><button type="submit"><?php print _("Next"); ?>&nbsp;&gt;&gt;</button><button type="submit" name="remove" value="remove" onclick="return confirmSubmit('<?php print _("Are you sure you wish to continue?"); ?>')"><?php print _("Remove"); ?></button><br /><br />
          </p>
        </form>
        </div>
        <div class="clear">&nbsp;</div>
<?php
    }
    foot();
  }elseif (isset($_GET['alignment']) && preg_match('/^(\d+)\.(\d+)/', $_GET['alignment'], $matches)) { // alignment
    $matches[1] = octdec($matches[1]);
    $matches[2] = octdec($matches[2]);
    if (isset($_POST['addict']) && ($_POST['addict'] == '4') && isset($_POST['remove']) && ($_POST['remove'] == 'remove')) {
      $result = sql_query('DELETE FROM alignment WHERE prefix=' . $matches[1] . ' AND id=' . $matches[2] . ';', $sql);
      $result = sql_query('DELETE FROM primer WHERE alignment_prefix=' . $matches[1] . ' AND alignment_id=' . $matches[2] . ';', $sql);
      header('Location: ' . $config['server'] . '/browse');
      exit(0);
    }elseif (isset($_POST['addict']) && ($_POST['addict'] == '4') && !empty($_POST['program']) && !empty($_POST['alignment']) && (substr(trim($_POST['alignment']), 0, 1) == '>') && !empty($_POST['consensus']) && isset($_POST['sequences'][2])) {
      $entry['alignment']['sequences'] = $_POST['sequences'];
      $entry['alignment']['alignment'] = preg_replace('/&amp;(#x?[0-9a-f]+);/', '&\1', htmlentities(html_entity_decode($_POST['alignment']), ENT_QUOTES, 'ISO8859-1'));
      $entry['alignment']['consensus'] = preg_replace('/[^\w]/', '', strtoupper($_POST['consensus']));
      $entry['alignment']['program'] = preg_replace('/[^\d\w\.\-\_\ ]/', '', $_POST['program']);
      if (!empty($_POST['comments'])) {
        $entry['alignment']['comments'] = ucfirst(preg_replace('/&amp;(#x?[0-9a-f]+);/', '&\1', htmlentities(html_entity_decode($_POST['comments']), ENT_QUOTES, 'ISO8859-1')));
      }
      $result = sql_query('UPDATE alignment SET sequences=\'' . implode(' ', $entry['alignment']['sequences']) . '\', alignment=\'' . addslashes(base64_encode(bzcompress($entry['alignment']['alignment']))) . '\', consensus=\'' . addslashes(base64_encode(bzcompress($entry['alignment']['consensus']))) . '\', program=\'' . addslashes($entry['alignment']['program']) . '\', comments=' . (isset($entry['alignment']['comments'])?('\'' . addslashes($entry['alignment']['comments']) . '\''):'NULL') . ', author=\'' . addslashes($_SESSION['login']['username']) . '\', updated=CURRENT_TIMESTAMP WHERE prefix=' . $matches[1] . ' AND id=' . $matches[2] . ';', $sql);
      if (!strlen($r = sql_last_error($sql))) {
        header('Location: ' . $config['server'] . '/alignment/' . $matches[0]);
        exit(0);
      }else {
        $msg = '        <div class="warning"><p><img src="' . $config['server'] . '/images/warning.png" alt="">&nbsp;' . _("Entry invalid, check your data") . "</p></div>\n";
      }
    }
    head('alignment', true);
    if (isset($msg)) {
      print $msg;
    }
    $result = sql_query('SELECT locus_prefix,locus_id,sequences,alignment,consensus,program,comments FROM alignment WHERE prefix=' . $matches[1] . ' AND id=' . $matches[2] . ';', $sql);
    if ((!strlen($r = sql_last_error($sql))) && (sql_num_rows($result) == 1)) {
      $row = sql_fetch_row($result);
      $row[4] = bzdecompress(base64_decode($row[4]));
      $row[3] = bzdecompress(base64_decode($row[3]));
?>
        <div class="item">
        <form action="<?php print $config['server'] . '/alignment/' . $matches[0]; ?>/edit" method="post">
          <h1>
            <?php print _("Alignment"); ?> 
          </h1>
          <div class="clear">&nbsp;</div>
          <p>
            <label for="program"><strong><?php print _("Program used:"); ?></strong></label> <input type="text" name="program" id="program" maxlength="100" title="<?php print _("Program and version used"); ?>" <?php print (isset($row[5])?' value="' . $row[5] . '" ':''); ?>/><br />
<?php
      $result = sql_query('SELECT a.prefix,a.id,a.name,a.accession,c.name,c.alias FROM sequence AS a, sequence_type AS b, organism as c WHERE a.locus_prefix=' . $row[0] . ' AND a.locus_id=' . $row[1] . ' AND a.sequence_type=b.id AND a.organism=c.id ORDER BY a.updated;', $sql);
      if ((!strlen($r = sql_last_error($sql))) && (sql_num_rows($result) > 0)) {
        $row[2] = explode(' ', $row[2]);
        print '            <label for="sequences"><strong>' . _("Sequence used:") . '</strong></label> <select name="sequences[]" id="sequences" title="' . _("Sequences used for the alignment") . '" multiple="multiple" class="multiple">';
        while ($row2 = sql_fetch_row($result)) {
          if (($_SESSION['specie'] == 'alias') && isset($row2[5])) {
            $specie = ucfirst($row2[5]);
          }else {
            $specie = ucfirst($row2[4]);
          }
          print '<option value="' . decoct($row2[0]) . '.' . decoct($row2[1]) . '"' . ((array_search(decoct($row2[0]) . '.' . decoct($row2[1]), $row[2]) !== false)?' selected="selected"':'') . ">$row2[2] ($row2[3] - <em>$specie</em>)</option>";
        }
        print "</select><br />\n";
      }
?>
            <label for="alignment"><strong><?php print _("Alignment:"); ?></strong></label> <textarea name="alignment" id="alignment" cols="30" rows="6" title="<?php print _("Fasta format Alignment"); ?>"><?php print (isset($row[3])?$row[3]:''); ?></textarea><br />
            <label for="consensus"><strong><?php print _("Consensus:"); ?></strong></label> <textarea name="consensus" id="consensus" cols="30" rows="3" title="<?php print _("Consensus 80%"); ?>"><?php print (isset($row[4])?$row[4]:''); ?></textarea><br />
            <label for="comments"><?php print _("Comments:"); ?></label> <textarea name="comments" id="comments" cols="30" rows="3" title="<?php print _("Primer pair comments"); ?>"><?php print (isset($row[6])?$row[6]:''); ?></textarea><br />
            <input type="hidden" name="addict" value="4" /><br /><button type="submit"><?php print _("Next"); ?>&nbsp;&gt;&gt;</button><button type="submit" name="remove" value="remove" onclick="return confirmSubmit('<?php print _("Are you sure you wish to continue?"); ?>')"><?php print _("Remove"); ?></button><br /><br />
          </p>
        </form>
        </div>
        <div class="clear">&nbsp;</div>
<?php
    }
    foot();
  }elseif (isset($_GET['primer']) && preg_match('/^(\d+)\.(\d+)/', $_GET['primer'], $matches)) { // primer
    $matches[1] = octdec($matches[1]);
    $matches[2] = octdec($matches[2]);
    if (isset($_POST['addict']) && ($_POST['addict'] == '5') && isset($_POST['remove']) && ($_POST['remove'] == 'remove')) {
      $result = sql_query('DELETE FROM primer WHERE prefix=' . $matches[1] . ' AND id=' . $matches[2] . ';', $sql);
      header('Location: ' . $config['server'] . '/browse');
      exit(0);
    }elseif (isset($_POST['addict']) && ($_POST['addict'] == '5') && !empty($_POST['left_seq']) && !empty($_POST['right_seq'])) {
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
      $result = sql_query('SELECT a.locus, b.consensus FROM primer AS a, alignment AS b WHERE a.id=' . $primer . ' AND a.alignment=b.id;', $sql);
      if ((!strlen($r = sql_last_error($sql))) && (sql_num_rows($result) == 1)) {
        $row = sql_fetch_row($result);
        $locus = intval($row[0]);
        $consensus = bzdecompress(base64_decode($row[1]));
        $forward = ((isset($_POST['left_pos']) && (intval($_POST['left_pos']) > 0)) ? intval($_POST['left_pos']) : strpos($consensus, align($entry['primer']['left_seq'], $consensus, get_pref('primer'))));
        $reverse = ((isset($_POST['right_pos']) && (intval($_POST['right_pos']) > 0)) ? intval($_POST['right_pos']) : strpos($consensus, align(revert($entry['primer']['right_seq']), $consensus, get_pref('primer'))) + strlen($entry['primer']['right_seq'])-1);
        $forwardgc = (substr_count($entry['primer']['left_seq'], 'G') + substr_count($entry['primer']['left_seq'], 'C') + substr_count($entry['primer']['left_seq'], 'N') * 0.5) / strlen($entry['primer']['left_seq']) * 100;
        $reversegc = (substr_count($entry['primer']['right_seq'], 'G') + substr_count($entry['primer']['right_seq'], 'C') + substr_count($entry['primer']['right_seq'], 'N') * 0.5) / strlen($entry['primer']['right_seq']) * 100;
        $result = sql_query('UPDATE primer SET penality=' . ($entry['primer']['left_penality'] + $entry['primer']['right_penality']) . ', left_seq=\'' . addslashes($entry['primer']['left_seq']) . '\', left_data=\'' . addslashes($forward . '|' . strlen($entry['primer']['left_seq']) . '|' . $entry['primer']['left_tm'] . '|' . $forwardgc . '|' . $entry['primer']['left_penality']) . '\', left_name=' . (isset($entry['primer']['left_name'])?('\'' . addslashes($entry['primer']['left_name']) . '\''):'NULL') . ', right_seq=\'' . addslashes($entry['primer']['right_seq']) . '\', right_data=\'' . addslashes($reverse . '|' . strlen($entry['primer']['right_seq']) . '|' . $entry['primer']['right_tm'] . '|' . $reversegc . '|' . $entry['primer']['right_penality']) . '\', right_name=' . (isset($entry['primer']['right_name'])?('\'' . addslashes($entry['primer']['right_name']) . '\''):'NULL') . ', location=' . (isset($entry['primer']['location'])?('\'' . addslashes($entry['primer']['location']) . '\''):'NULL') . ', pcr=' . (isset($entry['primer']['pcr'])?('\'' . addslashes($entry['primer']['pcr']) . '\''):'NULL') . ', comments=' . (isset($entry['primer']['comments'])?('\'' . addslashes($entry['primer']['comments']) . '\''):'NULL') . ', author=\'' . addslashes($_SESSION['login']['username']) . '\', updated=CURRENT_TIMESTAMP WHERE prefix=' . $matches[1] . ' AND id=' . $matches[2] . ';', $sql);
        if (!strlen($r = sql_last_error($sql))) {
          header('Location: ' . $config['server'] . '/primer/' . $matches[0]);
          exit(0);
        }else {
          $msg = '        <div class="warning"><p><img src="' . $config['server'] . '/images/warning.png" alt="">&nbsp;' . _("Entry invalid, check your data") . "</p></div>\n";
        }
      }else {
        $msg = '        <div class="warning"><p><img src="' . $config['server'] . '/images/warning.png" alt="">&nbsp;' . _("Entry invalid, check your data") . "</p></div>\n";
      }
    }
    head('primer', true);
    if (isset($msg)) {
      print $msg;
    }
    $result = sql_query('SELECT id,locus,alignment,penality,left_seq,left_data,left_name,right_seq,right_data,right_name,location,pcr,comments FROM primer WHERE prefix=' . $matches[1] . ' AND id=' . $matches[2] . ';', $sql);
    if ((!strlen($r = sql_last_error($sql))) && (sql_num_rows($result) == 1)) {
      $row = sql_fetch_row($result);
      $row[5] = explode('|', $row[5]);
      $row[8] = explode('|', $row[8]);
?>
        <div class="item">
        <form action="<?php print $config['server'] . '/primer/' . $matches[0]; ?>/edit" method="post">
          <h1>
            <?php print _("Primers"); ?> 
          </h1>
          <div class="clear">&nbsp;</div>
          <h2>
            <?php print _("Forward primer"); ?> 
          </h2>
          <p>
            <label for="left_name"><?php print _("Primer name:"); ?></label> <input type="text" name="left_name" id="left_name" maxlength="50" title="<?php print _("Reference name"); ?>" <?php print (isset($row[6])?' value="' . $row[6] . '" ':''); ?>/><br />
            <label for="left_seq"><strong><?php print _("Sequence:"); ?></strong></label> <input type="text" name="left_seq" id="left_seq" maxlength="50" title="<?php print _("DNA sequence"); ?>" <?php print (isset($row[4])?' value="' . $row[4] . '" ':''); ?>/><br />
            <label for="left_tm"><?php print _("TM:"); ?></label> <input type="text" name="left_tm" id="left_tm" maxlength="10" title="<?php print _("Estimate TM"); ?>" <?php print (!empty($row[5][2])?' value="' . $row[5][2] . '" ':''); ?>/>°C<br />
            <label for="left_pos"><?php print _("Position:"); ?></label> <input type="text" name="left_pos" id="left_pos" maxlength="10" title="<?php print _("Start position of the primer in the alignment"); ?>" <?php print (isset($row[5][0])?' value="' . $row[5][0] . '" ':''); ?>/><br />
            <label for="left_penality"><?php print _("Primer penality:"); ?></label> <input type="text" name="left_penality" id="left_penality" maxlength="10" title="<?php print _("Primer penality (defined by Primer3)"); ?>" <?php print (!empty($row[5][4])?' value="' . $row[5][4] . '" ':''); ?>/><br />
          </p>
          <h2>
            <?php print _("Reverse primer"); ?> 
          </h2>
          <p>
            <label for="right_name"><?php print _("Primer name:"); ?></label> <input type="text" name="right_name" id="right_name" maxlength="50" title="<?php print _("Reference name"); ?>" <?php print (isset($row[9])?' value="' . $row[9] . '" ':''); ?>/><br />
            <label for="right_seq"><strong><?php print _("Sequence:"); ?></strong></label> <input type="text" name="right_seq" id="right_seq" maxlength="50" title="<?php print _("DNA sequence"); ?>" <?php print (isset($row[7])?' value="' . $row[7] . '" ':''); ?>/><br />
            <label for="right_tm"><?php print _("TM:"); ?></label> <input type="text" name="right_tm" id="right_tm" maxlength="10" title="<?php print _("Estimate TM"); ?>" <?php print (!empty($row[8][2])?' value="' . $row[8][2] . '" ':''); ?>/>°C<br />
            <label for="right_pos"><?php print _("Position:"); ?></label> <input type="text" name="right_pos" id="right_pos" maxlength="10" title="<?php print _("End position of the primer in the alignment"); ?>" <?php print (isset($row[8][0])?' value="' . $row[8][0] . '" ':''); ?>/><br />
            <label for="right_penality"><?php print _("Primer penality:"); ?></label> <input type="text" name="right_penality" id="right_penality" maxlength="10" title="<?php print _("Primer penality (defined by Primer3)"); ?>" <?php print (!empty($row[8][4])?' value="' . $row[8][4] . '" ':''); ?>/><br />
          </p>
          <h2>
            <?php print _("PCR conditions"); ?> 
          </h2>
          <p>
            <label for="pcr"><?php print _("PCR conditions:"); ?></label> <input type="text" name="pcr" id="pcr" title="<?php print _("PCR conditions"); ?>" <?php print (isset($row[11])?' value="' . $row[11] . '" ':''); ?>/><br />
          </p>
          <h2>
            <?php print _("General"); ?> 
          </h2>
          <p>
            <label for="location"><?php print _("Product location:"); ?></label> <input type="text" name="location" id="location" title="<?php print _("Product location (ex. intron1-exon2)"); ?>" <?php print (isset($row[10])?' value="' . $row[10] . '" ':''); ?>/><br />
            <label for="comments"><?php print _("Comments:"); ?></label> <textarea name="comments" id="comments" cols="30" rows="3" title="<?php print _("Primer pair comments"); ?>"><?php print (isset($row[12])?$row[12]:''); ?></textarea><br />
            <input type="hidden" name="addict" value="5" /><br /><button type="submit"><?php print _("Next"); ?>&nbsp;&gt;&gt;</button><button type="submit" name="remove" value="remove" onclick="return confirmSubmit('<?php print _("Are you sure you wish to continue?"); ?>')"><?php print _("Remove"); ?></button><br /><br />
          </p>
        </form>
        </div>
        <div class="clear">&nbsp;</div>
<?php
    }
    foot();
  }
}else {
  header("HTTP/1.0 403 Forbidden");
  exit(403);
}
?>